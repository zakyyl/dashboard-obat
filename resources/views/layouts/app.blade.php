<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Flatpickr Datepicker CSS (Theme Dark) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    <style>
        :root {
            --bs-body-bg: #1c1917;
            --bs-body-color: #fef3c7;
            --navbar-bg: #292524;
            --navbar-text: #fef3c7;
            --navbar-border: #451a03;
            --sidebar-bg: #292524;
            --sidebar-text: #a8a29e;
            --sidebar-border: #451a03;
            --sidebar-heading-bg: #451a03;
            --sidebar-active-bg: #f59e0b;
            --sidebar-active-text: #ffffff;
            --sidebar-hover-bg: #451a03;
            --sidebar-hover-text: #fef3c7;
            --shadow-light: 0 1px 3px 0 rgb(245 158 11 / 0.2), 0 1px 2px -1px rgb(245 158 11 / 0.2);
            --shadow-medium: 0 4px 6px -1px rgb(245 158 11 / 0.25), 0 2px 4px -2px rgb(245 158 11 / 0.25);
            --border-radius: 8px;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 14px;
            line-height: 1.5;
        }

        .navbar {
            background-color: var(--navbar-bg) !important;
            border-bottom: 1px solid var(--navbar-border) !important;
            box-shadow: var(--shadow-light);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1.5rem;
        }

        .navbar .navbar-brand {
            color: var(--navbar-text) !important;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .navbar .nav-link {
            color: var(--navbar-text) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
        }

        .navbar .nav-link:hover {
            background-color: var(--sidebar-hover-bg);
            color: var(--sidebar-hover-text) !important;
        }

        .navbar .dropdown-menu {
            background-color: var(--navbar-bg);
            border: 1px solid var(--navbar-border);
            box-shadow: var(--shadow-medium);
            border-radius: var(--border-radius);
            padding: 0.5rem;
        }

        .navbar .dropdown-item {
            color: var(--navbar-text);
            border-radius: calc(var(--border-radius) - 2px);
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .navbar .dropdown-item:hover {
            background-color: var(--sidebar-hover-bg);
            color: var(--sidebar-hover-text);
        }

        #wrapper {
            overflow-x: hidden;
            min-height: 100vh;
            width: 100%;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: 280px;
            margin-left: -280px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            box-shadow: var(--shadow-medium);
            position: relative;
            z-index: 1000;
        }

        #sidebar-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--sidebar-bg) 100%);
            z-index: -1;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem 1.25rem;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
            color: white !important;
            font-weight: 700;
            font-size: 1.125rem;
            border: none;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 20px rgb(245 158 11 / 0.3);
        }

        #sidebar-wrapper .sidebar-heading i {
            font-size: 1.25rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        #sidebar-wrapper .list-group {
            padding: 1rem 0;
        }

        #sidebar-wrapper .list-group-item {
            background-color: transparent;
            color: var(--sidebar-text);
            border: none;
            border-radius: 0;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            margin: 0 0.75rem 0.25rem;
            border-radius: var(--border-radius);
        }

        #sidebar-wrapper .list-group-item i {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        #sidebar-wrapper .list-group-item:hover:not(.active) {
            background-color: var(--sidebar-hover-bg) !important;
            color: var(--sidebar-hover-text) !important;
            transform: translateX(4px);
        }

        #sidebar-wrapper .list-group-item.active {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%) !important;
            color: var(--sidebar-active-text) !important;
            box-shadow: 0 4px 12px rgb(245 158 11 / 0.4), 0 0 20px rgb(251 191 36 / 0.2);
            transform: none !important;
            border-left: 4px solid #fbbf24;
        }

        #sidebar-wrapper .collapse .list-group-item {
            padding-left: 3rem !important;
            font-size: 0.8rem;
            margin: 0 0.75rem 0.125rem;
            background-color: transparent;
            position: relative;
        }

        #sidebar-wrapper .collapse .list-group-item::before {
            content: '';
            position: absolute;
            left: 2.25rem;
            top: 50%;
            width: 4px;
            height: 4px;
            background-color: var(--sidebar-text);
            border-radius: 50%;
            transform: translateY(-50%);
            opacity: 0.5;
        }

        #sidebar-wrapper .collapse .list-group-item:hover:not(.active) {
            transform: translateX(2px);
        }

        #sidebar-wrapper .collapse .list-group-item.active {
            background: linear-gradient(90deg, transparent 0%, rgba(251, 191, 36, 0.1) 100%) !important;
            border-left: 3px solid #fbbf24;
            transform: none !important;
        }

        #sidebar-wrapper .collapse .list-group-item.active::before {
            background-color: #fbbf24;
            opacity: 1;
            box-shadow: 0 0 8px rgb(251 191 36 / 0.6);
        }

        .sidebar-footer {
            flex-shrink: 0;
            padding: 1rem;
            text-align: center;
            font-size: 0.75rem;
            opacity: 0.6;
            background-color: var(--sidebar-bg);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .bi-chevron-down {
            transition: transform 0.2s ease;
        }

        .collapsed .bi-chevron-down {
            transform: rotate(-90deg);
        }

        #page-content-wrapper {
            width: 100%;
            min-width: 0;
            flex-grow: 1;
            padding: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .container-fluid {
            padding: 1.5rem;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                width: calc(100% - 280px);
                min-width: 0;
            }

            body.sb-sidenav-toggled #wrapper #sidebar-wrapper {
                margin-left: -280px;
            }

            body.sb-sidenav-toggled #wrapper #page-content-wrapper {
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            #sidebar-wrapper {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                height: 100vh;
                z-index: 1050;
                box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
                margin-left: -280px;
                overflow-y: auto;
            }

            body.sb-sidenav-toggled #wrapper #sidebar-wrapper {
                margin-left: 0 !important;
            }

            body.sb-sidenav-toggled #wrapper #page-content-wrapper {
                width: 100% !important;
                min-width: 0 !important;
            }

            body.sb-sidenav-toggled::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.65);
                z-index: 1040;
                backdrop-filter: blur(2px);
            }

            .container-fluid {
                padding: 0.75rem !important;
            }
        }

        .btn-outline-secondary {
            border-color: var(--navbar-border);
            color: var(--navbar-text);
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-color: #f59e0b;
            color: white;
            box-shadow: 0 2px 8px rgb(245 158 11 / 0.3);
        }

        #sidebar-wrapper::-webkit-scrollbar {
            width: 4px;
        }

        #sidebar-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar-wrapper::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #fbbf24, #f59e0b);
            border-radius: 2px;
        }

        #sidebar-wrapper::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #f59e0b, #d97706);
        }

        /* CSS for Dark Theme Pagination */
        .dark-theme-pagination .page-item .page-link {
            background-color: #343a40;
            /* Dark gray background */
            border: 1px solid #495057;
            /* Slightly lighter border */
            color: #ffffff;
            /* White text color */
            margin: 0 2px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .dark-theme-pagination .page-item.active .page-link {
            background-color: #ffc107;
            /* Yellow background for active page */
            border-color: #ffc107;
            /* Yellow border */
            color: #212529;
            /* Dark text for contrast */
        }

        .dark-theme-pagination .page-item .page-link:hover {
            background-color: #495057;
            /* Hover effect */
            border-color: #6c757d;
        }

        .dark-theme-pagination .page-item.disabled .page-link {
            background-color: #212529;
            /* Darker background for disabled links */
            border-color: #343a40;
            color: #6c757d;
            /* Gray text color */
        }
        /* END CSS for Dark Theme Pagination */

        /* Custom Flatpickr Styling (Gold/Dark Theme) */
        .flatpickr-calendar {
            background: #292524 !important;
            border: 1px solid #451a03 !important;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.75) !important;
            border-radius: 8px !important;
            z-index: 99999 !important;
        }
        .flatpickr-calendar .flatpickr-months {
            background: #1c1917 !important;
            border-top-left-radius: 8px !important;
            border-top-right-radius: 8px !important;
        }
        .flatpickr-calendar .flatpickr-month {
            color: #fbbf24 !important;
            fill: #fbbf24 !important;
        }
        .flatpickr-calendar .flatpickr-current-month .cur-month {
            color: #fbbf24 !important;
            font-weight: 600 !important;
        }
        .flatpickr-calendar .flatpickr-current-month input.cur-year {
            color: #fbbf24 !important;
            font-weight: 600 !important;
        }
        .flatpickr-calendar .flatpickr-current-month .numInputWrapper span.arrowUp:after {
            border-bottom-color: #fbbf24 !important;
        }
        .flatpickr-calendar .flatpickr-current-month .numInputWrapper span.arrowDown:after {
            border-top-color: #fbbf24 !important;
        }
        .flatpickr-calendar span.flatpickr-weekday {
            background: #1c1917 !important;
            color: #d1d5db !important;
            font-weight: 600 !important;
        }
        .flatpickr-calendar .flatpickr-day {
            color: #e5e7eb !important;
            border-radius: 6px !important;
        }
        .flatpickr-calendar .flatpickr-day:hover,
        .flatpickr-calendar .flatpickr-day:focus {
            background: #451a03 !important;
            color: #fbbf24 !important;
            border-color: #f59e0b !important;
        }
        .flatpickr-calendar .flatpickr-day.today {
            border-color: #f59e0b !important;
            color: #fbbf24 !important;
        }
        .flatpickr-calendar .flatpickr-day.selected,
        .flatpickr-calendar .flatpickr-day.startRange,
        .flatpickr-calendar .flatpickr-day.endRange {
            background: #f59e0b !important;
            border-color: #f59e0b !important;
            color: #1c1917 !important;
            font-weight: bold !important;
        }
        .flatpickr-calendar .flatpickr-day.prevMonthDay,
        .flatpickr-calendar .flatpickr-day.nextMonthDay {
            color: #6b7280 !important;
        }
        .flatpickr-calendar .flatpickr-prev-month,
        .flatpickr-calendar .flatpickr-next-month {
            color: #fbbf24 !important;
            fill: #fbbf24 !important;
        }
        .flatpickr-calendar .flatpickr-prev-month:hover svg,
        .flatpickr-calendar .flatpickr-next-month:hover svg {
            fill: #f59e0b !important;
        }
        input.flatpickr-input[readonly] {
            cursor: pointer;
            background-color: #212529 !important;
            color: #fef3c7 !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23fbbf24' class='bi bi-calendar3' viewBox='0 0 16 16'%3E%3Cpath d='M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z'/%3E%3Cpath d='M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 14px 14px;
            padding-right: 2.25rem !important;
        }
        input.flatpickr-input[readonly]:focus {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.25) !important;
        }
        @media (max-width: 480px) {
            .flatpickr-calendar {
                max-width: 92vw !important;
            }
        }

    </style>
</head>


<body>
    <div class="d-flex" id="wrapper">
        @include('layouts.sidebar')

        <div id="page-content-wrapper">
            @include('layouts.navbar')

            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr Datepicker JS & Indonesian Locale -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Flatpickr global untuk semua input tanggal
            if (typeof flatpickr !== 'undefined') {
                flatpickr.localize(flatpickr.l10ns.id);
                flatpickr("input[type=date]", {
                    altInput: true,
                    altFormat: "d-m-Y",
                    dateFormat: "Y-m-d",
                    disableMobile: true,
                    locale: "id",
                    allowInput: false
                });
            }

            const bodyElement = document.body;
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    bodyElement.classList.toggle('sb-sidenav-toggled');
                });
            }

            function adjustSidebarOnLoad() {
                if (window.innerWidth < 768) {
                    bodyElement.classList.remove('sb-sidenav-toggled');
                } else {
                    bodyElement.classList.remove('sb-sidenav-toggled');
                }
            }

            adjustSidebarOnLoad();
            window.addEventListener('resize', adjustSidebarOnLoad);
            document.body.addEventListener('click', function(e) {
                const sidebar = document.getElementById('sidebar-wrapper');
                const sidebarToggleBtn = document.getElementById('sidebarToggle');
                if (window.innerWidth < 768 &&
                    bodyElement.classList.contains('sb-sidenav-toggled') &&
                    !sidebar.contains(e.target) &&
                    !sidebarToggleBtn.contains(e.target)) {
                    bodyElement.classList.remove('sb-sidenav-toggled');
                }
            });
            const collapseElements = document.querySelectorAll('[data-bs-toggle="collapse"]');
            collapseElements.forEach(element => {
                element.addEventListener('click', function() {
                    const chevron = this.querySelector('.bi-chevron-down');
                    if (chevron) {
                        setTimeout(() => {
                            chevron.style.transform = this.getAttribute('aria-expanded') ===
                                'true' ?
                                'rotate(0deg)' :
                                'rotate(-90deg)';
                        }, 50);
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
