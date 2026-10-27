<?php

namespace App\Support\Routing;
use Illuminate\Support\Facades\Route;
use Laravel\SerializableClosure\Support\ClosureStream;

final class PortalRoutes
{
    public static function tenant(\Closure|string $routes): void
    {
        Route::middleware(['web', 'auth', 'verified', 'tenant'])
        ->prefix('tenant/{tenant:slug}')
        ->name('tenant.')
        ->scopeBindings()
        ->group($routes);
    }
    public static function admin(\Closure|string $routes): void
    {
        Route::middleware(['web', 'auth', 'verifed', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group($routes);
    }
}
