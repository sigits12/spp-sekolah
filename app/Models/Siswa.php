<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'id_siswa');
    }
}
