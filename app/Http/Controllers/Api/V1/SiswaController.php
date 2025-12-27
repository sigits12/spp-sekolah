<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;

class SiswaController extends Controller
{
    public function search(Request $request) {
        $query = $request->input('q');

        $siswa = Siswa::with([
                'kelasAktif' => function ($query) {
                    // Gunakan nama_tabel.kolom untuk menghindari ambigu
                    $query->select('riwayat_kelas.id', 'riwayat_kelas.siswa_id', 'riwayat_kelas.kelas_id');
                },
                'kelasAktif.kelas:id,nama'   // Ambil id dan kolom nama_kelas
            ])
            ->where('nama', 'ILIKE', "%{$query}%")
            ->select('id', 'nama')
            ->limit(10)->get();
        
        $data = $siswa->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'kelas' => $item->kelasAktif ? $item->kelasAktif->kelas->nama : null
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
        
    }
}
