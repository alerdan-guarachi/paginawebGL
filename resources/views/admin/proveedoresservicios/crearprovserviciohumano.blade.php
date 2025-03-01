@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedoresservicios.index') }}">REGRESAR</a>
<h1>NUEVO PROVEEDOR DE SERVICIO HUMANO</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/proveedoresservicios.css') }}">
<style>
    #vista-previa {
    width: 300px;
    height: 300px;
    object-fit: cover;
}

</style>
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
            <div class="col-lg-9">
                {!! Form::open(['route' => 'admin.proveedoresservicios.store', 'method'=>'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                
                <div class="row">
                    <div class="form-group col-lg-8"> 
                        {!! Form::label('nombrecompleto', 'Nombre Completo:') !!}
                        {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'id' => 'nombrecompleto']) !!}

                        {!! Form::hidden('proveedor', null, ['class' => 'form-control', 'id' => 'proveedor']) !!}
                    
                        @error('nombrecompleto')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <script>
                        document.getElementById('nombrecompleto').addEventListener('input', function() {
                            document.getElementById('proveedor').value = this.value;
                        });
                    </script>
                    
                    <div class="form-group col-lg-4">
                        {!! Form::label('sucursal', 'Sucursal:') !!}
                        {!! Form::select('sucursal', $sucursal, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('sucursal')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-2">
                        {!! Form::label('ci', 'CI:') !!}
                        {!! Form::text('ci', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('ci')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('ciexp', 'CI exp.:') !!}
                        {!! Form::select('ciexp', $ciudadexp, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('ciexp')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-5">
                        {!! Form::label('direccion', 'Dirección:') !!}
                        {!! Form::text('direccion', null, ['class' => 'form-control', 'maxlength' => '255']) !!}
                        @error('direccion')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('emision', 'Emisión:') !!}
                        {!! Form::select('emision', [
                            'RECIBO' => 'RECIBO', 
                            'FACTURA' => 'FACTURA',
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('emision')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-3">
                        {!! Form::label('tipoproveedor', 'Tipo Proveedor:') !!}
                        {!! Form::select('tipoproveedor', [
                            'PERSONAL INTERNO' => 'PERSONAL INTERNO', 
                            'PERSONAL EXTERNO' => 'PERSONAL EXTERNO',
                            'PROVEEDOR DE SERVICIOS' => 'PROVEEDOR DE SERVICIOS',
                            'PASANTE' => 'PASANTE'
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('tipoproveedor')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    
                    <div class="form-group col-lg-3">
                        {!! Form::label('nit', 'NIT:') !!}
                        {!! Form::text('nit', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('nit')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('celular', 'Celular:') !!}
                        {!! Form::text('celular', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('celular')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('email', 'Email:') !!}
                        {!! Form::text('email', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('email')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6">
                        {!! Form::label('banco', 'Nombre de Banco:') !!}
                        {!! Form::select('banco', $bancos, null, ['class' => 'form-control', 'maxlength' => '255', 'placeholder' => '']) !!}
                        @error('banco')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-6">
                        {!! Form::label('numcuenta', 'Nro. de Cuenta:') !!}
                        {!! Form::text('numcuenta', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('numcuenta')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6">
                        {!! Form::label('contacto', 'Nombre de Contacto:') !!}
                        {!! Form::text('contacto', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('contacto')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-6">
                        {!! Form::label('celcontacto', 'Celular de Contacto:') !!}
                        {!! Form::text('celcontacto', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('celcontacto')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6">
                        {!! Form::label('fechaingreso', 'Fecha de ingreso (Solo Personal):') !!}
                        {!! Form::date('fechaingreso', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('fechaingreso')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    {{-- <div class="form-group col-lg-4">
                        {!! Form::label('fechasalida', 'Fecha de salida:') !!}
                        {!! Form::date('fechasalida', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('fechasalida')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div> --}}
                    <div class="form-group col-lg-6">
                        {!! Form::label('estado', 'Estado:') !!}
                        {!! Form::select('estado', $estado, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('estado')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    
                </div>
            </div>
            <div class="col-lg-3"> 
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="file">Foto de perfil:</label>
                            <input type="file" class="form-control-file" id="picture" name="picture" accept=".jpg, .jpeg, .png">
                            
                            <img id="vista-previa" src="#" alt="Vista previa de la imagen" style="display: none; max-width: 100%; height: auto;">
                            
                            @error('picture')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        {!! Form::submit('CREAR PROVEEDOR', ['class' => 'btn btn-crear']) !!}
        {!! Form::close() !!}
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