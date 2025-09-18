@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 text-center">
        <i class="bi bi-bar-chart-fill me-2"></i>Grafik Pengajuan Klaim Rawat Inap
        <br>
        <small class="text-muted">{{ \Carbon\Carbon::parse($startDate)->translatedFormat('F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('F Y') }}</small>
    </h2>

    <!-- Form Filter Tahun -->
    <div class="mb-3 text-center">
        <form method="GET" action="{{ url()->current() }}" class="d-inline-flex align-items-center gap-2">
            <label for="tahun" class="form-label mb-0">Tahun:</label>
            <select name="tahun" id="tahun" class="form-select" style="width: auto;" onchange="this.form.submit()">
                @for($i = date('Y'); $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Chart -->
    <div id="chartRanap" style="height: 350px;"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const options = {
        series: [
            {
                name: 'Pengajuan',
                type: 'column',
                data: @json($seriesColumn)
            },
            {
                name: 'Jumlah',
                type: 'line',
                data: @json($seriesColumn) // Gunakan data yang sama, tapi tampil dalam bentuk garis
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            foreColor: '#e5e7eb',
            toolbar: { show: false }
        },
        colors: ['#f59e0b', '#3b82f6'], // Bar: kuning, Line: biru
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
            width: [0, 3], // Bar tanpa garis tepi, line dengan ketebalan 3px
            curve: 'smooth'
        },
        
        dataLabels: {
    enabled: true,
    enabledOnSeries: [0], // Hanya tampilkan data label di bar (index 0)
    style: {
        colors: ['#000']
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
                text: 'Jumlah Pengajuan',
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
            shared: true,
            intersect: false,
            theme: 'dark'
        },
        grid: {
            borderColor: '#374151'
        },
        legend: {
            labels: {
                colors: '#f3f4f6'
            }
        }
    };

    new ApexCharts(document.querySelector("#chartRanap"), options).render();
</script>
@endsection
