<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar
        $query = DB::table('pemasukan_viz');
        
        // Filter berdasarkan range tanggal untuk chart
        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('tanggal', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('tanggal', '<=', $request->end_date);
        }
        
        // Clone query untuk tabel dengan filter terpisah
        $tableQuery = clone $query;
        
        // Filter dari/sampai untuk tabel
        if ($request->has('dari') && $request->dari != '') {
            $tableQuery->where('tanggal', '>=', $request->dari);
        }
        
        if ($request->has('sampai') && $request->sampai != '') {
            $tableQuery->where('tanggal', '<=', $request->sampai);
        }
        
        // Get data untuk chart (gunakan query dengan filter start_date/end_date)
        $chartData = $query->orderBy('tanggal')->get();
        
        // Get data untuk tabel (gunakan query dengan filter dari/sampai)
        $data = $tableQuery->orderByDesc('tanggal')->get();
        
        // List tahun untuk dropdown (dari data yang ada)
        $tahunList = DB::table('pemasukan_viz')
            ->selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
        
        return view('dashboard.pemasukan', compact('data', 'chartData', 'tahunList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        DB::table('pemasukan_viz')->insert([
            'tanggal' => $request->tanggal,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'created_at' => now(),
        ]);

        return redirect()->route('pemasukan.index')->with('success', 'Pemasukan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = DB::table('pemasukan_viz')->where('id', $id)->first();
        return view('dashboard.pemasukan.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        DB::table('pemasukan_viz')->where('id', $id)->update([
            'tanggal' => $request->tanggal,
            'jumlah' => $request->jumlah,
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