@extends('layouts.app')

@section('title', 'Jumlah Pasien per Poli')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
        <i class="bi bi-hospital me-2"></i>Grafik Jumlah Pasien per Poliklinik
        <br>
        <small class="text-muted fs-6">
            {{ \Carbon\Carbon::parse($tgl_dari)->translatedFormat('d F Y') }} -
            {{ \Carbon\Carbon::parse($tgl_sampai)->translatedFormat('d F Y') }}
        </small>
    </h2>

    <div class="card mb-3 mb-md-4 shadow-sm bg-dark border-0">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('dashboard.poli') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label text-light small mb-1">Dari Tanggal:</label>
                        <input type="date" name="tgl_dari" value="{{ $tgl_dari }}"
                            class="form-control form-control-sm bg-dark text-light border-secondary">
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label text-light small mb-1">Sampai Tanggal:</label>
                        <input type="date" name="tgl_sampai" value="{{ $tgl_sampai }}"
                            class="form-control form-control-sm bg-dark text-light border-secondary">
                    </div>

                    <div class="col-12 col-sm-6 col-md-4">
                        <label class="form-label text-light small mb-1">Cara Bayar / Penjamin:</label>
                        <select name="kd_pj" class="form-select form-select-sm bg-dark text-light border-secondary">
                            <option value="">Semua Cara Bayar</option>
                            <option value="U01" {{ request('kd_pj') == 'U01' ? 'selected' : '' }}>Umum</option>
                            @foreach($penjabList as $pj)
                                @if($pj->kd_pj !== 'U01')
                                    <option value="{{ $pj->kd_pj }}" {{ request('kd_pj') == $pj->kd_pj ? 'selected' : '' }}>
                                        {{ $pj->png_jawab }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-2 d-grid">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm bg-dark border-0">
        <div class="card-body p-2 p-md-3">
            <div style="max-height: 550px; overflow-y: auto;">
                <div id="poliChartContainer" style="height: {{ max(count($data) * 45, 380) }}px; min-height: 380px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    let poliChart;
    const poliData = @json($data);

    function getComputedThemeColors() {
        const style = getComputedStyle(document.documentElement);
        return {
            bodyColor: style.getPropertyValue('--bs-body-color').trim() || '#e5e7eb',
            borderColor: style.getPropertyValue('--navbar-border').trim() || '#374151',
        };
    }

    function createApexChart(data) {
        const themeColors = getComputedThemeColors();

        const chartData = data.map(item => ({
            x: item.nm_poli,
            y: parseInt(item.jumlah)
        }));

        const options = {
            series: [{
                name: 'Jumlah Pasien',
                data: chartData
            }],
            chart: {
                height: Math.max(data.length * 45, 380),
                type: 'bar',
                toolbar: { show: false },
                foreColor: themeColors.bodyColor
            },
            colors: ['#FFAF00'],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    gradientToColors: ['#FFFCFB'],
                    shadeIntensity: 0.5,
                    opacityFrom: 0.9,
                    opacityTo: 0.7,
                    stops: [0, 100]
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '75%',
                    borderRadius: 3
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#ffffff'],
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
                    style: { colors: themeColors.bodyColor, fontSize: '12px' }
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
                theme: 'dark'
            },
            legend: { show: false },
            responsive: [{
                breakpoint: 576,
                options: {
                    yaxis: {
                        labels: {
                            maxWidth: 130,
                            style: { fontSize: '11px' }
                        }
                    },
                    dataLabels: {
                        style: { fontSize: '10px' }
                    }
                }
            }]
        };

        const chartElement = document.querySelector("#poliChartContainer");
        chartElement.innerHTML = '';

        if (poliChart) {
            poliChart.destroy();
        }

        poliChart = new ApexCharts(chartElement, options);
        poliChart.render();
    }

    createApexChart(poliData);

    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                createApexChart(poliData);
            }
        }
    });
    observer.observe(document.documentElement, {
        attributes: true
    });
</script>
@endsection
