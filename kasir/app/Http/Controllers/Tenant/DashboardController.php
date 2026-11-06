<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaksi;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function index(ReportService $report)
    {
        $hari = now();
        $ringkas = $report->ringkasan($hari, $hari);

        return view('tenant.dashboard', [
            'omzet' => $ringkas['omzet'],
            'jumlahTrx' => $ringkas['jumlah'],
            'kasirBertugas' => Transaksi::query()->where('status', 'selesai')
                ->whereBetween('created_at', [$hari->copy()->startOfDay(), $hari->copy()->endOfDay()])
                ->distinct('user_id')->count('user_id'),
            'produkAktif' => Product::query()->where('is_active', true)->count(),
            'produkTotal' => Product::query()->count(),
            'stokMenipis' => Product::query()->where('is_active', true)->where('stok', '<=', config('kasir.low_stock'))->count(),
            'harian' => $report->harian($hari->copy()->subDays(6), $hari),
            'terlaris' => $report->topProduk($hari, $hari, 5),
        ]);
    }
}
