@extends('main')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-auto pr-0 mt-5">
                    <div class="register-box">
                        <div class="card card-outline card-primary">
                            <div class="card-header text-center">
                                <h1><b>Daftar</b></h1>
                            </div>
                            <div class="card-body">

                                @if (session()->has('error'))
                                    <div class="alert alert-danger alert-dismissible pb-1">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <h6><i class="icon fas fa-ban"></i> {{ session()->get('error') }}</h6>
                                    </div>
                                @endif

                                <form action="{{ url('auth/register') }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="name">Nama Lengkap</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" id="name" name="name"
                                                        class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap"
                                                        value="{{ old('name') }}" tabindex="1" autofocus>
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <span class="fas fa-user"></span>
                                                        </div>
                                                    </div>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="email">Email</label>
                                                <div class="input-group mb-3">
                                                    <input type="email" id="email" name="email"
                                                        class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                                        value="{{ old('email') }}" tabindex="2">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <span class="fas fa-envelope"></span>
                                                        </div>
                                                    </div>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="pasword">Password</label>
                                                <button id="toggle-password" type="button" class="btn btn-transparent border-0 p-0 float-right"
                                                    tabindex="7">
                                                    Lihat password
                                                </button>
                                                <div class="input-group mb-3">
                                                    <input type="password" id="password" name="password"
                                                        class="form-control @error('password') is-invalid @enderror" placeholder="Password"
                                                        value="{{ old('password') }}" tabindex="3">
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
                                                <label for="password-confirmation">Ulangi Password</label>
                                                <div class="input-group mb-3">
                                                    <input type="password" id="password-confirmation" name="password_confirmation"
                                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                                        autocomplete="current-password" placeholder="Ulangi Password"
                                                        value="{{ old('password_confirmation') }}" tabindex="4">
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
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block" tabindex="5">Sign Up</button>
                                </form>

                                <p class="mt-3 mb-0 text-center">
                                    <a href="{{ url('auth/login') }}" class="text-center" tabindex="6">Login</a>
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
