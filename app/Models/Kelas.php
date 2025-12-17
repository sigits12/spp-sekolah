<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'id_kelas');
    }
}
