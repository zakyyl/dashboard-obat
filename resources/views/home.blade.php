@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid bg-dark text-white py-4 min-vh-100">
    <!-- Statistik -->
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold">{{ $pasienHariIni }}</h3>
            <p>Pasien Hari ini</p>
        </div>
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold"></h3>
            <p>Pasien Baru Hari ini</p>
        </div>
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold"></h3>
            <p>Kunjungan Hari ini</p>
        </div>
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold"></h3>
            <p>Kunjungan Bulan ini</p>
        </div>
    </div>

    @php
    $namaBulan =
    [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $categories = [];
    $seriesColumn = [];
    $seriesLine = []; // Dummy

    foreach ($rawatJalanPerBulan as $item) {
    $categories[] = $namaBulan[$item->bulan];
    $seriesColumn[] = $item->jumlah;
    $seriesLine[] = $item->jumlah; // Dummy data untuk line (bisa diganti nanti)
    }
    @endphp


    <!-- Card Grafik RAWAT JALAN PERBULAN -->
    <div class="card bg-dark text-white shadow mb-4">
        <div class="card-header border-bottom border-secondary">
            <h5 class="mb-0 fw-bold text-warning">RAWAT JALAN PERBULAN</h5>
        </div>
        <div class="card-body">
            <div id="chart-rawatjalan"></div>
        </div>
    </div>

    <!-- Script ApexCharts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{
                name: 'Rawat Jalan',
                type: 'column',
                data: @json($seriesColumn)
            }, {
                name: 'Pembanding',
                type: 'line',
                data: @json($seriesLine)
            }],
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: false }
            },
            stroke: {
                width: [0, 4]
            },
            title: {
                text: 'Rawat Jalan per Bulan',
                style: {
                    color: '#fff'
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1]
            },
            labels: @json($categories),
            xaxis: {
                labels: {
                    style: {
                        colors: '#fff'
                    }
                }
            },
            yaxis: [{
                title: {
                    text: 'Rawat Jalan',
                    style: {
                        color: '#fff'
                    }
                },
                labels: {
                    style: {
                        colors: '#fff'
                    }
                }
            }, {
                opposite: true,
                title: {
                    text: 'Pembanding',
                    style: {
                        color: '#fff'
                    }
                },
                labels: {
                    style: {
                        colors: '#fff'
                    }
                }
            }],
            tooltip: {
                theme: 'dark'
            },
            grid: {
                borderColor: '#444'
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart-rawatjalan"), options);
        chart.render();
    });
    </script>



    <div class="card bg-dark text-white shadow mb-4">
        <div class="card-header border-bottom border-secondary">
            <h5 class="mb-0 fw-bold text-warning">Graphic</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-sm-6 col-md-3">
                    <div class="card bg-secondary text-white text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-calendar-month fs-2 mb-2"></i>
                            <h6></h6>
                            <p class="fw-bold">BELUM TAU</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card bg-secondary text-white text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-calendar-month fs-2 mb-2"></i>
                            <h6></h6>
                            <p class="fw-bold">BELUM TAU</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card bg-secondary text-white text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-calendar-month fs-2 mb-2"></i>
                            <h6></h6>
                            <p class="fw-bold">cara bayar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
