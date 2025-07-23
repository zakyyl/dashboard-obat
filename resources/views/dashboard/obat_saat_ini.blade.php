@extends('layouts.app')

@section('title', 'Stok Obat Saat Ini')

@section('content')
    <div class="container">
        <h2 class="mb-4">💊 Grafik Stok Obat Saat Ini</h2>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row gy-2 gx-3 align-items-center">
                    <div class="col-auto">
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

                    <div class="col-auto">
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

        function createChart(labels, data) {
            const ctx = document.getElementById('stokSaatIniChart').getContext('2d');
            if (stokSaatIniChart) stokSaatIniChart.destroy();

            stokSaatIniChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Stok Saat Ini',
                        data: data,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
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
                                text: 'Jumlah Stok Saat Ini'
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 90,
                                minRotation: 45,
                                autoSkip: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Grafik Stok Saat Ini per Barang'
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

        let debounceTimer;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const keyword = this.value;

            debounceTimer = setTimeout(() => {
                if (keyword.length >= 2) {
                    fetch(`{{ route('obat.search-obat-saat-ini') }}?q=${encodeURIComponent(keyword) }`)
                        .then(res => res.json())
                        .then(data => {
                            const labels = data.map(item => item.nama_brng);
                            const stok = data.map(item => item.total_stok);
                            createChart(labels, stok);
                        });
                }
            }, 300);
        });
    </script>
@endsection
