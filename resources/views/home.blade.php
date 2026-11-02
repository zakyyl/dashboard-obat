@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid text-white px-2 px-md-3 py-3 py-md-4 min-vh-100">
    {{-- Metric Cards --}}
    <div class="row text-center mb-3 mb-md-4 g-2 g-md-3">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card text-white bg-dark h-100 shadow border-0">
                <div class="card-body p-2 p-md-3">
                    <h3 class="text-warning fw-bold fs-4 fs-md-3 mb-1">{{ $pasienHariIni }}</h3>
                    <p class="card-text text-light small mb-0">Pasien Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card text-white bg-dark h-100 shadow border-0">
                <div class="card-body p-2 p-md-3">
                    <h3 class="text-warning fw-bold fs-4 fs-md-3 mb-1">{{ $resepHariIni }}</h3>
                    <p class="card-text text-light small mb-0">Resep Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card text-white bg-dark h-100 shadow border-0">
                <div class="card-body p-2 p-md-3">
                    <h3 class="text-warning fw-bold fs-4 fs-md-3 mb-1">{{ $rawatInapHariIni }}</h3>
                    <p class="card-text text-light small mb-0">Rawat Inap Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card text-white bg-dark h-100 shadow border-0">
                <div class="card-body p-2 p-md-3">
                    <h3 class="text-warning fw-bold fs-4 fs-md-3 mb-1">{{ $pasienMobileJknHariIni }}</h3>
                    <p class="card-text text-light small mb-0">Pasien Mobile JKN</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card text-white bg-dark h-100 shadow border-0">
                <div class="card-body p-2 p-md-3">
                    <h3 class="text-warning fw-bold fs-4 fs-md-3 mb-1">{{ $rawatIgdHariIni }}</h3>
                    <p class="card-text text-light small mb-0">Pasien IGD</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card text-white bg-dark h-100 shadow border-0">
                <div class="card-body p-2 p-md-3">
                    <h3 class="text-warning fw-bold fs-4 fs-md-3 mb-1">{{ $rawatpolri }}</h3>
                    <p class="card-text text-light small mb-0">Pasien Anggota Polri</p>
                </div>
            </div>
        </div>
    </div>

    @php
    $namaBulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];
    $categories = [];
    $seriesColumn = [];
    $seriesLine = [];
    foreach ($rawatJalanPerBulan as $item) {
        $categories[] = $namaBulan[$item->bulan] ?? $item->bulan;
        $seriesColumn[] = $item->jumlah;
        $seriesLine[] = $item->jumlah;
    }

    $caraBayarLabels = [];
    $caraBayarValues = [];
    foreach ($caraBayar as $cb) {
        $caraBayarLabels[] = $cb->png_jawab;
        $caraBayarValues[] = $cb->jumlah;
    }

    $labelsKematian = [];
    $dataKematian = [];
    foreach ($kematianPerBulan as $item) {
        $labelsKematian[] = $item->bulan;
        $dataKematian[] = $item->jumlah;
    }
    @endphp

    {{-- Chart Rawat Jalan Per Bulan --}}
    <div class="card text-white bg-dark shadow border-0 mb-4">
        <div class="card-header border-bottom border-secondary bg-transparent">
            <h5 class="mb-0 fw-bold text-warning fs-6 fs-md-5">RAWAT JALAN PERBULAN</h5>
        </div>
        <div class="card-body p-2 p-md-3">
            <div id="chart-rawatjalan" style="width: 100%;"></div>
        </div>
    </div>

    {{-- Grid: Keuangan, Pasien Mati, Cara Bayar --}}
    <div class="row g-3 mb-4">
        {{-- Rekap Keuangan --}}
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card text-white bg-dark h-100 shadow border-0">
                <div class="card-header border-secondary border-bottom bg-transparent">
                    <h5 class="mb-0 fw-bold text-warning fs-6 fs-md-5">📅 Rekap Keuangan Bulan {{ now()->format('F Y') }}</h5>
                </div>
                <div class="card-body p-3">
                    <div class="card bg-success bg-opacity-25 border-success mb-3" style="border-left: 4px solid #28a745;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="text-success mb-0 small fw-bold">💰 PEMASUKAN BULAN INI</h6>
                                <span class="badge bg-success" style="font-size: 0.7rem;">Masuk</span>
                            </div>
                            <h4 class="fw-bold text-success mb-1 fs-5 fs-md-4">
                                Rp {{ number_format($totalPemasukanBulanIni, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                Total pemasukan bulan {{ now()->format('F Y') }}
                            </small>
                        </div>
                    </div>

                    <div class="card bg-danger bg-opacity-25 border-danger mb-3" style="border-left: 4px solid #dc3545;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="text-danger mb-0 small fw-bold">💸 PENGELUARAN BULAN INI</h6>
                                <span class="badge bg-danger" style="font-size: 0.7rem;">Keluar</span>
                            </div>
                            <h4 class="fw-bold text-danger mb-1 fs-5 fs-md-4">
                                Rp {{ number_format($totalPengeluaranBulanIni, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                Total pengeluaran bulan {{ now()->format('F Y') }}
                            </small>
                        </div>
                    </div>

                    <div class="card {{ $saldoBulanIni >= 0 ? 'bg-primary bg-opacity-25 border-primary' : 'bg-warning bg-opacity-25 border-warning' }}"
                        style="border-left: 4px solid {{ $saldoBulanIni >= 0 ? '#007bff' : '#ffc107' }};">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="{{ $saldoBulanIni >= 0 ? 'text-primary' : 'text-warning' }} mb-0 small fw-bold">
                                    💵 SALDO BULAN INI
                                </h6>
                                <span class="badge {{ $saldoBulanIni >= 0 ? 'bg-primary' : 'bg-warning text-dark' }}" style="font-size: 0.7rem;">
                                    {{ $saldoBulanIni >= 0 ? '▲ Untung' : '▼ Rugi' }}
                                </span>
                            </div>
                            <h4 class="fw-bold {{ $saldoBulanIni >= 0 ? 'text-primary' : 'text-warning' }} mb-1 fs-5 fs-md-4">
                                Rp {{ number_format($saldoBulanIni, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                {{ $saldoBulanIni >= 0 ? 'Surplus' : 'Defisit' }}
                                ({{ $totalPemasukanBulanIni > 0 ? number_format(($saldoBulanIni / $totalPemasukanBulanIni) * 100, 1) : 0 }}%)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pasien Meninggal Per Bulan --}}
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card text-white bg-dark h-100 shadow border-0">
                <div class="card-header border-secondary border-bottom bg-transparent">
                    <h5 class="mb-0 fw-bold text-warning fs-6 fs-md-5">Pasien Mati Per Bulan</h5>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div id="chart-pasien-mati" style="width: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Cara Bayar --}}
        <div class="col-12 col-md-12 col-lg-4">
            <div class="card text-white bg-dark h-100 shadow border-0">
                <div class="card-header border-secondary border-bottom bg-transparent">
                    <h5 class="mb-0 fw-bold text-warning fs-6 fs-md-5">
                        Cara Bayar {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                    </h5>
                </div>
                <div class="card-body p-2 p-md-3 d-flex align-items-center justify-content-center">
                    <div id="chart-cara-bayar" style="width: 100%; min-height: 260px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Pasien Rawat Inap Hari Ini --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card text-white bg-dark shadow border-0">
                <div class="card-header border-secondary border-bottom bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold text-warning fs-6 fs-md-5">
                        <i class="bi bi-hospital me-2"></i>Data Pasien Rawat Inap Hari Ini
                    </h5>
                    <span class="badge bg-warning text-dark">{{ count($rawatInapHariIniData) }} Pasien</span>
                </div>
                <div class="card-body p-2 p-md-3">
                    <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                        <table class="table table-dark table-striped table-sm table-bordered align-middle mb-0" style="min-width: 650px;">
                            <thead class="table-secondary text-dark text-center">
                                <tr>
                                    <th style="width: 12%">No. RM</th>
                                    <th style="width: 25%">Nama Pasien</th>
                                    <th style="width: 15%">Kamar</th>
                                    <th style="width: 15%">Tanggal Masuk</th>
                                    <th style="width: 20%">Nama Dokter</th>
                                    <th style="width: 13%">Cara Bayar</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.875rem;">
                                @forelse($rawatInapHariIniData as $item)
                                <tr>
                                    <td class="font-monospace">{{ $item->no_rkm_medis }}</td>
                                    <td class="text-nowrap fw-semibold">{{ $item->nm_pasien }}</td>
                                    <td>{{ $item->kamar }}</td>
                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d-m-Y') }}</td>
                                    <td>{{ $item->nm_dokter }}</td>
                                    <td><span class="badge bg-secondary">{{ $item->png_jawab }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Tidak ada data rawat inap hari ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart Rawat Jalan
        var optionsRawatJalan = {
            series: [{
                name: 'Rawat Jalan',
                type: 'column',
                data: @json($seriesColumn)
            }, {
                name: 'Jumlah',
                type: 'line',
                data: @json($seriesLine)
            }],
            chart: {
                height: 320,
                type: 'line',
                foreColor: '#e5e7eb',
                toolbar: { show: false }
            },
            colors: ['#f59e0b', '#10b981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'vertical',
                    gradientToColors: ['#fcd34d', '#34d399'],
                    shadeIntensity: 0.5,
                    opacityFrom: 0.9,
                    opacityTo: 0.7,
                    stops: [0, 100]
                }
            },
            stroke: {
                width: [0, 3]
            },
            title: {
                text: 'Tren Rawat Jalan per Bulan',
                style: { color: '#fff', fontSize: '14px' }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1],
                style: { colors: ['#ffffff'] },
                background: {
                    enabled: true,
                    foreColor: '#000',
                    borderRadius: 2,
                    padding: 3,
                    opacity: 0.8
                }
            },
            labels: @json($categories),
            xaxis: {
                labels: {
                    rotate: -45,
                    style: { colors: '#d1d5db', fontSize: '11px' }
                }
            },
            yaxis: [{
                title: { text: 'Rawat Jalan', style: { color: '#f3f4f6' } },
                labels: { style: { colors: '#e5e7eb' } }
            }, {
                opposite: true,
                title: { text: 'Tren', style: { color: '#f3f4f6' } },
                labels: { style: { colors: '#e5e7eb' } }
            }],
            tooltip: { theme: 'dark' },
            grid: { borderColor: '#374151' },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: { height: 260 },
                    yaxis: [{
                        labels: { style: { fontSize: '10px' } }
                    }, {
                        show: false // hide opposite axis on narrow screens
                    }]
                }
            }]
        };
        new ApexCharts(document.querySelector("#chart-rawatjalan"), optionsRawatJalan).render();

        // Pie Chart Cara Bayar
        var optionsCaraBayar = {
            series: @json($caraBayarValues),
            chart: {
                type: 'pie',
                width: '100%',
                foreColor: '#fff'
            },
            colors: ['#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#6366f1', '#14b8a6', '#f97316'],
            labels: @json($caraBayarLabels),
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return parseFloat(val).toFixed(1) + '%'
                },
                style: { colors: ['#ffffff'] },
                background: {
                    enabled: true,
                    foreColor: '#000',
                    borderRadius: 2,
                    padding: 3,
                    opacity: 0.85
                },
                dropShadow: { enabled: false }
            },
            legend: {
                position: 'bottom',
                fontSize: '11px',
                labels: { colors: ['#e5e7eb'] }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { width: '100%' },
                    legend: { position: 'bottom' }
                }
            }]
        };
        new ApexCharts(document.querySelector("#chart-cara-bayar"), optionsCaraBayar).render();

        // Bar Chart Kematian
        var optionsKematian = {
            series: [{
                name: 'Pasien Meninggal',
                type: 'column',
                data: @json($dataKematian)
            }, {
                name: 'Tren Kematian',
                type: 'line',
                data: @json($dataKematian)
            }],
            chart: {
                type: 'line',
                height: 300,
                foreColor: '#e5e7eb',
                toolbar: { show: false }
            },
            colors: ['#ef4444', '#f59e0b'],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'vertical',
                    gradientToColors: ['#f87171', '#facc15'],
                    shadeIntensity: 0.5,
                    opacityFrom: 0.9,
                    opacityTo: 0.7,
                    stops: [0, 100]
                }
            },
            stroke: { width: [0, 3] },
            title: {
                text: 'Pasien Meninggal per Bulan',
                align: 'center',
                style: { color: '#fef2f2', fontSize: '13px' }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0],
                style: { colors: ['#000000'] }
            },
            labels: @json($labelsKematian),
            xaxis: {
                labels: {
                    rotate: -45,
                    style: { colors: '#e5e7eb', fontSize: '11px' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: [{
                title: { text: 'Jumlah', style: { color: '#f3f4f6' } },
                labels: { style: { colors: '#e5e7eb' } }
            }],
            tooltip: { theme: 'dark' },
            grid: { borderColor: '#374151' },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: { height: 250 }
                }
            }]
        };
        new ApexCharts(document.querySelector("#chart-pasien-mati"), optionsKematian).render();
    });
</script>
@endsection
