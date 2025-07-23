<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\ObatSaatIniController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/obat/stok-barang', [ObatController::class, 'stokBarang'])->name('obat.stok-barang');
Route::get('/obat/search-obat', [ObatController::class, 'searchObat'])->name('obat.search-obat');

Route::get('/obat/obat-saat-ini', [ObatSaatIniController::class, 'index']);
Route::get('/obat/obat-saat-ini/search', [ObatSaatIniController::class, 'searchObatSaatIni'])->name('obat.search-obat-saat-ini');


