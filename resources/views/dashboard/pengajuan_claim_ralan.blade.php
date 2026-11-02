@extends('layouts.app')

@section('title', 'Pengajuan Klaim Rawat Jalan')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
        <i class="bi bi-file-earmark-medical me-2"></i>Grafik Pengajuan Klaim Rawat Jalan
        <br>
        <small class="text-muted fs-6">
            {{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('F Y') }}
        </small>
    </h2>

    <div class="card mb-3 mb-md-4 shadow-sm bg-dark border-0">
        <div class="card-body p-3">
            <form method="GET" action="{{ url()->current() }}" class="row g-2 justify-content-center align-items-center">
                <div class="col-auto">
                    <label for="tahun" class="form-label text-light small mb-0 fw-semibold">Pilih Tahun:</label>
                </div>
                <div class="col-auto">
                    <select name="tahun" id="tahun" class="form-select form-select-sm bg-dark text-light border-secondary" onchange="this.form.submit()">
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm bg-dark border-0">
        <div class="card-body p-2 p-md-3">
            <div id="chartRalan" style="min-height: 350px; width: 100%;"></div>
        </div>
    </div>
</div>

<script>
    const options = {
        series: [
            {
                name: 'Pengajuan',
                type: 'column',
                data: @json($seriesColumn)
            },
            {
                name: 'Tren',
                type: 'line',
                data: @json($seriesColumn)
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            foreColor: '#e5e7eb',
            toolbar: { show: false }
        },
        colors: ['#f59e0b', '#3b82f6'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'vertical',
                gradientToColors: ['#fcd34d', '#60a5fa'],
                shadeIntensity: 0.5,
                opacityFrom: 0.9,
                opacityTo: 0.7,
                stops: [0, 100]
            }
        },
        stroke: {
            width: [0, 3],
            curve: 'smooth'
        },
        dataLabels: {
            enabled: true,
            enabledOnSeries: [0],
            style: {
                colors: ['#000000'],
                fontSize: '11px'
            }
        },
        labels: @json($categories),
        xaxis: {
            labels: {
                rotate: -45,
                style: {
                    colors: '#d1d5db',
                    fontSize: '11px'
                }
            }
        },
        yaxis: [{
            title: {
                text: 'Jumlah Pengajuan',
                style: { color: '#f3f4f6', fontSize: '11px' }
            },
            labels: {
                style: { colors: '#e5e7eb' }
            }
        }],
        tooltip: {
            shared: true,
            intersect: false,
            theme: 'dark'
        },
        grid: {
            borderColor: '#374151'
        },
        legend: {
            position: 'bottom',
            labels: {
                colors: '#f3f4f6'
            }
        },
        responsive: [{
            breakpoint: 576,
            options: {
                chart: { height: 280 },
                xaxis: { labels: { rotate: -45, style: { fontSize: '10px' } } },
                dataLabels: { style: { fontSize: '9px' } }
            }
        }]
    };

    new ApexCharts(document.querySelector("#chartRalan"), options).render();
</script>
@endsection
