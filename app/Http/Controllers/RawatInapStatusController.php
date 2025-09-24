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

        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'kamar_inap.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('kamar', 'kamar_inap.kd_kamar', '=', 'kamar.kd_kamar')
            ->join('bangsal', 'kamar.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('dpjp_ranap', 'reg_periksa.no_rawat', '=', 'dpjp_ranap.no_rawat')
            ->select(
                'kamar_inap.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'penjab.png_jawab as cara_bayar',
                'kamar_inap.kd_kamar',
                DB::raw("concat(bangsal.nm_bangsal, ' (', kamar.kelas, ')') as kamar"),
                DB::raw("if(kamar_inap.tgl_keluar = '0000-00-00', '', kamar_inap.tgl_keluar) as tgl_keluar"),
                DB::raw("(SELECT nm_dokter FROM dokter WHERE kd_dokter = dpjp_ranap.kd_dokter) as nm_dokter_dpjp"),
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

        // --- PERUBAHAN UTAMA DI SINI ---
        // Mengambil grup NS unik dari tabel 'bangsal' yang namanya diawali "NS "
        $nurse_stations = DB::table('bangsal')
            ->select('ket')
            ->where('ket', 'like', 'NS %') // <--- Filter berdasarkan pola nama
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
        // Fungsi helper untuk memeriksa keberadaan berkas di tabel
        $cekBerkas = function ($table, $no_rawat_param) {
            // Gunakan try-catch untuk menangani jika tabel tidak ditemukan
            try {
                return DB::table($table)->where('no_rawat', $no_rawat_param)->exists() ? 'Ada' : 'Tidak Ada';
            } catch (\Illuminate\Database\QueryException $e) {
                // Jika tabel tidak ada, kembalikan status 'Tabel Error'
                // Ini membantu debugging jika ada nama tabel yang salah
                return 'Tabel Error';
            }
        };

        // Daftar lengkap pengecekan berkas rekam medis
        $data = [
            // Kategori: Pemeriksaan & Diagnosa Inti
            ['nama' => 'Pemeriksaan (SOAP/CPPT)', 'status' => $cekBerkas('pemeriksaan_ranap', $no_rawat)],
            ['nama' => 'Diagnosa (ICD 10)', 'status' => $cekBerkas('diagnosa_pasien', $no_rawat)],
            ['nama' => 'Tindakan (ICD 9)', 'status' => $cekBerkas('prosedur_pasien', $no_rawat)],

            // Kategori: Penilaian Awal
            ['nama' => 'Penilaian Awal Keperawatan', 'status' => $cekBerkas('penilaian_awal_keperawatan_ranap', $no_rawat)],
            ['nama' => 'Penilaian Awal Medis', 'status' => $cekBerkas('penilaian_medis_ranap', $no_rawat)],

            // Kategori: Khusus Pasien Operasi
            ['nama' => 'Checklist Pra Operasi', 'status' => $cekBerkas('checklist_pre_operasi', $no_rawat)],
            ['nama' => 'Penilaian Pra Anestesi', 'status' => $cekBerkas('penilaian_pre_anestesi', $no_rawat)],
            ['nama' => 'Laporan Operasi', 'status' => $cekBerkas('laporan_operasi', $no_rawat)],

            // Kategori: Monitoring Pasien
            ['nama' => 'Pemantauan PEWS Dewasa', 'status' => $cekBerkas('pemantauan_pews_dewasa', $no_rawat)],
            ['nama' => 'Penilaian Ulang Nyeri', 'status' => $cekBerkas('penilaian_ulang_nyeri', $no_rawat)],
            ['nama' => 'Monitoring Keseimbangan Cairan', 'status' => $cekBerkas('catatan_keseimbangan_cairan', $no_rawat)],
            ['nama' => 'Monitoring Cek GDS', 'status' => $cekBerkas('catatan_cek_gds', $no_rawat)],

            // Kategori: Catatan & Dokumentasi Tindakan
            ['nama' => 'Catatan Keperawatan', 'status' => $cekBerkas('catatan_keperawatan_ranap', $no_rawat)],
            ['nama' => 'Pemberian Transfusi Darah', 'status' => $cekBerkas('pemberian_transfusi_darah', $no_rawat)],
            ['nama' => 'Monitoring Transfusi Darah', 'status' => $cekBerkas('monitoring_reaksi_tranfusi', $no_rawat)],
            ['nama' => 'Dokumentasi ESWL', 'status' => $cekBerkas('hasil_tindakan_eswl', $no_rawat)],

            // Kategori: Pemulangan & Transfer
            ['nama' => 'Transfer Antar Ruang', 'status' => $cekBerkas('transfer_pasien_antar_ruang', $no_rawat)],
            ['nama' => 'Perencanaan Pemulangan (Discharge Planning)', 'status' => $cekBerkas('perencanaan_pemulangan', $no_rawat)],
            ['nama' => 'Resume Dokter', 'status' => $cekBerkas('resume_pasien_ranap', $no_rawat)],
            ['nama' => 'Resume Keperawatan', 'status' => $cekBerkas('resume_keperawatan', $no_rawat)],
            ['nama' => 'Informasi & Edukasi', 'status' => $cekBerkas('edukasi_pasien_keluarga', $no_rawat)],
            ['nama' => 'Edukasi Pasien & Keluarga', 'status' => $cekBerkas('edukasi_pasien_keluarga', $no_rawat)],
        ];

        // Kembalikan sebagai JSON Array, sesuai dengan kode JavaScript Anda yang sudah berjalan
        return response()->json($data);
    }
}
