@extends('layouts.app')

@section('title', 'Grafik Ralan Per Bulan')

@section('content')
@php
    $selectedPenjabName = 'Semua Pasien';
    if (($kd_pj ?? 'all') !== 'all') {
        $found = $penjabList->firstWhere('kd_pj', $kd_pj);
        $selectedPenjabName = $found ? $found->png_jawab : $kd_pj;
    }
@endphp

<div class="container-fluid py-3 px-2 px-md-3 text-white min-vh-100">
    <h2 class="mb-3 fs-4 fs-md-2 text-center text-warning fw-bold">
        <i class="bi bi-bar-chart-fill me-2"></i>Grafik Pasien Rawat Jalan
        <div class="fs-6 text-light mt-1">{{ $selectedPenjabName }}</div>
        <small class="text-muted fs-6 fw-normal d-block mt-1">
            {{ \Carbon\Carbon::parse($bulan_dari)->translatedFormat('F Y') }} -
            {{ \Carbon\Carbon::parse($bulan_sampai)->translatedFormat('F Y') }}
        </small>
    </h2>

    <div class="card mb-4 shadow-sm bg-dark border-secondary">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="bulan_dari" class="form-label small mb-1">Dari Bulan:</label>
                    <input type="month" name="bulan_dari" id="bulan_dari" value="{{ $bulan_dari }}" class="form-control form-control-sm bg-dark text-white border-secondary">
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="bulan_sampai" class="form-label small mb-1">Sampai Bulan:</label>
                    <input type="month" name="bulan_sampai" id="bulan_sampai" value="{{ $bulan_sampai }}" class="form-control form-control-sm bg-dark text-white border-secondary">
                </div>

                <div class="col-12 col-sm-12 col-lg-4">
                    <label for="kd_pj" class="form-label small mb-1">Cara Bayar / Status:</label>
                    <select name="kd_pj" id="kd_pj" class="form-select form-select-sm bg-dark text-white border-secondary">
                        <option value="all" {{ ($kd_pj ?? 'all') == 'all' ? 'selected' : '' }}>Semua Cara Bayar (Semua Pasien)</option>
                        <option value="U01" {{ ($kd_pj ?? '') == 'U01' ? 'selected' : '' }}>Umum</option>
                        @foreach($penjabList as $pj)
                            @if($pj->kd_pj !== 'U01')
                                <option value="{{ $pj->kd_pj }}" {{ ($kd_pj ?? '') == $pj->kd_pj ? 'selected' : '' }}>{{ $pj->png_jawab }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-lg-2">
                    <button type="submit" class="btn btn-warning btn-sm w-100">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if (empty($dataRalan) || count($dataRalan) === 0)

            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle-fill me-1"></i> Tidak ada data untuk rentang tanggal tersebut.
            </div>
            @else
            <div id="chart-ralan" style="height: 400px;"></div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const themeColors = getComputedStyle(document.documentElement);
        const bodyColor = themeColors.getPropertyValue('--bs-body-color')?.trim() || '#fff';
    const borderColor = themeColors.getPropertyValue('--navbar-border')?.trim() || '#ccc';

        const optionsRalan = {
            series: [{
                name: 'Ralan ({{ $selectedPenjabName }})',
                data: @json($dataRalan)
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
                categories: @json($labelsRalan),
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

        if (@json($dataRalan) && @json($dataRalan).length > 0) {
            new ApexCharts(document.querySelector("#chart-ralan"), optionsRalan).render();
        }
    });
</script>
@endsection