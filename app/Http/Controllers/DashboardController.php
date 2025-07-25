<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Pastikan ini ada

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard utama.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Query untuk Pasien Hari Ini (total pasien registrasi hari ini, tidak termasuk 'APS%')
        $pasienHariIni = DB::table('reg_periksa')
            ->where('tgl_registrasi', DB::raw('CURDATE()'))
            ->where('no_rkm_medis', 'NOT LIKE', 'APS%')
            ->distinct('no_rawat')
            ->count();

        // Rawat jalan per bulan
        $rawatJalanPerBulan = DB::table('reg_periksa')
            ->select(
                DB::raw('MONTH(tgl_registrasi) as bulan'),
                DB::raw('COUNT(DISTINCT no_rawat) as jumlah')
            )
            ->whereYear('tgl_registrasi', now()->year)
            ->where('status_lanjut', 'Ralan')
            ->groupBy(DB::raw('MONTH(tgl_registrasi)'))
            ->get();

        // Kirim ke view
        return view('home', compact('pasienHariIni', 'rawatJalanPerBulan'));
    }
}
