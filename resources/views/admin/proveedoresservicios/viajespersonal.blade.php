@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-azulgrande" href="{{ route('admin.proveedoresservicios.verpersonal', $id ) }}">REGRESAR</a>
<a class="btn float-right btn-verdegrande btn-sm" data-toggle="modal" data-target="#nuevasolicitudModal">NUEVA SOLICITUD</a>
<h1>VIAJES DE {{$proveedoresservicios->razonsocial}}</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/serviciosproveedores.css') }}">
@stop

@section('js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
@endsection

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
        {{-- PESTAÑAS DE NAVEGACION --}}
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                        SOLICITUDES PENDIENTES
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                        SOLICITUDES RECHAZADAS
                    </a>
                </li>     
                <li class="nav-item">
                    <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                        SOLICITUDES APROBADAS
                    </a>
                </li>     
                <li class="nav-item">
                    <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                        RENDICIÓN DE VIAJES
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                        HISTORIAL DE VIAJES
                    </a>
                </li>       
            </ul>
        </div>

        <div class="tab-content" id="myTabContent">
            {{-- SOLICITUDES DE VIAJE --}}
            <div class="tab-pane fade show active" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Destino</th>
                                <th>Motivo</th>
                                <th>Rango_Fechas</th>
                                <th>Cant.Dias</th>
                                <th>Transp.</th>
                                <th>Hosp.</th>
                                <th>Monto.Sol.</th>
                                <th>Observaciones</th>
                                <th>Aprobar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($personalviajes as $personalviaje)
                                @if ($personalviaje->estado === 'PENDIENTE')
                                    <tr>
                                        <td>{{$personalviaje->id}}</td>
                                        <td>{{$personalviaje->destino}}</td>
                                        <td>{{$personalviaje->motivoviaje}}</td>
                                        <td>{{$personalviaje->fechasalida}} - {{$personalviaje->fecharetorno}}</td>
                                        <td>{{$personalviaje->cantidaddias}} DIAS</td>
                                        <td>{{$personalviaje->mediotransporte}}</td>
                                        <td>{{$personalviaje->requierehospedaje}}</td>
                                        <td>{{$personalviaje->montosolicitado}}</td>
                                        <td>{{$personalviaje->observaciones}}</td>
                                        <td>
                                            <a onclick="aprobarSolicitud({{ $personalviaje->id }})" class="btn btn-sm btn-verdepequeno" title="APROBAR SOLICITUD">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a onclick="rechazarSolicitud({{ $personalviaje->id }})" class="btn btn-sm btn-rojopequeno2" title="RECHAZAR SOLICITUD">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            <style>
                                                .btn-rojopequeno2 {
                                                    background-color:  #ffffff;
                                                    color: #d61515;
                                                    border-color: #d61515;
                                                    border-radius: 5px;
                                                    padding: 2px 8px;
                                                    }
                                                .btn-rojopequeno2:hover {
                                                    background-color: #d61515;
                                                    color: #ffffff;
                                                    }
                                            </style>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                        <script>
                            function aprobarSolicitud(id) {
                                Swal.fire({
                                    title: '¿APROBAR SOLICITUD?',
                                    text: "",
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: "#94c93b",
                                    cancelButtonColor: "#faa625",
                                    confirmButtonText: 'SI, APROBAR',
                                    cancelButtonText: 'NO',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        fetch("{{ url('/vacaciones/aprobarsolicitudviaje') }}/" + id, {

                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({})
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if(data.success){
                                                Swal.fire('Aprobado', data.message, 'success').then(() => {
                                                    location.reload();
                                                });
                                            } else {
                                                Swal.fire('Error', data.message, 'error');
                                            }
                                        });
                                    }
                                });
                            }
                        </script>
                        <script>
                            function rechazarSolicitud(id) {
                                Swal.fire({
                                    title: '¿RECHAZAR SOLICITUD?',
                                    icon: 'question',
                                    input: 'text',
                                    inputLabel: 'Motivo del rechazo',
                                    inputPlaceholder: 'Escriba el motivo aquí...',
                                    inputAttributes: {
                                        'aria-label': 'Motivo del rechazo'
                                    },
                                    showCancelButton: true,
                                    confirmButtonColor: "#94c93b",
                                    cancelButtonColor: "#faa625",
                                    confirmButtonText: 'SI, RECHAZAR',
                                    cancelButtonText: 'NO',
                                    reverseButtons: true,
                                    inputValidator: (value) => {
                                        if (!value) {
                                            return 'Debe escribir un motivo para rechazar';
                                        }
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed && result.value) {
                                        fetch("{{ url('/vacaciones/rechazarsolicitudviaje') }}/" + id, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                motivorechazo: result.value
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if(data.success){
                                                Swal.fire('Rechazado', data.message, 'success').then(() => {
                                                    location.reload();
                                                });
                                            } else {
                                                Swal.fire('Error', data.message, 'error');
                                            }
                                        });
                                    }
                                });
                            }
                        </script>
                    </table>
                </div>
            </div>

            {{-- SOLICITUDES RECHAZADAS --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Destino</th>
                                <th>Motivo</th>
                                <th>Rango_Fechas</th>
                                <th>Cant.Dias</th>
                                <th>Transp.</th>
                                <th>Hosp.</th>
                                <th>Monto.Sol.</th>
                                <th>Observaciones</th>
                                <th>Usuario_Rechazo</th>
                                <th>Motivo_Rechazo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($personalviajes as $personalviaje)
                                @if ($personalviaje->estado === 'RECHAZADO')
                                    <tr>
                                        <td>{{$personalviaje->id}}</td>
                                        <td>{{$personalviaje->destino}}</td>
                                        <td>{{$personalviaje->motivoviaje}}</td>
                                        <td>{{$personalviaje->fechasalida}} - {{$personalviaje->fecharetorno}}</td>
                                        <td>{{$personalviaje->cantidaddias}} DIAS</td>
                                        <td>{{$personalviaje->mediotransporte}}</td>
                                        <td>{{$personalviaje->requierehospedaje}}</td>
                                        <td>{{$personalviaje->montosolicitado}}</td>
                                        <td>{{$personalviaje->observaciones}}</td>
                                        <td>{{$personalviaje->usuarioautorizacion}}</td>
                                        <td>{{$personalviaje->motivorechazo}}</td>
                                        <td><span class="badge {{ $personalviaje->estado == 'RECHAZADO' ? 'bg-danger' : 'bg-warning' }}">{{ $personalviaje->estado }}</span></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- SOLICITUDES APROBADAS --}}
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Destino</th>
                                <th>Motivo</th>
                                <th>Rango_Fechas</th>
                                <th>Cant.Dias</th>
                                <th>Transp.</th>
                                <th>Hosp.</th>
                                <th>Monto.Sol.</th>
                                <th>Observaciones</th>
                                <th>Usuario_Autorizador</th>
                                <th>Estado</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($personalviajes as $personalviaje)
                                @if ($personalviaje->estado === 'APROBADO' || $personalviaje->estado === 'PROGRAMADO')
                                    <tr>
                                        <td>{{$personalviaje->id}}</td>
                                        <td>{{$personalviaje->destino}}</td>
                                        <td>{{$personalviaje->motivoviaje}}</td>
                                        <td>{{$personalviaje->fechasalida}} - {{$personalviaje->fecharetorno}}</td>
                                        <td>{{$personalviaje->cantidaddias}} DIAS</td>
                                        <td>{{$personalviaje->mediotransporte}}</td>
                                        <td>{{$personalviaje->requierehospedaje}}</td>
                                        <td>{{$personalviaje->montosolicitado}}</td>
                                        <td>{{$personalviaje->observaciones}}</td>
                                        <td>{{$personalviaje->usuarioautorizacion}}</td>
                                        <td>
                                            @if($personalviaje->estado == 'APROBADO')
                                                <span class="badge bg-success">{{ $personalviaje->estado }}</span>
                                            @elseif($personalviaje->estado == 'PROGRAMADO')
                                                <span class="badge bg-warning">{{ $personalviaje->estado }}</span>
                                            @else
                                                {{ $personalviaje->estado }}
                                            @endif
                                        </td>
                                        
                                        <td hidden>{{$personalviaje->proveeedornombre}}</td>
                                        @if ($personalviaje->estado === 'APROBADO')
                                            <td>
                                                <a class="btn btn-naranjapequeno btn-sm" 
                                                data-toggle="modal" 
                                                data-target="#itinerarioModal"
                                                data-id="{{ $personalviaje->id }}"
                                                data-destino="{{ $personalviaje->destino }}"
                                                data-motivo="{{ $personalviaje->motivoviaje }}"
                                                data-rango="{{ $personalviaje->fechasalida }} - {{ $personalviaje->fecharetorno }}"
                                                data-dias="{{ $personalviaje->cantidaddias }}"
                                                data-transporte="{{ $personalviaje->mediotransporte }}"
                                                data-hospedaje="{{ $personalviaje->requierehospedaje }}"
                                                data-monto="{{ $personalviaje->montosolicitado }}"
                                                data-observaciones="{{ $personalviaje->observaciones }}"
                                                data-proveedornombre="{{ $personalviaje->proveedornombre }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        @endif
                                        @if ($personalviaje->estado === 'PROGRAMADO')
                                            <td>
                                                <a class="btn btn-naranjapequeno2 btn-sm" 
                                                data-toggle="modal" 
                                                data-target="#programacionModal"
                                                data-id="{{ $personalviaje->id }}"
                                                data-destino="{{ $personalviaje->destino }}"
                                                data-motivo="{{ $personalviaje->motivoviaje }}"
                                                data-rango="{{ $personalviaje->fechasalida }} - {{ $personalviaje->fecharetorno }}"
                                                data-dias="{{ $personalviaje->cantidaddias }}"
                                                data-transporte="{{ $personalviaje->mediotransporte }}"
                                                data-hospedaje="{{ $personalviaje->requierehospedaje }}"
                                                data-monto="{{ $personalviaje->montosolicitado }}"
                                                data-observaciones="{{ $personalviaje->observaciones }}"
                                                data-proveedornombre="{{ $personalviaje->proveedornombre }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        @endif
                                        <style>
                                            .btn-naranjapequeno2 {
                                                background-color:  #ffffff;
                                                color: #faa625;
                                                border-color: #faa625;
                                                border-radius: 5px;
                                                padding: 2px 6px;
                                                }
                                            .btn-naranjapequeno2:hover {
                                                background-color: #faa625;
                                                color: #ffffff;
                                                }
                                        </style>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- RENDICION DE VIAJES --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Destino</th>
                                <th>Motivo</th>
                                <th>Rango_Fechas</th>
                                <th>Cant.Dias</th>
                                <th>Transp.</th>
                                <th>Hosp.</th>
                                <th>Monto.Sol.</th>
                                <th>Observaciones</th>
                                <th>Usuario_Autorizador</th>
                                <th>Estado</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($personalviajes as $personalviaje)
                                @if ($personalviaje->estado === 'PROGRAMADO')
                                    <tr>
                                        <td>{{$personalviaje->id}}</td>
                                        <td>{{$personalviaje->destino}}</td>
                                        <td>{{$personalviaje->motivoviaje}}</td>
                                        <td>{{$personalviaje->fechasalida}} - {{$personalviaje->fecharetorno}}</td>
                                        <td>{{$personalviaje->cantidaddias}} DIAS</td>
                                        <td>{{$personalviaje->mediotransporte}}</td>
                                        <td>{{$personalviaje->requierehospedaje}}</td>
                                        <td>{{$personalviaje->montosolicitado}}</td>
                                        <td>{{$personalviaje->observaciones}}</td>
                                        <td>{{$personalviaje->usuarioautorizacion}}</td>
                                        <td>
                                            @if($personalviaje->estado == 'APROBADO')
                                                <span class="badge bg-success">{{ $personalviaje->estado }}</span>
                                            @elseif($personalviaje->estado == 'PROGRAMADO')
                                                <span class="badge bg-warning">{{ $personalviaje->estado }}</span>
                                            @else
                                                {{ $personalviaje->estado }}
                                            @endif
                                        </td>
                                        <td hidden>{{$personalviaje->proveeedornombre}}</td>
                                        <td>
                                            <a class="btn btn-naranjapequeno3 btn-sm" 
                                            data-toggle="modal" 
                                            data-target="#rendicionModal"
                                            data-id="{{ $personalviaje->id }}"
                                            data-destino="{{ $personalviaje->destino }}"
                                            data-motivo="{{ $personalviaje->motivoviaje }}"
                                            data-rango="{{ $personalviaje->fechasalida }} - {{ $personalviaje->fecharetorno }}"
                                            data-dias="{{ $personalviaje->cantidaddias }}"
                                            data-transporte="{{ $personalviaje->mediotransporte }}"
                                            data-hospedaje="{{ $personalviaje->requierehospedaje }}"
                                            data-monto="{{ $personalviaje->montosolicitado }}"
                                            data-observaciones="{{ $personalviaje->observaciones }}"
                                            data-proveedornombre="{{ $personalviaje->proveedornombre }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                        <style>
                                            .btn-naranjapequeno3 {
                                                background-color:  #ffffff;
                                                color: #faa625;
                                                border-color: #faa625;
                                                border-radius: 5px;
                                                padding: 2px 6px;
                                                }
                                            .btn-naranjapequeno3:hover {
                                                background-color: #faa625;
                                                color: #ffffff;
                                                }
                                        </style>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- HISTORIAL DE VIAJES --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Destino</th>
                                <th>Motivo</th>
                                <th>Rango_Fechas</th>
                                <th>Cant.Dias</th>
                                <th>Transp.</th>
                                <th>Hosp.</th>
                                <th>Monto.Sol.</th>
                                <th>Observaciones</th>
                                <th>Usuario_Autorizador</th>
                                <th>Estado</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($personalviajes as $personalviaje)
                                @if ($personalviaje->estado === 'FINALIZADO')
                                    <tr>
                                        <td>{{$personalviaje->id}}</td>
                                        <td>{{$personalviaje->destino}}</td>
                                        <td>{{$personalviaje->motivoviaje}}</td>
                                        <td>{{$personalviaje->fechasalida}} - {{$personalviaje->fecharetorno}}</td>
                                        <td>{{$personalviaje->cantidaddias}} DIAS</td>
                                        <td>{{$personalviaje->mediotransporte}}</td>
                                        <td>{{$personalviaje->requierehospedaje}}</td>
                                        <td>{{$personalviaje->montosolicitado}}</td>
                                        <td>{{$personalviaje->observaciones}}</td>
                                        <td>{{$personalviaje->usuarioautorizacion}}</td>
                                        <td><span class="badge bg-danger">{{ $personalviaje->estado }}</span></td>
                                        <td hidden>{{$personalviaje->proveeedornombre}}</td>
                                        <td>
                                            <a class="btn btn-naranjapequeno4 btn-sm" 
                                            data-toggle="modal" 
                                            data-target="#historialModal"
                                            data-id="{{ $personalviaje->id }}"
                                            data-destino="{{ $personalviaje->destino }}"
                                            data-motivo="{{ $personalviaje->motivoviaje }}"
                                            data-rango="{{ $personalviaje->fechasalida }} - {{ $personalviaje->fecharetorno }}"
                                            data-dias="{{ $personalviaje->cantidaddias }}"
                                            data-transporte="{{ $personalviaje->mediotransporte }}"
                                            data-hospedaje="{{ $personalviaje->requierehospedaje }}"
                                            data-monto="{{ $personalviaje->montosolicitado }}"
                                            data-observaciones="{{ $personalviaje->observaciones }}"
                                            data-proveedornombre="{{ $personalviaje->proveedornombre }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                        <style>
                                            .btn-naranjapequeno4 {
                                                background-color:  #ffffff;
                                                color: #faa625;
                                                border-color: #faa625;
                                                border-radius: 5px;
                                                padding: 2px 6px;
                                                }
                                            .btn-naranjapequeno4:hover {
                                                background-color: #faa625;
                                                color: #ffffff;
                                                }
                                        </style>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL NUEVA SOLICITUD -->
<div class="modal fade" id="nuevasolicitudModal" tabindex="-1" aria-labelledby="nuevasolicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="nuevasolicitudModalLabel" style="font-weight: 900;">NUEVA SOLICITUD DE ANTICIPO</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>                
            </div>
            <form action="{{ route('admin.proveedoresservicios.guardarviajespersonal', $id ) }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>ID Solicitante:</label>
                                    <input type="text" class="form-control" id="proveedorid" name="proveedorid" value="{{ $proveedoresservicios->id }}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-10">
                                <div class="form-group">
                                    <label>Nombre Solicitante:</label>
                                    <input type="text" class="form-control" id="proveedornombre" name="proveedornombre" value="{{ $proveedoresservicios->razonsocial }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Destino:</label>
                                    <select id="destino" name="destino" class="form-control">
                                        <option value=""></option>
                                        <option value="COCHABAMBA">COCHABAMBA</option>
                                        <option value="SANTA CRUZ">SANTA CRUZ</option>
                                        <option value="LA PAZ">LA PAZ</option>
                                        <option value="ORURO">ORURO</option>
                                        <option value="BENI">BENI</option>
                                        <option value="POTOSI">POTOSI</option>
                                        <option value="CHUQUISACA">CHUQUISACA</option>
                                        <option value="TARIJA">TARIJA</option>
                                        <option value="PANDO">PANDO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="form-group">
                                    <label>Motivo Viaje:</label>
                                    <input type="text" class="form-control" id="motivoviaje" name="motivo_viaje" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Fecha Salida:</label>
                                    <input type="date" class="form-control" name="fecha_salida" id="fecha_salida" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Fecha Retorno:</label>
                                    <input type="date" class="form-control" name="fecha_retorno" id="fecha_retorno" required>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Cant.Días:</label>
                                    <input type="text" class="form-control" name="cantidad_dias" id="cantidad_dias" readonly>
                                </div>
                            </div>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    let fechaSalida = document.getElementById("fecha_salida");
                                    let fechaRetorno = document.getElementById("fecha_retorno");
                                    let cantidadDias = document.getElementById("cantidad_dias");
                            
                                    function calcularDias() {
                                        let inicio = new Date(fechaSalida.value);
                                        let fin = new Date(fechaRetorno.value);
                            
                                        if (!isNaN(inicio) && !isNaN(fin) && fin >= inicio) {
                                            let diferencia = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)); 
                                            cantidadDias.value = diferencia + 1; 
                                        } else {
                                            cantidadDias.value = ""; 
                                        }
                                    }
                            
                                    fechaSalida.addEventListener("change", calcularDias);
                                    fechaRetorno.addEventListener("change", calcularDias);
                                });
                            </script>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Transporte:</label>
                                    <select class="form-control" name="medio_transporte" required>
                                        <option value=""></option>
                                        <option value="AEREO">AEREO</option>
                                        <option value="TERRESTRE">TERRESTRE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Hospedaje:</label>
                                    <select class="form-control" name="hospedaje_requerido" required>
                                        <option value=""></option>
                                        <option value="SI">SI</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Monto Solicitado:</label>
                                    <input type="number" class="form-control" name="monto_solicitado" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="form-group">
                                    <label>Observaciones Adicionales:</label>
                                    <input type="text" class="form-control" id="observaciones" name="motivoviaje">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-verdegrande">GUARDAR SOLICITUD</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ITINERARIO Y CRONOGRAMA DE TRABAJO --}}
<div class="modal fade" id="itinerarioModal" tabindex="-1" aria-labelledby="itinerarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="itinerarioModalLabel" style="font-weight: 900;">ITINERARIO / CRONOGRAMA DE TRABAJO</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>                
            </div>
            <div class="modal-body">
                <div class="card p-2" style="background-color: #fafafa">
                    <div class="card-body p-2">
                        <div class="modal-body p-2">
                            <h5 style="margin-bottom: 10px;"><strong>SOLICITUD DE VIAJE</strong></h5>
                            <div class="row">
                                <div class="col-md-4"><strong>Personal:</strong> <span id="modal-proveedornombre"></span></div>
                                <div class="col-md-4"><strong>Destino:</strong> <span id="modal-destino"></span></div>
                                <div class="col-md-4"><strong>Motivo:</strong> <span id="modal-motivo"></span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><strong>Rango Fechas:</strong> <span id="modal-rango"></span></div>
                                <div class="col-md-4"><strong>Transporte:</strong> <span id="modal-transporte"></span></div>
                                <div class="col-md-4"><strong>Monto Solicitado:</strong> <span id="modal-monto"></span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><strong>Cantidad Días:</strong> <span id="modal-dias"></span></div>
                                <div class="col-md-4"><strong>Hospedaje:</strong> <span id="modal-hospedaje"></span></div>
                                <div class="col-md-4"><strong>Observaciones:</strong> <span id="modal-observaciones"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('admin.proveedoresservicios.guardarviajespersonaldetallado', $id ) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="viajeid" id="input-viajeid">
                    <div class="row">
                        {{-- ITINERARIO --}}
                        <div class="col-lg-5">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-center" style="margin-bottom: 10px;"><strong>ITINERARIO</strong></h5>
                                    <h6 class="text-center" style="background-color: #efefef; font-weight: 800;">TRANSPORTE</h6>
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <label>Transporte:</label>
                                            <select class="form-control" name="transporte">
                                                <option value=""></option>
                                                <option value="AVION">AVION</option>
                                                <option value="BUS">BUS</option>
                                                <option value="TREN">TREN</option>
                                                <option value="VEHICULO EMPRESA">VEHICULO EMPRESA</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Número de Vuelo/Boleto:</label>
                                            <input type="text" class="form-control" name="numero_vuelo">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Fecha y Hora de Salida:</label>
                                            <input type="datetime-local" class="form-control" name="fecha_salida">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Fecha y Hora de Llegada:</label>
                                            <input type="datetime-local" class="form-control" name="fecha_llegada">
                                        </div>
                                    </div>
                                    <h6 class="text-center" style="background-color: #efefef; font-weight: 800;">HOSPEDAJE</h6>
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <label>Nombre del Hotel:</label>
                                            <input type="text" class="form-control" name="hotel">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Dirección del Hotel:</label>
                                            <input type="text" class="form-control" name="direccion_hotel">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Fecha de Ingreso:</label>
                                            <input type="date" class="form-control" name="ingresohotel">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Fecha de Salida:</label>
                                            <input type="date" class="form-control" name="salidahotel">
                                        </div>
                                    </div>
                                    <h6 class="text-center" style="background-color: #efefef; font-weight: 800;">DOCUMENTOS ADJUNTOS</h6>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label>Boleto de Transporte:</label>
                                            <input type="file" class="form-control" name="boleto_transporte" id="boleto_transporte">
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <label>Reserva de Hotel:</label>
                                            <input type="file" class="form-control" name="reserva_hotel" id="reserva_hotel">
                                        </div>
                                    </div>
                                    <h6 class="text-center" style="background-color: #efefef; font-weight: 800;">GASTOS ESTIMADOS</h6>
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <label>Transporte:</label>
                                            <input type="number" class="form-control" name="gasto_transporte" id="gasto_transporte" oninput="calcularTotal()">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Alimentación:</label>
                                            <input type="number" class="form-control" name="gasto_alimentacion" id="gasto_alimentacion" oninput="calcularTotal()">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Otros Gastos:</label>
                                            <input type="number" class="form-control" name="otros_gastos" id="otros_gastos" oninput="calcularTotal()">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Monto Total:</label>
                                            <input type="number" class="form-control" name="monto_total" id="monto_total" readonly>
                                        </div>
                                    </div>
                                    <script>
                                        function calcularTotal() {
                                            let transporte = parseFloat(document.getElementById('gasto_transporte').value) || 0;
                                            let alimentacion = parseFloat(document.getElementById('gasto_alimentacion').value) || 0;
                                            let otros = parseFloat(document.getElementById('otros_gastos').value) || 0;
                                            let total = transporte + alimentacion + otros;
                                            document.getElementById('monto_total').value = total.toFixed(2);
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>

                        {{-- CRONOGRAMA DE TRABAJO --}}
                        <div class="col-lg-7">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-center" style="margin-bottom: 10px;"><strong>CRONOGRAMA DE TRABAJO</strong></h5>
                                    <h6 class="text-center" style="background-color: #efefef; font-weight: 800;">ACTIVIDADES</h6>
                                    <div id="agenda-container">
                                        <div class="row agenda-item">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label>Fecha y Hora:</label>
                                                    <input type="datetime-local" class="form-control" name="fecha_actividad[]">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Ubicación:</label>
                                                    <input type="text" class="form-control" name="ubicacion_actividad[]">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Descripción:</label>
                                                    <input type="text" class="form-control" name="descripcion_actividad[]">
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-activity" style="margin-top: 30px;"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="addActivity" style="margin-bottom: 20px; margin-top: -10px;"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-outline-success">GUARDAR</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal" aria-label="Cerrar">CERRAR</button>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const agendaContainer = document.getElementById("agenda-container");
                            const addActivityBtn = document.getElementById("addActivity");
                            addActivityBtn.addEventListener("click", function() {
                                const newActivity = document.createElement("div");
                                newActivity.classList.add("row", "agenda-item");
                                newActivity.innerHTML = `
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label>Fecha y Hora:</label>
                                            <input type="datetime-local" class="form-control" name="fecha_actividad[]">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Ubicación:</label>
                                            <input type="text" class="form-control" name="ubicacion_actividad[]">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Descripción:</label>
                                            <input type="text" class="form-control" name="descripcion_actividad[]">
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <button type="button" class="btn btn-sm btn-danger remove-activity" style="margin-top: 30px;"><i class="fas fa-trash"></i></button>
                                    </div>
                                `;
                                agendaContainer.appendChild(newActivity);
                            });
                            agendaContainer.addEventListener("click", function(e) {
                                if (e.target.classList.contains("remove-activity")) {
                                    e.target.closest(".agenda-item").remove();
                                }
                            });
                        });
                    </script>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.btn-naranjapequeno').on('click', function() {
            const viajeId = $(this).data('id');
            $('#modal-id').text(viajeId);
            $('#input-viajeid').val(viajeId); 

            $('#modal-id').text($(this).data('id'));
            $('#modal-destino').text($(this).data('destino'));
            $('#modal-motivo').text($(this).data('motivo'));
            $('#modal-rango').text($(this).data('rango'));
            $('#modal-dias').text($(this).data('dias'));
            $('#modal-transporte').text($(this).data('transporte'));
            $('#modal-hospedaje').text($(this).data('hospedaje'));
            $('#modal-monto').text($(this).data('monto'));
            $('#modal-observaciones').text($(this).data('observaciones'));
            $('#modal-proveedornombre').text($(this).data('proveedornombre'));
        });
    });
</script>

{{-- PROGRAMACION DE SOLICITUD DE VIAJE --}}
<div class="modal fade" id="programacionModal" tabindex="-1" aria-labelledby="programacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="programacionModalLabel" style="font-weight: 900;">ITINERARIO / CRONOGRAMA DE TRABAJO</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>                
            </div>
            <div class="modal-body">
                <div class="card p-2" style="background-color: #fafafa">
                    <div class="card-body p-2">
                        <div class="modal-body p-2">
                            <h5 style="margin-bottom: 10px;"><strong>SOLICITUD DE VIAJE</strong></h5>
                            <div class="row">
                                <div class="col-md-4"><strong>Personal:</strong> <span id="modal-proveedornombre2"></span></div>
                                <div class="col-md-4"><strong>Destino:</strong> <span id="modal-destino2"></span></div>
                                <div class="col-md-4"><strong>Motivo:</strong> <span id="modal-motivo2"></span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><strong>Rango Fechas:</strong> <span id="modal-rango2"></span></div>
                                <div class="col-md-4"><strong>Transporte:</strong> <span id="modal-transporte2"></span></div>
                                <div class="col-md-4"><strong>Monto Solicitado:</strong> <span id="modal-monto2"></span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><strong>Cantidad Días:</strong> <span id="modal-dias2"></span></div>
                                <div class="col-md-4"><strong>Hospedaje:</strong> <span id="modal-hospedaje2"></span></div>
                                <div class="col-md-4"><strong>Observaciones:</strong> <span id="modal-observaciones2"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($personalviaje->itinerario)
                <div class="card p-2" style="background-color: #fafafa">
                    <div class="card-body p-2">
                        <div class="modal-body p-2">
                            <h5 style="margin-bottom: 10px;"><strong>ITINERARIO DE VIAJE</strong></h5>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div><i class="fas fa-plane"></i> <strong> TRANSPORTE</strong></div>
                                    <div><strong>Medio Transp.:</strong> {{ $personalviaje->itinerario->transporte }}</div>
                                    <div><strong>Número Boleto:</strong> {{ $personalviaje->itinerario->numerovuelo }}</div>
                                    <div><strong>Fecha/Hora_Salida:</strong> {{ \Carbon\Carbon::parse($personalviaje->itinerario->fechahorasalida)->format('d/m/Y H:i') }}</div>
                                    <div><strong>Fecha/Hora_Llegada:</strong> {{ \Carbon\Carbon::parse($personalviaje->itinerario->fechahorallegada)->format('d/m/Y H:i') }}</div>
                                    <div><strong>Boleto Transp.:</strong>
                                        @if ($personalviaje->itinerario->boletotransporte)
                                            <a href="{{ asset('personal/viajes/' . $personalviaje->id . '/' . $personalviaje->itinerario->boletotransporte) }}" target="_blank" class="btn btn-naranjapequeno2 btn-sm" title="VER BOLETO DE TRANSPORTE">
                                                 <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            No disponible
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div><i class="fas fa-hotel"></i> <strong> HOSPEDAJE</strong></div>
                                    <div><strong>Nombre Hotel:</strong> {{ $personalviaje->itinerario->nombrehotel }}</div>
                                    <div><strong>Dirección Hotel:</strong> {{ $personalviaje->itinerario->direccionhotel }}</div>
                                    <div><strong>Ingreso Hotel:</strong> {{ \Carbon\Carbon::parse($personalviaje->itinerario->ingresohotel)->format('d/m/Y') }}</div>
                                    <div><strong>Salida Hotel:</strong> {{ \Carbon\Carbon::parse($personalviaje->itinerario->salidahotel)->format('d/m/Y') }}</div>
                                    <div><strong>Reserva Hotel:</strong>
                                        @if ($personalviaje->itinerario->reservahotel)
                                            <a href="{{ asset('personal/viajes/' . $personalviaje->id . '/' . $personalviaje->itinerario->reservahotel) }}" target="_blank" class="btn btn-naranjapequeno2 btn-sm" title="VER RESERVA DE HOTEL">
                                                 <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            No disponible
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div><i class="fas fa-wallet"></i><strong> GASTOS</strong></div>
                                    <div><strong>Monto Transporte:</strong> {{$personalviaje->itinerario->montotransporte}} Bs.</div>
                                    <div><strong>Monto Alimentación:</strong> {{$personalviaje->itinerario->montoalimentacion}} Bs.</div>
                                    <div><strong>Monto Otros Gastos:</strong> {{$personalviaje->itinerario->montootrosgastos}} Bs.</div>
                                    <div><strong>Monto Total:</strong> {{$personalviaje->itinerario->montototal}} Bs.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if ($personalviaje->cronograma->count() > 0)
                <div class="card p-2" style="background-color: #fafafa">
                    <div class="card-body p-2">
                        <div class="modal-body p-2">
                            <h5 class="mb-2"><strong>CRONOGRAMA DE TRABAJO</strong></h5>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr style="background-color: #ededed">
                                        <th style="color: black; font-weight: 700;">Nro.Act.</th>
                                        <th style="color: black; font-weight: 700;">Fecha y Hora</th>
                                        <th style="color: black; font-weight: 700;">Ubicación</th>
                                        <th style="color: black; font-weight: 700;">Descripción</th>
                                        <th style="color: black; font-weight: 700;">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($personalviaje->cronograma as $actividad)
                                        <tr style="background-color: white">
                                            <td>{{ $actividad->nroactividad }}</td>
                                            <td>{{ \Carbon\Carbon::parse($actividad->fechahoraactividad)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $actividad->ubicacionactividad }}</td>
                                            <td>{{ $actividad->descripcionactividad }}</td>
                                            <td><span class="badge bg-warning">{{ $actividad->estado }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                <div class="modal-footer" style="margin-right: -15px;">
                    <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal" aria-label="Cerrar">CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.btn-naranjapequeno2').on('click', function() {
            $('#modal-id2').text($(this).data('id'));
            $('#modal-destino2').text($(this).data('destino'));
            $('#modal-motivo2').text($(this).data('motivo'));
            $('#modal-rango2').text($(this).data('rango'));
            $('#modal-dias2').text($(this).data('dias'));
            $('#modal-transporte2').text($(this).data('transporte'));
            $('#modal-hospedaje2').text($(this).data('hospedaje'));
            $('#modal-monto2').text($(this).data('monto'));
            $('#modal-observaciones2').text($(this).data('observaciones'));
            $('#modal-proveedornombre2').text($(this).data('proveedornombre'));
        });
    });
</script>

{{-- RENDICION DE VIAJE --}}
<div class="modal fade" id="rendicionModal" tabindex="-1" aria-labelledby="rendicionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="rendicionModalLabel" style="font-weight: 900;">RENDICIÓN DE VIAJE</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>                
            </div>
            <form action="{{ route('admin.proveedoresservicios.guardarrendicionviajespersonal', $id ) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="viajeid" id="input-viajeid3">
                <div class="modal-body">
                    <div class="card p-2" style="background-color: #fafafa">
                        <div class="card-body p-2">
                            <div class="modal-body p-2">
                                <h5 style="margin-bottom: 10px;"><strong>SOLICITUD DE VIAJE</strong></h5>
                                <div class="row">
                                    <div class="col-md-4"><strong>Personal:</strong> <span id="modal-proveedornombre3"></span></div>
                                    <div class="col-md-4"><strong>Destino:</strong> <span id="modal-destino3"></span></div>
                                    <div class="col-md-4"><strong>Motivo:</strong> <span id="modal-motivo3"></span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><strong>Rango Fechas:</strong> <span id="modal-rango3"></span></div>
                                    <div class="col-md-4"><strong>Transporte:</strong> <span id="modal-transporte3"></span></div>
                                    <div class="col-md-4"><strong>Monto Solicitado:</strong> <span id="modal-monto3"></span></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"><strong>Cantidad Días:</strong> <span id="modal-dias3"></span></div>
                                    <div class="col-md-4"><strong>Hospedaje:</strong> <span id="modal-hospedaje3"></span></div>
                                    <div class="col-md-4"><strong>Observaciones:</strong> <span id="modal-observaciones3"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card p-2" style="background-color: #fafafa">
                        <div class="card-body p-2">
                            <div class="modal-body p-2">
                                <h5 class="mb-2"><strong>RENDICIÓN DE GASTOS</strong></h5>
                                <form id="form-rendicion">
                                    <div class="card p-3" style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 10px;">
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Columna Gasto Estimado -->
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body" style="background-color: #fafafa;">
                                                            <h6 class="mb-3 text-center"><strong>ESTIMADO</strong></h6>
                                                            <div class="mb-3">
                                                                <label><strong>Transporte:</strong></label>
                                                                <div>{{ $personalviaje->itinerario->montotransporte }} Bs.</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label><strong>Alimentación:</strong></label>
                                                                <div>{{ $personalviaje->itinerario->montoalimentacion }} Bs.</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label><strong>Otros Gastos:</strong></label>
                                                                <div>{{ $personalviaje->itinerario->montootrosgastos }} Bs.</div>
                                                            </div>
                                                            <hr>
                                                            <div class="mb-2">
                                                                <label><strong>Total Estimado:</strong></label>
                                                                <div>{{ number_format($personalviaje->itinerario->montototal, 2) }} Bs.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                
                                                <!-- Columna Real Gastado-->
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body" style="background-color: #fafafa;">
                                                            <h6 class="mb-3 text-center"><strong>REAL GASTADO</strong></h6>
                                                            <div class="mb-3">
                                                                <label for="gasto_transporte" style="margin-bottom: -2px;"><strong>Transporte:</strong></label>
                                                                <input type="number" step="0.01" class="form-control gasto-input shadow-sm form-control-sm" id="gasto_transporte" name="gasto_transporte_real" placeholder="Monto real gastado">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="gasto_alimentacion" style="margin-bottom: -2px;"><strong>Alimentación:</strong></label>
                                                                <input type="number" step="0.01" class="form-control gasto-input shadow-sm form-control-sm" id="gasto_alimentacion" name="gasto_alimentacion_real" placeholder="Monto real gastado">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="otros_gastos" style="margin-bottom: -2px;"><strong>Otros Gastos:</strong></label>
                                                                <input type="number" step="0.01" class="form-control gasto-input shadow-sm form-control-sm" id="otros_gastos" name="otros_gastos_real" placeholder="Monto real gastado">
                                                            </div>
                                                            <hr>
                                                            <div class="mb-2">
                                                                <label><strong>Total Real:</strong></label>
                                                                <div id="total-real" name="total-real" class="fw-bold">0.00 Bs.</div>
                                                                <input type="hidden" id="total-real-input" name="total-real" value="0.00">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- RESULTADO --}}
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-body" style="background-color: #fafafa;">
                                                            <h6 class="mb-3 text-center"><strong>RESULTADO</strong></h6>
                                                            <div class="mb-2">
                                                                <label><strong>Diferencia:</strong></label>
                                                                <div id="diferencia-monto" name="diferencia_monto_real" class="fw-bold">0.00 Bs.</div>
                                                                <input type="hidden" id="total-diferencia-monto" name="diferencia_monto_real" value="0.00">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label><strong>Estado:</strong></label>
                                                                <div id="resultado-comparacion" class="fw-bold">-</div>
                                                            </div>

                                                            <h6 class="mb-3 text-center" style="margin-top: 10px;"><strong>COMPROBANTE</strong></h6>
                                                            <div class="mb-2">
                                                                <input type="file" class="form-control" name="comprobante" id="comprobante">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const inputs = document.querySelectorAll('.gasto-input');
                            const totalRealSpan = document.getElementById('total-real');
                            const resultado = document.getElementById('resultado-comparacion');
                            const diferenciaSpan = document.getElementById('diferencia-monto');
                            const totalEstimado = parseFloat({{ $personalviaje->itinerario->montototal ?? 0 }});
                        
                            inputs.forEach(input => {
                                input.addEventListener('input', calcularDiferencia);
                            });
                        
                            function calcularDiferencia() {
                                let totalReal = 0;
                                inputs.forEach(input => {
                                    totalReal += parseFloat(input.value) || 0;
                                });

                                const diferenciaNum = totalReal - totalEstimado;
                                const diferenciaFormateada = diferenciaNum.toFixed(2);
                                const diferenciaConSigno = diferenciaNum > 0 ? '+' + diferenciaFormateada : diferenciaNum < 0 ? '-' + Math.abs(diferenciaFormateada) : '0.00';

                                totalRealSpan.textContent = totalReal.toFixed(2) + ' Bs.';
                                document.getElementById('total-real-input').value = totalReal.toFixed(2);
                                document.getElementById('total-diferencia-monto').value = diferenciaConSigno;

                                resultado.classList.remove('text-success', 'text-danger', 'text-primary');
                                diferenciaSpan.classList.remove('text-success', 'text-danger', 'text-primary');

                                if (diferenciaNum === 0) {
                                    resultado.textContent = 'GASTÓ EXACTO';
                                    resultado.classList.add('text-success');
                                    diferenciaSpan.classList.add('text-success');
                                    diferenciaSpan.textContent = '0.00 Bs.';
                                } else if (diferenciaNum > 0) {
                                    resultado.textContent = 'GASTÓ MÁS (SE GENERARÁ UNA CUENTA POR PAGAR)';
                                    resultado.classList.add('text-danger');
                                    diferenciaSpan.textContent = '+ ' + diferenciaFormateada + ' Bs.';
                                    diferenciaSpan.classList.add('text-danger');
                                } else {
                                    resultado.textContent = 'GASTÓ MENOS (SE GENERARÁ UNA CUENTA POR COBRAR)';
                                    resultado.classList.add('text-primary');
                                    diferenciaSpan.textContent = '- ' + Math.abs(diferenciaFormateada) + ' Bs.';
                                    diferenciaSpan.classList.add('text-primary');
                                }
                            }

                        });
                    </script>
                                
                    @if ($personalviaje->cronograma->count() > 0)
                    <div class="card p-2" style="background-color: #fafafa">
                        <div class="card-body p-2">
                            <div class="modal-body p-2">
                                <div class="table-responsive">
                                    <h5 class="mb-2"><strong>RENDICIÓN DE CRONOGRAMA</strong></h5>
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr style="background-color: #ededed">
                                                <th style="color: black; font-weight: 700;">Nro.Act.</th>
                                                <th style="color: black; font-weight: 700;">Fecha y Hora</th>
                                                <th style="color: black; font-weight: 700;">Ubicación</th>
                                                <th style="color: black; font-weight: 700;">Descripción</th>
                                                <th style="color: black; font-weight: 700;">Estado</th>
                                                <th style="color: black; font-weight: 700;">
                                                    Selec. <input type="checkbox" id="check-all">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($personalviaje->cronograma as $actividad)
                                                <tr style="background-color: white">
                                                    <td>{{ $actividad->nroactividad }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($actividad->fechahoraactividad)->format('d/m/Y H:i') }}</td>
                                                    <td>{{ $actividad->ubicacionactividad }}</td>
                                                    <td>{{ $actividad->descripcionactividad }}</td>
                                                    <td><span class="badge bg-warning">{{ $actividad->estado }}</span></td>
                                                    <td>
                                                        <input type="checkbox" class="check-item" name="actividades_seleccionadas[]" value="{{ $actividad->id }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <script>
                                        document.getElementById('check-all').addEventListener('change', function () {
                                            const checkboxes = document.querySelectorAll('.check-item');
                                            checkboxes.forEach(cb => cb.checked = this.checked);
                                        });
                                    </script>              
                                </div> 
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="modal-footer" style="margin-right: -15px;">
                        <button type="submit" class="btn btn-verdegrande">GUARDAR SOLICITUD</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal" aria-label="Cerrar">CERRAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.btn-naranjapequeno3').on('click', function() {
            const viajeId = $(this).data('id');
            $('#modal-id3').text(viajeId);
            $('#input-viajeid3').val(viajeId); 

            $('#modal-id3').text($(this).data('id'));
            $('#modal-destino3').text($(this).data('destino'));
            $('#modal-motivo3').text($(this).data('motivo'));
            $('#modal-rango3').text($(this).data('rango'));
            $('#modal-dias3').text($(this).data('dias'));
            $('#modal-transporte3').text($(this).data('transporte'));
            $('#modal-hospedaje3').text($(this).data('hospedaje'));
            $('#modal-monto3').text($(this).data('monto'));
            $('#modal-observaciones3').text($(this).data('observaciones'));
            $('#modal-proveedornombre3').text($(this).data('proveedornombre'));
        });
    });
</script>

{{-- PROGRAMACION DE SOLICITUD DE VIAJE --}}
<div class="modal fade" id="historialModal" tabindex="-1" aria-labelledby="historialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="historialModalLabel" style="font-weight: 900;">HISTORIAL DE VIAJE</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>                
            </div>
            <div class="modal-body">
                <div class="card p-2" style="background-color: #fafafa">
                    <div class="card-body p-2">
                        <div class="modal-body p-2">
                            <h5 style="margin-bottom: 10px;"><strong>SOLICITUD DE VIAJE</strong></h5>
                            <div class="row">
                                <div class="col-md-4"><strong>Personal:</strong> <span id="modal-proveedornombre4"></span></div>
                                <div class="col-md-4"><strong>Destino:</strong> <span id="modal-destino4"></span></div>
                                <div class="col-md-4"><strong>Motivo:</strong> <span id="modal-motivo4"></span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><strong>Rango Fechas:</strong> <span id="modal-rango4"></span></div>
                                <div class="col-md-4"><strong>Transporte:</strong> <span id="modal-transporte4"></span></div>
                                <div class="col-md-4"><strong>Monto Solicitado:</strong> <span id="modal-monto4"></span></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4"><strong>Cantidad Días:</strong> <span id="modal-dias4"></span></div>
                                <div class="col-md-4"><strong>Hospedaje:</strong> <span id="modal-hospedaje4"></span></div>
                                <div class="col-md-4"><strong>Observaciones:</strong> <span id="modal-observaciones4"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($personalviaje->itinerario)
                        <div class="modal-body p-1">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="card" style="background-color: #fafafa">
                                        <div class="card-body">
                                            <h5 style="margin-bottom: 10px;"><strong>ITINERARIO DE VIAJE</strong></h5>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div><i class="fas fa-plane"></i> <strong> TRANSPORTE</strong></div>
                                                    <div><strong>Medio Transp.:</strong> {{ $personalviaje->itinerario->transporte }}</div>
                                                    <div><strong>Número Boleto:</strong> {{ $personalviaje->itinerario->numerovuelo }}</div>
                                                    <div><strong>Fecha/Hora_Salida:</strong> {{ \Carbon\Carbon::parse($personalviaje->itinerario->fechahorasalida)->format('d/m/Y H:i') }}</div>
                                                    <div><strong>Fecha/Hora_Llegada:</strong> {{ \Carbon\Carbon::parse($personalviaje->itinerario->fechahorallegada)->format('d/m/Y H:i') }}</div>
                                                    <div><strong>Boleto Transp.:</strong>
                                                        @if ($personalviaje->itinerario->boletotransporte)
                                                            <a href="{{ asset('personal/viajes/' . $personalviaje->id . '/' . $personalviaje->itinerario->boletotransporte) }}" target="_blank" class="btn btn-naranjapequeno4 btn-sm" title="VER BOLETO DE TRANSPORTE">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            No disponible
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div><i class="fas fa-hotel"></i> <strong> HOSPEDAJE</strong></div>
                                                    <div><strong>Nombre Hotel:</strong> {{ $personalviaje->itinerario->nombrehotel }}</div>
                                                    <div><strong>Dirección Hotel:</strong> {{ $personalviaje->itinerario->direccionhotel }}</div>
                                                    <div><strong>Ingreso Hotel:</strong> {{ \Carbon\Carbon::parse($personalviaje->itinerario->ingresohotel)->format('d/m/Y') }}</div>
                                                    <div><strong>Salida Hotel:</strong> {{ \Carbon\Carbon::parse($personalviaje->itinerario->salidahotel)->format('d/m/Y') }}</div>
                                                    <div><strong>Reserva Hotel:</strong>
                                                        @if ($personalviaje->itinerario->reservahotel)
                                                            <a href="{{ asset('personal/viajes/' . $personalviaje->id . '/' . $personalviaje->itinerario->reservahotel) }}" target="_blank" class="btn btn-naranjapequeno4 btn-sm" title="VER RESERVA DE HOTEL">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            No disponible
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="card" style="background-color: #fafafa">
                                        <div class="card-body">
                                            <h5 style="margin-bottom: 10px;"><strong>RENDICIÓN DE GASTOS</strong></h5>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div><i class="fas fa-wallet"></i><strong> GASTOS ESTIMADOS</strong></div>
                                                    <div><strong>Transporte:</strong> {{$personalviaje->itinerario->montotransporte}} Bs.</div>
                                                    <div><strong>Alimentación:</strong> {{$personalviaje->itinerario->montoalimentacion}} Bs.</div>
                                                    <div><strong>Otros Gastos:</strong> {{$personalviaje->itinerario->montootrosgastos}} Bs.</div>
                                                    <div><strong>Total:</strong> {{$personalviaje->itinerario->montototal}} Bs.</div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div><i class="fas fa-wallet"></i><strong> GASTOS REALES</strong></div>
                                                    <div><strong>Transporte:</strong> {{$personalviaje->itinerario->rendicionmontotransporte}} Bs.</div>
                                                    <div><strong>Alimentación:</strong> {{$personalviaje->itinerario->rendicionmontoalimentacion}} Bs.</div>
                                                    <div><strong>Otros Gastos:</strong> {{$personalviaje->itinerario->rendicionmontootrosgastos}} Bs.</div>
                                                    <div><strong>Total:</strong> {{$personalviaje->itinerario->rendicionmontototal}} Bs.</div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div><strong>Estado:</strong> {{$personalviaje->itinerario->rendicionresultado}}</div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div><strong>Comprobante:</strong>
                                                        @if ($personalviaje->itinerario->rendicioncomprobante)
                                                            <a href="{{ asset('personal/viajes/' . $personalviaje->id . '/' . $personalviaje->itinerario->rendicioncomprobante) }}" target="_blank" class="btn btn-naranjapequeno4 btn-sm" title="VER RESERVA DE HOTEL">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            No disponible
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endif
                @if ($personalviaje->cronograma->count() > 0)
                <div class="card p-2" style="background-color: #fafafa">
                    <div class="card-body p-2">
                        <div class="modal-body p-2">
                            <div class="table-responsive">
                                <h5 class="mb-2"><strong>CRONOGRAMA DE TRABAJO</strong></h5>
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr style="background-color: #ededed">
                                            <th style="color: black; font-weight: 700;">Nro.Act.</th>
                                            <th style="color: black; font-weight: 700;">Fecha y Hora</th>
                                            <th style="color: black; font-weight: 700;">Ubicación</th>
                                            <th style="color: black; font-weight: 700;">Descripción</th>
                                            <th style="color: black; font-weight: 700;">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($personalviaje->cronograma as $actividad)
                                            <tr style="background-color: white">
                                                <td>{{ $actividad->nroactividad }}</td>
                                                <td>{{ \Carbon\Carbon::parse($actividad->fechahoraactividad)->format('d/m/Y H:i') }}</td>
                                                <td>{{ $actividad->ubicacionactividad }}</td>
                                                <td>{{ $actividad->descripcionactividad }}</td>
                                                <td>
                                                    @if($actividad->estado == 'FINALIZADO')
                                                        <span class="badge bg-success">{{ $actividad->estado }}</span>
                                                    @elseif($actividad->estado == 'INCUMPLIDO')
                                                        <span class="badge bg-danger">{{ $actividad->estado }}</span>
                                                    @else
                                                        {{ $actividad->estado }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="modal-footer" style="margin-right: -15px;">
                    <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal" aria-label="Cerrar">CERRAR</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.btn-naranjapequeno4').on('click', function() {
            $('#modal-id4').text($(this).data('id'));
            $('#modal-destino4').text($(this).data('destino'));
            $('#modal-motivo4').text($(this).data('motivo'));
            $('#modal-rango4').text($(this).data('rango'));
            $('#modal-dias4').text($(this).data('dias'));
            $('#modal-transporte4').text($(this).data('transporte'));
            $('#modal-hospedaje4').text($(this).data('hospedaje'));
            $('#modal-monto4').text($(this).data('monto'));
            $('#modal-observaciones4').text($(this).data('observaciones'));
            $('#modal-proveedornombre4').text($(this).data('proveedornombre'));
        });
    });
</script>
@stop