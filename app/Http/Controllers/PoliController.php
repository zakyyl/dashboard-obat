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
        $kd_pj = $request->get('kd_pj');

        // LEFT JOIN agar semua poli muncul walau tidak ada pasien
        $data = DB::table('poliklinik')
            ->leftJoin('reg_periksa', function ($join) use ($tgl_dari, $tgl_sampai, $kd_pj) {
                $join->on('poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                    ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_dari, $tgl_sampai]);
                if ($kd_pj) {
                    $join->where('reg_periksa.kd_pj', $kd_pj);
                }
            })
            ->select(
                'poliklinik.nm_poli',
                DB::raw('COUNT(reg_periksa.no_rawat) AS jumlah')
            )
            ->groupBy('poliklinik.nm_poli')
            ->orderBy('jumlah', 'desc')
            ->get();

        $penjabList = DB::table('penjab')
            ->select('kd_pj', 'png_jawab')
            ->where('status', '1')
            ->orderBy('png_jawab', 'asc')
            ->get();

        return view('dashboard.poli', compact('data', 'tgl_dari', 'tgl_sampai', 'kd_pj', 'penjabList'));
    }

    public function indexPerDokter(Request $request)
    {
        $tgl_dari = $request->get('tgl_dari', now()->toDateString());
        $tgl_sampai = $request->get('tgl_sampai', now()->toDateString());
        $kd_pj = $request->get('kd_pj');

        $query = DB::table('reg_periksa')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->whereBetween('reg_periksa.tgl_registrasi', [$tgl_dari, $tgl_sampai]);

        if ($kd_pj) {
            $query->where('reg_periksa.kd_pj', $kd_pj);
        }

        $data = $query->select('dokter.nm_dokter', DB::raw('COUNT(reg_periksa.no_rawat) AS jumlah'))
            ->groupBy('dokter.nm_dokter')
            ->orderBy('jumlah', 'desc')
            ->get();

        $penjabList = DB::table('penjab')
            ->select('kd_pj', 'png_jawab')
            ->where('status', '1')
            ->orderBy('png_jawab', 'asc')
            ->get();

        return view('dashboard.poli_perdokter', compact('data', 'tgl_dari', 'tgl_sampai', 'kd_pj', 'penjabList'));
    }
}
