<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Support\Security\SessionService::class);
    }

    public function boot(): void
    {
        if ($this->app->isProduction()) {
            URL::forceHttps(); // NFR-SEC-01
        }

        // Pembatas laju login: per IP + identitas (Bab 6.4.1). Penguncian akun 5x gagal ada di LoginController.
        RateLimiter::for('login', fn (Request $r) => [
            Limit::perMinute(20)->by($r->ip()),
            Limit::perMinute(8)->by($r->ip() . '|' . mb_strtolower((string) $r->input('identitas'))),
        ]);

        Paginator::defaultView('pager');
    }
}
