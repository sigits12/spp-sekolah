<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RiwayatPembayaranCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     * 
     */

    protected $staticCategories = [
                'SPP',
                'EKSTRAKURIKULER',
                'KOMITE',
                'UJIAN',
                'BUKU',
                'SARPRAS',
                'PEMBANGUNAN'
            ];
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn ($pembayaran) =>
            $this->mapRiwayat($pembayaran)
        )->all();
    }

    protected function mapRiwayat($pembayaran)
    {
        $staticCategories = $this->staticCategories;
        // Set dasar untuk baris data
        $row = [
            'id'           => $pembayaran->id,
            'tanggal'      => $pembayaran->tanggal_bayar,
            'total_bayar'  => (float) $pembayaran->total_bayar,
            'metode'       => $pembayaran->metode ?? '-', // penyesuaian jika ada kolom metode
        ];

        $kategoriData = [];
        foreach ($staticCategories as $cat) {
            $kategoriData[$cat] = 0;
        }

        foreach ($pembayaran->pembayaranDetail as $detail) {
            if ($detail->tagihan) {
                $keyKategori = str_replace(' ', '_', trim($detail->tagihan->kategori));
                if (in_array($keyKategori, $staticCategories)) {
                    $kategoriData[$keyKategori] += (float) $detail->jumlah_bayar;
                }
            }
        }

        $row['kategori'] = $kategoriData;

        return $row;
    }

    public function with(Request $request): array
    {
        return [
            'status' => 'success',
            'meta'   => [
                'columns' => $this->staticCategories
            ]
        ];
    }


}
