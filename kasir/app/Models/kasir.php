<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kasir extends Model
{
    protected $table = "Kasirs";

    protected $fillable = [
        'nama_kasir',
        'username',
        'password',
    ];
}
