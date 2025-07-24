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

        // Clone the query to get total_stok for each item
        $data = $query->get(); // Get initial data to iterate over for total_stok

        // Now, for each item, get its current total_stok
        foreach ($data as $item) {
            $totalStok = DB::table('gudangbarang')
                ->where('kode_brng', $item->kode_brng)
                ->where('no_batch', '') // Filter like in ObatSaatIniController
                ->where('no_faktur', '') // Filter like in ObatSaatIniController
                ->sum('stok');
            $item->total_stok = $totalStok; // Add total_stok to the item object
        }

        // Apply ordering and limit *after* total_stok is calculated if needed,
        // or re-think if you need to order by total_stok or stokminimal for the 20 limit.
        // For simplicity, let's re-order and limit by 'stokminimal' as before,
        // but now each item also has 'total_stok'.
        $data = $data->sortByDesc('stokminimal')->take(20);


        return view('dashboard.stok_barang', compact('data', 'listJenis', 'jenisFilter'));
    }


    public function searchObat(Request $request)
    {
        $keyword = $request->get('q');
        $jenisFilter = $request->get('jenis'); // Make sure to get jenis filter here too

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

        // Add total_stok to each item found by search
        foreach ($data as $item) {
            $totalStok = DB::table('gudangbarang')
                ->where('kode_brng', $item->kode_brng)
                ->where('no_batch', '')
                ->where('no_faktur', '')
                ->sum('stok');
            $item->total_stok = $totalStok;
        }

        return response()->json($data);
    }
}
