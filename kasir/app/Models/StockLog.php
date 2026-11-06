<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/** Riwayat stok — append-only. */
class StockLog extends Model
{
    use BelongsToTenant;

    protected $table = 'stock_logs';
    public $timestamps = false;
    protected $guarded = ['*'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('stock_logs bersifat append-only.'));
        static::deleting(fn () => throw new LogicException('stock_logs bersifat append-only.'));
    }
}
