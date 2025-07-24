<nav class="navbar navbar-expand-lg border-bottom" id="mainNavbar"> {{-- Remove bg-light and navbar-light here as theme will manage it --}}
    <div class="container-fluid">
        <button class="btn btn-primary" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                {{-- Dark Mode Toggle Button --}}
                <li class="nav-item ms-lg-3">
                    <button class="btn btn-link nav-link" id="darkModeToggle" aria-label="Toggle dark mode">
                        <i id="darkModeIcon" class="bi"></i> {{-- Icon will be set by JavaScript --}}
                    </button>
                </li>

                {{-- Account Profile and Logout Dropdown --}}
                @auth {{-- Tampilkan hanya jika pengguna sudah login --}}
                <li class="nav-item dropdown ms-lg-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{-- Contoh: Ikon pengguna atau avatar kecil --}}
                        <i class="bi bi-person-circle me-2"></i>
                        {{ Auth::user()->name ?? 'Pengguna' }} {{-- Tampilkan nama pengguna atau 'Pengguna' jika tidak ada --}}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                        <h6 class="dropdown-header">{{ Auth::user()->email ?? 'Email Pengguna' }}</h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-person-fill me-2"></i> Profil
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
                @endauth

                {{-- Opsional: Tampilkan link Login/Register jika belum login --}}
                @guest
                <li class="nav-item ms-lg-3">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                </li> --}}
                @endguest
            </ul>
        </div>
    </div>
</nav>