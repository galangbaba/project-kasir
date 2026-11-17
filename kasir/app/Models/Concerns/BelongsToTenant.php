<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // Membatasi query berdasarkan tenant yang sedang login
        static::addGlobalScope('tenant', function (Builder $query) {
            $user = Auth::user();

            if ($user !== null && $user->tenant_id !== null) {
                $table = $query->getModel()->getTable();

                $query->where(
                    $table . '.tenant_id',
                    '=',
                    $user->tenant_id
                );
            }
        });

        // Mengisi tenant_id otomatis saat membuat data baru
        static::creating(function (Model $model) {
            $user = Auth::user();

            if ($user !== null && $user->tenant_id !== null) {
                $model->tenant_id = $user->tenant_id;
            }
        });
    }
}
