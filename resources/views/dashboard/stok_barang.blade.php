@extends('layouts.app')

@section('title', 'Stok Obat Minimal')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">📊 Grafik Stok Obat Minimal & Saat Ini</h2>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row gy-2 gx-3 align-items-center">
                    <div class="col-md-auto">
                        <label for="jenis" class="form-label mb-0">Filter Jenis Barang:</label>
                        <select name="jenis" id="jenis" onchange="this.form.submit()" class="form-select">
                            <option value="">Semua Jenis</option>
                            @foreach ($listJenis as $j)
                                <option value="{{ $j->nama }}" {{ $jenisFilter == $j->nama ? 'selected' : '' }}>
                                    {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-auto">
                        <label for="search" class="form-label mb-0">Cari Nama Obat:</label>
                        <input type="text" id="search" class="form-control" placeholder="Ketik nama obat...">
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div id="stokChartContainer" style="height: 400px;">
                    {{-- ApexCharts akan dirender di sini --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        let stokApexChart;

        function getComputedThemeColors() {
            const style = getComputedStyle(document.documentElement);
            return {
                bodyColor: style.getPropertyValue('--bs-body-color').trim(),
                borderColor: style.getPropertyValue('--navbar-border').trim(),
            };
        }

        function createApexChart(data) {
            const themeColors = getComputedThemeColors();

            // PASTIKAN data adalah array, bahkan jika kosong
            // Jika data dari PHP di-json_encode sebagai objek kosong {}, ini akan jadi array kosong []
            const dataArray = Array.isArray(data) ? data : Object.values(data || {});

            const seriesData = dataArray.map(item => ({
                x: item.nama_brng,
                y: item.total_stok,
                goals: [{
                    name: 'Stok Minimal',
                    value: item.stokminimal,
                    strokeColor: '#FFD700', // Warna garis untuk stok minimal
                    strokeWidth: 22, // <-- Tambahkan atau ubah nilai ini untuk menebalkan garis
                }]
            }));

            const options = {
                series: [{
                    name: 'Stok Saat Ini',
                    data: seriesData
                }],
                chart: {
                    height: 400,
                    type: 'bar',
                    toolbar: {
                        show: false
                    },
                    background: 'transparent',
                    foreColor: themeColors.bodyColor
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '90%', // <--- NILAI INI YANG DIUBAH UNTUK MENEBALKAN BATANG
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    type: 'category',
                    labels: {
                        rotate: -45,
                        style: {
                            colors: themeColors.bodyColor
                        }
                    },
                    axisBorder: {
                        show: true,
                        color: themeColors.borderColor
                    },
                    axisTicks: {
                        show: true,
                        color: themeColors.borderColor
                    }
                },
                yaxis: {
                    title: {
                        text: 'Jumlah Stok',
                        style: {
                            colors: themeColors.bodyColor
                        }
                    },
                    labels: {
                        style: {
                            colors: themeColors.bodyColor
                        }
                    },
                    axisBorder: {
                        show: true,
                        color: themeColors.borderColor
                    },
                    axisTicks: {
                        show: true,
                        color: themeColors.borderColor
                    },
                    grid: {
                        borderColor: themeColors.borderColor
                    }
                },
                fill: {
                    opacity: 1,
                     colors: ['#28a745'] // <-- SAYA JUGA MENINGKATKAN OPACITY AGAR WARNA LEBIH SOLID
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " unit"
                        }
                    },
                    theme: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light'
                },
                legend: {
                    labels: {
                        colors: themeColors.bodyColor
                    },
                    markers: {
                        fillColors: ['rgba(54, 162, 235, 0.9)', '#775DD0']
                    }
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
                }
            };

            const chartElement = document.querySelector("#stokChartContainer");
            if (stokApexChart) {
                stokApexChart.destroy();
            }
            stokApexChart = new ApexCharts(chartElement, options);
            stokApexChart.render();
        }
        createApexChart({!! json_encode($data->values()) !!});
        // --- Dark Mode Chart Update Listener ---
        const observer = new MutationObserver((mutationsList) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                    if (stokApexChart && stokApexChart.w && stokApexChart.w.config && stokApexChart.w.config.series[0]) {
                        createApexChart(stokApexChart.w.config.series[0].data.map(item => ({
                            nama_brng: item.x,
                            total_stok: item.y,
                            stokminimal: item.goals[0].value
                        })));
                    } else {
                        console.warn("Chart not yet initialized or data not available for theme change.");
                    }
                }
            }
        });
        observer.observe(document.documentElement, {
            attributes: true
        });


        // --- Search/Filter Logic ---
        let debounceTimer;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const keyword = this.value;

            debounceTimer = setTimeout(() => {
                if (keyword.length >= 2 || keyword.length === 0) {
                    fetch(`{{ route('obat.search-obat') }}?q=${encodeURIComponent(keyword)}&jenis={{ $jenisFilter }}`)
                        .then(res => {
                            if (!res.ok) {
                                throw new Error('Network response was not ok ' + res.statusText);
                            }
                            return res.json();
                        })
                        .then(data => {
                            // PASTIKAN data dari fetch adalah array
                            createApexChart(data);
                        })
                        .catch(error => console.error('Error fetching search data:', error));
                }
            }, 300);
        });
    </script>
@endsection
