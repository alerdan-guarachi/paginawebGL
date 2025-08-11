@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedoresservicios.show', $proveedoresservicios) }}">REGRESAR</a>
<h1>EDITAR PROVEEDOR</h1>
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
        }, 3000);
    </script>
@endif

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                {!! Form::model($proveedoresservicios, ['route' => ['admin.proveedoresservicios.update', $proveedoresservicios], 'method' => 'put']) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                
                <input type="hidden" class="form-control" id="categoria" name="categoria" value="PROVEEDOR GENERAL">

                <div class="row">
                    <div class="form-group col-lg-1"> 
                        {!! Form::label('id', 'ID:') !!}
                        {!! Form::text('id', null, ['class' => 'form-control', 'readonly']) !!}
                        @error('id')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group col-lg-3"> 
                        {!! Form::label('razonsocial', 'Razón Social:') !!}
                        {!! Form::text('razonsocial', null, ['class' => 'form-control', 'id' => 'razonsocial', 'readonly']) !!}
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
                            'PAGO CHEQUE' => 'PAGO CHEQUE',
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
                            'CAJA DE AHORRO' => 'CAJA DE AHORRO',
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

                {!! Form::submit('ACTUALIZAR', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
     </div>
</div>

@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
@endsection
