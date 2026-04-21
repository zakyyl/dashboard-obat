<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        $dokterList = DB::table('dokter')
            ->select('kd_dokter', 'nm_dokter')
            ->get();

        $poliList = DB::table('poliklinik')
            ->select('kd_poli', 'nm_poli')
            ->get();

        $jadwal = DB::table('jadwal as j')
            ->join('dokter as d', 'j.kd_dokter', '=', 'd.kd_dokter')
            ->join('poliklinik as p', 'j.kd_poli', '=', 'p.kd_poli')
            ->select('j.hari_kerja', 'j.jam_mulai', 'j.jam_selesai', 'p.nm_poli', 'd.nm_dokter');

        if ($request->filled('kd_dokter')) {
            $jadwal->where('j.kd_dokter', $request->kd_dokter);
        }

        if ($request->filled('kd_poli')) {
            $jadwal->where('j.kd_poli', $request->kd_poli);
        }

        $jadwal = $jadwal->get();

        return view('dashboard.dokter', compact('dokterList', 'poliList', 'jadwal'));
    }
}
