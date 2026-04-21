@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center mb-4">Informasi Ketersediaan Tempat Tidur</h2>
    <p class="text-center">Update terakhir: {{ now()->format('d-m-Y H:i:s') }}</p>

    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <select id="nsFilter" class="form-select">
                <option value="all">Tampilkan Semua Ruangan</option>
                @foreach($data as $item)
                <option value="{{ $item['nurse_station'] }}">{{ $item['nurse_station'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <canvas id="bedChart" height="120"></canvas>
        </div>
    </div>

    @foreach($data as $item)
    <div class="card shadow-sm mb-4 ns-table" data-ns="{{ $item['nurse_station'] }}">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Ruang {{ $item['nurse_station'] }}</h5>
        </div>
        <div class="card-body">
            <p>Total: <b>{{ $item['jumlah_bed'] }}</b> | Terpakai: <b>{{ $item['bed_terisi'] }}</b> | Kosong: <b>{{
                    $item['bed_kosong'] }}</b></p>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Ruang / Bangsal</th>
                        <th>Kelas</th>
                        <th>Jumlah</th>
                        <th>Terisi</th>
                        <th>Kosong</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item['detail'] as $row)
                    <tr>
                        <td>{{ $row->nm_bangsal }}</td>
                        <td>{{ $row->kelas }}</td>
                        <td>{{ $row->jumlah }}</td>
                        <td>{{ $row->terisi }}</td>
                        <td>{{ $row->kosong }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const allData = @json($data);

    const ctx = document.getElementById('bedChart').getContext('2d');

    const gradientTerpakai = ctx.createLinearGradient(0, 0, 0, 400);
    gradientTerpakai.addColorStop(0, '#f59e0b'); // gold tua
    gradientTerpakai.addColorStop(1, '#fcd34d'); // gold muda

    const gradientKosong = ctx.createLinearGradient(0, 0, 0, 400);
    gradientKosong.addColorStop(0, '#6b7280');
    gradientKosong.addColorStop(1, '#d1d5db'); // abu muda

    const bedChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: allData.map(item => item.nurse_station),
        datasets: [
            {
                label: 'Terpakai',
                data: allData.map(item => item.bed_terisi),
                backgroundColor: function(context) {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) return null; // skip dulu sampai chart ready

                    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    gradient.addColorStop(0, '#f59e0b'); // gold tua
                    gradient.addColorStop(1, '#fcd34d'); // gold muda
                    return gradient;
                }
            },
            {
                label: 'Kosong',
                data: allData.map(item => item.bed_kosong),
                backgroundColor: function(context) {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) return null;

                    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    gradient.addColorStop(0, '#6b7280'); // abu tua
                    gradient.addColorStop(1, '#d1d5db'); // abu muda
                    return gradient;
                }
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    color: '#fff',
                    font: { size: 14 }
                }
            },
            title: {
                display: true,
                text: 'Grafik Ketersediaan Tempat Tidur per Ruangan',
                color: '#fff',
                font: { size: 16 }
            }
        },
        scales: {
            x: {
                ticks: { color: '#fff', font: { size: 12 } },
                grid: { color: 'rgba(255,255,255,0.1)' }
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#fff', font: { size: 12 } },
                grid: { color: 'rgba(255,255,255,0.1)' }
            }
        }
    }
});

    document.getElementById('nsFilter').addEventListener('change', function () {
        let val = this.value;

        document.querySelectorAll('.ns-table').forEach(el => {
            if (val === 'all' || el.dataset.ns === val) {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        });

        let filtered = (val === 'all') ? allData : allData.filter(item => item.nurse_station === val);

        bedChart.data.labels = filtered.map(item => item.nurse_station);
        bedChart.data.datasets[0].data = filtered.map(item => item.bed_terisi);
        bedChart.data.datasets[1].data = filtered.map(item => item.bed_kosong);
        bedChart.update();
    });
</script>
@endpush
