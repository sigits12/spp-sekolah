<?php

use App\Http\Controllers\Api\V1\BiayaSekolahController;
use App\Http\Controllers\Api\V1\TagihanSiswaController;
use App\Http\Controllers\Api\V1\PembayaranController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('keuangan')->group(function () {
        Route::get('/biaya-sekolah', [BiayaSekolahController::class, 'index']);
        Route::apiResource('tagihan', TagihanSiswaController::class);
        Route::get('/tagihan-siswa', [TagihanSiswaController::class, 'getGroupedTagihan']);
        Route::get('/generate-tagihan-siswa', [TagihanSiswaController::class, 'generateTagihan']);

        Route::apiResource('pembayaran', PembayaranController::class);

    });
});