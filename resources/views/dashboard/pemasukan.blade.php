@extends('layouts.app')

@section('title', 'Data Pemasukan')

@section('content')
<section class="content">
  <div class="container-fluid">

    <h2 class="mb-4 text-center">
      <i class="bi bi-bar-chart-fill me-2"></i>Grafik Pemasukan
      <br>
    </h2>
    
    <div class="card card-warning card-outline mb-4">
      <div class="card-header border-bottom">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h5 class="mb-0 fw-bold text-warning">GRAFIK PEMASUKAN</h5>
          <form method="GET" action="{{ route('pemasukan.index') }}" class="d-flex gap-2 flex-wrap align-items-end">
            <div>
              <label for="start_date" class="form-label mb-1 small">Dari Tanggal:</label>
              <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" style="width: 160px;">
            </div>
            <div>
              <label for="end_date" class="form-label mb-1 small">Sampai Tanggal:</label>
              <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" style="width: 160px;">
            </div>
            <div>
              <label for="group_by" class="form-label mb-1 small">Tampilkan:</label>
              <select name="group_by" id="group_by" class="form-select form-select-sm" style="width: 130px;">
                <option value="day" {{ request('group_by', 'month') == 'day' ? 'selected' : '' }}>Per Hari</option>
                <option value="month" {{ request('group_by', 'month') == 'month' ? 'selected' : '' }}>Per Bulan</option>
                <option value="year" {{ request('group_by', 'month') == 'year' ? 'selected' : '' }}>Per Tahun</option>
              </select>
            </div>
            <button type="submit" class="btn btn-sm btn-warning">
              <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('pemasukan.index') }}" class="btn btn-sm btn-secondary">
              <i class="fas fa-redo"></i> Reset
            </a>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div id="chart-pemasukan"></div>
      </div>
    </div>

    {{-- Card Utama Tabel --}}
    <div class="card card-warning card-outline">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Data Pemasukan</h3>
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalTambah">
          <i class="fas fa-plus"></i> Tambah Pemasukan
        </button>
      </div>

      <div class="card-body">
        {{-- Notifikasi --}}
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        {{-- Filter Tabel --}}
        <form method="GET" action="{{ route('pemasukan.index') }}" class="row mb-3">
          <input type="hidden" name="start_date" value="{{ request('start_date') }}">
          <input type="hidden" name="end_date" value="{{ request('end_date') }}">
          <input type="hidden" name="group_by" value="{{ request('group_by') }}">
        
        </form>

        {{-- Tabel Pemasukan --}}
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead class="table-warning text-center">
              <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Jumlah (Rp)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($data as $key => $item)
                <tr>
                  <td class="text-center">{{ $key + 1 }}</td>
                  <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                  <td>{{ $item->keterangan ?? '-' }}</td>
                  <td class="text-end fw-bold text-success">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted">Belum ada data pemasukan.</td>
                </tr>
              @endforelse
            </tbody>
            <tfoot class="table-warning">
              <tr>
                <th colspan="2" class="text-end">TOTAL:</th>
                <th></th>
                <th class="text-end fw-bold">Rp {{ number_format($data->sum('jumlah'), 0, ',', '.') }}</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

  </div>
</section>

{{-- Modal Tambah Data --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('pemasukan.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="modalTambahLabel">Tambah Data Pemasukan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="form-group mb-3">
          <label for="tanggal">Tanggal</label>
          <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>
        <div class="form-group mb-3">
          <label for="jumlah">Jumlah (Rp)</label>
          <input type="number" step="0.01" name="jumlah" id="jumlah" class="form-control" placeholder="Masukkan jumlah" required>
        </div>
        <div class="form-group mb-3">
          <label for="keterangan">Keterangan</label>
          <textarea name="keterangan" id="keterangan" class="form-control" placeholder="Contoh: Pembayaran pasien rawat jalan"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-warning">Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

@php
use Carbon\Carbon;

$categories = [];
$seriesData = [];
$groupBy = request('group_by', 'month');

// Grouping berdasarkan pilihan user
if ($groupBy == 'day') {
    // Per Hari
    $grouped = $chartData->groupBy(function($item) {
        return Carbon::parse($item->tanggal)->format('Y-m-d');
    })->map(function($items) {
        return $items->sum('jumlah');
    })->sortKeys();
    
    foreach ($grouped as $tanggal => $jumlah) {
        $date = Carbon::parse($tanggal);
        $categories[] = $date->format('d M Y');
        $seriesData[] = $jumlah;
    }
} elseif ($groupBy == 'year') {
    // Per Tahun
    $grouped = $chartData->groupBy(function($item) {
        return Carbon::parse($item->tanggal)->format('Y');
    })->map(function($items) {
        return $items->sum('jumlah');
    })->sortKeys();
    
    foreach ($grouped as $tahun => $jumlah) {
        $categories[] = $tahun;
        $seriesData[] = $jumlah;
    }
} else {
    // Per Bulan (default)
    $grouped = $chartData->groupBy(function($item) {
        return Carbon::parse($item->tanggal)->format('Y-m');
    })->map(function($items) {
        return $items->sum('jumlah');
    })->sortKeys();
    
    foreach ($grouped as $bulan => $jumlah) {
        $date = Carbon::parse($bulan . '-01');
        $categories[] = $date->translatedFormat('M Y');
        $seriesData[] = $jumlah;
    }
}
@endphp

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var groupBy = '{{ $groupBy }}';
  var categoriesCount = {{ count($categories) }};
  
  // Chart Pemasukan - Area Chart (Gold Theme)
  var options = {
    series: [{
      name: 'Pemasukan',
      data: @json($seriesData)
    }],
    chart: {
      type: 'area',
      height: 380,
      toolbar: {
        show: true,
        tools: {
          download: true,
          selection: false,
          zoom: false,
          zoomin: false,
          zoomout: false,
          pan: false,
          reset: false
        }
      },
      dropShadow: {
        enabled: true,
        top: 3,
        left: 0,
        blur: 5,
        opacity: 0.15
      }
    },
    colors: ['#ffc107'],
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.5,
        opacityTo: 0.1,
        stops: [0, 90, 100],
        colorStops: [
          {
            offset: 0,
            color: "#ffcc33",
            opacity: 0.7
          },
          {
            offset: 100,
            color: "#fff3cd",
            opacity: 0.1
          }
        ]
      }
    },
    dataLabels: {
      enabled: true,
      formatter: function(val) {
        if (val >= 1000000) {
          return (val/1000000).toFixed(1) + 'jt';
        } else if (val >= 1000) {
          return (val/1000).toFixed(0) + 'k';
        }
        return val.toLocaleString('id-ID');
      },
      offsetY: -5,
      style: {
        fontSize: '11px',
        fontWeight: 'bold',
        colors: ['#000']
      },
      background: {
        enabled: true,
        foreColor: '#000',
        borderRadius: 4,
        padding: 4,
        opacity: 0.9,
        borderWidth: 1,
        borderColor: '#ffc107',
        backgroundColor: '#fff3cd'
      }
    },
    stroke: {
      curve: 'smooth',
      width: 3,
      colors: ['#ffca2c']
    },
    grid: {
      borderColor: '#e7e7e7',
      strokeDashArray: 5,
      padding: {
        top: 0,
        right: 20,
        bottom: 0,
        left: 10
      }
    },
    xaxis: {
      categories: @json($categories),
      labels: {
        style: {
          fontSize: '11px',
          fontWeight: 500
        },
        rotate: groupBy === 'day' && categoriesCount > 7 ? -45 : 0,
        rotateAlways: groupBy === 'day' && categoriesCount > 7
      },
      axisBorder: {
        show: true,
        color: '#ffd54f'
      },
      axisTicks: {
        show: true
      }
    },
    yaxis: {
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
          fontSize: '12px',
          colors: ['#795548']
        }
      },
      title: {
        text: 'Total Pemasukan',
        style: {
          fontSize: '13px',
          fontWeight: 600,
          color: '#ffb300'
        }
      }
    },
    tooltip: {
      enabled: true,
      y: {
        formatter: function(val) {
          return 'Rp ' + val.toLocaleString('id-ID');
        }
      },
      style: {
        fontSize: '13px'
      }
    },
    markers: {
      size: 5,
      colors: ['#ffc107'],
      strokeColors: '#fff',
      strokeWidth: 2,
      hover: {
        size: 7
      }
    }
  };
  
  var chart = new ApexCharts(document.querySelector("#chart-pemasukan"), options);
  chart.render();
});
</script>
@endsection