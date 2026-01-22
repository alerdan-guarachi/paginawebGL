@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.listadoclienteauditoria', ['asociado' => 3]) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-acciones" data-toggle="modal" data-target="#ventanaModal">ACCIONES DEL CLIENTE</a>
@can('admin.asociados.subirdocumentacionextra')
<a class="btn btn-sm float-right btn-cartas" data-toggle="modal" data-target="#cartasclientes">DOCUMENTACIÓN</a>
@endcan

<h5>DATOS DE:</h5>
<h3>{{$clienteauditoria->nombrecompleto}}</h3>
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
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Sucursal</th>
                                                    <td>{{$clienteauditoria->sucursal}}</td>
                                                </tr>
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{$clienteauditoria->id}}</td>
                                                </tr>
                                                <tr>
                                                    <th>CI</th>
                                                    <td>{{$clienteauditoria->ci}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Género</th>
                                                    <td>{{$clienteauditoria->genero}}</td>
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

                                                    $fechaNacimiento = $clienteauditoria->fechanacimiento;
                                                    $edadCalculada = $fechaNacimiento ? Carbon::parse($fechaNacimiento)->age : null;
                                                @endphp

                                                <tr> 
                                                    <th>Fecha de nacimiento</th>
                                                    <td>{{ $fechaNacimiento ?? 'NINGUNO' }}</td>
                                                </tr>      
                                                <tr>
                                                    <th>Edad</th>
                                                    <td>
                                                        {{ $clienteauditoria->edad }}
                                                        @if ($fechaNacimiento && $clienteauditoria->edad != $edadCalculada)
                                                            <i class="fas fa-exclamation-triangle text-danger icon-pulse" title="SE DEBE ACTUALIZAR SU EDAD DEL CLIENTE"></i>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Lugar de nacimiento</th>
                                                    <td>{{$clienteauditoria->lugarnacimiento}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
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
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <th>Lugar de residencia</th>
                                                    <td>{{$clienteauditoria->lugarresidencia}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Estado civil</th>
                                                    <td>
                                                        {{ $clienteauditoria->estadocivil }}
                                                        @if(!empty($clienteauditoria->nombreespcon))
                                                            - {{ $clienteauditoria->nombreespcon }}
                                                        @endif
                                                        @if(!empty($clienteauditoria->ciespcon))
                                                            - {{ $clienteauditoria->ciespcon }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Domicilio</th>
                                                    <td>{{$clienteauditoria->direccion}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Celular</th>
                                                    <td>{{$clienteauditoria->celular}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Grado de instrucción</th>
                                                    <td>{{$clienteauditoria->gradoinstruccion}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Ocupación / Profesión</th>
                                                    <td>{{$clienteauditoria->ocupacionprofesion}}</td>
                                                </tr>
                                                <tr>
                                                    <th>Estado laboral</th>
                                                    <td>{{$clienteauditoria->actividadlaboral}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($clienteauditoria->banco1)
                <div class="col-lg-12" style="text-align: center; font-weight: bold; font-size: 18px;">ENTIDADES FINANCIERAS</div>
                @endif
                <div class="col-lg-6">
                    <div class="profile-feed">
                        <div class="d-flex align-items-start profile-feed-item">
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table style="width: 100%;">
                                            <tbody>
                                                @if($clienteauditoria->banco1)
                                                <tr>
                                                    <th>Banco 1</th>
                                                    <td>{{ $clienteauditoria->banco1 }}</td>
                                                </tr>
                                                @endif
                                                @if($clienteauditoria->banco2)
                                                <tr>
                                                    <th>Banco 2</th>
                                                    <td>{{ $clienteauditoria->banco2 }}</td>
                                                </tr>
                                                @endif
                                                @if($clienteauditoria->banco3)
                                                <tr>
                                                    <th>Banco 3</th>
                                                    <td>{{ $clienteauditoria->banco3 }}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
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
                                        <table style="width: 100%;">
                                            <tbody>
                                                @if($clienteauditoria->nrocredito1)
                                                <tr> 
                                                    <th>Nro. de Crédito Banco 1</th>
                                                    <td>
                                                        {{ implode(' / ', array_filter([
                                                            $clienteauditoria->nrocredito1,
                                                            $clienteauditoria->nrocredito2,
                                                            $clienteauditoria->nrocredito3,
                                                            $clienteauditoria->nrocredito4,
                                                            $clienteauditoria->nrocredito5,
                                                            $clienteauditoria->nrocredito6
                                                        ])) }}
                                                    </td>
                                                </tr>
                                                
                                                @endif
                                                @if($clienteauditoria->nrocredito7)
                                                <tr>
                                                    <th>Nro. de Crédito Banco 2</th>
                                                    <td>
                                                        {{ implode(' / ', array_filter([
                                                            $clienteauditoria->nrocredito7,
                                                            $clienteauditoria->nrocredito8,
                                                            $clienteauditoria->nrocredito9,
                                                            $clienteauditoria->nrocredito10,
                                                            $clienteauditoria->nrocredito11,
                                                            $clienteauditoria->nrocredito12
                                                        ])) }}
                                                    </td>
                                                </tr>
                                                @endif
                                                @if($clienteauditoria->nrocredito13)
                                                <tr>
                                                    <th>Nro. de Crédito Banco 3</th>
                                                    <td>
                                                        {{ implode(' / ', array_filter([
                                                            $clienteauditoria->nrocredito13,
                                                            $clienteauditoria->nrocredito14,
                                                            $clienteauditoria->nrocredito15,
                                                            $clienteauditoria->nrocredito16,
                                                            $clienteauditoria->nrocredito17,
                                                            $clienteauditoria->nrocredito18
                                                        ])) }}
                                                    </td>
                                                </tr>
                                                @endif
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


    <!-- ACCIONES DE CLIENTE -->
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
                            @can('admin.asociados.vercontactoclienteauditoria')
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.vercontactoclienteauditoria', $clienteauditoria) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CONTACTOS">
                                    <i class="fas fa-users"></i>
                                    <strong>CONTACTOS</strong>
                                </a>
                            </div>
                            @endcan
                            @can('admin.asociados.crearserviciocliente')

                            @if ($tieneContactos)
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="{{ route('admin.asociados.listadotramiteclienteauditoria', $clienteauditoria) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="ASIGNAR SERVICIO">
                                        <i class="fas fa-atlas"></i>
                                        <strong>SERVICIOS</strong>
                                    </a>
                                </div>
                            @else
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="#" class="btn btn-etapa1 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="ASIGNAR SERVICIO" aria-disabled="true">
                                        <i class="fas fa-atlas"></i>
                                        <strong>SERVICIOS</strong>
                                    </a>
                                </div>
                            @endif
                            @endcan
                            @can('admin.asociados.generarchecklistclienteauditoria')
                                @if ($tieneTramites)
                                    <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.asociados.generarchecklistclienteauditoria', $clienteauditoria) }}" class="btn btn-etapa1 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="GENERAR REQUISITOS">
                                            <i class="fas fa-tasks"></i>
                                            <strong>REQUISITOS</strong>
                                        </a>
                                    </div>
                                @else
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="#" class="btn btn-etapa1 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="SUBIR DOCUMENTACIÓN REQUISITOS" aria-disabled="true">
                                        <i class="fas fa-tasks"></i>
                                        <strong>REQUISITOS</strong>
                                    </a>
                                </div>
                                @endif
                            @endcan
                        </div>
                    </div>
                    @can('admin.asociados.crearbateriaclienteauditoria')
                    <div style="margin-top: 10px; background-color: #e9ffe9;  border-radius: 40px;">
                        <div style="text-align: center; padding: 1.5px;">
                            <strong style="color: #409c3e; font-size:20px;">ETAPA 2</strong>
                        </div> 
                        <div class="row text-center">
                            @can('admin.asociados.crearbateriaclienteauditoria')
                                    @if ($tieneRequisitos)
                                    <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.asociados.crearbateriaclienteauditoria', $clienteauditoria) }}" class="btn btn-etapa2 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CREAR BATERÍA">
                                            <i class="fas fa-charging-station"></i>
                                            <strong>BATERIA</strong>
                                        </a>
                                    </div>
                                    @else
                                    <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                        <a href="#" class="btn btn-etapa2 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="SUBIR DOCUMENTACIÓN REQUISITOS" aria-disabled="true">
                                            <i class="fas fa-charging-station"></i>
                                            <strong>BATERIA</strong>
                                        </a>
                                    </div>
                                    @endif
                                @endcan
                            @can('admin.asociados.aprobacioncotizacionclienteauditoria')
                                @if ($tieneBateria)
                                    <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.asociados.aprobacioncotizacionclienteauditoria', $clienteauditoria) }}" class="btn btn-etapa2 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="COTIZACIÓN DE PROGRAMACIÓN">
                                            <i class="fas fa-donate"></i>
                                            <strong>COTIZACIÓN</strong>
                                        </a>
                                    </div>
                                    @else
                                    <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                        <a href="#" class="btn btn-etapa2 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="COTIZACIÓN DE PROGRAMACIÓN" aria-disabled="true">
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
                            @can('admin.asociados.crearprogramacionclienteauditoria')
                                @if ($tieneCotizacionaprobada)
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="{{ route('admin.asociados.crearprogramacionclienteauditoria', $clienteauditoria) }}" class="btn btn-etapa3 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE">
                                        <i class="fas fa-calendar-alt"></i>
                                        <strong>PROG.</strong>
                                    </a>
                                </div>
                                @else
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="#" class="btn btn-etapa3 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                        <i class="fas fa-calendar-alt"></i>
                                        <strong>PROG.</strong>
                                    </a>
                                </div>
                                @endif
                            @endcan
                            @can('admin.asociados.estadoprogramacionclienteauditoria')
                                @if ($tieneProgramacion)
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="{{ route('admin.asociados.estadoprogramacionclienteauditoria', $clienteauditoria) }}" class="btn btn-etapa3 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="ESTADO DE PROGRAMACIÓN">
                                        <i class="fas fa-calendar-check"></i>
                                        <strong>ESTADO P.</strong>
                                    </a>
                                </div>
                                @else
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="#" class="btn btn-etapa3 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
                                        <i class="fas fa-calendar-check"></i>
                                        <strong>ESTADO P.</strong>
                                    </a>
                                </div>
                                @endif
                            @endcan
                            @can('admin.asociados.creardocumentacionclienteauditoria')
                                @if ($tieneProgramacionatentido)
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="{{ route('admin.asociados.creardocumentacionclienteauditoria', $clienteauditoria) }}" class="btn btn-etapa3 btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="SUBIR INFORMES">
                                        <i class="fas fa-list-alt"></i>
                                        <strong>INFORMES</strong>
                                    </a>
                                </div>
                                @else
                                <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                    <a href="#" class="btn btn-etapa3 btn-icono btn-block disabled" data-toggle="tooltip" data-placement="top" title="PROGRAMAR CLIENTE" aria-disabled="true">
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
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <button type="button" class="btn btn-otros btn-icono btn-block" data-toggle="modal" data-target="#dictamenModal" data-placement="top" title="DICTAMEN">
                                    <i class="fas fa-file-archive"></i>
                                    <strong>DICTAMEN</strong>
                                </button>
                            </div>
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <button type="button" class="btn btn-otros btn-icono btn-block" data-toggle="modal" data-target="#proveedorinformeModal" data-placement="top" title="PROVEEDOR INFORME FINAL">
                                    <i class="fas fa-user-md"></i>
                                    <strong>PROV. INF.</strong>
                                </button>
                            </div>
                            <div class="col-4 mb-3 d-flex justify-content-center align-items-center">
                                <button type="button" class="btn btn-otros btn-icono btn-block" data-toggle="modal" data-target="#historialMedicoModal" data-placement="top" title="HISTORIA MÉDICA">
                                    <i class="fas fa-archive"></i>
                                    <strong>HIST. MED.</strong>
                                </button>
                            </div>
                        </div>
                        <div class="row text-center">
                            @can('admin.cartaspolizas.index')
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.cartasdesgravamen.cartasactdesgravamen', $clienteauditoria) }}" class="btn btn-otros btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="CARTAS DE ACTIVACIÓN DE DESGRAVAMEN">
                                    <i class="fas fa-file"></i>
                                    <strong>CARTAS ACT.</strong>
                                </a>
                            </div>
                            @endcan
                            @can('admin.asociados.editarclienteauditoria')
                            <div class="col-6 mb-3 d-flex justify-content-center align-items-center">
                                <a href="{{ route('admin.asociados.editarclienteauditoria', $clienteauditoria) }}" class="btn btn-otros btn-icono btn-block" data-toggle="tooltip" data-placement="top" title="EDITAR CLIENTE">
                                    <i class="fas fa-edit"></i>
                                    <strong>EDITAR</strong>
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                    @endcan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-no" data-dismiss="modal">CERRAR</button>
                </div>
            </div>
        </div>
    </div>

    <!-- CARTAS -->
    <style>
        .modal-xxl {
            max-width: 90%;
        }
        .truncar2 {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 80px;
        }
    </style>
    <div class="modal fade" id="cartasclientes" tabindex="-1" role="dialog" aria-labelledby="cartasclientesLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <strong style="text-align: center; font-size:20px; margin-top: 20px;">DOCUMENTOS DE {{ $clienteauditoria->nombrecompleto }} </strong>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    {!! Form::model($clienteauditoria, ['route' => ['admin.asociados.guardarcartaclienteauditoria', $clienteauditoria], 'method' => 'POST', 'files' => true]) !!}
                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                    {!! Form::hidden('clienteauditoriaid', $clienteauditoria->id) !!}
                                    {!! Form::hidden('clienteauditorianombre', $clienteauditoria->nombrecompleto) !!}

                                    <strong style="color: #94c93b">SUBIR DOCUMENTOS</strong>
                                    {{-- <div class="form-group">
                                        {!! Form::label('detalle', 'Detalle:') !!}
                                        {!! Form::text('detalle', null, ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
                                        @error('detalle')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('cartaadjunto', 'Seleccionar Carta a Adjuntar:') !!}
                                        <select name="cartaadjunto" class="form-control">
                                            <option value="">Seleccionar Carta...</option>
                                            @foreach ($cartasclientes as $cartascliente)
                                                <option value="{{ $cartascliente->id }}">
                                                    {{ $cartascliente->detalle }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <div class="form-group d-flex align-items-center">
                                        <div style="flex: 1;">
                                            {!! Form::label('detalle', 'Detalle:') !!}
                                            {!! Form::text('detalle', null, ['class' => 'form-control', 'placeholder' => '', 'required']) !!}
                                            @error('detalle')
                                                <small class="text-danger fas fa-exclamation-circle">
                                                    {{ $message }}
                                                </small>
                                            @enderror
                                        </div>
                                        <div style="margin-left: 10px; margin-top: 25px;">
                                            <button type="button" id="btnMostrarCarta" class="btn btn-sm btn-outline-secondary" title="ADJUNTAR A CARTA">
                                                <i class="fas fa-envelope-open"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group" id="grupoCartaAdjunto" style="display: none;">
                                        {!! Form::label('cartaadjunto', 'Carta a Adjuntar:') !!}
                                        <select name="cartaadjunto" class="form-control">
                                            <option value="">Seleccionar Carta...</option>
                                            @foreach ($cartasclientes as $cartascliente)
                                                <option value="{{ $cartascliente->id }}">
                                                    {{ $cartascliente->detalle }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <script>
                                        document.getElementById('btnMostrarCarta').addEventListener('click', function () {
                                            const grupo = document.getElementById('grupoCartaAdjunto');
                                            grupo.style.display = grupo.style.display === 'none' ? 'block' : 'none';
                                        });
                                    </script>
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
                                {!! Form::model($clienteauditoria, ['route' => ['admin.asociados.actualizarBanco', $clienteauditoria], 'method' => 'POST']) !!}
                                <div class="card-body">
                                    <strong style="color: #94c93b">LISTADO DE DOCUMENTOS</strong>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm table-bordered">
                                            <thead>
                                                <tr style="background-color: rgb(255, 255, 255);">
                                                    <th class="text-left">Banco_Adj.</th>
                                                    <th class="text-center">Adj.</th>
                                                    <th class="text-left">ID</th>
                                                    <th class="text-left">Detalle</th>
                                                    <th class="text-left">Fecha</th>
                                                    <th class="text-center">Doc.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cartasclientes as $cartascliente)
                                                    @php
                                                        $adjuntos = DB::table('adjuntoacartas')
                                                            ->where('clienteid', $clienteauditoria->id)
                                                            ->where('idcarta', $cartascliente->id)
                                                            ->orderByRaw('CAST(nroorden AS UNSIGNED)')
                                                            ->get();
                                                    @endphp
                                                    <tr style="background-color: rgb(245, 245, 245);">
                                                        <td class="text-center truncar2">
                                                            @if($cartascliente->bancoadjunto)
                                                                <span title="{{ $cartascliente->bancoadjunto }}">{{ $cartascliente->bancoadjunto }}</span>
                                                            @else
                                                                <input type="checkbox" name="idcarta[]" value="{{ $cartascliente->id }}" class="check-adjunto">
                                                            @endif
                                                        </td>

                                                        <td class="text-center">
                                                            @if($adjuntos->count() > 0)
                                                                <a class="btn btn-sm btn-vercarta2 toggle-adjuntos" data-id="{{ $cartascliente->id }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @else
                                                                <button class="btn btn-sm btn-secondary" disabled>
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td style="text-align: left; padding: 5px;">{{ $cartascliente->id }}</td>
                                                        <td style="text-align: left; padding: 5px;">{{ $cartascliente->detalle }}</td>
                                                        <td style="text-align: left; padding: 5px;">{{ $cartascliente->fecha }}</td>
                                                        <td class="text-center">
                                                            <a href="{{ asset('/cartasclientes/' . $clienteauditoria->id . '/' . $cartascliente->documento) }}" class="btn btn-vercarta" target="_blank" title="VER DOCUMENTO">
                                                                <i class="fas fa-file-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>

                                                    @if($adjuntos->count() > 0)
                                                        <tr class="adjuntos-row" id="adjuntos-{{ $cartascliente->id }}" style="display: none;">
                                                            <td colspan="6">
                                                                <table class="table table-bordered table-sm mb-0">
                                                                    <thead>
                                                                        <tr style="background-color: white">
                                                                            <th class="text-left">ID</th>
                                                                            <th class="text-left">Orden</th>
                                                                            <th class="text-left">Detalle</th>
                                                                            <th class="text-center">Fecha</th>
                                                                            <th class="text-center">Doc.</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($adjuntos as $adj)
                                                                            @php
                                                                                if($adj->tipo == 'HISTORIA MEDICA' || $adj->tipo == 'PROGRAMACIONES'){
                                                                                    $detalle = DB::table('documentacionsubclientes')
                                                                                        ->where('id', $adj->idadjunto)
                                                                                        ->where('clienteauditoriaid', $clienteauditoria->id)
                                                                                        ->first();
                                                                                    $nombre = $detalle->accion ?? 'Sin Acción';
                                                                                    $documento = $detalle->document ?? '';
                                                                                    $fechaMostrar = $detalle ? \Carbon\Carbon::parse($detalle->created_at)->format('Y-m-d') : '';
                                                                                    $idMostrar = $detalle->id ?? '';
                                                                                } elseif($adj->tipo == 'CARTAS'){
                                                                                    $detalle = DB::table('cartasclientes')
                                                                                        ->where('id', $adj->idadjunto)
                                                                                        ->where('clienteid', $clienteauditoria->id)
                                                                                        ->first();
                                                                                    $nombre = $detalle->detalle ?? 'Sin Detalle';
                                                                                    $documento = $detalle->documento ?? '';
                                                                                    $fechaMostrar = $detalle->fecha ?? '';
                                                                                    $idMostrar = $detalle->id ?? '';
                                                                                } elseif($adj->tipo == 'DICTAMEN'){
                                                                                    $detalle = DB::table('dictamenauditoria')
                                                                                        ->where('id', $adj->idadjunto)
                                                                                        ->where('clienteauditoriaid', $clienteauditoria->id)
                                                                                        ->first();
                                                                                    $nrodictamen = $detalle->nrodictamen ?? 'Sin Detalle';
                                                                                    $porcentajeinvalidez = $detalle->porcentajeinvalidez ?? '';
                                                                                    $nrodictamen = $detalle->nrodictamen ?? '';
                                                                                    $documento = $detalle->documento ?? '';
                                                                                    $fechaMostrar = $detalle->fechadictamen ?? '';
                                                                                    $idMostrar = $detalle->id ?? '';
                                                                                } elseif($adj->tipo == 'DOCUMENTACIÓN'){
                                                                                    $idBase = preg_replace('/[^0-9]/', '', $adj->idadjunto);
                                                                                    $sufijo = preg_replace('/[0-9]/', '', $adj->idadjunto);

                                                                                    $detalle = DB::table('requisitosclientesauditorias')
                                                                                        ->where('id', $idBase)
                                                                                        ->where('clienteauditoriaid', $clienteauditoria->id)
                                                                                        ->first();

                                                                                    $nombre = '';
                                                                                    $documento = '';
                                                                                    $fechaMostrar = '';
                                                                                    $idMostrar = '';

                                                                                    if ($detalle) {
                                                                                        if ($sufijo === 'CI' && !empty($detalle->ciasegurado)) {
                                                                                            $nombre = 'CARNET DE IDENTIDAD';
                                                                                            $documento = $detalle->ciasegurado;
                                                                                            $fechaMostrar = \Carbon\Carbon::parse($detalle->updated_at)->format('Y-m-d');
                                                                                            $idMostrar = $detalle->id . 'CI';
                                                                                        } elseif ($sufijo === 'CN' && !empty($detalle->cnacasegurado)) {
                                                                                            $nombre = 'CERTIFICADO DE NACIMIENTO';
                                                                                            $documento = $detalle->cnacasegurado;
                                                                                            $fechaMostrar = \Carbon\Carbon::parse($detalle->updated_at)->format('Y-m-d');
                                                                                            $idMostrar = $detalle->id . 'CN';
                                                                                        }
                                                                                    }
                                                                                } elseif($adj->tipo == 'INFORME FINAL'){
                                                                                    $detalle = DB::table('informesfinales')
                                                                                        ->where('id', $adj->idadjunto)
                                                                                        ->where('clienteauditoriaid', $clienteauditoria->id)
                                                                                        ->first();
                                                                                    $nombre = $detalle->servicio ?? 'Sin Detalle';
                                                                                    $documento = $detalle->document ?? '';
                                                                                    $documento2 = $detalle->documentfirmado ?? '';
                                                                                    $fechaMostrar = \Carbon\Carbon::parse($detalle->created_at)->format('Y-m-d');
                                                                                    $idMostrar = $detalle->id ?? '';
                                                                                }
                                                                            @endphp
                                                                            <tr style="background-color: white;">
                                                                                <td class="text-left">{{ $idMostrar }}</td>
                                                                                <td class="text-left">{{ $adj->nroorden }}</td>
                                                                                <td class="text-left">
                                                                                    @if($adj->tipo == 'DICTAMEN')
                                                                                        DICTAMEN N° {{ $nrodictamen }} - Porcentaje: {{ $porcentajeinvalidez }}
                                                                                    @elseif($adj->tipo == 'INFORME FINAL')
                                                                                        INFORME FINAL DE {{ $nombre ?? 'Sin detalle' }}
                                                                                    @else
                                                                                        {{ $nombre ?? 'Sin detalle' }}
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center">{{ $fechaMostrar }}</td>
                                                                                <td class="text-center">
                                                                                    @if($documento)
                                                                                        @php
                                                                                            if($adj->tipo == 'CARTAS') {
                                                                                                $ruta = '/cartasclientes/' . $clienteauditoria->id . '/';
                                                                                            } elseif($adj->tipo == 'PROGRAMACIONES') {
                                                                                                $ruta = '/documentacionclientesauditoria/' . $clienteauditoria->id . '/';
                                                                                            } elseif($adj->tipo == 'HISTORIA MEDICA') {
                                                                                                $ruta = '/historiamedicaauditoria/' . $clienteauditoria->id . '/extracted/';
                                                                                            } elseif($adj->tipo == 'DICTAMEN') {
                                                                                                $ruta = '/dictamenauditoria/' . $clienteauditoria->id . '/';
                                                                                            } elseif($adj->tipo == 'DOCUMENTACIÓN') {
                                                                                                $ruta = '/requisitosclientesauditoria/' . $clienteauditoria->id . '/';
                                                                                            } elseif($adj->tipo == 'INFORME FINAL') {
                                                                                                $ruta = '/informesfinalesclientesauditoria/' . $clienteauditoria->id . '/';
                                                                                            } else {
                                                                                                $ruta = '';
                                                                                            }
                                                                                        @endphp

                                                                                        @if(!empty($documento) && $documento !== 'PENDIENTE')
                                                                                            <a href="{{ asset($ruta . $documento) }}" 
                                                                                            class="btn btn-sm btn-vercarta" 
                                                                                            target="_blank" 
                                                                                            title="Ver Documento">
                                                                                                <i class="fas fa-file-alt"></i>
                                                                                            </a>
                                                                                        @else
                                                                                            <span class="badge badge-warning">PENDIENTE</span>
                                                                                        @endif

                                                                                        @if($adj->tipo == 'INFORME FINAL' && !empty($documento2))
                                                                                            <a href="{{ asset($ruta . $documento2) }}" 
                                                                                            class="btn btn-sm btn-vercarta" 
                                                                                            target="_blank" 
                                                                                            title="Ver Documento 2">
                                                                                                <i class="fas fa-file-alt"></i>
                                                                                            </a>
                                                                                        @endif
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <label for="">Agrupar a Entidad Financiera:</label>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                                        <select name="banco" id="banco" class="form-control" style="flex: 1;">
                                            <option value="">Seleccionar opción...</option>
                                            @if($banco1) <option value="{{ $banco1 }}">{{ $banco1 }}</option> @endif
                                            @if($banco2) <option value="{{ $banco2 }}">{{ $banco2 }}</option> @endif
                                            @if($banco3) <option value="{{ $banco3 }}">{{ $banco3 }}</option> @endif
                                        </select>

                                        <button type="submit" class="btn btn-sm btn-si">AGRUPAR ENTIDAD</button>
                                    </div>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            document.querySelectorAll('.toggle-adjuntos').forEach(btn => {
                                                btn.addEventListener('click', function() {
                                                    let id = this.getAttribute('data-id');
                                                    let row = document.getElementById('adjuntos-' + id);
                                                    if(row.style.display === 'none' || row.style.display === '') {
                                                        row.style.display = 'table-row';
                                                    } else {
                                                        row.style.display = 'none';
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                </div>
                                
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    {!! Form::model($clienteauditoria, ['route' => ['admin.asociados.adjuntaracartas', $clienteauditoria], 'method' => 'POST', 'files' => true]) !!}
                                    {!! Form::hidden('usuarioregistroid', auth()->user()->id) !!}
                                    {!! Form::hidden('usuarioregistronombre', auth()->user()->name) !!}
                                    {!! Form::hidden('clienteid', $clienteauditoria->id) !!}
                                    {!! Form::hidden('clientenombre', $clienteauditoria->nombrecompleto) !!}
                                    <strong style="color: #94c93b">DOCUMENTOS DEL CLIENTE</strong>
                                    <div class="table-responsive">
                                        <div class="mb-3">
                                            <label>Seleccionar Fecha de Batería:</label>
                                            <select id="filtroFecha" class="form-control">
                                                <option value="">-- Seleccione --</option>
                                                @foreach($fechas as $f)
                                                    <option value="{{ $f->fechabateria }}">{{ $f->fechabateria }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <table class="table table-striped table-sm table-bordered" id="tablaInformes">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 10%;">Selec.</th>
                                                    <th class="text-left" style="width: 10%;">ID</th>
                                                    <th class="text-left" style="width: 70%;">Detalle</th>
                                                    <th class="text-center" style="width: 10%;">Doc.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($historiaMedica)
                                                    <tr data-fecha="">
                                                        <td class="text-center">
                                                            <input type="checkbox" name="seleccionados[]" value="{{ $historiaMedica->id }}">
                                                            <input type="hidden" name="tipos[{{ $historiaMedica->id }}]" value="HISTORIA MEDICA">
                                                            <input type="hidden" name="ordenes[{{ $historiaMedica->id }}]" class="orden-input">
                                                        </td>
                                                        <td class="text-left">{{ $historiaMedica->id }}</td>
                                                        <td class="text-left">{{ $historiaMedica->accion }}</td>
                                                        <td class="text-center">
                                                            <a href="{{ asset('/historiamedicaauditoria/' . $clienteauditoria->id . '/extracted/' . $historiaMedica->document) }}"
                                                            class="btn btn-vercarta btn-sm" target="_blank" title="Ver Documento">
                                                                <i class="fas fa-paste"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endif
                                                 @foreach ($informes as $informe)
                                                    <tr data-fecha="{{ $informe->fechabateria }}" style="display: none;">
                                                        <td class="text-center">
                                                            <input type="checkbox" name="seleccionados[]" value="{{ $informe->id }}">
                                                            <input type="hidden" name="tipos[{{ $informe->id }}]" value="PROGRAMACIONES">
                                                            <input type="hidden" name="ordenes[{{ $informe->id }}]" class="orden-input">
                                                        </td>
                                                        <td class="text-left">{{ $informe->id }}</td>
                                                        <td class="text-left">{{ $informe->accion }}</td>
                                                        <td class="text-center">
                                                            @if(!empty($informe->document))
                                                                <a href="{{ asset('/documentacionclientesauditoria/' . $clienteauditoria->id . '/' . $informe->document) }}"
                                                                    class="btn btn-vercarta btn-sm" target="_blank" title="Ver Documento">
                                                                    <i class="fas fa-paste"></i>
                                                                </a>
                                                            @endif
                                                            @if(!empty($informe->image))
                                                                <a href="{{ asset('/documentacionclientesauditoria/' . $clienteauditoria->id . '/' . $informe->image) }}"
                                                                    class="btn btn-vercarta2 btn-sm" target="_blank" title="Ver Imagen 1">
                                                                    <i class="fas fa-image"></i>
                                                                </a>
                                                            @endif
                                                            @if(!empty($informe->image2))
                                                                <a href="{{ asset('/documentacionclientesauditoria/' . $clienteauditoria->id . '/' . $informe->image2) }}"
                                                                    class="btn btn-vercarta2 btn-sm" target="_blank" title="Ver Imagen 2">
                                                                    <i class="fas fa-image"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function () {
                                                let ordenSeleccion = [];
                                                document.getElementById('filtroFecha').addEventListener('change', function() {
                                                    let fechaSeleccionada = this.value;
                                                    document.querySelectorAll('#tablaInformes tbody tr').forEach(fila => {
                                                        let fechaFila = fila.getAttribute('data-fecha');
                                                        if (fechaSeleccionada === "" || fechaFila === fechaSeleccionada || fechaFila === "") {
                                                            fila.style.display = "";
                                                        } else {
                                                            fila.style.display = "none";
                                                        }
                                                    });
                                                });
                                                document.querySelectorAll('input[type="checkbox"][name="seleccionados[]"]').forEach(chk => {
                                                    chk.addEventListener('change', function () {
                                                        let id = this.value;
                                                        if (this.checked) {
                                                            ordenSeleccion.push(id);
                                                        } else {
                                                            ordenSeleccion = ordenSeleccion.filter(item => item !== id);
                                                        }
                                                        document.querySelectorAll('input[type="checkbox"][name="seleccionados[]"]').forEach(chk2 => {
                                                            let numSpan = chk2.parentElement.querySelector('.check-order');
                                                            if (!numSpan) {
                                                                numSpan = document.createElement('span');
                                                                numSpan.classList.add('check-order');
                                                                chk2.parentElement.appendChild(numSpan);
                                                            }
                                                            let pos = ordenSeleccion.indexOf(chk2.value);
                                                            numSpan.textContent = pos >= 0 ? (pos + 1) : '';

                                                            let ordenInput = chk2.parentElement.querySelector('.orden-input');
                                                            if (ordenInput) {
                                                                ordenInput.value = pos >= 0 ? (pos + 1) : '';
                                                            }
                                                        });
                                                    });
                                                });
                                            });
                                        </script>

                                        <div class="row" style="margin-top: 30px;">
                                            <div class="col-lg-3">
                                                <label>Cartas Guardadas:</label>
                                                <button class="btn btn-outline-secondary btn-sm mb-2" type="button" data-toggle="collapse" data-target="#tablaCartas" aria-expanded="false" aria-controls="tablaCartas">
                                                    Ver Cartas
                                                </button>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Dictamen Guardado:</label>
                                                <button class="btn btn-outline-secondary btn-sm mb-2" type="button" data-toggle="collapse" data-target="#tablaDictamen" aria-expanded="false" aria-controls="tablaDictamen">
                                                    Ver Dictamen
                                                </button>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Doc. Guardado:</label>
                                                <button class="btn btn-outline-secondary btn-sm mb-2" type="button" data-toggle="collapse" data-target="#tablaDocumentacion" aria-expanded="false" aria-controls="tablaDocumentacion">
                                                    Ver Documentación
                                                </button>
                                            </div>
                                            <div class="col-lg-3">
                                                <label>Info. Final Guardado:</label>
                                                <button class="btn btn-outline-secondary btn-sm mb-2" type="button" data-toggle="collapse" data-target="#tablaInfofinal" aria-expanded="false" aria-controls="tablaInfofinal">
                                                    Ver Info. Final
                                                </button>
                                            </div>
                                        </div>

                                        <div class="collapse" id="tablaCartas">
                                            <label>Listado de Cartas</label>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 10%;">Selec.</th>
                                                            <th class="text-left" style="width: 10%;">ID</th>
                                                            <th class="text-left" style="width: 70%;">Detalle</th>
                                                            <th class="text-center" style="width: 10%;">Doc.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($cartasclientes as $cartascliente)
                                                            <tr>
                                                                <td class="text-center">
                                                                    <input type="checkbox" name="seleccionados[]" value="{{ $cartascliente->id }}">
                                                                    <input type="hidden" name="tipos[{{ $cartascliente->id }}]" value="CARTAS">
                                                                    <input type="hidden" name="ordenes[{{ $cartascliente->id }}]" class="orden-input">
                                                                </td>
                                                                <td style="text-align: left; padding: 5px;">{{ $cartascliente->id }}</td>
                                                                <td style="text-align: left; padding: 5px;">{{ $cartascliente->detalle }}</td>
                                                                <td class="text-center">
                                                                    <a href="{{ asset('/cartasclientes/' . $clienteauditoria->id . '/' . $cartascliente->documento) }}"
                                                                    class="btn btn-vercarta" target="_blank" title="VER DOCUMENTO">
                                                                        <i class="fas fa-file-alt"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="collapse" id="tablaDictamen">
                                            <label>Listado de Dictamen</label>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 10%;">Selec.</th>
                                                            <th class="text-left" style="width: 10%;">ID</th>
                                                            <th class="text-left" style="width: 30%;">Detalle</th>
                                                            <th class="text-left" style="width: 20%;">Fecha Dictamen</th>
                                                            <th class="text-left" style="width: 20%;">Porcentaje</th>
                                                            <th class="text-center" style="width: 10%;">Doc.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($dictamenauditorias as $dictamen)
                                                            <tr>
                                                                <td class="text-center">
                                                                    <input type="checkbox" name="seleccionados[]" value="{{ $dictamen->id }}">
                                                                    <input type="hidden" name="tipos[{{ $dictamen->id }}]" value="DICTAMEN">
                                                                    <input type="hidden" name="ordenes[{{ $dictamen->id }}]" class="orden-input">
                                                                </td>
                                                                <td style="text-align: left; padding: 5px;">{{ $dictamen->id }}</td>
                                                                <td style="text-align: left; padding: 5px;">DICTAMEN {{ $dictamen->nrodictamen }}</td>
                                                                <td style="text-align: left; padding: 5px;">{{ $dictamen->fechadictamen }}</td>
                                                                <td style="text-align: left; padding: 5px;">{{ $dictamen->porcentajeinvalidez }}</td>
                                                                <td class="text-center">
                                                                    <a href="{{ asset('/dictamenauditoria/' . $clienteauditoria->id . '/' . $dictamen->documento) }}"
                                                                    class="btn btn-vercarta" target="_blank" title="VER DICTAMEN">
                                                                        <i class="fas fa-file-alt"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="collapse" id="tablaDocumentacion">
                                            <label>Listado de Documentos</label>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 10%;">Selec.</th>
                                                            <th class="text-left" style="width: 10%;">ID</th>
                                                            <th class="text-left" style="width: 70%;">Detalle</th>
                                                            <th class="text-center" style="width: 10%;">Doc.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($documentacionauci as $doc)
                                                            <tr>
                                                                <td class="text-center">
                                                                    <input type="checkbox" name="seleccionados[]" value="{{ $doc->id }}CI">
                                                                    <input type="hidden" name="tipos[{{ $doc->id }}CI]" value="DOCUMENTACIÓN">
                                                                    <input type="hidden" name="ordenes[{{ $doc->id }}CI]" class="orden-input">
                                                                </td>
                                                                <td style="text-align: left; padding: 5px;">{{ $doc->id }}CI</td>
                                                                <td style="text-align: left; padding: 5px;">CARNET DE IDENTIDAD</td>
                                                                <td class="text-center">
                                                                    @if(!empty($doc->ciasegurado) && $doc->ciasegurado !== 'PENDIENTE')
                                                                        <a href="{{ asset('/requisitosclientesauditoria/' . $clienteauditoria->id . '/' . $doc->ciasegurado) }}"
                                                                        class="btn btn-vercarta" target="_blank" title="VER DOCUMENTACIÓN">
                                                                            <i class="fas fa-file-alt"></i>
                                                                        </a>
                                                                    @else
                                                                        <span class="badge badge-warning">PENDIENTE</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        @foreach ($documentacionaunac as $doc)
                                                            <tr>
                                                                <td class="text-center">
                                                                    <input type="checkbox" name="seleccionados[]" value="{{ $doc->id }}CN">
                                                                    <input type="hidden" name="tipos[{{ $doc->id }}CN]" value="DOCUMENTACIÓN">
                                                                    <input type="hidden" name="ordenes[{{ $doc->id }}CN]" class="orden-input">
                                                                </td>
                                                                <td style="text-align: left; padding: 5px;">{{ $doc->id }}CN</td>
                                                                <td style="text-align: left; padding: 5px;">CERTIFICADO DE NACIMIENTO</td>
                                                                <td class="text-center">
                                                                    @if(!empty($doc->cnacasegurado) && $doc->cnacasegurado !== 'PENDIENTE')
                                                                        <a href="{{ asset('/requisitosclientesauditoria/' . $clienteauditoria->id . '/' . $doc->cnacasegurado) }}"
                                                                        class="btn btn-vercarta" target="_blank" title="VER DOCUMENTACIÓN">
                                                                            <i class="fas fa-file-alt"></i>
                                                                        </a>
                                                                    @else
                                                                        <span class="badge badge-warning">PENDIENTE</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="collapse" id="tablaInfofinal">
                                            <label>Listado de Informes Finales</label>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 10%;">Selec.</th>
                                                            <th class="text-left" style="width: 10%;">ID</th>
                                                            <th class="text-left" style="width: 70%;">Detalle</th>
                                                            <th class="text-center" style="width: 10%;">Doc.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($informefinalau as $infofinal)
                                                            <tr>
                                                                <td class="text-center">
                                                                    <input type="checkbox" name="seleccionados[]" value="{{ $infofinal->id }}">
                                                                    <input type="hidden" name="tipos[{{ $infofinal->id }}]" value="INFORME FINAL">
                                                                    <input type="hidden" name="ordenes[{{ $infofinal->id }}]" class="orden-input">
                                                                </td>
                                                                <td style="text-align: left; padding: 5px;">{{ $infofinal->id }}</td>
                                                                <td style="text-align: left; padding: 5px;">INFORME FINAL DE {{ $infofinal->servicio }}</td>
                                                                <td class="text-center">
                                                                    @if($infofinal->document)
                                                                        <a href="{{ asset('/informesfinalesclientesauditoria/' . $clienteauditoria->id . '/' . $infofinal->document) }}"
                                                                        class="btn btn-vercarta" target="_blank" title="VER INFORME FINAL">
                                                                            <i class="fas fa-file-alt"></i>
                                                                        </a>
                                                                    @endif
                                                                    @if($infofinal->documentfirmado)
                                                                        <a href="{{ asset('/informesfinalesclientesauditoria/' . $clienteauditoria->id . '/' . $infofinal->documentfirmado) }}"
                                                                        class="btn btn-vercarta" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                                            <i class="fas fa-file-alt"></i>
                                                                        </a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="mb-3" style="margin-top: 20px;">
                                            <label>Carta a Adjuntar:</label>
                                            <select name="detalle" class="form-control" required>
                                                <option value="">Seleccionar Carta...</option>
                                                @foreach ($cartasclientes as $cartascliente)
                                                    <option value="{{ $cartascliente->id }}">
                                                        {{ $cartascliente->detalle }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-sm btn-si">ADJUNTAR</button>
                                    </div>
                                    {!! Form::close() !!}
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
                    <h3>{{$clienteauditoria->nombrecompleto}}</h3>
                </div>
                {!! Form::model($clienteauditoria, ['route' => ['admin.asociados.guardardictamenauditoria', $clienteauditoria], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                {!! Form::hidden('usuarioregistroid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistronombre', auth()->user()->name) !!}
                {!! Form::hidden('clienteauditoriaid', $clienteauditoria->id) !!}
                {!! Form::hidden('clienteauditorianombre', $clienteauditoria->nombrecompleto) !!}
            
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
                                    <div class="mb-3">
                                        {!! Form::label('documento', 'Documento:', ['class' => 'form-label']) !!}
                                        <input type="file" name="documento" id="documento" accept=".pdf" class="form-control" required/>
                                        @error('documento')
                                            <div class="text-danger">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{$message}}
                                            </div>
                                        @enderror
                                    </div>
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
                                            @foreach ($dictamenauditorias as $dictamenauditoria)
                                                <tr>
                                                    <td style="text-align: left">{{ $dictamenauditoria->id }}</td>
                                                    <td style="text-align: left">{{ $dictamenauditoria->nrodictamen }}</td>
                                                    <td style="text-align: left">{{ $dictamenauditoria->fechadictamen }}</td>
                                                    <td style="text-align: left">{{ $dictamenauditoria->porcentajeinvalidez }}</td>
                                                    <td style="text-align: left">
                                                        @if($dictamenauditoria->documento)
                                                            <a href="{{ asset('dictamenauditoria/' . $dictamenauditoria->clienteauditoriaid . '/' . $dictamenauditoria->documento) }}" 
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
                <h3>{{$clienteauditoria->nombrecompleto}}</h3>
                </div>
                {!! Form::model($clienteauditoria, ['route' => ['admin.asociados.guardarproveedorinformefinalauditoria', $clienteauditoria], 'method' => 'POST']) !!}
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
                                    // Obtén los datos de trámites desde el atributo data-tramites
                                    const tramitesPorFecha = JSON.parse(selectFechas.getAttribute('data-tramites'));

                                    // Escucha cambios en el select
                                    selectFechas.addEventListener('change', function () {
                                        const fechaSeleccionada = this.value;

                                        // Busca el trámite correspondiente a la fecha seleccionada
                                        if (tramitesPorFecha[fechaSeleccionada]) {
                                            tramiteInput.value = tramitesPorFecha[fechaSeleccionada];
                                        } else {
                                            tramiteInput.value = ''; // Limpia el campo si no hay trámite
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

                                // Variables para verificar registros del cliente
                                const clienteConInvalidez = selectFechas.getAttribute('data-cliente-con-invalidez') === 'true';
                                const clienteConApelacionOSegunda = selectFechas.getAttribute('data-cliente-con-apelacion-segunda') === 'true';

                                // Escucha cambios en el select Fecha Batería
                                selectFechas.addEventListener('change', function () {
                                    const fechaSeleccionada = this.value;

                                    // Simulación de lógica para obtener el trámite basado en la fecha seleccionada
                                    const tramitesPorFecha = JSON.parse(this.getAttribute('data-tramites'));
                                    if (tramitesPorFecha[fechaSeleccionada]) {
                                        const tramiteAutorellenado = tramitesPorFecha[fechaSeleccionada];
                                        tramiteInput.value = tramiteAutorellenado;

                                        // Autorellena el precio según el trámite
                                        if (tramiteAutorellenado === 'AUDITORIA MEDICA' || tramiteAutorellenado === 'INVALIDEZ') {
                                            precioInput.value = '2100.00'; // "AUDITORIA MEDICA" y "INVALIDEZ" siempre son 2100.00
                                        } else if (tramiteAutorellenado === 'APELACION' || tramiteAutorellenado === 'SEGUNDA SOLICITUD') {
                                            // Si el cliente tiene "INVALIDEZ"
                                            if (clienteConInvalidez) {
                                                precioInput.value = '1100.00';
                                            } else {
                                                precioInput.value = '2100.00'; // Si no tiene "INVALIDEZ"
                                            }
                                        } else {
                                            precioInput.value = ''; // Limpia si no coincide
                                        }
                                    } else {
                                        tramiteInput.value = '';
                                        precioInput.value = ''; // Limpia si no hay trámite asociado
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
                    <h3>{{$clienteauditoria->nombrecompleto}}</h3>
                </div>
                {!! Form::model($clienteauditoria, ['route' => ['admin.asociados.guardarhistoriamedicaauditoria', $clienteauditoria], 'method' => 'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}

                @if($documentacion)
                {{-- <div class="modal-body text-center">
                    <!-- Visor PDF -->
                    <iframe class="pdf-preview" src="{{ route('ver.documentoauditoria', $documentacion->id) }}" type="application/pdf"></iframe>

                    <a href="{{ route('ver.documentoauditoria', $documentacion->id) }}" class="btn btn-verhistoriamedica" target="_blank">
                        <strong>VER HIST. MED.</strong>
                    </a>
                </div> --}}

                <div class="modal-body text-center">
                    <!-- Vista previa del documento -->
                    <div class="pdf-preview-container mb-3">
                        <iframe 
                            src="{{ asset('/historiamedicaauditoria/' . $clienteauditoria->id . '/extracted/' . $historiamedicaclienteauditoria) }}" 
                            width="100%" 
                            height="400px" 
                            frameborder="0" 
                            style="border: 1px solid #ccc;">
                        </iframe>
                    </div><br>

                    <!-- Botón para ver el documento completo -->
                    <a href="{{ asset('/historiamedicaauditoria/' . $clienteauditoria->id . '/extracted/' . $historiamedicaclienteauditoria) }}" 
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
$('.dropify').dropify();
</script>
@stop

@section('css')
<style>
    .check-order {
        font-weight: bold;
        margin-left: 5px;
        color: #007bff;
    }
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
    .btn-vercarta2 {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-vercarta2:hover {
        background-color: #faa625;
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
    .btn-cartas {
        background-color: #ffffff;
        color: #9c3bc9;
        border-color: #9c3bc9;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cartas:hover {
        background-color: #9c3bc9;
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
    .btn-etapa1 {
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
    .btn-etapa1:hover {
        background-color: #26a1c0;
        color: #ffffff;
    }
    .btn-icono i {
        font-size: 4em;
    }
    .btn-otros {
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
    .btn-otros:hover {
        background-color: #e05f28;
        color: #ffffff;
    }
    .btn-otros i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-etapa2 {
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
    .btn-etapa2:hover {
        background-color: #148734;
        color: #ffffff;
    }
    .btn-etapa2 i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-etapa3 {
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
    .btn-etapa3:hover {
        background-color: #aeae2b;
        color: #ffffff;
    }
    .btn-etapa3 i {
        display: inline-block;
        vertical-align: middle;
    }
    .btn-tercerasolicitud {
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
    .btn-tercerasolicitud:hover {
        background-color: #ac2bae;
        color: #ffffff;
    }
    .btn-tercerasolicitud i {
        display: inline-block;
        vertical-align: middle;
    }
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
        height: 430px;
        border: none;
    }
    .titulo {
        margin-top: 50px;
        margin-left: 20px;
    }
    .modal-custom-height .modal-dialog {
        height: 93.5vh;
    }
    .modal-custom-height .modal-content {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .modal-custom-height .modal-body {
        overflow-y: auto;
        flex: 1;
        padding: 2rem;
    }
    .modal-footer {
        justify-content: center;
    }
    .dropify-wrapper {
        border-radius: 0.25rem;
    }
</style>
@stop
