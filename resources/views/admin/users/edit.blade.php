@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.users.index') }}">REGRESAR</a>
<h5>ASIGNAR ROL A:</h5>
<h3>{{$user->name}}</h3>
@stop

@section('content')
@if (session('info'))
        <div id="alert-info" class="alert alert-success">
            <strong>{{ session('info') }}</strong>
        </div>
        <script>
            setTimeout(function() {
                $('#alert-info').fadeOut('fast');
            }, 5000);
        </script>
    @endif
<div class="card">
    <div class="card-body">
        <h2>Lista de roles:</h2>
        {!! Form::model($user, ['route' => ['admin.users.update', $user], 'method' => 'put']) !!}
            @foreach ($roles as $role)
                <div class="form-check">
                    {!! Form::checkbox('roles[]', $role->id, null, ['id' => 'role_'.$role->id, 'class' => 'form-check-input']) !!}
                    <label class="form-check-label" for="role_{{ $role->id }}">
                        {{ $role->name }}
                    </label>
                </div>
            @endforeach
            <div class="mt-2">
                {!! Form::submit('ASIGNAR ROL', ['class' => 'btn btn-crear']) !!}
            </div>
        {!! Form::close() !!}
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
        }
    h2 {color:black; 
        font-family: "Segoe UI";
        font-weight: 900;
        font-size: 100%;
        }
    label {color:black; 
        font-family: "Segoe UI";
        font-weight: 450;
        font-size: 90%;
        margin-top: 1rem; /* Espacio superior ajustado */
        margin-bottom: 1rem; /* Espacio inferior ajustado */
        }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;
        }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .form-check-input {
         margin-top: 1.2rem; /* Espacio superior ajustado */
        margin-bottom: 1.2rem; /* Espacio inferior ajustado */
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
</style>
@stop

@section('js')
<script>console.log('Hi!');</script>
@stop