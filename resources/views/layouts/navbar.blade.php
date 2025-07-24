<nav class="navbar navbar-expand-lg border-bottom" id="mainNavbar"> {{-- Remove bg-light and navbar-light here as theme will manage it --}}
    <div class="container-fluid">
        <button class="btn btn-primary" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                {{-- You can add more navigation items here --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Dropdown
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </li>
                {{-- Dark Mode Toggle Button --}}
                <li class="nav-item ms-lg-3">
                    <button class="btn btn-link nav-link" id="darkModeToggle" aria-label="Toggle dark mode">
                        <i id="darkModeIcon" class="bi"></i> {{-- Icon will be set by JavaScript --}}
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>
