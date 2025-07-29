@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid text-white py-4 min-vh-100 overflow-auto">
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
    <div class="card  text-white shadow mb-4">
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
    // Chart Rawat Jalan
    var options = {
        series: [{
            name: 'Rawat Jalan',
            type: 'column',
            data: @json($seriesColumn)
        }, {
            name: 'Nilai',
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
        colors: ['#000000'] // warna hitam
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
            width: 380,
            foreColor: '#fff'
        },
        colors: ['#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#6366f1'],
        labels: @json($caraBayarLabels),
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 250 },
                legend: { position: 'bottom' }
            }
        }],
        legend: {
            labels: { colors: ['#e5e7eb'] }
        }
    };
    new ApexCharts(document.querySelector("#chart-cara-bayar"), optionsCaraBayar).render();

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
            toolbar: { show: false }
        },
        colors: ['#8b5cf6'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                gradientToColors: ['#a78bfa'],
                shadeIntensity: 0.5,
                opacityFrom: 0.9,
                opacityTo: 0.7,
                stops: [0, 100]
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                colors: ['#fefce8'],
                fontSize: '12px'
            }
        },
        xaxis: {
            categories: @json($labelsRanap),
            labels: {
                style: {
                    colors: '#e5e7eb'
                }
            }
        },
        grid: {
            borderColor: '#444'
        },
        tooltip: {
            theme: 'dark'
        }
    };
    new ApexCharts(document.querySelector("#chart-ranap"), optionsRanap).render();

    // Bar Chart Kematian
    var optionsKematian = {
        series: [{
            name: 'Jumlah Pasien Meninggal',
            type: 'column',
            data: @json($dataKematian)
        }, {
            name: 'Nilai Kematian',
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
            enabledOnSeries: [1],
            style: {
                colors: ['#fefce8']
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
            <!-- Card 1 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card  text-white h-100 shadow">
                    <div class="card-header border-secondary border-bottom">
                        <h5 class="mb-0 fw-bold text-warning">Kunjungan Pasien Per Bulan (Ranap)</h5>
                    </div>
                    <div class="card-body">
                        <div id="chart-ranap" style="height: 350px;"></div>
                    </div>
                </div>
            </div>

            <!-- Card Pasien Mati Per Bulan -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card  text-white h-100 shadow">
                    <div class="card-header border-secondary border-bottom">
                        <h5 class="mb-0 fw-bold text-warning">Pasien Mati Per Bulan</h5>
                    </div>
                    <div class="card-body">
                        <div id="chart-pasien-mati" style="height: 350px; width: 100%;"></div>
                    </div>
                </div>
            </div>


            <!-- Card 3 - Cara Bayar Perbulan-->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card  text-white h-100 shadow">
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
