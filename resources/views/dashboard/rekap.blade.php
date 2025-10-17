@extends('layouts.app')

@section('title', 'Rekap Keuangan')

@section('content')
<section class="content">
  <div class="container-fluid">

    <h2 class="mb-4 text-center">
      <i class="bi bi-graph-up me-2"></i>Rekap Keuangan
      <br>
      <small class="text-muted">
        Tampilan Per {{ $periode == 'year' ? 'Tahun' : ($periode == 'day' ? 'Hari' : 'Bulan') }}
      </small>
    </h2>

    {{-- Card Summary Total dengan Style Monitoring Saham --}}
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card text-white shadow-lg border-success" style="border-left: 5px solid #28a745;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="text-success mb-0">💰 TOTAL PEMASUKAN</h6>
              <span class="badge bg-success">Masuk</span>
            </div>
            <h2 class="fw-bold text-success mb-0">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h2>
            <small class="text-muted">Total semua pemasukan</small>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white shadow-lg border-danger" style="border-left: 5px solid #dc3545;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="text-danger mb-0">💸 TOTAL PENGELUARAN</h6>
              <span class="badge bg-danger">Keluar</span>
            </div>
            <h2 class="fw-bold text-danger mb-0">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h2>
            <small class="text-muted">Total semua pengeluaran</small>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white shadow-lg {{ $saldo >= 0 ? 'border-primary' : 'border-warning' }}" 
             style="border-left: 5px solid {{ $saldo >= 0 ? '#007bff' : '#ffc107' }};">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="{{ $saldo >= 0 ? 'text-primary' : 'text-warning' }} mb-0">💵 SALDO BERSIH</h6>
              <span class="badge {{ $saldo >= 0 ? 'bg-primary' : 'bg-warning text-dark' }}">
                {{ $saldo >= 0 ? '▲ Untung' : '▼ Rugi' }}
              </span>
            </div>
            <h2 class="fw-bold {{ $saldo >= 0 ? 'text-primary' : 'text-warning' }} mb-0">
              Rp {{ number_format($saldo, 0, ',', '.') }}
            </h2>
            <small class="text-muted">
              {{ $saldo >= 0 ? 'Surplus' : 'Defisit' }} 
              ({{ $totalPemasukan > 0 ? number_format(($saldo/$totalPemasukan)*100, 1) : 0 }}%)
            </small>
          </div>
        </div>
      </div>
    </div>

    {{-- Card Grafik Line Chart Style Saham --}}
    <div class="card text-white shadow-lg mb-4">
      <div class="card-header border-bottom border-secondary bg-transparent">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h5 class="mb-0 fw-bold text-warning">
            📈 GRAFIK KEUANGAN {{ $periode == 'year' ? 'TAHUNAN' : ($periode == 'day' ? 'HARIAN' : 'BULANAN') }}
          </h5>
          
          {{-- Filter Periode & Range Tanggal DINAMIS --}}
          <form method="GET" action="{{ route('keuangan.rekap') }}" class="d-flex gap-2 flex-wrap align-items-end">
            <div>
              <label for="periode" class="form-label mb-1 small text-light">Tampilan:</label>
              <select name="periode" id="periode" class="form-select form-select-sm" style="width: 110px;" onchange="updateDateInputs()">
                <option value="day" {{ request('periode', 'month') == 'day' ? 'selected' : '' }}>Per Hari</option>
                <option value="month" {{ request('periode', 'month') == 'month' ? 'selected' : '' }}>Per Bulan</option>
                <option value="year" {{ request('periode') == 'year' ? 'selected' : '' }}>Per Tahun</option>
              </select>
            </div>
            
            {{-- Filter Per Hari (type: date) --}}
            <div id="filter-day" style="display: none;">
              <label for="start_date_day" class="form-label mb-1 small text-light">Dari Tanggal:</label>
              <input type="date" name="start_date" id="start_date_day" value="{{ request('start_date') }}" class="form-control form-control-sm" style="width: 160px;">
            </div>
            
            <div id="filter-day-end" style="display: none;">
              <label for="end_date_day" class="form-label mb-1 small text-light">Sampai Tanggal:</label>
              <input type="date" name="end_date" id="end_date_day" value="{{ request('end_date') }}" class="form-control form-control-sm" style="width: 160px;">
            </div>
            
            {{-- Filter Per Bulan (type: month) --}}
            <div id="filter-month" style="display: none;">
              <label for="start_date_month" class="form-label mb-1 small text-light">Dari Bulan:</label>
              <input type="month" name="start_date" id="start_date_month" value="{{ request('start_date') ? (strlen(request('start_date')) == 7 ? request('start_date') : date('Y-m', strtotime(request('start_date')))) : '' }}" class="form-control form-control-sm" style="width: 160px;">
            </div>
            
            <div id="filter-month-end" style="display: none;">
              <label for="end_date_month" class="form-label mb-1 small text-light">Sampai Bulan:</label>
              <input type="month" name="end_date" id="end_date_month" value="{{ request('end_date') ? (strlen(request('end_date')) == 7 ? request('end_date') : date('Y-m', strtotime(request('end_date')))) : '' }}" class="form-control form-control-sm" style="width: 160px;">
            </div>
            
            {{-- Filter Per Tahun (type: number) --}}
            <div id="filter-year" style="display: none;">
              <label for="start_date_year" class="form-label mb-1 small text-light">Dari Tahun:</label>
              <input type="number" name="start_date" id="start_date_year" value="{{ request('start_date') ? (strlen(request('start_date')) == 4 ? request('start_date') : date('Y', strtotime(request('start_date')))) : '' }}" class="form-control form-control-sm" min="2000" max="2100" placeholder="2024" style="width: 120px;">
            </div>
            
            <div id="filter-year-end" style="display: none;">
              <label for="end_date_year" class="form-label mb-1 small text-light">Sampai Tahun:</label>
              <input type="number" name="end_date" id="end_date_year" value="{{ request('end_date') ? (strlen(request('end_date')) == 4 ? request('end_date') : date('Y', strtotime(request('end_date')))) : '' }}" class="form-control form-control-sm" min="2000" max="2100" placeholder="2024" style="width: 120px;">
            </div>
            
            <button type="submit" class="btn btn-sm btn-warning">
              <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('keuangan.rekap') }}" class="btn btn-sm btn-secondary">
              <i class="fas fa-redo"></i> Reset
            </a>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div id="chart-monitoring"></div>
      </div>
    </div>

    {{-- Tabel Detail dengan Style Professional --}}
    <div class="card text-white shadow-lg mb-4">
      <div class="card-header bg-transparent border-bottom border-secondary">
        <h5 class="mb-0 fw-bold text-warning">
          📊 DETAIL REKAP {{ $periode == 'year' ? 'TAHUNAN' : ($periode == 'day' ? 'HARIAN' : 'BULANAN') }}
        </h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead class="table-secondary">
              <tr class="text-center">
                <th width="5%" class="text-dark">No</th>
                <th class="text-dark">Periode</th>
                <th class="text-success">Pemasukan</th>
                <th class="text-danger">Pengeluaran</th>
                <th class="text-primary">Saldo</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rekapBulanan as $key => $item)
                <tr>
                  <td class="text-center text-light">{{ $key + 1 }}</td>
                  <td class="text-light">
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
                  <td colspan="5" class="text-center text-muted">Belum ada data rekap</td>
                </tr>
              @endforelse
            </tbody>
            <tfoot class="table-secondary">
              <tr>
                <th colspan="2" class="text-end text-dark">TOTAL:</th>
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
    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card text-white shadow-lg border-success" style="border-left: 4px solid #28a745;">
          <div class="card-header bg-transparent border-bottom border-success">
            <h5 class="mb-0 fw-bold text-success">📥 5 PEMASUKAN TERBARU</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th class="text-light">Tanggal</th>
                    <th class="text-end text-light">Jumlah</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($pemasukan as $item)
                    <tr>
                      <td class="text-light">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                      <td class="text-end text-success fw-bold">
                        + Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="2" class="text-center text-muted">Belum ada data</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-4">
        <div class="card text-white shadow-lg border-danger" style="border-left: 4px solid #dc3545;">
          <div class="card-header bg-transparent border-bottom border-danger">
            <h5 class="mb-0 fw-bold text-danger">📤 5 PENGELUARAN TERBARU</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th class="text-light">Tanggal</th>
                    <th class="text-end text-light">Jumlah</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($pengeluaran as $item)
                    <tr>
                      <td class="text-light">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                      <td class="text-end text-danger fw-bold">
                        - Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="2" class="text-center text-muted">Belum ada data</td>
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
</section>

@php
use Carbon\Carbon;

// Siapkan data untuk chart
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

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Function untuk update input filter sesuai periode
function updateDateInputs() {
  var periode = document.getElementById('periode').value;
  
  // Sembunyikan semua input terlebih dahulu
  document.getElementById('filter-day').style.display = 'none';
  document.getElementById('filter-day-end').style.display = 'none';
  document.getElementById('filter-month').style.display = 'none';
  document.getElementById('filter-month-end').style.display = 'none';
  document.getElementById('filter-year').style.display = 'none';
  document.getElementById('filter-year-end').style.display = 'none';
  
  // Disable semua input
  document.getElementById('start_date_day').disabled = true;
  document.getElementById('end_date_day').disabled = true;
  document.getElementById('start_date_month').disabled = true;
  document.getElementById('end_date_month').disabled = true;
  document.getElementById('start_date_year').disabled = true;
  document.getElementById('end_date_year').disabled = true;
  
  // Tampilkan dan enable input sesuai periode
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

// Render Chart
document.addEventListener('DOMContentLoaded', function() {
  // Update filter saat halaman dimuat
  updateDateInputs();
  
  var periode = '{{ $periode }}';
  var categoriesCount = {{ count($categories) }};
  
  // Chart Combo Bar + Line Style Financial Report
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
      height: 400,
      background: '#1a1a1a',
      foreColor: '#e5e7eb',
      toolbar: {
        show: true,
        tools: {
          download: true,
          selection: false,
          zoom: true,
          zoomin: true,
          zoomout: true,
          pan: false,
          reset: true
        }
      },
      zoom: {
        enabled: true
      }
    },
    colors: ['#28a745', '#dc3545', '#0d6efd'],
    stroke: {
      width: [0, 0, 4],
      curve: 'smooth'
    },
    plotOptions: {
      bar: {
        columnWidth: '60%',
        borderRadius: 3
      }
    },
    fill: {
      type: ['gradient', 'gradient', 'solid'],
      gradient: {
        shade: 'dark',
        type: 'vertical',
        shadeIntensity: 0.5,
        gradientToColors: ['#34d399', '#dc3545', undefined],
        opacityFrom: 0.9,
        opacityTo: 0.6,
        stops: [0, 100]
      }
    },
    markers: {
      size: [0, 0, 6],
      strokeWidth: 2,
      strokeColors: '#fff',
      hover: {
        size: 8
      }
    },
    dataLabels: {
      enabled: true,
      enabledOnSeries: [2],
      formatter: function(val) {
        if (val >= 1000000) {
          return (val/1000000).toFixed(1) + 'jt';
        } else if (val >= 1000) {
          return (val/1000).toFixed(0) + 'k';
        }
        return val;
      },
      offsetY: -10,
      style: {
        fontSize: '11px',
        fontWeight: 'bold',
        colors: ['#ffc107']
      },
      background: {
        enabled: true,
        foreColor: '#000',
        borderRadius: 3,
        padding: 4,
        opacity: 0.8,
        borderWidth: 1,
        borderColor: '#ffc107'
      }
    },
    grid: {
      borderColor: '#333',
      strokeDashArray: 3,
      xaxis: {
        lines: {
          show: true
        }
      },
      yaxis: {
        lines: {
          show: true
        }
      },
      padding: {
        top: 0,
        right: 30,
        bottom: 0,
        left: 10
      }
    },
    xaxis: {
      categories: @json($categories),
      labels: {
        style: {
          colors: '#9ca3af',
          fontSize: '11px'
        },
        rotate: periode === 'day' && categoriesCount > 7 ? -45 : 0,
        rotateAlways: periode === 'day' && categoriesCount > 7
      },
      axisBorder: {
        color: '#444'
      },
      axisTicks: {
        color: '#444'
      }
    },
    yaxis: {
      title: {
        text: 'Jumlah (Rupiah)',
        style: {
          color: '#e5e7eb',
          fontSize: '13px',
          fontWeight: 600
        }
      },
      labels: {
        formatter: function(val) {
          if (val >= 1000000) {
            return 'Rp ' + (val/1000000).toFixed(1) + 'jt';
          } else if (val >= 1000) {
            return 'Rp ' + (val/1000).toFixed(0) + 'k';
          }
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
      },
      style: {
        fontSize: '13px'
      }
    },
    legend: {
      position: 'top',
      horizontalAlign: 'right',
      fontSize: '13px',
      fontWeight: 600,
      labels: {
        colors: '#e5e7eb'
      },
      markers: {
        radius: 12
      }
    }
  };
  
  new ApexCharts(document.querySelector("#chart-monitoring"), options).render();
});
</script>
@endsection