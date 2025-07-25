<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RawatInapController extends Controller
{

public function index(Request $request)
{
    $tgl_dari = $request->get('tgl_dari', Carbon::now()->startOfMonth()->toDateString());
    $tgl_sampai = $request->get('tgl_sampai', Carbon::now()->endOfMonth()->toDateString());

    $data = DB::table('reg_periksa')
        ->join('periksa_lab', 'reg_periksa.no_rawat', '=', 'periksa_lab.no_rawat')
        ->select(
            DB::raw("DATE_FORMAT(reg_periksa.tgl_registrasi, '%Y-%m-%d') as tgl"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rawat) as jumlah")
        )
        ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_dari, $tgl_sampai])
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->groupBy(DB::raw("DATE_FORMAT(reg_periksa.tgl_registrasi, '%Y-%m-%d')"))
        ->orderBy('tgl')
        ->get();

    return view('dashboard.lab_kunjungan_Ranap', compact('data', 'tgl_dari', 'tgl_sampai'));
}

public function indexRadiologi(Request $request)
{
    $tgl_dari = $request->get('tgl_dari', Carbon::now()->startOfMonth()->toDateString());
    $tgl_sampai = $request->get('tgl_sampai', Carbon::now()->endOfMonth()->toDateString());

    $data = DB::table('reg_periksa')
        ->join('periksa_radiologi', 'reg_periksa.no_rawat', '=', 'periksa_radiologi.no_rawat')
        ->select(
            DB::raw("DATE_FORMAT(reg_periksa.tgl_registrasi, '%Y-%m-%d') as tgl"),
            DB::raw("COUNT(DISTINCT reg_periksa.no_rawat) as jumlah")
        )
        ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_dari, $tgl_sampai])
        ->where('reg_periksa.status_lanjut', 'Ranap')
        ->groupBy(DB::raw("DATE_FORMAT(reg_periksa.tgl_registrasi, '%Y-%m-%d')"))
        ->orderBy('tgl')
        ->get();

    return view('dashboard.radiologi_kunjungan_Ranap', compact('data', 'tgl_dari', 'tgl_sampai'));
}


}
