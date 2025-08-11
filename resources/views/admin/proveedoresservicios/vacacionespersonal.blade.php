@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-azulgrande" href="{{ route('admin.proveedoresservicios.verpersonal', $id ) }}">REGRESAR</a>
<a class="btn float-right btn-verdegrande btn-sm" data-toggle="modal" data-target="#nuevasolicitudModal">NUEVA SOLICITUD</a>
<h1>VACACIONES DE {{ $proveedoresservicios->razonsocial }}</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/serviciosproveedores.css') }}">
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
        }, 3000);
    </script>
@endif
@if (session('error'))
    <div id="alert-info" class="alert alert-danger">
        <strong>{{ session('error') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 3000);
    </script>
@endif
<div class="card col-lg-12">
    <div class="card-body">
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
                        HISTORIAL DE VACACIONES
                    </a>
                </li>       
            </ul>
        </div>

        <div class="tab-content" id="myTabContent">
            {{-- SOLICITUDES DE VACACIONES --}}
            <div class="tab-pane fade show active" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Rango_Fechas</th>
                                <th>Cant.Dias</th>
                                <th>Observaciones</th>
                                <th>Boleta</th>
                                <th>Aprobar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($personalvacaciones as $personalvacacion)
                                @if ($personalvacacion->estado === 'PENDIENTE')
                                    <tr>
                                        <td>{{$personalvacacion->id}}</td>
                                        <td>{{$personalvacacion->fechainicial}} - {{$personalvacacion->fechafinal}}</td>
                                        <td>{{$personalvacacion->cantidaddias}} DIAS</td>
                                        <td>{{$personalvacacion->observacion}}</td>
                                        <td>
                                            @if ($personalvacacion->boleta)
                                                <a href="{{ asset('vacaciones/' . $personalvacacion->proveedorid . '/' . $personalvacacion->boleta) }}" target="_blank" class="btn btn-sm btn-verdepequeno" title="VER BOLETA">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">No disponible</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- @can('admin.proveedoresservicios.aprobarvacaciones')
                                                <a onclick="aprobarSolicitud({{ $personalvacacion->id }})" class="btn btn-sm btn-verdepequeno" title="APROBAR SOLICITUD">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a onclick="rechazarSolicitud({{ $personalvacacion->id }})" class="btn btn-sm btn-rojopequeno2" title="RECHAZAR SOLICITUD">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            @endcan --}}
                                            @can('admin.proveedoresservicios.aprobarvacaciones')
                                                <a onclick="aprobarSolicitud({{ $personalvacacion->id }})" class="btn btn-sm btn-verdepequeno" title="APROBAR SOLICITUD">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a onclick="rechazarSolicitud({{ $personalvacacion->id }})" class="btn btn-sm btn-rojopequeno2" title="RECHAZAR SOLICITUD">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-secondary" title="APROBAR SOLICITUD" disabled>
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-secondary" title="RECHAZAR SOLICITUD" disabled>
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endcan

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
                                    cancelButtonText: 'CANCELAR',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        fetch("{{ url('/vacaciones/aprobarsolicitudvacacion') }}/" + id, {

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
                                    cancelButtonText: 'CANCELAR',
                                    reverseButtons: true,
                                    inputValidator: (value) => {
                                        if (!value) {
                                            return 'Debe escribir un motivo para rechazar';
                                        }
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed && result.value) {
                                        fetch("{{ url('/vacaciones/rechazarsolicitudvacacion') }}/" + id, {
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
                                <th>Rango_Fechas</th>
                                <th>Cant_Días</th>
                                <th>Usuario_Rechazo</th>
                                <th>Motivo_Rechazo</th>
                                <th>Estado</th>
                                <th>Boleta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($personalvacaciones as $personalvacacion)
                                @if ($personalvacacion->estado === 'RECHAZADO')
                                <tr>
                                    <td>{{$personalvacacion->id}}</td>
                                    <td>{{$personalvacacion->fechainicial}} - {{$personalvacacion->fechafinal}}</td>
                                    <td>{{$personalvacacion->cantidaddias}} DIAS</td>
                                    <td>{{$personalvacacion->usuarioautorizacion}}</td>
                                    <td>{{$personalvacacion->motivorechazo}}</td>
                                    <td><span class="badge {{ $personalvacacion->estado == 'RECHAZADO' ? 'bg-danger' : 'bg-warning' }}">{{ $personalvacacion->estado }}</span></td>
                                    <td>
                                        @if ($personalvacacion->boleta)
                                            <a href="{{ asset('vacaciones/' . $personalvacacion->proveedorid . '/' . $personalvacacion->boleta) }}" target="_blank" class="btn btn-sm btn-verdepequeno" title="VER BOLETA">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">No disponible</span>
                                        @endif
                                    </td>
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
                                <th>Rango Fechas</th>
                                <th>Cant. Días</th>
                                <th>Usuario Autorizador</th>
                                <th>Estado</th>
                                <th>Calen.</th>
                                <th>Boleta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $hoy = now()->format('Y-m-d');
                            @endphp
                            @foreach ($personalvacaciones as $personalvacacion)
                                @if ($personalvacacion->estado === 'APROBADO')
                                @php
                                    $fechaInicio = \Carbon\Carbon::parse($personalvacacion->fechainicial)->format('Y-m-d');
                                    $fechaFinal = \Carbon\Carbon::parse($personalvacacion->fechafinal)->format('Y-m-d');
                                    
                                    if ($hoy >= $fechaInicio && $hoy <= $fechaFinal) {
                                        $estado = 'EN PROCESO';
                                    } elseif ($hoy < $fechaInicio) {
                                        $estado = 'PROXIMAMENTE';
                                    } else {
                                        continue;
                                    }
                                @endphp
                                <tr>
                                    <td>{{$personalvacacion->id}}</td>
                                    <td>{{$fechaInicio}} - {{$fechaFinal}}</td>
                                    <td>{{$personalvacacion->cantidaddias}} DIAS</td>
                                    <td>{{$personalvacacion->usuarioautorizacion}}</td>
                                    <td><span class="badge {{ $estado == 'EN PROCESO' ? 'bg-success' : 'bg-warning' }}">{{ $estado }}</span></td>
                                    <td>
                                        <a class="btn btn-sm btn-naranjapequeno" onclick="mostrarCalendario('{{ $fechaInicio }}', '{{ $fechaFinal }}', '{{ $estado }}')" title="VER CALENDARIO">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                    </td>
                                    <td>
                                        @if ($personalvacacion->boleta)
                                            <a href="{{ asset('vacaciones/' . $personalvacacion->proveedorid . '/' . $personalvacacion->boleta) }}" target="_blank" class="btn btn-sm btn-verdepequeno" title="VER BOLETA">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">No disponible</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- HISTORIAL DE VACACIONES --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1"> 
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Rango Fechas</th>
                                <th>Cant. Días</th>
                                <th>Usuario Autorizador</th>
                                <th>Estado</th>
                                <th>Calen.</th>
                                <th>Boleta</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @php
                                $hoy = now()->format('Y-m-d');
                            @endphp
                            @foreach ($personalvacaciones as $personalvacacion)
                                @if ($personalvacacion->estado === 'APROBADO')
                                    @php
                                        $fechaInicio = \Carbon\Carbon::parse($personalvacacion->fechainicial)->format('Y-m-d');
                                        $fechaFinal = \Carbon\Carbon::parse($personalvacacion->fechafinal)->format('Y-m-d');
                        
                                        if ($hoy > $fechaFinal) {
                                            $estado = 'FINALIZADO';
                                            $badgeClass = 'bg-danger';
                                        } else {
                                            continue;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $personalvacacion->id }}</td>
                                        <td>{{ $fechaInicio }} - {{ $fechaFinal }}</td>
                                        <td>{{ $personalvacacion->cantidaddias }} DIAS</td>
                                        <td>{{ $personalvacacion->usuarioautorizacion }}</td>
                                        <td><span class="badge {{ $badgeClass }}">{{ $estado }}</span></td>
                                        <td>
                                            <a class="btn btn-sm btn-naranjapequeno" onclick="mostrarCalendario('{{ $fechaInicio }}', '{{ $fechaFinal }}', '{{ $estado }}')" title="VER CALENDARIO">
                                                <i class="fas fa-calendar-alt"></i>
                                            </a>
                                        </td>
                                        <td>
                                            @if ($personalvacacion->boleta)
                                                <a href="{{ asset('vacaciones/' . $personalvacacion->proveedorid . '/' . $personalvacacion->boleta) }}" target="_blank" class="btn btn-sm btn-verdepequeno" title="VER BOLETA">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">No disponible</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
            </div>
            
            <!-- Modal para el calendario -->
            <div class="modal fade" id="modalCalendario" tabindex="-1" aria-labelledby="modalCalendarioLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalCalendarioLabel" style="font-weight: 900;">CALENDARIO DE VACACIONES</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <div id="calendario"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-rojopequeno" data-dismiss="modal">CERRAR</button>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                .calendario-table {
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 15px;
                }
            
                .calendario-table th {
                    background-color: #ffe9c8;
                    color: rgb(0, 0, 0);
                    padding: 8px;
                    border-radius: 10px;
                    font-size: 13px;
                    text-transform: uppercase;
                }
            
                .calendario-celda {
                    padding: 10px;
                    border-radius: 12px;
                    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
                    font-size: 15px;
                    min-width: 70px;
                    height: 70px;
                    transition: all 0.3s ease-in-out;
                    background-color: #f0f0f0;
                }
            
                .calendario-celda:hover {
                    transform: scale(1.1);
                    cursor: pointer;
                }
            
                .celda-verde {
                    background-color: #94c93b !important;
                    color: white;
                    font-weight: 900;
                }
            
                .celda-hoy {
                    background-color: #faa625 !important;
                    color: white;
                    font-weight: 900;
                }
            </style>
            <script>
                function mostrarCalendario(fechaInicio, fechaFinal, estado) {
                    let hoy = new Date();
                    hoy.setHours(0, 0, 0, 0);
            
                    let fechaInicial = new Date(fechaInicio + 'T00:00:00');
                    let fechaFinalObj = new Date(fechaFinal + 'T00:00:00');
            
                    let diaInicial = fechaInicial.getDay();
                    let ajusteInicio = diaInicial === 0 ? -6 : 1 - diaInicial;
                    let inicioSemana = new Date(fechaInicial);
                    inicioSemana.setDate(inicioSemana.getDate() + ajusteInicio);
            
                    let diaFinal = fechaFinalObj.getDay();
                    let ajusteFinal = diaFinal === 0 ? 0 : 7 - diaFinal;
                    let finSemana = new Date(fechaFinalObj);
                    finSemana.setDate(finSemana.getDate() + ajusteFinal);
            
                    let nombresDias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                    let calendarioHTML = `<table class="calendario-table text-center">`;
            
                    // Cabecera de días
                    calendarioHTML += `<tr>`;
                    for (let i = 0; i < nombresDias.length; i++) {
                        calendarioHTML += `<th>${nombresDias[i]}</th>`;
                    }
                    calendarioHTML += `</tr><tr>`;
            
                    let fechaIterada = new Date(inicioSemana);
            
                    while (fechaIterada <= finSemana) {
                        let fechaComparada = new Date(fechaIterada);
                        fechaComparada.setHours(0, 0, 0, 0);
            
                        let clase = "calendario-celda";
            
                        if (fechaComparada >= fechaInicial && fechaComparada <= fechaFinalObj) {
                            clase += " celda-verde";
                        }
            
                        if (fechaComparada.getTime() === hoy.getTime() && estado !== "PROXIMAMENTE") {
                            clase += " celda-hoy";
                        }
            
                        let fechaTexto = `${fechaIterada.getDate().toString().padStart(2, '0')}/` +
                                         `${(fechaIterada.getMonth() + 1).toString().padStart(2, '0')}/` +
                                         `${fechaIterada.getFullYear()}`;
            
                        calendarioHTML += `<td class="${clase}">
                            ${fechaTexto}
                        </td>`;
            
                        if (fechaIterada.getDay() === 0) {
                            calendarioHTML += `</tr><tr>`;
                        }
            
                        fechaIterada.setDate(fechaIterada.getDate() + 1);
                    }
            
                    calendarioHTML += `</tr></table>`;
                    document.getElementById("calendario").innerHTML = calendarioHTML;
                    new bootstrap.Modal(document.getElementById("modalCalendario")).show();
                }
            </script>
        </div>
    </div>
</div>

<!-- MODAL NUEVA SOLICITUD -->
<div class="modal fade" id="nuevasolicitudModal" tabindex="-1" aria-labelledby="nuevasolicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="nuevasolicitudModalLabel" style="font-weight: 900;">NUEVA SOLICITUD DE VACACIONES</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>   
            </div>
            <div class="alert d-flex align-items-center" role="alert" style="font-size: 15px; background-color:#f7fcef;">
                <div>
                    <strong>FECHA DE INGRESO:</strong> {{ \Carbon\Carbon::parse($fechaIngreso)->format('d/m/Y') }} <br>
                    <strong>DIAS DISPONIBLES:</strong> {{ $diasDisponibles }} DIAS
                </div>
            </div>

            @if ($diasDisponibles <= 0 && !$codigoAprobacion)
                <div class="alert alert-danger">
                    <strong>NO TIENES DIAS DE VACACIONES DISPONIBLES</strong>
                </div>
                <form action="{{ route('verificar.codigo.adelantovacaciones') }}" method="POST" style="max-width: 500px; margin: 0 auto; margin-bottom: 30px;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $proveedoresservicios->id }}">
                    <div class="form-group mb-3">
                        <label for="codigo" class="font-weight-bold" style="font-size: 1rem;">Ingresa el código para adelanto de vacaciones:</label>
                        <input type="text" id="codigo" name="codigo" class="form-control" placeholder="Código de autorización" required style="border-radius: 5px;">
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-success btn-block" style="padding: 5px 10px; font-size: 1rem; border-radius: 5px;">VALIDAR CÓDIGO</button>
                </form>
            @else
                <form action="{{ route('admin.proveedoresservicios.guardarvacacionespersonal', $id ) }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>ID Solicitante:</label>
                                        <input type="text" class="form-control" id="proveedorid" name="proveedorid" value="{{ $proveedoresservicios->id }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="form-group">
                                        <label>Nombre Solicitante:</label>
                                        <input type="text" class="form-control" id="proveedornombre" name="proveedornombre" value="{{ $proveedoresservicios->razonsocial }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Fecha Inicio:</label>
                                        <input type="date" class="form-control" name="fecha_salida" id="fecha_salida" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Fecha Final:</label>
                                        <input type="date" class="form-control" name="fecha_retorno" id="fecha_retorno" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Cantidad Días:</label>
                                        <input type="text" class="form-control" name="cantidad_dias" id="cantidad_dias" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Observaciones:</label>
                                        <input type="text" class="form-control" name="observacion" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer" style="margin-right: -15px;">
                                <button type="submit" class="btn btn-sm btn-verdegrande">GUARDAR SOLICITUD</button>
                                <button type="button" class="btn btn-rojopequeno" data-dismiss="modal">CERRAR</button>
                            </div>
                        </div>
                    </div>
                    {{-- <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const fechaSalida = document.getElementById("fecha_salida");
                            const fechaRetorno = document.getElementById("fecha_retorno");
                            const cantidadDias = document.getElementById("cantidad_dias");
                            const diasDisponibles = {{ $diasDisponibles ?? 0 }};
                            const hoy = new Date();
                            const minFecha = new Date(hoy);
                            minFecha.setDate(minFecha.getDate() + 15);
                            fechaSalida.min = minFecha.toISOString().split('T')[0];

                            function calcularDias() {
                                const inicio = new Date(fechaSalida.value);
                                const fin = new Date(fechaRetorno.value);
                                if (fechaSalida.value) {
                                    const maxFecha = new Date(inicio);
                                    maxFecha.setDate(inicio.getDate() + diasDisponibles + 10);
                                    fechaRetorno.min = fechaSalida.value;
                                    fechaRetorno.max = maxFecha.toISOString().split('T')[0];
                                }

                                if (!isNaN(inicio) && !isNaN(fin) && fin >= inicio) {
                                    let dias = 0;
                                    let temp = new Date(inicio);

                                    while (temp <= fin) {
                                        if (temp.getDay() !== 0) {
                                            dias++;
                                        }
                                        temp.setDate(temp.getDate() + 1);
                                    }

                                    if (dias > diasDisponibles) {
                                        alert("No puedes seleccionar más días de los disponibles ({{ $diasDisponibles }} días).");
                                        fechaRetorno.value = "";
                                        cantidadDias.value = "";
                                    } else {
                                        cantidadDias.value = dias;
                                    }
                                } else {
                                    cantidadDias.value = "";
                                }
                            }
                            fechaSalida.addEventListener("change", calcularDias);
                            fechaRetorno.addEventListener("change", calcularDias);
                        });
                    </script> --}}
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                        const tienePermisoAdelanto = {{ $codigoAprobacion ? 'true' : 'false' }};
                        const fechaSalida = document.getElementById("fecha_salida");
                        const fechaRetorno = document.getElementById("fecha_retorno");
                        const cantidadDias = document.getElementById("cantidad_dias");
                        const diasDisponibles = {{ $diasDisponibles ?? 0 }};
                        const hoy = new Date();
                        const minFecha = new Date(hoy);
                        minFecha.setDate(minFecha.getDate() + 15);
                        fechaSalida.min = minFecha.toISOString().split('T')[0];

                        function crearFechaSinHora(fechaStr) {
                            if (!fechaStr) return null;
                            const [year, month, day] = fechaStr.split('-');
                            return new Date(year, month - 1, day);
                        }

                        function calcularDias() {
                            const inicio = crearFechaSinHora(fechaSalida.value);
                            const fin = crearFechaSinHora(fechaRetorno.value);

                            if (inicio) {
                                const maxFecha = new Date(inicio);
                                let limiteDias = tienePermisoAdelanto ? 15 : diasDisponibles;
                                maxFecha.setDate(inicio.getDate() + limiteDias);

                                fechaRetorno.min = fechaSalida.value;
                                fechaRetorno.max = maxFecha.toISOString().split('T')[0];
                            }

                            if (inicio && fin && fin >= inicio) {
                                let dias = 0;
                                let temp = new Date(inicio);

                                while (temp <= fin) {
                                    if (temp.getDay() !== 0) { // Ignora domingos
                                        dias++;
                                    }
                                    temp.setDate(temp.getDate() + 1);
                                }

                                const limite = tienePermisoAdelanto ? 15 : diasDisponibles;

                                if (dias > limite) {
                                    alert("No puedes seleccionar más de " + limite + " días.");
                                    fechaRetorno.value = "";
                                    cantidadDias.value = "";
                                } else {
                                    cantidadDias.value = dias;
                                }
                            } else {
                                cantidadDias.value = "";
                            }
                        }

                        fechaSalida.addEventListener("change", calcularDias);
                        fechaRetorno.addEventListener("change", calcularDias);
                    });

                    </script>

                </form>
            @endif
        </div>
    </div>
</div>
@endsection
