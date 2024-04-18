@extends('main')

@push('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('app-src/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-src/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-src/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Sweet Alert 2 -->
    <link rel="stylesheet" href="{{ asset('app-src/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('app-src/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
@endpush

@section('content')
    <x-content-header :title="$title" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @if (preg_match('[admin|owner]', auth()->user()->role->name))
                            <div class="card-header">
                                <x-button class="btn-primary float-left" id="add-new-ticket">
                                    <i class="fas fa-plus-circle"></i>&nbsp;{{ $title }}
                                </x-button>
                                @include('pages.tickets._create')
                            </div>
                        @endif
                        <div class="card-body">
                            <table id="tickets-table" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama SpeedBoat</th>
                                        <th>Pergi dari</th>
                                        <th>Tujuan ke</th>
                                        <th>Jam keberangkatan</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
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

    @include('pages.tickets._edit')
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
    <!-- Moment -->
    <script src="{{ asset('app-src/plugins/moment/moment.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('app-src/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

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
            $.fn.dataTable.ext.errMode = 'none';
            $('#tickets-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                lengthChange: true,
                autoWidth: true,
                dom: `
                    <'row'<'col-md-3'l><'col-md-5'B><'col-md-4'f>>
                    <'row'<'col-md-12'tr>>
                    <'row'<'col-md-5'i><'col-md-7'p>>
                `,
                order: [0, 'asc'],
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
                        searchable: false,
                        targets: 0,
                        width: '5%'
                    },
                    {
                        targets: [5],
                        searchable: false,
                        orderable: false,
                        className: 'dt-nowrap',
                    },
                ],
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
                    url: '{{ url('dashboard/tickets/get-data') }}',
                },
                columns: [{
                        name: 'DT_RowIndex',
                        data: 'DT_RowIndex',
                    },
                    {
                        name: 'speed_boat.name',
                        data: 'speed_boat.name',
                    },
                    {
                        name: 'street.from_route',
                        data: 'street.from_route',
                    },
                    {
                        name: 'street.to_route',
                        data: 'street.to_route',
                    },
                    {
                        name: 'hours_of_departure',
                        data: 'hours_of_departure',
                    },
                    {
                        name: 'price',
                        data: 'price',
                    },
                    {
                        name: 'stock',
                        data: 'stock',
                    },
                    {
                        name: 'action',
                        data: 'action',
                    },
                ],
            });

            $('#add-new-ticket').click(function() {
                $('#error-create-ticket').html('');

                $.get(`{{ url('dashboard/tickets/create') }}`, function(data) {
                    $('#speedboatid-create').html('<option value="" selected>Pilih Speedboat</option>');
                    $.each(data.speed_boats, function(_, speedBoat) {
                        $('#speedboatid-create').append(new Option(speedBoat.name, speedBoat.id));
                    })

                    $('#streetid-create').html('<option value="" selected>Pilih Rute</option>');
                    $.each(data.streets, function(_, street) {
                        $('#streetid-create').append(new Option(
                            `${street.from_route} - ${street.to_route}`, street.id
                        ));
                    });

                    $('#modal-create-ticket').modal('show');
                });
            });

            $('#tickets-table').on('click', '#edit-ticket', function() {
                $('#error-edit-ticket').html('');

                let ticketId = $(this).data('id');

                $.get(`{{ url('dashboard/tickets/${ticketId}/edit') }}`, function(data) {
                    $('#ticketid-edit').val(data.ticket.id);
                    $('#hoursofdeparture-edit').val(data.ticket.hours_of_departure);
                    $('#price-edit').val(formatRupiah((data.ticket.price).toString()));
                    $('#stock-edit').val(data.ticket.stock);

                    $('#modal-edit-ticket').modal('show');
                });
            });

            $('#tickets-table').on('click', '#delete-ticket', function() {
                let [ticketId, ticketName] = [$(this).data('id'), $(this).data('delete')]

                Confirm.fire({
                    title: 'Konfirmasi hapus Tiket !',
                    html: `Apakah anda yakin ingin menghapus Tiket ini ?`,
                    confirmButtonText: 'Ya, Hapus',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: `{{ url('dashboard/tickets/${ticketId}/delete') }}`,
                            success: function(data) {
                                Toast.fire({
                                    icon: 'success',
                                    title: `${data.success}`
                                })

                                $("#tickets-table").DataTable().ajax.reload();
                            },
                            error: function(jqXHR, status) {
                                if (status === 'error') {
                                    let respText = $.parseJSON(jqXHR.responseText);
                                    Toast.fire({
                                        icon: 'error',
                                        'title': respText.error
                                    })
                                }
                            }
                        })
                    }
                })
            });
        })

        const formatRupiah = (angka, prefix) => {
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
    </script>

    @stack('_scripts')
@endpush
