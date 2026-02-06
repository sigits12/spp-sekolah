<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PembayaranSiswaCollection;
use App\Http\Resources\PembayaranDetailCollection;
use App\Models\Siswa;
use App\Models\TagihanSiswa;
use App\Models\Pembayaran;
use App\Models\PembayaranDetail;
use App\Models\BiayaSekolah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function autofill($siswaId)
    {
        $bulanan = TagihanSiswa::query()
            ->where('siswa_id', $siswaId)
            ->whereHas('biayaSekolah', fn ($q) =>
                $q->where('tipe_tagihan', 'BULANAN')
            )
            ->where('sisa_pembayaran', '>', 0)
            ->get();

        $bulananGrouped = $bulanan->groupBy('biaya_sekolah_id');

        $bulananResult = $bulananGrouped->map(function ($items) {
        return [
            'biaya_sekolah_id' => $items->first()->biaya_sekolah_id,
            'kategori' => $items->first()->biayaSekolah->kategori,
            'nominal_tagihan' => $items->first()->nominal_tagihan,
            'outstanding' => $items->count(),
            'periode' => $items->map(fn ($i) => [
                'bulan' => $i->bulan_tagihan,
                'tahun' => $i->tahun_tagihan
            ])->values()
        ];
        })
        ->sortByDesc('nominal_tagihan')
        ->values();

        $nonBulanan = TagihanSiswa::query()
            ->where('siswa_id', $siswaId)
            ->whereHas('biayaSekolah', fn ($q) =>
                $q->where('tipe_tagihan', 'SEKALI')
            )
            ->where('sisa_pembayaran', '>', 0)
            ->get();

        $nonBulananResult = $nonBulanan->map(function ($item) {
            return [
                'biaya_sekolah_id' => $item->biaya_sekolah_id,
                'kategori' => $item->biayaSekolah->kategori,
                'remaining' => $item->sisa_pembayaran
            ];
        })->sortByDesc('nominal_tagihan');

        return response()->json([
            'bulanan' => $bulananResult,
            'non_bulanan' => $nonBulananResult
        ]);
    }

    public function detail($id) 
    {
        $query = PembayaranDetail::with([
                                'tagihan:id,biaya_sekolah_id,bulan_tagihan,tahun_tagihan',
                                'tagihan.biayaSekolah:id,kategori'
                            ])
                            ->where('pembayaran_id', $id)
                            ->get();

        return new PembayaranDetailCollection($query);
    }

    /**
     * List pembayaran (untuk menu Pembayaran)
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with([
                'siswa:id,nama',
                'siswa.kelasAktif.kelas',
                'pembayaranDetail',
            ])
            ->select('id', 'tanggal_bayar', 'siswa_id', 'kelas_aktif_id', 'metode')
            ->withSum('pembayaranDetail as total_bayar', 'jumlah_bayar') 
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        return new PembayaranSiswaCollection($query);
    }
    /**
     * Simpan pembayaran baru
     */
    public function store(Request $r)
    {
        $r->validate([
            'siswa_id'   => 'required|exists:siswa,id',
            'total_bayar' => 'required|numeric|min:1',
            // 'metode'       => 'required|in:tunai,transfer,qris',
        ]);

        $r->metode = $r->metode ? $r->metode : 'tunai';

        DB::transaction(function () use ($r) {

            $pembayaran = Pembayaran::create([
                    'siswa_id'       => $r->siswa_id,
                    'kelas_aktif_id' => $r->kelas_aktif_id,
                    'tanggal_bayar'  => now(),
                    'metode'         => $r->metode,
                    'total_bayar'    => 0, // sementara
                ]);

            $totalAkumulasi = 0;
            $t = [];
            foreach ($r->pembayaran['bulanan'] as $item) {

                $tagihanBulanan  = TagihanSiswa::where([
                    'siswa_id'        => $r->siswa_id,
                    'kelas_aktif_id'  => $r->kelas_aktif_id,
                    'biaya_sekolah_id'=> $item['biaya_sekolah_id'],
                ])
                ->where('sisa_pembayaran', '>', 0)
                ->whereNotNull('bulan_tagihan')
                ->whereNotNull('tahun_tagihan')
                ->orderBy('tahun_tagihan')
                ->orderBy('bulan_tagihan')
                ->lockForUpdate()
                ->get();


                $jumlahBulan = $item['jumlah_bulan'];
                
                foreach ($tagihanBulanan as $tagihan) {
                    
                    if ($jumlahBulan <= 0) continue;

                    $sudahDibayar = PembayaranDetail::where('tagihan_siswa_id', $tagihan->id)
                        ->sum('jumlah_bayar');
                        
                    $dibayar = $tagihan->nominal_tagihan - $sudahDibayar;
                    if ($dibayar <= 0) continue;
                    
                    PembayaranDetail::create([
                        'pembayaran_id'     => $pembayaran->id,
                        'tagihan_siswa_id'  => $tagihan->id,
                        'jumlah_bayar'      => $dibayar,
                    ]);

                    $sisaBaru = $tagihan->sisa_pembayaran - $dibayar;

                    $tagihan->update([
                        'sisa_pembayaran' => $sisaBaru
                    ]);
                    $jumlahBulan --;
                    $totalAkumulasi += $dibayar;
                }
            }

            foreach ($r->pembayaran['non_bulanan'] as $item) {

                $sisaBayar = $item['nominal'];

                $tagihan = TagihanSiswa::where([
                            'siswa_id'        => $r->siswa_id,
                            'kelas_aktif_id'  => $r->kelas_aktif_id,
                            'biaya_sekolah_id'=> $item['biaya_sekolah_id'],
                        ])
                        ->where('sisa_pembayaran', '>', 0)
                        ->whereNull('bulan_tagihan')
                        ->lockForUpdate()
                        ->firstOrFail();
                
                $sudahDibayar = PembayaranDetail::where('tagihan_siswa_id', $tagihan->id)
                    ->sum('jumlah_bayar');
                
                $dibayar = min($sisaBayar, $tagihan->sisa_pembayaran);

                if ($sisaBayar <= 0) continue;

                $pembayaranDetail = PembayaranDetail::create([
                    'pembayaran_id'     => $pembayaran->id,
                    'tagihan_siswa_id'  => $tagihan->id,
                    'jumlah_bayar'      => $dibayar,
                ]);

                $sisaBaru = $tagihan->sisa_pembayaran - $dibayar;
                $tagihan->update([
                    'sisa_pembayaran' => $sisaBaru
                ]);

                $totalAkumulasi += $dibayar;
            }

            $pembayaran->update([
                'total_bayar' => $totalAkumulasi,
            ]);

            if ($totalAkumulasi != $r->total_bayar) {
                throw new \Exception('Total pembayaran tidak sesuai detail');
            }
        });


        return response()->json([
            'message' => 'Pembayaran berhasil disimpan'
        ], 201);
    }

    /**
     * Detail satu pembayaran
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihan.siswa',
            'tagihan.biaya'
        ])->findOrFail($id);

        return response()->json($pembayaran);
    }

    /**
     * Update pembayaran (jika perlu koreksi)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode'       => 'required|in:tunai,transfer,qris',
            'keterangan'   => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $id) {

            $pembayaran = Pembayaran::findOrFail($id);
            $tagihan = TagihanSiswa::lockForUpdate()
                ->findOrFail($pembayaran->id_tagihan);

            // hitung ulang total pembayaran
            $totalBayar = Pembayaran::where('id_tagihan', $tagihan->id_tagihan)
                ->where('id_pembayaran', '!=', $pembayaran->id_pembayaran)
                ->sum('jumlah_bayar');

            if ($totalBayar + $request->jumlah_bayar > $tagihan->nominal) {
                abort(422, 'Jumlah bayar melebihi nominal tagihan');
            }

            $pembayaran->update([
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode'       => $request->metode,
                'keterangan'   => $request->keterangan
            ]);

            // update status tagihan
            $totalAkhir = $totalBayar + $request->jumlah_bayar;

            if ($totalAkhir == 0) {
                $tagihan->status = 'belum';
            } elseif ($totalAkhir < $tagihan->nominal) {
                $tagihan->status = 'sebagian';
            } else {
                $tagihan->status = 'lunas';
            }

            $tagihan->save();
        });

        return response()->json([
            'message' => 'Pembayaran berhasil diperbarui'
        ]);
    }

    /**
     * Hapus pembayaran
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $pembayaran = Pembayaran::findOrFail($id);
            $tagihan = TagihanSiswa::lockForUpdate()
                ->findOrFail($pembayaran->id_tagihan);

            $pembayaran->delete();

            // hitung ulang status tagihan
            $totalBayar = Pembayaran::where('id_tagihan', $tagihan->id_tagihan)
                ->sum('jumlah_bayar');

            if ($totalBayar == 0) {
                $tagihan->status = 'belum';
            } elseif ($totalBayar < $tagihan->nominal) {
                $tagihan->status = 'sebagian';
            } else {
                $tagihan->status = 'lunas';
            }

            $tagihan->save();
        });

        return response()->json([
            'message' => 'Pembayaran berhasil dihapus'
        ]);
    }
}
