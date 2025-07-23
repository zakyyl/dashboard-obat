<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <style>
        #sidebar-wrapper {
            width: 250px;
        }

        #page-content-wrapper {
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        @include('layouts.sidebar')

        <div id="page-content-wrapper" class="container-fluid mt-4">
            @yield('content')
        </div>
    </div>
</body>

</html>
