@extends('layouts.app')

@section('title', 'Status Data RM Rawat Inap')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">Status Data Rawat Inap</h2>

    {{-- Filter Box --}}
    <form method="GET" action="{{ route('statusrm-ranap.index') }}" class="row mb-4 g-3" autocomplete="off">
        <div class="col-md-4">
            <label for="tanggal_awal" class="form-label">Tanggal Awal Keluar</label>
            <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" value="{{ $tanggal_awal }}">
        </div>
        <div class="col-md-4">
            <label for="tanggal_akhir" class="form-label">Tanggal Akhir Keluar</label>
            <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control"
                value="{{ $tanggal_akhir }}">
        </div>
        <div class="col-md-4">
    <label for="ns_group" class="form-label">Nurse Station</label>
    {{-- Ubah 'name' menjadi 'ns_group' --}}
    <select name="ns_group" id="ns_group" class="form-select">
        <option value="">Semua Nurse Station</option>
        @foreach($nurse_stations as $ns)
            {{-- Gunakan 'ket' untuk value dan teks, dan variabel baru untuk 'selected' --}}
            <option value="{{ $ns->ket }}" {{ ($ns_group_selected ?? '') == $ns->ket ? 'selected' : '' }}>
                {{ $ns->ket }}
            </option>
        @endforeach
    </select>
</div>
        {{--  <div class="col-md-4">
            <label for="kd_bangsal" class="form-label">Nurse Station</label>
            <select name="kd_bangsal" id="kd_bangsal" class="form-select">
                <option value="">Semua Nurse Station</option>
                @foreach($nurse_stations as $ns)
                <option value="{{ $ns->kd_bangsal }}" {{ $kd_bangsal==$ns->kd_bangsal ? 'selected' : '' }}>
                    {{ $ns->nm_bangsal }}
                </option>
                @endforeach
            </select>
        </div>  --}}
        <div class="col-md-4">
            <label for="no_rawat" class="form-label">Nomor Rawat</label>
            <input type="text" name="no_rawat" id="no_rawat" class="form-control" value="{{ $no_rawat ?? '' }}"
                placeholder="Masukkan No. Rawat">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-warning w-100">
                <i class="fa-solid fa-search me-1"></i> Filter
            </button>
        </div>
    </form>

    {{-- Data Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Data Pasien | Total: {{ $data_ranap->total() }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th style="width: 110px;">Aksi</th>
                            <th>No. Rawat</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>Kamar</th>
                            <th>Tgl. Keluar</th>
                            <th>DPJP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data_ranap as $item)
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
                            <td>{{ $item->no_rkm_medis }}</td>
                            <td>{{ $item->nm_pasien }}</td>
                            <td>{{ $item->kamar }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_keluar)->format('d-m-Y') }}</td>
                            <td>{{ $item->nm_dokter_dpjp }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($data_ranap->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $data_ranap->appends(request()->query())->links('vendor.pagination.dark-theme') }}
            </div>
            @endif
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

        fetch(`{{ url('/dashboard/status-rm-ranap/kelengkapan') }}/${noRawat}`)
            .then(response => {
                if (!response.ok) {
                    console.error('Network response was not ok. Status:', response.status);
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (Array.isArray(data) && data.length) {
                    let htmlRows = '';
                    data.forEach(item => {
                        let badgeClass = item.status === 'Ada' ? 'bg-success' : 'bg-danger';
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
                tbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Gagal memuat data: ' + error.message + '</td></tr>';
            });
    });
});
</script>
@endpush
