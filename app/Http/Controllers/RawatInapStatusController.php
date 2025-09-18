<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RawatInapStatusController extends Controller
{
    public function index(Request $request)
    {
        // Filter tanggal sekarang berdasarkan tgl_keluar, default 1 bulan terakhir
        $tanggal_awal = $request->input('tanggal_awal', Carbon::now()->startOfMonth()->toDateString());
        $tanggal_akhir = $request->input('tanggal_akhir', Carbon::now()->endOfMonth()->toDateString());
        $no_rawat = $request->input('no_rawat');
        $kd_bangsal = $request->input('kd_bangsal');

        $data_ranap = DB::table('kamar_inap')
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
            })
            ->whereBetween('kamar_inap.tgl_keluar', [$tanggal_awal, $tanggal_akhir])
            // Filter 'no_rawat' (inpatient treatment number)
            ->when($no_rawat, function ($query, $no_rawat) {
                return $query->where('kamar_inap.no_rawat', 'like', '%' . $no_rawat . '%');
            })
            // Add a new filter for 'kd_bangsal' (nurse station code)
            ->when($kd_bangsal, function ($query, $kd_bangsal) {
                return $query->where('bangsal.kd_bangsal', $kd_bangsal);
            })
            ->orderBy('bangsal.nm_bangsal')
            ->orderBy('kamar_inap.tgl_keluar')
            ->paginate(15);

        // Fetch all unique nurse stations for the filter dropdown
        $nurse_stations = DB::table('bangsal')->get();

        return view('dashboard.rawat_inap_status', [
            'data_ranap' => $data_ranap,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'no_rawat' => $no_rawat,
            'kd_bangsal' => $kd_bangsal, // Pass the selected nurse station code back to the view
            'nurse_stations' => $nurse_stations, // Pass the list of nurse stations to the view
        ]);
    }

    public function getKelengkapan($no_rawat)
    {
        $cekBerkas = function ($table, $no_rawat) {
            return DB::table($table)->where('no_rawat', $no_rawat)->exists() ? 'Ada' : 'Tidak Ada';
        };

        $data = [
            ['nama' => 'SOAP / CPPT Ranap', 'status' => $cekBerkas('pemeriksaan_ranap', $no_rawat)],
            ['nama' => 'Resume Medis Rawat Inap', 'status' => $cekBerkas('resume_pasien_ranap', $no_rawat)],
            ['nama' => 'ICD 10', 'status' => $cekBerkas('diagnosa_pasien', $no_rawat)],
            ['nama' => 'ICD 9', 'status' => $cekBerkas('prosedur_pasien', $no_rawat)],
            ['nama' => 'Pemeriksaan Laboratorium', 'status' => $cekBerkas('periksa_lab', $no_rawat)],
            ['nama' => 'Pemeriksaan Radiologi', 'status' => $cekBerkas('periksa_radiologi', $no_rawat)],
            ['nama' => 'Resep Obat', 'status' => $cekBerkas('resep_obat', $no_rawat)],
        ];

        return response()->json($data);
    }
}
