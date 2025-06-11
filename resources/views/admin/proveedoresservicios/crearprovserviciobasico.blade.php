@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedoresservicios.listaproveedoresservicios') }}">REGRESAR</a>
<h1>NUEVO PROVEEDOR DE SERVICIO BÁSICO</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/proveedoresserviciosgeneral.css') }}">
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
                {!! Form::open(['route' => 'admin.proveedoresservicios.guardarproveedor', 'method'=>'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                <input type="hidden" class="form-control" id="categoria" name="categoria" value="PROVEEDOR SERVICIO BASICO">
                <div class="row">
                    <div class="form-group col-lg-4"> 
                        {!! Form::label('razonsocial', 'Razón Social:') !!}
                        {!! Form::text('razonsocial', null, ['class' => 'form-control', 'id' => 'razonsocial']) !!}
                        @error('razonsocial')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('sigla', 'Sigla:') !!}
                        {!! Form::text('sigla', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('sigla')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('nit', 'NIT:') !!}
                        {!! Form::text('nit', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('nit')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('celular', 'Celular:') !!}
                        {!! Form::text('celular', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('celular')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('telefono', 'Teléfono:') !!}
                        {!! Form::text('telefono', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('telefono')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-2">
                        {!! Form::label('ciudad', 'Ciudad:') !!}
                        {!! Form::select('ciudad', $sucursal, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('ciudad')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('direccion', 'Dirección:') !!}
                        {!! Form::text('direccion', null, ['class' => 'form-control', 'maxlength' => '255']) !!}
                        @error('direccion')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('bancoorigen', 'Cuenta Origen:') !!}
                        {!! Form::select('bancoorigen', [
                            'CUENTA FACTURADA' => 'CUENTA FACTURADA', 
                            'CUENTA NO FACTURADA' => 'CUENTA NO FACTURADA',
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('bancoorigen')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
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
                    <div class="form-group col-lg-2">
                        {!! Form::label('tipoplanilla', 'Tipo planilla:') !!}
                        {!! Form::select('tipoplanilla', [
                            'PAGO A TERCERO' => 'PAGO A TERCERO', 
                            'PAGO INTERBANCARIO' => 'PAGO INTERBANCARIO',
                            'PAGO EN LINEA' => 'PAGO EN LINEA',
                            'PAGO QR' => 'PAGO QR',
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('tipoplanilla')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('estado', 'Estado:') !!}
                        {!! Form::select('estado', $estado, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('estado')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-4">
                        {!! Form::label('tipoorden1', 'Tipo Orden 1:') !!}
                        {!! Form::select('tipoorden1', [
                            'ORDEN DE COMPRA' => 'ORDEN DE COMPRA',
                            'ORDEN DE SERVICIO' => 'ORDEN DE SERVICIO',
                            'ORDEN DE PERSONAL' => 'ORDEN DE PERSONAL',
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('parentesco')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('tipoorden2', 'Tipo Orden 2:') !!}
                        {!! Form::select('tipoorden2', [
                            'ORDEN DE COMPRA' => 'ORDEN DE COMPRA',
                            'ORDEN DE SERVICIO' => 'ORDEN DE SERVICIO',
                            'ORDEN DE PERSONAL' => 'ORDEN DE PERSONAL',
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('parentesco')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('tipoorden3', 'Tipo Orden 3:') !!}
                        {!! Form::select('tipoorden3', [
                            'ORDEN DE COMPRA' => 'ORDEN DE COMPRA',
                            'ORDEN DE SERVICIO' => 'ORDEN DE SERVICIO',
                            'ORDEN DE PERSONAL' => 'ORDEN DE PERSONAL',
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('parentesco')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="form-group col-lg-2">
                        {!! Form::label('codigo', 'Codigo:') !!}
                        {!! Form::text('codigo', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('codigo')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('contrato', 'Contrato:') !!}
                        {!! Form::text('contrato', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('contrato')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('linea', 'Linea:') !!}
                        {!! Form::text('linea', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('linea')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('cuenta', 'Cuenta:') !!}
                        {!! Form::text('cuenta', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('cuenta')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('servicio', 'Servicio:') !!}
                        {!! Form::text('servicio', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('servicio')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    
                </div> --}}
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