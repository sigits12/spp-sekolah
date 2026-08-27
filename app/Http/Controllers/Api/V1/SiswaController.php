<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;

class SiswaController extends Controller
{
    public function search(Request $request) {
        $query = $request->input('q');

        $siswa = Siswa::whereHas('kelasAktif')
            ->with([
                'kelasAktif' => function ($query) {
                    $query->select('riwayat_kelas.id', 'riwayat_kelas.siswa_id', 'riwayat_kelas.kelas_id', 'tahun_ajaran_id');
                },
                'kelasAktif.kelas:id,nama'
            ])
            ->where('nama', 'ILIKE', "%{$query}%")
            ->select('id', 'nama')
            ->limit(10)->get();
        
        $data = $siswa->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'kelas' => $item->kelasAktif ? $item->kelasAktif->kelas->nama : null,
                'kelas_aktif_id' => $item->kelasAktif ? $item->kelasAktif->kelas->id : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
        
    }

    public function index() {
        $siswa = Siswa::whereHas('kelasAktif')
            ->with([
                'kelasAktif' => function ($query) {
                    $query->select(
                        'riwayat_kelas.id',
                        'riwayat_kelas.siswa_id',
                        'riwayat_kelas.kelas_id',
                        'tahun_ajaran_id'
                    );
                },
                'kelasAktif.kelas:id,nama'
            ])
            ->select('id', 'nama')
            ->get();

        $data = $siswa->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'kelas' => $item->kelasAktif?->kelas?->nama,
                'kelas_aktif_id' => $item->kelasAktif?->kelas?->id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function list(Request $request) {
        $siswa = Siswa::whereHas('kelasAktif')
            ->with([
                'kelasAktif' => function ($query) {
                    $query->select(
                        'riwayat_kelas.id',
                        'riwayat_kelas.siswa_id',
                        'riwayat_kelas.kelas_id',
                        'tahun_ajaran_id'
                    );
                },
                'kelasAktif.kelas:id,nama'
            ])
            ->select('id', 'nama', 'no_whatsapp')
            ->paginate(30);

        $data = $siswa->map(function ($item) {
            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'no_whatsapp' => $item->no_whatsapp,
                'kelas' => $item->kelasAktif?->kelas?->nama,
                'kelas_aktif_id' => $item->kelasAktif?->kelas?->id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
