<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObatController extends Controller
{
    public function stokBarang(Request $request)
    {
        $jenisFilter = $request->get('jenis');
        $searchKeyword = $request->get('search');
        $page = $request->get('page', 1);
        $perPage = 20; // Items per page
        
        $listJenis = DB::table('jenis')->select('kdjns', 'nama')->orderBy('nama')->get();
        
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
            ->when($jenisFilter, function ($q) use ($jenisFilter) {
                return $q->where('jenis.nama', $jenisFilter);
            })
            ->when($searchKeyword, function ($q) use ($searchKeyword) {
                return $q->where('databarang.nama_brng', 'like', '%' . $searchKeyword . '%');
            });

        // Get total count for pagination info
        $totalItems = $query->count();
        
        // Apply pagination
        $data = $query->orderBy('databarang.stokminimal', 'desc')
                     ->offset(($page - 1) * $perPage)
                     ->limit($perPage)
                     ->get();

        foreach ($data as $item) {
            $item->total_stok = $stokData[$item->kode_brng] ?? 0;
        }

        // Check if there are more items
        $hasMore = $totalItems > ($page * $perPage);
        $currentCount = ($page - 1) * $perPage + $data->count();

        if ($request->ajax()) {
            return response()->json([
                'data' => $data,
                'hasMore' => $hasMore,
                'currentCount' => $currentCount,
                'totalItems' => $totalItems,
                'nextPage' => $page + 1
            ]);
        }

        return view('dashboard.stok_barang', compact(
            'data', 
            'listJenis', 
            'jenisFilter', 
            'searchKeyword',
            'hasMore',
            'currentCount',
            'totalItems'
        ));
    }

    public function searchObat(Request $request)
    {
        $keyword = $request->get('q');
        $jenisFilter = $request->get('jenis');
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