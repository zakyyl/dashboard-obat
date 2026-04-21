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
            'pasienMobileJknHariIni' => $this->getPasienMobileJknHariIni(),
            'rawatJalanPerBulan' => $this->getRawatJalanPerBulan(),
            'caraBayar' => $this->getCaraBayar(),
            'resepHariIni' => $this->getResepHariIni()->count(),
            'kematianPerBulan' => $this->getKematianPerBulan(),
            'rawatInapHariIni' => $this->getRawatInapHariIni()->count(),
            'rawatInapHariIniData' => $this->getRawatInapHariIni(),
            'rawatIgdHariIni' => $this->getPasienIgdHariIni(),
            'rawatpolri'=> $this->getPasienPolriHariIni(),
            'totalPemasukanHariIni' => $this->getPemasukanHariIni(),
            'totalPengeluaranHariIni' => $this->getPengeluaranHariIni(),
            'saldoHariIni' => $this->getSaldoHariIni(),
            'totalPemasukanBulanIni' => $this->getPemasukanBulanIni(),
            'totalPengeluaranBulanIni' => $this->getPengeluaranBulanIni(),
            'saldoBulanIni' => $this->getSaldoBulanIni(),
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

    private function getPasienIgdHariIni()
    {
        return DB::table('reg_periksa')
            ->whereDate('tgl_registrasi', DB::raw('CURDATE()'))
            ->where('kd_poli', 'IGDK')
            ->distinct('no_rawat')
            ->count();
    }

    private function getPasienPolriHariIni()
{
    return DB::table('reg_periksa')
        ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
        ->join('pasien_polri', 'pasien_polri.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('pangkat_polri', 'pasien_polri.pangkat_polri', '=', 'pangkat_polri.id')
        ->join('satuan_polri', 'pasien_polri.satuan_polri', '=', 'satuan_polri.id')
        ->join('golongan_polri', 'pasien_polri.golongan_polri', '=', 'golongan_polri.id')
        ->join('jabatan_polri', 'pasien_polri.jabatan_polri', '=', 'jabatan_polri.id')
        ->whereDate('reg_periksa.tgl_registrasi', DB::raw('CURDATE()'))
        ->distinct('reg_periksa.no_rawat')
        ->count('reg_periksa.no_rawat');
}



//     private function getPasienHariIni()
// {
//     return DB::table('reg_periksa')
//         ->whereDate('tgl_registrasi', now()) 
//         ->where('no_rkm_medis', 'NOT LIKE', 'APS%')
//         ->select(DB::raw('COUNT(DISTINCT no_rawat) as total'))
//         ->value('total'); 
// }

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
            ->whereMonth('reg_periksa.tgl_registrasi', now()->month)
            ->whereYear('reg_periksa.tgl_registrasi', now()->year)
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

    private function getRawatInapHariIni()
    {
        return DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
            ->join('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
            ->join('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
            ->select(
                'kamar_inap.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                DB::raw("CONCAT(pasien.alamat, ', ', kelurahan.nm_kel, ', ', kecamatan.nm_kec, ', ', kabupaten.nm_kab) as alamat"),
                'penjab.png_jawab',
                DB::raw("CONCAT(kamar_inap.kd_kamar, ' ', bangsal.nm_bangsal) as kamar"),
                'kamar.trf_kamar',
                'kamar_inap.diagnosa_awal',
                'kamar_inap.diagnosa_akhir',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar',
                'kamar_inap.ttl_biaya',
                'kamar_inap.stts_pulang',
                'dokter.nm_dokter',
                DB::raw("
                IF(
                    kamar_inap.stts_pulang = 'Pindah Kamar',
                    IFNULL(DATEDIFF(NOW(), kamar_inap.tgl_masuk), 0),
                    IFNULL(DATEDIFF(NOW(), kamar_inap.tgl_masuk) + 1, 1)
                ) AS lama
            ")
            )
            ->where('kamar_inap.stts_pulang', '-')
            ->whereDate('kamar_inap.tgl_masuk', DB::raw('CURDATE()'))
            // ->whereDate('kamar_inap.tgl_masuk', '>=', now()->subDays(7)->toDateString()) cuman untuk testing
            ->orderBy('bangsal.nm_bangsal')
            ->orderBy('kamar_inap.tgl_masuk')
            ->orderBy('kamar_inap.jam_masuk')
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

    private function getPasienMobileJknHariIni()
{
    return DB::table('referensi_mobilejkn_bpjs')
        ->whereDate('tanggalperiksa', now()->toDateString())
        ->count();
}
private function getPemasukanHariIni()
    {
        return DB::table('pemasukan_viz')
            ->whereDate('tanggal', now()->toDateString())
            ->sum('jumlah');
    }

    private function getPengeluaranHariIni()
    {
        return DB::table('pengeluaran_viz')
            ->whereDate('tanggal', now()->toDateString())
            ->sum('jumlah');
    }

    private function getSaldoHariIni()
    {
        $pemasukan = $this->getPemasukanHariIni();
        $pengeluaran = $this->getPengeluaranHariIni();
        return $pemasukan - $pengeluaran;
    }

    private function getPemasukanBulanIni()
    {
        return DB::table('pemasukan_viz')
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('jumlah');
    }

    private function getPengeluaranBulanIni()
    {
        return DB::table('pengeluaran_viz')
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('jumlah');
    }

    private function getSaldoBulanIni()
    {
        $pemasukan = $this->getPemasukanBulanIni();
        $pengeluaran = $this->getPengeluaranBulanIni();
        return $pemasukan - $pengeluaran;
    }
}

