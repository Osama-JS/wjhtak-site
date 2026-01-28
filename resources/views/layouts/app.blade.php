<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'My Trip')) - Admin Dashboard</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset(\App\Models\Setting::get('site_favicon', 'images/favicon.png')) }}">

    <!-- Global Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
     <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

     <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('vendor/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('vendor/perfect-scrollbar/js/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>


    <!-- Global Variables for Template -->
    <script>
        window.bootstrap = bootstrap;
        var dlabConfig = {
            typography: "poppins",
            version: "light",
            layout: "horizontal",
            primary: "color_1",
            headerBg: "color_1",
            navheaderBg: "color_1",
            sidebarBg: "color_1",
            sidebarStyle: "full",
            sidebarPosition: "fixed",
            headerPosition: "fixed",
            containerLayout: "full",
        };
    </script>

    <!-- Custom Stylesheet -->
    <link href="{{ asset('vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/aos/css/aos.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/metismenu/css/metisMenu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/toastr/css/toastr.min.css') }}" rel="stylesheet">

    <!-- Icons -->
    <link href="{{ asset('icons/simple-line-icons/css/simple-line-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/font-awesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/material-design-iconic-font/css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/themify-icons/css/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/line-awesome/css/line-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/avasta/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/flaticon/flaticon.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/flaticon_1/flaticon_1.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/icomoon/icomoon.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/bootstrap-icons/font/bootstrap-icons.css') }}" rel="stylesheet">

    <!-- Vite CSS -->
    @vite(['resources/css/app.css'])

    <!-- Custom Admin CSS -->
    <link href="{{ asset('css/admin-custom.css') }}" rel="stylesheet">

    @stack('styles')
</head>
@php
    $theme_version = $_COOKIE['version'] ?? 'light';
    $typography = $_COOKIE['typography'] ?? 'poppins';
    $layout = $_COOKIE['layout'] ?? 'vertical';
    $nav_headerbg = $_COOKIE['navheaderBg'] ?? 'color_1';
    $headerbg = $_COOKIE['headerBg'] ?? 'color_1';
    $sidebarStyle = $_COOKIE['sidebarStyle'] ?? 'full';
    $sidebarPosition = $_COOKIE['sidebarPosition'] ?? 'fixed';
    $headerPosition = $_COOKIE['headerPosition'] ?? 'fixed';
    $containerLayout = $_COOKIE['containerLayout'] ?? 'full';
    $primary = $_COOKIE['primary'] ?? 'color_1';
@endphp
<body
    data-typography="{{ $typography }}"
    data-theme-version="{{ $theme_version }}"
    data-layout="{{ $layout }}"
    data-nav-headerbg="{{ $nav_headerbg }}"
    data-headerbg="{{ $headerbg }}"
    data-sidebar-style="{{ $sidebarStyle }}"
    data-sidebar-position="{{ $sidebarPosition }}"
    data-header-position="{{ $headerPosition }}"
    data-container-layout="{{ $containerLayout }}"
    data-primary="{{ $primary }}"
>

    <!-- Preloader -->
    @include('partials.preloader')

    <!-- Main wrapper -->
    <div id="main-wrapper">
        <!-- Nav header -->
        @include('partials.nav-header')

        <!-- Header -->
        @include('partials.header')

        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Content body -->
        <div class="content-body">
            <div class="container-fluid">
                @yield('page-header')

                <!-- Display flash messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Main content -->
                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        @include('partials.footer')
    </div>

    <!-- Vite JS -->
    @vite(['resources/js/app.js'])

    <!-- Custom Admin JS -->
    <script src="{{ asset('js/admin-custom.js') }}"></script>

    @stack('scripts')
</body>
</html>
