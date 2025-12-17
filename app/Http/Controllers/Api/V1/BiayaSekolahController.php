<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BiayaSekolah;

use Illuminate\Http\Request;

class BiayaSekolahController extends Controller
{
    public function index() {
        $biaya = BiayaSekolah::get(['kategori', 'nominal', 'keterangan']);
        return response()->json([
            'success' => true,
            'data' => $biaya
        ], 200);
    }
}
