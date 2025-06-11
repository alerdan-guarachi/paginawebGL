@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedoresservicios.listaproveedoresservicios') }}">REGRESAR</a>
<h1>NUEVO PROVEEDOR GENERAL</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/proveedoresservicios.css') }}">
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
                
                <input type="hidden" class="form-control" id="categoria" name="categoria" value="PROVEEDOR GENERAL">

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
                        {!! Form::label('ci', 'CI:') !!}
                        {!! Form::text('ci', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('ci')
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
                    <div class="form-group col-lg-3">
                        {!! Form::label('correo', 'Correo:') !!}
                        {!! Form::text('correo', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('correo')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('tipotransaccion', 'Tipo transacción:') !!}
                        {!! Form::select('tipotransaccion', [
                            'TRANSFERENCIA BANCARIA' => 'TRANSFERENCIA BANCARIA', 
                            'DEPOSITO BANCARIO' => 'DEPOSITO BANCARIO',
                            'CHEQUE' => 'CHEQUE',
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('tipotransaccion')
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
                    <div class="form-group col-lg-3">
                        {!! Form::label('ciudad', 'Ciudad 1:') !!}
                        {!! Form::select('ciudad', $sucursal, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('ciudad')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('direccion', 'Dirección 1:') !!}
                        {!! Form::text('direccion', null, ['class' => 'form-control', 'maxlength' => '255']) !!}
                        @error('direccion')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('ciudad2', 'Ciudad 2:') !!}
                        {!! Form::select('ciudad2', $sucursal, null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('ciudad')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('direccion2', 'Dirección 2:') !!}
                        {!! Form::text('direccion2', null, ['class' => 'form-control', 'maxlength' => '255']) !!}
                        @error('direccion')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-lg-3">
                        {!! Form::label('banco', 'Nombre de Banco 1:') !!}
                        {!! Form::select('banco', $bancos, null, ['class' => 'form-control', 'maxlength' => '255', 'placeholder' => '']) !!}
                        @error('banco')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('tipocuenta', 'Tipo Cuenta 1:') !!}
                        {!! Form::select('tipocuenta', [
                            'CUENTA CORRIENTE' => 'CUENTA CORRIENTE',
                            'CAJA DE AHORRO' => 'CUENTA DE AHORRO',
                            'CUENTA INFANTIL' => 'CUENTA INFANTIL',
                            'CUENTA JOVEN' => 'CUENTA JOVEN',
                            'CUENTA MANCOMUNADA' => 'CUENTA MANCOMUNADA',
                            'CUENTA NÓMINA' => 'CUENTA NÓMINA',
                            'CUENTA NO NÓMINA' => 'CUENTA NO NÓMINA',
                            'CUENTA ONLINE' => 'CUENTA ONLINE',
                            'CUENTA REMUNERADA' => 'CUENTA REMUNERADA', 
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('tipocuenta')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('numcuenta', 'Nro. de Cuenta 1:') !!}
                        {!! Form::text('numcuenta', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('numcuenta')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
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
                    <div class="form-group col-lg-3">
                        {!! Form::label('sigla', 'Sigla 1:') !!}
                        {!! Form::text('sigla', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('sigla')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3">
                        {!! Form::label('sigla2', 'Sigla 2:') !!}
                        {!! Form::text('sigla2', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('sigla2')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('tipobusqueda', 'Tipo de busqueda 1:') !!}
                        {!! Form::text('tipobusqueda', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('tipobusqueda')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('tipobusqueda2', 'Tipo de busqueda 2:') !!}
                        {!! Form::text('tipobusqueda2', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('tipobusqueda2')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('tipobusqueda3', 'Tipo de busqueda 3:') !!}
                        {!! Form::text('tipobusqueda3', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('tipobusqueda3')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-lg-4">
                        {!! Form::label('imagenqr', 'Imagen QR:') !!}
                        {!! Form::file('imagenqr', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('imagenqr')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('fechavencqr', 'Fecha venc. QR:') !!}
                        {!! Form::text('fechavencqr', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('fechavencqr')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('esquemapago', 'Esquema de pago:') !!}
                        {!! Form::text('esquemapago', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('esquemapago')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-lg-2">
                        {!! Form::label('representantelegal', 'Representante legal:') !!}
                        {!! Form::text('representantelegal', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('representantelegal')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('nombrebancotercero', 'Nombre de Banco Tercero:') !!}
                        {!! Form::select('nombrebancotercero', $bancos, null, ['class' => 'form-control', 'maxlength' => '255', 'placeholder' => '']) !!}
                        @error('nombrebancotercero')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('nrocuentatercero', 'Nro. Cuenta Tercero:') !!}
                        {!! Form::text('nrocuentatercero', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('nrocuentatercero')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-2">
                        {!! Form::label('tipocuentatercero', 'Tipo Cuenta Tercero:') !!}
                        {!! Form::select('tipocuentatercero', [
                            'CAJA DE AHORROS' => 'CAJA DE AHORROS', 
                            'CUENTA CORRIENTE' => 'CUENTA CORRIENTE',
                        ], null, ['class' => 'form-control', 'maxlength' => '45', 'placeholder' => '']) !!}
                        @error('tipocuentatercero')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-4">
                        {!! Form::label('documentorespaldo', 'Documento Tercero:') !!}
                        {!! Form::file('documentorespaldo', null, ['class' => 'form-control', 'maxlength' => '45']) !!}
                        @error('documentorespaldo')
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