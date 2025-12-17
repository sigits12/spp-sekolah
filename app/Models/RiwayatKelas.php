<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKelas extends Model
{
    protected $table = "riwayat_kelas";

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function scopeTahunAktif($query)
    {
        return $query->whereHas('tahunAjaran', fn($q) => $q->where('is_aktif', true));
    }
}
