<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanSiswa extends Model
{
    protected $table = 'tagihan_siswa';

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function biayaSekolah()
    {
        return $this->belongsTo(BiayaSekolah::class, 'biaya_sekolah_id');
    }
}
