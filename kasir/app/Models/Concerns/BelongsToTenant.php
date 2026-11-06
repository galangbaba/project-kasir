<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Isolasi tenant (SRS: "Tenant A tidak dapat mengakses data Tenant B").
 * Untuk pengguna yang punya tenant_id, SETIAP query otomatis dibatasi ke tenant-nya,
 * tenant_id diambil dari sesi terautentikasi di server — bukan dari masukan klien.
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $query) {
            $user = Auth::user();
            if ($user && $user->tenant_id) {
                $query->where($query->getModel()->getTable() . '.tenant_id', $user->tenant_id);
            }
        });

        static::creating(function ($model) {
            $user = Auth::user();
            if ($user && $user->tenant_id) {
                $model->tenant_id = $user->tenant_id; // paksa, abaikan nilai dari luar
            }
        });
    }
}
