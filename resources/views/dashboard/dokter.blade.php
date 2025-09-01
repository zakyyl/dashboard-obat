@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Jadwal Dokter</h2>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('dokter.index') }}" class="row mb-4 g-3">
        <div class="col-md-4">
            <label for="kd_dokter" class="form-label">Pilih Dokter</label>
            <select name="kd_dokter" id="kd_dokter" class="form-select">
                <option value="">-- Semua Dokter --</option>
                @foreach($dokterList as $dokter)
                    <option value="{{ $dokter->kd_dokter }}" 
                        {{ request('kd_dokter') == $dokter->kd_dokter ? 'selected' : '' }}>
                        {{ $dokter->nm_dokter }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="kd_poli" class="form-label">Pilih Poli</label>
            <select name="kd_poli" id="kd_poli" class="form-select">
                <option value="">-- Semua Poli --</option>
                @foreach($poliList as $poli)
                    <option value="{{ $poli->kd_poli }}" 
                        {{ request('kd_poli') == $poli->kd_poli ? 'selected' : '' }}>
                        {{ $poli->nm_poli }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-warning w-100">Filter</button>
        </div>
    </form>

    <!-- Tabel Jadwal -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Hari</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Poli</th>
                        <th>Dokter</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal as $row)
                        <tr>
                            <td>{{ $row->hari_kerja }}</td>
                            <td>{{ $row->jam_mulai }}</td>
                            <td>{{ $row->jam_selesai }}</td>
                            <td>{{ $row->nm_poli }}</td>
                            <td>{{ $row->nm_dokter }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada jadwal ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
