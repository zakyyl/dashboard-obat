@extends('layouts.app')

@section('title', 'Stok Obat Masuk')

@section('content')
<div class="container py-4 text-white min-vh-100">
    <h2 class="mb-4 text-center">
        GRAFIK STOK OBAT MASUK
        <br>
        <small class="text-muted">
            {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }}
            s/d
            {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
        </small>
    </h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form id="searchForm" method="GET" action="{{ route('obat.stok-barang-masuk') }}"
                class="row gy-2 gx-3 align-items-end">

                <div class="col-md-auto">
                    <label for="start_date" class="form-label mb-0">Dari Tanggal:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="form-control">


                </div>

                <div class="col-md-auto">
                    <label for="end_date" class="form-label mb-0">Sampai Tanggal:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>

                <div class="col-md-auto">
                    <label for="kode_brng" class="form-label mb-0">Cari Kode / Nama Obat:</label>
                    <input type="text" id="kode_brng" name="kode_brng" class="form-control"
                        placeholder="Ketik kode atau nama..." value="{{ $kodeBrng ?? '' }}">
                </div>

                <div class="col-md-auto">
                    <button type="button" id="searchBtn" class="btn btn-warning">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @if ($data->count() === 0)
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle-fill me-1"></i> Tidak ada data obat masuk.
            </div>
            @else
            <div style="max-height: 500px; overflow-y: auto;">
                <div id="stokChartContainer" style="min-height: 400px;"></div>
            </div>
            @endif
        </div>
    </div>

    @if ($hasMore)
    <div class="text-center mt-3 mb-3">
        <button id="loadMoreBtn" class="btn btn-outline-warning">
            <i class="bi bi-arrow-down-circle"></i> Muat Lebih Banyak
        </button>
    </div>
    @endif


    @if ($data->count() > 0)
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover table-sm align-middle text-white">
                <thead class="table-dark">
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Jenis</th>
                        <th class="text-end">Jumlah Masuk</th>
                        {{-- <th class="text-end">Total (Rp)</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @php
                    $grandJumlah = 0;
                    $grandTotal = 0;
                    @endphp
                    @foreach ($data as $item)
                    @php
                    $grandJumlah += $item->jumlah;
                    $grandTotal += $item->total;
                    @endphp
                    <tr>
                        <td>{{ $item->kode_brng }}</td>
                        <td>{{ $item->nama_brng }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ $item->namajenis }}</td>
                        <td class="text-end">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        {{-- <td class="text-end">{{ number_format($item->total, 0, ',', '.') }}</td> --}}
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th colspan="4" class="text-start">TOTAL</th>
                        <th class="text-end" data-total="{{ $grandJumlah }}">{{ number_format($grandJumlah, 0, ',', '.')
                            }}</th>
                    </tr>
                </tfoot>

            </table>

        </div>
    </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
            let stokApexChart;
            let currentData = @json($data->values() ?? []);
            let currentPage = 1;
            let isLoading = false;

            const searchForm = document.getElementById('searchForm');
            const searchBtn = document.getElementById('searchBtn');
            const loadMoreBtn = document.getElementById('loadMoreBtn');

            function getThemeColors() {
                const style = getComputedStyle(document.documentElement);
                return {
                    bodyColor: style.getPropertyValue('--bs-body-color').trim() || '#fff',
                    borderColor: style.getPropertyValue('--navbar-border').trim() || '#ccc'
                };
            }

            function createApexChart(data) {
                const themeColors = getThemeColors();
                const dataArray = Array.isArray(data) ? data : Object.values(data || []);
                const chartHeight = Math.min(dataArray.length * 45, 1200);

                const kategoriObat = dataArray.map(item => item?.nama_brng ?? '-');
                const jumlahMasuk = dataArray.map(item => item?.jumlah ?? 0);

                // const options = {
                //     series: [{
                //         name: 'Jumlah Masuk',
                //         data: jumlahMasuk
                //     }],
                //     chart: {
                //         type: 'bar',
                //         height: Math.max(chartHeight, 400),
                //         toolbar: { show: false },
                //         foreColor: themeColors.bodyColor
                //     },
                //     colors: ['#799EFF'], 
                //             fill: {
                //                 type: 'gradient',
                //                 gradient: {
                //                     shade: 'light',
                //                     type: 'horizontal',
                //                     gradientToColors: ['#FFCC00'], 
                //                     shadeIntensity: 0.5,
                //                     opacityFrom: 0.9,
                //                     opacityTo: 0.9,
                //                     stops: [0, 100]
                //                 }
                //             },
                //     plotOptions: {
                //         bar: { horizontal: true, barHeight: '80%', borderRadius: 3 }
                //     },
                //     dataLabels: {
                //         enabled: true,
                //         style: { colors: ['#fff'], fontSize: '12px' }
                //     },
                //     xaxis: {
                //         categories: kategoriObat,
                //         labels: { style: { colors: themeColors.bodyColor } }
                //     },
                //     tooltip: {
                //         y: { formatter: val => val + ' unit' }
                //     }
                // };

                const options = {
                    series: [{
                        name: 'Jumlah Masuk',
                        data: jumlahMasuk
                    }],
                    chart: {
                        type: 'bar',
                        height: Math.max(chartHeight, 400),
                        toolbar: {
                            show: false
                        },
                        foreColor: themeColors.bodyColor,
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },

                    colors: ['#093FB4'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'horizontal',
                            gradientToColors: ['#FFCC00'],
                            shadeIntensity: 0.5,
                            opacityFrom: 0.9,
                            opacityTo: 0.9,
                            stops: [0, 100]
                        }
                    },

                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '80%',
                            borderRadius: 3
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            colors: ['#fefce8'],
                            fontSize: '12px'
                        }
                    },
                    xaxis: {
                        categories: kategoriObat,
                        labels: {
                            style: {
                                colors: themeColors.bodyColor
                            }
                        },
                        axisBorder: {
                            color: themeColors.borderColor
                        },
                        axisTicks: {
                            color: themeColors.borderColor
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: themeColors.bodyColor
                            }
                        },
                        axisBorder: {
                            color: themeColors.borderColor
                        },
                        axisTicks: {
                            color: themeColors.borderColor
                        }
                    },
                    grid: {
                        borderColor: themeColors.borderColor
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: val => val + ' unit'
                        },
                        theme: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' :
                            'light'
                    }
                };

                const chartElement = document.querySelector("#stokChartContainer");
                if (stokApexChart) stokApexChart.destroy();
                stokApexChart = new ApexCharts(chartElement, options);
                stokApexChart.render();
            }

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function () {
    if (isLoading) return;
    isLoading = true;
    loadMoreBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Memuat...';
    loadMoreBtn.disabled = true;

    const formData = new FormData(searchForm);
    formData.append('page', currentPage + 1);

    fetch(searchForm.action + '?' + new URLSearchParams(formData), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        currentData = currentData.concat(data.data);
        createApexChart(currentData);
        currentPage = data.nextPage - 1;

        // ====== TABEL: Tambahkan baris baru ======
        const tbody = document.querySelector('table tbody');
        const tfoot = document.querySelector('table tfoot');
        let addedJumlah = 0;

        data.data.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.kode_brng}</td>
                <td>${item.nama_brng}</td>
                <td>${item.satuan}</td>
                <td>${item.namajenis}</td>
                <td class="text-end">${Number(item.jumlah).toLocaleString('id-ID')}</td>
            `;
            tbody.appendChild(row);
            addedJumlah += parseFloat(item.jumlah);
        });

        // ====== Update total jumlah di tfoot ======
        const jumlahCell = tfoot.querySelector('th.text-end');
        if (jumlahCell) {
            const existingJumlah = parseFloat(jumlahCell.dataset.total || 0);
            const newJumlah = existingJumlah + addedJumlah;
            jumlahCell.dataset.total = newJumlah;
            jumlahCell.innerText = newJumlah.toLocaleString('id-ID');
        }

        // ====== Periksa apakah masih ada data ======
        if (!data.hasMore) loadMoreBtn.style.display = 'none';

        loadMoreBtn.innerHTML = '<i class="bi bi-arrow-down-circle"></i> Muat Lebih Banyak';
        loadMoreBtn.disabled = false;
        isLoading = false;
    })
    .catch(() => {
        loadMoreBtn.innerHTML = '<i class="bi bi-arrow-down-circle"></i> Muat Lebih Banyak';
        loadMoreBtn.disabled = false;
        isLoading = false;
    });
});

            }

            searchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                searchForm.submit();
            });

            document.getElementById('kode_brng').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchBtn.click();
                }
            });

            if (currentData.length > 0) {
                createApexChart(currentData);
            }
        });
</script>
@endsection