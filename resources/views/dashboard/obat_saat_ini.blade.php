@extends('layouts.app')

@section('title', 'Stok Obat Saat Ini')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">💊 Grafik Stok Obat Saat Ini</h2>

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
                <div style="height: 400px;">
                    <canvas id="stokSaatIniChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stokSaatIniChart;

        function getComputedThemeColors() {
            const style = getComputedStyle(document.documentElement);
            return {
                bodyColor: style.getPropertyValue('--bs-body-color').trim(),
                bodyBg: style.getPropertyValue('--bs-body-bg').trim(),
                borderColor: style.getPropertyValue('--navbar-border').trim(),
            };
        }

        function createChart(labels, data) {
            const ctx = document.getElementById('stokSaatIniChart').getContext('2d');
            if (stokSaatIniChart) stokSaatIniChart.destroy();

            const themeColors = getComputedThemeColors();

            stokSaatIniChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Stok Saat Ini',
                        data: data,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Stok Saat Ini',
                                color: themeColors.bodyColor
                            },
                            ticks: {
                                color: themeColors.bodyColor
                            },
                            grid: {
                                color: themeColors.borderColor
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 90,
                                minRotation: 45,
                                autoSkip: false,
                                color: themeColors.bodyColor
                            },
                            grid: {
                                color: themeColors.borderColor
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: themeColors.bodyColor
                            }
                        },
                        title: {
                            display: true,
                            text: 'Grafik Stok Saat Ini per Barang',
                            color: themeColors.bodyColor
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' unit';
                                }
                            }
                        }
                    }
                }
            });
        }

        createChart(
            {!! json_encode($data->pluck('nama_brng')) !!},
            {!! json_encode($data->pluck('total_stok')) !!}
        );

        const observer = new MutationObserver((mutationsList) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                    createChart(
                        stokSaatIniChart.data.labels,
                        stokSaatIniChart.data.datasets[0].data
                    );
                }
            }
        });

        observer.observe(document.documentElement, {
            attributes: true
        });

        let debounceTimer;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const keyword = this.value;

            debounceTimer = setTimeout(() => {
                if (keyword.length >= 2 || keyword.length === 0) {
                    fetch(
                            `{{ route('obat.search-obat-saat-ini') }}?q=${encodeURIComponent(keyword)}&jenis={{ $jenisFilter }}`)
                        .then(res => {
                            if (!res.ok) {
                                throw new Error('Network response was not ok ' + res.statusText);
                            }
                            return res.json();
                        })
                        .then(data => {
                            const labels = data.map(item => item.nama_brng);
                            const stok = data.map(item => item.total_stok);
                            createChart(labels, stok);
                        })
                        .catch(error => console.error('Error fetching search data:', error));
                }
            }, 300);
        });
    </script>
@endsection
