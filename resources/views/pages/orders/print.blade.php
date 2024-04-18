@extends('main')

@section('content')
    <div class="wrapper">
        <section class="invoice" style="margin: 30px; border: none">
            <div class="row">
                <div class="col-12">
                    <h2 class="page-header">
                        Booking Tiket Speedboat | BTS
                    </h2>
                </div>
            </div>

            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    <br>
                    <br>
                    <address>
                        <strong>BTS</strong><br>
                        Jl. Silaberanti No.123<br>
                        Kec. Seberang Ulu II, Kota. Palembang<br>
                        Phone: 082252948897<br>
                        Email: bts@gmail.com
                    </address>
                </div>
                <div class="col-sm-4 invoice-col">
                    <br>
                    <br>
                    <address>
                        @isset($order->user->phone_number)
                            <strong>{{ $order->user->name }}</strong>
                        @endisset

                        @isset($order->user->phone_number)
                            <br> Phone: {{ $order->user->phone_number }}
                        @endisset

                        @isset($order->user->email)
                            <br> Email: {{ $order->user->email }}
                        @endisset

                    </address>
                </div>
                <div class="col-sm-4 invoice-col">
                    <br>
                    <br>
                    <b>Invoice #{{ $order->order_code }}</b><br>
                    <b>Order ID:</b> {{ $order->order_code }}<br>
                    @isset($order->transaction->paymentMethod->method)
                        <b>Metode Pembayaran :</b> {{ $order->transaction->paymentMethod->method }}
                        <br>
                    @endisset

                    <b>Payment date:</b> {{ $order->transaction->updated_at }}<br>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="3%">No</th>
                                <th width="">Nama Penumpang</th>
                                <th width="">Speedboat</th>
                                <th width="">Rute</th>
                                <th width="">Tanggal Pergi</th>
                                <th width="">Tanggal Pulang</th>
                                <th width="">Jam Berangkat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->passengers as $passenger)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $passenger->name }}</td>
                                    <td>{{ $order->ticket->speedboat->name }}</td>
                                    <td>
                                        {{ $order->ticket->street->from_route }} - {{ $order->ticket->street->to_route }}
                                    </td>
                                    @isset($order->go_date)
                                        <td>{{ $order->go_date }}</td>
                                    @else
                                        <td>Hanya Pulang</td>
                                    @endisset
                                    @isset($order->back_date)
                                        <td>{{ $order->back_date }}</td>
                                    @else
                                        <td>Hanya Pergi</td>
                                    @endisset
                                    <td>{{ $order->ticket->updated_at }} WIB</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12">
                    <h6 class="font-weight-bold">
                        Total: Rp {{ number_format($order->transaction->total, 0, ',', '.') }}
                    </h6>
                    <p class="lead">
                        Your Payment Was Successful &nbsp; &nbsp;<i class="fas fa-check"></i> <br>
                    </p>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        window.print();
    </script>
@endpush
