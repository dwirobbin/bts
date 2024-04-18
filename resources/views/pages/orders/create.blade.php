@extends('main')

@push('styles')
    <!-- Sweet Alert 2 -->
    <link rel="stylesheet" href="{{ asset('app-src/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endpush

@section('content')
    <x-content-header :title="$title" />

    <section class="content">

        <div class="container-fluid">
            @if (session()->has('error'))
                <div>{{ session()->get('error') }}</div>
            @endif
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">

                        <form action="{{ url('dashboard/orders/store') }}" method="post">
                            @csrf
                            <div class="card-body">
                                <h4 class="mb-3">Data Tiket :</h4>
                                <div class="row">
                                    <div class="col-md-6 form-group mb-4">
                                        <x-label for="ticketid-create">
                                            Tiket<span class="text-danger font-weight-bold">*</span>
                                        </x-label>
                                        <x-select id="ticketid-create" name="ticket_data[ticket_id]" emptyOptTxt="Pilih Tiket">
                                            @foreach ($tickets as $ticket)
                                                <option value="{{ $ticket->id }}" @selected(old('ticket_data.ticket_id'))>
                                                    {{ 'SpeedBoat: ' . $ticket->speedBoat->name . ', Rute: ' . $ticket->street->from_route . ' - ' . $ticket->street->to_route . ', Rp.' . number_format($ticket->price, 0, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </x-select>
                                        <x-error name="ticket_data.ticket_id" />
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <x-label for="typeoftrip-create">
                                            Jenis Perjalanan<span class="text-danger font-weight-bold">*</span>
                                        </x-label>
                                        <div class="d-flex flex-row justify-content-between pt-2 ">
                                            <x-radio-button id="go-create" name="ticket_data[trip_type]" label="Sekali Jalan" value="Pergi"
                                                checked="{{ old('ticket_data.trip_type') == 'Pergi' }}" />
                                            <x-radio-button id="goback-create" name="ticket_data[trip_type]" label="Pulang-Pergi"
                                                value="Pulang-Pergi" checked="{{ old('ticket_data.trip_type') == 'Pulang-Pergi' }}" />
                                        </div>
                                        <x-error name="ticket_data.trip_type" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group mb-4" id="godate-div">
                                        <x-label for="godate-create">
                                            Tanggal Pergi<span class="text-danger font-weight-bold">*</span>
                                        </x-label>
                                        <x-date-input id="godate-create" name="ticket_data[go_date]" placeholder="Tanggal Pergi"
                                            value="{{ old('ticket_data.go_date') }}" />
                                        <x-error name="ticket_data.go_date" />
                                    </div>
                                    <div class="col-md-6 form-group mb-4 d-none" id="backdate-div">
                                        <x-label for="backdate-create">
                                            Tanggal Pulang<span class="text-danger font-weight-bold">*</span>
                                        </x-label>
                                        <x-date-input id="backdate-create" name="ticket_data[back_date]" placeholder="Tanggal Pulang"
                                            value="{{ old('ticket_data.back_date') }}" />
                                        <x-error name="ticket_data.back_date" />
                                    </div>
                                </div>

                                <hr />

                                <div class="d-flex justify-content-between pb-2">
                                    <h4 class="d-inline-flex">Data Penumpang :</h4>
                                    <x-button class="btn-danger btn-sm ml-auto mr-2" id="rm-row">
                                        <i class="fas fa-minus"></i> Baris
                                    </x-button>
                                    <x-button class="btn-primary btn-sm" id="add-row">
                                        <i class="fas fa-plus"></i> Baris
                                    </x-button>
                                </div>
                                <div id="first-passenger">
                                    <div class="row" id="passenger-div">
                                        <div class="col-md-4 form-group mb-4">
                                            <x-label for="passengername-create">
                                                Nama Penumpang<span class="text-danger font-weight-bold">*</span>
                                            </x-label>
                                            <x-input id="passengername-create" name="passenger_data[name][]" placeholder="Nama Penumpang"
                                                value="{{ old('passenger_data.name.0') }}" />
                                            <x-error name="passenger_data.name.0" />
                                        </div>

                                        <div class="col-md-4 form-group mb-4">
                                            <x-label for="ktpnumber-create">
                                                No. KTP<span class="text-danger font-weight-bold">*</span>
                                            </x-label>
                                            <x-input id="ktpnumber-create" name="passenger_data[ktp_number][]" placeholder="KTP"
                                                value="{{ old('passenger_data.ktp_number.0') }}" />
                                            <x-error name="passenger_data.ktp_number.0" />
                                        </div>

                                        <div class="col-md-4 form-group mb-4">
                                            <x-label for="passengergender-create">
                                                Jenis Kelamin<span class="text-danger font-weight-bold">*</span>
                                            </x-label>
                                            <x-select id="passengergender-create" name="passenger_data[gender][]" emptyOptTxt="Jenis Kelamin">
                                                <option value="Laki-laki" @selected(old('passenger_data.gender.0'))>Laki-laki</option>
                                                <option value="Perempuan" @selected(old('passenger_data.gender.1'))>Perempuan</option>
                                            </x-select>
                                            <x-error name="passenger_data.gender.0" />
                                        </div>
                                    </div>
                                </div>

                                <div id="more-passenger"></div>

                                <hr />

                                <div class="form-group row justify-content-between mt-1">
                                    <x-button class="btn-info col-sm-4" id="check-price-btn">
                                        Cek total harga yang harus dibayar
                                    </x-button>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <x-input id="totalprice-create" name="payment_data[total_price]" placeholder="Total Harga"
                                                :disabled="true" value="{{ old('payment_data.total_price') }}" />
                                            <x-input type="hidden" id="totalprice-send" name="payment_data[total_price]"
                                                value="{{ old('payment_data.total_price') }}" />
                                        </div>
                                        <x-error name="payment_data.total_price" />
                                    </div>
                                </div>

                                <hr />

                                <h4 class="mt-4 mb-3">Data Pembayaran :</h4>
                                <div class="row">
                                    <div class="col-md-4 form-group mb-4">
                                        <x-label for="selectedpaymentmethod-create">
                                            Metode Pembayaran<span class="text-danger font-weight-bold">*</span>
                                        </x-label>
                                        <x-select id="selectedpaymentmethod-create" name="payment_data[paymentmethod_id]"
                                            emptyOptTxt="Pilih Metode Pembayaran">
                                        </x-select>
                                        <x-error name="payment_data.paymentmethod_id" />
                                    </div>

                                    <div class="col-md-4 form-group mb-4">
                                        <x-label for="senderaccountname-create">
                                            Nama Rekening Pengirim<span class="text-danger font-weight-bold">*</span>
                                        </x-label>
                                        <x-input id="senderaccountname-create" name="payment_data[senderaccount_name]" placeholder="Nama Lengkap"
                                            value="{{ old('payment_data.senderaccount_name') }}" />
                                        <x-error name="payment_data.senderaccount_name" />
                                    </div>

                                    <div class="col-md-4 form-group mb-4">
                                        <x-label for="senderaccountnumber-create">
                                            Nomor Rekening Pengirim<span class="text-danger font-weight-bold">*</span>
                                        </x-label>
                                        <x-input id="senderaccountnumber-create" name="payment_data[senderaccount_number]"
                                            placeholder="Nomor Pengirim" value="{{ old('payment_data.senderaccount_number') }}" />
                                        <x-error name="payment_data.senderaccount_number" />
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer clearfix">
                                <a href="{{ url('dashboard/index') }}" class="btn btn-sm btn-warning float-left">
                                    <i class="fas fa-arrow-left"></i> Halaman utama
                                </a>
                                <x-button type="submit" class="float-right btn-primary">
                                    <i class="fas fa-save"></i> Buat Pesanan
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <!-- Sweet Alert 2 -->
    <script src="{{ asset('app-src/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    @if (session()->has('error'))
        <script>
            let Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                title: `{{ session()->get('error') }}`,
                icon: 'error',
            })
        </script>
    @endif

    <script>
        window.onload = init;

        $(function() {
            $("input[id='senderaccountnumber-create']").on('input', function(e) {
                $(e.target).val($(e.target).val().replace(/[^\d.]/ig, "")); // only number
            });
        })

        const formatRp = (angka, prefix) => {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
        }

        function init() {
            const checkPriceBtn = document.getElementById("check-price-btn");
            const ticketId = document.getElementById("ticketid-create");
            const totalPrice = document.getElementById('totalprice-create');
            const totalPriceSend = document.getElementById('totalprice-send');

            checkPriceBtn.addEventListener('click', function() {
                var totalPassengerName = $('input[name="passenger_data[name][]"]').length;
                var radioVal = $("input[name='ticket_data[trip_type]']:checked").val();

                fetch(
                        `/dashboard/tickets/check-price?ticket_id=${ticketId.value}&trip_type=${radioVal}&total_passenger=${totalPassengerName}`
                    )
                    .then(response => {
                        return response.json();
                    })
                    .then(res => {
                        totalPrice.value = formatRp((res.txt).toString());
                        totalPriceSend.value = formatRp((res.txt).toString());
                    })
                    .catch(res => {
                        totalPrice.value = "Harga tiket tidak dapat ditampilkan";
                    })
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#ticketid-create').change(function() {
                var $paymentMethod = $('#selectedpaymentmethod-create');

                $.get("{{ url('dashboard/orders/get-payment-methods') }}", {
                    'ticket_id': $(this).val()
                }, function(data) {
                    $paymentMethod.html('<option value="" selected>Pilih Metode Pembayaran</option>');
                    $.each(data, function(id, value) {
                        $paymentMethod.append(`<option value="${id}">${value}</option>`)
                    });
                });
            });

            $('#add-row').click(function() {
                $('#first-passenger #passenger-div').clone()
                    .find("input[id='passengername-create']").val('').end()
                    .find("input[id='ktpnumber-create']").val('').end()
                    .find("select[id='passengergender-create']").val('').end()
                    .appendTo('#more-passenger');
            });

            $('#rm-row').click(function() {
                $('#more-passenger #passenger-div').last().remove();
            });

            $('#go-create').click(function() {
                $('#backdate-div').addClass('d-none');
            });

            $('#goback-create').click(function() {
                $('#backdate-div').removeClass('d-none');
            });

            if ($('#goback-create').is(':checked')) {
                $('#backdate-div').removeClass('d-none');
            } else {
                $('#backdate-div').addClass('d-none')
            }
        })
    </script>
@endpush
