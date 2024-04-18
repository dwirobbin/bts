<div class="modal fade" id="modal-create-ticket">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-create-ticket" action="{{ url('dashboard/tickets/store') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Tambah {{ $title }}</h4>
                    <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-create-ticket" class="pl-0 mb-0"></ul>

                    <div class="form-group row">
                        <x-label for="speedboatid-create" class="col-sm-3 col-form-label">
                            Speedboat<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-9">
                            <x-select id="speedboatid-create" name="speedboat_id" emptyOptTxt="Pilih Speedboat" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <x-label for="streetid-create" class="col-sm-3 col-form-label">
                            Rute<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-9">
                            <x-select id="streetid-create" name="street_id" emptyOptTxt="Pilih Rute" />
                        </div>
                    </div>

                    <div class="bootstrap-timepicker">
                        <div class="form-group row">
                            <x-label for="hoursofdeparture-create" class="col-sm-3 col-form-label">
                                Jam Keberangkatan<span class="text-danger font-weight-bold">*</span>
                            </x-label>

                            <div class="col-sm-9">
                                <div class="input-group date" id="timepicker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="hoursofdeparture-create"
                                        name="hours_of_departure" data-target="#timepicker" placeholder="Jam keberangkatan" />
                                    <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="far fa-clock"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <x-label for="goprice-create" class="col-sm-3 col-form-label">
                            Harga<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-9">
                            <x-input type="text" id="price-create" name="price" placeholder="Harga" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <x-label for="stock-create" class="col-sm-3 col-form-label">
                            Stok<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-9">
                            <x-input type="number" id="stock-create" name="stock" placeholder="Stok" />
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <x-button class="btn-default" data-dismiss="modal">Tutup</x-button>
                    <x-button type="submit" class="btn-primary">Simpan</x-button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('_scripts')
    <script>
        $(document).ready(function() {

            $('#price-create').keyup(function(e) {
                $(this).val(formatRupiah($(this).val()));
            });

            $("input[id='stock-create']").on('input', function(e) {
                $(e.target).val($(e.target).val().replace(/[^\d.]/ig, "")); // only number
            })

            $('#timepicker').datetimepicker({
                format: 'HH:mm'
            });

            $('#modal-create-ticket').on('hidden.bs.modal', function(e) {
                $(this).find('#form-create-ticket')[0].reset();
            });

            $('#form-create-ticket').on('submit', function(e) {
                e.preventDefault();

                let $form = $(this);

                $.post($form.attr("action"), $form.serialize())
                    .done(function(data) {
                        $('#modal-create-ticket').modal('hide');
                        $('#form-create-ticket')[0].reset();

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

                            if (jqXHR.status === 409) {
                                let respText = $.parseJSON(jqXHR.responseText);
                                html += `<li class="mb-0">${respText.same_ticket}</li>`;
                            }

                            html += '</div>';

                            setTimeout(function() {
                                $('.alert').fadeTo(200, 0).slideUp(500, () =>
                                    $(this).remove()
                                );
                            }, 5000);

                            $('#error-create-ticket').html(html);
                        }
                    }, 'json')
            });
        })
    </script>
@endpush
