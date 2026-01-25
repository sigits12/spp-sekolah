<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranDetail extends Model
{
    protected $table = 'pembayaran_detail';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'tagihan_siswa_id',
        'pembayaran_id',
        'jumlah_bayar',
        'created_at',
        'updated_at',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_siswa_id');
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id');
    }
}
