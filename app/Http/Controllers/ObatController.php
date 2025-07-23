<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObatController extends Controller
{
    public function stokBarang(Request $request)
    {
        $jenisFilter = $request->get('jenis');
        $listJenis = DB::table('jenis')->select('kdjns', 'nama')->orderBy('nama')->get();
        $data = DB::table('databarang')
            ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
            ->select(
                'databarang.kode_brng',
                'databarang.nama_brng',
                'kodesatuan.satuan',
                'databarang.stokminimal',
                'jenis.nama as jenis'
            )
            ->where('databarang.status', '1')
            ->when($jenisFilter, function ($query, $jenisFilter) {
                return $query->where('jenis.nama', $jenisFilter);
            })
            ->orderBy('databarang.stokminimal', 'desc')
            ->limit(20)
            ->get();

        return view('dashboard.stok_barang', compact('data', 'listJenis', 'jenisFilter'));
    }


    public function searchObat(Request $request)
{
    $keyword = $request->get('q');

    $data = DB::table('databarang')
        ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
        ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
        ->select(
            'databarang.kode_brng',
            'databarang.nama_brng',
            'kodesatuan.satuan',
            'databarang.stokminimal',
            'jenis.nama as jenis'
        )
        ->where('databarang.status', '1')
        ->where('databarang.nama_brng', 'like', "%$keyword%")
        ->orderBy('databarang.stokminimal', 'desc')
        // ->limit(10)
        ->get();

    return response()->json($data);
}

}
