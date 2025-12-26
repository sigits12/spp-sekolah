<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    public function tagihan()
    {
        return $this->belongsTo(TagihanSiswa::class, 'tagihan_id');
    }
}
