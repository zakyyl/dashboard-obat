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
    $dataSeries = [];

    foreach ($rawatJalanPerBulan as $item) {
    $categories[] = $namaBulan[$item->bulan];
    $dataSeries[] = $item->jumlah;
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
                data: @json($dataSeries)
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: @json($categories),
                labels: {
                    style: {
                        colors: '#fff'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#fff'
                    }
                }
            },
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
                            <p class="fw-bold">cara bayar</p>
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