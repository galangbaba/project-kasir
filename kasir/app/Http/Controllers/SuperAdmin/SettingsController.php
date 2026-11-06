<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\AuthSession;
use App\Support\Security\Md5Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        return view('superadmin.pengaturan');
    }

    public function password(Request $request)
    {
        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ]);

        $user = Auth::user();
        if (! Md5Password::check($data['password_lama'], $user->password)) {
            return back()->withErrors(['password_lama' => 'Kata sandi lama tidak sesuai.']);
        }

        $user->forceFill(['password' => Md5Password::make($data['password'])])->save();

        // Cabut semua sesi lain milik akun ini (perangkat lain harus login ulang).
        $current = $request->attributes->get('authSession');
        AuthSession::query()->where('user_id', $user->id)->where('status', 'active')
            ->where('id', '!=', $current->id)->update(['status' => 'revoked', 'revoked_at' => now()]);
        AuditLog::catat('akun.ganti_sandi', 'users', $user->id);

        return back()->with('ok', 'Kata sandi diperbarui.');
    }
}
