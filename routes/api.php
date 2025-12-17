<?php

use App\Http\Controllers\Api\V1\BiayaSekolahController;
use App\Http\Controllers\Api\V1\TagihanSiswaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('keuangan')->group(function () {
        Route::get('/biaya-sekolah', [BiayaSekolahController::class, 'index']);
        Route::apiResource('tagihan', TagihanSiswaController::class);
        Route::get('/generate-tagihan-siswa', [TagihanSiswaController::class, 'generateTagihan']);
    });
});