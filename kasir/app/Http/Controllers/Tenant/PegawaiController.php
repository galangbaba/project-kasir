<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\Security\Md5Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    /** Hanya akun kasir milik tenant pengguna (tenant_id dari sesi server). */
    private function kasirs()
    {
        return User::query()->where('tenant_id', Auth::user()->tenant_id)->where('role', User::KASIR);
    }

    public function index()
    {
        return view('tenant.pegawai.index', ['pegawai' => $this->kasirs()->orderBy('nama')->paginate(15)]);
    }

    public function create()
    {
        return view('tenant.pegawai.form', ['pegawai' => new User(['status' => 'aktif'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null) + $request->validate(['password' => ['required', 'string', 'min:8', 'max:100']]);

        $u = new User(['nama' => $data['nama'], 'username' => $data['username'], 'status' => 'aktif']);
        $u->forceFill([
            'role' => User::KASIR,
            'tenant_id' => Auth::user()->tenant_id,
            'password' => Md5Password::make($data['password']),
        ])->save();
        AuditLog::catat('akun.tambah', 'users', $u->id, ['role' => 'kasir']);

        return redirect()->route('tenant.pegawai')->with('ok', 'Akun kasir dibuat.');
    }

    public function edit(int $id)
    {
        return view('tenant.pegawai.form', ['pegawai' => $this->kasirs()->findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $u = $this->kasirs()->findOrFail($id);
        $data = $this->validated($request, $u) + $request->validate(['password' => ['nullable', 'string', 'min:8', 'max:100']]);

        $u->fill(['nama' => $data['nama'], 'username' => $data['username']]);
        if (! empty($data['password'])) {
            $u->password = Md5Password::make($data['password']);
        }
        $u->save();
        AuditLog::catat('akun.ubah', 'users', $u->id, ['ganti_sandi' => ! empty($data['password'])]);

        return redirect()->route('tenant.pegawai')->with('ok', 'Akun kasir diperbarui.');
    }

    public function toggle(int $id)
    {
        $u = $this->kasirs()->findOrFail($id);
        $u->status = $u->isActive() ? 'nonaktif' : 'aktif';
        $u->save();
        AuditLog::catat('akun.status', 'users', $u->id, ['status' => $u->status]);

        return back()->with('ok', "Akun {$u->nama} sekarang {$u->status}.");
    }

    private function validated(Request $request, ?User $ignore): array
    {
        $request->merge(['username' => mb_strtolower(trim((string) $request->input('username')))]);

        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'regex:/^[a-z0-9._-]{3,50}$/', Rule::unique('users', 'username')->ignore($ignore?->id)],
        ]);
    }
}
