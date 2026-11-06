<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use BelongsToTenant;

    protected $table = 'products';
    protected $fillable = ['nama_product', 'harga'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /** habis | menipis | tersedia */
    public function statusStok(): string
    {
        if ($this->stok <= 0) {
            return 'habis';
        }

        return $this->stok <= config('kasir.low_stock') ? 'menipis' : 'tersedia';
    }
}
