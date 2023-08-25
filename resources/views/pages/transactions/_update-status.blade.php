<div class="modal fade" id="modal-transaction-updatestatus">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form-transaction-updatestatus" method="post">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <x-button class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </x-button>
                </div>
                <div class="modal-body pb-2" style="max-height: calc(100vh - 210px);overflow-y: auto;">
                    <ul id="error-transaction-updatestatus" class="pl-0 mb-0"></ul>

                    <x-input type="hidden" id="transactionupdatestatus-id" name="transaction_id" />

                    <div class="card">
                        <div class="card-header">
                            <h5 class="font-weight-bold">Data Penumpang</h5>
                        </div>
                        <div class="card-body">
                            <div class="row justify-content-center" id="passenger-row">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="font-weight-bold">Bukti Pembayaran</h5>
                        </div>
                        <div class="card-body" id="transaction-image">
                        </div>
                    </div>

                    <div class="input-group w-100">
                        <div class="input-group-text">
                            <input type="checkbox" id="status" name="status">
                        </div>
                        <label type="text" class="form-control text-wrap" id="label-status"></label>
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
            $('#form-transaction-updatestatus').on('submit', function(e) {
                e.preventDefault();

                let [formData, transactionId] = [$(this).serializeArray(), $("#transactionupdatestatus-id").val()];

                $.ajax({
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        url: `{{ url('dashboard/transactions/history/${transactionId}/update-status') }}`,
                        data: formData,
                        dataType: 'json',
                    })
                    .done(function(data) {
                        $('#modal-transaction-updatestatus').modal('hide');
                        $('#form-transaction-updatestatus')[0].reset();

                        Toast.fire({
                            icon: 'success',
                            title: `${data.success}`
                        });

                        $("#transactions-table").DataTable().ajax.reload();
                    })
            });
        })
    </script>
@endpush
