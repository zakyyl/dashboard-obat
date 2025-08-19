<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ObatController extends Controller
{
    public function stokBarang(Request $request)
    {
        $jenisFilter = $request->get('jenis');
        $searchKeyword = $request->get('search');
        $page = $request->get('page', 1);
        $perPage = 20;

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

        $totalItems = $query->count();

        $data = $query->orderBy('databarang.stokminimal', 'desc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        foreach ($data as $item) {
            $item->total_stok = $stokData[$item->kode_brng] ?? 0;
        }

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

    public function stokBarangPerDepo(Request $request)
    {
        $kdBangsal = $request->get('kd_bangsal');
        $jenisFilter = $request->get('jenis');
        $searchKeyword = $request->get('search');
        $page = $request->get('page', 1);
        $perPage = 20;


        $listDepo = DB::table('bangsal')
            ->select('kd_bangsal', 'nm_bangsal')
            ->where('status', '1')
            ->whereIn('kd_bangsal', ['DO001', 'AP001', 'DO003', 'GO001'])
            ->orderBy('nm_bangsal')
            ->get();


        $listJenis = DB::table('jenis')->select('kdjns', 'nama')->orderBy('nama')->get();

        $stokData = DB::table('gudangbarang')
            ->join('bangsal', 'gudangbarang.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->select('gudangbarang.kode_brng', DB::raw('SUM(gudangbarang.stok) as total_stok'))
            ->where('gudangbarang.no_batch', '')
            ->where('gudangbarang.no_faktur', '')
            ->when($kdBangsal, function ($q) use ($kdBangsal) {
                return $q->where('gudangbarang.kd_bangsal', $kdBangsal);
            })
            ->groupBy('gudangbarang.kode_brng')
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

        $totalItems = $query->count();
        $data = $query->orderBy('databarang.stokminimal', 'desc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        foreach ($data as $item) {
            $item->total_stok = $stokData[$item->kode_brng] ?? 0;
        }

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

        return view('dashboard.stok_barang_per_depo', compact(
            'data',
            'listDepo',
            'listJenis',
            'kdBangsal',
            'jenisFilter',
            'searchKeyword',
            'hasMore',
            'currentCount',
            'totalItems'
        ));
    }
    // public function obatMasuk(Request $request)
    // {
    //     $kodeBrng = $request->get('kode_brng'); // ambil kode barang dari parameter request
    //     $tanggal = $request->get('tanggal', now()->toDateString()); // default hari ini

    //     $data = DB::table('pemesanan')
    //         ->join('datasuplier', 'pemesanan.kode_suplier', '=', 'datasuplier.kode_suplier')
    //         ->join('petugas', 'pemesanan.nip', '=', 'petugas.nip')
    //         ->join('bangsal', 'pemesanan.kd_bangsal', '=', 'bangsal.kd_bangsal')
    //         ->join('detailpesan', 'pemesanan.no_faktur', '=', 'detailpesan.no_faktur')
    //         ->join('databarang', 'detailpesan.kode_brng', '=', 'databarang.kode_brng')
    //         ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
    //         ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
    //         ->join('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
    //         ->select(
    //             'detailpesan.kode_brng',
    //             'databarang.nama_brng',
    //             'databarang.kode_sat',
    //             'kodesatuan.satuan',
    //             'jenis.nama AS namajenis',
    //             DB::raw('SUM(detailpesan.jumlah2) AS jumlah'),
    //             DB::raw('SUM(detailpesan.total) AS total')
    //         )
    //         ->whereDate('pemesanan.tgl_pesan', $tanggal)
    //         ->when($kodeBrng, function ($q) use ($kodeBrng) {
    //             return $q->where('detailpesan.kode_brng', $kodeBrng);
    //         })
    //         ->groupBy(
    //             'detailpesan.kode_brng',
    //             'databarang.nama_brng',
    //             'databarang.kode_sat',
    //             'kodesatuan.satuan',
    //             'jenis.nama'
    //         )
    //                 ->get();

    //     return view('dashboard.stok_barang_masuk', compact('data', 'tanggal', 'kodeBrng'));
    // }


public function stokBarangMasuk(Request $request)
{
    // Default dari awal bulan sampai akhir bulan
    $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
    $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

    $kodeBrng = $request->get('kode_brng');
    $page = $request->get('page', 1);
    $perPage = 20;

    $query = DB::table('pemesanan')
        ->join('datasuplier', 'pemesanan.kode_suplier', '=', 'datasuplier.kode_suplier')
        ->join('petugas', 'pemesanan.nip', '=', 'petugas.nip')
        ->join('bangsal', 'pemesanan.kd_bangsal', '=', 'bangsal.kd_bangsal')
        ->join('detailpesan', 'pemesanan.no_faktur', '=', 'detailpesan.no_faktur')
        ->join('databarang', 'detailpesan.kode_brng', '=', 'databarang.kode_brng')
        ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
        ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
        ->join('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
        ->select(
            'detailpesan.kode_brng',
            'databarang.nama_brng',
            'kodesatuan.satuan',
            'jenis.nama AS namajenis',
            DB::raw('SUM(detailpesan.jumlah2) AS jumlah'),
            DB::raw('SUM(detailpesan.total) AS total')
        )
        ->whereBetween('pemesanan.tgl_pesan', [$startDate, $endDate])
        ->when($kodeBrng, function ($q) use ($kodeBrng) {
            $q->where(function ($sub) use ($kodeBrng) {
                $sub->where('detailpesan.kode_brng', 'like', "%$kodeBrng%")
                    ->orWhere('databarang.nama_brng', 'like', "%$kodeBrng%");
            });
        })
        ->groupBy(
            'detailpesan.kode_brng',
            'databarang.nama_brng',
            'kodesatuan.satuan',
            'jenis.nama'
        );

    $totalItems = $query->count();
    $data = $query->orderBy('jumlah', 'desc')
        ->offset(($page - 1) * $perPage)
        ->limit($perPage)
        ->get();

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

    return view('dashboard.stok_barang_masuk', compact(
        'data',
        'startDate',
        'endDate',
        'kodeBrng',
        'hasMore',
        'currentCount',
        'totalItems'
    ));
}


}
