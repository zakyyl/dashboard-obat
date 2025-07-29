@extends('layouts.app')

@section('title', 'Grafik Ranap Per Bulan')

@section('content')
<div class="container py-4 text-white min-vh-100">
    <h2 class="mb-4 text-center">
    <i class="bi bi-bar-chart-fill me-2"></i>Grafik Pasien Rawat Inap
    <br>
    <small class="text-muted">
        {{ \Carbon\Carbon::parse($bulan_dari)->translatedFormat('F Y') }} -
        {{ \Carbon\Carbon::parse($bulan_sampai)->translatedFormat('F Y') }}
    </small>
</h2>


    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row gy-2 gx-3 align-items-end">
    <div class="col-md-auto">
        <label for="bulan_dari" class="form-label mb-0">Dari Bulan:</label>
        <input type="month" name="bulan_dari" id="bulan_dari" value="{{ $bulan_dari }}" class="form-control">
    </div>

    <div class="col-md-auto">
        <label for="bulan_sampai" class="form-label mb-0">Sampai Bulan:</label>
        <input type="month" name="bulan_sampai" id="bulan_sampai" value="{{ $bulan_sampai }}" class="form-control">
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
            @if (empty($dataRanap) || count($dataRanap) === 0)

                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle-fill me-1"></i> Tidak ada data untuk rentang tanggal tersebut.
                </div>
            @else
                <div id="chart-ranap" style="height: 400px;"></div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const themeColors = getComputedStyle(document.documentElement);
        const bodyColor = themeColors.getPropertyValue('--bs-body-color')?.trim() || '#fff';
        const borderColor = themeColors.getPropertyValue('--navbar-border')?.trim() || '#ccc';

        const optionsRanap = {
            series: [{
                name: 'Ranap',
                data: @json($dataRanap)
            }],
            chart: {
                type: 'bar',
                height: 350,
                foreColor: bodyColor,
                toolbar: { show: false }
            },
            colors: ['#8b5cf6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    gradientToColors: ['#FFC100'],
                    shadeIntensity: 0.5,
                    opacityFrom: 0.9,
                    opacityTo: 0.7,
                    stops: [0, 100]
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 3,
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
                        colors: bodyColor
                    }
                },
                axisBorder: {
                    color: borderColor
                },
                axisTicks: {
                    color: borderColor
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah',
                    style: {
                        color: bodyColor,
                        fontSize: '12px',
                        fontWeight: '500'
                    }
                },
                labels: {
                    style: { colors: bodyColor },
                },
                axisBorder: {
                    color: borderColor
                },
                axisTicks: {
                    color: borderColor
                }
            },
            grid: {
                borderColor: borderColor
            },
            tooltip: {
                theme: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light'
            }
        };

        if (@json($dataRanap) && @json($dataRanap).length > 0) {
            new ApexCharts(document.querySelector("#chart-ranap"), optionsRanap).render();
        }
    });
</script>
@endsection
