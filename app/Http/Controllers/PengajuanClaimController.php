<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class PengajuanClaimController extends Controller
{
    public function index(Request $request)
    {
        $startMonth = $request->input('start_month', Carbon::now()->format('Y-m'));
        $endMonth = $request->input('end_month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::parse($startMonth)->startOfMonth()->toDateString();
        $endDate = Carbon::parse($endMonth)->endOfMonth()->toDateString();

        $cacheKey = md5($startDate . $endDate);

        $totalRawatJalan = Cache::remember("total_rawat_jalan_$cacheKey", 10, fn () =>
            $this->countRegPeriksa('ralan', $startDate, $endDate)
        );

        $totalRawatInap = Cache::remember("total_rawat_inap_$cacheKey", 10, fn () =>
            $this->countRegPeriksa('ranap', $startDate, $endDate)
        );

        $pengajuanRawatJalan = Cache::remember("pengajuan_ralan_$cacheKey", 10, fn () =>
            $this->countPengajuan('ralan', $startDate, $endDate)
        );

        $pengajuanRawatInap = Cache::remember("pengajuan_ranap_$cacheKey", 10, fn () =>
            $this->countPengajuan('ranap', $startDate, $endDate)
        );

        return view('dashboard.pengajuan_claim', compact(
            'totalRawatJalan',
            'totalRawatInap',
            'pengajuanRawatJalan',
            'pengajuanRawatInap',
            'startDate',
            'endDate'
        ));
    }

    public function claimRalan(Request $request)
    {
        $startMonth = $request->input('start_month', Carbon::now()->startOfYear()->format('Y-m'));
        $endMonth = $request->input('end_month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::parse($startMonth)->startOfMonth()->toDateString();
        $endDate = Carbon::parse($endMonth)->endOfMonth()->toDateString();
        $cacheKey = md5("ralan-$startDate-$endDate");

        $totalRawatJalan = Cache::remember("total_rawat_jalan_$cacheKey", 10, fn () =>
            $this->countRegPeriksa('ralan', $startDate, $endDate)
        );

        $pengajuanRawatJalan = Cache::remember("pengajuan_ralan_$cacheKey", 10, fn () =>
            $this->countPengajuan('ralan', $startDate, $endDate)
        );

        $chartData = $this->getChartData('ralan', $startMonth, $endMonth);

        return view('dashboard.pengajuan_claim_ralan', array_merge(
            compact('totalRawatJalan', 'pengajuanRawatJalan', 'startDate', 'endDate'),
            $chartData
        ));
    }

    public function claimRanap(Request $request)
    {
        $startMonth = $request->input('start_month', Carbon::now()->startOfYear()->format('Y-m'));
        $endMonth = $request->input('end_month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::parse($startMonth)->startOfMonth()->toDateString();
        $endDate = Carbon::parse($endMonth)->endOfMonth()->toDateString();
        $cacheKey = md5("ranap-$startDate-$endDate");

        $totalRawatInap = Cache::remember("total_rawat_inap_$cacheKey", 10, fn () =>
            $this->countRegPeriksa('ranap', $startDate, $endDate)
        );

        $pengajuanRawatInap = Cache::remember("pengajuan_ranap_$cacheKey", 10, fn () =>
            $this->countPengajuan('ranap', $startDate, $endDate)
        );

        $chartData = $this->getChartData('ranap', $startMonth, $endMonth);

        return view('dashboard.pengajuan_claim_ranap', array_merge(
            compact('totalRawatInap', 'pengajuanRawatInap', 'startDate', 'endDate'),
            $chartData
        ));
    }

    private function countRegPeriksa($statusLanjut, $start, $end)
    {
        return DB::table('reg_periksa')
            ->where('kd_pj', 'BP1')
            ->where('status_lanjut', $statusLanjut)
            ->whereBetween('tgl_registrasi', [$start, $end])
            ->count();
    }

    private function countPengajuan($jenis, $start, $end)
    {
        return DB::table('mlite_vedika')
            ->where('jenis', $jenis)
            ->where('status', 'Pengajuan')
            ->whereBetween('tanggal', [$start, $end])
            ->count();
    }

    private function getChartData($jenis, $startMonth, $endMonth)
    {
        // Parse start dan end month
        $startDate = Carbon::parse($startMonth . '-01');
        $endDate = Carbon::parse($endMonth . '-01');

        // Array nama bulan dalam bahasa Indonesia
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Ambil data dari database dan group by bulan
        $dbData = DB::table('mlite_vedika')
            ->selectRaw("YEAR(tanggal) as tahun, MONTH(tanggal) as bulan, COUNT(*) as jumlah")
            ->where('jenis', $jenis)
            ->where('status', 'Pengajuan')
            ->whereBetween('tanggal', [
                $startDate->startOfMonth()->toDateString(),
                $endDate->endOfMonth()->toDateString()
            ])
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->keyBy(function($item) {
                return $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            });

        $categories = [];
        $seriesColumn = [];
        $seriesLine = [];

        // Loop untuk setiap bulan dalam rentang
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $monthKey = $current->format('Y-m');
            $monthName = $monthNames[$current->month];
            
            // Ambil data dari database, jika tidak ada maka 0
            $jumlah = $dbData->has($monthKey) ? $dbData[$monthKey]->jumlah : 0;
            
            $categories[] = $monthName;
            $seriesColumn[] = (int) $jumlah;
            
            // Series line sebagai pembanding (contoh: target atau rata-rata)
            $seriesLine[] = $jumlah > 0 ? round($jumlah * 1.15) : 0;
            
            $current->addMonth();
        }

        return [
            'categories' => $categories,
            'seriesColumn' => $seriesColumn,
            'seriesLine' => $seriesLine
        ];
    }
}