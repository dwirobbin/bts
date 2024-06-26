<div class="modal fade" id="modal-create-speedboat">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-create-speedboat" action="{{ url('dashboard/speedboats/store') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Tambah {{ $title }}</h4>
                    <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-create-speedboat" class="pl-0 mb-0"></ul>

                    <div class="form-group mb-3">
                        <x-label for="speedboatname-create">
                            Nama {{ $title }}<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-input id="speedboatname-create" name="name" placeholder="Nama {{ $title }}" />
                    </div>

                    <div class="form-group mb-3">
                        <x-label for="ownerid-create">
                            Pemilik {{ $title }}<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-select id="ownerid-create" name="owner_id" emptyOptTxt="Pilih Pemilik" />
                    </div>

                    <div class="form-group mb-3">
                        <x-label for="status-create">Status {{ $title }}</x-label>
                        <x-select id="status-create" name="status" emptyOptTxt="Pilih Status" />
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
            $('#modal-create-speedboat').on('hidden.bs.modal', function(e) {
                $(this).find('#form-create-speedboat')[0].reset();
            });

            $('#form-create-speedboat').on('submit', function(e) {
                e.preventDefault();

                let $form = $(this);

                $.post($form.attr("action"), $form.serialize())
                    .done(function(data) {
                        $('#modal-create-speedboat').modal('hide');
                        $('#form-create-speedboat')[0].reset();

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

                            $('#error-create-speedboat').html(html);
                        }
                    }, 'json')
            });
        })
    </script>
@endpush
