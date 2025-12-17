<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaSekolah extends Model
{
    protected $table = 'biaya_sekolah';
    protected $fillable = ['kategori', 'nominal', 'keterangan', 'tahun_ajaran_id'];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
