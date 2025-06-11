@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedoresservicios.listaproveedoresservicios') }}">REGRESAR</a>

@can('admin.proveedoresservicios.edit')
<a class="btn btn-sm btn-editar float-right" style="margin-right: 10px;" href="{{route('admin.proveedoresservicios.edit', $proveedoresservicios)}}">EDITAR PROVEEDOR</a>
@endcan

<h1>INFORMACIÓN DE {{$proveedoresservicios->razonsocial}}</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/verproveedor.css') }}">
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
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
        <div class="row">
            <div class="col-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong >DATOS PERSONALES</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{$proveedoresservicios->id}}</td>
                                                </tr>
                                                <tr>
                                                    <th>CI</th>
                                                    <td>{{$proveedoresservicios->ci}}</td>
                                                </tr> 
                                                <tr>
                                                    <th>NIT</th>
                                                    <td>{{$proveedoresservicios->nit}}</td>
                                                </tr> 
                                                <tr>
                                                    <th>Correo</th>
                                                    <td>{{$proveedoresservicios->correo}}</td>
                                                </tr>
                                                <tr> 
                                                    <th>Dirección.1</th>
                                                    <td>{{$proveedoresservicios->ciudad}} - {{$proveedoresservicios->direccion}}</td>
                                                </tr>
                                                @if (!empty($proveedoresservicios->ciudad2))
                                                <tr> 
                                                    <th>Dirección.2</th>
                                                    <td>{{$proveedoresservicios->ciudad2}} - {{$proveedoresservicios->direccion2}}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <th>Celular</th>
                                                    <td>{{$proveedoresservicios->celular}} - {{$proveedoresservicios->telefono}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>TIPO DE PAGO</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Modo de pago</th>
                                                    <td>{{$proveedoresservicios->tipotransaccion}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Emisión</th>
                                                    <td>{{$proveedoresservicios->emision}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Banco</th>
                                                    <td>{{$proveedoresservicios->banco}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tipo de cuenta</th>
                                                    <td>{{$proveedoresservicios->tipocuenta}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Cuenta</th>
                                                    <td>{{$proveedoresservicios->numcuenta}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Banco Origen</th>
                                                    <td>{{$proveedoresservicios->bancoorigen}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>DATOS REFERENCIALES</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Contacto</th>
                                                    <td>{{$proveedoresservicios->contacto}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Cel. Contacto</th>
                                                    <td>{{$proveedoresservicios->celcontacto}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <strong>TIPO DE ORDENES Y PLANILLA</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Órdenes</th>
                                                    <td>
                                                        {{ $proveedoresservicios->tipoorden1 }}
                                                        @if(!empty($proveedoresservicios->tipoorden2))
                                                            - {{ $proveedoresservicios->tipoorden2 }}
                                                        @endif
                                                        @if(!empty($proveedoresservicios->tipoorden3))
                                                            - {{ $proveedoresservicios->tipoorden3 }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Planilla</th>
                                                    <td>{{$proveedoresservicios->tipoplanilla}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
