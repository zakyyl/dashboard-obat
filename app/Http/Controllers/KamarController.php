<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KamarController extends Controller
{
    public function index()
    {
        $nsList = [
            'NS Siginjai',
            'NS Bulian',
            'NS Jasmine',
            'NS Kebidanan',
            'NS ICU',
            'NS Perinatologi',
        ];

        $data = [];

        foreach ($nsList as $ns) {
            $jumlahBed = DB::table('kamar')
                ->where('statusdata', '1')
                ->where('ns', $ns)
                ->count();

            $bedTerisi = DB::table('kamar')
                ->where('statusdata', '1')
                ->where('ns', $ns)
                ->where('status', 'ISI')
                ->count();

            $bedKosong = DB::table('kamar')
                ->where('statusdata', '1')
                ->where('ns', $ns)
                ->where('status', 'KOSONG')
                ->count();

            $detail = DB::table('bangsal')
                ->join('kamar', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
                ->select(
                    'bangsal.kd_bangsal',
                    'bangsal.nm_bangsal',
                    'kamar.kelas',
                    DB::raw('COUNT(kamar.kd_kamar) as jumlah'),
                    DB::raw("SUM(CASE WHEN kamar.status = 'ISI' THEN 1 ELSE 0 END) as terisi"),
                    DB::raw("SUM(CASE WHEN kamar.status = 'KOSONG' THEN 1 ELSE 0 END) as kosong")
                )
                ->where('kamar.statusdata', '1')
                ->where('kamar.ns', $ns)
                ->groupBy('bangsal.kd_bangsal', 'bangsal.nm_bangsal', 'kamar.kelas')
                ->orderBy('bangsal.nm_bangsal')
                ->get();

            $data[] = [
                'nurse_station' => $ns,
                'jumlah_bed'    => $jumlahBed,
                'bed_terisi'    => $bedTerisi,
                'bed_kosong'    => $bedKosong,
                'detail'        => $detail,
            ];
        }

        return view('dashboard.kamar', compact('data'));
    }
}
