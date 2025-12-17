<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TagihanSiswa;
use App\Models\Siswa;
use App\Models\RiwayatKelas;
use App\Models\TahunAjaran;
use App\Models\BiayaSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagihanSiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = TagihanSiswa::with(['siswa', 'biayaSekolah']);

        if ($request->has('siswa_id')) {
            $query->where('siswa_id', $request->siswa_id);
        }

        $tagihan = $query->latest()->paginate(10);
        return response()->json($tagihan);
    }

    // 2. POST (Create) - Tambah Tagihan Manual
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'siswa_id' => 'required|exists:siswa,id',
            'biaya_sekolah_id' => 'required|exists:biaya_sekolah,id',
            'nominal_tagihan' => 'required|numeric',
            'bulan_tagihan' => 'required|integer|between:1,12',
            'tahun_tagihan' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $tagihan = TagihanSiswa::create($request->all());
        return response()->json(['message' => 'Tagihan berhasil dibuat', 'data' => $tagihan], 21);
    }

    // 3. GET SINGLE (Read Detail)
    public function show($id)
    {
        $tagihan = TagihanSiswa::with(['siswa', 'biayaSekolah'])->find($id);
        if (!$tagihan) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        
        return response()->json($tagihan);
    }

    // 4. PUT/PATCH (Update) - Ubah Nominal atau Status
    public function update(Request $request, $id)
    {
        $tagihan = TagihanSiswa::find($id);
        if (!$tagihan) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $tagihan->update($request->only([
            'nominal_tagihan', 
            'status_bayar', 
            'keterangan'
        ]));

        return response()->json(['message' => 'Tagihan berhasil diupdate', 'data' => $tagihan]);
    }

    // 5. DELETE (Delete)
    public function destroy($id)
    {
        $tagihan = TagihanSiswa::find($id);
        if (!$tagihan) return response()->json(['message' => 'Data tidak ditemukan'], 404);

        $tagihan->delete();
        return response()->json(['message' => 'Tagihan berhasil dihapus']);
    }

    public function generateTagihan(Request $request)
    {
        // 1. Ambil Tahun Ajaran Aktif
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        
        $tingkat = $request->tingkat;
        $tahun   = $tahunAjaran->id;

        $bulanGanjil = [7, 8, 9, 10, 11, 12];
        $tahunGanjil = $tahunAjaran->ganjil;
        $bulanGenap = [1, 2, 3, 4, 5, 6];
        $tahunGenap = $tahunAjaran->genap;


        $siswaList = Siswa::whereHas('riwayatKelas', function ($q) use ($tingkat, $tahun) {
            $q->where('id_tahun_ajaran', $tahun)
            ->whereHas('kelas', function ($q) use ($tingkat) {
                $q->where('tingkat', $tingkat);
            });
        })->get();

        // 2. Ambil Master Biaya (Buku & SPP) untuk tahun tersebut
        // Anda mungkin butuh filter tambahan seperti 'tingkat' jika biaya buku berbeda tiap kelas

        $sppPilihan = in_array($tingkat, [1, 2]) ? 'pilihan 2' : 'pilihan 1';

        $biayaList = BiayaSekolah::where('tahun_ajaran_id', $tahun)
            ->where(function ($q) use ($tingkat, $sppPilihan) {

                // 🔹 SPP sesuai pilihan
                $q->where(function ($q) use ($sppPilihan) {
                    $q->where('kategori', 'SPP')
                    ->where('keterangan', $sppPilihan);
                })

                // 🔹 BUKU sesuai tingkat
                ->orWhere(function ($q) use ($tingkat) {
                    $q->where('kategori', 'BUKU')
                    ->where('tingkat', $tingkat);
                })

                // 🔹 Biaya lain (selain SPP & BUKU)
                ->orWhereNotIn('kategori', ['SPP', 'BUKU']);
            })
            ->get();

        $biayaBulanan = $biayaList->where('tipe_tagihan', 'BULANAN');
        $biayaSekali  = $biayaList->where('tipe_tagihan', 'SEKALI');

        $dataInsert = [];

        foreach ($siswaList as $siswa) {

            /* =========================
            TAGIHAN BULANAN
            ========================= */

            foreach ($biayaBulanan as $biaya) {

                // Semester Ganjil
                foreach ($bulanGanjil as $bulan) {
                    $dataInsert[] = [
                        'id_siswa'        => $siswa->id_siswa,
                        'biaya_sekolah_id'        => $biaya->id,
                        'kategori'        => $biaya->kategori,
                        'nominal'         => $biaya->nominal,
                        'bulan_tagihan'   => $bulan,
                        'tahun_tagihan'   => $tahunGanjil,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'status'          => 'BELUM_BAYAR',
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }

                // Semester Genap
                foreach ($bulanGenap as $bulan) {
                    $dataInsert[] = [
                        'id_siswa'        => $siswa->id_siswa,
                        'biaya_sekolah_id'        => $biaya->id,
                        'kategori'        => $biaya->kategori,
                        'nominal'         => $biaya->nominal,
                        'bulan_tagihan'   => $bulan,
                        'tahun_tagihan'   => $tahunGenap,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'status'          => 'BELUM_BAYAR',
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            /* =========================
            TAGIHAN SEKALI
            ========================= */

            foreach ($biayaSekali as $biaya) {
                $dataInsert[] = [
                    'id_siswa'        => $siswa->id_siswa,
                    'biaya_sekolah_id'        => $biaya->id,
                    'kategori'        => $biaya->kategori,
                    'nominal'         => $biaya->nominal,
                    'bulan_tagihan'   => null,
                    'tahun_tagihan'   => $tahunGanjil,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'status'          => 'BELUM_BAYAR',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        // 3. Simpan sekaligus (Mass Insert)
        TagihanSiswa::insert($dataInsert);

        return response()->json(['message' => 'Tagihan SPP dan Buku berhasil diterbitkan']);
    }
}
