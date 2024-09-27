@extends('layouts.app')

@section('content')
<html>
<body background>
</body>
<style>
    body {
        /* background: rgba(0, 0, 0, 0.6) url(""); */
        background-color: #e9ede1;
        background-size: cover; 
        background-position: center;
        background-blend-mode: darken;
        background-repeat: no-repeat;
}
    </style>
</html>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 mt-lg-5">
                <div class="card" style="border-radius: 1rem;">
                    <div class="card-header p-3 mb-3 txt2" style="border-radius: 1rem;">{{ __('INICIAR SESIÓN') }}</div>
                    
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end txt4">{{ __('Email') }}</label>

                                <div class="col-md-8">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end txt4">{{ __('Contraseña') }}</label>

                                <div class="col-md-8">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label txt3" for="remember">
                                            {{ __('Recordar contraseña') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-8 offset-md-5">
                                    @if (Route::has('password.request'))
                                        <a class="txt1" href="{{ route('password.request') }}">
                                            {{ __('¿Olvidaste tu contraseña?') }}
                                        </a>
                                    @endif

                                    <button type="submit" class="btningresar2">
                                        {{ __('Ingresar') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </body>
        </div>
    </div>
</div>
@endsection
<style>
    .btningresar {
        background-color: #faa625;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
    }
    .btningresar:hover {
        background-color: #94c93b;
    }
    .btningresar2 {
        background-color: #94c93b;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
        position: relative;
    }
    .btningresar2:hover {
        background-color: #faa625;
    }
    .txt1 {
        font-family: "Segoe UI";
        font-size: 15px;
        line-height: 1.6;
        padding-right: 15px;
        color: #faa625;
    }
    .txt2 {
        font-family: "Segoe UI";
        font-size: 30px;
        line-height: 1.6;
        text-align: center;
        font-weight: 700;
        color: #94c93b;
    }
    .txt3 {
        font-family: "Segoe UI";
        font-size: 14px;
        line-height: 1.6;
        text-align: center;
        font-weight: 400;
    }
    .txt4 {
        font-family: "Segoe UI";
        font-size: 15px;
        line-height: 1.6;
        font-weight: 600;
        color: #94c93b;
    }
    .txt1:hover{
        color: #94c93b;
    }

</style>