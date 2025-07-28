<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard utama.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('home', [
            'pasienHariIni' => $this->getPasienHariIni(),
            'rawatJalanPerBulan' => $this->getRawatJalanPerBulan(),
            'caraBayar' => $this->getCaraBayar(),
            'resepHariIni' => $this->getResepHariIni()->count(),
            'ranapPerBulan' => $this->getRanapPerBulan(),
            'kematianPerBulan' => $this->getKematianPerBulan(),
        ]);
    }

    private function getPasienHariIni()
    {
        return DB::table('reg_periksa')
            ->where('tgl_registrasi', DB::raw('CURDATE()'))
            ->where('no_rkm_medis', 'NOT LIKE', 'APS%')
            ->distinct('no_rawat')
            ->count();
    }

    private function getRawatJalanPerBulan()
    {
        return DB::table('reg_periksa')
            ->select(
                DB::raw('MONTH(tgl_registrasi) as bulan'),
                DB::raw('COUNT(DISTINCT no_rawat) as jumlah')
            )
            ->whereYear('tgl_registrasi', now()->year)
            ->where('status_lanjut', 'Ralan')
            ->groupBy(DB::raw('MONTH(tgl_registrasi)'))
            ->get();
    }

    private function getCaraBayar()
    {
        return DB::table('reg_periksa')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select('penjab.png_jawab', DB::raw('COUNT(*) as jumlah'))
            ->whereBetween('reg_periksa.tgl_registrasi', [now()->subDays(3)->toDateString(), now()->toDateString()])
            ->groupBy('penjab.png_jawab')
            ->get();
    }

    private function getResepHariIni()
    {
        return DB::table('resep_obat')
            ->join('reg_periksa', 'resep_obat.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'resep_obat.kd_dokter', '=', 'dokter.kd_dokter')
            ->select(
                'resep_obat.no_resep',
                'resep_obat.tgl_perawatan',
                'resep_obat.jam',
                'resep_obat.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'resep_obat.kd_dokter',
                'dokter.nm_dokter'
            )
            ->whereDate('resep_obat.tgl_perawatan', DB::raw('CURDATE()'))
            ->orderBy('resep_obat.tgl_perawatan')
            ->orderBy('resep_obat.jam')
            ->get();
    }

    private function getRanapPerBulan()
    {
        return DB::table('reg_periksa')
            ->select(
                DB::raw("DATE_FORMAT(tgl_registrasi, '%Y-%m') AS bulan"),
                DB::raw('COUNT(*) AS jumlah')
            )
            ->where('status_lanjut', 'Ranap')
            ->whereBetween('tgl_registrasi', ['2025-01-01', '2025-12-31'])
            ->groupBy(DB::raw("DATE_FORMAT(tgl_registrasi, '%Y-%m')"))
            ->orderBy(DB::raw("DATE_FORMAT(tgl_registrasi, '%Y-%m')"))
            ->get();
    }

    private function getKematianPerBulan()
    {
        return DB::table('pasien_mati')
            ->select(
                DB::raw("DATE_FORMAT(tanggal, '%Y-%m') AS bulan"),
                DB::raw('COUNT(*) AS jumlah')
            )
            ->whereYear('tanggal', now()->year)
            ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"))
            ->orderBy(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"))
            ->get();
    }
}
