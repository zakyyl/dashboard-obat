<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="bg-white border-end shadow-sm" id="sidebar-wrapper" style="min-height: 100vh; width: 250px;">
    <div class="sidebar-heading text-center py-4 fw-bold fs-5 text-primary border-bottom">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard Eksekutif
    </div>

    <div class="list-group list-group-flush mt-2">
        <a href="{{ url('/home') }}"
           class="list-group-item list-group-item-action py-3 px-4 {{ request()->is('home') ? 'active bg-primary text-white' : 'text-dark' }}">
            <i class="bi bi-house-door-fill me-2"></i> Home
        </a>
        <a class="list-group-item list-group-item-action py-3 px-4 d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse" href="#obatMenu" role="button" aria-expanded="false" aria-controls="obatMenu">
            <span class="{{ request()->is('obat/*') ? 'text-primary' : 'text-dark' }}">
                <i class="bi bi-capsule me-2"></i> Obat
            </span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ request()->is('obat/*') || request()->is('settings') ? 'show' : '' }}" id="obatMenu">
    <a href="{{ url('/obat/stok-barang') }}"
       class="list-group-item list-group-item-action ps-5 py-2 d-flex align-items-center gap-2 {{ request()->is('obat/stok-barang') ? 'active bg-primary text-white' : 'text-dark' }}">
        <i class="bi bi-box-seam"></i> Obat Minimal
    </a>
    <a href="{{ url('/obat/obat-saat-ini') }}"
   class="list-group-item list-group-item-action ps-5 py-2 d-flex align-items-center gap-2 {{ request()->is('obat/obat-saat-ini') ? 'active bg-primary text-white' : 'text-dark' }}">
    <i class="bi bi-capsule-pill"></i> Obat Saat Ini
</a>

</div>


    </div>
</div>
