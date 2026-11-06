<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        return view('superadmin.tenants.index', ['tenants' => Tenant::query()->orderBy('nama_tenants')->paginate(15)]);
    }

    public function create()
    {
        return view('superadmin.tenants.form', ['tenant' => new Tenant(['status' => 'aktif'])]);
    }

    public function store(Request $request)
    {
        $tenant = new Tenant($this->data($request));
        $tenant->status = 'aktif';
        $tenant->save();
        AuditLog::catat('tenant.tambah', 'tenants', $tenant->id, ['nama' => $tenant->nama_tenants]);

        return redirect()->route('superadmin.tenants')->with('ok', "Tenant {$tenant->nama_tenants} ditambahkan. Lanjutkan dengan membuat akun Admin Tenant.");
    }

    public function edit(Tenant $tenant)
    {
        return view('superadmin.tenants.form', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant->update($this->data($request));
        AuditLog::catat('tenant.ubah', 'tenants', $tenant->id, ['nama' => $tenant->nama_tenants]);

        return redirect()->route('superadmin.tenants')->with('ok', 'Data tenant diperbarui.');
    }

    public function toggle(Tenant $tenant)
    {
        $tenant->status = $tenant->isActive() ? 'nonaktif' : 'aktif';
        $tenant->save();
        AuditLog::catat('tenant.status', 'tenants', $tenant->id, ['status' => $tenant->status]);

        return back()->with('ok', "{$tenant->nama_tenants} sekarang {$tenant->status}.");
    }

    private function data(Request $request): array
    {
        return $request->validate([
            'nama_tenants' => ['required', 'string', 'max:100'],
            'kategori_usaha' => ['nullable', 'string', 'max:60'],
            'penanggung_jawab' => ['required', 'string', 'max:100'],
        ]);
    }
}
