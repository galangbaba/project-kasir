<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use LogicException;

/** Audit log append-only (NFR-AUD-01). */
class AuditLog extends Model
{
    protected $table = 'audit_logs';
    public $timestamps = false;
    protected $guarded = ['*'];

    protected function casts(): array
    {
        return ['meta' => 'array', 'created_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('audit_logs bersifat append-only.'));
        static::deleting(fn () => throw new LogicException('audit_logs bersifat append-only.'));
    }

    public static function catat(string $aksi, ?string $entitas = null, ?int $entitasId = null, array $meta = [], ?User $user = null, ?int $tenantId = null): void
    {
        $user ??= Auth::user();
        $log = new static;
        $log->forceFill([
            'tenant_id' => $tenantId ?? $user?->tenant_id,
            'user_id' => $user?->id,
            'aksi' => $aksi,
            'entitas' => $entitas,
            'entitas_id' => $entitasId,
            'meta' => $meta ?: null,
            'ip_address' => request()?->ip(),
        ])->save();
    }
}
