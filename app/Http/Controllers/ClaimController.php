<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ClaimController extends Controller
{
    public function pengajuanClaimRalan(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        $bulanLengkap = [
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mei',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Agu',
            '09' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des',
        ];

        $dataFinal = [];
        foreach ($bulanLengkap as $key => $namaBulan) {
            $bulan = (int) $key;
            $total = $this->getTotalPengajuan($bulan, $tahun, 'Ralan');
            $dataFinal[] = $total;
        }

        $labels = array_values($bulanLengkap);
        $counts = $dataFinal;

        $startDate = Carbon::createFromDate($tahun, 1, 1);
        $endDate = Carbon::createFromDate($tahun, 12, 31);

        $seriesColumn = $counts;
        $seriesLine = count($counts) > 0 ? array_fill(0, count($counts), round(array_sum($counts) / count($counts))) : [];
        $categories = $labels;

        return view('dashboard.pengajuan_claim_ralan', compact(
            'labels',
            'counts',
            'tahun',
            'startDate',
            'endDate',
            'seriesColumn',
            'seriesLine',
            'categories'
        ));
    }

    public function pengajuanClaimRanap(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        $bulanLengkap = [
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mei',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Agu',
            '09' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des',
        ];

        $dataFinal = [];
        foreach ($bulanLengkap as $key => $namaBulan) {
            $bulan = (int) $key;
            $total = $this->getTotalPengajuan($bulan, $tahun, 'Ranap');
            $dataFinal[] = $total;
        }

        $labels = array_values($bulanLengkap);
        $counts = $dataFinal;

        $startDate = Carbon::createFromDate($tahun, 1, 1);
        $endDate = Carbon::createFromDate($tahun, 12, 31);

        $seriesColumn = $counts;
        $seriesLine = count($counts) > 0 ? array_fill(0, count($counts), round(array_sum($counts) / count($counts))) : [];
        $categories = $labels;

        return view('dashboard.pengajuan_claim_ranap', compact(
            'labels',
            'counts',
            'tahun',
            'startDate',
            'endDate',
            'seriesColumn',
            'seriesLine',
            'categories'
        ));
    }

    // Copy persis dari DashboardController
    private function getTotalPengajuan($bulan, $tahun, $jenis)
    {
        if ($jenis === 'Ranap') {
            // Untuk rawat inap, gunakan logika seperti indexBpjs
            $latestKamarInap = DB::table('kamar_inap')
                ->select('no_rawat', DB::raw('MAX(tgl_keluar) as tgl_keluar'))
                ->groupBy('no_rawat');

            return DB::table('mlite_vedika as v')
                ->joinSub($latestKamarInap, 'ki', function ($join) {
                    $join->on('v.no_rawat', '=', 'ki.no_rawat');
                })
                ->where('v.jenis', 'Ranap')
                ->where('v.status', 'Pengajuan')
                ->whereMonth('ki.tgl_keluar', $bulan)
                ->whereYear('ki.tgl_keluar', $tahun)
                ->count();
        } else {
            // Untuk rawat jalan (Ralan dan jenis '2')
            return DB::table('mlite_vedika')
                ->where(function ($q) {
                    $q->where('jenis', 'Ralan')
                        ->orWhere('jenis', '2');
                })
                ->where('status', 'Pengajuan')
                ->whereMonth('tgl_registrasi', $bulan)
                ->whereYear('tgl_registrasi', $tahun)
                ->count();
        }
    }
}