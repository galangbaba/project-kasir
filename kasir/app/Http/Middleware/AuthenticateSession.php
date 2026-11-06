<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\Security\SessionService;
use Closure;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memvalidasi sesi login pada SETIAP permintaan (Bab 6.4.4) lalu memeriksa peran (RBAC).
 * Pemakaian: ->middleware('auth.session:admin_tenant')
 *
 * PENTING: mengimplementasikan AuthenticatesRequests membuat middleware ini berjalan SEBELUM
 * SubstituteBindings, sehingga route-model-binding sudah memakai global scope tenant (isolasi tenant).
 */
class AuthenticateSession implements AuthenticatesRequests
{
    public function __construct(private SessionService $sessions) {}

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $loginRole = $roles[0] ?? User::KASIR;
        $session = $this->sessions->resolve($request->cookie($this->sessions->cookieName()));
        $user = $session?->user()->with('tenant')->first();

        $valid = $user
            && $user->isActive()
            && ($user->tenant_id === null || $user->tenant?->isActive());

        if (! $valid) {
            if ($session) {
                $this->sessions->revoke($session);
            }

            return redirect(User::loginRouteFor($loginRole))
                ->withCookie(cookie()->forget($this->sessions->cookieName()));
        }

        if ($roles && ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        Auth::setUser($user);
        $request->attributes->set('authSession', $session);

        if (! $session->last_seen_at || $session->last_seen_at->diffInSeconds(now(), true) > 60) {
            $session->forceFill(['last_seen_at' => now()])->save();
        }

        return $next($request);
    }
}
