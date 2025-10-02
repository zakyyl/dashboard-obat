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
        $cekBerkas = function ($table, $no_rawat_param) {
            try {
                return DB::table($table)->where('no_rawat', $no_rawat_param)->exists();
            } catch (\Illuminate\Database\QueryException $e) {
                return 'error';
            }
        };

        $getStatus = function($result) {
            if ($result === 'error') {
                return 'Tabel Error';
            }
            return $result ? 'Ada' : 'Tidak Ada';
        };

        $soapResult = $cekBerkas('pemeriksaan_ranap', $no_rawat);
        $soapExists = ($soapResult === true);
        $soapStatus = $getStatus($soapResult);
        $soapSubmenu = [];

        if ($soapExists) {
            try {
                $soapSubmenu = DB::table('pemeriksaan_ranap')
                    ->join('pegawai', 'pemeriksaan_ranap.nip', '=', 'pegawai.nik')
                    ->where('pemeriksaan_ranap.no_rawat', $no_rawat)
                    ->select('pemeriksaan_ranap.tgl_perawatan', 'pemeriksaan_ranap.jam_rawat', 'pegawai.nama as nm_dokter')
                    ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'desc')
                    ->orderBy('pemeriksaan_ranap.jam_rawat', 'desc')
                    ->get()
                    ->unique('tgl_perawatan')
                    ->map(function ($item) {
                        $item->tanggal = Carbon::parse($item->tgl_perawatan)->format('d-m-Y');
                        return $item;
                    })
                    ->values()
                    ->toArray();
            } catch (\Illuminate\Database\QueryException $e) {
                $soapSubmenu = [];
            }
        }

        $data = [
            [
                'nama' => 'Pemeriksaan (SOAP/CPPT)',
                'status' => $soapStatus,
                'submenu' => $soapSubmenu
            ],
            ['nama' => 'Diagnosa (ICD 10)', 'status' => $getStatus($cekBerkas('diagnosa_pasien', $no_rawat))],
            ['nama' => 'Tindakan (ICD 9)', 'status' => $getStatus($cekBerkas('prosedur_pasien', $no_rawat))],
            ['nama' => 'Penilaian Awal Keperawatan', 'status' => $getStatus($cekBerkas('penilaian_awal_keperawatan_ranap', $no_rawat))],
            // --- PENAMBAHAN BARU (KHUSUS KEBIDANAN) ---
            ['nama' => 'Catatan Persalinan', 'status' => $getStatus($cekBerkas('catatan_persalinan', $no_rawat))],
            ['nama' => 'Penilaian Awal Medis', 'status' => $getStatus($cekBerkas('penilaian_medis_ranap', $no_rawat))],
            ['nama' => 'Kriteria Masuk ICU', 'status' => $getStatus($cekBerkas('checklist_kriteria_masuk_icu', $no_rawat))],
            ['nama' => 'Kriteria Keluar ICU', 'status' => $getStatus($cekBerkas('checklist_kriteria_keluar_icu', $no_rawat))],
            ['nama' => 'Checklist Pra Operasi', 'status' => $getStatus($cekBerkas('checklist_pre_operasi', $no_rawat))],
            ['nama' => 'Penilaian Pra Anestesi', 'status' => $getStatus($cekBerkas('penilaian_pre_anestesi', $no_rawat))],
            ['nama' => 'Laporan Operasi', 'status' => $getStatus($cekBerkas('laporan_operasi', $no_rawat))],
            ['nama' => 'Pemantauan PEWS Dewasa', 'status' => $getStatus($cekBerkas('pemantauan_pews_dewasa', $no_rawat))],
            ['nama' => 'Pemantauan PEWS Anak', 'status' => $getStatus($cekBerkas('pemantauan_pews_anak', $no_rawat))],
            ['nama' => 'Penilaian Ulang Nyeri', 'status' => $getStatus($cekBerkas('penilaian_ulang_nyeri', $no_rawat))],
            ['nama' => 'Monitoring Keseimbangan Cairan', 'status' => $getStatus($cekBerkas('catatan_keseimbangan_cairan', $no_rawat))],
            ['nama' => 'Monitoring Cek GDS', 'status' => $getStatus($cekBerkas('catatan_cek_gds', $no_rawat))],
            ['nama' => 'Catatan Keperawatan', 'status' => $getStatus($cekBerkas('catatan_keperawatan_ranap', $no_rawat))],
            ['nama' => 'Pemberian Transfusi Darah', 'status' => $getStatus($cekBerkas('pemberian_transfusi_darah', $no_rawat))],
            ['nama' => 'Monitoring Transfusi Darah', 'status' => $getStatus($cekBerkas('monitoring_reaksi_tranfusi', $no_rawat))],
            ['nama' => 'Dokumentasi ESWL', 'status' => $getStatus($cekBerkas('hasil_tindakan_eswl', $no_rawat))],
            ['nama' => 'Transfer Antar Ruang', 'status' => $getStatus($cekBerkas('transfer_pasien_antar_ruang', $no_rawat))],
            ['nama' => 'Perencanaan Pemulangan (Discharge Planning)', 'status' => $getStatus($cekBerkas('perencanaan_pemulangan', $no_rawat))],
            ['nama' => 'Resume Dokter', 'status' => $getStatus($cekBerkas('resume_pasien_ranap', $no_rawat))],
            ['nama' => 'Resume Keperawatan', 'status' => $getStatus($cekBerkas('resume_keperawatan', $no_rawat))],
            ['nama' => 'Informasi & Edukasi', 'status' => $getStatus($cekBerkas('edukasi_pasien_keluarga', $no_rawat))],
            ['nama' => 'Penilaian Medis Hemodialisa', 'status' => $getStatus($cekBerkas('penilaian_medis_hemodialisa', $no_rawat))],
            ['nama' => 'Observasi Hemodialisa', 'status' => $getStatus($cekBerkas('catatan_observasi_hemodialisa', $no_rawat))],
            ['nama' => 'Laporan Tindakan', 'status' => $getStatus($cekBerkas('laporan_tindakan', $no_rawat))],
            ['nama' => 'Pemberian Obat', 'status' => $getStatus($cekBerkas('pemberian_obat', $no_rawat))],
            ['nama' => 'Surat Pulang Atas Permintaan Sendiri', 'status' => $getStatus($cekBerkas('surat_pulang_atas_permintaan_sendiri', $no_rawat))],
            ['nama' => 'Surat Pernyataan Pasien Umum', 'status' => $getStatus($cekBerkas('surat_pernyataan_pasien_umum', $no_rawat))],
            ['nama' => 'Data SEP', 'status' => $getStatus($cekBerkas('bridging_sep', $no_rawat))],
        ];

        return response()->json($data);
    }
}