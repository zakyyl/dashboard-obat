<div id="sidebar-wrapper">
    <div class="sidebar-heading">
        <i class="bi bi-bezier"></i>
        <span>Dashboard Eksekutif</span>
    </div>

    <div class="list-group list-group-flush">
        <a href="{{ url('/home') }}"
            class="list-group-item list-group-item-action {{ request()->is('home') ? 'active' : '' }}">
            <i class="bi bi-house-fill me-3"></i>
            <span>Beranda</span>
        </a>

        <a href="{{ url('/obat/stok-barang') }}"
            class="list-group-item list-group-item-action {{ request()->is('obat/stok-barang*') ? 'active' : '' }}">
            <i class="bi bi-capsule me-3"></i>
            <span>Manajemen Obat</span>
        </a>

        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request()->is('dashboard/poli*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#submenuPoli" role="button"
            aria-expanded="{{ request()->is('dashboard/poli*') ? 'true' : 'false' }}" aria-controls="submenuPoli">
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

        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
   {{ request()->is('dashboard/labor-kunjungan-ranap') || request()->is('dashboard/labor-kunjungan-ralan') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#submenuLabor" role="button"
            aria-expanded="{{ request()->is('dashboard/labor-kunjungan-ranap') || request()->is('dashboard/labor-kunjungan-ralan') ? 'true' : 'false' }}"
            aria-controls="submenuLabor">
            <div class="d-flex align-items-center">
                <i class="bi bi-clipboard2-pulse me-3"></i>
                <span>Laboratorium</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/labor-kunjungan-ranap') || request()->is('dashboard/labor-kunjungan-ralan') ? 'show' : '' }}"
            id="submenuLabor">
            <a href="{{ url('/dashboard/labor-kunjungan-ranap') }}"
                class="list-group-item list-group-item-action {{ request()->is('dashboard/labor-kunjungan-ranap') ? 'active' : '' }}">
                <i class="bi bi-hospital me-3"></i>
                <span>Lab Rawat Inap</span>
            </a>
            <a href="{{ url('/dashboard/labor-kunjungan-ralan') }}"
                class="list-group-item list-group-item-action {{ request()->is('dashboard/labor-kunjungan-ralan') ? 'active' : '' }}">
                <i class="bi bi-building me-3"></i>
                <span>Lab Rawat Jalan</span>
            </a>
        </div>

        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
   {{ request()->is('dashboard/radiologi-kunjungan-ranap') || request()->is('dashboard/radiologi-kunjungan-ralan') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#submenuRadiologi" role="button"
            aria-expanded="{{ request()->is('dashboard/radiologi-kunjungan-ranap') || request()->is('dashboard/radiologi-kunjungan-ralan') ? 'true' : 'false' }}"
            aria-controls="submenuRadiologi">
            <div class="d-flex align-items-center">
                <i class="bi bi-camera-reels me-3"></i>
                <span>Radiologi</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/radiologi-kunjungan-ranap') || request()->is('dashboard/radiologi-kunjungan-ralan') ? 'show' : '' }}"
            id="submenuRadiologi">
            <a href="{{ url('/dashboard/radiologi-kunjungan-ranap') }}"
                class="list-group-item list-group-item-action {{ request()->is('dashboard/radiologi-kunjungan-ranap') ? 'active' : '' }}">
                <i class="bi bi-hospital me-3"></i>
                <span>Radiologi Rawat Inap</span>
            </a>
            <a href="{{ url('/dashboard/radiologi-kunjungan-ralan') }}"
                class="list-group-item list-group-item-action {{ request()->is('dashboard/radiologi-kunjungan-ralan') ? 'active' : '' }}">
                <i class="bi bi-building me-3"></i>
                <span>Radiologi Rawat Jalan</span>
            </a>
        </div>

        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
   {{ request()->is('dashboard/pasien-ranap') || request()->is('dashboard/pasien-ralan') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#submenuPasien" role="button"
            aria-expanded="{{ request()->is('dashboard/pasien-ranap') || request()->is('dashboard/pasien-ralan') ? 'true' : 'false' }}"
            aria-controls="submenuPasien">
            <div class="d-flex align-items-center">
                <i class="bi bi-people-fill me-3"></i>
                <span>Pasien</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/pasien-ranap') || request()->is('dashboard/pasien-ralan') ? 'show' : '' }}"
            id="submenuPasien">
            <a href="{{ url('/dashboard/pasien-ranap') }}"
                class="list-group-item list-group-item-action {{ request()->is('dashboard/pasien-ranap') ? 'active' : '' }}">
                <i class="bi bi-hospital me-3"></i>
                <span>Pasien Rawat Inap</span>
            </a>
            <a href="{{ url('/dashboard/pasien-ralan') }}"
                class="list-group-item list-group-item-action {{ request()->is('dashboard/pasien-ralan') ? 'active' : '' }}">
                <i class="bi bi-building me-3"></i>
                <span>Pasien Rawat Jalan</span>
            </a>
        </div>

        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center 
    {{ request()->is('pengajuan-claim-ralan') || request()->is('pengajuan-claim-ranap') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#submenuKlaim" role="button"
            aria-expanded="{{ request()->is('pengajuan-claim-ralan') || request()->is('pengajuan-claim-ranap') ? 'true' : 'false' }}"
            aria-controls="submenuKlaim">
            <div class="d-flex align-items-center">
                <i class="bi bi-file-earmark-check me-3"></i>
                <span>Pengajuan Klaim</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('pengajuan-claim-ralan') || request()->is('pengajuan-claim-ranap') ? 'show' : '' }}"
            id="submenuKlaim">
            <a href="{{ url('/pengajuan-claim-ralan') }}"
                class="list-group-item list-group-item-action {{ request()->is('pengajuan-claim-ralan') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-medical me-3"></i>
                <span>Klaim Rawat Jalan</span>
            </a>
            <a href="{{ url('/pengajuan-claim-ranap') }}"
                class="list-group-item list-group-item-action {{ request()->is('pengajuan-claim-ranap') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-medical me-3"></i>
                <span>Klaim Rawat Inap</span>
            </a>
        </div>


        <div class="my-3 mx-3">
            <hr class="border-top" style="opacity: 0.1;">
        </div>


    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-center">
            <i class="bi bi-shield-check me-2"></i>
            <span>Sistem Informasi RS</span>
        </div>
        <div class="mt-1">v0.1</div>
    </div>
</div>
