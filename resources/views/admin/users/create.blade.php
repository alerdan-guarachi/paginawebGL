@extends('adminlte::page')

@section('content_header')
<h1>Crear usuario</h1>
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
            {!! Form::open(['route' => 'admin.users.store']) !!}
            <div class="form-group">
                {!! Form::label('name', 'Nombre') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el nombre del usuario']) !!}
                @error('name')
                <small class="text-danger">
                    {{$message}}
                </small>
                @enderror
                {!! Form::label('email', 'Email') !!}
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el email del usuario']) !!}
                @error('email')
                <small class="text-danger">
                    {{$message}}
                </small>
                @enderror
                {!! Form::label('password', 'Contraseña') !!}
                {!! Form::password('password', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el password del usuario']) !!}
                @error('password')
                <small class="text-danger">
                    {{$message}}
                </small>
                @enderror
            </div>
            {!! Form::submit('Crear usuario', ['class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>

    </div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>console.log('Hi!');</script>
@stop