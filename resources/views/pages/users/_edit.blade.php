<div class="modal fade" id="modal-edit-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-edit-user" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Edit {{ $title }}</h4>
                    <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-edit-user" class="pl-0 mb-0"></ul>

                    <x-input type="hidden" id="userid-edit" name="user_id" />

                    <div class="form-group row">
                        <x-label for="username-edit" class="col-sm-4 col-form-label">
                            Nama<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8">
                            <x-input id="username-edit" name="name" placeholder="Nama" autofocus />
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-label for="useremail-edit" class="col-sm-4 col-form-label">
                            Email<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8">
                            <x-input id="useremail-edit" name="email" placeholder="Email" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <x-label for="gender-edit" class="col-sm-4 col-form-label">
                            Jenis Kelamin<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8 d-flex flex-row justify-content-between align-items-center">
                            <x-radio-button id="gendermale-edit" name="gender" value="Laki-laki" label="Laki-laki" />
                            <x-radio-button id="genderfemale-edit" name="gender" value="Perempuan" label="Perempuan" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-label for="phonenumber-edit" class="col-sm-4 col-form-label">
                            No. Hp<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8">
                            <x-input type="number" id="phonenumber-edit" name="phone_number" placeholder="No. Hp" />
                        </div>
                    </div>

                    @if (preg_match('[admin|owner]', auth()->user()->role->name))
                        <div class="form-group row">
                            <x-label for="roleid-edit" class="col-sm-4 col-form-label">
                                Pilih Role<span class="text-danger font-weight-bold">*</span>
                            </x-label>
                            <div class="col-sm-8">
                                <x-select id="roleid-edit" name="role_id" emptyOptTxt="Pilih Role" />
                            </div>
                        </div>
                    @endif
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
            $('#modal-edit-user').on('hidden.bs.modal', function(e) {
                $(this).find('#form-edit-user')[0].reset();
            });

            $('#form-edit-user').on('submit', function(e) {
                e.preventDefault();

                let [formData, userId] = [$(this).serializeArray(), $("#userid-edit").val()];

                $.ajax({
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        url: `{{ url('dashboard/users/${userId}/update') }}`,
                        data: formData,
                        dataType: 'json',
                    })
                    .done(function(data) {
                        $('#modal-edit-user').modal('hide');
                        $('#form-edit-user')[0].reset();

                        Toast.fire({
                            icon: 'success',
                            title: `${data.success}`
                        });

                        $("#users-table").DataTable().ajax.reload();
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

                            $('#error-edit-user').html(html);
                        }
                    })
            });
        })
    </script>
@endpush
