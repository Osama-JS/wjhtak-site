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

    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

    <style>
        /* تحسين شكل منطقة الرفع */
        .dropzone.border-dashed {
            border: 2px dashed #007bff;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .dz-message {
            font-weight: bold;
            color: #555;
        }
    </style>




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

<body data-typography="poppins" data-theme-version="dark" data-sidebar-style="full" data-layout="vertical" data-nav-headerbg="color_1" data-headerbg="color_1" data-sibebarbg="color_1" data-sidebar-position="fixed" data-header-position="fixed" data-container="wide" direction="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" data-primary="color_1">


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

    <script>
        window.Translations = {
            confirm_title: "{{ __('Confirmation') }}",
            confirm_yes: "{{ __('Yes') }}",
            confirm_cancel: "{{ __('Cancel') }}",
            loading_text: "{{ __('Loading...') }}",
        };
    </script>

    <!-- Custom Admin JS -->
    <script src="{{ asset('js/admin-custom.js') }}"></script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
