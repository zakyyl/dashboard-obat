@extends('layouts.app')

@section('title', 'Status Data RM Rawat Jalan')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
        <i class="bi bi-file-earmark-check me-2"></i>Status Data Rekam Medis Rawat Jalan
    </h2>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-3 mb-md-4 shadow-sm bg-dark border-0">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('statusrm-ralan.index') }}" autocomplete="off">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="tanggal_awal" class="form-label text-light small mb-1">Tanggal Awal:</label>
                        <input type="date" name="tanggal_awal" id="tanggal_awal"
                            class="form-control form-control-sm bg-dark text-light border-secondary" value="{{ $tanggal_awal }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="tanggal_akhir" class="form-label text-light small mb-1">Tanggal Akhir:</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                            class="form-control form-control-sm bg-dark text-light border-secondary" value="{{ $tanggal_akhir }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="no_rawat" class="form-label text-light small mb-1">Nomor Rawat:</label>
                        <input type="text" name="no_rawat" id="no_rawat"
                            class="form-control form-control-sm bg-dark text-light border-secondary"
                            value="{{ $no_rawat ?? '' }}" placeholder="No. Rawat">
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="kd_poli" class="form-label text-light small mb-1">Poliklinik:</label>
                        <select name="kd_poli" id="kd_poli" class="form-select form-select-sm bg-dark text-light border-secondary">
                            <option value="">-- Semua Poli --</option>
                            @foreach ($poliklinik as $poli)
                            <option value="{{ $poli->kd_poli }}" {{ $kd_poli_selected == $poli->kd_poli ? 'selected' : '' }}>
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-semibold fs-6 fs-md-5 text-warning">
                    Data Pasien <span class="badge bg-secondary text-light ms-1">{{ $data_ralan->total() ?? $data_ralan->count() }} Pasien</span>
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-striped table-bordered align-middle text-light mb-0" style="min-width: 780px;">
                    <thead class="table-secondary text-dark text-center">
                        <tr>
                            <th style="width: 80px;">Aksi</th>
                            <th style="width: 15%;">No. Rawat</th>
                            <th style="width: 12%;">Tanggal</th>
                            <th style="width: 20%;">Dokter</th>
                            <th style="width: 12%;">No. RM</th>
                            <th style="width: 20%;">Nama Pasien</th>
                            <th style="width: 15%;">Poliklinik</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse ($data_ralan as $item)
                        <tr>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-warning btn-kelengkapan py-0 px-2"
                                    data-bs-toggle="modal" data-bs-target="#kelengkapanModal"
                                    data-norawat="{{ $item->no_rawat }}" data-namapasien="{{ $item->nm_pasien }}"
                                    title="Lihat Kelengkapan Berkas">
                                    <i class="bi bi-check-circle me-1"></i>Cek
                                </button>
                            </td>
                            <td class="font-monospace text-nowrap">{{ $item->no_rawat }}</td>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tgl_registrasi)->format('d-m-Y') }}</td>
                            <td>{{ $item->nm_dokter }}</td>
                            <td class="font-monospace">{{ $item->no_rkm_medis }}</td>
                            <td class="fw-semibold">{{ $item->nm_pasien }}</td>
                            <td><span class="badge bg-secondary">{{ $item->nm_poli }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Data tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data_ralan->hasPages())
            <div class="d-flex justify-content-center justify-content-md-end mt-3">
                {{ $data_ralan->appends(request()->query())->links('vendor.pagination.dark-theme') }}
            </div>
            @endif
        </div>
    </div>

    {{-- Modal Kelengkapan Berkas --}}
    <div class="modal fade" id="kelengkapanModal" tabindex="-1" aria-labelledby="kelengkapanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content bg-dark text-light border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fs-6" id="kelengkapanModalLabel">
                        Kelengkapan Berkas: <span id="namaPasienModal" class="fw-bold text-warning"></span>
                        | <span class="font-monospace text-muted" id="noRawatModal"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-2 p-md-3">
                    <div class="table-responsive">
                        <table class="table table-dark table-bordered table-striped mb-0">
                            <thead class="table-secondary text-dark">
                                <tr>
                                    <th>Nama Berkas</th>
                                    <th style="width: 120px;" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="isiKelengkapan" style="font-size: 0.875rem;">
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        Memuat data... <i class="bi bi-arrow-repeat spin ms-2"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
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
            tbody.innerHTML = '<tr><td colspan="2" class="text-center py-4 text-warning">Memuat data...</td></tr>';

            fetch(`{{ url('/dashboard/status-rm-ralan/kelengkapan') }}/${noRawat}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response error');
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
                                    <td class="text-center"><span class="badge ${badgeClass}">${item.status}</span></td>
                                </tr>`;
                        });
                        tbody.innerHTML = htmlRows;
                    } else {
                        tbody.innerHTML = '<tr><td colspan="2" class="text-center text-muted">Tidak ada data kelengkapan.</td></tr>';
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
