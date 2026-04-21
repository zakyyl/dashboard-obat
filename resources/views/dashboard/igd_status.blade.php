@extends('layouts.app')
@section('title', 'Status Data RM IGD')
@section('content')

{{-- Notifikasi Sukses --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Filter Box --}}
<div class="card mb-4 mt-5 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Filter Data Pasien IGD</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('statusrm-igd.index') }}" method="GET" autocomplete="off">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="tanggal" class="form-label">Tanggal Registrasi</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}">
                </div>
                <div class="col-md-3">
                    <label for="no_rawat" class="form-label">Nomor Rawat</label>
                    <input type="text" name="no_rawat" id="no_rawat" class="form-control"
                        placeholder="Masukkan No. Rawat" value="{{ $no_rawat ?? '' }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fa-solid fa-search me-1"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Data Table --}}
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Data Pasien | Total: {{ $data_igd->count() }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th style="width: 110px;">Aksi</th>
                        <th>No. Rawat</th>
                        <th>Tanggal Registrasi</th>
                        <th>Nama Dokter</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Poli</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_igd as $item)
                    <tr class="align-middle">
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-warning btn-kelengkapan" data-bs-toggle="modal"
                                data-bs-target="#kelengkapanModal" data-norawat="{{ $item->no_rawat }}"
                                data-namapasien="{{ $item->nm_pasien }}" title="Lihat Kelengkapan Berkas">
                                <i class="fa-solid fa-check-circle me-2"></i>Cek
                            </button>
                        </td>
                        <td class="text-center">{{ $item->no_rawat }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tgl_registrasi)->format('d-m-Y') }}</td>
                        <td>{{ $item->nm_dokter }}</td>
                        <td class="text-center">{{ $item->no_rkm_medis }}</td>
                        <td>{{ $item->nm_pasien }}</td>
                        <td>{{ $item->nm_poli }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Kelengkapan Berkas --}}
<div class="modal fade" id="kelengkapanModal" tabindex="-1" aria-labelledby="kelengkapanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="kelengkapanModalLabel">
                    Kelengkapan Berkas :
                    <span id="namaPasienModal" class="fw-bold"></span>
                    | No. Rawat : <strong id="noRawatModal"></strong>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-bordered mb-0">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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

            fetch(`{{ url('/dashboard/status-rm-igd/kelengkapan') }}/${noRawat}`)
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
    tbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Gagal memuat data. Silakan coba lagi atau hubungi admin.</td></tr>';
});
        });
    });
</script>
@endpush
