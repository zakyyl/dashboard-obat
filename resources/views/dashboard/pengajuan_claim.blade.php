@extends('layouts.app')

@section('title', 'Grafik Pengajuan Klaim')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">
            <i class="bi bi-bar-chart-fill me-2"></i>Grafik Pengajuan Klaim
            <br>
            <small class="text-muted">
                {{ \Carbon\Carbon::parse($startDate)->format('M Y') }} -
                {{ \Carbon\Carbon::parse($endDate)->format('M Y') }}
            </small>
        </h2>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row gy-2 gx-3 align-items-end">
                    <div class="col-md-auto">
                        <label for="start_month" class="form-label mb-0">Dari Bulan:</label>
                        <input type="month" id="start_month" name="start_month" class="form-control"
                            value="{{ request('start_month', \Carbon\Carbon::parse($startDate)->format('Y-m')) }}">
                    </div>
                    <div class="col-md-auto">
                        <label for="end_month" class="form-label mb-0">Sampai Bulan:</label>
                        <input type="month" id="end_month" name="end_month" class="form-control"
                            value="{{ request('end_month', \Carbon\Carbon::parse($endDate)->format('Y-m')) }}">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-search me-1"></i> Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                @if ($pengajuanRawatJalan == 0 && $pengajuanRawatInap == 0)
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle-fill me-1"></i> Tidak ada data pengajuan klaim pada rentang waktu
                        tersebut.
                    </div>
                @else
                    <div id="pengajuanChartContainer" style="min-height: 400px;"></div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let chart = null;

            function getComputedThemeColors() {
                const style = getComputedStyle(document.documentElement);
                return {
                    bodyColor: style.getPropertyValue('--bs-body-color').trim(),
                    borderColor: style.getPropertyValue('--navbar-border')?.trim() || '#dee2e6'
                };
            }

            function getChartOptions() {
                const themeColors = getComputedThemeColors();

                return {
                    series: [{
                        name: 'Jumlah Pengajuan',
                        data: [{{ $pengajuanRawatJalan }}, {{ $pengajuanRawatInap }}]
                    }],
                    chart: {
                        type: 'bar',
                        height: 400,
                        toolbar: {
                            show: false
                        },
                        background: 'transparent',
                        foreColor: themeColors.bodyColor
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '45%',
                            distributed: true,
                            borderRadius: 1,
                            borderRadiusApplication: 'end',
                            borderRadiusWhenStacked: 'last'
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '16px',
                            fontWeight: '500',
                            colors: [themeColors.bodyColor] 
                        },
                        formatter: function(val) {
                            return `${val} data`;
                        },
                        offsetY: -10
                    },

                    xaxis: {
                        categories: ['Rawat Jalan', 'Rawat Inap'],
                        labels: {
                            style: {
                                colors: themeColors.bodyColor
                            }
                        },
                        axisBorder: {
                            color: themeColors.borderColor
                        },
                        axisTicks: {
                            color: themeColors.borderColor
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah Pengajuan',
                            style: {
                                color: themeColors.bodyColor,
                                fontSize: '15px',   
                                fontWeight: '600',
                            },
                        },
                        labels: {
                            style: {
                                colors: themeColors.bodyColor
                            }
                        },
                        axisBorder: {
                            color: themeColors.borderColor
                        },
                        axisTicks: {
                            color: themeColors.borderColor
                        }
                    },

                    fill: {
                        opacity: 1,
                        colors: ['#F3C623', '#FA812F']
                    },
                    stroke: {
                        show: true,
                        width: 1,
                        colors: ['#fff']
                    },
                    tooltip: {
                        y: {
                            formatter: val => `${val} data`
                        },
                        theme: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light'
                    },
                    grid: {
                        borderColor: themeColors.borderColor,
                        xaxis: {
                            lines: {
                                show: false
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    legend: {
                        show: false
                    }
                };
            }

            function renderChart() {
                if (chart) {
                    chart.destroy();
                }
                chart = new ApexCharts(document.querySelector("#pengajuanChartContainer"), getChartOptions());
                chart.render();
            }

            renderChart();

            const observer = new MutationObserver((mutationsList) => {
                for (const mutation of mutationsList) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                        renderChart();
                    }
                }
            });

            observer.observe(document.documentElement, {
                attributes: true
            });
        });
    </script>
@endsection
