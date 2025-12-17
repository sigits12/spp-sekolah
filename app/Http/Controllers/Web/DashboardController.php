<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BiayaSekolah;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\RiwayatKelas;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $jumlahKelas = Kelas::count();
        $jumlahSiswa = RiwayatKelas::tahunAktif()->distinct()->count('id_siswa');
        $totalBiaya = BiayaSekolah::count();
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        
        return view('dashboard.index', compact('jumlahKelas', 'jumlahSiswa', 'totalBiaya', 'tahunAktif'));
    }
}
