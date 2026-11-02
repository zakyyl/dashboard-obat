@extends('layouts.app')

@section('title', 'Jadwal Dokter')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
        <i class="bi bi-calendar2-week me-2"></i>Jadwal Praktik Dokter
    </h2>

    <div class="card mb-3 mb-md-4 shadow-sm bg-dark border-0">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('dokter.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label for="kd_dokter" class="form-label text-light small mb-1">Pilih Dokter:</label>
                        <select name="kd_dokter" id="kd_dokter" class="form-select form-select-sm bg-dark text-light border-secondary">
                            <option value="">-- Semua Dokter --</option>
                            @foreach($dokterList as $dokter)
                                <option value="{{ $dokter->kd_dokter }}" 
                                    {{ request('kd_dokter') == $dokter->kd_dokter ? 'selected' : '' }}>
                                    {{ $dokter->nm_dokter }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-5">
                        <label for="kd_poli" class="form-label text-light small mb-1">Pilih Poliklinik:</label>
                        <select name="kd_poli" id="kd_poli" class="form-select form-select-sm bg-dark text-light border-secondary">
                            <option value="">-- Semua Poliklinik --</option>
                            @foreach($poliList as $poli)
                                <option value="{{ $poli->kd_poli }}" 
                                    {{ request('kd_poli') == $poli->kd_poli ? 'selected' : '' }}>
                                    {{ $poli->nm_poli }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-2 d-grid">
                        <button type="submit" class="btn btn-warning btn-sm">
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
                <table class="table table-dark table-striped table-bordered align-middle text-light mb-0" style="min-width: 600px;">
                    <thead class="table-secondary text-dark text-center">
                        <tr>
                            <th style="width: 15%">Hari</th>
                            <th style="width: 15%">Jam Mulai</th>
                            <th style="width: 15%">Jam Selesai</th>
                            <th style="width: 25%">Poliklinik</th>
                            <th style="width: 30%">Nama Dokter</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse($jadwal as $row)
                            <tr>
                                <td class="fw-semibold text-warning">{{ $row->hari_kerja }}</td>
                                <td>{{ $row->jam_mulai }}</td>
                                <td>{{ $row->jam_selesai }}</td>
                                <td>{{ $row->nm_poli }}</td>
                                <td class="fw-medium">{{ $row->nm_dokter }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Tidak ada jadwal ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
