<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KeuanganController extends Controller
{
    public function rekap(Request $request)
    {
        // Ambil parameter filter
        $periode = $request->get('periode', 'month'); // default: month
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Query dengan filter tanggal
        $queryPemasukan = DB::table('pemasukan_viz');
        $queryPengeluaran = DB::table('pengeluaran_viz');

        // Handle filter berdasarkan periode
        if ($periode == 'year') {
            // Filter untuk Per Tahun (input berupa angka tahun: 2024)
            if ($startDate) {
                $queryPemasukan->whereYear('tanggal', '>=', $startDate);
                $queryPengeluaran->whereYear('tanggal', '>=', $startDate);
            }
            if ($endDate) {
                $queryPemasukan->whereYear('tanggal', '<=', $endDate);
                $queryPengeluaran->whereYear('tanggal', '<=', $endDate);
            }
        } elseif ($periode == 'month') {
            // Filter untuk Per Bulan (input type month: 2024-01)
            if ($startDate) {
                // Ambil tanggal awal bulan
                $startDateFormatted = $startDate . '-01';
                $queryPemasukan->where('tanggal', '>=', $startDateFormatted);
                $queryPengeluaran->where('tanggal', '>=', $startDateFormatted);
            }
            if ($endDate) {
                // Ambil tanggal akhir bulan
                $endDateFormatted = date('Y-m-t', strtotime($endDate . '-01'));
                $queryPemasukan->where('tanggal', '<=', $endDateFormatted);
                $queryPengeluaran->where('tanggal', '<=', $endDateFormatted);
            }
        } else {
            // Filter untuk Per Hari (input type date: 2024-01-15)
            if ($startDate) {
                $queryPemasukan->where('tanggal', '>=', $startDate);
                $queryPengeluaran->where('tanggal', '>=', $startDate);
            }
            if ($endDate) {
                $queryPemasukan->where('tanggal', '<=', $endDate);
                $queryPengeluaran->where('tanggal', '<=', $endDate);
            }
        }

        // 💰 Total keseluruhan (dengan filter)
        $totalPemasukan = (clone $queryPemasukan)->sum('jumlah');
        $totalPengeluaran = (clone $queryPengeluaran)->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Get data untuk rekap
        $dataPemasukan = (clone $queryPemasukan)->get();
        $dataPengeluaran = (clone $queryPengeluaran)->get();

        // 📅 Rekap berdasarkan periode menggunakan Collection
        $rekapBulanan = $this->processRekapByPeriode($periode, $dataPemasukan, $dataPengeluaran);

        // 🔹 5 data terbaru (tidak terpengaruh filter)
        $pemasukan = DB::table('pemasukan_viz')->latest('tanggal')->take(5)->get();
        $pengeluaran = DB::table('pengeluaran_viz')->latest('tanggal')->take(5)->get();

        // 🧾 Kirim ke view
        return view('dashboard.rekap', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'pemasukan',
            'pengeluaran',
            'rekapBulanan',
            'periode'
        ));
    }

    private function processRekapByPeriode($periode, $dataPemasukan, $dataPengeluaran)
    {
        $result = collect();

        if ($periode == 'year') {
            // Rekap Per Tahun
            $groupedPemasukan = $dataPemasukan->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y');
            })->map(function($items) {
                return $items->sum('jumlah');
            });

            $groupedPengeluaran = $dataPengeluaran->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y');
            })->map(function($items) {
                return $items->sum('jumlah');
            });

            // Gabungkan semua tahun unik
            $allYears = $groupedPemasukan->keys()->merge($groupedPengeluaran->keys())->unique()->sort();

            foreach ($allYears as $year) {
                $pemasukan = $groupedPemasukan->get($year, 0);
                $pengeluaran = $groupedPengeluaran->get($year, 0);
                
                $result->push((object)[
                    'nama_periode' => $year,
                    'tahun' => $year,
                    'periode_sort' => $year,
                    'total_pemasukan' => $pemasukan,
                    'total_pengeluaran' => $pengeluaran,
                    'saldo' => $pemasukan - $pengeluaran
                ]);
            }

        } elseif ($periode == 'day') {
            // Rekap Per Hari
            $groupedPemasukan = $dataPemasukan->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            })->map(function($items) {
                return $items->sum('jumlah');
            });

            $groupedPengeluaran = $dataPengeluaran->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            })->map(function($items) {
                return $items->sum('jumlah');
            });

            // Gabungkan semua tanggal unik
            $allDays = $groupedPemasukan->keys()->merge($groupedPengeluaran->keys())->unique()->sort();

            foreach ($allDays as $day) {
                $date = Carbon::parse($day);
                $pemasukan = $groupedPemasukan->get($day, 0);
                $pengeluaran = $groupedPengeluaran->get($day, 0);
                
                $result->push((object)[
                    'nama_periode' => $date->translatedFormat('d F Y'),
                    'tanggal' => $day,
                    'periode_sort' => $day,
                    'total_pemasukan' => $pemasukan,
                    'total_pengeluaran' => $pengeluaran,
                    'saldo' => $pemasukan - $pengeluaran
                ]);
            }

        } else {
            // Rekap Per Bulan (default)
            $groupedPemasukan = $dataPemasukan->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m');
            })->map(function($items) {
                return $items->sum('jumlah');
            });

            $groupedPengeluaran = $dataPengeluaran->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m');
            })->map(function($items) {
                return $items->sum('jumlah');
            });

            // Gabungkan semua bulan unik
            $allMonths = $groupedPemasukan->keys()->merge($groupedPengeluaran->keys())->unique()->sort();

            foreach ($allMonths as $month) {
                $date = Carbon::parse($month . '-01');
                $pemasukan = $groupedPemasukan->get($month, 0);
                $pengeluaran = $groupedPengeluaran->get($month, 0);
                
                $result->push((object)[
                    'nama_periode' => $date->translatedFormat('F Y'),
                    'tahun' => $date->year,
                    'bulan_angka' => $date->month,
                    'periode_sort' => $month,
                    'total_pemasukan' => $pemasukan,
                    'total_pengeluaran' => $pengeluaran,
                    'saldo' => $pemasukan - $pengeluaran
                ]);
            }
        }

        return $result;
    }
}