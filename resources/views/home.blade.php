@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid bg-dark text-white py-4 min-vh-100">
    <!-- Statistik -->
    <div class="row text-center mb-4">
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold">{{ $pasienHariIni }}</h3>
            <p>Pasien Hari ini</p>
        </div>
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold"></h3>
            <p>Pasien Baru Hari ini</p>
        </div>
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold"></h3>
            <p>Kunjungan Hari ini</p>
        </div>
        <div class="col-md-3 mb-3">
            <h3 class="text-warning fw-bold"></h3>
            <p>Kunjungan Bulan ini</p>
        </div>
    </div>

    @php
    $namaBulan =
    [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    @endphp

    <!-- Card Besar -->
    <div class="card bg-dark text-white shadow mb-4">
        <div class="card-header border-bottom border-secondary">
            <h5 class="mb-0 fw-bold text-warning">RAWAT JALAN PERBULAN</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach ($rawatJalanPerBulan as $item)
                <div class="col-sm-6 col-md-3">
                    <div class="card bg-secondary text-white text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-calendar-month fs-2 mb-2"></i>
                            <h6>{{ $namaBulan[$item->bulan] }}</h6>
                            <p class="fw-bold">{{ $item->jumlah }} Kunjungan</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection