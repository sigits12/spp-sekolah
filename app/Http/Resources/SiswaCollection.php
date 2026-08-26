<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SiswaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn ($siswa) =>
            $this->mapSiswa($siswa)
        )->all();
    }

    protected function mapSiswa($siswa)
    {
        return [
            'id' => $siswa->id,
            'nama' => $siswa->nama,
            'no_whatsapp' => $siswa->no_whatsapp,
            'kelas' =>$siswa->kelasAktif ? $siswa->kelasAktif->kelas->nama : null
        ];
    }
}
