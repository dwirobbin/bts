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

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        @if (auth()->user()->role->name === 'admin')
                            <div class="card-header">
                                <x-button class="btn-primary float-left" id="add-new-airline">
                                    <i class="fas fa-plus-circle"></i>&nbsp;{{ $title }}
                                </x-button>
                                @include('pages.airlines._create')
                            </div>
                        @endif
                        <div class="card-body">
                            <table id="airlines-table" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama {{ $title }}</th>
                                        <th>Pemilik {{ $title }}</th>
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

    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#airlines-table').DataTable({
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
                        targets: [4],
                        searchable: false,
                        orderable: false,
                        className: 'dt-nowrap',
                        width: '5%'
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
                    url: '{{ url('dashboard/airlines/get-data') }}',
                },
                columns: [{
                        name: 'DT_RowIndex',
                        data: 'DT_RowIndex',
                    },
                    {
                        name: 'name',
                        data: 'name',
                    },
                    {
                        name: 'owner.name',
                        data: 'owner.name',
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

            $('#add-new-airline').click(function() {
                $('#error-create-airline').html('');

                $.get(`{{ url('dashboard/airlines/create') }}`, function(data) {
                    $('#ownerid-create').html('<option value="" selected>Pilih Pemilik</option>');
                    $.each(data.owners, function(_, owner) {
                        $('#ownerid-create').append(new Option(owner.name, owner.id));
                    })

                    $('#status-create').html('<option value="" selected>Pilih Status</option>');
                    $.each(data.statuses, function(key, value) {
                        $('#status-create').append(new Option(value, value));
                    })
                });

                $('#modal-create-airline').modal('show');
            });

            $('#airlines-table').on('click', '#edit-airline', function() {
                $('#error-edit-airline').html('');

                let airlineId = $(this).data('id');

                $.get(`{{ url('dashboard/airlines/${airlineId}/edit') }}`, function(data) {
                    $('#airlineid-edit').val(data.airline.id);
                    $('#airlinename-edit').val(data.airline.name);

                    $('#status-edit').html('<option value="" selected>Pilih Status</option>');
                    $.each(data.statuses, function(_, status) {
                        $('#status-edit').append($('<option>', {
                            value: status,
                            selected: status == data.airline.status ? status : null,
                            text: status,
                        }));
                    })

                    $('#ownerid-edit').html('<option value="" selected>Pilih Pemilik</option>');
                    $.each(data.owners, function(_, owner) {
                        $('#ownerid-edit').append($('<option>', {
                            value: owner.id,
                            selected: owner.id == data.airline.owner_id ? owner.id : null,
                            text: owner.name,
                        }));
                    })

                    $('#modal-edit-airline').modal('show');
                });
            });

            $('#airlines-table').on('click', '#delete-airline', function() {
                let [airlineId, airlineName] = [$(this).data('id'), $(this).data('delete')]

                Confirm.fire({
                    title: 'Konfirmasi hapus {{ $title }} !',
                    html: `Apakah anda yakin ingin menghapus {{ $title }} <b>${airlineName}</b>`,
                    confirmButtonText: 'Ya, Hapus',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: `{{ url('dashboard/airlines/${airlineId}/delete') }}`,
                            success: function(data) {
                                Toast.fire({
                                    icon: 'success',
                                    title: `${data.success}`
                                })

                                $("#airlines-table").DataTable().ajax.reload();
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
