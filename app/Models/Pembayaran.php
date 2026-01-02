<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tagihan_id',
        'tanggal_bayar',
        'siswa_id',
        'kelas_aktif_id',
        'total_bayar',
        'metode',
        'keterangan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tagihan()
    {
        return $this->belongsTo(TagihanSiswa::class, 'tagihan_id');
    }

    public function pembayaranDetail()
    {
        return $this->hasMany(PembayaranDetail::class, 'pembayaran_id');
    }
}
