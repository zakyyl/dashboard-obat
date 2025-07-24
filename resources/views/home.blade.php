@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Selamat Datang!</h6>
        </div>
        <div class="card-body">
            <p>Anda telah berhasil login ke dashboard.</p>
            <p>Ini adalah halaman utama Anda setelah autentikasi.</p>
            <p>Sekarang Anda bisa mengakses fitur-fitur di sidebar.</p>
        </div>
    </div>
@endsection