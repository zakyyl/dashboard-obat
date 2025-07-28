@extends('layouts.app')

@section('title', 'Grafik Kunjungan Lab Rawat Jalan')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">
            <i class="bi bi-bar-chart-line-fill me-2"></i>Grafik Kunjungan Lab Rawat Jalan
        </h2>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row gy-2 gx-3 align-items-end">
                    <div class="col-md-auto">
                        <label for="tgl_dari" class="form-label mb-0">Dari Tanggal:</label>
                        <input type="date" name="tgl_dari" id="tgl_dari" value="{{ $tgl_dari }}"
                            class="form-control">
                    </div>

                    <div class="col-md-auto">
                        <label for="tgl_sampai" class="form-label mb-0">Sampai Tanggal:</label>
                        <input type="date" name="tgl_sampai" id="tgl_sampai" value="{{ $tgl_sampai }}"
                            class="form-control">
                    </div>

                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">
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
                        <div id="labChartContainer" style="min-height: 400px;"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        let labChart;
        const labData = @json($data);

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
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: [themeColors.bodyColor]
                    }
                },
                xaxis: {
                    categories: chartData.map(item => item.x),
                    labels: {
                        rotate: -45,
                        style: {
                            colors: themeColors.bodyColor,
                            fontSize: '12px',
                            fontWeight: '500',
                        }
                    },
                    axisBorder: {
                        color: themeColors.borderColor
                    },
                    axisTicks: {
                        color: themeColors.borderColor
                    },
                    title: {
                        text: 'Kunjungan Lab',
                        style: {
                            color: themeColors.bodyColor,
                            fontSize: '12px',
                            fontWeight: '500',
                        }
                    }
                },

                yaxis: {
                    title: {
                        text: 'Jumlah Kunjungan',
                        style: {
                            color: themeColors.bodyColor,
                            fontSize: '12px',
                            fontWeight: '500',
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
                    colors: ['#F8DE22']
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    y: {
                        formatter: val => `${val} kunjungan`
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

            const chartElement = document.querySelector("#labChartContainer");
            chartElement.innerHTML = '';

            if (labChart) labChart.destroy();

            labChart = new ApexCharts(chartElement, options);
            labChart.render();
        }

        if (labData.length > 0) {
            createApexChart(labData);
        }
        const observer = new MutationObserver((mutationsList) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                    createApexChart(labData);
                }
            }
        });
        observer.observe(document.documentElement, {
            attributes: true
        });
    </script>
@endsection
