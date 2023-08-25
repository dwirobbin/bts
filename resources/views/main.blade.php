<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') . ' | ' . isset($title) }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('app-src/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('app-src/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('app-src/css/adminlte.min.css') }}">
    <!-- Page specific style -->
    @stack('styles')

</head>

<body
    class="hold-transition {{ $show_sidebar === true ? 'sidebar-mini' : 'sidebar-closed sidebar-collapse overflow-scroll' }} layout-fixed layout-navbar-fixed layout-footer-fixed">

    @if (request()->is('/'))
        <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
            <div class="container">
                <a class="navbar-brand" href="./">
                    <h1 class="font-weight-bold text-white ms-2">BTS</h1>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="oi oi-menu"></span> Menu
                </button>

                <div class="collapse navbar-collapse" id="ftco-nav">
                    <ul class="navbar-nav ml-auto">
                        @auth
                            <li class="nav-item {{ Request::is('dashboard') ? 'active ' : '' }}">
                                <a href="{{ url('dashboard/index') }}" class="nav-link">Dashboard</a>
                            </li>
                            <li class="nav-item pt-1">
                                <form action="{{ url('auth/logout') }}" method="POST" class="my-3">
                                    @csrf
                                    <button type="submit" class="btn bg-transparent text-white">
                                        Keluar
                                    </button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item {{ Request::is('register') ? 'active ' : '' }}">
                                <a href="{{ url('auth/register') }}" class="nav-link">Register</a>
                            </li>
                            <li class="nav-item {{ Request::is('login') ? 'active ' : '' }}">
                                <a href="{{ url('auth/login') }}" class="nav-link">Login</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <div class="content-wrapper"
            style="background: url('{{ asset('app-src/img/bg_apps.png') }}') no-repeat center center fixed; background-size: cover;">
            @yield('content')
        </div>
    @else
        <div class="wrapper">
            @include('layout.partials.navbar')

            @include('layout.partials.sidebar')

            <div class="content-wrapper"
                style="background: url('{{ asset('app-src/img/bg_apps.png') }}') no-repeat center center fixed; background-size: cover;">
                @yield('content')
            </div>

            @if (!request()->is('auth/*'))
                @include('layout.partials.footer')
            @endif
        </div>
    @endif

    <!-- jQuery -->
    <script src="{{ asset('app-src/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('app-src/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('app-src/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('app-src/js/adminlte.min.js') }}"></script>
    <!-- Toggle Switch Dark Or Light -->
    <script>
        var toggleSwitch = document.querySelector('.custom-switch input[type="checkbox"]');
        var currentTheme = localStorage.getItem('theme');
        var mainHeader = document.querySelector('.main-header');

        if (currentTheme) {
            if (currentTheme === 'dark') {
                if (!document.body.classList.contains('dark-mode')) {
                    document.body.classList.add("dark-mode");
                }
                if (mainHeader.classList.contains('navbar-light')) {
                    mainHeader.classList.add('navbar-dark');
                    mainHeader.classList.remove('navbar-light');
                }
                toggleSwitch.checked = true;
            }
        }

        function switchTheme(e) {
            if (e.target.checked) {
                if (!document.body.classList.contains('dark-mode')) {
                    document.body.classList.add("dark-mode");
                }
                if (mainHeader.classList.contains('navbar-light')) {
                    mainHeader.classList.add('navbar-dark');
                    mainHeader.classList.remove('navbar-light');
                }
                localStorage.setItem('theme', 'dark');
            } else {
                if (document.body.classList.contains('dark-mode')) {
                    document.body.classList.remove("dark-mode");
                }
                if (mainHeader.classList.contains('navbar-dark')) {
                    mainHeader.classList.add('navbar-light');
                    mainHeader.classList.remove('navbar-dark');
                }
                localStorage.setItem('theme', 'light');
            }
        }

        toggleSwitch.addEventListener('change', switchTheme, false);
    </script>

    <!-- Page specific script -->
    @stack('scripts')
</body>

</html>
