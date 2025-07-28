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
        :root, [data-bs-theme="light"] {
            --bs-body-bg: #f8f9fa;
            --bs-body-color: #212529;
            --navbar-bg: #f8f9fa;
            --navbar-text: #212529;
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
            --bs-body-bg: #212529;
            --bs-body-color: #f8f9fa;
            --navbar-bg: #343a40;
            --navbar-text: #f8f9fa;
            --navbar-border: #495057;
            --sidebar-bg: #343a40;
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
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar {
            background-color: var(--navbar-bg) !important;
            border-bottom: 1px solid var(--navbar-border) !important;
        }
        .navbar .navbar-nav .nav-link,
        .navbar .navbar-brand {
            color: var(--navbar-text) !important;
        }
        .navbar .navbar-nav .nav-link:hover {
            color: color-mix(in srgb, var(--navbar-text) 75%, transparent) !important;
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

        #wrapper {
            overflow-x: hidden;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: 300px;
            margin-left: -300px;
            transition: margin .25s ease-out, background-color 0.3s ease, border-color 0.3s ease;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 0.875rem 1.25rem;
            font-size: 1.2rem;
            border-bottom: 1px solid var(--sidebar-border);
            color: var(--bs-primary) !important;
        }

        #sidebar-wrapper .list-group-item {
            background-color: var(--list-group-item-bg);
            color: var(--list-group-item-text);
        }

        #sidebar-wrapper .list-group-item:hover {
            background-color: var(--list-group-item-hover-bg);
            color: var(--list-group-item-hover-text);
        }

        #sidebar-wrapper .list-group-item.active {
            background-color: var(--list-group-item-active-bg) !important;
            color: var(--list-group-item-active-text) !important;
        }

        #page-content-wrapper {
            min-width: 100vw;
            padding: 0.75rem;
        }

        body.sb-sidenav-toggled #wrapper #sidebar-wrapper {
            margin-left: 0;
        }

        body.sb-sidenav-toggled #wrapper #page-content-wrapper {
            min-width: calc(100vw - 300px);
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                min-width: calc(100vw - 300px);
            }

            body.sb-sidenav-toggled #wrapper #sidebar-wrapper {
                margin-left: -300px;
            }

            body.sb-sidenav-toggled #wrapper #page-content-wrapper {
                min-width: 100vw;
            }
        }

        @media (max-width: 767.98px) {
            #sidebar-wrapper {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 1040;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            }

            body.sb-sidenav-toggled::before {
                content: '';
                position: fixed;
                top: 0;
                left: 300px;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1030;
            }
        }
    </style>
</head>

<body>
<div class="d-flex" id="wrapper">
    @include('layouts.sidebar')

    <div id="page-content-wrapper">
        @include('layouts.navbar')

        <div class="container-fluid mt-4">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const htmlElement = document.documentElement;
        const bodyElement = document.body;
        const sidebarToggle = document.getElementById('sidebarToggle');
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const mainNavbar = document.getElementById('mainNavbar');

        function setTheme(theme) {
            htmlElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);

            if (mainNavbar) {
                mainNavbar.classList.toggle('navbar-dark', theme === 'dark');
                mainNavbar.classList.toggle('navbar-light', theme !== 'dark');
            }

            if (darkModeIcon) {
                darkModeIcon.classList.toggle('bi-sun-fill', theme === 'dark');
                darkModeIcon.classList.toggle('bi-moon-fill', theme !== 'dark');
                darkModeIcon.title = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
            }
        }

        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                bodyElement.classList.toggle('sb-sidenav-toggled');
            });
        }

        function adjustSidebarOnLoad() {
            if (window.innerWidth < 768) {
                bodyElement.classList.remove('sb-sidenav-toggled');
            } else {
                bodyElement.classList.add('sb-sidenav-toggled');
            }
        }

        adjustSidebarOnLoad();
        window.addEventListener('resize', adjustSidebarOnLoad);

        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function() {
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                setTheme(newTheme);
            });
        }

        document.body.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar-wrapper');
            if (
                bodyElement.classList.contains('sb-sidenav-toggled') &&
                !sidebar.contains(e.target) &&
                !e.target.closest('#sidebarToggle')
            ) {
                bodyElement.classList.remove('sb-sidenav-toggled');
            }
        });
    });
</script>
@yield('scripts')
</body>
</html>