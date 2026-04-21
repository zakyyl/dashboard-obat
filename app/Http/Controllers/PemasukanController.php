<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemasukanController extends Controller
{
    
    public function index(Request $request)
    {
        $query = DB::table('pemasukan_viz')
            ->leftJoin('master_kode_pemasukan', 'pemasukan_viz.kode_pemasukan_id', '=', 'master_kode_pemasukan.id')
            ->select('pemasukan_viz.*', 'master_kode_pemasukan.nama_pemasukan');

        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('pemasukan_viz.tanggal', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('pemasukan_viz.tanggal', '<=', $request->end_date);
        }

        if ($request->has('kode_pemasukan_filter') && $request->kode_pemasukan_filter != '') {
            $query->where('pemasukan_viz.kode_pemasukan_id', $request->kode_pemasukan_filter);
        }

        $tableQuery = clone $query;

        if ($request->has('dari') && $request->dari != '') {
            $tableQuery->where('pemasukan_viz.tanggal', '>=', $request->dari);
        }
        if ($request->has('sampai') && $request->sampai != '') {
            $tableQuery->where('pemasukan_viz.tanggal', '<=', $request->sampai);
        }

        $chartData = $query->orderBy('pemasukan_viz.tanggal')->get();
        $totalKeseluruhan = $tableQuery->sum('pemasukan_viz.jumlah');
        $data = $tableQuery->orderByDesc('pemasukan_viz.tanggal')->paginate(50)->withQueryString();

        $tahunList = DB::table('pemasukan_viz')
            ->selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $masterPemasukan = DB::table('master_kode_pemasukan')->orderBy('nama_pemasukan')->get();

        return view('dashboard.pemasukan', compact('data', 'chartData', 'tahunList', 'masterPemasukan', 'totalKeseluruhan'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'kode_pemasukan_id' => 'required|exists:master_kode_pemasukan,id',
            'keterangan' => 'nullable|string',
        ]);

        DB::table('pemasukan_viz')->insert([
            'tanggal' => $request->tanggal,
            'jumlah' => $request->jumlah,
            'kode_pemasukan_id' => $request->kode_pemasukan_id,
            'keterangan' => $request->keterangan,
            'created_at' => now(),
        ]);

        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = DB::table('pemasukan_viz')->where('id', $id)->first();
        $masterPemasukan = DB::table('master_kode_pemasukan')->orderBy('nama_pemasukan')->get();
        return view('dashboard.pemasukan.edit', compact('item', 'masterPemasukan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'kode_pemasukan_id' => 'required|exists:master_kode_pemasukan,id',
            'keterangan' => 'nullable|string',
        ]);

        DB::table('pemasukan_viz')->where('id', $id)->update([
            'tanggal' => $request->tanggal,
            'jumlah' => $request->jumlah,
            'kode_pemasukan_id' => $request->kode_pemasukan_id,
            'keterangan' => $request->keterangan,
            'updated_at' => now(),
        ]);

        return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('pemasukan_viz')->where('id', $id)->delete();
        return redirect()->route('pemasukan.index')->with('success', 'Data berhasil dihapus.');
    }
}
