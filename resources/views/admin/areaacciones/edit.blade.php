@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.areaacciones.index') }}">REGRESAR</a>
<h1>EDITAR {{ $areaaccion->tipoarea }}</h1>
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
        <div class="row ">
            <div class="col-lg-12">
                {!! Form::model($areaaccion, ['route' => ['admin.areaacciones.update', $areaaccion], 'method' => 'PUT']) !!}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('proveedor', 'Proveedor:') !!}
                            {!! Form::text('proveedor', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                            @error('proveedor')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('sucursal', 'Sucursal:') !!}
                            {!! Form::text('sucursal', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                            @error('sucursal')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('area', 'Área:') !!}
                            {!! Form::text('area', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                            @error('area')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('accion', 'Áccion:') !!}
                            {!! Form::text('accion', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                            @error('area')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('asociado', 'Tipo de Cliente:') !!}
                            {!! Form::text('asociado', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                            @error('asociado')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('servicio', 'Atención de Servicio:') !!}
                            {!! Form::select('servicio', ['INTERNO' => 'INTERNO', 'EXTERNO' => 'EXTERNO'], $areaaccion->servicio ?? 'INTERNO', ['class' => 'form-control']) !!}
                            @error('servicio')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('pagoservicio', 'Pago de servicio:') !!}
                            {!! Form::select('pagoservicio', $pagoservicio, null, [
                                'class' => 'form-control',
                                'placeholder' => '',
                            ]) !!}
                            @error('servicio')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('comision', 'Comisión:') !!}
                            {!! Form::text('comision', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('comision')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('precio', 'Precio Venta:') !!}
                            {!! Form::text('precio', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('precio')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('preciocompra', 'Precio Compra:') !!}
                            {!! Form::text('preciocompra', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('preciocompra')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('estado', 'Estado:') !!}
                            {!! Form::select('estado', $estadoproveedor, $areaaccion->estado ?? 'ACTIVO', ['class' => 'form-control'])!!}
                            @error('estado')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
            {!! Form::submit('ACTUALIZAR', ['class' => 'btn btn-crear']) !!}
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
<script>
    function mostrarVistaPrevia(input) {
        if (input.files && input.files[0]) {
            var lector = new FileReader();
            lector.onload = function(e) {
                $('#vista-previa').attr('src', e.target.result);
                $('#vista-previa').show();
            }
            lector.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        $("#picture").change(function() {
            mostrarVistaPrevia(this);
        });
    });
</script>

@endsection
@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    label {
        margin-bottom: 0%;
        }
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
    #vista-previa {
        display: block;
        max-width: 100%;
        max-height: 100%;
        margin-top: 10px;
        border: 1px solid #ccc;
        padding: 5px;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2);
        }
    h2 {font-size:  80%;
        text-align : right;
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
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
</style>
@stop