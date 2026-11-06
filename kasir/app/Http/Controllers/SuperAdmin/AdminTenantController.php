<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Security\Md5Password;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminTenantController extends Controller
{
    public function index()
    {
        $admins = User::query()->where('role', User::ADMIN_TENANT)->with('tenant')->orderBy('nama')->paginate(15);

        return view('superadmin.admin.index', compact('admins'));
    }

    public function create()
    {
        return view('superadmin.admin.form', [
            'admin' => new User(['status' => 'aktif']),
            'tenants' => Tenant::query()->orderBy('nama_tenants')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null) + $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'tenant_id' => ['required', 'exists:tenants,id'],
        ]);

        $admin = new User(['nama' => $data['nama'], 'email' => $data['email'], 'username' => $data['username'], 'status' => 'aktif']);
        $admin->forceFill([
            'role' => User::ADMIN_TENANT,
            'tenant_id' => $data['tenant_id'],
            'password' => Md5Password::make($data['password']),
        ])->save();
        AuditLog::catat('akun.tambah', 'users', $admin->id, ['role' => $admin->role, 'tenant_id' => $admin->tenant_id]);

        return redirect()->route('superadmin.admin')->with('ok', 'Akun Admin Tenant dibuat.');
    }

    public function edit(User $admin)
    {
        abort_unless($admin->role === User::ADMIN_TENANT, 404);

        return view('superadmin.admin.form', [
            'admin' => $admin->load('tenant'),
            'tenants' => Tenant::query()->orderBy('nama_tenants')->get(),
        ]);
    }

    public function update(Request $request, User $admin)
    {
        abort_unless($admin->role === User::ADMIN_TENANT, 404);

        $data = $this->validated($request, $admin) + $request->validate([
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
        ]);

        $admin->fill(['nama' => $data['nama'], 'email' => $data['email'], 'username' => $data['username'], 'status' => $data['status']]);
        if (! empty($data['password'])) {
            $admin->password = Md5Password::make($data['password']);
        }
        $admin->save();
        AuditLog::catat('akun.ubah', 'users', $admin->id, ['status' => $admin->status, 'ganti_sandi' => ! empty($data['password'])]);

        return redirect()->route('superadmin.admin')->with('ok', 'Akun Admin Tenant diperbarui.');
    }

    private function validated(Request $request, ?User $ignore): array
    {
        $request->merge([
            'username' => mb_strtolower(trim((string) $request->input('username'))),
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($ignore?->id)],
            'username' => ['required', 'regex:/^[a-z0-9._-]{3,50}$/', Rule::unique('users', 'username')->ignore($ignore?->id)],
        ]);
    }
}
