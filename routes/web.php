<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PoliController;
use App\Http\Controllers\RawatJalanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RawatInapController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Rute POST untuk menangani proses login
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');

// Rute Logout
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

    // Tambahkan rute dashboard lainnya di sini jika memerlukan autentikasi
    // Route::get('/poli', function () {
    //     return "Halaman Poli";
    // })->name('poli');
    // Route::get('/ranap', function () {
    //     return "Halaman Ranap";
    // })->name('ranap');
    // Route::get('/labor', function () {
    //     return "Halaman Labor";
    // })->name('labor');
    // Route::get('/radiologi', function () {
    //     return "Halaman Radiologi";
    // })->name('radiologi');
    // Route::get('/pengajuan-klaim', function () {
    //     return "Halaman Pengajuan Klaim";
    // })->name('pengajuan-klaim');
});
