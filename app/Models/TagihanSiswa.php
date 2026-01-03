<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanSiswa extends Model
{
    protected $table = 'tagihan_siswa';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $casts = [
        'nominal_tagihan' => 'integer',
        'sisa_pembayaran' => 'integer',
    ];
    protected $fillable = [
        'siswa_id',
        'biaya_sekolah_id',
        'nominal_tagihan',
        'kategori',
        'bulan_tagihan',
        'tahun_tagihan',
        'tahun_ajaran_id',
        'sisa_pembayaran',
        'created_at',
        'updated_at',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function biayaSekolah()
    {
        return $this->belongsTo(BiayaSekolah::class, 'biaya_sekolah_id');
    }

    public function pembayaranDetails()
    {
        return $this->hasMany(PembayaranDetail::class, 'tagihan_siswa_id');
    }

    public function scopeBelumLunas($q)
    {
        return $q->where('sisa_pembayaran', '>', 0);
    }

    public function scopeBulanan($q)
    {
        return $q->whereNotNull('bulan_tagihan')
                ->whereNotNull('tahun_tagihan');
    }

    public function scopeTahunan($q)
    {
        return $q->whereNull('bulan_tagihan');
    }

}
