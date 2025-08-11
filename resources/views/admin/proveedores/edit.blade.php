@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedoresservicios.listaproveedoresservicios', $proveedor) }}">REGRESAR</a>
    <h1>EDITAR PROVEEDOR</h1>
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
        {!! Form::model($proveedor, ['route' => ['admin.proveedores.update', $proveedor], 'method' => 'PUT']) !!}
        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
        {!! Form::hidden('usuarioregistro') !!}
        {!! Form::hidden('usuarioactualizacion', auth()->user()->name) !!}

        <div class="row "> 
            <div class="col-lg-12">
                {!! Form::label('', 'DATOS DEL PROVEEDOR') !!}
                <div class="row">
                    <div class="col-lg-1" hidden>
                        <div class="form-group">
                            {!! Form::label('id', 'ID Prov.:') !!}
                            {!! Form::text('id', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90', 'readonly' => true]) !!}
                            @error('id')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('proveedor', 'Nombre Proveedor:') !!}
                            {!! Form::text('proveedor', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90', 'readonly' => true]) !!}
                            @error('proveedor')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
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
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('ci', 'CI:') !!}
                            {!! Form::text('ci', null, ['class' => 'form-control', 'placeholder' => '','maxlength' => '20', 'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57']) !!}
                            @error('ci')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
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
                </div>
                <div class="row">
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('ciudad', 'Ciudad 1:') !!}
                            {!! Form::select('ciudad', $ciudades, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('ciudad')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('direccion', 'Dirección 2:') !!}
                            {!! Form::text('direccion', null, ['class' => 'form-control', 'placeholder' => '' , 'maxlength' => '200']) !!}
                            @error('direccion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">  
                        <div class="form-group">
                            <label for="linkubicacion">Link Dirección 1:</label>
                            <div style="display: flex; width: 100%;">
                                {!! Form::text('linkubicacion', null, [
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'maxlength' => '500',
                                    'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                    'style' => 'flex: 1; margin-right: 5px;'
                                ]) !!}
                                <a id="open-link" class="btn btn-ver" type="button" style="flex-shrink: 0;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                            </div>
                        </div>
                        @error('linkubicacion')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const inputField = document.querySelector('input[name="linkubicacion"]');
                            const openLinkButton = document.getElementById('open-link');
                            function updateButtonState() {
                                const link = inputField.value;
                                if (link.startsWith('http://') || link.startsWith('https://')) {
                                    openLinkButton.style.pointerEvents = 'auto';
                                    openLinkButton.style.opacity = '1';
                                    openLinkButton.onclick = function(event) {
                                        event.preventDefault();
                                        window.open(link, '_blank');
                                    };
                                } else {
                                    openLinkButton.style.pointerEvents = 'none';
                                    openLinkButton.style.opacity = '0.5';
                                    openLinkButton.onclick = null;
                                }
                            }
                            updateButtonState();
                            inputField.addEventListener('input', updateButtonState);
                        });
                    </script>

                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('ciudad2', 'Ciudad 2:') !!}
                            {!! Form::select('ciudad2', $ciudades, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('ciudad2')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('direccion2', 'Dirección 2:') !!}
                            {!! Form::text('direccion2', null, ['class' => 'form-control', 'placeholder' => '' , 'maxlength' => '200']) !!}
                            @error('direccion2')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">  
                        <div class="form-group">
                            <label for="linkubicacion2">Link Dirección 2:</label>
                            <div style="display: flex; width: 100%;">
                                {!! Form::text('linkubicacion2', null, [
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'maxlength' => '500',
                                    'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                    'style' => 'flex: 1; margin-right: 5px;'
                                ]) !!}
                                <a id="open-link2" class="btn btn-ver" type="button" style="flex-shrink: 0;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                            </div>
                        </div>
                        @error('linkubicacion2')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const inputField = document.querySelector('input[name="linkubicacion2"]');
                            const openLinkButton = document.getElementById('open-link2');
                            function updateButtonState2() {
                                const link = inputField.value;
                                if (link.startsWith('http://') || link.startsWith('https://')) {
                                    openLinkButton.style.pointerEvents = 'auto';
                                    openLinkButton.style.opacity = '1';
                                    openLinkButton.onclick = function(event) {
                                        event.preventDefault();
                                        window.open(link, '_blank');
                                    };
                                } else {
                                    openLinkButton.style.pointerEvents = 'none';
                                    openLinkButton.style.opacity = '0.5';
                                    openLinkButton.onclick = null;
                                }
                            }
                            updateButtonState2();
                            inputField.addEventListener('input', updateButtonState2);
                        });
                    </script>
                </div>
                <div class="row">
                    <div class="col-lg-2">
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
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('banco', 'Banco:') !!}
                            {!! Form::select('banco', $bancos, $bancoActual, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '255']) !!}
                            @error('banco')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2">
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
                    <div class="col-lg-2">
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
                    <div class="col-lg-2">
                        <div class="form-group">
                            {!! Form::label('estadoproveedor', 'Estado Provedor:') !!}
                            {!! Form::select('estadoproveedor', $estadoproveedor, null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '45']) !!}
                            @error('estadoproveedor')
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
        {!! Form::submit('ACTUALIZAR PROVEEDOR', ['class' => 'btn btn-crear']) !!}
        {!! Form::close() !!}     
    </div>
</div>
@stop
@section('js')
<script>
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });
</script>
@stop
@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-ver {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-ver:hover {
        background-color: #faa625;
        color: #ffffff;
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
