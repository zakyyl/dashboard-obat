@extends('layouts.app')

@section('title', 'Jumlah Pasien per Dokter')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">Grafik Jumlah Pasien per Dokter</h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-auto">
                    <label for="tgl_dari" class="form-label mb-0">Dari Tanggal:</label>
                    <input type="date" name="tgl_dari" value="{{ $tgl_dari }}" class="form-control">
                </div>

                <div class="col-md-auto">
                    <label for="tgl_sampai" class="form-label mb-0">Sampai Tanggal:</label>
                    <input type="date" name="tgl_sampai" value="{{ $tgl_sampai }}" class="form-control">
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
            <div style="max-height: 500px; overflow-y: auto;">
                <div id="dokterChartContainer" style="height: {{ count($data) * 50 }}px; min-height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
let dokterChart;
const dokterData = @json($data);

function getComputedThemeColors() {
    const style = getComputedStyle(document.documentElement);
    return {
        bodyColor: style.getPropertyValue('--bs-body-color').trim(),
        borderColor: style.getPropertyValue('--navbar-border').trim(),
    };
}

function createApexChart(data) {
    const themeColors = getComputedThemeColors();

    const chartData = data.map(item => ({
        x: item.nm_dokter,
        y: parseInt(item.jumlah)
    }));

    const options = {
        series: [{
            name: 'Jumlah Pasien',
            data: chartData
        }],
        chart: {
            height: 50 * data.length,
            type: 'bar',
            toolbar: { show: false },
            foreColor: themeColors.bodyColor
        },
        colors: ['#FFFF80'], // warna dasar
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                gradientToColors: ['#FF6500'], // gradasi terang
                shadeIntensity: 0.5,
                opacityFrom: 0.9,
                opacityTo: 0.7,
                stops: [0, 100]
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '80%',
                borderRadius: 3
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                colors: ['#ffffff'], // label putih
                fontSize: '12px',
                fontWeight: 'bold'
            }
        },
        xaxis: {
            title: {
                text: 'Jumlah Pasien',
                style: { color: themeColors.bodyColor }
            },
            labels: { style: { colors: themeColors.bodyColor } },
            axisBorder: { color: themeColors.borderColor },
            axisTicks: { color: themeColors.borderColor }
        },
        yaxis: {
            labels: {
                style: { colors: themeColors.bodyColor, fontSize: '13px' }
            },
            axisBorder: { color: themeColors.borderColor },
            axisTicks: { color: themeColors.borderColor }
        },
        grid: {
            borderColor: themeColors.borderColor
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: { formatter: val => `${val} pasien` },
            theme: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light'
        },
        legend: { show: false }
    };

    const chartElement = document.querySelector("#dokterChartContainer");
    chartElement.innerHTML = '';

    if (dokterChart) dokterChart.destroy();

    dokterChart = new ApexCharts(chartElement, options);
    dokterChart.render();
}

createApexChart(dokterData);

const observer = new MutationObserver(() => {
    createApexChart(dokterData);
});
observer.observe(document.documentElement, { attributes: true });
</script>
@endsection
