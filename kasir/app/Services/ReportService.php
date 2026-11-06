<?php

namespace App\Services;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/** Agregasi laporan untuk tenant pengguna yang sedang login. Hanya transaksi berstatus "selesai" dihitung. */
class ReportService
{
    private function tenantId(): int
    {
        return (int) Auth::user()->tenant_id;
    }

    private function selesai(CarbonInterface $from, CarbonInterface $to)
    {
        return Transaksi::query()->where('status', 'selesai')
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
    }

    /** @return array{omzet:int,jumlah:int,rata:int} */
    public function ringkasan(CarbonInterface $from, CarbonInterface $to): array
    {
        $r = $this->selesai($from, $to)->selectRaw('COALESCE(SUM(total_bayar),0) as omzet, COUNT(*) as jml')->first();
        $omzet = (int) $r->omzet;
        $jml = (int) $r->jml;

        return ['omzet' => $omzet, 'jumlah' => $jml, 'rata' => $jml ? intdiv($omzet, $jml) : 0];
    }

    /** @return Collection<int,array{tanggal:\Carbon\Carbon,omzet:int,jumlah:int}> satu baris per hari (0 bila kosong) */
    public function harian(CarbonInterface $from, CarbonInterface $to): Collection
    {
        $rows = $this->selesai($from, $to)->selectRaw('DATE(created_at) as tgl, SUM(total_bayar) as omzet, COUNT(*) as jml')
            ->groupBy('tgl')->get()->keyBy('tgl');

        return collect(CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay()))->map(function ($d) use ($rows) {
            $row = $rows->get($d->format('Y-m-d'));

            return ['tanggal' => $d->copy(), 'omzet' => (int) ($row->omzet ?? 0), 'jumlah' => (int) ($row->jml ?? 0)];
        });
    }

    public function topProduk(CarbonInterface $from, CarbonInterface $to, int $limit = 5): Collection
    {
        return TransaksiDetail::query()
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_detail.transaksi_id')
            ->where('transaksi.tenant_id', $this->tenantId())->where('transaksi.status', 'selesai')
            ->whereBetween('transaksi.created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->groupBy('transaksi_detail.product_id', 'transaksi_detail.nama_product')
            ->selectRaw('transaksi_detail.nama_product as nama, SUM(transaksi_detail.jumlah) as qty, SUM(transaksi_detail.subtotal) as omzet')
            ->orderByDesc('qty')->orderBy('nama')->limit($limit)->get();
    }

    public function perKasir(CarbonInterface $from, CarbonInterface $to): Collection
    {
        return $this->selesai($from, $to)->join('users', 'users.id', '=', 'transaksi.user_id')
            ->groupBy('transaksi.user_id', 'users.nama')
            ->selectRaw('users.nama as nama, COUNT(*) as jml, SUM(transaksi.total_bayar) as omzet')
            ->orderByDesc('omzet')->get();
    }

    public function terbaru(CarbonInterface $from, CarbonInterface $to, ?int $limit = 10): Collection
    {
        $q = Transaksi::query()->with('kasir')
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->orderByDesc('created_at')->orderByDesc('id');

        return $limit ? $q->limit($limit)->get() : $q->get();
    }
}
