<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\Security\Md5Password;
use App\Support\Security\SessionService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private const COPY = [
        User::SUPER_ADMIN => ['Masuk sebagai Super Admin', 'Kelola seluruh client/tenant pada satu platform.', 'Email / Username', 'nama@contoh.com'],
        User::ADMIN_TENANT => ['Masuk sebagai Admin Tenant', 'Kelola produk, stok, pegawai, dan laporan tenant Anda.', 'Email / Username', 'nama@contoh.com'],
        User::KASIR => ['Masuk Kasir', 'Gunakan akun yang diberikan Admin Tenant', 'Username', 'rani.kasir'],
    ];

    public function __construct(private SessionService $sessions) {}

    public function show(Request $request, string $role)
    {
        $user = $this->sessions->resolve($request->cookie($this->sessions->cookieName()))?->user;
        if ($user && $user->isActive() && $user->role === $role) {
            return redirect($user->homeRoute());
        }

        [$title, $subtitle, $label, $placeholder] = self::COPY[$role];

        return view('auth.login', compact('role', 'title', 'subtitle', 'label', 'placeholder'));
    }

    public function login(Request $request, string $role)
    {
        $data = $request->validate([
            'identitas' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'max:200'],
        ]);

        $identitas = mb_strtolower(trim($data['identitas']));
        $gagal = back()->withInput($request->only('identitas'))
            ->withErrors(['identitas' => 'Login gagal: akun/kata sandi salah, atau akun sedang terkunci/nonaktif.']);

        // Peran dibatasi pada query: akun kasir tidak bisa masuk lewat form admin, dan sebaliknya.
        $user = User::query()->where('role', $role)
            ->where(fn ($q) => $q->where('username', $identitas)->orWhere('email', $identitas))
            ->first();

        $passwordOk = Md5Password::check($data['password'], $user?->password);

        if ($user && $user->isLocked()) {
            AuditLog::catat('login.ditolak_terkunci', 'users', $user->id, [], $user);

            return $gagal;
        }

        if (! $user || ! $passwordOk) {
            if ($user) {
                $user->failed_attempts++;
                if ($user->failed_attempts >= config('kasir.max_login_attempts')) {
                    $user->locked_until = now()->addMinutes(config('kasir.lock_minutes'));
                    $user->failed_attempts = 0;
                }
                $user->save();
            }
            AuditLog::catat('login.gagal', 'users', $user?->id, ['identitas' => mb_substr($identitas, 0, 60)], $user, $user?->tenant_id);

            return $gagal;
        }

        $user->load('tenant');
        if (! $user->isActive() || ($user->tenant_id && ! $user->tenant?->isActive())) {
            AuditLog::catat('login.ditolak_nonaktif', 'users', $user->id, [], $user);

            return $gagal;
        }

        $user->forceFill(['failed_attempts' => 0, 'locked_until' => null, 'last_login_at' => now()])->save();

        $request->session()->regenerate(); // cegah session fixation pada sesi CSRF/flash
        $cookie = $this->sessions->start($user, $request);
        AuditLog::catat('login.berhasil', 'users', $user->id, [], $user);

        return redirect()->intended($user->homeRoute())->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $session = $this->sessions->resolve($request->cookie($this->sessions->cookieName()));
        $role = $session?->user?->role ?? User::KASIR;
        if ($session) {
            AuditLog::catat('logout', 'users', $session->user_id, [], $session->user);
            $this->sessions->revoke($session);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(User::loginRouteFor($role))->withCookie(cookie()->forget($this->sessions->cookieName()));
    }
}
