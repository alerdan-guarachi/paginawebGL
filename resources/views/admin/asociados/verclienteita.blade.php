@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.listadoclienteita', ['asociado' => 6]) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-acciones" data-toggle="modal" data-target="#ventanaModal">ACCIONES DEL CLIENTE</a>
@if ($tieneAuditoriaMedica || $tieneApelacion || $tieneSegundasolicitud || $tieneTercerasolicitud)
    <a class="btn btn-sm float-right btn-auditoriamedica" data-toggle="modal" data-target="#ventanaModalauditoriamedica">SERVICIOS ADICIONALES</a>
@endif

@can('admin.asociados.subirdocumentacionextra')
@if ($tieneAuditoriaMedica)
<a class="btn btn-sm float-right btn-cartas" data-toggle="modal" data-target="#cartasclientes">DOCUMENTACIÓN AUDITORIA</a>
@endif
@endcan

<h5>DATOS DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
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

{{-- VER DATOS DEL CLIENTE --}}
<div class="container col-lg-12">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="border-bottom text-center pb-4">
                        <div style="display: flex; flex-direction: column;">
                            <div class="image-container" style="width: 100%; height: 320px; overflow: auto;">
                                <img src="{{asset('image/'.$cliente->image)}}" alt="Foto de perfil" class="col-md-12 mb-12" id="vista-previa" style="width: 100%; height: auto; object-fit: cover; object-position: center; " />
                            </div>
                            <h5>ID: {{$cliente->id}}</h5>
                        </div>
                    </div>
                    <div class="py-1">
                        <p class="clearfix">
                            <span class="float-left h6" style="font-weight: bold; color:#94c93b">
                                Nombres
                            </span>
                            <span class="float-right text-muted">
                                {{$cliente->nombres}}
                                </a>
                            </span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left" style="font-weight: bold; color:#94c93b">
                                Apellido Paterno
                            </span>
                            <span class="float-right text-muted">
                                {{$cliente->apepaterno}}
                                </a>
                            </span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left" style="font-weight: bold; color:#94c93b">
                                Apellido Materno
                            </span>
                            <span class="float-right text-muted">
                                {{$cliente->apematerno}}
                                </a>
                            </span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left" style="font-weight: bold; color:#94c93b">
                                Sucursal
                            </span>
                            <span class="float-right text-muted">
                                {{$cliente->sucursal}}
                                </a>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="profile-feed">
                        <div class="d-flex align-items-start profile-feed-item">
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Tipo de ident.</th>
                                                    <td>{{$cliente->tipoidentificacion}}</td>
                                                </tr>
                                                <tr>
                                                    <th>CI</th>
                                                    <td>{{$cliente->ci}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Complemento</th>
                                                    <td>{{$cliente->cicomplemento ?? 0}}</td>
                                                </tr>
                                                <tr>
                                                    <th>C/exp.</th>
                                                    <td>{{$cliente->ciexp ?? 0}}</td>
                                                </tr>
                                                <tr> 
                                                    <th>Fecha Ven/CI</th>
                                                    <td>{{ $cliente->fechavencci ? $cliente->fechavencci : 'INDEFINIDO' }}</td>
                                                </tr>      
                                                <style>
                                                    @keyframes heartbeat {
                                                    0%, 100% {
                                                        transform: scale(1);
                                                    }
                                                    50% {
                                                        transform: scale(1.3);
                                                    }
                                                    }
                                                    .icon-pulse {
                                                    animation: heartbeat 1s infinite;
                                                    }
                                                </style>

                                                @php
                                                    use Carbon\Carbon;

                                                    $fechaNacimiento = $cliente->fechanacimiento;
                                                    $edadCalculada = $fechaNacimiento ? Carbon::parse($fechaNacimiento)->age : null;
                                                @endphp

                                                <tr> 
                                                    <th>Fecha Nac.</th>
                                                    <td>{{ $fechaNacimiento ?? 'NINGUNO' }}</td>
                                                </tr>      
                                                <tr>
                                                    <th>Edad</th>
                                                    <td>
                                                        {{ $cliente->edad }}
                                                        @if ($fechaNacimiento && $cliente->edad != $edadCalculada)
                                                            <i class="fas fa-exclamation-triangle text-danger icon-pulse" title="SE DEBE ACTUALIZAR SU EDAD DEL CLIENTE"></i>
                                                        @endif
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Lugar nac.</th>
                                                    <td>{{$cliente->paisnacimiento}} - {{$cliente->lugarnacimiento}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Genero</th>
                                                    <td>{{$cliente->genero}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Estado civil</th>
                                                    <td>{{$cliente->estadocivil}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Email</th>
                                                    <td>{{$cliente->email}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Profesion / Ocupacion</th>
                                                    <td>{{$cliente->ocupacion}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Grado inst.</th>
                                                    <td>{{$cliente->gradoinstruccion}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Celular</th>
                                                    <td>{{$cliente->celular}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Telefono</th>
                                                    <td>{{$cliente->telefono}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Domicilio</th>
                                                    <td>{{$cliente->domicilio}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
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
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Servicio</th>
                                                    <td>{{ implode(', ', $cliente->servicios->pluck('tramite')->unique()->toArray()) }}</td> <!-- Mostrar servicios separados por comas -->
                                                </tr>                                
                                                <tr>
                                                    <th>NUA/CUA</th>
                                                    <td>{{$cliente->nuacua}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Estado laboral</th>
                                                    <td>{{$cliente->estadolaboral}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Empresa</th>
                                                    <td>{{$cliente->empresa}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Pais res.</th>
                                                    <td>{{$cliente->paisresidencia}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Dep. res.</th>
                                                    <td>{{$cliente->departamentoresidencia}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ciudad res.</th>
                                                    <td>{{$cliente->ciudadresidencia}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Aseguradora</th>
                                                    <td>{{$cliente->aseguradora}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Referenciador</th>
                                                    <td>{{$cliente->referenciador}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Gestora</th>
                                                    <td>{{$cliente->afp}}</td>
                                                </tr>
                                                <tr>
                                                    <th>N. hijos &lt; 25</th>
                                                    <td>{{$cliente->numhijosmenores}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Alertas</th>
                                                    <td>{{$cliente->alertas ?? 0}}</td>
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

<!-- ACCIONES DEL CLIENTE -->
<div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="ventanaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <strong style="text-align: center; font-size:20px; margin-top: 20px;">ACCIONES DEL CLIENTE</strong>
            <style>
                .bordeetapa1 {
                    border-top: 2px solid #26a1c0;
                    border-bottom: 2px solid #26a1c0;
                    border-right: 2px solid #26a1c0;
                    border-left: 2px solid #26a1c0;
                }
                .bordeetapa2 {
                    border-top: 2px solid #409c3e;
                    border-bottom: 2px solid #409c3e;
                    border-right: 2px solid #409c3e;
                    border-left: 2px solid #409c3e;
                }
                .bordeetapa3 {
                    border-top: 2px solid #a3bc35;
                    border-bottom: 2px solid #a3bc35;
                    border-right: 2px solid #a3bc35;
                    border-left: 2px solid #a3bc35;
                }
                .otros {
                    border-top: 2px solid #c47a35;
                    border-bottom: 2px solid #c47a35;
                    border-right: 2px solid #c47a35;
                    border-left: 2px solid #c47a35;
                }
            </style>
            <div class="modal-body">
                <div style="background-color: #e9fbff;  border-radius: 40px;">
                    <div style="text-align: center;padding: 1.5px;">
                        <strong style="color: #26a1c0; font-size:20px;">ETAPA 1</strong>
                    </div>
                    <div class="row text-center">
                        @can('admin.asociados.vercontactoclienteita')
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.asociados.vercontactoclienteita', $cliente) }}" class="btn btn-contactos btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CONTACTOS">
                                <i class="fas fa-users"></i>
                                <strong>CONTACTOS</strong>
                            </a>
                        </div>
                        @endcan

                        @can('admin.asociados.crearbateriaclienteita')

                        @if ($tieneContactos)
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.listadotramiteclienteita', $cliente) }}" class="btn btn-asignartramite btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="ASIGNAR SERVICIO">
                                    <i class="fas fa-atlas"></i>
                                    <strong>SERVICIOS</strong>
                                </a>
                            </div>
                        @else
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-asignartramite btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="ASIGNAR SERVICIO" aria-disabled="true">
                                    <i class="fas fa-atlas"></i>
                                    <strong>SERVICIOS</strong>
                                </a>
                            </div>
                        @endif

                        @endcan
                        @can('admin.asociados.generarchecklistclienteita')
                            @if ($tieneTramites)
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.generarchecklistclienteita', $cliente) }}" class="btn btn-requisitos btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="DERIVACION Y REQUISITOS">
                                    <i class="fas fa-tasks"></i>
                                    <strong>DERIV. Y REQ.</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-requisitos btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="DERIVACION Y REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-tasks"></i>
                                    <strong>DERIV. Y REQ.</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                    </div>
                </div>
                
                @can('admin.asociados.crearbateriaclienteita')

                <div style="margin-top: 10px; background-color: #e9ffe9;  border-radius: 40px;">
                    <div style="text-align: center; padding: 1.5px;">
                        <strong style="color: #409c3e; font-size:20px;">ETAPA 2</strong>
                    </div>
                    <div class="row text-center">
                        @can('admin.asociados.crearbateriaclienteita')
                            @if (($tieneRequisitos && $cartaconsentimientoExistente) || $tieneBateria || $bateriaaprobadaExistente)
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.crearbateriaclienteita', $cliente) }}" class="btn btn-bateria btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CREAR BATERÍA">
                                    <i class="fas fa-charging-station"></i>
                                    <strong>BATERIA</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-bateria btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="SUBIR DOCUMENTACIÓN REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-charging-station"></i>
                                    <strong>BATERIA</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                        @can('admin.asociados.aprobacioncotizacionclienteita')
                        @if ($tieneBateria)
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.aprobacioncotizacionclienteita', $cliente) }}" class="btn btn-cotizacion btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="COTIZACIÓN DE PROGRAMACIÓN">
                                    <i class="fas fa-donate"></i>
                                    <strong>COTIZACIÓN</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-cotizacion btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="COTIZACIÓN DE PROGRAMACIÓN" aria-disabled="true">
                                    <i class="fas fa-donate"></i>
                                    <strong>COTIZACIÓN</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                    </div>
                </div>

                <div style="margin-top: 10px; background-color: #fbffe7;  border-radius: 40px;">
                    <div style="text-align: center; padding: 1.5px;">
                        <strong style="color: #a3bc35; font-size:20px;">ETAPA 3</strong>
                    </div>
                    <div class="row text-center">
                        @can('admin.asociados.crearprogramacionclienteita')
                            @if ($tieneCotizacionaprobada)
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.crearprogramacionclienteita', $cliente) }}" class="btn btn-programar btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE">
                                    <i class="fas fa-calendar-alt"></i>
                                    <strong>PROG.</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-programar btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                    <i class="fas fa-calendar-alt"></i>
                                    <strong>PROG.</strong>
                                </a>
                            </div>
                            @endif
                        @endcan

                        @can('admin.asociados.estadoprogramacionclienteita')
                            @if ($tieneProgramacion)
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.estadoprogramacionclienteita', $cliente) }}" class="btn btn-estado btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="ESTADO DE PROGRAMACIÓN">
                                    <i class="fas fa-calendar-check"></i>
                                    <strong>ESTADO P.</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-estado btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                    <i class="fas fa-calendar-check"></i>
                                    <strong>ESTADO P.</strong>
                                </a>
                            </div>
                            @endif
                        @endcan

                        @can('admin.asociados.creardocumentacionclienteita')
                            @if ($tieneProgramacionatentido)
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.creardocumentacionclienteita', $cliente) }}" class="btn btn-subirdocumento btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="SUBIR INFORMES">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-subirdocumento btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                    </div>
                </div>

                <div style="margin-top: 10px; background-color: #fff0e3;  border-radius: 40px;">
                    <div style="text-align: center; padding: 1.5px;">
                        <strong  style="color: #c47a35; font-size:20px;">OTROS</strong>
                    </div>
                    <div class="row text-center">
                        
                        @can('admin.asociados.editarclienteita')
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.asociados.editarclienteita', $cliente) }}" class="btn btn-editar btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="EDITAR CLIENTE">
                                <i class="fas fa-edit"></i>
                                <strong>EDITAR</strong>
                            </a>
                        </div>
                        @endcan

                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <button type="button" class="btn btn-proveedorinforme btn-icono btn-block" data-toggle="modal" data-target="#proveedorinformeModal" data-placement="top" title="PROVEEDOR INFORME FINAL">
                                <i class="fas fa-user-md"></i>
                                <strong>PROV. INF.</strong>
                            </button>
                        </div>

                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <button type="button" class="btn btn-historiamedica btn-icono btn-block" data-toggle="modal" data-target="#historialMedicoModal" data-placement="top" title="HISTORIA MÉDICA">
                                <i class="fas fa-archive"></i>
                                <strong>HIST. MED.</strong>
                            </button>
                        </div>
                    </div>
                </div>
                @endcan
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-no" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ACCIONES DEL CLIENTE AUDITORIA MEDICA -->
<div class="modal fade" id="ventanaModalauditoriamedica" tabindex="-1" role="dialog" aria-labelledby="ventanaModalauditoriamedicaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <strong style="text-align: center; font-size:20px; margin-top: 20px;">SERVICIOS ADICIONALES</strong>
            {{-- AUDITORIA MEDICA --}}
            @if ($tieneAuditoriaMedica)
            <div class="modal-body">
                <div style="background-color: #e9fbff;  border-radius: 40px;">
                    <div style="text-align: center;padding: 1.5px;">
                        <strong style="color: #26a1c0; font-size:20px;">AUDITORIA MEDICA</strong>
                    </div>
                    <div class="row text-center">
                        @can('admin.asociados.generarchecklistclienteita')
                            @if ($tieneContactos)
                                {{-- @if (!$tienerequisitosauditoria) --}}
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="{{ route('admin.asociados.generarchecklistclienteitaaudi', $cliente) }}" class="btn btn-requisitos btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS">
                                        <i class="fas fa-tasks"></i>
                                        <strong>DERIV. Y REQ.</strong>
                                    </a>
                                </div>
                                {{-- @else
                                <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="{{ route('admin.asociados.subirdocrequisitosaudi', $cliente) }}" class="btn btn-requisitos btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="REQUISITOS" aria-disabled="true">
                                        <i class="fas fa-tasks"></i>
                                        <strong>DERIV. Y REQ.</strong>
                                    </a>
                                </div>
                                @endif --}}
                            @else
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-requisitos btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-tasks"></i>
                                    <strong>DERIV. Y REQ.</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                        @can('admin.asociados.creardocumentacionclienteita')
                            @if ($tienerequisitosauditoria)
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.creardocumentacionclienteita', $cliente) }}" class="btn btn-subirdocumento2 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="SUBIR INFORMES">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-subirdocumento2 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @endif
                        @endcan

                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <button type="button" class="btn btn-subirdocumento2 btn-icono btn-block" data-toggle="modal" data-target="#dictamenModal" data-placement="top" title="DICTAMEN">
                                <i class="fas fa-file-archive"></i>
                                <strong>DICTAMEN</strong>
                            </button>
                        </div>
                    </div>
                </div>
                
            </div>
            @endif

            @if ($tieneApelacion)
            {{-- APELACION --}}
            <div class="modal-body">
                <div style="background-color: #e9ffe9;  border-radius: 40px;">
                    <div style="text-align: center;padding: 1.5px;">
                        <strong style="color: #409c3e; font-size:20px;">APELACION</strong>
                    </div>
                    <div class="row text-center">
                        @can('admin.asociados.generarchecklistclienteita')
                            @if ($tieneContactos)
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.generarchecklistclienteitaapelacion', $cliente) }}" class="btn btn-bateria btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS">
                                    <i class="fas fa-tasks"></i>
                                    <strong>DERIV. Y REQ.</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-bateria btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-tasks"></i>
                                    <strong>DERIV. Y REQ.</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                        @can('admin.asociados.creardocumentacionclienteita')
                            @if ($tienerequisitosapelacion)
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.creardocumentacionclienteita', $cliente) }}" class="btn btn-bateria btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="SUBIR INFORMES">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-bateria btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @endif
                        @endcan

                        {{-- @can('admin.asociados.generaretiquetaclienteita')
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.asociados.generaretiquetaclienteitaapelacion', $cliente) }}" class="btn btn-bateria btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR ETIQUETA">
                                <i class="fas fa-tags"></i>
                                <strong>ETIQUETA</strong>
                            </a>
                        </div>
                        @endcan --}}
                    </div>
                </div>
                
            </div>
            @endif

            @if ($tieneSegundasolicitud)
            {{-- SEGUNDA SOLICITUD --}}
            <div class="modal-body">
                <div style="background-color: #fbffe7;  border-radius: 40px;">
                    <div style="text-align: center;padding: 1.5px;">
                        <strong style="color: #a3bc35; font-size:20px;">SEGUNDA SOLICITUD</strong>
                    </div>
                    <div class="row text-center">
                        @can('admin.asociados.generarchecklistclienteita')
                            @if ($tieneContactos)
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.generarchecklistclienteitasegsolicitud', $cliente) }}" class="btn btn-programar btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS">
                                    <i class="fas fa-tasks"></i>
                                    <strong>DERIV. Y REQ.</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-programar btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-tasks"></i>
                                    <strong>DERIV. Y REQ.</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                        @can('admin.asociados.creardocumentacionclienteita')
                            @if ($tienerequisitossegundasolicitud)
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.creardocumentacionclienteita', $cliente) }}" class="btn btn-programar btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="SUBIR INFORMES">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-programar btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
            @endif

            @if ($tieneTercerasolicitud)
            {{-- TERCERA SOLICITUD --}}
            <div class="modal-body">
                <div style="background-color: #fde7ff;  border-radius: 40px;">
                    <div style="text-align: center;padding: 1.5px;">
                        <strong style="color: #a835bc; font-size:20px;">TERCERA SOLICITUD</strong>
                    </div>
                    <div class="row text-center">
                        @can('admin.asociados.generarchecklistclienteita')
                            @if ($tieneContactos)
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.generarchecklistclienteitatercerasolicitud', $cliente) }}" class="btn btn-programar222 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS">
                                    <i class="fas fa-tasks"></i>
                                    <strong>DERIV. Y REQ.</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-programar222 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-tasks"></i>
                                    <strong>DERIV. Y REQ.</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                        @can('admin.asociados.creardocumentacionclienteita')
                            @if ($tienerequisitostercerasolicitud)
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.creardocumentacionclienteita', $cliente) }}" class="btn btn-programar222 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="SUBIR INFORMES">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-programar222 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
            @endif
            <div class="modal-footer">
                <button type="button" class="btn btn-no" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- CARTAS -->
    <div class="modal fade" id="cartasclientes" tabindex="-1" role="dialog" aria-labelledby="cartasclientesLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <strong style="text-align: center; font-size:20px; margin-top: 20px;">DOCUMENTOS DE {{ $cliente->nombrecompleto }} </strong>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    {!! Form::model($cliente, ['route' => ['admin.asociados.guardarcartaclienteauditoriaita', $cliente], 'method' => 'POST', 'files' => true]) !!}
                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}

                                    <strong style="color: #94c93b">SUBIR DOCUMENTOS</strong>
                                    <div class="form-group">
                                        {!! Form::label('detalle', 'Detalle:') !!}
                                        {!! Form::text('detalle', null, ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
                                        @error('detalle')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('fecha', 'Fecha_Reg:') !!}
                                        {!! Form::date('fecha', null, ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
                                        @error('fecha')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="mb-3">
                                            {!! Form::label('carta', 'Documento:', ['class' => 'form-label']) !!}
                                            <input type="file" name="carta" id="carta" class="dropify" accept=".pdf"/>
                                            @error('carta')
                                                <small class="text-danger fas fa-exclamation-circle">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-sm btn-si">GUARDAR</button>
                                        <button type="button" class="btn btn-sm btn-no" data-dismiss="modal">CERRAR</button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <strong style="color: #94c93b">LISTADO DE DOCUMENTOS</strong>
                                    <div class="table-responsive"></div>
                                        <table class="table table-striped">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th style="color: black; padding: 5px;">ID</th>
                                                    <th style="color: black; padding: 5px;">Detalle</th>
                                                    <th style="color: black; padding: 5px;">Fecha</th>
                                                    <th style="color: black; padding: 5px;">Doc.</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cartasclientes as $cartascliente)
                                                    <tr>
                                                        <td style="text-align: left; padding: 5px;">{{ $cartascliente->id }}</td>
                                                        <td style="text-align: left; padding: 5px;">{{ $cartascliente->detalle }}</td>
                                                        <td style="text-align: left; padding: 5px;">{{ $cartascliente->fecha }}</td>
                                                        <td style="text-align: left; padding: 5px;">
                                                            <a href="{{ asset('/cartasclientesita/' . $cliente->id . '/' . $cartascliente->documento) }}" class="btn btn-vercarta" target="_blank" title="VER DOCUMENTO">
                                                                <i class="fas fa-file-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
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

{{-- DICTAMEN --}}
<div class="modal fade modal-custom-height" id="dictamenModal" tabindex="-1" aria-labelledby="dictamenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="titulo">
                <h5 class="modal-title" id="dictamenModalLabel">DICTAMEN DE</h5>
                <h3>{{$cliente->nombrecompleto}}</h3>
            </div>
            {!! Form::model($cliente, ['route' => ['admin.asociados.guardardictamenita', $cliente], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
            {!! Form::hidden('usuarioregistroid', auth()->user()->id) !!}
            {!! Form::hidden('usuarioregistronombre', auth()->user()->name) !!}
            {!! Form::hidden('clienteitaid', $cliente->id) !!}
            {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
        
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-lg-4">
                                {!! Form::label('nrodictamen', 'Nro. Dictamen:') !!}
                                {!! Form::text('nrodictamen', null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                                @error('nrodictamen')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4">
                                {!! Form::label('fechadictamen', 'Fecha Dictamen:') !!}
                                {!! Form::date('fechadictamen', null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                                @error('fechadictamen')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4"> 
                                {!! Form::label('porcentajeinvalidez', 'Porcentaje de Invalidez:') !!}
                                <div class="input-group">
                                    {!! Form::text('porcentajeinvalidez', null, [
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'required' => 'required'
                                    ]) !!}
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                @error('porcentajeinvalidez')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-8">
                                {!! Form::label('documento', 'Documento:', ['class' => 'form-label']) !!}
                                <input type="file" name="documento" id="documento" accept=".pdf" class="form-control" required/>
                                @error('documento')
                                    <div class="text-danger">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{$message}}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group col-lg-4 d-flex justify-content-end align-items-end">
                                <div>
                                    <button type="submit" class="btn btn-si">GUARDAR</button>
                                    <button type="button" class="btn btn-no" data-dismiss="modal" aria-label="Cerrar">CERRAR</button>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="col-lg-12">
                                <table class="table table-striped table-sm">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th style="color: black">ID Reg.</th>
                                            <th style="color: black">Nro. Dictamen</th>
                                            <th style="color: black">Fecha Dictamen</th>
                                            <th style="color: black">Porcentaje Invalidez</th>
                                            <th style="color: black">Archivo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dictamenitas as $dictamenita)
                                            <tr>
                                                <td style="text-align: left">{{ $dictamenita->id }}</td>
                                                <td style="text-align: left">{{ $dictamenita->nrodictamen }}</td>
                                                <td style="text-align: left">{{ $dictamenita->fechadictamen }}</td>
                                                <td style="text-align: left">{{ $dictamenita->porcentajeinvalidez }}</td>
                                                <td style="text-align: left">
                                                    @if($dictamenita->documento)
                                                        <a href="{{ asset('dictamenita/' . $dictamenita->clienteitaid . '/' . $dictamenita->documento) }}" 
                                                            target="_blank" class="btn btn-vercarta btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @else
                                                        VACIO
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
            </div>
        </div>
    </div>
</div>

{{-- PROVEEDOR INFORME FINAL --}}
<div class="modal fade modal-custom-height" id="proveedorinformeModal" tabindex="-1" aria-labelledby="proveedorinformeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="titulo">
            <h5 class="modal-title" id="proveedorinformeModalLabel">PROVEEDOR PARA INFORME FINAL DE</h5>
            <h3>{{$cliente->nombrecompleto}}</h3>
            </div>
            {!! Form::model($cliente, ['route' => ['admin.asociados.guardarproveedorinformefinal', $cliente], 'method' => 'POST', 'files' => true]) !!}
            {!! Form::hidden('usuarioid', auth()->user()->id) !!}
            {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
        
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-4">
                        <strong style="color: #94c93b">ASIGNAR PROVEEDOR</strong>
                        <div class="form-group" style="margin-top: 15px"> 
                            <strong>Fecha Batería:</strong>
                            <select id="select-fechas" name="fechabateria" class="form-control" 
                                    data-tramites='@json($tramitesPorFecha)' 
                                    data-cliente-con-invalidez="{{ $clienteConInvalidez ? 'true' : 'false' }}"
                                    data-cliente-con-apelacion-segunda="{{ $clienteConApelacionOSegunda ? 'true' : 'false' }}">
                                <option value=""></option>
                                @foreach($accionesPorFecha as $fecha => $acciones)
                                    <option value="{{ $fecha }}">{{ $fecha }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('tramite', 'Trámite asignado:') !!}
                            {!! Form::text('tramite', null, ['class' => 'form-control', 'placeholder' => '' , 'readonly' => 'readonly' ]) !!}
                            @error('tramite')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        
                        <script>
                           document.addEventListener('DOMContentLoaded', function () {
                            const selectFechas = document.getElementById('select-fechas');
                            const tramiteInput = document.querySelector('input[name="tramite"]');

                            if (selectFechas) {
                                const tramitesPorFecha = JSON.parse(selectFechas.getAttribute('data-tramites'));
                                selectFechas.addEventListener('change', function () {
                                    const fechaSeleccionada = this.value;
                                    if (tramitesPorFecha[fechaSeleccionada]) {
                                        tramiteInput.value = tramitesPorFecha[fechaSeleccionada];
                                    } else {
                                        tramiteInput.value = '';
                                    }
                                });
                            }
                        });
                        </script>
                        <div class="form-group">
                            {!! Form::label('proveedorasignado', 'Proveedor:') !!}
                            {!! Form::select('proveedorasignado', $proveedores->pluck('proveedor', 'id'), null, [
                                'class' => 'form-control', 
                                'id' => 'proveedorasignado', 
                                'placeholder' => ''
                            ]) !!}
                            @error('proveedorasignado')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group" hidden>
                            {!! Form::label('celularproveedor', 'Celular del proveedor:') !!}
                            {!! Form::text('celularproveedor', null, ['class' => 'form-control', 'id' => 'celularproveedor', 'readonly' => true]) !!}
                            @error('celularproveedor')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group"> 
                            {!! Form::label('precio', 'Precio:') !!}
                            {!! Form::text('precio', null, [
                                'class' => 'form-control', 
                                'id' => 'precio', 
                                'placeholder' => '',
                                'readonly' => auth()->user()->name !== 'CARLOS ALEJANDRO GUARACHI SANDOVAL' && auth()->user()->name !== 'DENISSE MAUREN LOPEZ FLORES' && auth()->user()->name !== 'JHOSELINE EVA VELASQUEZ ESCOBAR' && auth()->user()->name !== 'VANESSA MAMANI HUANACO' ? 'readonly' : null
                            ]) !!}
                            @error('precio')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group" hidden>
                            {!! Form::label('preciocompra', 'Precio Compra:') !!}
                            {!! Form::text('preciocompra', null, ['class' => 'form-control', 'id' => 'preciocompra', 'placeholder' => '' ]) !!}
                            @error('preciocompra')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <script>
                           document.addEventListener('DOMContentLoaded', function () {
                                const selectFechas = document.getElementById('select-fechas');
                                const tramiteInput = document.querySelector('input[name="tramite"]');
                                const precioInput = document.getElementById('precio');
                                const proveedorSelect = document.getElementById('proveedorasignado');
                                const precioCompraInput = document.getElementById('preciocompra');

                                const clienteConInvalidez = selectFechas.getAttribute('data-cliente-con-invalidez') === 'true';
                                const clienteConApelacionOSegunda = selectFechas.getAttribute('data-cliente-con-apelacion-segunda') === 'true';

                                selectFechas.addEventListener('change', function () {
                                    const fechaSeleccionada = this.value;
                                    const tramitesPorFecha = JSON.parse(this.getAttribute('data-tramites'));

                                    if (tramitesPorFecha[fechaSeleccionada]) {
                                        const tramiteAutorellenado = tramitesPorFecha[fechaSeleccionada];
                                        tramiteInput.value = tramiteAutorellenado;

                                        if (tramiteAutorellenado === 'AUDITORIA MEDICA' || tramiteAutorellenado === 'INVALIDEZ') {
                                            precioInput.value = '2500.00';
                                        } else if (tramiteAutorellenado === 'APELACION') {
                                            precioInput.value = '1300.00';
                                        } else if (tramiteAutorellenado === 'SEGUNDA SOLICITUD') {
                                            precioInput.value = clienteConInvalidez ? '1300.00' : '2500.00';
                                        } else {
                                            precioInput.value = '1300.00';
                                        }
                                    } else {
                                        tramiteInput.value = '';
                                        precioInput.value = '';
                                    }
                                });

                                // Escucha cambios en el select Proveedor
                                proveedorSelect.addEventListener('change', function () {
                                    const proveedorSeleccionado = proveedorSelect.options[proveedorSelect.selectedIndex].text;

                                    if (proveedorSeleccionado === 'AGUIRRE VASQUEZ MARIA RENEE') {
                                        precioCompraInput.value = '250.00';
                                    } else if (proveedorSeleccionado === 'MARIA ANGELA LOZANO FLORES') {
                                        precioCompraInput.value = '400.00';
                                    } else {
                                        precioCompraInput.value = ''; // Limpia si no coincide
                                    }
                                });
                            });


                        </script>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var proveedores = @json($proveedores);
                                var selectProveedor = document.getElementById('proveedorasignado');
                                var celularProveedor = document.getElementById('celularproveedor');
                    
                                selectProveedor.addEventListener('change', function() {
                                    var selectedId = parseInt(this.value);
                                    
                                    var proveedorSeleccionado = proveedores.find(function(proveedor) {
                                        return proveedor.id === selectedId;
                                    });
                                    
                                    if (proveedorSeleccionado) {
                                        celularProveedor.value = '591' + proveedorSeleccionado.celular;
                                    } else {
                                        celularProveedor.value = '';
                                    }
                                });
                            });
                        </script>
                    
                        <div class="modal-footer" style="margin-top: 30px;">
                            <button type="submit" class="btn btn-si">Asignar</button>
                            <button type="button" class="btn btn-no" data-dismiss="modal" aria-label="Cerrar">Cerrar</button>
                        </div>
                        {!! Form::close() !!}
                    </div>

                    <div class="col-lg-8">
                        <strong style="color: #94c93b">PROVEEDORES ASIGNADOS</strong> 
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="color: black">Bateria</th>
                                    <th style="color: black">Servicio</th>
                                    <th style="color: black">Proveedor</th>
                                    {{-- <th style="color: black">Celular</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requisitosubclientes as $requisitosubcliente)
                                    <tr>
                                        <td style="text-align: left">{{ $requisitosubcliente->fechabateria }}</td>
                                        <td style="text-align: left">{{ $requisitosubcliente->servicio }}</td>
                                        <td style="text-align: left">{{ $requisitosubcliente->proveedorasignado }}</td>
                                        {{-- <td style="text-align: left">{{ $requisitosubcliente->celularproveedor }}</td> --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- VER HISTORIA MEDICA --}}
<div class="modal fade modal-custom-height" id="historialMedicoModal" tabindex="-1" aria-labelledby="historialMedicoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="titulo">
                <h5 class="modal-title" id="historialMedicoModalLabel">HISTORIA MÉDICA DE</h5>
                <h3>{{$cliente->nombrecompleto}}</h3>
            </div>
            {!! Form::model($cliente, ['route' => ['admin.asociados.guardarhistoriamedica', $cliente], 'method' => 'POST', 'files' => true]) !!}
            {!! Form::hidden('usuarioid', auth()->user()->id) !!}
            {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}

            @if($documentacion)
            <div class="modal-body text-center">
                <!-- Vista previa del documento -->
                <div class="pdf-preview-container mb-3">
                    <iframe 
                        src="{{ asset('/historiamedica/' . $cliente->id . '/extracted/' . $historiamedicaclienteita) }}" 
                        width="100%" 
                        height="400px" 
                        frameborder="0" 
                        style="border: 1px solid #ccc;">
                    </iframe>
                </div><br>

                <!-- Botón para ver el documento completo -->
                <a href="{{ asset('/historiamedica/' . $cliente->id . '/extracted/' . $historiamedicaclienteita) }}" 
                   class="btn btn-verhistoriamedica" 
                   target="_blank">
                    <i class="fas fa-book-medical"></i> Ver Documento Completo
                </a>
            </div>
            @else
            <div class="modal-body" style="margin-top: 50px;">
                <div class="mb-3">
                    {!! Form::label('file', 'Documento:', ['class' => 'form-label']) !!}
                    <input type="file" name="archivo" id="archivo" class="dropify" accept=".pdf"/>
                    @error('archivo')
                        <div class="text-danger">
                            <i class="fas fa-exclamation-circle"></i> {{$message}}
                        </div>
                    @enderror
                </div>
                <input type="text" class="form-control" id="accion" name="accion" value="HISTORIA MÉDICA" hidden>
            </div>
            <div class="modal-footer" style="margin-top: 50px;">
                <button type="button" class="btn btn-no" data-dismiss="modal" aria-label="Cerrar">Cerrar</button>
                <button type="submit" class="btn btn-si">Subir Doc.</button>
            </div>
            @endif
            {!! Form::close() !!}
        </div>
    </div>
</div>

<style>
    .btn-verhistoriamedica {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 10px;
        margin-top: 20px;
    }
    .btn-verhistoriamedica:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .pdf-preview {
      width: 100%;
      height: 430px; /* Ajusta la altura del visor PDF */
      border: none;
    }
    .titulo {
      margin-top: 50px;
      margin-left: 20px;
    }
    /* Define el alto específico para el modal con la clase personalizada */
    .modal-custom-height .modal-dialog {
      height: 93.5vh; /* 75% de la altura de la ventana del navegador */
    }
    .modal-custom-height .modal-content {
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .modal-custom-height .modal-body {
      overflow-y: auto; /* Permite desplazamiento vertical */
      flex: 1; /* Permite que el contenido se expanda */
      padding: 2rem; /* Espaciado interior */
    }
    .modal-footer {
      justify-content: center; /* Centra los botones en el pie del modal */
    }
    .dropify-wrapper {
      border-radius: 0.25rem;
    }
  </style>
@endsection
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

<script>
    $(document).ready(function() {
        $('.dropify').dropify({
            messages: {
                'default': 'Arrastre y suelte un archivo o haga clic aquí',
                'replace': 'Arrastre y suelte o haga clic para reemplazar',
                'remove': 'Eliminar',
                'error': 'Ooops, algo salió mal.'
            }
        });
    
        $('.dropify').on('dropify.error.fileSize', function(event, element) {
            var maxSize = element.input.files[0].size / (1024 * 1024);
            var errorMessage = 'El archivo es demasiado grande (' + maxSize.toFixed(2) + ' MB máx.).';
            $(element.input).siblings('.dropify-error').text(errorMessage);
        });
    });

    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });

    document.getElementById('archivo').addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var fileURL = URL.createObjectURL(file);
            var previewCard = document.getElementById('preview-card');
            var documentPreview = document.getElementById('document-preview');
    
            previewCard.style.display = 'block';
            documentPreview.src = fileURL;
        } else {
            var previewCard = document.getElementById('preview-card');
            previewCard.style.display = 'none';
            documentPreview.src = '';
        }
    });

//CANCELAR FUNCION DE LA TECLA ENTER
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
@stop

@section('css')
<style>
    /* Estilo para el botón deshabilitado */
    .btn.disabled, .btn.disabled:hover, .btn.disabled:focus, .btn.disabled:active {
        pointer-events: none; /* Evita la interacción con el botón */
        opacity: 0.6; /* Da un aspecto visual de deshabilitado */
        cursor: not-allowed; /* Cambia el cursor para mostrar que está deshabilitado */
    }
</style>
<style>
    .btn-vercarta {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-vercarta:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-cartas {
        background-color: #ffffff;
        color: #9c3bc9;
        border-color: #9c3bc9;
        border-radius: 5px;
        padding: 5px 10px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-cartas:hover {
        background-color: #9c3bc9;
        color: #ffffff;
    }
    .btn-no {
    color: #fd1d1d;
    border-color: #fd1d1d;
    }
    .btn-no:hover {
    background-color: #fd1d1d;
    color: #ffffff;
    }
    .btn-si {
    color: #94c93b;
    border-color: #94c93b;
    }
    .btn-si:hover {
    background-color: #94c93b;
    color: #ffffff;
    }
    .custom-dropdown {
        position: relative;
        display: inline-block;
    }
    .custom-select-wrapper {
        border: 1px solid black;
        background-color: #fceacf;
        padding: 1px;
        text-align: center;
        border-radius: 5px;
        width: 140px; 
    }
    .custom-dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        width: 200px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }
    .custom-dropdown-content a {
        color: black;
        padding: 0px 5px;
        text-decoration: none;
        display: block;
    }
    .custom-dropdown-content a:hover {
        background-color: #eefed3;
    }
    .custom-dropdown:hover .custom-dropdown-content {
        display: block;
    }
    th, td {
        border-bottom: 1px solid #94c93b;
    }
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 700;
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
    #vista-previa {
        display: block;
        height: auto;
        border: 1px solid #ccc;
        padding: 5px;
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2);
        }
    table {
        border-collapse: separate;
        border-spacing: 2px;
    }
    th, td {
        padding: 3px;
    }
    td{
        text-align: right;
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
    .btn-acciones {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-acciones:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-auditoriamedica {
        background-color: #ffffff;
        color: #e0752e;
        border-color: #e0752e;
        border-radius: 5px;
        padding: 5px 10px;

    }
    .btn-auditoriamedica:hover {
        background-color: #e0752e;
        color: #ffffff;
    }
    .btn-generaretiqueta {
        background-color: #ffffff;
        color: #ce5bda;
        border-color: #ce5bda;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-generaretiqueta:hover {
        background-color: #ce5bda;
        color: #ffffff;
    }
    .btn-cerrar {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cerrar:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-contactos {
        width: 100px;
        height: 90px;
        font-size: 13px;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #26a1c0;
        border-color: #26a1c0;
        border-radius: 5px;
        flex-direction: column;
    }
    .btn-contactos:hover {
        background-color: #26a1c0;
        color: #ffffff;
    }
    .btn-icono i {
        font-size: 4em;
    }
    .btn-editar {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #e05f28;
        border-color: #e05f28;
        border-radius: 5px;
    }
    .btn-editar:hover {
        background-color: #e05f28;
        color: #ffffff;
    }
    .btn-editar i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-bateria {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #148734;
        border-color: #148734;
        border-radius: 5px;
    }
    .btn-bateria:hover {
        background-color: #148734;
        color: #ffffff;
    }
    .btn-bateria i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-cotizacion {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #148734;
        border-color: #148734;
        border-radius: 5px;
    }
    .btn-cotizacion:hover {
        background-color: #148734;
        color: #ffffff;
    }
    .btn-cotizacion i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-aprobacion {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #000000;
        border-color: #000000;
        border-radius: 5px;
    }
    .btn-aprobacion:hover {
        background-color: #000000;
        color: #ffffff;
    }
    .btn-aprobacion i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-programar {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #aeae2b;
        border-color: #aeae2b;
        border-radius: 5px;
    }
    .btn-programar:hover {
        background-color: #aeae2b;
        color: #ffffff;
    }
    .btn-programar i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-programar222 {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #ac2bae;
        border-color: #ac2bae;
        border-radius: 5px;
    }
    .btn-programar222:hover {
        background-color: #ac2bae;
        color: #ffffff;
    }
    .btn-programar222 i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-estado {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #aeae2b;
        border-color: #aeae2b;
        border-radius: 5px;
    }
    .btn-estado:hover {
        background-color: #aeae2b;
        color: #ffffff;
    }
    .btn-estado i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-subirdocumento {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #aeae2b;
        border-color: #aeae2b;
        border-radius: 5px;
    }
    .btn-subirdocumento:hover {
        background-color: #aeae2b;
        color: #ffffff;
    }
    .btn-proveedorinforme {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #e05f28;
        border-color: #e05f28;
        border-radius: 5px;
    }
    .btn-proveedorinforme:hover {
        background-color: #e05f28;
        color: #ffffff;
    }
    .btn-historiamedica {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #e05f28;
        border-color: #e05f28;
        border-radius: 5px;
    }
    .btn-historiamedica:hover {
        background-color: #e05f28;
        color: #ffffff;
    }
    .btn-asignartramite {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #26a1c0;
        border-color: #26a1c0;
        border-radius: 5px;
    }
    .btn-asignartramite:hover {
        background-color: #26a1c0;
        color: #ffffff;
    }
    .btn-subirdocumento i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-listadodocumentos {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #e05f28;
        border-color: #e05f28;
        border-radius: 5px;
    }
    .btn-listadodocumentos:hover {
        background-color: #e05f28;
        color: #ffffff;
    }
    .btn-listadodocumentos i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-formulario {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #e05f28;
        border-color: #e05f28;
        border-radius: 5px;
    }
    .btn-formulario:hover {
        background-color: #e05f28;
        color: #ffffff;
    }
    .btn-formulario i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-etiqueta {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #e05f28;
        border-color: #e05f28;
        border-radius: 5px;
    }
    .btn-etiqueta:hover {
        background-color: #e05f28;
        color: #ffffff;
    }
    .btn-etiqueta2 {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #26a1c0;
        border-color: #26a1c0;
        border-radius: 5px;
    }
    .btn-etiqueta2:hover {
        background-color: #26a1c0;
        color: #ffffff;
    }
    .btn-etiqueta2 i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-subirdocumento2 {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #26a1c0;
        border-color: #26a1c0;
        border-radius: 5px;
    }
    .btn-subirdocumento2:hover {
        background-color: #26a1c0;
        color: #ffffff;
    }
    .btn-requisitos {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #26a1c0;
        border-color: #26a1c0;
        border-radius: 5px;
    }
    .btn-requisitos:hover {
        background-color: #26a1c0;
        color: #ffffff;
    }
    .btn-requisitos i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-subirrequisitos {
        width: 100px;
        height: 90px;
        font-size: 13px;
        flex-direction: column;
        text-align: center;
        padding: 10px;
        background-color: #ffffff;
        color: #000000;
        border-color: #000000;
        border-radius: 5px;
    }
    .btn-subirrequisitos:hover {
        background-color: #000000;
        color: #ffffff;
    }
    .btn-subirrequisitos i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-contactos i {
        display: inline-block;
        vertical-align: middle;
    }
</style>
@stop
