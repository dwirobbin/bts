<div class="modal fade" id="modal-complaint">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Pengaduan</h4>
                <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
            </div>

            <div class="modal-body d-flex flex-column">
                <ul id="error-complaint" class="pl-0 mb-0"></ul>

                <div id="complaint-chats"></div>

            </div>

            <form action="{{ url('dashboard/complaints') }}" id="form-complaint" method="POST" class="d-inline">
                @csrf
                <div class="modal-footer row justify-content-between">
                    <div class="col-sm-3">
                        <label for="phone_number" class="col-form-label">Kirim pesan baru:</label>
                    </div>

                    <div class="col-sm-8 d-flex flex-row">
                        <input type="hidden" id="order-id" name="order_id">
                        <input type="text" class="form-control" name="body">

                        <x-button type="submit" class="btn-success">Kirim</x-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('_scripts')
    <script>
        $(document).ready(function() {
            $('#modal-complaint').on('hidden.bs.modal', function(e) {
                $(this).find('#form-complaint')[0].reset();
            });

            $('#form-complaint').on('submit', function(e) {
                e.preventDefault();

                let $form = $(this);

                $.post($form.attr("action"), $form.serialize())
                    .done(function(data) {
                        $('#modal-complaint').modal('hide');
                        $('#form-complaint')[0].reset();

                        Toast.fire({
                            icon: 'success',
                            title: `${data.success}`
                        });

                        location.reload()
                    })
                    .fail(function(jqXHR, status) {
                        console.log(jqXHR);
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

                            $('#error-complaint').html(html);
                        }
                    }, 'json')
            });
        })
    </script>
@endpush
