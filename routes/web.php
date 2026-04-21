<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PoliController;
use App\Http\Controllers\RawatJalanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RawatInapController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\RawatJalanStatusController;
use App\Http\Controllers\RawatInapStatusController;
use App\Http\Controllers\IGDStatusController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\PasienPolriController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {


    Route::middleware('role:admin')->group(function () {
        Route::get('/home', [DashboardController::class, 'index'])->name('home');

        // Route::get('/obat/stok-barang', [ObatController::class, 'stokBarang'])->name('obat.stok-barang');
        // Route::get('/obat/search-obat', [ObatController::class, 'searchObat'])->name('obat.search-obat');
        // Route::get('/obat/stok-barang-per-depo', [ObatController::class, 'stokBarangPerDepo'])->name('obat.stok-barang-per-depo');
        // Route::get('/obat/stok-barang-masuk', [ObatController::class, 'stokBarangMasuk'])->name('obat.stok-barang-masuk');
        // Route::get('/obat/stok-barang-keluar', [ObatController::class, 'stokBarangKeluar'])->name('obat.stok-barang-keluar');
        Route::get('/dashboard/poli', [PoliController::class, 'index'])->name('dashboard.poli');
        Route::get('/dashboard/poli-perdokter', [PoliController::class, 'indexPerDokter']);
        Route::get('/dashboard/labor-kunjungan-ralan', [RawatJalanController::class, 'index'])->name('dashboard.labor');
        Route::get('/dashboard/radiologi-kunjungan-ralan', [RawatJalanController::class, 'indexRadiologi'])->name('radiologi.kunjungan.ralan');
        Route::get('/dashboard/labor-kunjungan-ranap', [RawatInapController::class, 'index'])->name('dashboard.labor');
        Route::get('/dashboard/radiologi-kunjungan-ranap', [RawatInapController::class, 'indexRadiologi'])->name('radiologi.kunjungan.ranap');
        Route::get('/dashboard/pasien-ranap', [RawatInapController::class, 'pasienRanap'])->name('dashboard.pasien.ranap');
        Route::get('/dashboard/pasien-ralan', [RawatJalanController::class, 'pasienRalan'])->name('dashboard.pasien.ralan');
        Route::get('/pengajuan-claim-ralan', [ClaimController::class, 'pengajuanClaimRalan'])->name('pengajuan.claim.ralan');
        Route::get('/pengajuan-claim-ranap', [ClaimController::class, 'pengajuanClaimRanap'])->name('pengajuan.claim.ranap');
        Route::get('/kamar', [KamarController::class, 'index'])->name('kamar.index');
        Route::get('/dashboard/dokter', [DokterController::class, 'index'])->name('dokter.index');
        Route::get('/dashboard/pasien-polri', [PasienPolriController::class, 'pasienPolri'])->name('dashboard.pasienPolri');
    });

     Route::middleware('role:obat,admin')->group(function () {

        Route::get('/obat/stok-barang', [ObatController::class, 'stokBarang'])->name('obat.stok-barang');
        Route::get('/obat/search-obat', [ObatController::class, 'searchObat'])->name('obat.search-obat');
        Route::get('/obat/stok-barang-per-depo', [ObatController::class, 'stokBarangPerDepo'])->name('obat.stok-barang-per-depo');
        Route::get('/obat/stok-barang-masuk', [ObatController::class, 'stokBarangMasuk'])->name('obat.stok-barang-masuk');
        Route::get('/obat/stok-barang-keluar', [ObatController::class, 'stokBarangKeluar'])->name('obat.stok-barang-keluar');

    });
    
    Route::middleware('role:erm,admin')->group(function () {
        Route::get('/dashboard/status-rm-ralan', [RawatJalanStatusController::class, 'index'])->name('statusrm-ralan.index');
        Route::get('/dashboard/status-rm-ralan/kelengkapan/{no_rawat}', [RawatJalanStatusController::class, 'getKelengkapan'])
            ->name('status-rm-ralan.get-kelengkapan')
            ->where('no_rawat', '.*');

        Route::get('/dashboard/status-rm-ranap', [RawatInapStatusController::class, 'index'])->name('statusrm-ranap.index');
        Route::get('/dashboard/status-rm-ranap/kelengkapan/{no_rawat}', [RawatInapStatusController::class, 'getKelengkapan'])
            ->name('status-rm-ranap.get-kelengkapan')
            ->where('no_rawat', '.*');

        Route::get('/dashboard/status-rm-igd', [IGDStatusController::class, 'index'])->name('statusrm-igd.index');
        Route::get('/dashboard/status-rm-igd/kelengkapan/{no_rawat}', [IGDStatusController::class, 'getKelengkapan'])
            ->name('status-rm-igd.get-kelengkapan')
            ->where('no_rawat', '.*');
    });

    Route::middleware('role:keuangan,admin')->group(function () {
        Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
        Route::post('/pemasukan/store', [PemasukanController::class, 'store'])->name('pemasukan.store');
        Route::get('/pemasukan/{id}/edit', [PemasukanController::class, 'edit'])->name('pemasukan.edit');
        Route::post('/pemasukan/{id}/update', [PemasukanController::class, 'update'])->name('pemasukan.update');
        Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');
        Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
        Route::post('/pengeluaran/store', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
        Route::get('/pengeluaran/{id}/edit', [PengeluaranController::class, 'edit'])->name('pengeluaran.edit');
        Route::post('/pengeluaran/{id}/update', [PengeluaranController::class, 'update'])->name('pengeluaran.update');
        Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');
        Route::get('/keuangan/rekap', [KeuanganController::class, 'rekap'])->name('keuangan.rekap');
    });
});
