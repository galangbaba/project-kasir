<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $tenants = Tenant::query()->orderBy('nama_tenants')->get();

        $perTenant = Transaksi::query()->where('status', 'selesai')
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->groupBy('tenant_id')
            ->selectRaw('tenant_id, SUM(total_bayar) as omzet, COUNT(*) as jml')
            ->get()->keyBy('tenant_id');

        return view('superadmin.dashboard', [
            'tenants' => $tenants,
            'perTenant' => $perTenant,
            'aktif' => $tenants->where('status', 'aktif')->count(),
            'nonaktif' => $tenants->where('status', 'nonaktif')->count(),
            'baruBulanIni' => $tenants->filter(fn ($t) => $t->created_at->isSameMonth(now()))->count(),
            'omzet' => (int) $perTenant->sum('omzet'),
            'jumlahTrx' => (int) $perTenant->sum('jml'),
        ]);
    }
}
