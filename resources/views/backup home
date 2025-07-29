@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid bg-dark text-white py-4 min-vh-100 overflow-auto">
    <!-- Statistik -->
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold">{{ $pasienHariIni }}</h3>
            <p>Pasien Hari Ini</p>
        </div>
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold">{{ $resepHariIni }}</h3>
            <p>Resep Hari Ini</p>
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

    $labelsRanap = [];
    $dataRanap = [];
    foreach ($ranapPerBulan as $item) {
    $labelsRanap[] = \Carbon\Carbon::parse($item->bulan . '-01')->translatedFormat('F Y');
    $dataRanap[] = $item->jumlah;
    }

    $labelsKematian = [];
    $dataKematian = [];
    foreach ($kematianPerBulan as $item) {
    $labelsKematian[] = $item->bulan;
    $dataKematian[] = $item->jumlah;
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
                    foreColor: '#fff',
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

            // Pie Chart Cara Bayar
            var optionsCaraBayar = {
                series: @json($caraBayarValues),
                chart: {
                    type: 'pie',
                    width: 380,
                    foreColor: '#fff'
                },
                labels: @json($caraBayarLabels),
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: { width: 250 },
                        legend: { position: 'bottom' }
                    }
                }],
                legend: {
                    labels: { colors: ['#fff'] }
                }
            };
            var chartCaraBayar = new ApexCharts(document.querySelector("#chart-cara-bayar"), optionsCaraBayar);
            chartCaraBayar.render();

            // Bar Chart Ranap - Horizontal
            var optionsRanap = {
                series: [{
                    name: 'Ranap',
                    data: @json($dataRanap)
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    foreColor: '#fff',
                    toolbar: { show: false } // opsional, hilangkan menu
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        borderRadiusApplication: 'end',
                        horizontal: true
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: ['#fff'], // warna teks di dalam bar
                        fontSize: '12px'
                    },
                    formatter: function (val) {
                        return val; // menampilkan angka value-nya langsung
                    }
                },
                xaxis: {
                    categories: @json($labelsRanap),
                    labels: {
                        style: {
                            colors: '#fff'
                        }
                    }
                },
                grid: {
                    borderColor: '#444'
                },
                tooltip: {
                    theme: 'dark' // ✅ ini dia tambahan tooltip-nya
                }
            };
            var chartRanap = new ApexCharts(document.querySelector("#chart-ranap"), optionsRanap);
            chartRanap.render();

            // Bar Chart Kematian
            var options = {
            series: [{
                name: 'Jumlah Pasien Meninggal',
                type: 'column',
                data: @json($dataKematian) // data jumlah pasien per bulan
            }, {
                name: 'Nilai Kematian',
                type: 'line',
                data: @json($dataKematian) // sama, hanya untuk garis
            }],
            chart: {
                type: 'line',
                height: 350,
                width: '100%',
                toolbar: { show: false },
                zoom: { enabled: false },
                foreColor: '#ffffff' // putih di dark mode
            },
            stroke: {
                width: [0, 3]
            },
            title: {
                text: 'Pasien Meninggal per Bulan',
                align: 'center',
                style: {
                    color: '#fff'
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [1], // tampilkan angka di bar & garis
                style: {
                    colors: ['#000']
                },
                dropShadow: {
                    enabled: false
                }
            },
            labels: @json($labelsKematian), // nama bulan
            xaxis: {
                labels: { style: { colors: '#fff' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: [{
                title: {
                    text: 'Jumlah Pasien',
                    style: {
                        color: '#fff'
                    }
                },
                labels: {
                    style: { colors: '#fff' }
                }
            }],
            tooltip: {
                theme: 'dark'
            },
            grid: {
                borderColor: '#444'
            }
        };
        var chart = new ApexCharts(document.querySelector("#chart-pasien-mati"), options);
        chart.render();
        });
    </script>

    <!-- Grafik Cara Bayar -->
    <div class="container-fluid">
        <div class="row">
            <!-- Card 1 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card bg-dark text-white h-100 shadow">
                    <div class="card-header border-secondary border-bottom">
                        <h5 class="mb-0 fw-bold text-primary">Kunjungan Pasien Per Bulan (Ranap)</h5>
                    </div>
                    <div class="card-body">
                        <div id="chart-ranap" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <!-- Card Pasien Mati Per Bulan -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card bg-dark text-white h-100 shadow">
                    <div class="card-header border-secondary border-bottom">
                        <h5 class="mb-0 fw-bold text-danger">Pasien Mati Per Bulan</h5>
                    </div>
                    <div class="card-body">
                        <div id="chart-pasien-mati" style="height: 350px; width: 100%;"></div>
                    </div>
                </div>
            </div>


            <!-- Card 3 - Cara Bayar Perbulan-->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card bg-dark text-white h-100 shadow">
                    <div class="card-header border-secondary border-bottom">
                        <h5 class="mb-0 fw-bold text-warning">Cara Bayar Per Bulan</h5>
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
