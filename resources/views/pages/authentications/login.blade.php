@extends('main')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-auto pr-0">
                    <div class="login-box mr-0 mt-5">
                        <div class="card card-outline card-primary">
                            <div class="card-header text-center">
                                <h1><b>Masuk</b></h1>
                            </div>
                            <div class="card-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissible px-2">
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

                                <form action="{{ url('auth/login') }}" method="post">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="email">Email</label>
                                        <div class="input-group mb-3">
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror" placeholder="Email" tabindex="1"
                                                autofocus>
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

                                    <div class="form-group mb-3">
                                        <label for="pasword">Password</label>
                                        <button id="toggle-password" type="button" class="btn btn-transparent border-0 p-0 float-right"
                                            tabindex="5">
                                            Lihat password
                                        </button>
                                        <div class="input-group mb-3">
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" placeholder="Password" tabindex="2">
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

                                    <button type="submit" class="btn btn-primary btn-block" tabindex="3">Sign In</button>
                                </form>

                                {{-- <p class="mt-3 mb-0 text-center">
                                    <a href="{{ url('auth/forgot-password') }}" class="text-center">Lupa Password</a>
                                </p> --}}
                                <p class="mt-3 mb-0 text-center">
                                    <a href="{{ url('auth/register') }}" class="text-center" tabindex="4">Register</a>
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
        const togglePasswordButton = document.getElementById('toggle-password');

        togglePasswordButton.addEventListener('click', togglePassword);

        function togglePassword() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePasswordButton.textContent = 'Hide password';
            } else {
                passwordInput.type = 'password';
                togglePasswordButton.textContent = 'Show password';
            }
        }
    </script>
@endpush
