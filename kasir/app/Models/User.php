<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

/**
 * Pengguna. Kata sandi disimpan sebagai MD5 (lihat App\Support\Security\Md5Password).
 * Model ini TIDAK memakai cast "hashed" milik Laravel.
 */
class User extends Model implements Authenticatable
{
    use AuthenticatableTrait, HasFactory;

    public const SUPER_ADMIN = 'super_admin';
    public const ADMIN_TENANT = 'admin_tenant';
    public const KASIR = 'kasir';

    protected $table = 'users';

    // role & tenant_id & password tidak mass-assignable: selalu di-set eksplisit di controller
    protected $fillable = ['nama', 'email', 'username', 'status'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['locked_until' => 'datetime', 'last_login_at' => 'datetime'];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function homeRoute(): string
    {
        return match ($this->role) {
            self::SUPER_ADMIN => route('superadmin.dashboard'),
            self::ADMIN_TENANT => route('tenant.dashboard'),
            default => route('kasir.transaksi'),
        };
    }

    public function loginRoute(): string
    {
        return self::loginRouteFor($this->role);
    }

    public static function loginRouteFor(string $role): string
    {
        return match ($role) {
            self::SUPER_ADMIN => route('superadmin.login'),
            self::ADMIN_TENANT => route('tenant.login'),
            default => route('kasir.login'),
        };
    }
}
