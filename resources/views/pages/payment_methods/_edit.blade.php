<div class="modal fade" id="modal-edit-paymentmethod">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-edit-paymentmethod" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Edit {{ $title }}</h4>
                    <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-edit-paymentmethod" class="pl-0 mb-0"></ul>

                    <x-input type="hidden" id="paymentmethodid-edit" name="paymentmethod_id" />

                    <div class="form-group mb-3">
                        <x-label for="method-edit">
                            Metode Pembayaran<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-input id="method-edit" name="method" placeholder="Metode Pembayaran" autofocus />
                    </div>
                    <div class="form-group mb-3">
                        <x-label for="targetaccount-edit">
                            No. Rekening Tujuan<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-input type="number" id="targetaccount-edit" name="target_account" placeholder="No. Rekening Tujuan" />
                    </div>
                    <div class="form-group mb-3 {{ auth()->user()->role->name == 'owner' ? 'd-none' : '' }}" id="">
                        <x-label for="ownerid-edit">
                            Pilih Pemilik<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <x-select id="ownerid-edit" name="owner_id" emptyOptTxt="Pilih Pemilik"></x-select>
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
            $('#modal-edit-paymentmethod').on('hidden.bs.modal', function(e) {
                $(this).find('#form-edit-paymentmethod')[0].reset();
            });

            $('#form-edit-paymentmethod').on('submit', function(e) {
                e.preventDefault();

                let [formData, paymentmethodId] = [$(this).serializeArray(), $("#paymentmethodid-edit").val()];

                $.ajax({
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        url: `{{ url('dashboard/payment-methods/${paymentmethodId}/update') }}`,
                        data: formData,
                        dataType: 'json',
                    })
                    .done(function(data) {
                        $('#modal-edit-paymentmethod').modal('hide');
                        $('#form-edit-paymentmethod')[0].reset();

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

                            $('#error-edit-paymentmethod').html(html);
                        }
                    })
            });
        })
    </script>
@endpush
