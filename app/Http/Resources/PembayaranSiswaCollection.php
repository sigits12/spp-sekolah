<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PembayaranSiswaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn ($pembayaran) =>
            $this->mapTagihan($pembayaran)
        )->all();
    }

    protected function mapTagihan($pembayaran)
    {
        return [
            'id' => $pembayaran->id,
            'tanggal_bayar' => $pembayaran->tanggal_bayar,
            'siswa' => [
                'nama' => $pembayaran->siswa->nama,
                'kelas' => $pembayaran->siswa->kelasAktif->kelas->nama
            ],
            'metode' => $pembayaran->metode,
            'total_bayar' => $pembayaran->total_bayar,
        ];
    }

    protected function status($nominal, $dibayar)
    {
        if ($dibayar <= 0) {
            return 'BELUM LUNAS';
        }

        if ($dibayar < $nominal) {
            return 'PARTIAL';
        }

        return 'LUNAS';
    }

    protected function summary()
    {
        $all = $this->resource->getCollection();

        return [
            'total_tagihan' => $all->sum('nominal'),
            'total_dibayar' => $all->sum(
                fn ($t) => $t->pembayaran->sum('jumlah')
            ),
            'total_sisa' => $all->sum(
                fn ($t) => max(0, $t->nominal - $t->pembayaran->sum('jumlah'))
            ),
        ];
    }


}
