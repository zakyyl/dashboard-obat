@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid text-white py-4 min-vh-100 overflow-auto">
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark h-100 shadow">
                <div class="card-body">
                    <h3 class="text-warning fw-bold">{{ $pasienHariIni }}</h3>
                    <p class="card-text">Pasien Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark h-100 shadow">
                <div class="card-body">
                    <h3 class="text-warning fw-bold">{{ $resepHariIni }}</h3>
                    <p class="card-text">Resep Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark h-100 shadow">
                <div class="card-body">
                    <h3 class="text-warning fw-bold">0</h3> {{-- Assuming 0 or a placeholder if data is not available --}}
                    <p class="card-text">Kunjungan Hari ini</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark h-100 shadow">
                <div class="card-body">
                    <h3 class="text-warning fw-bold">0</h3> {{-- Assuming 0 or a placeholder if data is not available --}}
                    <p class="card-text">Kunjungan Bulan ini</p>
                </div>
            </div>
        </div>
    </div>

    @php
    $namaBulan =
    [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $categories = [];
    $seriesColumn = [];
    $seriesLine = [];
    foreach ($rawatJalanPerBulan as $item) {
    $categories[] = $namaBulan[$item->bulan];
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

    <div class="card text-white shadow mb-4">
        <div class="card-header border-bottom border-secondary">
            <h5 class="mb-0 fw-bold text-warning">RAWAT JALAN PERBULAN</h5>
        </div>
        <div class="card-body">
            <div id="chart-rawatjalan"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    // Chart Rawat Jalan
    var options = {
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
            height: 350,
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
            width: [0, 4]
        },
        title: {
            text: 'Rawat Jalan per Bulan',
            style: { color: '#fff' }
        },
        dataLabels: {
            enabled: true,
            enabledOnSeries: [1],
            style: {
                colors: ['#000000']
            }
        },
        labels: @json($categories),
        xaxis: {
            labels: {
                style: {
                    colors: '#d1d5db'
                }
            }
        },
        yaxis: [{
            title: {
                text: 'Rawat Jalan',
                style: {
                    color: '#f3f4f6'
                }
            },
            labels: {
                style: {
                    colors: '#e5e7eb'
                }
            }
        }, {
            opposite: true,
            title: {
                text: 'Pembanding',
                style: {
                    color: '#f3f4f6'
                }
            },
            labels: {
                style: {
                    colors: '#e5e7eb'
                }
            }
        }],
        tooltip: {
            theme: 'dark'
        },
        grid: {
            borderColor: '#374151'
        }
    };
    new ApexCharts(document.querySelector("#chart-rawatjalan"), options).render();

    // Pie Chart Cara Bayar
    var optionsCaraBayar = {
        series: @json($caraBayarValues),
        chart: {
            type: 'pie',
            width: '100%',
            foreColor: '#fff'
        },
        colors: ['#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#6366f1'],
        labels: @json($caraBayarLabels),
        dataLabels: { // Added dataLabels configuration for the pie chart
            enabled: true,
            formatter: function (val) {
                return parseFloat(val).toFixed(1) + '%' // Format to one decimal place (e.g., 88.5%)
            },
            style: {
                colors: ['#000000'] // Text color of the data labels (black)
            },
            background: { // Background for the data labels
                enabled: true,
                foreColor: '#fff', // White background color
                borderRadius: 2,
                padding: 4,
                opacity: 0.9,
                borderWidth: 0, // No border
            },
            dropShadow: {
                enabled: false // No visible shadow as in the image
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 250 },
                legend: { position: 'bottom' }
            }
        }],
        legend: {
            position: 'bottom',
            labels: { colors: ['#e5e7eb'] }
        }
    };
    new ApexCharts(document.querySelector("#chart-cara-bayar"), optionsCaraBayar).render();

    // Bar Chart Kematian
    var optionsKematian = {
        series: [{
            name: 'Jumlah Pasien Meninggal',
            type: 'column',
            data: @json($dataKematian)
        }, {
            name: 'Jumlah Kematian',
            type: 'line',
            data: @json($dataKematian)
        }],
        chart: {
            type: 'line',
            height: 350,
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
        stroke: {
            width: [0, 3]
        },
        title: {
            text: 'Pasien Meninggal per Bulan',
            align: 'center',
            style: {
                color: '#fef2f2'
            }
        },
        dataLabels: {
            enabled: true,
            enabledOnSeries: [0],
            style: {
                colors: ['#000000']
            }
        },
        labels: @json($labelsKematian),
        xaxis: {
            labels: { style: { colors: '#e5e7eb' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: [{
            title: {
                text: 'Jumlah Pasien',
                style: {
                    color: '#f3f4f6'
                }
            },
            labels: {
                style: { colors: '#e5e7eb' }
            }
        }],
        tooltip: {
            theme: 'dark'
        },
        grid: {
            borderColor: '#374151'
        }
    };
    new ApexCharts(document.querySelector("#chart-pasien-mati"), optionsKematian).render();
});
    </script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white h-100 shadow">
                    <div class="card-header border-secondary border-bottom">
                        <h5 class="mb-0 fw-bold text-warning">Kunjungan Pasien</h5>
                    </div>
                    <div class="card-body">
                        {{-- The chart for "Kunjungan Pasien Per Bulan (Ranap)" was removed as requested. --}}
                        <div id="chart-ranap" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white h-100 shadow">
                    <div class="card-header border-secondary border-bottom">
                        <h5 class="mb-0 fw-bold text-warning">Pasien Mati Per Bulan</h5>
                    </div>
                    <div class="card-body">
                        <div id="chart-pasien-mati" style="height: 350px; width: 100%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card text-white h-100 shadow">
                    <div class="card-header border-secondary border-bottom">
                        <h5 class="mb-0 fw-bold text-warning">Cara Bayar {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="chart-cara-bayar" style="height: 250px; max-width: 100%; overflow: true;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
