<?php

// use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/{any}', function () {
    return view('app'); // File resources/views/app.blade.php
})->where('any', '.*');
