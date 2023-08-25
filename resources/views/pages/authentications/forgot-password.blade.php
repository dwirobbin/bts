@extends('main')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-auto pr-0">
                    <div class="login-box mr-0 mt-5">
                        <div class="card card-outline card-primary">
                            <div class="card-header text-center">
                                <h1><b>{{ $title }}</b></h1>
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

                                <form action="{{ url('auth/send-password-reset-link') }}" method="post">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="email">Email</label>
                                        <div class="input-group mb-3">
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                                value="{{ old('email') }}" autofocus>
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

                                    <button type="submit" class="btn btn-primary btn-block">Kirim</button>
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
