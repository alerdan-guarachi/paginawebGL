@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedoresservicios.listaproveedoresservicios') }}">REGRESAR</a>

@can('admin.proveedoresservicios.edit')
<a class="btn btn-sm btn-editar float-right" style="margin-right: 10px;" href="{{route('admin.proveedoresservicios.editarproveedorserviciobasico', $proveedoresservicios)}}">EDITAR PROVEEDOR</a>
@endcan

<h1>INFORMACIÓN DE {{$proveedoresservicios->sigla}}</h1>
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
            <div class="col-lg-6">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong >DATOS DEL PROVEEDOR</strong><br>
                                    <div class="table-responsive">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{$proveedoresservicios->id}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Razon Social</th>
                                                    <td>{{$proveedoresservicios->razonsocial}}</td>
                                                </tr> 
                                                <tr>
                                                    <th>Sigla</th>
                                                    <td>{{$proveedoresservicios->sigla}}</td>
                                                </tr>
                                                <tr>
                                                    <th>NIT</th>
                                                    <td>{{$proveedoresservicios->nit}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ciudad</th>
                                                    <td>{{$proveedoresservicios->ciudad}}</td>
                                                </tr>
                                                <tr> 
                                                    <th>Dirección</th>
                                                    <td>{{$proveedoresservicios->direccion}}</td>
                                                </tr>
                                                @if (!empty($proveedoresservicios->ciudad2))
                                                    <tr> 
                                                        <th>Ciudad 2</th>
                                                        <td>{{ $proveedoresservicios->ciudad2 }}</td>
                                                    </tr>
                                                @endif

                                                @if (!empty($proveedoresservicios->direccion2))
                                                    <tr> 
                                                        <th>Dirección 2</th>
                                                        <td>{{ $proveedoresservicios->direccion2 }}</td>
                                                    </tr>
                                                @endif

                                                <tr>
                                                    <th>Celular</th>
                                                    <td>{{$proveedoresservicios->celular}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Banco Origen</th>
                                                    <td>{{$proveedoresservicios->bancoorigen}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Emisión</th>
                                                    <td>{{$proveedoresservicios->emision}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Estado</th>
                                                    <td>{{$proveedoresservicios->estado}}</td>
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
            <div class="col-lg-6">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <div class="col-md-12">
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
            {{-- <div class="col-lg-8">
                <div class="profile-feed">
                    <div class="d-flex align-items-start profile-feed-item">
                        <div class="form-group col-md-12">
                            <div class="row">
                                <style>
                                    .table-sm td,
                                    .table-sm th {
                                        text-align: left !important;
                                    }
                                    .table-striped-custom tbody tr:nth-child(odd) {
                                        background-color: #f9f9f9;
                                    }
                                </style>
                                <div class="col-md-12">
                                    <strong>PLANES DEL PROVEEDOR</strong><br>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm table-striped-custom">
                                            <thead>
                                                <tr>
                                                    <th>Plan</th>
                                                    <th>Sigla</th>
                                                    @if($mostrarColumnas['codigo']) <th>Código</th> @endif
                                                    @if($mostrarColumnas['contrato']) <th>Contrato</th> @endif
                                                    @if($mostrarColumnas['linea']) <th>Línea</th> @endif
                                                    @if($mostrarColumnas['cuenta']) <th>Cuenta</th> @endif
                                                    @if($mostrarColumnas['servicio']) <th>Servicio</th> @endif
                                                    <th>Monto Fijo</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($planes as $plan)
                                                    <tr>
                                                        <td>{{ $plan->plan }}</td>
                                                        <td>{{ $plan->sigla }}</td>
                                                        @if($mostrarColumnas['codigo']) <td>{{ $plan->codigo }}</td> @endif
                                                        @if($mostrarColumnas['contrato']) <td>{{ $plan->contrato }}</td> @endif
                                                        @if($mostrarColumnas['linea']) <td>{{ $plan->linea }}</td> @endif
                                                        @if($mostrarColumnas['cuenta']) <td>{{ $plan->cuenta }}</td> @endif
                                                        @if($mostrarColumnas['servicio']) <td>{{ $plan->servicio }}</td> @endif
                                                        <td>{{ $plan->montofijo }}</td>
                                                        <td>
                                                            @if($plan->estado === 'ACTIVO')
                                                                <span class="badge bg-success">ACTIVO</span>
                                                            @elseif($plan->estado === 'INACTIVO')
                                                                <span class="badge bg-danger">INACTIVO</span>
                                                            @else
                                                                <span class="badge bg-secondary">{{ $plan->estado }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center">No hay registros disponibles</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
@endsection
