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
                            <table id="orders-table" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>ID Booking</th>
                                        <th>Nama</th>
                                        <th>SpeedBoat</th>
                                        <th>Rute</th>
                                        <th>Jmlh</th>
                                        <th>Tipe</th>
                                        <th>Tgl Pergi</th>
                                        <th>Tgl Pulang</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $order->user->name }}</td>
                                            <td>{{ $order->ticket->airline->name }}</td>
                                            <td>{{ $order->ticket->street->from_route . '-' . $order->ticket->street->to_route }}</td>
                                            <td>{{ $order->amount }}</td>
                                            <td>{{ $order->trip_type }}</td>
                                            <td>{{ $order->go_date }}</td>
                                            <td>{{ $order->back_date != null ? Carbon\Carbon::parse($order->back_date)->format('d/m/Y') : 'Hanya Pergi' }}
                                            </td>
                                            <td>
                                                @if (!preg_match('[driver]', auth()->user()->role->name))
                                                    @if (preg_match('[admin|owner]', auth()->user()->role->name))
                                                        <a href="/dashboard/print?order={{ $order->order_code }}" target="_blank">
                                                            <x-button class="btn-xs btn-success">Cetak</x-button>
                                                        </a>
                                                    @elseif (auth()->user()->role->name === 'customer' && $order->transaction->status == true)
                                                        <a href="/dashboard/print?order={{ $order->order_code }}" target="_blank">
                                                            <x-button class="btn-success btn-xs">Cetak</x-button>
                                                        </a>
                                                    @endif

                                                    <x-button class='btn-xs btn-warning position-relative' dataToggle="modal"
                                                        dataTarget="#modal-complaint">
                                                        Lapor

                                                        @if (auth()->user()->role->name === 'customer')
                                                            @if ($order->complaints->where('seen_for_admin', 0)->count() != 0)
                                                                <span
                                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                                    {{ $order->complaints->where('seen_for_admin', 0)->count() }}
                                                                </span>
                                                            @endif
                                                        @elseif (preg_match('[admin|owner]', auth()->user()->role->name))
                                                            @if ($order->complaints->where('seen', 0)->count() != 0)
                                                                <span
                                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                                    {{ $order->complaints->where('seen', 0)->count() }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </x-button>
                                                    @include('pages.orders._complaint')

                                                    @if (preg_match('[admin|owner]', auth()->user()->role->name))
                                                        <x-button id='delete-order' data-id='{{ $order->id }}'
                                                            data-delete='{{ $order->order_code }}' class='btn-xs btn-danger'>
                                                            Hapus
                                                        </x-button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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

    @if (session()->has('success'))
        <script>
            Toast.fire({
                title: `{{ session()->get('success') }}`,
                icon: 'success',
            })
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            Toast.fire({
                title: `{{ session()->get('error') }}`,
                icon: 'error',
            })
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#orders-table').DataTable({
                responsive: {
                    details: {
                        renderer: $.fn.dataTable.Responsive.renderer.listHiddenNodes()
                    }
                },
                lengthChange: true,
                autoWidth: false,
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
                        targets: [9],
                        searchable: false,
                        orderable: false,
                        className: 'dt-wrap dt-center',
                        width: '3%'
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
            });

            $('#orders-table').on('click', '#delete-order', function() {
                let [orderId, orderName] = [$(this).data('id'), $(this).data('delete')]

                Confirm.fire({
                    title: 'Konfirmasi hapus {{ $title }} !',
                    html: `Apakah anda yakin ingin menghapus Pesanan dengan <b>Booking ID: ${orderName}</b>`,
                    confirmButtonText: 'Ya, Hapus',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: `{{ url('dashboard/orders/history/${orderId}/delete') }}`,
                            success: function(data) {
                                Toast.fire({
                                    icon: 'success',
                                    title: `${data.success}`
                                })

                                $("#orders-table").DataTable().ajax.reload();
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
    </script>

    @stack('_scripts')
@endpush
