@extends('main')

@push('styles')
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
@endpush

@section('content')
    <x-content-header :title="$title" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @if (preg_match('[admin|owner|driver]', auth()->user()->role->name))
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h4 class="font-weight-bold">Tiket</h4>
                                <p>Terdapat {{ $tickets->count() }} Tiket yang telah terdaftar!</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-card"></i>
                            </div>
                            <a href="{{ url('dashboard/tickets/index') }}" class="small-box-footer">
                                Klik untuk melihat daftar tiket <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    @if (preg_match('[owner]', auth()->user()->role->name))
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h4 class="font-weight-bold">Pesanan</h4>
                                    <p>Terdapat {{ $orders->count() }} Pesanan yang masuk ke keranjang!</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="{{ url('dashboard/orders/history/index') }}" class="small-box-footer">
                                    Klik untuk melihat daftar pesanan <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h4 class="font-weight-bold">Transaksi</h4>
                                    <p>Terdapat {{ $transactions->count() }} Transaksi belum dikonfirmasi!</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="{{ url('dashboard/transactions/history/index') }}" class="small-box-footer">
                                    Klik untuk melihat daftar transaksi <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h4 class="font-weight-bold">Keluhan/Obrolan</h4>
                                    <p>{{ $complaints->count() }} Keluhan/Obrolan belum ditanggapi!</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pull-request"></i>
                                </div>
                                <a href="{{ url('dashboard/orders/history/index') }}" class="small-box-footer">
                                    Klik untuk lihat keluhan/obrolan <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            @if (preg_match('[customer]', auth()->user()->role->name))
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h4 class="font-weight-bold">Tiket</h4>
                                <p>Lebih dari {{ $tickets->count() - 1 }} Tiket yang dapat kamu pesan sekarang!</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-card"></i>
                            </div>
                            <a href="{{ url('dashboard/tickets/index') }}" class="small-box-footer">
                                Klik untuk melihat daftar tiket <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h4 class="font-weight-bold">Pesanan</h4>
                                <p>Ayo, pesan tiket speedboat mu sekarang!</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ url('dashboard/orders/create') }}" class="small-box-footer">
                                Klik untuk memesan tiket <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h4 class="font-weight-bold">Transaksi</h4>
                                <p>Terdapat {{ $transactions->count() }} Transaksi belum dikonfirmasi!</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="{{ url('dashboard/transactions/history/index') }}" class="small-box-footer">
                                Klik untuk melihat transaksimu <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h4 class="font-weight-bold">Keluhan/Obrolan</h4>
                                <p>{{ $complaints->count() }} Keluhan/Obrolan belum kamu tanggapi!</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pull-request"></i>
                            </div>
                            <a href="{{ url('dashboard/orders/history/index') }}" class="small-box-footer">
                                Klik untuk lihat keluhan/obrolan <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
