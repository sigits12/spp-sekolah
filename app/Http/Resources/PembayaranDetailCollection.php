<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PembayaranDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn ($detail) =>
            $this->mapDetail($detail)
        )->all();
    }

    protected function mapDetail($detail)
    {
        $item = $this->getItem($detail->tagihan->biayaSekolah->kategori, $detail->tagihan->tahun_tagihan, $detail->tagihan->bulan_tagihan);
        return [
            'item' => $item,
            'jumlah_bayar' => $detail->jumlah_bayar,
        ];
    }

    protected function getItem($kategori, $tahun_tagihan, $bulan_tagihan)
    {
        return $bulan_tagihan ? $kategori.' - '.$this->getNamaBulan($bulan_tagihan).' '.$tahun_tagihan : $kategori;
    }

    protected function getNamaBulan($angka)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        return $bulan[$angka] ?? 'Bulan Tidak Valid';
    }

}
