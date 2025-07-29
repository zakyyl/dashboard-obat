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

        $results = DB::table('mlite_vedika')
            ->selectRaw("DATE_FORMAT(tanggal, '%m') AS bulan, COUNT(*) AS total")
            ->where('jenis', '2')
            // ->where('jenis', 'Ralan')
            ->where('status', 'Pengajuan')
            ->whereYear('tanggal', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

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
            $item = $results->firstWhere('bulan', $key);
            $dataFinal[] = $item ? $item->total : 0;
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

        $results = DB::table('mlite_vedika')
            ->selectRaw("DATE_FORMAT(tanggal, '%m') AS bulan, COUNT(*) AS total")
            ->where('jenis', 'Ranap')
            ->where('status', 'Pengajuan')
            ->whereYear('tanggal', $tahun)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

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
            $item = $results->firstWhere('bulan', $key);
            $dataFinal[] = $item ? $item->total : 0;
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
}
