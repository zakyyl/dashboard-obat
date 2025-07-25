<div class="shadow-sm" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 fw-bold fs-5 text-primary border-bottom">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard Eksekutif
    </div>

    <div class="list-group list-group-flush mt-2">
        <a href="{{ url('/home') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('home') ? 'active bg-primary text-white' : '' }}">
            <i class="bi bi-house-door-fill me-2"></i> Home
        </a>
        <a href="{{ url('/obat/stok-barang') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('obat/stok-barang*') ? 'active bg-primary text-white' : '' }}">
            <i class="bi bi-box-seam-fill me-2"></i> Obat
        </a>
        <a href="{{ url('/dashboard/poli') }}"
   class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('dashboard/poli*') ? 'active bg-primary text-white' : '' }}">
    <i class="bi bi-hospital-fill me-2"></i> Poli
</a>
        <a href="{{ url('/ranap') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('ranap*') ? 'active bg-primary text-white' : '' }}">
            <i class="bi bi-hospital-fill me-2"></i> Ranap
        </a>
        <a href="{{ url('/labor') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('labor*') ? 'active bg-primary text-white' : '' }}">
            <i class="bi bi-clipboard-data-fill me-2"></i> Labor
        </a>
        <a href="{{ url('/radiologi') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('radiologi*') ? 'active bg-primary text-white' : '' }}">
            <i class="bi bi-body-text me-2"></i> Radiologi
        </a>
        <a href="{{ url('/pengajuan-klaim') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('pengajuan-klaim*') ? 'active bg-primary text-white' : '' }}">
            <i class="bi bi-journal-check me-2"></i> Pengajuan Klaim
        </a>
    </div>
</div>