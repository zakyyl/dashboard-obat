@extends('layouts.app')

@section('title', 'Status Data RM Rawat Inap')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
        <i class="bi bi-file-earmark-check me-2"></i>Status Data Rekam Medis Rawat Inap
    </h2>

    <div class="card mb-3 mb-md-4 shadow-sm bg-dark border-0">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('statusrm-ranap.index') }}" autocomplete="off">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="tanggal_awal" class="form-label text-light small mb-1">Tgl Awal Keluar:</label>
                        <input type="date" name="tanggal_awal" id="tanggal_awal"
                            class="form-control form-control-sm bg-dark text-light border-secondary" value="{{ $tanggal_awal }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="tanggal_akhir" class="form-label text-light small mb-1">Tgl Akhir Keluar:</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                            class="form-control form-control-sm bg-dark text-light border-secondary" value="{{ $tanggal_akhir }}">
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="ns_group" class="form-label text-light small mb-1">Nurse Station:</label>
                        <select name="ns_group" id="ns_group" class="form-select form-select-sm bg-dark text-light border-secondary">
                            <option value="">Semua NS</option>
                            @foreach($nurse_stations as $ns)
                            <option value="{{ $ns->ket }}" {{ ($ns_group_selected ?? '') == $ns->ket ? 'selected' : '' }}>
                                {{ $ns->ket }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label for="no_rawat" class="form-label text-light small mb-1">Nomor Rawat:</label>
                        <input type="text" name="no_rawat" id="no_rawat"
                            class="form-control form-control-sm bg-dark text-light border-secondary"
                            value="{{ $no_rawat ?? '' }}" placeholder="No. Rawat">
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
                    Data Pasien <span class="badge bg-secondary text-light ms-1">{{ $data_ranap->total() }} Pasien</span>
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-striped table-bordered align-middle text-light mb-0" style="min-width: 820px;">
                    <thead class="table-secondary text-dark text-center">
                        <tr>
                            <th style="width: 80px;">Aksi</th>
                            <th style="width: 15%;">No. Rawat</th>
                            <th style="width: 12%;">No. RM</th>
                            <th style="width: 22%;">Nama Pasien</th>
                            <th style="width: 15%;">Kamar</th>
                            <th style="width: 13%;">Tgl. Keluar</th>
                            <th style="width: 23%;">DPJP</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @forelse ($data_ranap as $item)
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
                            <td class="font-monospace">{{ $item->no_rkm_medis }}</td>
                            <td class="fw-semibold">{{ $item->nm_pasien }}</td>
                            <td><span class="badge bg-secondary">{{ $item->kamar }}</span></td>
                            <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tgl_keluar)->format('d-m-Y') }}</td>
                            <td>{{ $item->nm_dokter }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Data tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data_ranap->hasPages())
            <div class="d-flex justify-content-center justify-content-md-end mt-3">
                {{ $data_ranap->appends(request()->query())->links('vendor.pagination.dark-theme') }}
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
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-kelengkapan').forEach(button => {
        button.addEventListener('click', function() {
            const noRawat = this.dataset.norawat;
            const namaPasien = this.dataset.namapasien;

            document.getElementById('namaPasienModal').innerText = namaPasien;
            document.getElementById('noRawatModal').innerText = noRawat;

            const tbody = document.getElementById('isiKelengkapan');
            tbody.innerHTML = `<tr><td colspan="2" class="text-center py-4 text-warning">Memuat data...</td></tr>`;

            fetch(`{{ url('/dashboard/status-rm-ranap/kelengkapan') }}/${noRawat}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(result => {
                    if (Array.isArray(result.data) && result.data.length) {
                        let htmlRows = '';
                        const selectedNS = document.getElementById('ns_group').value;

                        const icuItems = ['Kriteria Masuk ICU', 'Kriteria Keluar ICU'];
                        const kebidananItems = ['Catatan Persalinan'];

                        result.data.forEach((item, index) => {
                            let showItem = true;
                            const isIcuItem = icuItems.includes(item.nama);
                            const isKebidananItem = kebidananItems.includes(item.nama);

                            if (isIcuItem) {
                                showItem = (selectedNS === 'NS ICU');
                            } else if (isKebidananItem) {
                                showItem = (selectedNS === 'NS Kebidanan');
                            }
                            
                            if (showItem) {
                                let badgeClass = item.status === 'Ada' ? 'bg-success' :
                                                 item.status === 'Tidak Ada' ? 'bg-danger' :
                                                 item.status === 'Tabel Error' ? 'bg-dark' : 'bg-secondary';

                                if (item.hasOwnProperty('submenu')) {
                                    let submenuContent = '<p class="mb-0 text-muted"><em>Tidak ada detail.</em></p>';

                                    if (Array.isArray(item.submenu) && item.submenu.length > 0) {
                                        submenuContent = ''; 
                                        item.submenu.forEach((subItem, subIndex) => {
                                            let profesiBadgeClass = 'bg-secondary';
                                            const namaPetugas = subItem.nm_dokter ? subItem.nm_dokter.toLowerCase() : '';
                                            if (namaPetugas.includes('dr.') || namaPetugas.includes(', sp.')) {
                                                profesiBadgeClass = 'bg-success';
                                            } else if (namaPetugas.includes('am.kep') || namaPetugas.includes('s.kep')) {
                                                profesiBadgeClass = 'bg-primary';
                                            } else if (namaPetugas.includes('apt.') || namaPetugas.includes('s.farm')) {
                                                profesiBadgeClass = 'bg-warning text-dark';
                                            }
                                            const borderClass = (subIndex < item.submenu.length - 1) ? 'border-bottom' : '';
                                            submenuContent += `
                                                <div class="d-flex justify-content-between align-items-center py-2 ${borderClass}">
                                                    <span><i class="bi bi-calendar-event me-2"></i>${subItem.tanggal}</span>
                                                    <span class="badge ${profesiBadgeClass} rounded-pill">${subItem.nm_dokter}</span>
                                                </div>`;
                                        });
                                    }

                                    htmlRows += `
                                        <tr data-bs-toggle="collapse" href="#submenu-${index}" role="button">
                                            <td>${item.nama} <i class="bi bi-chevron-down ms-2"></i></td>
                                            <td class="text-center"><span class="badge ${badgeClass}">${item.status}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="p-0 border-0">
                                                <div class="collapse" id="submenu-${index}"><div class="p-3 bg-secondary bg-opacity-10">${submenuContent}</div></div>
                                            </td>
                                        </tr>`;
                                } else {
                                    htmlRows += `<tr><td>${item.nama}</td><td class="text-center"><span class="badge ${badgeClass}">${item.status}</span></td></tr>`;
                                }
                            }
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
});
</script>
@endpush
