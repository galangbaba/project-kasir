<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiService
{
    public function __construct(private StokService $stok) {}

    /**
     * Simpan transaksi penjualan secara atomik (UC-10, NFR-DAT-03).
     * Harga SELALU diambil dari basis data (bukan dari klien) lalu disimpan sebagai snapshot.
     *
     * @param array<int,int> $items product_id => qty
     */
    public function buat(User $kasir, array $items, string $metode): Transaksi
    {
        $items = array_filter($items, fn ($q) => $q > 0);
        if (! $items) {
            throw ValidationException::withMessages(['items' => 'Pilih minimal satu produk.']);
        }

        return DB::transaction(function () use ($kasir, $items, $metode) {
            $ids = array_keys($items);
            sort($ids); // urutan kunci konsisten -> tidak deadlock

            $products = Product::query()->whereIn('id', $ids)->where('is_active', true)
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            if ($products->count() !== count($ids)) {
                throw ValidationException::withMessages(['items' => 'Ada produk yang sudah tidak tersedia. Muat ulang halaman.']);
            }

            $total = 0;
            foreach ($ids as $id) {
                $total += $products[$id]->harga * $items[$id];
            }

            // Nomor TRX per tenant — kunci baris tenant sebagai mutex penomoran.
            $tenant = Tenant::query()->whereKey($kasir->tenant_id)->lockForUpdate()->firstOrFail();
            $tenant->trx_counter = max($tenant->trx_counter, config('kasir.trx_start')) + 1;
            $tenant->save();

            $trx = new Transaksi;
            $trx->forceFill([
                'tenant_id' => $kasir->tenant_id,
                'user_id' => $kasir->id,
                'no_transaksi' => 'TRX-' . $tenant->trx_counter,
                'total_bayar' => $total,
                'metode_bayar' => $metode,
                'status' => 'selesai',
            ])->save();

            foreach ($ids as $id) {
                $p = $products[$id];
                $qty = $items[$id];

                $d = new TransaksiDetail;
                $d->forceFill([
                    'transaksi_id' => $trx->id,
                    'product_id' => $p->id,
                    'nama_product' => $p->nama_product,
                    'harga_satuan' => $p->harga,
                    'jumlah' => $qty,
                    'subtotal' => $p->harga * $qty,
                ])->save();

                $this->stok->ubah($p, 'penjualan', -$qty, $trx->no_transaksi);
            }

            return $trx;
        });
    }

    /** Void (FR-KAS-04): hanya milik sendiri, dalam batas waktu, stok dikembalikan. */
    public function batalkan(User $kasir, int $transaksiId): Transaksi
    {
        return DB::transaction(function () use ($kasir, $transaksiId) {
            $trx = Transaksi::query()->whereKey($transaksiId)->where('user_id', $kasir->id)->lockForUpdate()->firstOrFail();

            if (! $trx->bisaDibatalkan()) {
                throw ValidationException::withMessages([
                    'void' => 'Transaksi tidak dapat dibatalkan (sudah dibatalkan atau melewati batas '
                        . config('kasir.void_minutes') . ' menit).',
                ]);
            }

            $details = $trx->details()->orderBy('product_id')->get();
            $products = Product::query()->whereIn('id', $details->pluck('product_id'))
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            foreach ($details as $d) {
                $this->stok->ubah($products[$d->product_id], 'void', $d->jumlah, 'Void ' . $trx->no_transaksi);
            }

            $trx->forceFill(['status' => 'dibatalkan', 'dibatalkan_at' => now(), 'dibatalkan_oleh' => $kasir->id])->save();
            AuditLog::catat('transaksi.void', 'transaksi', $trx->id, ['no' => $trx->no_transaksi, 'total' => $trx->total_bayar]);

            return $trx;
        });
    }
}
