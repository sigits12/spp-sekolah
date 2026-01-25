<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanSiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanPembayaranController extends Controller
{
    public function rekapBulanan(Request $request)
    {
        $tahunPelajaranId = $request->tahun_ajaran_id ? $request->tahun_ajaran_id : 1 ;
        $bulan = $request->bulan; // 1 - 12
        $tahun = $request->tahun; // 2026

        // Validasi sederhana
        if (!$bulan || !$tahun) {
            return response()->json([
                'message' => 'Bulan dan tahun wajib diisi'
            ], 422);
        }

        // Ambil semua tagihan + pembayaran di bulan tsb
        $tagihan = TagihanSiswa::with([
                    'biayaSekolah',
                    'pembayaranDetails.pembayaran' => function ($q) use ($bulan, $tahun) {
                        $q->whereMonth('tanggal_bayar', $bulan)
                        ->whereYear('tanggal_bayar', $tahun);
                    }
                ])
                ->where('tahun_ajaran_id', $tahunPelajaranId)
                ->whereHas('pembayaranDetails.pembayaran', function ($q) use ($bulan, $tahun) {
                    $q->whereMonth('tanggal_bayar', $bulan)
                    ->whereYear('tanggal_bayar', $tahun);
                })
                ->get();


        // Helper rekap by tipe
        $rekapByTipe = function ($tipe) use ($tagihan) {
            return $tagihan
                ->filter(fn ($t) =>
                    strtoupper(optional($t->biayaSekolah)->tipe_tagihan) === $tipe
                )
                ->groupBy(fn ($t) => $t->biayaSekolah->kategori)
                ->map(function ($items, $namaItem) {

                    $totalTagihan = $items->sum('nominal_tagihan');

                    $totalDibayar = $items->sum(function ($item) {
                        return $item->pembayaranDetails
                            ->filter(fn ($d) => $d->pembayaran !== null)
                            ->sum('jumlah_bayar');
                    });

                    return [
                        'item' => $namaItem,
                        'total_tagihan' => $totalTagihan,
                        'total_dibayar' => $totalDibayar,
                        'sisa' => max(0, $totalTagihan - $totalDibayar),
                    ];
                })
                ->values();
        };

        // Rekap
        $rekapBulanan = $rekapByTipe('BULANAN');
        $rekapTahunan = $rekapByTipe('SEKALI');

        // Grand total bulan tsb
        $grandTagihan =
            $rekapBulanan->sum('total_tagihan') +
            $rekapTahunan->sum('total_tagihan');

        $grandDibayar =
            $rekapBulanan->sum('total_dibayar') +
            $rekapTahunan->sum('total_dibayar');

        $payments = $tagihan
            ->flatMap->pembayaranDetails
            ->filter(fn ($d) => $d->pembayaran)
            ->groupBy(fn ($d) => $d->pembayaran->metode);

        $dibayarTransfer  = $payments['transfer']->sum('jumlah_bayar') ?? 0;
        $dibayarTunai = $payments['tunai']->sum('jumlah_bayar') ?? 0;

        return response()->json([
            'periode' => [
                'bulan' => (int) $bulan,
                'tahun' => (int) $tahun,
                'label' => Carbon::create($tahun, $bulan)->translatedFormat('F Y'),
            ],
            'rekap' => [
                'bulanan' => $rekapBulanan,
                'tahunan' => $rekapTahunan,
            ],
            'grand_total' => [
                'total_tagihan' => $grandTagihan,
                'total_dibayar' => $grandDibayar,
                'dibayar_transfer' => $dibayarTransfer,
                'dibayar_tunai' => $dibayarTunai,
                'total_sisa' => max(0, $grandTagihan - $grandDibayar),
            ],
        ]);
    }
}
