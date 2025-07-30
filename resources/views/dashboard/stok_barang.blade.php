@extends('layouts.app')

@section('title', 'Stok Obat Minimal')

@section('content')
    <div class="container py-4 text-white min-vh-100">
        <h2 class="mb-4 text-center">
            GRAFIK STOK OBAT 
            <br>
            <small class="text-muted">
                {{ now()->translatedFormat('F Y') }}
            </small>
        </h2>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <!-- Modal Warning -->
                <div class="modal fade" id="loadingWarningModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-dark text-white">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Data yang akan ditampilkan cukup banyak dan mungkin memerlukan waktu untuk memuat.</p>
                                <p class="mb-0">Apakah Anda yakin ingin menampilkan semua data yang sesuai pencarian?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="button" id="confirmSearchBtn" class="btn btn-danger">Ya, Tampilkan</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading Modal -->
                <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body text-center p-4">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mb-0">Sedang memuat data...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="searchForm" method="GET" action="{{ route('obat.stok-barang') }}" class="row gy-2 gx-3 align-items-end">
                    <div class="col-md-auto">
                        <label for="jenis" class="form-label mb-0">Filter Jenis Barang:</label>
                        <select name="jenis" id="jenis" class="form-select">
                            <option value="">Semua Jenis</option>
                            @foreach ($listJenis as $j)
                                <option value="{{ $j->nama }}" {{ $jenisFilter == $j->nama ? 'selected' : '' }}>
                                    {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-auto">
                        <label for="search" class="form-label mb-0">Cari Nama Obat:</label>
                        <input type="text" id="search" name="search" class="form-control"
                            placeholder="Ketik nama obat..." value="{{ $searchKeyword ?? '' }}">
                    </div>

                    <div class="col-md-auto">
                        <button type="button" id="searchBtn" class="btn btn-warning">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                @if (isset($data) && $data->count() === 0)
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle-fill me-1"></i> Tidak ada data obat yang ditemukan.
                    </div>
                @else
                    <!-- Info Data -->
                    <div id="dataInfo" class="mb-3">
                        @if(isset($currentCount) && isset($totalItems))
                            <small class="text-muted">
                                Menampilkan {{ $currentCount }} dari {{ $totalItems }} data
                            </small>
                        @endif
                    </div>

                    <div style="max-height: 500px; overflow-y: auto;">
                        <div id="stokChartContainer" style="min-height: 400px;"></div>
                    </div>

                    <!-- Load More Button -->
                    @if(isset($hasMore) && $hasMore)
                        <div class="text-center mt-3">
                            <button id="loadMoreBtn" class="btn btn-outline-warning">
                                <i class="bi bi-arrow-down-circle"></i> Muat Lebih Banyak
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let stokApexChart;
            let currentData = @json($data->values() ?? []);
            let currentPage = 1;
            let isLoading = false;

            const searchForm = document.getElementById('searchForm');
            const searchBtn = document.getElementById('searchBtn');
            const confirmSearchBtn = document.getElementById('confirmSearchBtn');
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const loadingWarningModal = new bootstrap.Modal(document.getElementById('loadingWarningModal'));
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));

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
                const stokMinimal = dataArray.map(item => item?.stokminimal ?? 0);
                const totalStok = dataArray.map(item => item?.total_stok ?? 0);

                const options = {
                    series: [{
                            name: 'Total Stok',
                            data: totalStok
                        },
                        {
                            name: 'Stok Minimal',
                            data: stokMinimal
                        }
                    ],
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

                    colors: ['#093FB4', '#ED3500'], 
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: 'horizontal',
                            gradientToColors: ['#4FC3F7', '#FF8A80'], 
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
                        theme: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light'
                    }
                };

                const chartElement = document.querySelector("#stokChartContainer");
                if (stokApexChart) stokApexChart.destroy();
                stokApexChart = new ApexCharts(chartElement, options);
                stokApexChart.render();
            }

            function updateDataInfo(currentCount, totalItems) {
                const dataInfo = document.getElementById('dataInfo');
                if (dataInfo) {
                    dataInfo.innerHTML = `<small class="text-muted">Menampilkan ${currentCount} dari ${totalItems} data</small>`;
                }
            }

            function checkSearchCriteria() {
                const searchValue = document.getElementById('search').value.trim();
                const jenisValue = document.getElementById('jenis').value;
                
                // Show warning modal if search criteria is likely to return many results
                if (searchValue.length > 0 && searchValue.length < 3 || jenisValue !== '') {
                    return true; // Show modal
                }
                return false; // Direct search
            }

            // Search button click handler
            searchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (checkSearchCriteria()) {
                    loadingWarningModal.show();
                } else {
                    performSearch();
                }
            });

            // Confirm search from modal
            confirmSearchBtn.addEventListener('click', function() {
                loadingWarningModal.hide();
                performSearch();
            });

            // Perform search function
            function performSearch() {
                if (isLoading) return;
                
                isLoading = true;
                loadingModal.show();
                currentPage = 1;
                
                searchForm.submit();
            }

            // Load more functionality
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
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
                    .then(response => response.json())
                    .then(data => {
                        // Append new data to current data
                        currentData = currentData.concat(data.data);
                        
                        // Update chart with new data
                        createApexChart(currentData);
                        
                        // Update info
                        updateDataInfo(data.currentCount, data.totalItems);
                        
                        // Update page counter
                        currentPage = data.nextPage - 1;
                        
                        // Show/hide load more button
                        if (!data.hasMore) {
                            loadMoreBtn.style.display = 'none';
                        }
                        
                        isLoading = false;
                        loadMoreBtn.innerHTML = '<i class="bi bi-arrow-down-circle"></i> Muat Lebih Banyak';
                        loadMoreBtn.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        isLoading = false;
                        loadMoreBtn.innerHTML = '<i class="bi bi-arrow-down-circle"></i> Muat Lebih Banyak';
                        loadMoreBtn.disabled = false;
                    });
                });
            }

            // Initialize chart
            if (currentData.length > 0) {
                createApexChart(currentData);
            }

            // Theme observer
            const observer = new MutationObserver(() => {
                if (currentData.length > 0) {
                    createApexChart(currentData);
                }
            });
            observer.observe(document.documentElement, {
                attributes: true
            });

            // Enter key handler for search input
            document.getElementById('search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchBtn.click();
                }
            });
        });
    </script>
@endsection