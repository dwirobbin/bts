@extends('main')

@push('styles')
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endpush

@section('content')
    <div class="hero-wrap js-fullheight" style="background-image: url('app-src/img/bg_front.jpg');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text js-fullheight align-items-center" data-scrollax-parent="true">
                <div class="col-md-7 ftco-animate">
                    <h1 class="mb-4">Selamat Datang di Website Booking Tiket SpeedBoat</h1>
                    <p class="caps">Website Untuk Jasa Pemesanan Tiket SpeedBoat Palembang <> Jalur</p>
                    @auth
                        @if (auth()->user()->role->name == 'customer')
                            <a href="{{ url('dashboard/orders/create') }}" class="btn btn-primary">Pesan Sekarang!</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery-migrate-3.0.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/jquery.animateNumber.min.js') }}"></script>
    <script src="{{ asset('js/scrollax.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
@endpush
