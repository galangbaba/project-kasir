<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    use BelongsToTenant;

    protected $table = 'transaksi';
    protected $guarded = ['*']; // dibuat hanya lewat TransaksiService

    protected function casts(): array
    {
        return ['dibatalkan_at' => 'datetime'];
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function bisaDibatalkan(): bool
    {
        return $this->isSelesai()
            && $this->created_at->diffInMinutes(now(), true) <= config('kasir.void_minutes');
    }
}
