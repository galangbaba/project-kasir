<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/** Satu-satunya pintu perubahan stok: selalu tercatat pada stock_logs. Pemanggil harus sudah di dalam transaksi DB + lockForUpdate. */
class StokService
{
    public function ubah(Product $product, string $tipe, int $delta, ?string $keterangan = null): void
    {
        $sebelum = (int) $product->stok;
        $sesudah = $sebelum + $delta;

        if ($sesudah < 0) {
            throw ValidationException::withMessages([
                'items' => "Stok {$product->nama_product} tidak cukup (sisa {$sebelum}).",
            ]);
        }

        $product->stok = $sesudah;
        $product->save();

        $log = new StockLog;
        $log->forceFill([
            'tenant_id' => $product->tenant_id,
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'tipe' => $tipe,
            'perubahan' => $delta,
            'stok_sebelum' => $sebelum,
            'stok_sesudah' => $sesudah,
            'keterangan' => $keterangan,
        ])->save();
    }
}
