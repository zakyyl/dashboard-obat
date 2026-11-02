@extends('layouts.app')

@section('title', 'Ketersediaan Tempat Tidur')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
        <i class="bi bi-door-open me-2"></i>Informasi Ketersediaan Tempat Tidur
        <br>
        <small class="text-muted fs-6">Update terakhir: {{ now()->format('d-m-Y H:i:s') }}</small>
    </h2>

    <div class="card mb-3 mb-md-4 shadow-sm bg-dark border-0">
        <div class="card-body p-3">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">
                    <label for="nsFilter" class="form-label text-light small mb-1">Filter Ruangan / Nurse Station:</label>
                    <select id="nsFilter" class="form-select form-select-sm bg-dark text-light border-secondary">
                        <option value="all">Tampilkan Semua Ruangan</option>
                        @foreach($data as $item)
                        <option value="{{ $item['nurse_station'] }}">{{ $item['nurse_station'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4 bg-dark border-0">
        <div class="card-body p-2 p-md-3">
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="bedChart"></canvas>
            </div>
        </div>
    </div>

    @foreach($data as $item)
    <div class="card shadow-sm mb-3 mb-md-4 ns-table bg-dark border-0" data-ns="{{ $item['nurse_station'] }}">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold fs-6 fs-md-5">Ruang {{ $item['nurse_station'] }}</h5>
            <div class="small fw-semibold">
                Total: <span class="badge bg-dark text-light">{{ $item['jumlah_bed'] }}</span>
                Terpakai: <span class="badge bg-danger">{{ $item['bed_terisi'] }}</span>
                Kosong: <span class="badge bg-success">{{ $item['bed_kosong'] }}</span>
            </div>
        </div>
        <div class="card-body p-2 p-md-3">
            <div class="table-responsive">
                <table class="table table-dark table-striped table-bordered align-middle text-light mb-0" style="min-width: 550px;">
                    <thead class="table-secondary text-dark text-center">
                        <tr>
                            <th style="width: 40%">Ruang / Bangsal</th>
                            <th style="width: 20%">Kelas</th>
                            <th style="width: 12%">Jumlah</th>
                            <th style="width: 14%">Terisi</th>
                            <th style="width: 14%">Kosong</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @foreach($item['detail'] as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row->nm_bangsal }}</td>
                            <td>{{ $row->kelas }}</td>
                            <td class="text-center">{{ $row->jumlah }}</td>
                            <td class="text-center text-warning fw-bold">{{ $row->terisi }}</td>
                            <td class="text-center text-success fw-bold">{{ $row->kosong }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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

    const bedChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: allData.map(item => item.nurse_station),
            datasets: [
                {
                    label: 'Terpakai',
                    data: allData.map(item => item.bed_terisi),
                    backgroundColor: '#f59e0b',
                    borderRadius: 4
                },
                {
                    label: 'Kosong',
                    data: allData.map(item => item.bed_kosong),
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#e5e7eb',
                        font: { size: 12 }
                    }
                },
                title: {
                    display: false
                }
            },
            scales: {
                x: {
                    ticks: { color: '#9ca3af', font: { size: 11 } },
                    grid: { color: 'rgba(255,255,255,0.06)' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#9ca3af', font: { size: 11 } },
                    grid: { color: 'rgba(255,255,255,0.06)' }
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
