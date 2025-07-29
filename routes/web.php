<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PoliController;
use App\Http\Controllers\RawatJalanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RawatInapController;
use App\Http\Controllers\PengajuanClaimController;



Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    Route::get('/obat/stok-barang', [ObatController::class, 'stokBarang'])->name('obat.stok-barang');
    Route::get('/obat/search-obat', [ObatController::class, 'searchObat'])->name('obat.search-obat');

    Route::get('/dashboard/poli', [PoliController::class, 'index'])->name('dashboard.poli');
    Route::get('/dashboard/poli-perdokter', [PoliController::class, 'indexPerDokter']);

    Route::get('/dashboard/labor-kunjungan-ralan', [RawatJalanController::class, 'index'])->name('dashboard.labor');
    Route::get('/dashboard/radiologi-kunjungan-ralan', [RawatJalanController::class, 'indexRadiologi'])->name('radiologi.kunjungan.ralan');

    Route::get('/dashboard/labor-kunjungan-ranap', [RawatInapController::class, 'index'])->name('dashboard.labor');
    Route::get('/dashboard/radiologi-kunjungan-ranap', [RawatInapController::class, 'indexRadiologi'])->name('radiologi.kunjungan.ranap');

    // Route::get('/dashboard/pasien-ranap', [RawatInapController::class, 'index'])->name('dashboard.pasien.ranap');
    Route::get('/dashboard/pasien-ranap', [RawatInapController::class, 'pasienRanap'])->name('dashboard.pasien.ranap');
    Route::get('/dashboard/pasien-ralan', [RawatJalanController::class, 'pasienRalan'])->name('dashboard.pasien.ralan');


    Route::get('/dashboard/pengajuan-claim', [PengajuanClaimController::class, 'index']);
});
