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
}
