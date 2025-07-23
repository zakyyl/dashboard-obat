<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObatSaatIniController extends Controller
{
    public function index(Request $request)
    {
        $jenisFilter = $request->get('jenis');

        $query = DB::table('gudangbarang')
            ->join('bangsal', 'gudangbarang.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('databarang', 'gudangbarang.kode_brng', '=', 'databarang.kode_brng')
            ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
            ->select(
                'gudangbarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'jenis.nama as jenis',
                DB::raw('SUM(gudangbarang.stok) as total_stok')
            )
            ->where('bangsal.status', '1')
            ->where('gudangbarang.no_batch', '')
            ->where('gudangbarang.no_faktur', '');

        if ($jenisFilter) {
            $query->where('jenis.nama', $jenisFilter);
        }

        $data = $query
            ->groupBy(
                'gudangbarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'jenis.nama'
            )
            ->orderByDesc('total_stok')
            ->limit(20)
            ->get();

        $listJenis = DB::table('jenis')->select('nama')->orderBy('nama')->get();

        return view('dashboard.obat_saat_ini', compact('data', 'listJenis', 'jenisFilter'));
    }



    public function searchObatSaatIni(Request $request)
    {
        $keyword = $request->get('q');

        $data = DB::table('gudangbarang')
            ->join('bangsal', 'gudangbarang.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('databarang', 'gudangbarang.kode_brng', '=', 'databarang.kode_brng')
            ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
            ->select(
                'gudangbarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'jenis.nama as jenis',
                DB::raw('SUM(gudangbarang.stok) as total_stok')
            )
            ->where('bangsal.status', '1')
            ->where('gudangbarang.no_batch', '')
            ->where('gudangbarang.no_faktur', '')
            ->when($keyword, function ($query, $keyword) {
                return $query->where('databarang.nama_brng', 'like', "%{$keyword}%");
            })
            ->groupBy(
                'gudangbarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_sat',
                'jenis.nama'
            )
            ->orderByDesc('total_stok')
            ->limit(20)
            ->get();

        return response()->json($data);
    }
}
