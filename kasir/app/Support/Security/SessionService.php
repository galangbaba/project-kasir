<?php

namespace App\Support\Security;

use App\Models\AuthSession;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

/** Penerbitan, validasi, dan pencabutan sesi login (token opaque) — Bab 6.3 & 6.4. */
class SessionService
{
    public function cookieName(): string
    {
        return config('kasir.session_cookie');
    }

    /** Terbitkan sesi baru, kembalikan cookie berisi token MENTAH (hanya hash yang disimpan). */
    public function start(User $user, Request $request): Cookie
    {
        $token = OpaqueToken::issue(32);
        $minutes = config('kasir.session_ttl_hours') * 60;

        $session = new AuthSession;
        $session->forceFill([
            'user_id' => $user->id,
            'token_hash' => $token['hash'],
            'status' => 'active',
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            'last_seen_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
        ])->save();

        return cookie(
            name: $this->cookieName(),
            value: $token['plain'],
            minutes: $minutes,
            path: '/',
            domain: null,
            secure: $request->isSecure() || app()->isProduction(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        );
    }

    public function resolve(?string $plain): ?AuthSession
    {
        if (! is_string($plain) || $plain === '' || strlen($plain) > 128) {
            return null;
        }

        $session = AuthSession::query()->where('token_hash', OpaqueToken::hash($plain))->first();

        return ($session && $session->isActive()) ? $session : null;
    }

    public function revoke(AuthSession $session): void
    {
        $session->forceFill(['status' => 'revoked', 'revoked_at' => now()])->save();
    }

    /** Tandai kedaluwarsa & hapus baris lama (dijadwalkan harian). Validasi saat request tetap berjalan. */
    public function purge(): int
    {
        AuthSession::query()->where('status', 'active')->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        return AuthSession::query()->whereIn('status', ['expired', 'revoked'])
            ->where('updated_at', '<', now()->subDays(7))->delete();
    }
}
