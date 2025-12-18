<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    public function kelasAktif()
    {
        return $this->hasOne(RiwayatKelas::class)->latestOfMany();
    }

    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'siswa_id', 'id');
    }

    public function tagihan()
    {
        return $this->hasMany(TagihanSiswa::class, 'siswa_id');
    }
}
