<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiwayatKelas;
use DB;

class RiwayatKelasController extends Controller
{
   public function promoteClass(Request $request)
    {
        try {
            DB::beginTransaction();

            $rk = RiwayatKelas::where('tahun_ajaran_id', 1)->with('siswa')->get();

            $data = [];
            foreach ($rk as $st) {
                if($st->kelas_id < 6) {
                    $data[] = [
                        'siswa_id'       => $st->siswa_id,
                        'kelas_id'       => (int)$st->kelas_id + 1,
                        'tahun_ajaran_id'=> 2,
                    ];
                } else {
                    continue;
                }
            }

            DB::table('riwayat_kelas')->insert($data);
            DB::commit();

            return response()->json([
                'message' => 'Siswa berhasil dinaikkan ke kelas berikutnya dan riwayat kelas diperbarui.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal memproses kenaikan kelas: ' . $e->getMessage(),
            ], 500);
        }
    }
}