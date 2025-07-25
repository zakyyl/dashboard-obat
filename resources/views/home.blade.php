@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<style>
    /* Custom styles for this dashboard page to match the image */
    body.sb-sidenav-toggled #page-content-wrapper {
        background-color: #1a202c;
        /* Dark blue background for the content area */
        color: #f8f9fa;
        /* Light text color for the dashboard content */
    }

    /* Ensure the background applies to the entire content area */
    #page-content-wrapper>.container-fluid {
        background-color: #1a202c;
        /* Apply to the inner container as well */
        min-height: calc(100vh - 56px);
        /* Adjust height to fill viewport below navbar */
        padding-top: 1.5rem !important;
        /* Add some top padding */
    }

    /* Card styling for stats and menu items */
    .dashboard-card {
        background-color: #ffffff;
        /* White background for cards */
        color: #212529;
        /* Dark text for cards */
        border-radius: 0.75rem;
        /* Rounded corners */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        /* Subtle lift effect on hover */
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
    }

    /* Specific styling for stat cards */
    .stat-card .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem;
    }

    .stat-card .stat-icon {
        font-size: 2.5rem;
        color: #007bff;
        /* Blue color for stat icons */
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: #007bff;
        /* Blue color for stat values */
    }

    .stat-card .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        /* Gray color for stat labels */
    }

    /* Specific styling for menu cards */
    .menu-card {
        text-align: center;
        padding: 1.5rem;
        height: 100%;
        /* Ensure cards in a row have equal height */
    }

    .menu-card .menu-icon {
        font-size: 3.5rem;
        /* Larger icons for menu items */
        margin-bottom: 0.5rem;
        color: #007bff;
        /* Blue color for menu icons */
    }

    .menu-card .menu-title {
        font-size: 1.1rem;
        font-weight: bold;
        color: #212529;
        /* Dark text for menu titles */
        text-transform: uppercase;
    }

    .menu-card .menu-subtitle {
        font-size: 0.8rem;
        color: #6c757d;
        /* Gray color for menu subtitles */
        text-transform: uppercase;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .stat-card .stat-icon {
            font-size: 2rem;
        }

        .stat-card .stat-value {
            font-size: 1.5rem;
        }

        .menu-card .menu-icon {
            font-size: 3rem;
        }

        #page-content-wrapper>.container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }
</style>

<div class="container-fluid"> {{-- Use container-fluid for full width if desired --}}
    <h2 class="mb-4 text-white">Dashboard Eksekutif</h2> {{-- Judul Dashboard --}}

    {{-- Top Row - Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card stat-card h-100">
                <div class="card-body">
                    <div>
                        {{-- Pasien Hari Ini --}}
                        <div class="stat-value">{{ $pasienHariIni }}</div>
                        <div class="stat-label">Pasien Hari Ini</div>
                    </div>
                    <i class="bi bi-person-fill stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card stat-card h-100">
                <div class="card-body">
                    <div>
                        {{-- Pasien Baru Hari Ini --}}
                        <div class="stat-value">{{ $pasienBaruHariIni }}</div>
                        <div class="stat-label">Pasien Baru Hari Ini</div>
                    </div>
                    <i class="bi bi-person-plus-fill stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card stat-card h-100">
                <div class="card-body">
                    <div>
                        {{-- Kunjungan Hari Ini --}}
                        <div class="stat-value">{{ $kunjunganHariIni }}</div>
                        <div class="stat-label">Kunjungan Hari Ini</div>
                    </div>
                    <i class="bi bi-calendar-check-fill stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card stat-card h-100">
                <div class="card-body">
                    <div>
                        {{-- Kunjungan Bulan Ini --}}
                        <div class="stat-value">{{ $kunjunganBulanIni }}</div>
                        <div class="stat-label">Kunjungan Bulan Ini</div>
                    </div>
                    <i class="bi bi-calendar-event-fill stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- NEW SECTION: RAWAT JALAN PERBULAN --}}
    <h2 class="mb-4 mt-5 text-white">RAWAT JALAN PERBULAN</h2> {{-- Judul baru --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Grafik Kunjungan Rawat Jalan per Bulan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4 mt-4"> {{-- g-4 for consistent gutter spacing --}}
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <a href="#" class="text-decoration-none"> {{-- Link ke halaman terkait --}}
                                <div class="card dashboard-card menu-card">
                                    <i class="bi bi-hospital-fill menu-icon"></i>
                                    <div class="menu-title">Rumah Sakit</div>
                                    <div class="menu-subtitle">PROFIL</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <a href="#" class="text-decoration-none">
                                <div class="card dashboard-card menu-card">
                                    <i class="bi bi-globe menu-icon"></i>
                                    <div class="menu-title">Website</div>
                                    <div class="menu-subtitle">BERITA</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <a href="#" class="text-decoration-none">
                                <div class="card dashboard-card menu-card">
                                    <i class="bi bi-calendar-week-fill menu-icon"></i>
                                    <div class="menu-title">Jadwal</div>
                                    <div class="menu-subtitle">DOKTER</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <a href="#" class="text-decoration-none">
                                <div class="card dashboard-card menu-card">
                                    <i class="bi bi-door-open-fill menu-icon"></i> {{-- Atau bi-hospital-fill,
                                    bi-bed-fill --}}
                                    <div class="menu-title">Informasi</div>
                                    <div class="menu-subtitle">KAMAR</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <a href="#" class="text-decoration-none">
                                <div class="card dashboard-card menu-card">
                                    <i class="bi bi-chat-left-text-fill menu-icon"></i> {{-- Atau bi-heart-fill --}}
                                    <div class="menu-title">Penilaian</div>
                                    <div class="menu-subtitle">PENGADUAN</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <a href="#" class="text-decoration-none">
                                <div class="card dashboard-card menu-card">
                                    <i class="bi bi-star-fill menu-icon"></i>
                                    <div class="menu-title">Index</div>
                                    <div class="menu-subtitle">Kepuasan Pasien</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <a href="#" class="text-decoration-none">
                                <div class="card dashboard-card menu-card">
                                    <i class="bi bi-folder-fill menu-icon"></i> {{-- Atau bi-file-earmark-text-fill --}}
                                    <div class="menu-title">SigapVer</div>
                                    <div class="menu-subtitle">VISUM</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END NEW SECTION --}}


    {{-- Grid - Menu Cards (Original) --}}


</div>
@endsection
