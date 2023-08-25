<div class="modal fade" id="modal-create-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-create-user" action="{{ url('dashboard/users/store') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Tambah {{ $title }}</h4>
                    <x-button class="close" data-dismiss="modal"><span>&times;</span></x-button>
                </div>
                <div class="modal-body pb-2">
                    <ul id="error-create-user" class="pl-0 mb-0"></ul>

                    <div class="form-group row">
                        <x-label for="username-create" class="col-sm-4 col-form-label">
                            Nama<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8">
                            <x-input id="username-create" name="name" placeholder="Nama" autofocus />
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-label for="useremail-create" class="col-sm-4 col-form-label">
                            Email<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8">
                            <x-input id="useremail-create" name="email" placeholder="Email" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <x-label for="gender-create" class="col-sm-4 col-form-label">
                            Jenis Kelamin<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8 d-flex flex-row justify-content-between align-items-center">
                            <x-radio-button id="gendermale-create" name="gender" value="Laki-laki" label="Laki-laki" />
                            <x-radio-button id="genderfemale-create" name="gender" value="Perempuan" label="Perempuan" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-label for="phonenumber-create" class="col-sm-4 col-form-label">
                            No. Hp<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8">
                            <x-input type="number" id="phonenumber-create" name="phone_number" placeholder="No. Hp" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <x-label for="password-create" class="col-sm-4 col-form-label">
                            Password<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8">
                            <div class="input-group" id="show_hide_password">
                                <x-input type="password" id="password-create" name="password" placeholder="Password" />
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span toggle="#password-create" class="fas fa-eye toggle-password-icon" role="button"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <x-label for="password-confirmation" class="col-sm-4 col-form-label">
                            Ulangi Password<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8">
                            <div class="input-group" id="show_hide_password">
                                <x-input type="password" id="password-confirmation" name="password_confirmation" placeholder="Ulangi Password" />
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span toggle="#password-confirmation" class="fas fa-eye toggle-password-icon" role="button"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <x-label for="roleid-create" class="col-sm-4 col-form-label">
                            Pilih Role<span class="text-danger font-weight-bold">*</span>
                        </x-label>
                        <div class="col-sm-8">
                            <x-select id="roleid-create" name="role_id" emptyOptTxt="Pilih Role" />
                        </div>
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
            $('#modal-create-user').on('hidden.bs.modal', function(e) {
                $(this).find('#form-create-user')[0].reset();
            });

            $(".toggle-password-icon").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $($(this).attr("toggle"));
                if (input.attr("type") == "password" || input.attr("type") == "password-confirmation") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });

            $('#form-create-user').on('submit', function(e) {
                e.preventDefault();

                let $form = $(this);

                $.post($form.attr("action"), $form.serialize())
                    .done(function(data) {
                        $('#modal-create-user').modal('hide');
                        $('#form-create-user')[0].reset();

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

                            $('#error-create-user').html(html);
                        }
                    }, 'json')
            });
        })
    </script>
@endpush
