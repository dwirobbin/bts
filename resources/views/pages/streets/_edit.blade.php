<div class="modal fade" id="modal-edit-street">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-edit-street" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Edit {{ $title }}</h4>
                    <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-edit-street" class="pl-0 mb-0"></ul>

                    <x-input type="hidden" id="streetid-edit" name="street_id" />

                    <div class="form-group mb-3">
                        <x-label for="fromroute-edit">
                            Lokasi Berangkat<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-input id="fromroute-edit" name="from_route" placeholder="Lokasi Berangkat" autofocus />
                    </div>
                    <div class="form-group mb-3">
                        <x-label for="toroute-edit">
                            Lokasi Tujuan<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-input id="toroute-edit" name="to_route" placeholder="Lokasi Tujuan" />
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
            $('#modal-edit-street').on('hidden.bs.modal', function(e) {
                $(this).find('#form-edit-street')[0].reset();
            });

            $('#form-edit-street').on('submit', function(e) {
                e.preventDefault();

                let [formData, streetId] = [$(this).serializeArray(), $("#streetid-edit").val()];

                $.ajax({
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        url: `{{ url('dashboard/streets/${streetId}/update') }}`,
                        data: formData,
                        dataType: 'json',
                    })
                    .done(function(data) {
                        $('#modal-edit-street').modal('hide');
                        $('#form-edit-street')[0].reset();

                        Toast.fire({
                            icon: 'success',
                            title: `${data.success}`
                        });

                        $("#streets-table").DataTable().ajax.reload();
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
                                html += `<li class="mb-0">${respText.same_route}</li>`;
                            }

                            html += '</div>';

                            setTimeout(function() {
                                $('.alert').fadeTo(200, 0).slideUp(500, () =>
                                    $(this).remove()
                                );
                            }, 5000);

                            $('#error-edit-street').html(html);
                        }
                    })
            });
        })
    </script>
@endpush
