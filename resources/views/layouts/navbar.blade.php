<nav class="navbar navbar-expand border-bottom px-2 px-md-3" id="mainNavbar">
    <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-warning btn-sm px-2 py-1" id="sidebarToggle" aria-label="Toggle Navigation">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="navbar-brand mb-0 h1 fs-6 d-inline-block d-md-none text-truncate text-warning" style="max-width: 170px;">
                RS Bhayangkara
            </span>
        </div>

        <div class="d-flex align-items-center gap-2 ms-auto">
            <button class="btn btn-link nav-link p-1 text-warning" id="darkModeToggle" aria-label="Toggle dark mode">
                <i id="darkModeIcon" class="bi"></i>
            </button>

            @auth
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center text-truncate py-1 px-2 rounded" href="#" id="navbarDropdownUser"
                        role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="max-width: 220px;">
                        <i class="bi bi-person-circle fs-5 me-1 text-warning"></i>
                        <span class="d-none d-sm-inline small">{{ Auth::user()->name ?? 'Pengguna' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdownUser">
                        <div class="dropdown-header small text-muted d-block d-sm-none">
                            {{ Auth::user()->name ?? 'Pengguna' }}
                        </div>
                        <div class="dropdown-divider d-block d-sm-none"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            @guest
                <a class="btn btn-outline-warning btn-sm" href="{{ route('login') }}">Login</a>
            @endguest
        </div>
    </div>
</nav>
