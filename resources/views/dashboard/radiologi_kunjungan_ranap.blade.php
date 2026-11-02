@extends('layouts.app')

@section('title', 'Grafik Kunjungan Radiologi Rawat Inap')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
        <i class="bi bi-radioactive me-2"></i>Grafik Kunjungan Radiologi Rawat Inap
        <br>
        <small class="text-muted fs-6">
            {{ \Carbon\Carbon::parse($tgl_dari)->translatedFormat('d F Y') }} -
            {{ \Carbon\Carbon::parse($tgl_sampai)->translatedFormat('d F Y') }}
        </small>
    </h2>

    <div class="card mb-3 mb-md-4 shadow-sm bg-dark border-0">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('radiologi.kunjungan.ranap') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-5 col-md-4">
                        <label for="tgl_dari" class="form-label text-light small mb-1">Dari Tanggal:</label>
                        <input type="date" name="tgl_dari" id="tgl_dari" value="{{ $tgl_dari }}"
                            class="form-control form-control-sm bg-dark text-light border-secondary">
                    </div>
                    <div class="col-12 col-sm-5 col-md-4">
                        <label for="tgl_sampai" class="form-label text-light small mb-1">Sampai Tanggal:</label>
                        <input type="date" name="tgl_sampai" id="tgl_sampai" value="{{ $tgl_sampai }}"
                            class="form-control form-control-sm bg-dark text-light border-secondary">
                    </div>
                    <div class="col-12 col-sm-2 col-md-4 d-grid">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm bg-dark border-0">
        <div class="card-body p-2 p-md-3">
            @if ($data->isEmpty())
                <div class="alert alert-warning text-center my-3 bg-dark border-secondary text-warning">
                    <i class="bi bi-info-circle-fill me-1"></i> Tidak ada data kunjungan untuk rentang tanggal tersebut.
                </div>
            @else
                <div style="max-height: 550px; overflow-y: auto;">
                    <div id="radiologiChartContainer" style="min-height: 380px; width: 100%;"></div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
let radiologiChart;
const radiologiData = @json($data);

function getComputedThemeColors() {
    const style = getComputedStyle(document.documentElement);
    return {
        bodyColor: style.getPropertyValue('--bs-body-color').trim() || '#e5e7eb',
        borderColor: style.getPropertyValue('--navbar-border')?.trim() || '#374151'
    };
}

function createApexChart(data) {
    const themeColors = getComputedThemeColors();
    const chartData = data.map(item => ({
        x: item.tgl,
        y: parseInt(item.jumlah)
    }));

    const options = {
        series: [{
            name: 'Jumlah Kunjungan',
            data: chartData.map(item => item.y)
        }],
        chart: {
            type: 'bar',
            height: 380,
            toolbar: { show: false },
            foreColor: themeColors.bodyColor
        },
        colors: ['#8b5cf6'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'vertical',
                gradientToColors: ['#a78bfa'],
                shadeIntensity: 0.5,
                opacityFrom: 0.9,
                opacityTo: 0.7,
                stops: [0, 100]
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '50%',
                borderRadius: 3
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                colors: ['#ffffff'],
                fontSize: '11px'
            }
        },
        xaxis: {
            categories: chartData.map(item => item.x),
            labels: {
                rotate: -45,
                style: { colors: themeColors.bodyColor, fontSize: '11px' }
            },
            axisBorder: { color: themeColors.borderColor },
            axisTicks: { color: themeColors.borderColor }
        },
        yaxis: {
            title: {
                text: 'Jumlah Kunjungan',
                style: { color: themeColors.bodyColor, fontSize: '11px' }
            },
            labels: { style: { colors: themeColors.bodyColor } },
            axisBorder: { color: themeColors.borderColor },
            axisTicks: { color: themeColors.borderColor }
        },
        grid: { borderColor: themeColors.borderColor },
        tooltip: {
            y: { formatter: val => `${val} kunjungan` },
            theme: 'dark'
        },
        legend: { show: false },
        responsive: [{
            breakpoint: 576,
            options: {
                chart: { height: 300 },
                plotOptions: { bar: { columnWidth: '70%' } },
                xaxis: { labels: { rotate: -45, style: { fontSize: '10px' } } }
            }
        }]
    };

    const chartElement = document.querySelector("#radiologiChartContainer");
    if (chartElement) {
        if (radiologiChart) radiologiChart.destroy();
        radiologiChart = new ApexCharts(chartElement, options);
        radiologiChart.render();
    }
}

if (radiologiData.length > 0) {
    createApexChart(radiologiData);
}

const observer = new MutationObserver(() => {
    if (radiologiData.length > 0) createApexChart(radiologiData);
});
observer.observe(document.documentElement, { attributes: true });
</script>
@endsection
