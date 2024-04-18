<div class="modal fade" id="modal-edit-speedboat">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-edit-speedboat" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Edit {{ $title }}</h4>
                    <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-edit-speedboat" class="pl-0 mb-0"></ul>

                    <x-input type="hidden" id="speedboatid-edit" name="speedboat_id" />

                    <div class="form-group mb-3">
                        <x-label for="speedboatname-edit">
                            Nama {{ $title }}<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-input id="speedboatname-edit" name="name" placeholder="Nama {{ $title }}" />
                    </div>

                    @if (auth()->user()->role->name == 'admin')
                        <div class="form-group mb-3">
                            <x-label for="ownerid-edit">
                                Pemilik {{ $title }}<span class="text-danger font-weight-bold">*</span>
                            </x-label>
                            <x-select id="ownerid-edit" name="owner_id" emptyOptTxt="Pilih Pemilik" />
                        </div>
                    @endif

                    <div class="form-group mb-3">
                        <x-label for="status-edit">Status {{ $title }}</x-label>
                        <x-select id="status-edit" name="status" emptyOptTxt="Pilih Status" />
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
            $('#modal-edit-speedboat').on('hidden.bs.modal', function(e) {
                $(this).find('#form-edit-speedboat')[0].reset();
            });

            $('#form-edit-speedboat').on('submit', function(e) {
                e.preventDefault();

                let [formData, speedboatId] = [$(this).serializeArray(), $("#speedboatid-edit").val()];

                $.ajax({
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        url: `{{ url('dashboard/speedboats/${speedboatId}/update') }}`,
                        data: formData,
                        dataType: 'json',
                    })
                    .done(function(data) {
                        $('#modal-edit-speedboat').modal('hide');
                        $('#form-edit-speedboat')[0].reset();

                        Toast.fire({
                            icon: 'success',
                            title: `${data.success}`
                        });

                        $("#speedboats-table").DataTable().ajax.reload();
                    })
                    .fail(function(jqXHR, status) {
                        if (status === 'error') {
                            let html = '<div class="alert alert-danger py-2">';
                            $.each(jqXHR.responseJSON.errors, function(_, val) {
                                html += `<li class="mb-0">${val}</li>`;
                            });
                            html += '</div>';

                            setTimeout(function() {
                                $('.alert').fadeTo(200, 0).slideUp(500, () =>
                                    $(this).remove()
                                );
                            }, 5000);

                            $('#error-edit-speedboat').html(html);
                        }
                    })
            });
        })
    </script>
@endpush
