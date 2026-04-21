@php
use Illuminate\Support\Facades\Auth;
$role = Auth::user()->role ?? null;
@endphp

<div id="sidebar-wrapper">
    <div class="sidebar-heading">
        <i class="bi bi-bezier"></i>
        <span>Dashboard Eksekutif</span>
    </div>

    <div class="list-group list-group-flush">
        @if ($role === 'admin')
        {{-- Beranda --}}
        <a href="{{ route('home') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="bi bi-house-fill me-3"></i>
            <span>Beranda</span>
        </a>
        @endif

        {{-- Manajemen Obat --}}
@if ($role === 'admin' || $role === 'obat')
<a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
   {{ request()->is('obat/*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#submenuObat" role="button"
    aria-expanded="{{ request()->is('obat/*') ? 'true' : 'false' }}" aria-controls="submenuObat">
    <div class="d-flex align-items-center">
        <i class="bi bi-capsule me-3"></i>
        <span>Manajemen Obat</span>
    </div>
    <i class="bi bi-chevron-down transition-transform"></i>
</a>
<div class="collapse {{ request()->is('obat/*') ? 'show' : '' }}" id="submenuObat">
    <a href="{{ url('/obat/stok-barang') }}"
        class="list-group-item list-group-item-action {{ request()->is('obat/stok-barang') ? 'active' : '' }}">
        <i class="bi bi-hospital me-3"></i>
        <span>Stok Obat Saat Ini</span>
    </a>
    <a href="{{ url('/obat/stok-barang-per-depo') }}"
        class="list-group-item list-group-item-action {{ request()->is('obat/stok-barang-per-depo') ? 'active' : '' }}">
        <i class="bi bi-building me-3"></i>
        <span>Stok Obat per Depo</span>
    </a>
    <a href="{{ url('/obat/stok-barang-masuk') }}"
        class="list-group-item list-group-item-action {{ request()->is('obat/stok-barang-masuk') ? 'active' : '' }}">
        <i class="bi bi-box-arrow-in-down me-3"></i>
        <span>Obat Masuk</span>
    </a>
    <a href="{{ url('/obat/stok-barang-keluar') }}"
        class="list-group-item list-group-item-action {{ request()->is('obat/stok-barang-keluar') ? 'active' : '' }}">
        <i class="bi bi-box-arrow-up me-3"></i>
        <span>Obat Keluar</span>
    </a>
</div>
@endif

@if ($role === 'admin')
        {{-- Poliklinik --}}
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
           {{ request()->is('dashboard/poli*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#submenuPoli"
            role="button" aria-expanded="{{ request()->is('dashboard/poli*') ? 'true' : 'false' }}"
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

        {{-- Laboratorium --}}
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
           {{ request()->is('dashboard/labor-*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#submenuLabor"
            role="button" aria-expanded="{{ request()->is('dashboard/labor-*') ? 'true' : 'false' }}"
            aria-controls="submenuLabor">
            <div class="d-flex align-items-center">
                <i class="bi bi-clipboard2-pulse me-3"></i>
                <span>Laboratorium</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/labor-*') ? 'show' : '' }}" id="submenuLabor">
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

        {{-- Radiologi --}}
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
           {{ request()->is('dashboard/radiologi-*') ? 'active' : '' }}" data-bs-toggle="collapse"
            href="#submenuRadiologi" role="button"
            aria-expanded="{{ request()->is('dashboard/radiologi-*') ? 'true' : 'false' }}"
            aria-controls="submenuRadiologi">
            <div class="d-flex align-items-center">
                <i class="bi bi-camera-reels me-3"></i>
                <span>Radiologi</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/radiologi-*') ? 'show' : '' }}" id="submenuRadiologi">
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

        {{-- Pasien --}}
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
           {{ request()->is('dashboard/pasien-*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#submenuPasien"
            role="button" aria-expanded="{{ request()->is('dashboard/pasien-*') ? 'true' : 'false' }}"
            aria-controls="submenuPasien">
            <div class="d-flex align-items-center">
                <i class="bi bi-people-fill me-3"></i>
                <span>Pasien</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/pasien-*') ? 'show' : '' }}" id="submenuPasien">
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
            <a href="{{ url('/dashboard/pasien-polri') }}"
                class="list-group-item list-group-item-action {{ request()->is('dashboard/pasien-ralan') ? 'active' : '' }}">
                <i class="bi bi-building me-3"></i>
                <span>Pasien Polri</span>
            </a>
        </div>

        {{-- Klaim --}}
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
           {{ request()->is('pengajuan-claim-*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#submenuKlaim"
            role="button" aria-expanded="{{ request()->is('pengajuan-claim-*') ? 'true' : 'false' }}"
            aria-controls="submenuKlaim">
            <div class="d-flex align-items-center">
                <i class="bi bi-file-earmark-check me-3"></i>
                <span>Pengajuan Klaim</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('pengajuan-claim-*') ? 'show' : '' }}" id="submenuKlaim">
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

        {{-- Kamar --}}
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
           {{ request()->routeIs('kamar.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#submenuKamar"
            role="button" aria-expanded="{{ request()->routeIs('kamar.*') ? 'true' : 'false' }}"
            aria-controls="submenuKamar">
            <div class="d-flex align-items-center">
                <i class="bi bi-door-open-fill me-3"></i>
                <span>Kamar</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->routeIs('kamar.*') ? 'show' : '' }}" id="submenuKamar">
            <a href="{{ route('kamar.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('kamar.index') ? 'active' : '' }}">
                <i class="bi bi-usb-mini me-3"></i>
                <span>Data Kamar</span>
            </a>
        </div>

        {{-- Dokter --}}
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
           {{ request()->routeIs('dokter.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#submenuDokter"
            role="button" aria-expanded="{{ request()->routeIs('dokter.*') ? 'true' : 'false' }}"
            aria-controls="submenuDokter">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-badge-fill me-3"></i>
                <span>Dokter</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->routeIs('dokter.*') ? 'show' : '' }}" id="submenuDokter">
            <a href="{{ route('dokter.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('dokter.index') ? 'active' : '' }}">
                <i class="bi bi-calendar-check me-3"></i>
                <span>Jadwal Dokter</span>
            </a>
        </div>

        
    @endif

    <!-- Data Rekam Medis -->
        @if ($role === 'admin' || $role === 'erm')
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
           {{ request()->is('dashboard/status-rm-*') || request()->routeIs('igd.*') ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#submenuDataRM" role="button"
            aria-expanded="{{ request()->is('dashboard/status-rm-*') || request()->routeIs('igd.*') ? 'true' : 'false' }}"
            aria-controls="submenuDataRM">
            <div class="d-flex align-items-center">
                <i class="bi bi-clipboard2-pulse me-3"></i>
                <span>Data Rekam Medis</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('dashboard/status-rm-*') || request()->routeIs('igd.*') ? 'show' : '' }}"
            id="submenuDataRM">
            <a href="{{ route('statusrm-ralan.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('statusrm-ralan.index') ? 'active' : '' }}">
                <i class="bi bi-hospital me-3"></i>
                <span>Status Data Rawat Jalan</span>
            </a>
            <a href="{{ route('statusrm-ranap.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('statusrm-ranap.index') ? 'active' : '' }}">
                <i class="bi bi-building me-3"></i>
                <span>Status Data Rawat Inap</span>
            </a>
            <a href="{{ route('statusrm-igd.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('igd.index') ? 'active' : '' }}">
                <i class="bi bi-hospital-fill me-3"></i>
                <span>Status Data IGD</span>
            </a>
        </div>
        @endif

                
        
        @if ($role === 'admin' || $role === 'keuangan')
        {{-- Keuangan --}}
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center
           {{ request()->is('keuangan/*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#submenuKeuangan"
            role="button" aria-expanded="{{ request()->is('keuangan/*') ? 'true' : 'false' }}"
            aria-controls="submenuKeuangan">
            <div class="d-flex align-items-center">
                <i class="bi bi-wallet2 me-3"></i>
                <span>Keuangan</span>
            </div>
            <i class="bi bi-chevron-down transition-transform"></i>
        </a>
        <div class="collapse {{ request()->is('keuangan/*') ? 'show' : '' }}" id="submenuKeuangan">
            <a href="{{ route('pemasukan.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('pemasukan.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack me-3"></i>
                <span>Pemasukan</span>
            </a>
            <a href="{{ route('pengeluaran.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin me-3"></i>
                <span>Pengeluaran</span>
            </a>
            <a href="{{ route('keuangan.rekap') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('keuangan.rekap') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line me-3"></i>
                <span>Rekap Keuangan</span>
            </a>
        </div>

        @endif
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