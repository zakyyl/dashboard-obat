<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RawatJalanStatusController extends Controller
{
    public function index(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal', Carbon::today()->toDateString());
        $tanggal_akhir = $request->input('tanggal_akhir', Carbon::today()->toDateString());
        $no_rawat = $request->input('no_rawat');
        // Asumsi kita tetap menggunakan filter poliklinik dari permintaan sebelumnya
        $kd_poli = $request->input('kd_poli');

        $poliklinik = DB::table('poliklinik')
            ->where('status', '1')
            ->orderBy('nm_poli', 'asc')
            ->get();

        $data_ralan = DB::table('reg_periksa')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'dokter.nm_dokter',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'penjab.png_jawab as cara_bayar',
                'reg_periksa.status_lanjut',
                'reg_periksa.stts'
            )
            ->where('reg_periksa.stts', 'Sudah')
            ->where('reg_periksa.status_lanjut', 'Ralan')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggal_awal, $tanggal_akhir])
            ->when($no_rawat, function ($query, $no_rawat) {
                return $query->where('reg_periksa.no_rawat', 'like', '%' . $no_rawat . '%');
            })
            ->when($kd_poli, function ($query, $kd_poli) {
                return $query->where('reg_periksa.kd_poli', $kd_poli);
            })
            ->whereNotIn('reg_periksa.kd_poli', ['IGDK', 'POL08', 'U0081', 'U0035'])
            ->orderBy('reg_periksa.tgl_registrasi', 'asc')
            // --- PERUBAHAN DISINI ---
            // Ubah ->get() menjadi ->paginate()
            // Angka 15 berarti 15 data per halaman, bisa diubah sesuai kebutuhan.
            // ->appends($request->all()) penting agar filter tetap aktif saat pindah halaman.
            ->paginate(15)->appends($request->all());

        return view('dashboard.rawat_jalan_status', [
            'data_ralan' => $data_ralan,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'no_rawat' => $no_rawat,
            'poliklinik' => $poliklinik,
            'kd_poli_selected' => $kd_poli
        ]);
    }

    public function getKelengkapan($no_rawat)
    {
        $cekBerkas = function ($table, $no_rawat_param) {
            return DB::table($table)->where('no_rawat', $no_rawat_param)->exists() ? 'Ada' : 'Tidak Ada';
        };

        $data = [
            ['nama' => 'SOAP / CPPT Rajal', 'status' => $cekBerkas('pemeriksaan_ralan', $no_rawat)],
            ['nama' => 'Resume Medis Rawat Jalan', 'status' => $cekBerkas('resume_pasien', $no_rawat)],
            ['nama' => 'ICD 10', 'status' => $cekBerkas('diagnosa_pasien', $no_rawat)],
            ['nama' => 'ICD 9', 'status' => $cekBerkas('prosedur_pasien', $no_rawat)],
            ['nama' => 'Pemeriksaan Laboratorium', 'status' => $cekBerkas('periksa_lab', $no_rawat)],
            ['nama' => 'Resep Obat', 'status' => $cekBerkas('resep_obat', $no_rawat)],
            ['nama' => 'SEP', 'status' => $cekBerkas('bridging_sep', $no_rawat)],
            ['nama' => 'Hasil Pemeriksaan USG', 'status' => $cekBerkas('hasil_pemeriksaan_usg', $no_rawat)],
            ['nama' => 'Pemeriksaan Radiologi', 'status' => $cekBerkas('periksa_radiologi', $no_rawat)],
            ['nama' => 'Gambar Radiologi', 'status' => $cekBerkas('gambar_radiologi', $no_rawat)],
            ['nama' => 'Penilaian Awal Keperawatan Ralan', 'status' => $cekBerkas('penilaian_awal_keperawatan_ralan', $no_rawat)],
            ['nama' => 'Penilaian Medis Ralan', 'status' => $cekBerkas('penilaian_medis_ralan', $no_rawat)],
            ['nama' => 'Penilaian Awal Keperawatan Kebidanan', 'status' => $cekBerkas('penilaian_awal_keperawatan_kebidanan', $no_rawat)],
            ['nama' => 'Penilaian Medis Ralan Kandungan', 'status' => $cekBerkas('penilaian_medis_ralan_kandungan', $no_rawat)],
            ['nama' => 'Penilaian Awal Keperawatan Ralan Bayi', 'status' => $cekBerkas('penilaian_awal_keperawatan_ralan_bayi', $no_rawat)],
            ['nama' => 'Penilaian Medis Ralan Anak', 'status' => $cekBerkas('penilaian_medis_ralan_anak', $no_rawat)],
            ['nama' => 'Penilaian Awal Keperawatan Mata', 'status' => $cekBerkas('penilaian_awal_keperawatan_mata', $no_rawat)],
            ['nama' => 'Penilaian Medis Ralan Mata', 'status' => $cekBerkas('penilaian_medis_ralan_mata', $no_rawat)],
        ];

        return response()->json([
            'no_rawat_diterima' => $no_rawat,
            'data' => $data,
        ]);

        // return response()->json($data);
    }
}
