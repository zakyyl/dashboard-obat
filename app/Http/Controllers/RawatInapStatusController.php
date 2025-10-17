<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RawatInapStatusController extends Controller
{
    public function index(Request $request)
    {
        $ns_group = $request->input('ns_group');
        $tanggal_awal = $request->input('tanggal_awal', Carbon::now()->startOfMonth()->toDateString());
        $tanggal_akhir = $request->input('tanggal_akhir', Carbon::now()->endOfMonth()->toDateString());
        $no_rawat = $request->input('no_rawat');

        // --- OPTIMASI: MENGUBAH SUBQUERY MENJADI JOIN UNTUK PERFORMA LEBIH BAIK ---
        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('dpjp_ranap', 'reg_periksa.no_rawat', '=', 'dpjp_ranap.no_rawat')
            ->leftJoin('dokter', 'dpjp_ranap.kd_dokter', '=', 'dokter.kd_dokter') // <-- JOIN ini lebih efisien
            ->select(
                'kamar_inap.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'penjab.png_jawab as cara_bayar',
                'kamar_inap.kd_kamar',
                DB::raw("concat(bangsal.nm_bangsal, ' (', kamar.kelas, ')') as kamar"),
                DB::raw("if(kamar_inap.tgl_keluar = '0000-00-00', '', kamar_inap.tgl_keluar) as tgl_keluar"),
                'dokter.nm_dokter as nm_dokter_dpjp', // <-- Mengambil nama dokter dari JOIN
                'reg_periksa.status_lanjut'
            )
            ->where('reg_periksa.status_bayar', 'Sudah Bayar')
            ->where(function ($query) {
                $query->where('kamar_inap.stts_pulang', '<>', 'Pindah Kamar')
                    ->orWhere('kamar_inap.stts_pulang', '<>', '-');
            });

        if ($ns_group) {
            $query->where('bangsal.ket', $ns_group);
        } else {
            $query->whereBetween('kamar_inap.tgl_keluar', [$tanggal_awal, $tanggal_akhir]);
        }

        if ($no_rawat) {
            $query->where('kamar_inap.no_rawat', 'like', '%' . $no_rawat . '%');
        }

        $data_ranap = $query->orderBy('bangsal.nm_bangsal')
            ->orderBy('kamar_inap.tgl_keluar')
            ->paginate(15);

        $nurse_stations = DB::table('bangsal')
            ->select('ket')
            ->where('ket', 'like', 'NS %')
            ->distinct()
            ->orderBy('ket', 'asc')
            ->get();

        return view('dashboard.rawat_inap_status', [
            'data_ranap' => $data_ranap,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'no_rawat' => $no_rawat,
            'ns_group_selected' => $ns_group,
            'nurse_stations' => $nurse_stations,
        ]);
    }

    public function getKelengkapan($no_rawat)
    {
        // --- AWAL OPTIMASI N+1 QUERY ---

        // 1. Definisikan semua item yang akan dicek dalam satu array
        $checklistItems = [
            'diagnosa_pasien' => 'Diagnosa (ICD 10)',
            'prosedur_pasien' => 'Tindakan (ICD 9)',
            'penilaian_awal_keperawatan_ranap' => 'Penilaian Awal Keperawatan',
            'catatan_persalinan' => 'Catatan Persalinan',
            'penilaian_medis_ranap' => 'Penilaian Awal Medis',
            'checklist_kriteria_masuk_icu' => 'Kriteria Masuk ICU',
            'checklist_kriteria_keluar_icu' => 'Kriteria Keluar ICU',
            'checklist_pre_operasi' => 'Checklist Pra Operasi',
            'penilaian_pre_anestesi' => 'Penilaian Pra Anestesi',
            'laporan_operasi' => 'Laporan Operasi',
            'pemantauan_pews_dewasa' => 'Pemantauan PEWS Dewasa',
            'pemantauan_pews_anak' => 'Pemantauan PEWS Anak',
            'penilaian_ulang_nyeri' => 'Penilaian Ulang Nyeri',
            'catatan_keseimbangan_cairan' => 'Monitoring Keseimbangan Cairan',
            'catatan_cek_gds' => 'Monitoring Cek GDS',
            'catatan_keperawatan_ranap' => 'Catatan Keperawatan',
            'pemberian_transfusi_darah' => 'Pemberian Transfusi Darah',
            'monitoring_reaksi_tranfusi' => 'Monitoring Transfusi Darah',
            'hasil_tindakan_eswl' => 'Dokumentasi ESWL',
            'transfer_pasien_antar_ruang' => 'Transfer Antar Ruang',
            'perencanaan_pemulangan' => 'Perencanaan Pemulangan (Discharge Planning)',
            'resume_pasien_ranap' => 'Resume Dokter',
            'resume_keperawatan' => 'Resume Keperawatan',
            'edukasi_pasien_keluarga' => 'Informasi & Edukasi',
            'penilaian_medis_hemodialisa' => 'Penilaian Medis Hemodialisa',
            'catatan_observasi_hemodialisa' => 'Observasi Hemodialisa',
            'laporan_tindakan' => 'Laporan Tindakan',
            'pemberian_obat' => 'Pemberian Obat',
            'surat_pulang_atas_permintaan_sendiri' => 'Surat Pulang Atas Permintaan Sendiri',
            'surat_pernyataan_pasien_umum' => 'Surat Pernyataan Pasien Umum',
            'bridging_sep' => 'Data SEP',
        ];

        // 2. Bangun satu query besar untuk mengecek semua tabel sekaligus dengan UNION ALL
        $unionQueries = [];
        $bindings = [];
        $errorTables = [];

        foreach ($checklistItems as $table => $displayName) {
            // Kita gunakan try-catch untuk menangani jika ada nama tabel yang salah/tidak ada
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
        
        // 4. Siapkan data SOAP/CPPT (ini tetap butuh query sendiri karena ada submenu)
        $data = [];
        $soapSubmenu = [];
        $soapStatus = 'Tidak Ada';
        try {
            if (DB::table('pemeriksaan_ranap')->where('no_rawat', $no_rawat)->exists()) {
                $soapStatus = 'Ada';
                $soapSubmenu = DB::table('pemeriksaan_ranap')
                    ->join('pegawai', 'pemeriksaan_ranap.nip', '=', 'pegawai.nik')
                    ->where('pemeriksaan_ranap.no_rawat', $no_rawat)
                    ->select('pemeriksaan_ranap.tgl_perawatan', 'pegawai.nama as nm_dokter')
                    ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'desc')
                    ->get()
                    ->unique('tgl_perawatan')
                    ->map(function ($item) {
                        $item->tanggal = Carbon::parse($item->tgl_perawatan)->format('d-m-Y');
                        return $item;
                    })->values()->toArray();
            }
        } catch (\Exception $e) {
            $soapStatus = 'Tabel Error';
        }

        $data[] = [
            'nama' => 'Pemeriksaan (SOAP/CPPT)',
            'status' => $soapStatus,
            'submenu' => $soapSubmenu
        ];

        // 5. Susun hasil akhir TANPA query tambahan lagi ke database
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

        // --- KODE BARU ---
        return response()->json(['data' => $data]);
    }
}