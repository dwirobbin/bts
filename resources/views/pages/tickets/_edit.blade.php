<div class="modal fade" id="modal-edit-ticket">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-edit-ticket" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Tiket</h4>
                    <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-edit-ticket" class="pl-0 mb-0"></ul>

                    <x-input type="hidden" id="ticketid-edit" name="ticket_id" />

                    <div class="bootstrap-timepicker">
                        <div class="form-group row">
                            <x-label for="hoursofdeparture-edit" class="col-sm-3 col-form-label">
                                Jam Keberangkatan<span class="text-danger font-weight-bold">*</span>
                            </x-label>

                            <div class="col-sm-9">
                                <div class="input-group date" id="timepicker-edit" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="hoursofdeparture-edit"
                                        name="hours_of_departure" data-target="#timepicker-edit" placeholder="Jam keberangkatan" />
                                    <div class="input-group-append" data-target="#timepicker-edit" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="far fa-clock"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <x-label for="goprice-edit" class="col-sm-3 col-form-label">
                            Harga<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-9">
                            <x-input type="text" id="price-edit" name="price" placeholder="Harga" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <x-label for="stock-edit" class="col-sm-3 col-form-label">
                            Stok<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-9">
                            <x-input type="number" id="stock-edit" name="stock" placeholder="Stok" />
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <x-button class="btn-default" data-dismiss="modal">Tutup</x-button>
                    <x-button type="submit" class="btn-primary">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('_scripts')
    <script>
        $(document).ready(function() {

            $('#price-edit').keyup(function(e) {
                $(this).val(formatRupiah($(this).val()));
            });

            $("input[id='stock-edit']").on('input', function(e) {
                $(e.target).val($(e.target).val().replace(/[^\d.]/ig, "")); // only number
            })

            $('#timepicker-edit').datetimepicker({
                format: 'HH:mm'
            });

            $('#modal-edit-ticket').on('hidden.bs.modal', function(e) {
                $(this).find('#form-edit-ticket')[0].reset();
            });

            $('#form-edit-ticket').on('submit', function(e) {
                e.preventDefault();

                let [formData, ticketId] = [$(this).serializeArray(), $("#ticketid-edit").val()];

                $.ajax({
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        url: `{{ url('dashboard/tickets/${ticketId}/update') }}`,
                        data: formData,
                        dataType: 'json',
                    })
                    .done(function(data) {
                        $('#modal-edit-ticket').modal('hide');
                        $('#form-edit-ticket')[0].reset();

                        Toast.fire({
                            icon: 'success',
                            title: `${data.success}`
                        });

                        $("#tickets-table").DataTable().ajax.reload();
                    })
                    .fail(function(jqXHR, status) {
                        if (status === 'error') {
                            let html = '<div class="alert alert-danger py-2">';

                            if (jqXHR.status === 422) {
                                $.each(jqXHR.responseJSON.errors, function(_, val) {
                                    html += `<li class="mb-0">${val}</li>`;
                                });
                            }

                            html += '</div>';

                            setTimeout(function() {
                                $('.alert').fadeTo(200, 0).slideUp(500, () =>
                                    $(this).remove()
                                );
                            }, 5000);

                            $('#error-edit-ticket').html(html);
                        }
                    })
            });
        })
    </script>
@endpush
