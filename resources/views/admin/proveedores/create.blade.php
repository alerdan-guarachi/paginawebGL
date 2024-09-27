@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedores.index') }}">REGRESAR</a>
<h1>NUEVO PROVEEDOR</h1>
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
        {!! Form::model($proveedor, ['route' => ['admin.proveedores.store', $proveedor], 'method' => 'POST']) !!}
        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}

        <div class="row ">
            <div class="col-lg-12">
                {!! Form::label('', 'DATOS DEL PROVEEDOR') !!}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('proveedor', 'Nombre Completo:') !!}
                            {!! Form::text('proveedor', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('proveedor')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('ciudad', 'Ciudad:') !!}
                            {!! Form::select('ciudad', $departamentos, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('ciudad')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('estadoproveedor', 'Estado:') !!}
                            {!! Form::select('estadoproveedor', $estadoproveedor, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('estadoproveedor')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('celular', 'Celular:') !!}
                            {!! Form::text('celular', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '30', 'onkeypress' => 'return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 45']) !!}
                            @error('celular')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('telefono', 'Teléfono:') !!}
                            {!! Form::text('telefono', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '30', 'onkeypress' => 'return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 45']) !!}
                            @error('telefono')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('direccion', 'Dirección:') !!}
                            {!! Form::text('direccion', null, ['class' => 'form-control', 'placeholder' => '' , 'maxlength' => '200']) !!}
                            @error('direccion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('nit', 'NIT:') !!}
                            {!! Form::text('nit', null, ['class' => 'form-control', 'placeholder' => '','maxlength' => '20', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                            @error('nit')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('mododepago', 'Modo de pago:') !!}
                            {!! Form::select('mododepago', $mododepago, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('mododepago')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('banco', 'Banco:') !!}
                            {!! Form::select('banco', $bancos, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('banco')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('cuenta', 'Número de cuenta:') !!}
                            {!! Form::text('cuenta', null, ['class' => 'form-control', 'placeholder' => '','maxlength' => '30', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                            @error('cuenta')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('tipocuenta', 'Tipo de cuenta:') !!}
                            {!! Form::select('tipocuenta', $tipocuenta, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('tipocuenta')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                {!! Form::label('', 'DATOS REFERENCIALES') !!}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('personacontacto', 'Persona de referencia:') !!}
                            {!! Form::text('personacontacto', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('personacontacto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('celularreferencia', 'Celular de referencia:') !!}
                            {!! Form::text('celularreferencia', null, ['class' => 'form-control', 'placeholder' => '','maxlength' => '30', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                            @error('celularreferencia')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('telefonoreferencia', 'Teléfono de referencia:') !!}
                            {!! Form::text('telefonoreferencia', null, ['class' => 'form-control', 'placeholder' => '','maxlength' => '30', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                            @error('telefonoreferencia')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>   
        {!! Form::submit('Crear proveedor', ['class' => 'btn btn-crear']) !!}
        {!! Form::close() !!}     
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });
</script>

<script>
//CANCELAR FUNCION DE LA TECLA ENTER
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
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
    .mensaje-error {
        color: #e1172b;
        font-family: "Times New Roman";
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        font-size: 12.5px;
        font-weight: bold;
        display: inline-block;
        margin-left: -10px;
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