<div class="modal fade" id="modal-transaction-uploadimg">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-transaction-uploadimg" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title">Unggah Bukti Pembayaran</h4>
                    <x-button class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-transaction-uploadimg" class="pl-0 mb-0"></ul>

                    <x-input type="hidden" id="transaction-id" name="transaction_id" />

                    <div class="form-group">
                        <x-label for="image-file">File</x-label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image-file" name="image" accept=".png,.jpg,.jpeg" />
                            <label class="custom-file-label" for="file">Choose file</label>
                        </div>
                    </div>

                    <div class="card" id="transaction-file">
                        <div class="card-body">
                            <img src="" id="d-file" class="card-img-top" height="240" alt="Gambar" />
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
            $('#modal-transaction-uploadimg').on('hidden.bs.modal', function(e) {
                $(this).find('#form-transaction-uploadimg')[0].reset();
            });

            $('#form-transaction-uploadimg').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                let transactionId = $("#transaction-id").val();

                $.ajax({
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    url: `{{ url('dashboard/transactions/history/${transactionId}/store-img') }}`,
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        $('#modal-transaction-uploadimg').modal('hide');
                        $('#form-transaction-uploadimg')[0].reset();

                        Toast.fire({
                            icon: 'success',
                            title: `${data.success}`
                        });

                        $("#transactions-table").DataTable().ajax.reload();
                    },
                    error: function(jqXHR, status) {
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

                            $('#error-transaction-uploadimg').html(html);
                        }
                    }
                })
            });

            $('#delete-file').click(function() {
                let transactionId = $(this).data('id');

                Confirm.fire({
                    title: `Konfirmasi hapus!`,
                    html: `Apakah anda yakin ingin menghapus bukti pembayaran ini ?`,
                    confirmButtonText: 'Ya, Hapus',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'delete',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: `{{ url('dashboard/transactions/history/${transactionId}/delete-img') }}`,
                            success: function(data) {
                                $('#transaction-file').remove();

                                Toast.fire({
                                    title: `${data.success}`
                                });

                                location.reload();
                            },
                            error: function(jqXHR, status) {
                                if (status === 'error') {
                                    let html = '<div class="alert alert-danger py-2">';
                                    $.each(jqXHR.responseJSON.errors, function(_, val) {
                                        html += `<li class="mb-0">${val}</li>`;
                                    });
                                    html += '</div>';

                                    setTimeout(function() {
                                        $('.alert').fadeTo(200, 0).slideUp(500,
                                            () =>
                                            $(this).remove()
                                        );
                                    }, 5000);

                                    $('#error-transaction-uploadimg').html(html);
                                }
                            }
                        })
                    }
                })
            })
        })
    </script>
@endpush
