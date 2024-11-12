@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.listadoclienteita', ['asociado' => 6]) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-acciones" data-toggle="modal" data-target="#ventanaModal">ACCIONES DEL CLIENTE</a>
@if ($tieneAuditoriaMedica || $tieneApelacion || $tieneSegundasolicitud)
    <a class="btn btn-sm float-right btn-auditoriamedica" data-toggle="modal" data-target="#ventanaModalauditoriamedica">SERVICIOS ADICIONALES</a>
@endif

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
                                                    <td>{{$cliente->cicomplemento}}</td>
                                                </tr>
                                                <tr>
                                                    <th>C/exp.</th>
                                                    <td>{{$cliente->ciexp}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha Ven/CI</th>
                                                    <td>{{$cliente->fechavencci}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Edad</th>
                                                    <td>{{$cliente->edad}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ciudad nac.</th>
                                                    <td>{{$cliente->lugarnacimiento}}</td>
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
                                                    <th>AFP</th>
                                                    <td>{{$cliente->afp}}</td>
                                                </tr>
                                                <tr>
                                                    <th>N. hijos &lt; 25</th>
                                                    <td>{{$cliente->numhijosmenores}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Alertas</th>
                                                    <td>{{$cliente->alertas}}</td>
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
            {{-- <div class="modal-header" >
                <h5 class="" id="ventanaModalLabel" style="align-content: center"><strong style="color: #000000; text-align:center">ACCIONES DEL CLIENTE</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> --}}
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

                        @can('admin.asociados.generarchecklistclienteita')
                            @if ($tieneTramites)
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.generarchecklistclienteita', $cliente) }}" class="btn btn-requisitos btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS">
                                    <i class="fas fa-tasks"></i>
                                    <strong>REQUISITOS</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-requisitos btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="SUBIR DOCUMENTACIÓN REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-tasks"></i>
                                    <strong>REQUISITOS</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                    </div>
                </div>
                
                <div style="margin-top: 10px; background-color: #e9ffe9;  border-radius: 40px;">
                    <div style="text-align: center; padding: 1.5px;">
                        <strong style="color: #409c3e; font-size:20px;">ETAPA 2</strong>
                    </div>
                    {{-- @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'VANESSA MAMANI HUANACO' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
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
                    @else --}}
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
                    {{-- @endif --}}
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

                        {{-- @can('admin.asociados.subirhistorialmedico') --}}
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <button type="button" class="btn btn-proveedorinforme btn-icono btn-block" data-toggle="modal" data-target="#proveedorinformeModal" data-placement="top" title="PROVEEDOR INFORME FINAL">
                                <i class="fas fa-user-md"></i>
                                <strong>PROV. INF.</strong>
                            </button>
                        </div>
                        {{-- @endcan --}}

                        
                        
                        {{-- @can('admin.asociados.subirhistorialmedico') --}}
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <button type="button" class="btn btn-historiamedica btn-icono btn-block" data-toggle="modal" data-target="#historialMedicoModal" data-placement="top" title="HISTORIA MÉDICA">
                                <i class="fas fa-archive"></i>
                                <strong>HIST. MED.</strong>
                            </button>
                        </div>
                        {{-- @endcan --}}
                        {{-- @can('admin.asociados.generaretiquetaclienteita')
                        <div class="col-12 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.asociados.generaretiquetaclienteita', $cliente) }}" class="btn btn-etiqueta btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR ETIQUETA">
                                <i class="fas fa-tags"></i>
                                <strong>ETIQUETA</strong>
                            </a>
                        </div>
                        @endcan --}}
                    </div>
                </div>
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
                                @if (!$tienerequisitosauditoria)
                                <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="{{ route('admin.asociados.generarchecklistclienteitaaudi', $cliente) }}" class="btn btn-requisitos btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS">
                                        <i class="fas fa-tasks"></i>
                                        <strong>REQUISITOS</strong>
                                    </a>
                                </div>
                                @else
                                <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="{{ route('admin.asociados.subirdocrequisitosaudi', $cliente) }}" class="btn btn-requisitos btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="REQUISITOS" aria-disabled="true">
                                        <i class="fas fa-tasks"></i>
                                        <strong>REQUISITOS</strong>
                                    </a>
                                </div>
                                @endif
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-requisitos btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-tasks"></i>
                                    <strong>REQUISITOS</strong>
                                </a>
                            </div>
                            @endif
                        @endcan
                        @can('admin.asociados.creardocumentacionclienteita')
                            @if ($tienerequisitosauditoria)
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.creardocumentacionclienteita', $cliente) }}" class="btn btn-subirdocumento2 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="SUBIR INFORMES">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-subirdocumento2 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                    <i class="fas fa-list-alt"></i>
                                    <strong>INFORMES</strong>
                                </a>
                            </div>
                            @endif
                        @endcan

                        {{-- @can('admin.asociados.generaretiquetaclienteita')
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.asociados.generaretiquetaclienteitaauditoria', $cliente) }}" class="btn btn-etiqueta2 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR ETIQUETA">
                                <i class="fas fa-tags"></i>
                                <strong>ETIQUETA</strong>
                            </a>
                        </div>
                        @endcan --}}
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
                                    <strong>REQUISITOS</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-bateria btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-tasks"></i>
                                    <strong>REQUISITOS</strong>
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
                                    <strong>REQUISITOS</strong>
                                </a>
                            </div>
                            @else
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="#" class="btn btn-programar btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS" aria-disabled="true">
                                    <i class="fas fa-tasks"></i>
                                    <strong>REQUISITOS</strong>
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

                        {{-- @can('admin.asociados.generaretiquetaclienteita')
                        <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                            <a href="{{ route('admin.asociados.generaretiquetaclienteitasegundasolicitud', $cliente) }}" class="btn btn-programar btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR ETIQUETA">
                                <i class="fas fa-tags"></i>
                                <strong>ETIQUETA</strong>
                            </a>
                        </div>
                        @endcan --}}
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
                            <strong>Cod. de Batería:</strong>
                            <select id="select-fechas" name="fechabateria" class="form-control">
                                <option value=""></option>
                                @foreach($accionesPorFecha as $fecha => $acciones)
                                    <option value="{{ $fecha }}">{{ $fecha }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            {!! Form::label('proveedorasignado', 'Proveedor:') !!}
                            {!! Form::select('proveedorasignado', $proveedores->pluck('proveedor', 'id'), null, ['class' => 'form-control proveedor-select', 'id' => 'proveedorasignado', 'placeholder' => '' ]) !!}
                            @error('proveedorasignado')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            {!! Form::label('celularproveedor', 'Celular del proveedor:') !!}
                            {!! Form::text('celularproveedor', null, ['class' => 'form-control', 'id' => 'celularproveedor', 'readonly' => true]) !!}
                            @error('celularproveedor')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                        
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
                                    <th style="color: black">Proveedor</th>
                                    <th style="color: black">Celular</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requisitosubclientes as $requisitosubcliente)
                                    <tr>
                                        <td style="text-align: left">{{ $requisitosubcliente->fechabateria }}</td>
                                        <td style="text-align: left">{{ $requisitosubcliente->proveedorasignado }}</td>
                                        <td style="text-align: left">{{ $requisitosubcliente->celularproveedor }}</td>
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
                <!-- Visor PDF -->
                <iframe class="pdf-preview" src="{{ route('ver.documento', $documentacion->id) }}" type="application/pdf"></iframe>

                <a href="{{ route('ver.documento', $documentacion->id) }}" class="btn btn-verhistoriamedica" target="_blank">
                    <strong>VER HIST. MED.</strong>
                </a>
            </div>
            @else
            <div class="modal-body" style="margin-top: 50px;">
                <div class="mb-3">
                    {!! Form::label('file', 'Documento:', ['class' => 'form-label']) !!}
                    <input type="file" name="archivo" id="archivo" class="dropify" />
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
        padding: 10px 10px;
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
        padding: 10px 10px;
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
        padding: 10px 10px;

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
