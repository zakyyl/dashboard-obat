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
        // 1. Definisikan semua item yang akan dicek dalam satu array
        $checklistItems = [
            'pemeriksaan_ralan' => 'SOAP / CPPT Rajal',
            'resume_pasien' => 'Resume Medis Rawat Jalan',
            'diagnosa_pasien' => 'ICD 10',
            'prosedur_pasien' => 'ICD 9',
            'periksa_lab' => 'Pemeriksaan Laboratorium',
            'resep_obat' => 'Resep Obat',
            'bridging_sep' => 'SEP',
            'hasil_pemeriksaan_usg' => 'Hasil Pemeriksaan USG',
            'periksa_radiologi' => 'Pemeriksaan Radiologi',
            'gambar_radiologi' => 'Gambar Radiologi',
            'penilaian_awal_keperawatan_ralan' => 'Penilaian Awal Keperawatan Ralan',
            'penilaian_medis_ralan' => 'Penilaian Medis Ralan',
            'penilaian_awal_keperawatan_kebidanan' => 'Penilaian Awal Keperawatan Kebidanan',
            'penilaian_medis_ralan_kandungan' => 'Penilaian Medis Ralan Kandungan',
            'penilaian_awal_keperawatan_ralan_bayi' => 'Penilaian Awal Keperawatan Ralan Bayi',
            'penilaian_medis_ralan_anak' => 'Penilaian Medis Ralan Anak',
            'penilaian_awal_keperawatan_mata' => 'Penilaian Awal Keperawatan Mata',
            'penilaian_medis_ralan_mata' => 'Penilaian Medis Ralan Mata',
        ];

        // 2. Bangun satu query besar untuk mengecek semua tabel sekaligus dengan UNION ALL
        $unionQueries = [];
        $bindings = [];
        $errorTables = [];

        foreach ($checklistItems as $table => $displayName) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                    $unionQueries[] = "(SELECT '$table' as table_name FROM `$table` WHERE no_rawat = ? LIMIT 1)";
                    $bindings[] = $no_rawat;
                } else {
                     $errorTables[$table] = true;
                }
            } catch (\Exception $e) {
                $errorTables[$table] = true;
            }
        }

        // 3. Eksekusi query gabungan HANYA SEKALI
        $foundTables = [];
        if (!empty($unionQueries)) {
            $query_str = implode(" UNION ALL ", $unionQueries);
            $results = DB::select($query_str, $bindings);
            foreach ($results as $result) {
                $foundTables[$result->table_name] = true;
            }
        }

        // 4. Susun hasil akhir TANPA query tambahan lagi ke database
        $data = [];
        foreach ($checklistItems as $table => $displayName) {
             if (isset($errorTables[$table])) {
                $status = 'Tabel Error';
            } else {
                $status = isset($foundTables[$table]) ? 'Ada' : 'Tidak Ada';
            }
            $data[] = [
                'nama' => $displayName,
                'status' => $status
            ];
        }

        // --- PERBAIKAN DI SINI ---
        return response()->json([
            'data' => $data
        ]);
    }
}