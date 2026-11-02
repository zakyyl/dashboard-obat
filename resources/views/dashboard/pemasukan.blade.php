@extends('layouts.app')

@section('title', 'Data Pemasukan')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mt-md-4">
    <h2 class="mb-3 mb-md-4 text-center text-warning fs-4 fs-md-2">
      <i class="bi bi-graph-up-arrow me-2"></i>Grafik & Data Pemasukan
    </h2>

    {{-- Card Filter & Chart --}}
    <div class="card bg-dark text-white border-0 shadow-sm mb-4">
      <div class="card-header border-bottom border-secondary bg-transparent">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <h5 class="mb-0 fw-bold text-warning fs-6 fs-md-5">📈 TREN PEMASUKAN</h5>
        </div>
        <form method="GET" action="{{ route('pemasukan.index') }}" class="w-100">
          <div class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
              <label for="start_date" class="form-label text-light mb-1 small">Dari Tanggal:</label>
              <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                class="form-control form-control-sm bg-dark text-light border-secondary">
            </div>
            <div class="col-6 col-md-3">
              <label for="end_date" class="form-label text-light mb-1 small">Sampai Tanggal:</label>
              <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                class="form-control form-control-sm bg-dark text-light border-secondary">
            </div>
            <div class="col-6 col-md-2">
              <label for="kode_pemasukan_filter" class="form-label text-light mb-1 small">Jenis:</label>
              <select name="kode_pemasukan_filter" id="kode_pemasukan_filter" class="form-select form-select-sm bg-dark text-light border-secondary">
                <option value="">Semua Jenis</option>
                @foreach($masterPemasukan as $kode)
                <option value="{{ $kode->id }}" {{ request('kode_pemasukan_filter')==$kode->id ? 'selected' : '' }}>
                  {{ $kode->nama_pemasukan }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="col-6 col-md-2">
              <label for="group_by" class="form-label text-light mb-1 small">Tampilkan:</label>
              <select name="group_by" id="group_by" class="form-select form-select-sm bg-dark text-light border-secondary">
                <option value="day" {{ request('group_by', 'month')=='day' ? 'selected' : '' }}>Per Hari</option>
                <option value="month" {{ request('group_by', 'month')=='month' ? 'selected' : '' }}>Per Bulan</option>
                <option value="year" {{ request('group_by', 'month')=='year' ? 'selected' : '' }}>Per Tahun</option>
              </select>
            </div>
            <div class="col-6 col-md-1 d-grid">
              <button type="submit" class="btn btn-sm btn-warning">
                <i class="bi bi-funnel me-1"></i>Filter
              </button>
            </div>
            <div class="col-6 col-md-1 d-grid">
              <a href="{{ route('pemasukan.index') }}" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
              </a>
            </div>
          </div>
        </form>
      </div>
      <div class="card-body p-2 p-md-3">
        <div id="chart-pemasukan" style="min-height: 350px; width: 100%;"></div>
      </div>
    </div>

    {{-- Card Data Tabel --}}
    <div class="card bg-dark text-white border-0 shadow-sm mb-4">
      <div class="card-header border-bottom border-secondary bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold text-warning fs-6 fs-md-5">
          <i class="bi bi-cash-stack me-2"></i>Data Pemasukan
        </h5>
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalTambah">
          <i class="bi bi-plus-circle me-1"></i>Tambah Pemasukan
        </button>
      </div>

      <div class="card-body p-2 p-md-3">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="table-responsive">
          <table class="table table-dark table-striped table-bordered align-middle text-light mb-0" style="min-width: 620px;">
            <thead class="table-secondary text-dark text-center">
              <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 25%">Jenis Pemasukan</th>
                <th style="width: 30%">Keterangan</th>
                <th style="width: 25%">Jumlah (Rp)</th>
              </tr>
            </thead>
            <tbody style="font-size: 0.875rem;">
              @forelse($data as $key => $item)
              <tr>
                <td class="text-center">{{ $data->firstItem() + $key }}</td>
                <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td class="fw-medium">{{ $item->nama_pemasukan ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td class="text-end fw-bold text-success">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-3">Belum ada data pemasukan.</td>
              </tr>
              @endforelse
            </tbody>
            <tfoot class="table-secondary text-dark">
              <tr>
                <th colspan="3" class="text-end">TOTAL KESELURUHAN:</th>
                <th></th>
                <th class="text-end fw-bold text-success">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</th>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="d-flex justify-content-center align-items-center mt-3">
          <div class="d-flex gap-2">
            @if ($data->onFirstPage())
            <button class="btn btn-sm btn-secondary disabled" disabled>
              <i class="bi bi-chevron-left me-1"></i>Previous
            </button>
            @else
            <a href="{{ $data->previousPageUrl() }}" class="btn btn-sm btn-warning">
              <i class="bi bi-chevron-left me-1"></i>Previous
            </a>
            @endif
            @if ($data->hasMorePages())
            <a href="{{ $data->nextPageUrl() }}" class="btn btn-sm btn-warning">
              Next<i class="bi bi-chevron-right ms-1"></i>
            </a>
            @else
            <button class="btn btn-sm btn-secondary disabled" disabled>
              Next<i class="bi bi-chevron-right ms-1"></i>
            </button>
            @endif
          </div>
        </div>
      </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('pemasukan.store') }}" method="POST" class="modal-content bg-dark text-light border-secondary">
      @csrf
      <div class="modal-header border-secondary">
        <h5 class="modal-title fs-6 text-warning" id="modalTambahLabel">Tambah Data Pemasukan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-3">
        <div class="form-group mb-3">
          <label for="kode_pemasukan_id" class="form-label small text-light mb-1">Jenis Pemasukan</label>
          <select name="kode_pemasukan_id" id="kode_pemasukan_id" class="form-select form-select-sm bg-dark text-light border-secondary" required>
            <option value="">-- Pilih Jenis Pemasukan --</option>
            @foreach($masterPemasukan as $kode)
            <option value="{{ $kode->id }}">{{ $kode->nama_pemasukan }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group mb-3">
          <label for="tanggal" class="form-label small text-light mb-1">Tanggal</label>
          <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm bg-dark text-light border-secondary" required>
        </div>

        <div class="form-group mb-3">
          <label for="jumlah" class="form-label small text-light mb-1">Jumlah (Rp)</label>
          <input type="number" step="0.01" name="jumlah" id="jumlah" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Masukkan jumlah" required>
        </div>

        <div class="form-group mb-3">
          <label for="keterangan" class="form-label small text-light mb-1">Keterangan</label>
          <textarea name="keterangan" id="keterangan" class="form-control form-control-sm bg-dark text-light border-secondary" rows="2" placeholder="Contoh: Pembayaran pasien rawat jalan"></textarea>
        </div>
      </div>

      <div class="modal-footer border-secondary">
        <button type="submit" class="btn btn-warning btn-sm">Simpan</button>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
      </div>
    </form>
  </div>
</div>

@php
use Carbon\Carbon;

$categories = [];
$seriesData = [];
$groupBy = request('group_by', 'month');

if ($groupBy == 'day') {
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  var groupBy = '{{ $groupBy }}';
  var categoriesCount = {{ count($categories) }};
  
  var options = {
    series: [{
      name: 'Pemasukan',
      data: @json($seriesData)
    }],
    chart: {
      type: 'area',
      height: 350,
      foreColor: '#e5e7eb',
      toolbar: { show: false }
    },
    colors: ['#ffc107'],
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.5,
        opacityTo: 0.1,
        stops: [0, 90, 100]
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
      style: {
        fontSize: '10px',
        fontWeight: 'bold',
        colors: ['#000']
      },
      background: {
        enabled: true,
        foreColor: '#000',
        borderRadius: 3,
        padding: 3,
        opacity: 0.85,
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
      borderColor: '#374151'
    },
    xaxis: {
      categories: @json($categories),
      labels: {
        style: { fontSize: '11px', colors: '#d1d5db' },
        rotate: groupBy === 'day' && categoriesCount > 7 ? -45 : 0
      }
    },
    yaxis: {
      labels: {
        formatter: function(val) {
          if (val >= 1000000) return 'Rp ' + (val/1000000).toFixed(1) + 'jt';
          if (val >= 1000) return 'Rp ' + (val/1000).toFixed(0) + 'k';
          return 'Rp ' + val.toLocaleString('id-ID');
        },
        style: { fontSize: '11px', colors: '#e5e7eb' }
      }
    },
    tooltip: {
      y: {
        formatter: function(val) {
          return 'Rp ' + val.toLocaleString('id-ID');
        }
      },
      theme: 'dark'
    },
    responsive: [{
      breakpoint: 576,
      options: {
        chart: { height: 260 },
        dataLabels: { enabled: false },
        xaxis: { labels: { rotate: -45, style: { fontSize: '10px' } } }
      }
    }]
  };
  
  var chart = new ApexCharts(document.querySelector("#chart-pemasukan"), options);
  chart.render();
});
</script>
@endsection
