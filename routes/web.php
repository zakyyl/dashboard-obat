<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard/stok-barang', [DashboardController::class, 'stokBarang'])->name('dashboard.stok-barang');