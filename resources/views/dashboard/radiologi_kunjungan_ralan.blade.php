@extends('layouts.app')

@section('title', 'Grafik Kunjungan Radiologi Rawat Jalan')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">
        <i class="bi bi-bar-chart-line-fill me-2"></i>Grafik Kunjungan Radiologi Rawat Jalan
    </h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-auto">
                    <label for="tgl_dari" class="form-label mb-0">Dari Tanggal:</label>
                    <input type="date" name="tgl_dari" id="tgl_dari" value="{{ $tgl_dari }}" class="form-control">
                </div>
                <div class="col-md-auto">
                    <label for="tgl_sampai" class="form-label mb-0">Sampai Tanggal:</label>
                    <input type="date" name="tgl_sampai" id="tgl_sampai" value="{{ $tgl_sampai }}" class="form-control">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($data->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle-fill me-1"></i> Tidak ada data kunjungan untuk rentang tanggal tersebut.
                </div>
            @else
                <div style="max-height: 500px; overflow-y: auto;">
                    <div id="radiologiChartContainer" style="min-height: 400px;"></div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
let radiologiChart;
const radiologiData = @json($data);

function getComputedThemeColors() {
    const style = getComputedStyle(document.documentElement);
    return {
        bodyColor: style.getPropertyValue('--bs-body-color').trim(),
        borderColor: style.getPropertyValue('--navbar-border')?.trim() || '#dee2e6'
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
            height: 400,
            toolbar: { show: false },
            foreColor: themeColors.bodyColor
        },
        colors: ['#FB8B24'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'vertical',
                gradientToColors: ['#CE5A67'],
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
                colors: ['#fefce8'],
                fontSize: '12px'
            }
        },
        xaxis: {
            categories: chartData.map(item => item.x),
            labels: {
                rotate: -45,
                style: { colors: themeColors.bodyColor, fontSize: '12px' }
            },
            axisBorder: { color: themeColors.borderColor },
            axisTicks: { color: themeColors.borderColor }
        },
        yaxis: {
            title: {
                text: 'Jumlah Kunjungan',
                style: { color: themeColors.bodyColor, fontSize: '12px' }
            },
            labels: { style: { colors: themeColors.bodyColor } },
            axisBorder: { color: themeColors.borderColor },
            axisTicks: { color: themeColors.borderColor }
        },
        grid: { borderColor: themeColors.borderColor },
        tooltip: {
            y: { formatter: val => `${val} kunjungan` },
            theme: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light'
        },
        legend: { show: false }
    };

    const chartElement = document.querySelector("#radiologiChartContainer");
    if (radiologiChart) radiologiChart.destroy();
    radiologiChart = new ApexCharts(chartElement, options);
    radiologiChart.render();
}

if (radiologiData.length > 0) {
    createApexChart(radiologiData);
}

const observer = new MutationObserver(() => {
    createApexChart(radiologiData);
});
observer.observe(document.documentElement, { attributes: true });
</script>
@endsection
