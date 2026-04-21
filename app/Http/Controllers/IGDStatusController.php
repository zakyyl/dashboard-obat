<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IGDStatusController extends Controller
{
    public function index(Request $request)
    {
        // Filter tanggal, default hari ini
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $no_rawat = $request->input('no_rawat');

        $data_igd = DB::table('reg_periksa')
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
                'reg_periksa.status_lanjut',
                'reg_periksa.stts'
            )
            ->where('reg_periksa.stts', 'Sudah')
            ->where('reg_periksa.kd_poli', 'IGDK')
            ->where('reg_periksa.tgl_registrasi', $tanggal)
            ->when($no_rawat, function ($query, $no_rawat) {
                return $query->where('reg_periksa.no_rawat', 'like', '%' . $no_rawat . '%');
            })
            ->orderBy('reg_periksa.tgl_registrasi', 'asc')
            ->paginate(15);

        return view('dashboard.igd_status', [
            'data_igd' => $data_igd,
            'tanggal' => $tanggal,
            'no_rawat' => $no_rawat
        ]);
    }

    public function getKelengkapan($no_rawat)
    {
        // Normalisasi no_rawat: hilangkan '/'
        $no_rawat_clean = str_replace('/', '', $no_rawat);

        // Helper untuk cek berkas (sesuaikan tabel sesuai kebutuhan IGD)
        $cekBerkas = function ($table, $no_rawat_param, $no_rawat_clean_param) {
            return DB::table($table)
                ->where(function ($query) use ($no_rawat_param, $no_rawat_clean_param) {
                    $query->where('no_rawat', $no_rawat_param)
                        ->orWhere('no_rawat', $no_rawat_clean_param);
                })
                ->exists() ? 'Ada' : 'Tidak Ada';
        };

        $data = [
            ['nama' => 'SOAP / CPPT',     'status' => $cekBerkas('pemeriksaan_ranap', $no_rawat, $no_rawat_clean)],
            ['nama' => 'Resume Medis',    'status' => $cekBerkas('resume_pasien_ranap', $no_rawat, $no_rawat_clean)],
            ['nama' => 'ICD 10',          'status' => $cekBerkas('diagnosa_pasien', $no_rawat, $no_rawat_clean)],
            ['nama' => 'ICD 9',           'status' => $cekBerkas('prosedur_pasien', $no_rawat, $no_rawat_clean)],
            ['nama' => 'Laboratorium',    'status' => $cekBerkas('periksa_lab', $no_rawat, $no_rawat_clean)],
            ['nama' => 'Radiologi',       'status' => $cekBerkas('periksa_radiologi', $no_rawat, $no_rawat_clean)],
            ['nama' => 'Resep Obat',      'status' => $cekBerkas('resep_obat', $no_rawat, $no_rawat_clean)],
        ];

        return response()->json([
            'no_rawat_diterima' => $no_rawat,
            'data' => $data,
        ]);
        // return response()->json($data);
    }
}
