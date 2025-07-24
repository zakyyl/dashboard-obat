@extends('layouts.app')

@section('title', 'Stok Obat Minimal')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 text-center">📊 Grafik Stok Obat Minimal & Saat Ini</h2>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" class="row gy-2 gx-3 align-items-center">
                    <div class="col-md-auto">
                        <label for="jenis" class="form-label mb-0">Filter Jenis Barang:</label>
                        <select name="jenis" id="jenis" onchange="this.form.submit()" class="form-select">
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
                        <input type="text" id="search" class="form-control" placeholder="Ketik nama obat...">
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                @php
    $chartHeight = min($data->count() * 45, 1200); // maksimal tinggi 1200px
@endphp
                <div style="max-height: 500px; overflow-y: auto;">
    <div id="stokChartContainer" style="height: {{ $data->count() * 45 }}px; min-height: 400px;">
        {{-- ApexCharts akan dirender di sini --}}
    </div>
</div>
            </div>
        </div>
    </div>

    <script>
        let stokApexChart;

        function getComputedThemeColors() {
            const style = getComputedStyle(document.documentElement);
            return {
                bodyColor: style.getPropertyValue('--bs-body-color').trim(),
                borderColor: style.getPropertyValue('--navbar-border').trim(),
            };
        }

        function createApexChart(data) {
    const themeColors = getComputedThemeColors();

    const dataArray = Array.isArray(data) ? data : Object.values(data || []);
    // console.log("Data Array isi:", dataArray);

    // Ambil data untuk dua series: stok minimal & total stok
    const kategoriObat = dataArray.map(item => item?.nama_brng ?? '-');
    const stokMinimal = dataArray.map(item => item?.stokminimal ?? 0);
    const totalStok = dataArray.map(item => item?.total_stok ?? 0);

    const options = {
    series: [
        { name: 'Stok Minimal', data: stokMinimal },
        { name: 'Total Stok', data: totalStok }
    ],
    chart: {
        type: 'bar',
        height: 930,
        toolbar: { show: false },
        background: 'transparent',
        foreColor: themeColors.bodyColor
    },
    plotOptions: {
        bar: {
            horizontal: true,
            barHeight: '80%'
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 1,
        colors: ['#fff']
    },
    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: val => val + ' unit'
        },
        theme: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light'
    },
    xaxis: {
        categories: kategoriObat,
        labels: { style: { colors: themeColors.bodyColor } },
        axisBorder: { show: true, color: themeColors.borderColor },
        axisTicks: { show: true, color: themeColors.borderColor }
    },
    yaxis: {
        labels: { style: { colors: themeColors.bodyColor } },
        title: { text: undefined }
    },
    legend: {
        labels: { colors: themeColors.bodyColor },
        markers: { fillColors: ['#FF4560', '#00E396'] }
    },
    colors: ['#FF4560', '#00E396'],
    grid: {
        borderColor: themeColors.borderColor,
        xaxis: { lines: { show: false } },
        yaxis: { lines: { show: true } }
    }
};


    const chartElement = document.querySelector("#stokChartContainer");

    if (stokApexChart) stokApexChart.destroy();

    stokApexChart = new ApexCharts(chartElement, options);
    stokApexChart.render();
        }
        createApexChart({!! json_encode($data->values()) !!});
        // --- Dark Mode Chart Update Listener ---
        const observer = new MutationObserver((mutationsList) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                    if (stokApexChart && stokApexChart.w && stokApexChart.w.config && stokApexChart.w.config.series.length === 2) {
    const kategoriObat = stokApexChart.w.globals.labels;
    const stokMinimal = stokApexChart.w.config.series[0].data;
    const totalStok = stokApexChart.w.config.series[1].data;

    const combined = kategoriObat.map((nama_brng, i) => ({
        nama_brng,
        stokminimal: stokMinimal[i] ?? 0,
        total_stok: totalStok[i] ?? 0
    }));

    createApexChart(combined);
}
else {
                        console.warn("Chart not yet initialized or data not available for theme change.");
                    }
                }
            }
        });
        observer.observe(document.documentElement, {
            attributes: true
        });


        // --- Search/Filter Logic ---
        let debounceTimer;
        document.getElementById('search').addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const keyword = this.value;

            debounceTimer = setTimeout(() => {
                if (keyword.length >= 2 || keyword.length === 0) {
                    fetch(`{{ route('obat.search-obat') }}?q=${encodeURIComponent(keyword)}&jenis={{ $jenisFilter }}`)
                        .then(res => {
                            if (!res.ok) {
                                throw new Error('Network response was not ok ' + res.statusText);
                            }
                            return res.json();
                        })
                        .then(data => {
                            // PASTIKAN data dari fetch adalah array
                            createApexChart(data);
                        })
                        .catch(error => console.error('Error fetching search data:', error));
                }
            }, 300);
        });
    </script>
@endsection