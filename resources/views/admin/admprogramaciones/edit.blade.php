@extends('adminlte::page')

@section('content_header')
<h1>Editar Empresa</h1>
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

<div class="container col-lg-12">
    <div class="card">
        <div class="card-body">
            <div class="row ">
                <div class="col-lg-6">
                    {!! Form::model($empresa, ['route' => ['admin.empresas.update', $empresa], 'method' => 'put']) !!}

                    <div class="form-group">
                        {!! Form::label('nombreempresa', 'Nombre:') !!}
                        {!! Form::text('nombreempresa', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el nombre de la empresa' , 'required' => 'required']) !!}
                        @error('nombreempresa')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                            
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('contacto', 'Contacto:') !!}
                        {!! Form::text('contacto', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el contacto de la empresa']) !!}
                        @error('contacto')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                            
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('celular', 'Celular:') !!}
                        {!! Form::text('celular', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el celular de la empresa']) !!}
                        @error('celular')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                            
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('telefono', 'Telefono:') !!}
                        {!! Form::text('telefono', null, ['class' => 'form-control', 'placeholder' => 'Ingrese el telefono de la empresa']) !!}
                        @error('telefono')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                            
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('direccion', 'Direccion:') !!}
                        {!! Form::text('direccion', null, ['class' => 'form-control', 'placeholder' => 'Ingrese la direccion de la empresa']) !!}
                        @error('direccion')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                            
                        @enderror
                    </div>
                    

                        {!! Form::submit('Editar empresa', ['class' => 'btn btn-editar']) !!}
                    {!! Form::close() !!}
                </div>
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

.btn-editar {
    background-color:  #ffffff;
    color: #94c93b;
    border-color: #94c93b;
    border-radius: 5px;
    padding: 10px 20px;
    }

.btn-editar:hover {
    background-color: #94c93b;
    color: #ffffff;
    }
</style>
@stop