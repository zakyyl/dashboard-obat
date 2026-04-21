<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('pengeluaran_viz')
            ->leftJoin('master_kode_pengeluaran', 'pengeluaran_viz.kode_pengeluaran_id', '=', 'master_kode_pengeluaran.id')
            ->select('pengeluaran_viz.*', 'master_kode_pengeluaran.nama_pengeluaran');

        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('pengeluaran_viz.tanggal', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('pengeluaran_viz.tanggal', '<=', $request->end_date);
        }

        if ($request->has('kode_pengeluaran_filter') && $request->kode_pengeluaran_filter != '') {
            $query->where('pengeluaran_viz.kode_pengeluaran_id', $request->kode_pengeluaran_filter);
        }

        $tableQuery = clone $query;

        if ($request->has('dari') && $request->dari != '') {
            $tableQuery->where('pengeluaran_viz.tanggal', '>=', $request->dari);
        }
        if ($request->has('sampai') && $request->sampai != '') {
            $tableQuery->where('pengeluaran_viz.tanggal', '<=', $request->sampai);
        }

        $chartData = $query->orderBy('pengeluaran_viz.tanggal')->get();
        $totalKeseluruhan = $tableQuery->sum('pengeluaran_viz.jumlah');
        $data = $tableQuery->orderByDesc('pengeluaran_viz.tanggal')->paginate(50)->withQueryString();

        $tahunList = DB::table('pengeluaran_viz')
            ->selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $masterPengeluaran = DB::table('master_kode_pengeluaran')->orderBy('nama_pengeluaran')->get();

        return view('dashboard.pengeluaran', compact('data', 'chartData', 'tahunList', 'masterPengeluaran',  'totalKeseluruhan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'kode_pengeluaran_id' => 'required|exists:master_kode_pengeluaran,id',
            'keterangan' => 'nullable|string',
        ]);

        DB::table('pengeluaran_viz')->insert([
            'tanggal' => $request->tanggal,
            'jumlah' => $request->jumlah,
            'kode_pengeluaran_id' => $request->kode_pengeluaran_id,
            'keterangan' => $request->keterangan,
            'created_at' => now(),
        ]);

        return redirect()->route('pengeluaran.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = DB::table('pengeluaran_viz')->where('id', $id)->first();
        $masterPengeluaran = DB::table('master_kode_pengeluaran')->orderBy('nama_pengeluaran')->get();
        return view('dashboard.pengeluaran.edit', compact('item', 'masterPengeluaran'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'kode_pengeluaran_id' => 'required|exists:master_kode_pengeluaran,id',
            'keterangan' => 'nullable|string',
        ]);

        DB::table('pengeluaran_viz')->where('id', $id)->update([
            'tanggal' => $request->tanggal,
            'jumlah' => $request->jumlah,
            'kode_pengeluaran_id' => $request->kode_pengeluaran_id,
            'keterangan' => $request->keterangan,
            'updated_at' => now(),
        ]);

        return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('pengeluaran_viz')->where('id', $id)->delete();
        return redirect()->route('pengeluaran.index')->with('success', 'Data berhasil dihapus.');
    }
}
