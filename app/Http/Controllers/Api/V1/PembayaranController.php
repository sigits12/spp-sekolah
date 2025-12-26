<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TagihanSiswa;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    /**
     * List pembayaran (untuk menu Pembayaran)
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with([
            'tagihan:id,siswa_id,status_bayar,biaya_sekolah_id,kategori,bulan_tagihan,tahun_tagihan',
            'tagihan.siswa:id,nama',
            'tagihan.biayaSekolah'
        ])->orderByDesc('tanggal_bayar');

        // filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_bayar', $request->tanggal);
        }

        // filter metode
        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        return response()->json(
            $query->paginate(20)
        );
    }

    /**
     * Simpan pembayaran baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_tagihan'   => 'required|exists:tagihan_siswa,id_tagihan',
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode'       => 'required|in:tunai,transfer,qris',
            'keterangan'   => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {

            $tagihan = TagihanSiswa::lockForUpdate()
                ->findOrFail($request->id_tagihan);

            // total pembayaran sebelumnya
            $totalBayar = Pembayaran::where('id_tagihan', $tagihan->id_tagihan)
                ->sum('jumlah_bayar');

            $sisa = $tagihan->nominal - $totalBayar;

            if ($request->jumlah_bayar > $sisa) {
                abort(422, 'Jumlah bayar melebihi sisa tagihan');
            }

            // simpan pembayaran
            Pembayaran::create([
                'id_tagihan'   => $tagihan->id_tagihan,
                'tanggal_bayar'=> now()->toDateString(),
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode'       => $request->metode,
                'keterangan'   => $request->keterangan,
                'user_id'      => auth()->id()
            ]);

            // update status tagihan
            $totalSetelahBayar = $totalBayar + $request->jumlah_bayar;

            if ($totalSetelahBayar == $tagihan->nominal) {
                $tagihan->status = 'lunas';
            } elseif ($totalSetelahBayar > 0) {
                $tagihan->status = 'sebagian';
            }

            $tagihan->save();
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
