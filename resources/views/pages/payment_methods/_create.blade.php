<div class="modal fade" id="modal-create-paymentmethod">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-create-paymentmethod" action="{{ url('dashboard/payment-methods/store') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Tambah {{ $title }}</h4>
                    <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-create-paymentmethod" class="pl-0 mb-0"></ul>

                    <div class="form-group mb-3">
                        <x-label for="method-create">
                            Metode Pembayaran<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-input id="method-create" name="method" placeholder="Metode Pembayaran" autofocus />
                    </div>
                    <div class="form-group mb-3">
                        <x-label for="targetaccount-create">
                            No. Rekening Tujuan<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-input type="number" id="targetaccount-create" name="target_account" placeholder="No. Rekening Tujuan" />
                    </div>
                    <div class="form-group mb-3 {{ auth()->user()->role->name == 'owner' ? 'd-none' : '' }}" id="">
                        <x-label for="ownerid-create">
                            Pilih Pemilik<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-select id="ownerid-create" name="owner_id" emptyOptTxt="Pilih Pemilik"></x-select>
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
            $('#modal-create-paymentmethod').on('hidden.bs.modal', function(e) {
                $(this).find('#form-create-paymentmethod')[0].reset();
            });

            $('#form-create-paymentmethod').on('submit', function(e) {
                e.preventDefault();

                let $form = $(this);

                $.post($form.attr("action"), $form.serialize())
                    .done(function(data) {
                        $('#modal-create-paymentmethod').modal('hide');
                        $('#form-create-paymentmethod')[0].reset();

                        Toast.fire({
                            icon: 'success',
                            title: `${data.success}`
                        });

                        $("#paymentmethods-table").DataTable().ajax.reload();
                    })
                    .fail(function(jqXHR, status) {
                        if (jqXHR.status === 422) {
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

                            $('#error-create-paymentmethod').html(html);
                        }
                    }, 'json')
            });
        })
    </script>
@endpush
