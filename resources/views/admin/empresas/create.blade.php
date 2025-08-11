@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.empresas.index') }}">REGRESAR</a>
<h1>NUEVA EMPRESA</h1>
@stop

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 3000);
    </script>
@endif

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                {!! Form::open(['route' => 'admin.empresas.store', 'method'=>'POST']) !!}
                    <div class="row">
                        <div class="form-group col-lg-6">
                            {!! Form::label('nombreempresa', 'Nombre:') !!}
                            {!! Form::text('nombreempresa', null, ['class' => 'form-control', 'required' => 'required']) !!}
                            @error('nombreempresa')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                            @enderror
                        </div>
                        <div class="form-group col-lg-6">
                            {!! Form::label('direccion', 'Dirección:') !!}
                            {!! Form::text('direccion', null, ['class' => 'form-control', 'required' => 'required']) !!}
                            @error('direccion')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>  
                            @enderror
                        </div>
                        <div class="form-group col-lg-4">
                            {!! Form::label('contacto', 'Contacto:') !!}
                            {!! Form::text('contacto', null, ['class' => 'form-control', 'required' => 'required']) !!}
                            @error('contacto')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                                
                            @enderror
                        </div>
                        <div class="form-group col-lg-4">
                            {!! Form::label('celular', 'Celular:') !!}
                            {!! Form::text('celular', null, ['class' => 'form-control', 'required' => 'required']) !!}
                            @error('celular')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                                
                            @enderror
                        </div>
                        <div class="form-group col-lg-4">
                            {!! Form::label('telefono', 'Teléfono:') !!}
                            {!! Form::text('telefono', null, ['class' => 'form-control', 'required' => 'required']) !!}
                            @error('telefono')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                                
                            @enderror
                        </div>
                    </div>
                {!! Form::submit('CREAR EMPRESA', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }

    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        }

    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-regresar {
        background-color:  #ffffff;
        color: #2d2ac9;
        border-color: #2d2ac9;
        border-radius: 5px;
        padding: 5px 10px;
        }

    .btn-regresar:hover {
        background-color: #2d2ac9;
        color: #ffffff;
        }
</style>
@stop
