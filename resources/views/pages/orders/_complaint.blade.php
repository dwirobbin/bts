<div class="modal fade" id="modal-complaint">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Pengaduan</h4>
                <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
            </div>

            <div class="modal-body d-flex flex-column">
                <ul id="error-complaint" class="pl-0 mb-0"></ul>

                @foreach ($order->complaints as $complaint)
                    <div class="d-flex flex-row align-items-center mb-2 @if ($complaint->user->id == Auth::id()) justify-content-end @endif">
                        <img src="{{ asset('app-src/img/default_profile.jpg') }}" alt="Profile Image" style="max-width: 30px; max-height: 30px"
                            class="rounded-circle mx-2">

                        <label class="my-1">{{ $complaint->user->name }}</label>
                    </div>
                    <p class="border rounded mx-2 mb-4 p-2 font-weight-normal text-left">
                        {{ $complaint->body }}
                    </p>
                @endforeach
            </div>

            <form action="{{ url('dashboard/complaints') }}" id="form-complaint" method="POST" class="d-inline">
                @csrf
                <div class="modal-footer row justify-content-between">
                    <div class="col-sm-3">
                        <label for="phone_number" class="col-form-label">Kirim pesan baru:</label>
                    </div>

                    <div class="col-sm-8 d-flex flex-row">
                        <input type="hidden" id="order-id" name="order_id" value={{ $order->id }}>
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
