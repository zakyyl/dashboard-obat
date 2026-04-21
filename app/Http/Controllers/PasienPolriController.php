<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasienPolriController extends Controller
{
    public function pasienPolri(Request $request)

    {
        $tanggal_dari = $request->get('tanggal_dari', date('Y-m-d'));
        $tanggal_sampai = $request->get('tanggal_sampai', date('Y-m-d'));
        $search = $request->get('search');

        $query = DB::table('reg_periksa')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pasien_polri', 'pasien_polri.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('pangkat_polri', 'pasien_polri.pangkat_polri', '=', 'pangkat_polri.id')
            ->join('satuan_polri', 'pasien_polri.satuan_polri', '=', 'satuan_polri.id')
            ->join('golongan_polri', 'pasien_polri.golongan_polri', '=', 'golongan_polri.id')
            ->join('jabatan_polri', 'pasien_polri.jabatan_polri', '=', 'jabatan_polri.id')
            ->select(
                'pasien.nm_pasien',
                'pasien.nip',
                'pangkat_polri.nama_pangkat',
                'satuan_polri.nama_satuan',
                'golongan_polri.nama_golongan',
                'jabatan_polri.nama_jabatan',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi'
            )
            ->whereBetween('reg_periksa.tgl_registrasi', [$tanggal_dari, $tanggal_sampai]);

        if ($search) {
            $query->where('pasien.nm_pasien', 'like', "%{$search}%");
        }

        $pasien = $query->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->paginate(25)
            ->appends(['tanggal_dari' => $tanggal_dari, 'tanggal_sampai' => $tanggal_sampai, 'search' => $search]);

        return view('dashboard.pasien_polri', compact('pasien', 'tanggal_dari', 'tanggal_sampai', 'search'));
    }
}
