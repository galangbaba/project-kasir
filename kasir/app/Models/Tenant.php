<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $table = 'tenants';

    protected $fillable = [
        'nama_tenants',
        'kategori_usaha',
        'penanggung_jawab',
        'status',
        'slug'];

        //membuat slug otomatis ketika tenant ditambahkan
    protected static function booted(): void {
        static::creating(function (Tenant $tenant) {

            $slug = Str::slug($tenant->nama_teanants);
            $original = $slug;
            $counter = 1;

            //memastikan slug tidak duplikat
            while (static::where('slug', $slug)->exists()) {
                $slug = $original . '_' . $counter++;
            }

        $tenant->slug = $slug;
        });
    }

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
