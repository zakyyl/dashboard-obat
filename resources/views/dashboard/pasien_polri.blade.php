@extends('layouts.app')

@section('title', 'Data Pasien POLRI')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
        <i class="bi bi-layout-text-window me-2"></i>Laporan Anggota POLRI/PNS & Keluarga Yang Dirawat
        <br>
        <small class="text-muted fs-6">
            {{ \Carbon\Carbon::parse($tanggal_dari)->translatedFormat('d F Y') }} -
            {{ \Carbon\Carbon::parse($tanggal_sampai)->translatedFormat('d F Y') }}
        </small>
    </h2>

    <div class="card mb-3 mb-md-4 shadow-sm bg-dark border-0">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('dashboard.pasienPolri') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm bg-dark text-light border-secondary"
                            placeholder="Cari nama pasien..." value="{{ $search }}">
                    </div>

                    <div class="col-6 col-md-3">
                        <input type="date" name="tanggal_dari" value="{{ $tanggal_dari }}"
                            class="form-control form-control-sm bg-dark text-light border-secondary">
                    </div>

                    <div class="col-6 col-md-3">
                        <input type="date" name="tanggal_sampai" value="{{ $tanggal_sampai }}"
                            class="form-control form-control-sm bg-dark text-light border-secondary">
                    </div>

                    <div class="col-12 col-md-2 d-grid">
                        <button class="btn btn-warning btn-sm">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm bg-dark border-0">
        <div class="card-body p-2 p-md-3">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered align-middle text-light mb-0" style="min-width: 760px;">
                    <thead class="bg-warning text-dark text-center">
                        <tr>
                            <th style="width: 18%">Nama Pasien</th>
                            <th style="width: 10%">NIP</th>
                            <th style="width: 10%">Pangkat</th>
                            <th style="width: 10%">Golongan</th>
                            <th style="width: 14%">Jabatan</th>
                            <th style="width: 14%">Satuan</th>
                            <th style="width: 12%">No. Rawat</th>
                            <th style="width: 12%">Tgl Registrasi</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($pasien as $row)
                        <tr>
                            <td class="text-nowrap fw-semibold">{{ $row->nm_pasien }}</td>
                            <td>{{ $row->nip }}</td>
                            <td>{{ $row->nama_pangkat ?? '-' }}</td>
                            <td>{{ $row->nama_golongan ?? '-' }}</td>
                            <td>{{ $row->nama_jabatan ?? '-' }}</td>
                            <td>{{ $row->nama_satuan ?? '-' }}</td>
                            <td>{{ $row->no_rawat }}</td>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($row->tgl_registrasi)->format('d-m-Y') }}</td>
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