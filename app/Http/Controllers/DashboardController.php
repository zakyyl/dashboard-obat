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

        // Variabel lainnya dikosongkan (diatur ke 0)
        $pasienBaruHariIni = 0;
        $kunjunganHariIni = 0;
        $kunjunganBulanIni = 0;

        // Mengirim data ke view
        return view('home', compact('pasienHariIni', 'pasienBaruHariIni', 'kunjunganHariIni', 'kunjunganBulanIni'));
    }
}
