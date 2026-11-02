@extends('layouts.app')

@section('title', 'Rekap Keuangan')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
      <i class="bi bi-graph-up me-2"></i>Rekap Keuangan
      <br>
      <small class="text-muted fs-6">
        Tampilan Per {{ $periode == 'year' ? 'Tahun' : ($periode == 'day' ? 'Hari' : 'Bulan') }}
      </small>
    </h2>

    {{-- Card Summary Total --}}
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
      <div class="col-12 col-md-4">
        <div class="card text-white bg-dark shadow-sm border-0 h-100" style="border-left: 5px solid #28a745 !important;">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <h6 class="text-success mb-0 small fw-bold">💰 TOTAL PEMASUKAN</h6>
              <span class="badge bg-success" style="font-size: 0.7rem;">Masuk</span>
            </div>
            <h3 class="fw-bold text-success mb-1 fs-5 fs-md-4">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
            <small class="text-muted" style="font-size: 0.75rem;">Total semua pemasukan</small>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="card text-white bg-dark shadow-sm border-0 h-100" style="border-left: 5px solid #dc3545 !important;">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <h6 class="text-danger mb-0 small fw-bold">💸 TOTAL PENGELUARAN</h6>
              <span class="badge bg-danger" style="font-size: 0.7rem;">Keluar</span>
            </div>
            <h3 class="fw-bold text-danger mb-1 fs-5 fs-md-4">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
            <small class="text-muted" style="font-size: 0.75rem;">Total semua pengeluaran</small>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="card text-white bg-dark shadow-sm border-0 h-100" 
             style="border-left: 5px solid {{ $saldo >= 0 ? '#007bff' : '#ffc107' }} !important;">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <h6 class="{{ $saldo >= 0 ? 'text-primary' : 'text-warning' }} mb-0 small fw-bold">💵 SALDO BERSIH</h6>
              <span class="badge {{ $saldo >= 0 ? 'bg-primary' : 'bg-warning text-dark' }}" style="font-size: 0.7rem;">
                {{ $saldo >= 0 ? '▲ Untung' : '▼ Rugi' }}
              </span>
            </div>
            <h3 class="fw-bold {{ $saldo >= 0 ? 'text-primary' : 'text-warning' }} mb-1 fs-5 fs-md-4">
              Rp {{ number_format($saldo, 0, ',', '.') }}
            </h3>
            <small class="text-muted" style="font-size: 0.75rem;">
              {{ $saldo >= 0 ? 'Surplus' : 'Defisit' }} 
              ({{ $totalPemasukan > 0 ? number_format(($saldo/$totalPemasukan)*100, 1) : 0 }}%)
            </small>
          </div>
        </div>
      </div>
    </div>

    {{-- Card Grafik Keuangan --}}
    <div class="card bg-dark text-white border-0 shadow-sm mb-4">
      <div class="card-header border-bottom border-secondary bg-transparent">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0 fw-bold text-warning fs-6 fs-md-5">
            📈 GRAFIK KEUANGAN {{ $periode == 'year' ? 'TAHUNAN' : ($periode == 'day' ? 'HARIAN' : 'BULANAN') }}
          </h5>
        </div>
          
        {{-- Filter Periode & Range Tanggal DINAMIS --}}
        <form method="GET" action="{{ route('keuangan.rekap') }}" class="w-100">
          <div class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
              <label for="periode" class="form-label mb-1 small text-light">Tampilan:</label>
              <select name="periode" id="periode" class="form-select form-select-sm bg-dark text-light border-secondary" onchange="updateDateInputs()">
                <option value="day" {{ request('periode', 'month') == 'day' ? 'selected' : '' }}>Per Hari</option>
                <option value="month" {{ request('periode', 'month') == 'month' ? 'selected' : '' }}>Per Bulan</option>
                <option value="year" {{ request('periode') == 'year' ? 'selected' : '' }}>Per Tahun</option>
              </select>
            </div>
            
            {{-- Filter Per Hari (type: date) --}}
            <div class="col-6 col-md-3" id="filter-day" style="display: none;">
              <label for="start_date_day" class="form-label mb-1 small text-light">Dari Tanggal:</label>
              <input type="date" name="start_date" id="start_date_day" value="{{ request('start_date') }}" class="form-control form-control-sm bg-dark text-light border-secondary">
            </div>
            
            <div class="col-6 col-md-3" id="filter-day-end" style="display: none;">
              <label for="end_date_day" class="form-label mb-1 small text-light">Sampai Tanggal:</label>
              <input type="date" name="end_date" id="end_date_day" value="{{ request('end_date') }}" class="form-control form-control-sm bg-dark text-light border-secondary">
            </div>
            
            {{-- Filter Per Bulan (type: month) --}}
            <div class="col-6 col-md-3" id="filter-month" style="display: none;">
              <label for="start_date_month" class="form-label mb-1 small text-light">Dari Bulan:</label>
              <input type="month" name="start_date" id="start_date_month" value="{{ request('start_date') ? (strlen(request('start_date')) == 7 ? request('start_date') : date('Y-m', strtotime(request('start_date')))) : '' }}" class="form-control form-control-sm bg-dark text-light border-secondary">
            </div>
            
            <div class="col-6 col-md-3" id="filter-month-end" style="display: none;">
              <label for="end_date_month" class="form-label mb-1 small text-light">Sampai Bulan:</label>
              <input type="month" name="end_date" id="end_date_month" value="{{ request('end_date') ? (strlen(request('end_date')) == 7 ? request('end_date') : date('Y-m', strtotime(request('end_date')))) : '' }}" class="form-control form-control-sm bg-dark text-light border-secondary">
            </div>
            
            {{-- Filter Per Tahun (type: number) --}}
            <div class="col-6 col-md-3" id="filter-year" style="display: none;">
              <label for="start_date_year" class="form-label mb-1 small text-light">Dari Tahun:</label>
              <input type="number" name="start_date" id="start_date_year" value="{{ request('start_date') ? (strlen(request('start_date')) == 4 ? request('start_date') : date('Y', strtotime(request('start_date')))) : '' }}" class="form-control form-control-sm bg-dark text-light border-secondary" min="2000" max="2100" placeholder="2024">
            </div>
            
            <div class="col-6 col-md-3" id="filter-year-end" style="display: none;">
              <label for="end_date_year" class="form-label mb-1 small text-light">Sampai Tahun:</label>
              <input type="number" name="end_date" id="end_date_year" value="{{ request('end_date') ? (strlen(request('end_date')) == 4 ? request('end_date') : date('Y', strtotime(request('end_date')))) : '' }}" class="form-control form-control-sm bg-dark text-light border-secondary" min="2000" max="2100" placeholder="2024">
            </div>
            
            <div class="col-6 col-md-1 d-grid">
              <button type="submit" class="btn btn-sm btn-warning">
                <i class="bi bi-funnel me-1"></i>Filter
              </button>
            </div>
            <div class="col-6 col-md-1 d-grid">
              <a href="{{ route('keuangan.rekap') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
              </a>
            </div>
          </div>
        </form>
      </div>
      <div class="card-body p-2 p-md-3">
        <div id="chart-monitoring" style="min-height: 350px; width: 100%;"></div>
      </div>
    </div>

    {{-- Tabel Detail --}}
    <div class="card bg-dark text-white border-0 shadow-sm mb-4">
      <div class="card-header bg-transparent border-bottom border-secondary">
        <h5 class="mb-0 fw-bold text-warning fs-6 fs-md-5">
          📊 DETAIL REKAP {{ $periode == 'year' ? 'TAHUNAN' : ($periode == 'day' ? 'HARIAN' : 'BULANAN') }}
        </h5>
      </div>
      <div class="card-body p-2 p-md-3">
        <div class="table-responsive">
          <table class="table table-dark table-striped table-bordered align-middle text-light mb-0" style="min-width: 600px;">
            <thead class="table-secondary text-dark text-center">
              <tr>
                <th style="width: 5%">No</th>
                <th style="width: 25%">Periode</th>
                <th style="width: 23%" class="text-success">Pemasukan</th>
                <th style="width: 23%" class="text-danger">Pengeluaran</th>
                <th style="width: 24%" class="text-primary">Saldo</th>
              </tr>
            </thead>
            <tbody style="font-size: 0.875rem;">
              @forelse($rekapBulanan as $key => $item)
                <tr>
                  <td class="text-center">{{ $key + 1 }}</td>
                  <td>
                    <strong>{{ $item->nama_periode }}</strong>
                  </td>
                  <td class="text-end text-success fw-bold">
                    + Rp {{ number_format($item->total_pemasukan, 0, ',', '.') }}
                  </td>
                  <td class="text-end text-danger fw-bold">
                    - Rp {{ number_format($item->total_pengeluaran, 0, ',', '.') }}
                  </td>
                  <td class="text-end fw-bold {{ $item->saldo >= 0 ? 'text-primary' : 'text-warning' }}">
                    Rp {{ number_format($item->saldo, 0, ',', '.') }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-3">Belum ada data rekap</td>
                </tr>
              @endforelse
            </tbody>
            <tfoot class="table-secondary text-dark">
              <tr>
                <th colspan="2" class="text-end">TOTAL:</th>
                <th class="text-end text-success fw-bold">+ Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</th>
                <th class="text-end text-danger fw-bold">- Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</th>
                <th class="text-end fw-bold {{ $saldo >= 0 ? 'text-primary' : 'text-warning' }}">
                  Rp {{ number_format($saldo, 0, ',', '.') }}
                </th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    {{-- Card Data Terbaru --}}
    <div class="row g-3">
      <div class="col-12 col-lg-6 mb-4">
        <div class="card bg-dark text-white border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
          <div class="card-header bg-transparent border-bottom border-success">
            <h5 class="mb-0 fw-bold text-success fs-6">📥 5 PEMASUKAN TERBARU</h5>
          </div>
          <div class="card-body p-2 p-md-3">
            <div class="table-responsive">
              <table class="table table-dark table-sm table-striped align-middle mb-0">
                <thead>
                  <tr class="text-muted small">
                    <th>Tanggal</th>
                    <th class="text-end">Jumlah</th>
                  </tr>
                </thead>
                <tbody style="font-size: 0.875rem;">
                  @forelse($pemasukan as $item)
                    <tr>
                      <td class="text-light">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                      <td class="text-end text-success fw-bold">
                        + Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="2" class="text-center text-muted py-3">Belum ada data</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6 mb-4">
        <div class="card bg-dark text-white border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
          <div class="card-header bg-transparent border-bottom border-danger">
            <h5 class="mb-0 fw-bold text-danger fs-6">📤 5 PENGELUARAN TERBARU</h5>
          </div>
          <div class="card-body p-2 p-md-3">
            <div class="table-responsive">
              <table class="table table-dark table-sm table-striped align-middle mb-0">
                <thead>
                  <tr class="text-muted small">
                    <th>Tanggal</th>
                    <th class="text-end">Jumlah</th>
                  </tr>
                </thead>
                <tbody style="font-size: 0.875rem;">
                  @forelse($pengeluaran as $item)
                    <tr>
                      <td class="text-light">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                      <td class="text-end text-danger fw-bold">
                        - Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="2" class="text-center text-muted py-3">Belum ada data</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

@php
use Carbon\Carbon;

$categories = [];
$dataPemasukan = [];
$dataPengeluaran = [];
$dataSaldo = [];

foreach ($rekapBulanan as $item) {
  $categories[] = $item->nama_periode;
  $dataPemasukan[] = $item->total_pemasukan;
  $dataPengeluaran[] = $item->total_pengeluaran;
  $dataSaldo[] = $item->saldo;
}
@endphp

<script>
function updateDateInputs() {
  var periode = document.getElementById('periode').value;
  
  document.getElementById('filter-day').style.display = 'none';
  document.getElementById('filter-day-end').style.display = 'none';
  document.getElementById('filter-month').style.display = 'none';
  document.getElementById('filter-month-end').style.display = 'none';
  document.getElementById('filter-year').style.display = 'none';
  document.getElementById('filter-year-end').style.display = 'none';
  
  document.getElementById('start_date_day').disabled = true;
  document.getElementById('end_date_day').disabled = true;
  document.getElementById('start_date_month').disabled = true;
  document.getElementById('end_date_month').disabled = true;
  document.getElementById('start_date_year').disabled = true;
  document.getElementById('end_date_year').disabled = true;
  
  if (periode === 'day') {
    document.getElementById('filter-day').style.display = 'block';
    document.getElementById('filter-day-end').style.display = 'block';
    document.getElementById('start_date_day').disabled = false;
    document.getElementById('end_date_day').disabled = false;
  } else if (periode === 'month') {
    document.getElementById('filter-month').style.display = 'block';
    document.getElementById('filter-month-end').style.display = 'block';
    document.getElementById('start_date_month').disabled = false;
    document.getElementById('end_date_month').disabled = false;
  } else if (periode === 'year') {
    document.getElementById('filter-year').style.display = 'block';
    document.getElementById('filter-year-end').style.display = 'block';
    document.getElementById('start_date_year').disabled = false;
    document.getElementById('end_date_year').disabled = false;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  updateDateInputs();
  
  var periode = '{{ $periode }}';
  var categoriesCount = {{ count($categories) }};
  
  var options = {
    series: [{
      name: 'Pemasukan',
      type: 'column',
      data: @json($dataPemasukan)
    }, {
      name: 'Pengeluaran',
      type: 'column',
      data: @json($dataPengeluaran)
    }, {
      name: 'Saldo',
      type: 'line',
      data: @json($dataSaldo)
    }],
    chart: {
      type: 'line',
      height: 350,
      foreColor: '#9ca3af',
      toolbar: { show: false }
    },
    colors: ['#10b981', '#ef4444', '#3b82f6'],
    stroke: {
      width: [0, 0, 3],
      curve: 'smooth'
    },
    plotOptions: {
      bar: {
        columnWidth: '55%',
        borderRadius: 3
      }
    },
    fill: {
      opacity: [0.85, 0.85, 1],
      gradient: {
        inverseColors: false,
        shade: 'dark',
        type: "vertical",
        opacityFrom: 0.85,
        opacityTo: 0.55,
        stops: [0, 100, 100, 100]
      }
    },
    grid: {
      borderColor: '#374151'
    },
    xaxis: {
      categories: @json($categories),
      labels: {
        style: {
          colors: '#9ca3af',
          fontSize: '11px'
        },
        rotate: -45
      },
      axisBorder: { color: '#374151' },
      axisTicks: { color: '#374151' }
    },
    yaxis: {
      title: {
        text: 'Jumlah (Rp)',
        style: {
          color: '#e5e7eb',
          fontSize: '11px'
        }
      },
      labels: {
        formatter: function(val) {
          if (val >= 1000000) return 'Rp ' + (val/1000000).toFixed(1) + 'jt';
          if (val >= 1000) return 'Rp ' + (val/1000).toFixed(0) + 'k';
          return 'Rp ' + val.toLocaleString('id-ID');
        },
        style: {
          colors: '#9ca3af',
          fontSize: '11px'
        }
      }
    },
    tooltip: {
      theme: 'dark',
      y: {
        formatter: function(val) {
          return 'Rp ' + val.toLocaleString('id-ID');
        }
      }
    },
    legend: {
      position: 'bottom',
      labels: {
        colors: '#e5e7eb'
      }
    },
    responsive: [{
      breakpoint: 576,
      options: {
        chart: { height: 260 },
        plotOptions: { bar: { columnWidth: '75%' } },
        xaxis: { labels: { rotate: -45, style: { fontSize: '10px' } } }
      }
    }]
  };
  
  new ApexCharts(document.querySelector("#chart-monitoring"), options).render();
});
</script>
@endsection
