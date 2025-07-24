<!DOCTYPE html>
<html lang="en" data-bs-theme="light"> {{-- Default theme set to light --}}

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- ApexCharts (if needed for your dashboard content) --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        /* CSS Variables for theming */
        :root, [data-bs-theme="light"] {
            --bs-body-bg: #f8f9fa; /* Default light background */
            --bs-body-color: #212529; /* Default light text */

            --navbar-bg: #f8f9fa; /* Light navbar background */
            --navbar-text: #212529; /* Light navbar text */
            --navbar-border: #dee2e6;

            --sidebar-bg: #ffffff;
            --sidebar-text: #212529;
            --sidebar-border: #dee2e6;
            --list-group-item-bg: #ffffff;
            --list-group-item-text: #212529;
            --list-group-item-hover-bg: #f8f9fa;
            --list-group-item-hover-text: #212529;
            --list-group-item-active-bg: var(--bs-primary);
            --list-group-item-active-text: #ffffff;
        }

        [data-bs-theme="dark"] {
            --bs-body-bg: #212529; /* Dark background */
            --bs-body-color: #f8f9fa; /* Dark text */

            --navbar-bg: #343a40; /* Dark navbar background */
            --navbar-text: #f8f9fa; /* Dark navbar text */
            --navbar-border: #495057;

            --sidebar-bg: #343a40; /* Darker sidebar background */
            --sidebar-text: #f8f9fa;
            --sidebar-border: #495057;
            --list-group-item-bg: #343a40;
            --list-group-item-text: #f8f9fa;
            --list-group-item-hover-bg: #495057;
            --list-group-item-hover-text: #f8f9fa;
            --list-group-item-active-bg: var(--bs-primary);
            --list-group-item-active-text: #ffffff;
        }

        body {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transition */
        }

        /* Navbar Styling */
        .navbar {
            background-color: var(--navbar-bg) !important;
            border-bottom: 1px solid var(--navbar-border) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .navbar .navbar-nav .nav-link,
        .navbar .navbar-brand {
            color: var(--navbar-text) !important;
            transition: color 0.3s ease;
        }
        .navbar .navbar-brand span.text-primary { /* Keep brand primary color */
            color: var(--bs-primary) !important;
        }
        .navbar .navbar-nav .nav-link:hover {
            color: color-mix(in srgb, var(--navbar-text) 75%, transparent) !important; /* Slightly transparent on hover */
        }
        .navbar .navbar-nav .dropdown-menu {
            background-color: var(--navbar-bg);
            border-color: var(--navbar-border);
        }
        .navbar .navbar-nav .dropdown-item {
            color: var(--navbar-text);
        }
        .navbar .navbar-nav .dropdown-item:hover {
            background-color: var(--list-group-item-hover-bg);
            color: var(--list-group-item-hover-text);
        }
        .navbar .navbar-nav .dropdown-divider {
            border-top: 1px solid var(--navbar-border);
        }

        #wrapper {
            overflow-x: hidden;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: 300px; /* Lebar sidebar baru */
            margin-left: -300px; /* Default: hidden on all screens, sesuaikan dengan lebar baru */
            transition: margin .25s ease-out, background-color 0.3s ease, border-color 0.3s ease;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
            border-bottom: 1px solid var(--sidebar-border);
            color: var(--bs-primary) !important;
        }

        #sidebar-wrapper .list-group {
            width: 100%;
        }

        #sidebar-wrapper .list-group-item {
            background-color: var(--list-group-item-bg);
            color: var(--list-group-item-text);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        #sidebar-wrapper .list-group-item:hover {
            background-color: var(--list-group-item-hover-bg);
            color: var(--list-group-item-hover-text);
        }

        #sidebar-wrapper .list-group-item.active {
            background-color: var(--list-group-item-active-bg) !important;
            color: var(--list-group-item-active-text) !important;
        }

        #sidebar-wrapper .list-group-item span {
            color: var(--list-group-item-text);
        }
        #sidebar-wrapper .list-group-item span.text-primary {
            color: var(--bs-primary) !important;
        }

        #page-content-wrapper {
            min-width: 100vw;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            overflow-x: hidden;
        }

        /* Saat sidebar terbuka (di-toggle) - ini berlaku untuk SEMUA ukuran layar */
        body.sb-sidenav-toggled #wrapper #sidebar-wrapper {
            margin-left: 0; /* Sidebar terlihat */
        }

        body.sb-sidenav-toggled #wrapper #page-content-wrapper {
            min-width: calc(100vw - 300px); /* Konten menyusut saat sidebar terbuka, sesuaikan dengan lebar baru */
            width: 100%; /* Memastikan tidak ada scrollbar horizontal yang tidak diinginkan */
        }


        /* Adjust for larger screens (e.g., desktops >= 768px) */
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0; /* Sidebar terlihat secara default di desktop */
            }

            #page-content-wrapper {
                min-width: calc(100vw - 300px); /* Konten mengambil sisa lebar saat sidebar terlihat, sesuaikan dengan lebar baru */
                width: 100%; /* Memastikan tidak ada scrollbar horizontal yang tidak diinginkan */
            }

            /* Di desktop, ketika di-toggle, sidebar akan disembunyikan */
            body.sb-sidenav-toggled #wrapper #sidebar-wrapper {
                margin-left: -300px; /* Sesuaikan dengan lebar baru */
            }

            body.sb-sidenav-toggled #wrapper #page-content-wrapper {
                min-width: 100vw; /* Konten mengambil seluruh lebar saat sidebar tersembunyi */
            }
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        @include('layouts.sidebar') {{-- Memasukkan sidebar --}}

        <div id="page-content-wrapper">
            @include('layouts.navbar') {{-- Memasukkan navbar --}}

            <div class="container-fluid mt-4">
                @yield('content') {{-- Placeholder untuk konten halaman --}}
            </div>
        </div>
    </div>

    {{-- Bootstrap Bundle with Popper --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const htmlElement = document.documentElement;
            const bodyElement = document.body;
            const sidebarToggle = document.getElementById('sidebarToggle');
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            const mainNavbar = document.getElementById('mainNavbar');

            // --- Theme / Dark Mode Logic ---
            function setTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);

                if (mainNavbar) {
                    if (theme === 'dark') {
                        mainNavbar.classList.remove('navbar-light');
                        mainNavbar.classList.add('navbar-dark');
                    } else {
                        mainNavbar.classList.remove('navbar-dark');
                        mainNavbar.classList.add('navbar-light');
                    }
                }

                if (darkModeIcon) { // Ensure darkModeIcon exists
                    if (theme === 'dark') {
                        darkModeIcon.classList.remove('bi-moon-fill');
                        darkModeIcon.classList.add('bi-sun-fill');
                        darkModeIcon.title = 'Switch to Light Mode';
                    } else {
                        darkModeIcon.classList.remove('bi-sun-fill');
                        darkModeIcon.classList.add('bi-moon-fill');
                        darkModeIcon.title = 'Switch to Dark Mode';
                    }
                }
            }

            // Load theme from localStorage or default to light
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);

            // --- Sidebar Toggle Logic ---
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    bodyElement.classList.toggle('sb-sidenav-toggled');
                });
            }

            // --- Initial Sidebar State on Load ---
            function adjustSidebarOnLoad() {
                if (window.innerWidth < 768) {
                    // On mobile, ensure sidebar is hidden by default
                    bodyElement.classList.remove('sb-sidenav-toggled');
                } else {
                    // On desktop, ensure sidebar is shown by default
                    bodyElement.classList.add('sb-sidenav-toggled');
                }
            }

            adjustSidebarOnLoad(); // Call on initial load
            window.addEventListener('resize', adjustSidebarOnLoad); // Adjust on window resize

            // --- Dark Mode Toggle Event Listener ---
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                    setTheme(newTheme);
                });
            }
        });
    </script>
</body>

</html>