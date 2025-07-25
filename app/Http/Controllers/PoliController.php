<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoliController extends Controller
{

public function index(Request $request)
{
    $tgl_dari = $request->get('tgl_dari', now()->toDateString());
    $tgl_sampai = $request->get('tgl_sampai', now()->toDateString());

    // LEFT JOIN agar semua poli muncul walau tidak ada pasien
    $data = DB::table('poliklinik')
        ->leftJoin('reg_periksa', function ($join) use ($tgl_dari, $tgl_sampai) {
            $join->on('poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_dari, $tgl_sampai]);
        })
        ->select(
            'poliklinik.nm_poli',
            DB::raw('COUNT(reg_periksa.no_rawat) AS jumlah')
        )
        ->groupBy('poliklinik.nm_poli')
        ->orderBy('jumlah', 'desc')
        ->get();

    return view('dashboard.poli', compact('data', 'tgl_dari', 'tgl_sampai'));
}


}
