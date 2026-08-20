<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BiayaSekolahController;
use App\Http\Controllers\Api\V1\TagihanSiswaController;
use App\Http\Controllers\Api\V1\PembayaranController;
use App\Http\Controllers\Api\V1\LaporanPembayaranController;
use App\Http\Controllers\Api\V1\SiswaController;
use App\Http\Controllers\Api\V1\ImportController;
use App\Http\Controllers\Api\V1\RiwayatKelasController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout sukses']);
    });

    Route::get('/me', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('role:admin,tu')->group(function () {
            Route::get('/siswa-index', [SiswaController::class, 'index']);
        });
    });
    
    Route::prefix('keuangan')->group(function () {
        Route::get('/biaya-sekolah', [BiayaSekolahController::class, 'index']);
        Route::apiResource('tagihan', TagihanSiswaController::class);
        Route::get('/tagihan-siswa', [TagihanSiswaController::class, 'getGroupedTagihan']);
        Route::get('/generate-tagihan-siswa', [TagihanSiswaController::class, 'generateTagihanV2']);
        Route::get('/rekap/tagihan-siswa/{siswa_id}', [TagihanSiswaController::class, 'rekap']);
        Route::get('/rekap/pembayaran/{id}', [PembayaranController::class, 'detail']);

        Route::apiResource('pembayaran', PembayaranController::class);
        Route::get('/pembayaran/autofill/{siswa_id}', [PembayaranController::class, 'autofill']);
        
        Route::get('/laporan/rekap-bulanan', [LaporanPembayaranController::class, 'rekapBulanan']);
        Route::get('/pembayaran/siswa/{siswa_id}/history', [PembayaranController::class, 'history']);
    });

    Route::get('/siswa/search', [SiswaController::class, 'search']);
    Route::get('/import/pembayaran', [ImportController::class, 'pembayaran']);
    Route::get('/promote-class', [RiwayatKelasController::class, 'promoteClass']);

});