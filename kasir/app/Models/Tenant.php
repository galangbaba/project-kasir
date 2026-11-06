<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $table = 'tenants';

    protected $fillable = ['nama_tenants', 'kategori_usaha', 'penanggung_jawab', 'status'];

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
