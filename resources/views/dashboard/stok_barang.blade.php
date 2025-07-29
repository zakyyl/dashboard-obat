@extends('layouts.app')

@section('title', 'Stok Obat Minimal')

@section('content')
<div class="container py-4 text-white min-vh-100">
    <h2 class="mb-4 text-center">
        Grafik Stok Obat Minimal & Saat Ini
        <br>
        <small class="text-muted">
            {{ now()->translatedFormat('F Y') }}
        </small>
    </h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('obat.stok-barang') }}" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-auto">
                    <label for="jenis" class="form-label mb-0">Filter Jenis Barang:</label>
                    <select name="jenis" id="jenis" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach ($listJenis as $j)
                        <option value="{{ $j->nama }}" {{ $jenisFilter==$j->nama ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-auto">
                    <label for="search" class="form-label mb-0">Cari Nama Obat:</label>
                    <input type="text" id="search" name="search" class="form-control"
                        placeholder="Ketik nama obat..." value="{{ request('search') }}">
                </div>

                <div class="col-md-auto">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @php
            $chartHeight = min($data->count() * 45, 1200);
            @endphp

            @if ($data->count() === 0)
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle-fill me-1"></i> Tidak ada data obat.
                </div>
            @else
                <div style="max-height: 500px; overflow-y: auto;">
                    <div id="stokChartContainer" style="height: {{ $chartHeight }}px; min-height: 400px;"></div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let stokApexChart;

    function getThemeColors() {
        const style = getComputedStyle(document.documentElement);
        return {
            bodyColor: style.getPropertyValue('--bs-body-color').trim() || '#fff',
            borderColor: style.getPropertyValue('--navbar-border').trim() || '#ccc'
        };
    }

    function createApexChart(data) {
        const themeColors = getThemeColors();
        const dataArray = Array.isArray(data) ? data : Object.values(data || []);

        const kategoriObat = dataArray.map(item => item?.nama_brng ?? '-');
        const stokMinimal = dataArray.map(item => item?.stokminimal ?? 0);
        const totalStok = dataArray.map(item => item?.total_stok ?? 0);

        const options = {
            series: [
                { name: 'Stok Minimal', data: stokMinimal },
                { name: 'Total Stok', data: totalStok }
            ],
            chart: {
                type: 'bar',
                height: 930,
                toolbar: { show: false },
                foreColor: themeColors.bodyColor
            },
            colors: ['#093FB4', '#ED3500'], // Warna dari grafik Ralan
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    gradientToColors: ['#FFFCFB', '#FFD8D8'],
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
                    colors: ['#fefce8'],
                    fontSize: '12px'
                }
            },
            xaxis: {
                categories: kategoriObat,
                labels: { style: { colors: themeColors.bodyColor } },
                axisBorder: { color: themeColors.borderColor },
                axisTicks: { color: themeColors.borderColor }
            },
            yaxis: {
                labels: { style: { colors: themeColors.bodyColor } },
                axisBorder: { color: themeColors.borderColor },
                axisTicks: { color: themeColors.borderColor }
            },
            grid: {
                borderColor: themeColors.borderColor
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: { formatter: val => val + ' unit' },
                theme: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light'
            }
        };

        const chartElement = document.querySelector("#stokChartContainer");
        if (stokApexChart) stokApexChart.destroy();
        stokApexChart = new ApexCharts(chartElement, options);
        stokApexChart.render();
    }

    createApexChart(@json($data->values()));

    // Listener untuk dark mode
    const observer = new MutationObserver(() => {
        createApexChart(@json($data->values()));
    });
    observer.observe(document.documentElement, { attributes: true });
});
</script>
@endsection
