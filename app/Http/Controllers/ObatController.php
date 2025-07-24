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

    // Ambil semua stok dari gudang dalam satu query, filter batch & faktur kosong
    $stokData = DB::table('gudangbarang')
        ->select('kode_brng', DB::raw('SUM(stok) as total_stok'))
        ->where('no_batch', '')
        ->where('no_faktur', '')
        ->groupBy('kode_brng')
        ->pluck('total_stok', 'kode_brng'); // hasilnya: [kode_brng => total_stok]

    $query = DB::table('databarang')
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
        });

    $data = $query->get();

    // Gabungkan hasil stok ke masing-masing item
    foreach ($data as $item) {
        $item->total_stok = $stokData[$item->kode_brng] ?? 0;
    }

    // Sort & ambil top 20
    $data = $data->sortByDesc('stokminimal')->take(20);

    return view('dashboard.stok_barang', compact('data', 'listJenis', 'jenisFilter'));
}



public function searchObat(Request $request)
{
    $keyword = $request->get('q');
    $jenisFilter = $request->get('jenis');

    // Ambil stok total hanya sekali
    $stokData = DB::table('gudangbarang')
        ->select('kode_brng', DB::raw('SUM(stok) as total_stok'))
        ->where('no_batch', '')
        ->where('no_faktur', '')
        ->groupBy('kode_brng')
        ->pluck('total_stok', 'kode_brng');

    $query = DB::table('databarang')
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
        ->where('databarang.nama_brng', 'like', "%$keyword%")
        ->orderBy('databarang.stokminimal', 'desc');

    $data = $query->get();

    foreach ($data as $item) {
        $item->total_stok = $stokData[$item->kode_brng] ?? 0;
    }

    return response()->json($data);
}

}