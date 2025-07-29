<div id="sidebar-wrapper">
    <!-- Sidebar Header -->
    <div class="sidebar-heading">
        <i class="bi bi-bezier"></i>
        <span>Dashboard Eksekutif</span>
    </div>

    <!-- Navigation Menu -->
    <div class="list-group list-group-flush">
        <!-- Home -->
        <a href="{{ url('/home') }}"
           class="list-group-item list-group-item-action {{ request()->is('home') ? 'active' : '' }}">
            <i class="bi bi-house-fill me-3"></i>
            <span>Beranda</span>
        </a>

        <!-- Obat -->
        <a href="{{ url('/obat/stok-barang') }}"
           class="list-group-item list-group-item-action {{ request()->is('obat/stok-barang*') ? 'active' : '' }}">
            <i class="bi bi-capsule me-3"></i>
            <span>Manajemen Obat</span>
        </a>

        <!-- Poli Section -->
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->is('dashboard/poli*') ? 'active' : '' }}"
           data-bs-toggle="collapse" 
           href="#submenuPoli" 
           role="button"
           aria-expanded="{{ request()->is('dashboard/poli*') ? 'true' : 'false' }}"
           aria-controls="submenuPoli">
            <div class="d-flex align-items-center">
                <i class="bi bi-hospital me-3"></i>
                <span>Poliklinik</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/poli*') ? 'show' : '' }}" id="submenuPoli">
            <a href="{{ url('/dashboard/poli') }}"
               class="list-group-item list-group-item-action {{ request()->is('dashboard/poli') && !request()->is('dashboard/poli-perdokter') ? 'active' : '' }}">
                <i class="bi bi-person-check me-3"></i>
                <span>Kunjungan Pasien</span>
            </a>
            <a href="{{ url('/dashboard/poli-perdokter') }}"
               class="list-group-item list-group-item-action {{ request()->is('dashboard/poli-perdokter') ? 'active' : '' }}">
                <i class="bi bi-person-badge me-3"></i>
                <span>Kunjungan per Dokter</span>
            </a>
        </div>

        <!-- Labor Rawat Inap Section -->
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->is('dashboard/labor-kunjungan-ranap') || request()->is('dashboard/radiologi-kunjungan-ranap') ? 'active' : '' }}"
           data-bs-toggle="collapse" 
           href="#submenuPenunjangRanap" 
           role="button"
           aria-expanded="{{ request()->is('dashboard/labor-kunjungan-ranap') || request()->is('dashboard/radiologi-kunjungan-ranap') ? 'true' : 'false' }}"
           aria-controls="submenuPenunjangRanap">
            <div class="d-flex align-items-center">
                <i class="bi bi-building-add me-3"></i>
                <span>Penunjang Rawat Inap</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/labor-kunjungan-ranap') || request()->is('dashboard/radiologi-kunjungan-ranap') ? 'show' : '' }}"
             id="submenuPenunjangRanap">
            <a href="{{ url('/dashboard/labor-kunjungan-ranap') }}"
               class="list-group-item list-group-item-action {{ request()->is('dashboard/labor-kunjungan-ranap') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-pulse me-3"></i>
                <span>Laboratorium</span>
            </a>
            <a href="{{ url('/dashboard/radiologi-kunjungan-ranap') }}"
               class="list-group-item list-group-item-action {{ request()->is('dashboard/radiologi-kunjungan-ranap') ? 'active' : '' }}">
                <i class="bi bi-camera-reels me-3"></i>
                <span>Radiologi</span>
            </a>
            <a href="{{ url('/dashboard/pasien-ranap') }}"
               class="list-group-item list-group-item-action {{ request()->is('dashboard/pasien-ranap') ? 'active' : '' }}">
                <i class="bi bi-people-fill me-3"></i>
                <span>Pasien</span>
            </a>
        </div>

        <!-- Labor Rawat Jalan Section -->
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->is('dashboard/labor-kunjungan-ralan') || request()->is('dashboard/radiologi-kunjungan-ralan') ? 'active' : '' }}"
           data-bs-toggle="collapse" 
           href="#submenuPenunjangRalan" 
           role="button"
           aria-expanded="{{ request()->is('dashboard/labor-kunjungan-ralan') || request()->is('dashboard/radiologi-kunjungan-ralan') ? 'true' : 'false' }}"
           aria-controls="submenuPenunjangRalan">
            <div class="d-flex align-items-center">
                <i class="bi bi-building me-3"></i>
                <span>Penunjang Rawat Jalan</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/labor-kunjungan-ralan') || request()->is('dashboard/radiologi-kunjungan-ralan') ? 'show' : '' }}"
             id="submenuPenunjangRalan">
            <a href="{{ url('/dashboard/labor-kunjungan-ralan') }}"
               class="list-group-item list-group-item-action {{ request()->is('dashboard/labor-kunjungan-ralan') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-pulse me-3"></i>
                <span>Laboratorium</span>
            </a>
            <a href="{{ url('/dashboard/radiologi-kunjungan-ralan') }}"
               class="list-group-item list-group-item-action {{ request()->is('dashboard/radiologi-kunjungan-ralan') ? 'active' : '' }}">
                <i class="bi bi-camera-reels me-3"></i>
                <span>Radiologi</span>
            </a>
            <a href="{{ url('/dashboard/pasien-ralan') }}"
               class="list-group-item list-group-item-action {{ request()->is('dashboard/pasien-ralan') ? 'active' : '' }}">
                <i class="bi bi-people-fill me-3"></i>
                <span>Pasien</span>
            </a>
        </div>

        <!-- Klaim -->
        <a href="{{ url('/dashboard/pengajuan-claim') }}"
           class="list-group-item list-group-item-action {{ request()->is('dashboard/pengajuan-claim*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-check me-3"></i>
            <span>Pengajuan Klaim</span>
        </a>

        <!-- Divider -->
        <div class="my-3 mx-3">
            <hr class="border-top" style="opacity: 0.1;">
        </div>

        
    </div>

    <!-- Sidebar Footer (Optional) -->
    <div class="mt-auto p-3 text-center" style="opacity: 0.6; font-size: 0.75rem;">
        <div class="d-flex align-items-center justify-content-center">
            <i class="bi bi-shield-check me-2"></i>
            <span>Sistem Informasi RS</span>
        </div>
        <div class="mt-1">v0.1</div>
    </div>
</div>