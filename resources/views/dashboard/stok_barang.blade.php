@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-xl font-semibold mb-4">Grafik Stok Barang</h2>

    {{-- Dropdown Filter Jenis --}}
    {{-- askdjka --}}
    <div class="mb-6">
        <form method="GET">
            <label for="jenis" class="mr-2 font-medium">Filter Jenis Barang:</label>
            <select name="jenis" id="jenis" onchange="this.form.submit()" class="border px-3 py-1 rounded">
                <option value="">Semua Jenis</option>
                @foreach($listJenis as $j)
                    <option value="{{ $j->nama }}" {{ $jenisFilter == $j->nama ? 'selected' : '' }}>
                        {{ $j->nama }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Grafik Chart --}}
    <div style="height: 400px;">
        <canvas id="stokChart"></canvas>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('stokChart').getContext('2d');

    const stokChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($data->pluck('nama_brng')) !!},
            datasets: [{
                label: 'Stok Minimal',
                data: {!! json_encode($data->pluck('stokminimal')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
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
                        text: 'Jumlah Stok Minimal'
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
                    text: 'Grafik Stok Minimal per Barang'
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
</script>
@endsection
