@extends('layouts.app')

@section('title', 'Status Data RM Rawat Jalan')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Status Data Rawat Jalan</h2>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Filter Box --}}
    {{-- --- PERUBAHAN DIMULAI --- --}}
    <form method="GET" action="{{ route('statusrm-ralan.index') }}" class="row mb-4 g-3" autocomplete="off">
        <div class="col-md-3">
            <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
        </div>
        <div class="col-md-3">
            <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control"
                value="{{ $tanggal_akhir }}">
        </div>
        <div class="col-md-3">
            <label for="no_rawat" class="form-label">Nomor Rawat</label>
            <input type="text" name="no_rawat" id="no_rawat" class="form-control" value="{{ $no_rawat ?? '' }}"
                placeholder="Masukkan No. Rawat">
        </div>
        {{-- 1. Tambah dropdown poliklinik --}}
        <div class="col-md-3">
            <label for="kd_poli" class="form-label">Poliklinik</label>
            <select name="kd_poli" id="kd_poli" class="form-select">
                <option value="">-- Semua Poliklinik --</option>
                @foreach ($poliklinik as $poli)
                <option value="{{ $poli->kd_poli }}" {{ $kd_poli_selected==$poli->kd_poli ? 'selected' : '' }}>
                    {{ $poli->nm_poli }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-warning">
                <i class="fa-solid fa-search me-1"></i> Filter
            </button>
        </div>
    </form>
    {{-- --- PERUBAHAN SELESAI --- --}}


    {{-- Data Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Data Pasien | Total: {{ $data_ralan->count() }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th style="width: 110px;">Aksi</th>
                            <th>No. Rawat</th>
                            <th>Tanggal</th>
                            <th>Dokter</th>
                            <th>No RM</th>
                            <th>Nama Pasien</th>
                            <th>Poliklinik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data_ralan as $item)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning btn-kelengkapan"
                                    data-bs-toggle="modal" data-bs-target="#kelengkapanModal"
                                    data-norawat="{{ $item->no_rawat }}" data-namapasien="{{ $item->nm_pasien }}"
                                    title="Lihat Kelengkapan Berkas">
                                    <i class="fa-solid fa-check-circle me-2"></i> Cek
                                </button>
                            </td>
                            <td>{{ $item->no_rawat }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_registrasi)->format('d-m-Y') }}</td>
                            <td>{{ $item->nm_dokter }}</td>
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->nm_poli }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- --- TAMBAHKAN KODE INI --- --}}
            {{-- Letakkan di bawah div table-responsive atau di bawah card-body --}}
            @if($data_ralan->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $data_ralan->appends(request()->query())->links('vendor.pagination.dark-theme') }}
            </div>
            @endif
            {{-- --- SELESAI --- --}}
        </div>
    </div>

    {{-- Modal Kelengkapan Berkas --}}
    <div class="modal fade" id="kelengkapanModal" tabindex="-1" aria-labelledby="kelengkapanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="kelengkapanModalLabel">
                        Kelengkapan Berkas: <span id="namaPasienModal" class="fw-bold"></span>
                        | No. Rawat: <strong id="noRawatModal"></strong>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Berkas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="isiKelengkapan">
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        Memuat data... <i class="fa fa-spinner fa-spin ms-2"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-kelengkapan').forEach(button => {
        button.addEventListener('click', function() {
            const noRawat = this.dataset.norawat;
            const namaPasien = this.dataset.namapasien;

            document.getElementById('namaPasienModal').innerText = namaPasien;
            document.getElementById('noRawatModal').innerText = noRawat;

            const tbody = document.getElementById('isiKelengkapan');
            tbody.innerHTML = '<tr><td colspan="2" class="text-center py-4">Memuat data... <i class="fa fa-spinner fa-spin ms-2"></i></td></tr>';

            fetch(`{{ url('/dashboard/status-rm-ralan/kelengkapan') }}/${noRawat}`)
                .then(response => {
                    if (!response.ok) {
                        console.error('Network response was not ok. Status:', response.status);
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (Array.isArray(data.data) && data.data.length) {
                        let htmlRows = '';
                        data.data.forEach(item => {
                            let badgeClass = (item.status === 'Ada') ? 'bg-success' : 'bg-danger';
                            htmlRows += `
                                <tr>
                                    <td>${item.nama}</td>
                                    <td><span class="badge ${badgeClass}">${item.status}</span></td>
                                </tr>`;
                        });
                        tbody.innerHTML = htmlRows;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="2" class="text-center">Tidak ada data kelengkapan.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Gagal memuat data.</td></tr>';
                });
        });
    });
</script>
@endpush
