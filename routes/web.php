<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObatController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/obat/stok-barang', [ObatController::class, 'stokBarang'])->name('obat.stok-barang');
Route::get('/obat/search-obat', [ObatController::class, 'searchObat'])->name('obat.search-obat');


