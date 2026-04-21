@extends('layouts.app')

@section('title', 'Data Pasien POLRI')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-center text-warning">
        <i class="bi bi-layout-text-window me-2"></i>Laporan Anggota POLRI/PNS & Keluarga Yang Dirawat
        <br>
        <small class="text-muted">
            {{ \Carbon\Carbon::parse($tanggal_dari)->translatedFormat('d F Y') }} -
            {{ \Carbon\Carbon::parse($tanggal_sampai)->translatedFormat('d F Y') }}
        </small>
    </h2>

    <div class="card mb-4 shadow-sm bg-dark border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.pasienPolri') }}">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <input type="text" name="search" class="form-control form-control-sm bg-dark text-light border-secondary"
                        placeholder="Cari nama pasien..." value="{{ $search }}" style="width: 200px;">

                    <div class="d-flex gap-2">
                        <input type="date" name="tanggal_dari" value="{{ $tanggal_dari }}"
                            class="form-control form-control-sm bg-dark text-light border-secondary">

                        <input type="date" name="tanggal_sampai" value="{{ $tanggal_sampai }}"
                            class="form-control form-control-sm bg-dark text-light border-secondary">

                        <button class="btn btn-warning btn-sm">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm bg-dark border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered align-middle text-light">
                    <thead class="bg-warning text-dark text-center">
                        <tr>
                            <th style="width: 15%">Nama Pasien</th>
                            <th style="width: 8%">NIP</th>
                            <th style="width: 7%">Pangkat</th>
                            <th style="width: 10%">Golongan</th>
                            <th style="width: 12%">Jabatan</th>
                            <th style="width: 12%">Satuan</th>
                            <th style="width: 12%">No. Rawat</th>
                            <th style="width: 10%">Tgl Registrasi</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.9rem;">
                        @forelse($pasien as $row)
                        <tr>
                            <td>{{ $row->nm_pasien }}</td>
                            <td>{{ $row->nip }}</td>
                            <td>{{ $row->nama_pangkat ?? '-' }}</td>
                            <td>{{ $row->nama_golongan ?? '-' }}</td>
                            <td>{{ $row->nama_jabatan ?? '-' }}</td>
                            <td>{{ $row->nama_satuan ?? '-' }}</td>
                            <td>{{ $row->no_rawat }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tgl_registrasi)->format('d-m-Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Tidak ada data ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($pasien->hasPages())
            <div class="d-flex justify-content-center mt-3">
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page --}}
                        @if ($pasien->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link bg-secondary border-0 text-light">
                                    <i class="bi bi-chevron-left me-1"></i>Previous
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link bg-warning border-0 text-dark" href="{{ $pasien->previousPageUrl() }}">
                                    <i class="bi bi-chevron-left me-1"></i>Previous
                                </a>
                            </li>
                        @endif

                        {{-- Next Page --}}
                        @if ($pasien->hasMorePages())
                            <li class="page-item ms-2">
                                <a class="page-link bg-warning border-0 text-dark" href="{{ $pasien->nextPageUrl() }}">
                                    Next<i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item ms-2 disabled">
                                <span class="page-link bg-secondary border-0 text-light">
                                    Next<i class="bi bi-chevron-right ms-1"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection