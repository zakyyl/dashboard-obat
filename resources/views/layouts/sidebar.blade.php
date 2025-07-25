<div class="shadow-sm" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 fw-bold fs-5 text-primary border-bottom">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard Eksekutif
    </div>

    <div class="list-group list-group-flush mt-2">
        <a href="{{ url('/home') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('home') ? 'active bg-primary text-white' : '' }}">
            <i class="bi bi-house-door-fill me-2"></i> Home
        </a>

        <a href="{{ url('/obat/stok-barang') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('obat/stok-barang*') ? 'active bg-primary text-white' : '' }}">
            <i class="bi bi-box-seam-fill me-2"></i> Obat
        </a>

        {{-- POLI --}}
        <a class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center {{ request()->is('dashboard/poli*') ? 'active bg-primary text-white' : '' }}"
           data-bs-toggle="collapse" href="#submenuPoli" role="button"
           aria-expanded="{{ request()->is('dashboard/poli*') ? 'true' : 'false' }}"
           aria-controls="submenuPoli">
            <span><i class="bi bi-hospital-fill me-2"></i> Poli</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/poli*') ? 'show' : '' }}" id="submenuPoli">
            <a href="{{ url('/dashboard/poli') }}"
               class="list-group-item list-group-item-action ps-5 py-2 {{ request()->is('dashboard/poli') ? 'active bg-primary text-white' : '' }}">
                <i class="bi bi-dot me-1"></i> Poli per Pasien
            </a>
            <a href="{{ url('/dashboard/poli-perdokter') }}"
               class="list-group-item list-group-item-action ps-5 py-2 {{ request()->is('dashboard/poli-perdokter') ? 'active bg-primary text-white' : '' }}">
                <i class="bi bi-dot me-1"></i> Poli per Dokter
            </a>
        </div>

        {{-- PENUNJANG RAWAT INAP --}}
        <a class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center {{ request()->is('dashboard/labor-kunjungan-ranap') || request()->is('dashboard/radiologi-kunjungan-ranap') ? 'active bg-primary text-white' : '' }}"
           data-bs-toggle="collapse" href="#submenuPenunjangRanap" role="button"
           aria-expanded="{{ request()->is('dashboard/labor-kunjungan-ranap') || request()->is('dashboard/radiologi-kunjungan-ranap') ? 'true' : 'false' }}"
           aria-controls="submenuPenunjangRanap">
            <span><i class="bi bi-clipboard-pulse me-2"></i> Labor Rawat Inap</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/labor-kunjungan-ranap') || request()->is('dashboard/radiologi-kunjungan-ranap') ? 'show' : '' }}"
             id="submenuPenunjangRanap">
            <a href="{{ url('/dashboard/labor-kunjungan-ranap') }}"
               class="list-group-item list-group-item-action ps-5 py-2 {{ request()->is('dashboard/labor-kunjungan-ranap') ? 'active bg-primary text-white' : '' }}">
                <i class="bi bi-dot me-1"></i> Kunjungan Labor
            </a>
            <a href="{{ url('/dashboard/radiologi-kunjungan-ranap') }}"
               class="list-group-item list-group-item-action ps-5 py-2 {{ request()->is('dashboard/radiologi-kunjungan-ranap') ? 'active bg-primary text-white' : '' }}">
                <i class="bi bi-dot me-1"></i> Kunjungan Radiologi
            </a>
        </div>

        {{-- PENUNJANG RAWAT JALAN --}}
        <a class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center {{ request()->is('dashboard/labor-kunjungan-ralan') || request()->is('dashboard/radiologi-kunjungan-ralan') ? 'active bg-primary text-white' : '' }}"
           data-bs-toggle="collapse" href="#submenuPenunjangRalan" role="button"
           aria-expanded="{{ request()->is('dashboard/labor-kunjungan-ralan') || request()->is('dashboard/radiologi-kunjungan-ralan') ? 'true' : 'false' }}"
           aria-controls="submenuPenunjangRalan">
            <span><i class="bi bi-clipboard-pulse me-2"></i> Labor Rawat Jalan</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/labor-kunjungan-ralan') || request()->is('dashboard/radiologi-kunjungan-ralan') ? 'show' : '' }}"
             id="submenuPenunjangRalan">
            <a href="{{ url('/dashboard/labor-kunjungan-ralan') }}"
               class="list-group-item list-group-item-action ps-5 py-2 {{ request()->is('dashboard/labor-kunjungan-ralan') ? 'active bg-primary text-white' : '' }}">
                <i class="bi bi-dot me-1"></i> Kunjungan Labor
            </a>
            <a href="{{ url('/dashboard/radiologi-kunjungan-ralan') }}"
               class="list-group-item list-group-item-action ps-5 py-2 {{ request()->is('dashboard/radiologi-kunjungan-ralan') ? 'active bg-primary text-white' : '' }}">
                <i class="bi bi-dot me-1"></i> Kunjungan Radiologi
            </a>
        </div>

        {{-- KLAIM --}}
        <a href="{{ url('/pengajuan-klaim') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('pengajuan-klaim*') ? 'active bg-primary text-white' : '' }}">
            <i class="bi bi-journal-check me-2"></i> Pengajuan Klaim
        </a>
    </div>
</div>
