@extends('main')

@push('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('app-src/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-src/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-src/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Sweet Alert 2 -->
    <link rel="stylesheet" href="{{ asset('app-src/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endpush

@section('content')
    <x-content-header :title="$title" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="transactions-table" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID Booking</th>
                                        <th>Nama</th>
                                        <th>Metode Pmbyrn</th>
                                        <th>Nama Akun Rek.</th>
                                        <th>No. Rek. Plnggn</th>
                                        <th>No. Rek. Tjuan</th>
                                        <th>Total Byar</th>
                                        <th>Bukti Pmbyrn</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('pages.transactions._upload-img')
    @include('pages.transactions._update-status')
@endsection

@push('scripts')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('app-src/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('app-src/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-src/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- Sweet Alert 2 -->
    <script src="{{ asset('app-src/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- bs-custom-file-input -->
    <script src="{{ asset('app-src/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

    <script>
        let Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });

        let Confirm = Swal.mixin({
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            denyButtonText: 'Batal',
            confirmButtonText: 'Ya',
            allowOutsideClick: false,
            customClass: {
                denyButton: 'order-1',
                confirmButton: 'order-2',
            },
        });
    </script>

    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();

            $.fn.dataTable.ext.errMode = 'none';
            $('#transactions-table').DataTable({
                responsive: true,
                lengthChange: true,
                autoWidth: false,
                dom: `
                    <'row'<'col-md-3'l><'col-md-5'B><'col-md-4'f>>
                    <'row'<'col-md-12'tr>>
                    <'row'<'col-md-5'i><'col-md-7'p>>
                `,
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, 'Semua']
                ],
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-sm',
                        pageSize: 'A4',
                        orientation: 'potrait',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(
                                doc.content[1].table.body[0].length + 1
                            ).join('*').split('');
                            doc.defaultStyle.alignment = 'center';
                            doc.styles.tableHeader.alignment = 'center';
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        className: 'btn btn-sm',
                        text: 'Kolom',
                        autoClose: true
                    },
                ],
                pagingType: 'numbers',
                columnDefs: [{
                    targets: [9],
                    searchable: false,
                    orderable: false,
                }, ],
                language: {
                    lengthMenu: 'Tampil _MENU_ data',
                    buttons: {
                        "copyTitle": "Salin ke Papan klip",
                        "copySuccess": {
                            "_": "%d baris disalin ke papan klip"
                        },
                    },
                    search: 'Cari:',
                    zeroRecords: 'Tidak ditemukan data yang sesuai',
                    emptyTable: 'Tidak ada data yang tersedia pada tabel ini',
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoFiltered: '(difilter dari total _MAX_ catatan)',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    loadingRecords: 'Sedang memproses...',
                    processing: 'Memproses...',
                },
                ajax: {
                    url: '{{ url('dashboard/transactions/history/get-data') }}',
                },
                columns: [{
                        name: 'order.order_code',
                        data: 'order.order_code',
                    },
                    {
                        name: 'order.user.name',
                        data: 'order.user.name',
                    },
                    {
                        name: 'payment_method.method',
                        data: 'payment_method.method',
                    },
                    {
                        name: 'name_account',
                        data: 'name_account',
                    },
                    {
                        name: 'from_account',
                        data: 'from_account',
                    },
                    {
                        name: 'payment_method.target_account',
                        data: 'payment_method.target_account',
                    },
                    {
                        name: 'total',
                        data: 'total',
                    },
                    {
                        name: 'image',
                        data: 'image',
                    },
                    {
                        name: 'status',
                        data: 'status',
                    },
                    {
                        name: 'action',
                        data: 'action',
                    },
                ],
            });

            $('#transactions-table').on('click', '#update-status', function() {
                $('#error-transaction-updatestatus').html('');

                let transactionId = $(this).data('id');

                $.get(`{{ url('dashboard/transactions/history/${transactionId}/edit') }}`, function(data) {
                    $('#transactionupdatestatus-id').val(data.transaction.id);

                    $('.modal-title').html(`
                        Update Status Transaksi dengan <b>Booking ID ${data.transaction.order.order_code}</b>
                    `);

                    $('#passenger-row').html('');
                    $.each(data.transaction.order.passengers, function(_, passenger) {
                        $('#passenger-row').append($('<div/>', {
                            class: 'col-lg-6 card card-body px-1 py-0',
                            html: `
                                <table>
                                    <tr>
                                        <th>Nama</th>
                                        <td style="vertical-align: top">:</td>
                                        <td id="passenger-name" style="vertical-align: top">${passenger.name}</td>
                                    </tr>
                                    <tr>
                                        <th>Nik</th>
                                        <td style="vertical-align: top">:</td>
                                        <td id="passenger-idnumber" style="vertical-align: top">${passenger.id_number}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Kelamin</th>
                                        <td style="vertical-align: top">:</td>
                                        <td id="passenger-gender" style="vertical-align: top">${passenger.gender}</td>
                                    </tr>
                                </table>
                            `
                        }));
                    })

                    $('#transaction-image').html('');
                    if (data.transaction.image !== null) {
                        $('#transaction-image').append($('<img />', {
                            src: `{{ asset('storage/images/${data.transaction.image}') }}`,
                            class: 'rounded img-fluid mx-auto d-block',
                        }));
                    } else {
                        $('#transaction-image').html('<span>Bukti Pembayaran Belum diunggah.</span>');
                    }

                    data.transaction.status ? $('#status').prop('checked', true) : $('#status').prop('checked', false);
                    $('#label-status').text(`
                        Konfirmasi transaksi ini ?
                    `);

                    $('#modal-transaction-updatestatus').modal('show');
                });

            })

            $('#transactions-table').on('click', '#upload-img', function() {
                $('#error-transaction-uploadimg').html('');

                let transactionId = $(this).data('id');

                $.get(`{{ url('dashboard/transactions/history/${transactionId}/edit') }}`, function(data) {
                    $('#transaction-id').val(transactionId);

                    if (data.transaction.image !== null) {
                        $('#transaction-file').removeClass('d-none');
                        $('#d-file').attr('src',
                            `{{ asset('storage/images/${data.transaction.image}') }}`
                        );
                    } else {
                        $('#transaction-file').addClass('d-none');
                    }

                    $('#modal-transaction-uploadimg').modal('show');
                });
            });
        })
    </script>

    @stack('_scripts')
@endpush
