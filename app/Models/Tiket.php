<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    protected $fillable = [
        'nama_pemesan', 
        'tanggal_lihat',
        'harga', 
        'stok',
        'event_id'
    ];
}
