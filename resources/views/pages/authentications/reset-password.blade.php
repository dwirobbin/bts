@extends('main')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-auto pr-0">
                    <div class="login-box mr-0  mt-5">
                        <div class="card card-outline card-primary">
                            <div class="card-header text-center">
                                <h1><b>Reset Password</b></h1>
                            </div>
                            <div class="card-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <h6><i class="icon fas fa-check"></i>{{ session()->get('success') }}</h6>
                                    </div>
                                @endif

                                @if (session()->has('error'))
                                    <div class="alert alert-danger alert-dismissible pb-1">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <h6><i class="icon fas fa-ban"></i> {{ session()->get('error') }}</h6>
                                    </div>
                                @endif

                                <form action="{{ route('reset_password.post') }}" method="post">
                                    @csrf
                                    <input type="text" name="token" hidden value="{{ $token }}">

                                    <div class="form-group mb-3">
                                        <label for="pasword">Password Baru</label>
                                        <button id="toggle-password" type="button" class="btn btn-transparent border-0 p-0 float-right">
                                            Lihat password
                                        </button>
                                        <div class="input-group mb-3">
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                                                value="{{ old('password') }}">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-lock"></span>
                                                </div>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="password-confirmation">Konfirmasi Password</label>
                                        <div class="input-group mb-3">
                                            <input type="password" id="password-confirmation" name="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                placeholder="Konfirmasi Password" value="{{ old('password_confirmation') }}">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-undo"></span>
                                                </div>
                                            </div>
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block">Ubah Password</button>
                                </form>

                                <p class="mt-3 mb-0 text-center">
                                    <a href="{{ url('auth/login') }}" class="text-center">Login</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password-confirmation');
        const togglePasswordButton = document.getElementById('toggle-password');

        togglePasswordButton.addEventListener('click', togglePassword);

        function togglePassword() {
            if (passwordInput.type === 'password' || passwordConfirmationInput.type === 'password') {
                passwordInput.type = 'text';
                passwordConfirmationInput.type = 'text';
                togglePasswordButton.textContent = 'Hide password';
            } else {
                passwordInput.type = 'password';
                passwordConfirmationInput.type = 'password';
                togglePasswordButton.textContent = 'Show password';
            }
        }
    </script>
@endpush
