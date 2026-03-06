@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.tramites.index') }}">REGRESAR</a>
{{-- @if($inicioocontinuidad)
    <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalcomunicaciones">COMUNICACIÓN</a>
    <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalsolicitudes">HISTORIAL DE MISIVAS</a>
    <a class="btn btn-sm float-right btn-cartareclamo" href="{{ route('admin.tramites.cartasprocjubilacion', $cliente->id) }}">NUEVA MISIVA</a>
    <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalDatos">DATOS CLIENTE</a>
    <a class="btn btn-sm float-right btn-cancelacion" data-toggle="modal" data-target="#modalCancelacion">CANCELACIÓN</a>
    <a class="btn btn-sm float-right btn-cancelacion" data-toggle="modal" data-target="#modalNotifErroneas">NOTIF. ERRÓNEAS</a>
@endif
<a class="btn btn-sm float-right btn-seguimiento" data-toggle="modal" data-target="#modalCodigo">CÓD. PERMISO</a> --}}

@if($inicioocontinuidad)
    <div class="dropdown float-right ml-2">
        <button class="btn btn-sm btn-cartareclamo dropdown-toggle shadow-sm"
                type="button"
                id="dropdownAcciones"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
            <i class="fas fa-cogs mr-1"></i> ACCIONES
        </button>
        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
            aria-labelledby="dropdownAcciones"
            style="min-width: 300px;">

            <a class="dropdown-item" data-toggle="modal" data-target="#modalDatos">
                <i class="fas fa-user mr-2 text-orange"></i> DATOS DEL CLIENTE
            </a>
            <a class="dropdown-item" data-toggle="modal" data-target="#modalcomunicaciones">
                <i class="fas fa-envelope mr-2 text-orange"></i> COMUNICACIONES
            </a>
            <a class="dropdown-item" href="{{ route('admin.tramites.cartasprocjubilacion', $cliente->id) }}">
                <i class="fas fa-plus-circle mr-2 text-orange"></i> NUEVA MISIVA
            </a>
            <a class="dropdown-item" data-toggle="modal" data-target="#modalsolicitudes">
                <i class="fas fa-history mr-2 text-orange"></i> HISTORIAL DE MISIVAS
            </a>
            <a class="dropdown-item" data-toggle="modal" data-target="#modalNotifErroneas">
                <i class="fas fa-exclamation-triangle mr-2 text-orange"></i> NOTIFICACIONES ERRÓNEAS
            </a>
            {{-- NUEVO 130226 --}}
            <a class="dropdown-item" data-toggle="modal" data-target="#modalNotifObservadas">
                <i class="fas fa-eye mr-2 text-orange"></i> NOTIFICACIONES OBSERVADAS
            </a>
            <a class="dropdown-item" data-toggle="modal" data-target="#modalCancelacion">
                <i class="fas fa-times-circle mr-2 text-orange"></i> CANCELACIÓN DE TRÁMITE
            </a>
            <a class="dropdown-item" data-toggle="modal" data-target="#modalCodigo">
                <i class="fas fa-key mr-2 text-orange"></i> CÓDIGOS DE PERMISO
            </a>
        </div>
    </div>
    <style>
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 18px;
            font-size: 15px;
            transition: all 0.2s ease;
            cursor: pointer !important;
        }
        .dropdown-item:hover {
            background-color: rgba(255, 140, 0, 0.08);
            transform: translateX(5px);
        }
    </style>
@endif

<div class="modal fade" id="modalCodigo" tabindex="-1" role="dialog" aria-labelledby="modalCodigoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formCodigo">
                <div class="modal-header">
                    <h5 class="modal-title titulomodal" id="modalCodigoLabel">INGRESAR CÓDIGO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" id="codigoInput" name="codigo" class="form-control" placeholder="Ingrese el código" required>
                    <div id="codigoMensaje" class="mt-2 text-danger" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3">VALIDAR</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('formCodigo').addEventListener('submit', function(e) {
        e.preventDefault();
        const codigo = document.getElementById('codigoInput').value.trim();
        const mensaje = document.getElementById('codigoMensaje');
        mensaje.style.display = 'none';
        fetch('{{ route("permisoscodigo.cambiofechaarchivoprestaciones") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ codigo: codigo })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                $('#modalCodigo').modal('hide');
                alert('Código validado correctamente');
                location.reload();
            } else {
                mensaje.textContent = data.message;
                mensaje.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mensaje.textContent = 'Ocurrió un error al procesar la solicitud.';
            mensaje.style.display = 'block';
        });
    });
</script>

<div class="modal fade" id="modalDatos" tabindex="-1" role="dialog" aria-labelledby="modalDatosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalDatosLabel">DATOS DEL CLIENTE</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::model($cliente, ['route' => ['admin.tramites.actualizardatoscliente', $cliente], 'method' => 'PUT']) !!}
                {!! Form::hidden('users_id', auth()->user()->id) !!}
                    <div class="row">
                        <div class="form-group col-lg-12">
                            {!! Form::label('estadolaboral', 'Estado laboral:') !!}
                            {!! Form::select('estadolaboral', $estlab, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                        <div class="form-group col-lg-12">
                            {!! Form::label('aseguradora', 'Aseguradora:') !!}
                            {!! Form::select('aseguradora', $aseguradoras, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                        <div class="form-group col-lg-12">
                            {!! Form::label('afp', 'Gestora/Afp:') !!}
                            {!! Form::text('afp', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                        <div class="form-group col-lg-12">
                            {!! Form::label('matricula', 'Matricula:') !!}
                            {!! Form::text('matricula', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                        <div class="form-group col-lg-12">
                            {!! Form::label('nuacua', 'NUA/CUA:') !!}
                            {!! Form::text('nuacua', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        </div>
                    </div>
                {!! Form::submit('ACTUALIZAR DATOS', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<h5>PROCEDIMIENTO DE JUBILACIÓN DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
{{-- <link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}?v={{ filemtime(public_path('css/tramitesgestora.css')) }}"> --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.scroll-shadow-wrapper').forEach(wrapper => {
            const container = wrapper.querySelector('.scroll-shadow-container');
            container.addEventListener('scroll', function () {
                /* if (container.scrollLeft + container.clientWidth >= container.scrollWidth - 1) {
                    wrapper.classList.add('scrolled-right');
                } else {
                    wrapper.classList.remove('scrolled-right');
                } */
                if (Math.ceil(container.scrollLeft + container.clientWidth) >= container.scrollWidth - 2) {
                    wrapper.classList.add('scrolled-right');
                } else {
                    wrapper.classList.remove('scrolled-right');
                }

            });
        });
    });
</script>
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
    {{-- ELECCION DE INICIO O CONTINUIDAD DE TRAMITE --}}
    @if(!$inicioocontinuidad)
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    <form id="formTramite" action="{{ route('admin.tramites.guardariniciotramiteclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                        {!! Form::hidden('clienteid', $cliente->id) !!}
                        {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                        {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                        {!! Form::hidden('tramite', 'JUBILACIÓN') !!}
                        {!! Form::hidden('idtramite', $idTramite) !!}
                        {!! Form::hidden('fechasubida', now()) !!}
                        {!! Form::hidden('nivelprocedimiento', '', ['id' => 'nivelprocedimiento']) !!}
                        @csrf
                        <h5 style="text-align: center; font-size: 27px; margin-bottom:30px; margin-top:20px; font-weight: 700;">ELIGE UNA OPCIÓN</h5>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                                <button type="button" class="btn btn-custom" style="width: 50%;" onclick="confirmarTramite('INICIO DE TRÁMITE')">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-file-signature fa-5x mb-2"></i>
                                        <span class="h6 mb-0">INICIO DE TRAMITE</span>
                                    </div>
                                </button>
                            </div>
                            <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                                <button type="button"
                                    class="btn btn-custom"
                                    style="width: 50%;"
                                    onclick="confirmarTramite('CONTINUIDAD DE TRÁMITE')">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-sync-alt fa-5x mb-2"></i>
                                        <span class="h6 mb-0">CONTINUIDAD DE TRÁMITE</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarTramite(nivel) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `¿DESEAS REGISTRAR ${nivel}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#94c93b',
                cancelButtonColor: '#faa625',
                confirmButtonText: 'SI, CONTINUAR',
                cancelButtonText: 'CANCELAR'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('nivelprocedimiento').value = nivel;
                    document.getElementById('formTramite').submit();
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    title: '¡Guardado!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#94c93b',
                    confirmButtonText: 'ACEPTAR'
                });
            @endif
        });
    </script>
    
    @if($inicioocontinuidad)
        {{-- NIVELES DE PROCEDIMIENTO --}}
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">INICIO DE TRÁMITE</a>
                </li>
                @php
                    $documento5 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')
                        ->where('nivelprocedimiento', 'FIRMA EAP')
                        ->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')
                        ->where('estadocomunicado', 'COMUNICADO')
                        ->where(function ($query) {
                            $query->whereNotNull('capturacomunicacion')
                                ->where(function ($subQuery) {
                                    $subQuery->whereNotNull('capturacomunicacion');
                                });
                        })
                    ->first();
                    $documento3 = $cliente->tramites()->where('subprocedimiento', 'FIRMA EAP')
                        ->where('tramite', 'JUBILACIÓN')
                        ->where('estadocomunicado', 'COMUNICADO')
                        ->where(function ($query) {
                            $query->whereNotNull('capturacomunicacion')
                                ->where(function ($subQuery) {
                                    $subQuery->whereNotNull('capturacomunicacion');
                                });
                        })
                    ->first();
                    $estadotramite = $cliente->tramites()->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
                        ->where('subprocedimiento', 'INCLUSIÓN DE PODER')
                        ->where('tramite', 'JUBILACIÓN')
                        ->whereIn('estadotramite', ['INGRESO DE TRÁMITE', 'FIRMA EAP'])
                    ->first();
                @endphp
                <li class="nav-item">
                    <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="false">RESULTADO DEL PROCESO</a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                {{-- 1.- INICIO DE TRÁMITE / ESTRUCTURA DE VISTA--}}
                <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    @if($tramiteinicio)
                        <div class="row">
                            {{-- DATOS DEL AFILIADO --}}
                            <div class="col-12 col-md-3 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDatosAfiliado">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-address-card fa-5x mb-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">DATOS DEL AFILIADO</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $datosAfiliado = $subtramites->where('tipo', 'DATOS DE AFILIADO')->where('tramite', 'JUBILACIÓN')->where('clienteid', $cliente->id)->first();
                                @endphp
                                <div class="text-center"> 
                                    @if (!$datosAfiliado)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @elseif ($datosAfiliado)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> REGISTRADO
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- INGRESO DE TRAMITE --}}
                            <div class="col-12 col-md-3 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalIngresoTramite">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-folder-plus fa-5x mb-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">INGRESO DE TRÁMITE</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                                    $documento2 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'INCLUSIÓN DE PODER')->first();
                                @endphp
                                <div class="text-center"> 
                                    @if (!$documento1 && !$documento2)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @elseif ($documento1 || $documento2)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> COMPLETO
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- NOTIFICACION DEL PODER --}}
                            @php
                                $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECEPCION DE TRÁMITE')->where('estadocomunicado', 'COMUNICADO')->first();
                                $documento2 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'INCLUSION DE PODER')->where('estadocomunicado', 'COMUNICADO')->first();
                            @endphp
                            <div class="col-12 col-md-3 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalNotificacionPoder">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-envelope-open-text fa-5x mb-2"></i>
                                        <span class="h6 mb-0">NOTIFICACIÓN DEL PODER</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento3 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->whereIn('subprocedimiento', ['VALIDACIÓN DE PODER', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS'])->first();
                                @endphp
                                <div class="text-center">
                                    @if (!$documento3)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @else
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> COMPLETO
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- FIRMA EAP --}}
                            @php
                                $documento3 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->whereIn('subprocedimiento', ['VALIDACIÓN DE PODER', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS'])->where('estadocomunicado', 'COMUNICADO')->first();
                            @endphp
                            <div class="col-12 col-md-3 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalFirmaEAP" @if (!$documento3) disabled @endif>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-signature fa-5x mb-2"></i>
                                        <span class="h6 mb-0">FIRMA EAP</span>
                                        @php
                                            $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                                        @endphp
                                        @if (!$documento1)
                                            @if ($documento3)
                                                @php
                                                    $fechaSubidaEAP = \Carbon\Carbon::parse($documento3->fechasubida);
                                                    $diasRestantesEAP = max(0, 10 - $fechaSubidaEAP->diffInDays(\Carbon\Carbon::now()));
                                                    $mensajeDias = $diasRestantesEAP == 1 ? '1 DIA RESTANTE' : "$diasRestantesEAP DIAS RESTANTES";
                                                @endphp
                                                {{-- <span class="badge badge-orange mt-2">{{ $mensajeDias }}</span> --}}
                                            @endif
                                        @endif
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento5 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                                @endphp
                                <div class="text-center">
                                    @if (!$documento1)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @else
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> COMPLETO
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($tramitecontinuidad)
                        <div class="row">
                            {{-- DATOS DEL AFILIADO --}}
                            <div class="col-12 col-md-3 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDatosAfiliado">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-address-card fa-5x mb-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">DATOS DEL AFILIADO</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $datosAfiliado = $subtramites->where('tipo', 'DATOS DE AFILIADO')->where('tramite', 'JUBILACIÓN')->where('clienteid', $cliente->id)->first();
                                @endphp
                                <div class="text-center"> 
                                    @if (!$datosAfiliado)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @elseif ($datosAfiliado)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> REGISTRADO
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- INGRESO DE TRAMITE --}}
                            <div class="col-12 col-md-3 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalIngresoTramite">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-folder-plus fa-5x mb-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">INGRESO DE PODER</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                                    $documento2 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'INCLUSIÓN DE PODER')->first();
                                @endphp
                                <div class="text-center"> 
                                    @if (!$documento1 && !$documento2)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @elseif ($documento1 || $documento2)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> COMPLETO
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- NOTIFICACION DEL PODER --}}
                            @php
                                $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECEPCION DE TRÁMITE')->where('estadocomunicado', 'COMUNICADO')->first();
                                $documento2 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'INCLUSION DE PODER')->where('estadocomunicado', 'COMUNICADO')->first();
                            @endphp
                            <div class="col-12 col-md-3 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalNotificacionPoder" @if ($cliente->paisnacimiento !== 'BOLIVIA')
                                @elseif (!$documento1 && !$documento2)
                                    disabled
                                @endif>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-envelope-open-text fa-5x mb-2"></i>
                                        <span class="h6 mb-0">NOTIFICACIÓN DEL PODER</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento3 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'VALIDACIÓN DE PODER')->first();
                                @endphp
                                <div class="text-center">
                                    @if (!$documento3)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @else
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> COMPLETO
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- FIRMA EAP --}}
                            @php
                                $documento3 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'VALIDACIÓN DE PODER')->where('estadocomunicado', 'COMUNICADO')->first();
                                $estadotramite = $cliente->tramites()->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')->where('subprocedimiento', 'INCLUSIÓN DE PODER')->where('tramite', 'JUBILACIÓN')->whereIn('estadotramite', ['INGRESO DE TRÁMITE', 'FIRMA EAP'])->first();
                            @endphp
                            <div class="col-12 col-md-3 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalFirmaEAP" @if (!$documento3 || !$estadotramite) disabled @endif>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-signature fa-5x mb-2"></i>
                                        <span class="h6 mb-0">FIRMA EAP</span>
                                        @php
                                            $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                                        @endphp
                                        @if (!$documento1)
                                            @if ($documento3)
                                                @php
                                                    $fechaSubidaEAP = \Carbon\Carbon::parse($documento3->fechasubida);
                                                    $diasRestantesEAP = max(0, 10 - $fechaSubidaEAP->diffInDays(\Carbon\Carbon::now()));
                                                    $mensajeDias = $diasRestantesEAP == 1 ? '1 DIA RESTANTE' : "$diasRestantesEAP DIAS RESTANTES";
                                                @endphp
                                                {{-- <span class="badge badge-orange mt-2">{{ $mensajeDias }}</span> --}}
                                            @endif
                                        @endif
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento5 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->whereNotNull('capturacomunicacion');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                                @endphp
                                <div class="text-center">
                                    @if (!$documento1)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @else
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> COMPLETO
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- MODAL DATOS DEL AFILIADO -->
                <div class="modal fade" id="modalDatosAfiliado" tabindex="-1" role="dialog" aria-labelledby="modalDatosAfiliadoLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalDatosAfiliadoLabel">DATOS DEL AFILIADO - {{$tipojubilacion}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('admin.tramites.guardardatosafiliado', $cliente) }}" method="POST">
                                    @csrf
                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                    {!! Form::hidden('clienteid', $cliente->id) !!}
                                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                                    {!! Form::hidden('idtramite', $idTramite) !!}
                                    {!! Form::hidden('tramite', 'JUBILACIÓN') !!}
                                    {!! Form::hidden('apoderado', $apoderadoAsignado) !!}

                                    <div class="container">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    @php
                                                        $datosAfiliado = $subtramites->where('tipo', 'DATOS DE AFILIADO')->where('tramite', 'JUBILACIÓN')->where('clienteid', $cliente->id)->first();
                                                    @endphp
                                                    @if($tipojubilacion === 'PENSIÓN SOLIDARIA DE VEJEZ')
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label class="text-center d-block w-100" style="margin-bottom: -3px;">Hijos < 25 años</label>
                                                                <p class="form-control-plaintext text-center">{{ $numhijosmenorescliente }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label class="text-center d-block w-100" style="margin-bottom: -3px;">Estado Civil</label>
                                                                <p class="form-control-plaintext text-center">{{ $estadocivilcliente }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label class="text-center d-block w-100" style="margin-bottom: -3px;">Años de Servicio</label>
                                                            @if($datosAfiliado)
                                                                <p class="form-control-plaintext text-center">{{ $datosAfiliado->anniosservicio }}</p>
                                                                {!! Form::hidden('anios_servicio', $datosAfiliado->anniosservicio) !!}
                                                            @else
                                                                {!! Form::number('anios_servicio', null, ['class' => 'form-control text-center']) !!}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label class="text-center d-block w-100" style="margin-bottom: -3px;">Cantidad de Cuotas</label>
                                                            @if($datosAfiliado)
                                                                <p class="form-control-plaintext text-center">{{ $datosAfiliado->cantidadcuotas }}</p>
                                                                {!! Form::hidden('cantidad_cuotas', $datosAfiliado->cantidadcuotas, ['id' => 'cantidad_cuotas']) !!}
                                                            @else
                                                                {!! Form::number('cantidad_cuotas', null, [
                                                                    'class' => 'form-control text-center',
                                                                    'id' => 'cantidad_cuotas',
                                                                    'min' => 120,
                                                                    'max' => 420
                                                                ]) !!}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label class="text-center d-block w-100" style="margin-bottom: -3px;">Saldo Acumulado</label>
                                                            @if($datosAfiliado)
                                                                <p class="form-control-plaintext text-center">{{ $datosAfiliado->saldoacumulado }}</p>
                                                                {!! Form::hidden('saldo_acumulado', $datosAfiliado->saldoacumulado, ['id' => 'saldo_acumulado']) !!}
                                                            @else
                                                                {!! Form::number('saldo_acumulado', null, [
                                                                    'class' => 'form-control text-center',
                                                                    'id' => 'saldo_acumulado',
                                                                    'step' => '0.01'
                                                                ]) !!}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    
                                                    <div class="col-lg-3">
                                                        <div class="form-group">
                                                            <label class="text-center d-block w-100" style="margin-bottom: -3px;">Saldo ÷ 195</label>
                                                            @if($datosAfiliado)
                                                                <p class="form-control-plaintext text-center">{{ $datosAfiliado->montoaprox }}</p>
                                                                {!! Form::hidden('montoaprox', $datosAfiliado->montoaprox) !!}
                                                            @else
                                                                {!! Form::number('montoaprox', null, ['class' => 'form-control text-center', 'id' => 'resultadoDivision','step' => '0.01']) !!}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-lg-12">
                                                        <table class="table table-bordered text-center table-sm">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th>Años</th>
                                                                    <th>Meses</th>
                                                                    <th>Mínimo</th>
                                                                    <th>Máximo</th>
                                                                    <th>%_Ref.</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tablaCuotas">
                                                                @if($datosAfiliado && $datosAfiliado->leyannios)
                                                                    <tr>
                                                                        <td>{{ $datosAfiliado->leyannios }}</td>
                                                                        <td>{{ $datosAfiliado->leymeses }}</td>
                                                                        <td>{{ $datosAfiliado->leyminimo }}</td>
                                                                        <td>{{ $datosAfiliado->leymaximo }}</td>
                                                                        <td>{{ $datosAfiliado->leyporcentajeref }}</td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td colspan="5">Ingrese la cantidad de cuotas</td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    {{-- Hidden inputs fuera del tbody --}}
                                                    <input type="hidden" name="leyannios" id="leyannios" value="{{ $datosAfiliado?->leyannios }}">
                                                    <input type="hidden" name="leymeses" id="leymeses" value="{{ $datosAfiliado?->leymeses }}">
                                                    <input type="hidden" name="leyminimo" id="leyminimo" value="{{ $datosAfiliado?->leyminimo }}">
                                                    <input type="hidden" name="leymaximo" id="leymaximo" value="{{ $datosAfiliado?->leymaximo }}">
                                                    <input type="hidden" name="leyporcentajeref" id="leyporcentajeref" value="{{ $datosAfiliado?->leyporcentajeref }}">
                                                    <script>
                                                        let aporteslimitesley = @json($aporteslimitesley);

                                                        document.getElementById('cantidad_cuotas').addEventListener('input', function () {
                                                            let valor = parseInt(this.value);
                                                            let tbody = document.getElementById('tablaCuotas');
                                                            let fila = aporteslimitesley.find(item => item.meses == valor);

                                                            if (fila) {
                                                                tbody.innerHTML = `
                                                                    <tr>
                                                                        <td>${fila.annio}</td>
                                                                        <td>${fila.meses}</td>
                                                                        <td>${fila.minimo}</td>
                                                                        <td>${fila.maximo}</td>
                                                                        <td>${fila.porcentajeref}</td>
                                                                    </tr>
                                                                `;

                                                                // llenar hidden
                                                                document.getElementById('leyannios').value = fila.annio;
                                                                document.getElementById('leymeses').value = fila.meses;
                                                                document.getElementById('leyminimo').value = fila.minimo;
                                                                document.getElementById('leymaximo').value = fila.maximo;
                                                                document.getElementById('leyporcentajeref').value = fila.porcentajeref;
                                                            } else {
                                                                tbody.innerHTML = `<tr><td colspan="5">No hay datos para ${valor} meses</td></tr>`;
                                                                document.getElementById('leyannios').value = '';
                                                                document.getElementById('leymeses').value = '';
                                                                document.getElementById('leyminimo').value = '';
                                                                document.getElementById('leymaximo').value = '';
                                                                document.getElementById('leyporcentajeref').value = '';
                                                            }
                                                        });

                                                        document.getElementById('saldo_acumulado').addEventListener('input', function () {
                                                            let saldo = parseFloat(this.value);
                                                            if (!isNaN(saldo)) {
                                                                let division = (saldo / 195).toFixed(2);
                                                                document.getElementById('resultadoDivision').value = division;
                                                                document.getElementById('montoaprox').value = division; // guardar hidden
                                                            } else {
                                                                document.getElementById('resultadoDivision').value = "";
                                                                document.getElementById('montoaprox').value = "";
                                                            }
                                                        });
                                                    </script>

                                                    @if($tipojubilacion === 'PENSIÓN ANTICIPADA DE VEJEZ')
                                                        <div class="col-lg-12" style="margin-bottom: -15px;">
                                                            <div class="form-group">
                                                                <label class="text-center d-block w-100" style="margin-bottom: -3px;">Aporte Independiente</label>
                                                                @if($datosAfiliado)
                                                                    <p class="form-control-plaintext text-center">
                                                                        {{ $datosAfiliado->aporteindependiente === 'SI' ? 'Sí' : 'No' }}
                                                                    </p>
                                                                    {!! Form::hidden('aporte_independiente', $datosAfiliado->aporteindependiente) !!}
                                                                @else
                                                                    {!! Form::select(
                                                                        'aporte_independiente',
                                                                        ['NO'=>'No','SI'=>'Sí'],
                                                                        null,
                                                                        ['class'=>'form-control', 'id'=>'aporte_independiente']
                                                                    ) !!}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 {{ ($datosAfiliado->aporteindependiente ?? '') === 'SI' ? '' : 'd-none' }}" id="contenedor_aportes">
                                            <h5 class="text-center d-block w-100" style="margin-bottom: 10px;">APORTES INDEPENDIENTES</h5>
                                            <table class="table table-sm table-bordered table-striped" id="tabla_aportes">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th class="text-center">Monto de Aporte (Bs)</th>
                                                        <th class="text-center">Fecha de Aporte</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($subtramites->where('tipo', 'APORTE MENSUAL')->where('tramite', 'JUBILACIÓN')->where('clienteid', $cliente->id) as $aporte)
                                                        <tr>
                                                            <td class="text-center">{{ $aporte->cantidadaporte }}</td>
                                                            <td class="text-center">{{ $aporte->fechaaporte }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-seguimiento btn-sm" id="btnAgregarAporte">AGREGAR</button>
                                        </div>

                                        <div class="col-lg-12 mt-3">
                                            <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                                        </div>
                                    </div>
                                </form>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const aporteIndependiente = document.getElementById("aporte_independiente");
                                        const contenedorAportes = document.getElementById("contenedor_aportes");
                                        const tablaAportes = document.getElementById("tabla_aportes").querySelector("tbody");
                                        const btnAgregar = document.getElementById("btnAgregarAporte");
                                        aporteIndependiente?.addEventListener("change", function() {
                                            if (aporteIndependiente.value === "SI") {
                                                contenedorAportes.classList.remove("d-none");
                                            } else {
                                                contenedorAportes.classList.add("d-none");
                                                tablaAportes.querySelectorAll("tr.nuevo").forEach(tr => tr.remove());
                                            }
                                        });
                                        btnAgregar?.addEventListener("click", function() {
                                            const row = document.createElement("tr");
                                            row.classList.add("nuevo");
                                            row.innerHTML = `
                                                <td>
                                                    <input type="number" name="aportes[cantidad][]" class="form-control" step="0.01" required>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <input type="date" name="aportes[fecha][]" class="form-control" required>
                                                        <button type="button" class="btn btn-danger btn-sm btnEliminar ms-2">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            `;
                                            tablaAportes.appendChild(row);
                                        });
                                        tablaAportes.addEventListener("click", function(e) {
                                            if (e.target.classList.contains("btnEliminar")) {
                                                e.target.closest("tr").remove();
                                            }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL INGRESO DE TRÁMITE -->
                <div class="modal fade" id="modalIngresoTramite" tabindex="-1" role="dialog" aria-labelledby="modalIngresoTramiteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="d-flex align-items-center">
                                    @if($tramiteinicio)
                                        <h5 class="modal-title titulomodal" id="modalIngresoTramiteLabel">INGRESO DE TRÁMITE</h5>
                                    @endif
                                    @if($tramitecontinuidad)
                                        <h5 class="modal-title titulomodal" id="modalIngresoTramiteLabel">INGRESO DE PODER</h5>
                                    @endif
                                    <a class="btn btn-sm btn-subirrequisitos ml-2" href="{{ route('admin.asociados.subirdocrequisitosjubilacion', $cliente->id) }}">VER REQUISITOS</a>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                    {!! Form::hidden('clienteid', $cliente->id) !!}
                                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                                    {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                                    {!! Form::hidden('idtramite', $idTramite) !!}
                                    @csrf
                                    <div class="container">
                                        @php
                                            $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                                            $fecha = \Carbon\Carbon::now();
                                            $valor = $fecha->day <= 15 ? $fecha : $fecha->copy()->addMonth();
                                            $valorFormateado = $valor->format('m/y');
                                            $documento2 = $cliente->tramites()->where('subprocedimiento', 'INCLUSIÓN DE PODER')->where('tramite', 'JUBILACIÓN')->first();
                                        @endphp

                                        @if($tramiteinicio)
                                            <div class="table-responsive">
                                                <div class="scroll-shadow-wrapper">
                                                    <div class="scroll-shadow-container">
                                                        <table class="table table-bordered table-sm align-middle text-center">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th style="width: 15%;">SUB_PROCEDIMIENTO</th>
                                                                    <th style="width: 15%;">APODERADOS</th>
                                                                    <th style="width: 10%;">NRO.PODER</th>
                                                                    <th style="width: 15%;">USUARIO_INGRESO</th>
                                                                    <th style="width: 15%;">MES_CIERRE</th>
                                                                    <th style="width: 10%;">FECHA_REGISTRO</th>
                                                                    <th style="width: 10%;">FECHA_RETORNO</th>
                                                                    <th style="width: 10%;">DOCUMENTO</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">RECEPCIÓN DE TRÁMITE</p>
                                                                        @if (!$documento1)
                                                                            <input type="hidden" name="tramite[]" value="JUBILACIÓN">
                                                                            <input type="hidden" name="nivelprocedimiento[]" value="INGRESO DE TRÁMITE">
                                                                            <input type="hidden" name="subprocedimiento[]" value="RECEPCIÓN DE TRÁMITE">
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($apoderadosList->isNotEmpty())
                                                                            @foreach ($apoderadosList as $apoderado)
                                                                                <p class="mb-0">{{ $apoderado }}</p>
                                                                            @endforeach
                                                                        @else
                                                                            <p class="mb-0 text-muted">SIN APODERADOS</p>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        {{ $numeropoder }}
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento1)
                                                                            {{ $documento1->usuarioingreso }}
                                                                        @else
                                                                            <select class="form-control" name="usuarioingreso[]" required>
                                                                                <option value="" disabled selected>Seleccione un apoderado...</option>
                                                                                @foreach ($apoderadosList as $apoderado)
                                                                                    <option value="{{ $apoderado }}">{{ $apoderado }}</option>
                                                                                @endforeach
                                                                                <option value="DENISSE MAUREN LOPEZ FLORES">DENISSE MAUREN LOPEZ FLORES</option>
                                                                                <option value="FABRICIO ORLANDO PRADO PARRADO">FABRICIO ORLANDO PRADO PARRADO</option>
                                                                            </select>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento1)
                                                                            {{ $documento1->mescierre }}
                                                                        @else
                                                                            <input type="text" class="form-control text-center" name="mescierre[]" value="{{ $valorFormateado }}" readonly>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento1)
                                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento1->fechasubida)->format('d-m-Y') }}</p>
                                                                        @else
                                                                            @php
                                                                                $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                                $fechaLimiteOriginal = \Carbon\Carbon::now()->addDays(15);
                                                                                $fechaLimite = $fechaLimiteOriginal->copy();
                                                                                if ($fechaLimite->isSaturday()) {
                                                                                    $fechaLimite->subDay();
                                                                                } elseif ($fechaLimite->isSunday()) {
                                                                                    $fechaLimite->addDay();
                                                                                }
                                                                                $fechaLimiteStr = $fechaLimite->toDateString();
                                                                            @endphp
                                                                            <input type="date" class="form-control text-center" id="fechasubida" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }} onchange="actualizarFechaRetorno(this)">
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento1)
                                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento1->fecharetorno)->format('d-m-Y') }}</p>
                                                                        @else
                                                                            <input type="date" class="form-control text-center" id="fecharetorno" name="fecharetorno[]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento1)
                                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/INGRESO DE TRÁMITE/{$documento1->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                            @if ($puedeEditarArchivo)
                                                                                <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                                    <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                                    <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                        <i class="fas fa-upload"></i>
                                                                                    </button>
                                                                                    <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento1->id }}">
                                                                                </div>
                                                                            @endif
                                                                        @else
                                                                            <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($tramitecontinuidad)
                                            <div class="table-responsive">
                                                <div class="scroll-shadow-wrapper">
                                                    <div class="scroll-shadow-container">
                                                        <table class="table table-bordered table-sm align-middle text-center">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                                                    <th style="width: 15%;">APODERADOS</th>
                                                                    <th style="width: 10%;">NRO.PODER</th>
                                                                    <th style="width: 20%;">USUARIO_INGRESO</th>
                                                                    <th style="width: 10%;">FECHA_INCLUSION</th>
                                                                    <th style="width: 10%;">FECHA_RETORNO</th>
                                                                    <th style="width: 10%;">DOCUMENTO</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">INCLUSIÓN DE PODER</p>
                                                                        @if (!$documento2)
                                                                            <input type="hidden" name="tramite[]" value="JUBILACIÓN">
                                                                            <input type="hidden" name="nivelprocedimiento[]" value="INGRESO DE TRÁMITE">
                                                                            <input type="hidden" name="subprocedimiento[]" value="INCLUSIÓN DE PODER">
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($apoderadosList->isNotEmpty())
                                                                            @foreach ($apoderadosList as $apoderado)
                                                                                <p class="mb-0">{{ $apoderado }}</p>
                                                                            @endforeach
                                                                        @else
                                                                            <p class="mb-0 text-muted">SIN APODERADOS</p>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        {{ $numeropoder }}
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento2)
                                                                            {{ $documento2->usuarioingreso }}
                                                                        @else
                                                                            <select class="form-control" name="usuarioingreso[]" required>
                                                                                <option value="" disabled selected>Seleccione un apoderado...</option>
                                                                                @foreach ($apoderadosList as $apoderado)
                                                                                    <option value="{{ $apoderado }}">{{ $apoderado }}</option>
                                                                                @endforeach
                                                                                <option value="DENISSE MAUREN LOPEZ FLORES">DENISSE MAUREN LOPEZ FLORES</option>
                                                                                <option value="FABRICIO ORLANDO PRADO PARRADO">FABRICIO ORLANDO PRADO PARRADO</option>
                                                                            </select>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento2)
                                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento2->fechainclusion)->format('d-m-Y') }}</p>
                                                                        @else
                                                                            <input type="date" class="form-control text-center" name="fechainclusion[]">
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento2)
                                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento2->fecharetorno)->format('d-m-Y') }}</p>
                                                                        @else
                                                                            <input type="date" class="form-control text-center" id="fecharetorno" name="fecharetorno[]">
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento2)
                                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/INGRESO DE TRÁMITE/{$documento2->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                            @if ($puedeEditarArchivo)
                                                                                <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                                    <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                                    <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                        <i class="fas fa-upload"></i>
                                                                                    </button>
                                                                                    <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento2->id }}">
                                                                                </div>
                                                                            @endif
                                                                        @else
                                                                            <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <table class="table table-bordered table-sm align-middle text-center">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th style="width: 25%;">FECHA_INGRESO</th>
                                                                    <th style="width: 25%;">MES_CIERRE</th>
                                                                    <th style="width: 25%;">ESTADO_TRAMITE</th>
                                                                    <th style="width: 25%;">FECHA_ESTADO_TRAMITE</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento2)
                                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento2->fechasubida)->format('d-m-Y') }}</p>
                                                                        @else
                                                                            @php
                                                                                $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                                $fechaLimiteOriginal = \Carbon\Carbon::now()->addDays(15);
                                                                                $fechaLimite = $fechaLimiteOriginal->copy();
                                                                                if ($fechaLimite->isSaturday()) {
                                                                                    $fechaLimite->subDay();
                                                                                } elseif ($fechaLimite->isSunday()) {
                                                                                    $fechaLimite->addDay();
                                                                                }
                                                                                $fechaLimiteStr = $fechaLimite->toDateString();
                                                                            @endphp
                                                                            <input type="date" class="form-control text-center" id="fechasubida" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" onchange="actualizarFechaRetorno3(this)">
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento2)
                                                                            {{ $documento2->mescierre }}
                                                                        @else
                                                                            <input type="text" class="form-control text-center" name="mescierre[]" value="{{ $valorFormateado }}" readonly>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento2)
                                                                            {{ $documento2->estadotramite }}
                                                                        @else
                                                                            <select class="form-control" name="estadotramite[]">
                                                                                <option value="" disabled selected>SELECCIONE UN ESTADO</option>
                                                                                <option value="INGRESO DE TRÁMITE">INGRESO DE TRÁMITE</option>
                                                                                <option value="FIRMA EAP">FIRMA EAP</option>
                                                                                <option value="SITM ENTE GESTOR DE SALUD">SITM ENTE GESTOR DE SALUD</option>
                                                                                <option value="SITM NOTIFICACIÓN TMC">SITM NOTIFICACIÓN TMC</option>
                                                                                <option value="SITM NOTIFICACIÓN TMR">SITM NOTIFICACIÓN TMR</option>
                                                                                <option value="SITM EMPLEADOR">SITM EMPLEADOR</option>
                                                                                <option value="COMPRA DE SERVICIOS">COMPRA DE SERVICIOS</option>
                                                                                <option value="SIC ENTE GESTOR DE SALUD">SITM ENTE GESTOR DE SALUD</option>
                                                                                <option value="SIC NOTIFICACIÓN TMC">SITM NOTIFICACIÓN TMC</option>
                                                                                <option value="SIC NOTIFICACIÓN TMR">SITM NOTIFICACIÓN TMR</option>
                                                                                <option value="SIC EMPLEADOR">SITM EMPLEADOR</option>
                                                                                <option value="SIC CANCELACIÓN DE TRÁMITE">SIC CANCELACIÓN DE TRÁMITE</option>
                                                                                <option value="CARTA DE SOLICITUD DE COMPRA DE SERVICIOS">CARTA DE SOLICITUD DE COMPRA DE SERVICIOS</option>
                                                                            </select>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento2)
                                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento2->fechaestadotramite)->format('d-m-Y') }}</p>
                                                                        @else
                                                                            <input type="date" class="form-control text-center" name="fechaestadotramite[]">
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if (!$documento1 && !$documento2)
                                        <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL NOTIFICACION DEL PODER -->
                <div class="modal fade" id="modalNotificacionPoder" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionPoderLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalNotificacionPoderLabel">NOTIFICACIÓN DE PODER</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                    {!! Form::hidden('clienteid', $cliente->id) !!}
                                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                                    {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                                    {!! Form::hidden('idtramite', $idTramite) !!}
                                    @csrf
                                    <div class="container">
                                        @php
                                            $documento4 = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE PODER')->where('tramite', 'JUBILACIÓN')->first();
                                            $documento3 = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE PODER')->where('tramite', 'JUBILACIÓN')->first();
                                            $documentoformval = $cliente->tramites()->where('subprocedimiento', 'FORMULARIO DE VALIDACIÓN DE PODER')->where('tramite', 'JUBILACIÓN')->first();
                                            $correccionpoder = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE PODER')->where('tramite', 'JUBILACIÓN')->first();
                                            $rechazodocext = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->first();
                                            $validaciondocext = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->first();
                                            $correcciondocext = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->first();
                                        @endphp
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm align-middle text-center">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 40%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 30%;">FECHA_REGISTRO</th>
                                                        <th style="width: 30%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- FORMULARIO DE VALIDACION DE PODER --}}
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">FORMULARIO DE VALIDACIÓN DE PODER</p>
                                                            @if (!$documentoformval)
                                                                <input type="hidden" name="tramite[formulariovalidacion]" value="JUBILACIÓN">
                                                                <input type="hidden" name="nivelprocedimiento[formulariovalidacion]" value="NOTIFICACIÓN DE PODER">
                                                                <input type="hidden" name="subprocedimiento[formulariovalidacion]" value="FORMULARIO DE VALIDACIÓN DE PODER">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documentoformval)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documentoformval->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" name="fechasubida[formulariovalidacion]" value="{{ \Carbon\Carbon::now()->toDateString() }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documentoformval)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/NOTIFICACIÓN DE PODER/{$documentoformval->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documentoformval->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" name="archivo[formulariovalidacion]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    {{-- VALIDACION DE PODER --}}
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">VALIDACIÓN DE PODER</p>
                                                            @if (!$documento3)
                                                                <input type="hidden" name="tramite[validacionpoder]" value="JUBILACIÓN">
                                                                <input type="hidden" name="nivelprocedimiento[validacionpoder]" value="NOTIFICACIÓN DE PODER">
                                                                <input type="hidden" name="subprocedimiento[validacionpoder]" value="VALIDACIÓN DE PODER">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento3)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento3->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" name="fechasubida[validacionpoder]" value="{{ \Carbon\Carbon::now()->toDateString() }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento3)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/NOTIFICACIÓN DE PODER/{$documento3->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento3->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" name="archivo[validacionpoder]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            @if ($cliente->paisnacimiento !== 'BOLIVIA')
                                                <table class="table table-bordered table-sm align-middle text-center">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="width: 40%;">SUB_PROCEDIMIENTO</th>
                                                            <th style="width: 20%;">FECHA_REGISTRO</th>
                                                            <th style="width: 20%;">TIPO_DOCUMENTO</th>
                                                            <th style="width: 20%;">DOCUMENTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- FORMULARIO DE RECEPCIÓN --}}
                                                        @php
                                                            $formulariosrecepcion = $cliente->tramites()->where('subprocedimiento', 'FORMULARIO DE RECEPCIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->get();
                                                        @endphp
                                                        <tbody id="contenedor-formulariorecepcion">
                                                            @if ($formulariosrecepcion->count() > 0)
                                                                @foreach ($formulariosrecepcion as $formulariorecepcion)
                                                                    <tr class="fila-formulariorecepcion">
                                                                        <td class="align-middle text-center">
                                                                            <p class="mb-0">FORMULARIO DE RECEPCIÓN DE DOCUMENTOS EXTRANJEROS</p>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($formulariorecepcion->fechasubida)->format('d/m/Y') }}</p>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <p class="mb-0">{{ $formulariorecepcion->tipodocumento }}</p>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            @if ($formulariorecepcion->document)
                                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/NOTIFICACIÓN DE PODER/{$formulariorecepcion->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                            @else
                                                                                <span class="text-muted">Sin archivo</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr class="fila-formulariorecepcion">
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">FORMULARIO DE RECEPCIÓN DE DOCUMENTOS EXTRANJEROS</p>
                                                                        <input type="hidden" name="tramite[]" value="JUBILACIÓN">
                                                                        <input type="hidden" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER">
                                                                        <input type="hidden" name="subprocedimiento[]" value="FORMULARIO DE RECEPCIÓN DE DOCUMENTOS EXTRANJEROS">
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <select class="form-control" name="tipodocumento[]">
                                                                            <option value="">Tipo Doc. Extranjero...</option>
                                                                            <option value="CERTIFICADO DE NACIMIENTO DEL ASEGURADO">CERTIFICADO DE NACIMIENTO DEL ASEGURADO</option>
                                                                            <option value="CERTIFICADO DE NACIMIENTO DEL CONYUGE">CERTIFICADO DE NACIMIENTO DEL CONYUGE</option>
                                                                            <option value="CERTIFICADO DE NACIMIENTO DE HIJO">CERTIFICADO DE NACIMIENTO DE HIJO</option>
                                                                            <option value="CERTIFICADO DE MATRIMONIO">CERTIFICADO DE MATRIMONIO</option>
                                                                            <option value="PASAPORTE">PASAPORTE</option>
                                                                            <option value="PROTOCOLIZADO DE PODER">PROTOCOLIZADO DE PODER</option>
                                                                        </select>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <input type="file" name="archivo[]" class="mx-auto d-block" accept="application/pdf">
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                        @if ($formulariosrecepcion->count() == 0)
                                                            <tr>
                                                                <td colspan="4" class="text-left">
                                                                    <button type="button" id="agregar-fila-formulariorecepcion" class="btn btn-sm btn-verdocumento">AGREGAR FORMULARIO DE RECEPCIÓN</button>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <script>
                                                            document.addEventListener('DOMContentLoaded', function () {
                                                                const botonAgregar = document.getElementById('agregar-fila-formulariorecepcion');
                                                                const contenedor = document.getElementById('contenedor-formulariorecepcion');
                                                                if (botonAgregar) {
                                                                    botonAgregar.addEventListener('click', function () {
                                                                        const filasActuales = contenedor.querySelectorAll('.fila-formulariorecepcion').length;
                                                                        if (filasActuales >= 10) {
                                                                            alert('Solo se permiten hasta 10 formularios.');
                                                                            return;
                                                                        }
                                                                        const filaOriginal = contenedor.querySelector('.fila-formulariorecepcion');
                                                                        const nuevaFila = filaOriginal.cloneNode(true);
                                                                        nuevaFila.querySelector('input[type="date"]').value = '{{ \Carbon\Carbon::now()->toDateString() }}';
                                                                        nuevaFila.querySelector('select').selectedIndex = 0;
                                                                        nuevaFila.querySelector('input[type="file"]').value = '';
                                                                        contenedor.appendChild(nuevaFila);
                                                                    });
                                                                }
                                                            });
                                                        </script>

                                                        {{-- RECHAZO DE DOCUMENTOS EXTRANJEROS --}}
                                                        @if (!$validaciondocext || $rechazodocext && (!$rechazodocext || $rechazodocext))
                                                            @php
                                                                $rechazosDocExt = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->get();
                                                                $validacionesDocExt = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->get();
                                                            @endphp
                                                            <tbody id="contenedor-rechazos">
                                                                @if ($rechazosDocExt->count() > 0)
                                                                    @foreach ($rechazosDocExt as $rechazo)
                                                                        <tr class="fila-rechazo">
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">RECHAZO DE DOCUMENTOS EXTRANJEROS</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($rechazo->fechasubida)->format('d/m/Y') }}</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">{{ $rechazo->tipodocumento }}</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                @if ($rechazo->document)
                                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/NOTIFICACIÓN DE PODER/{$rechazo->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                                @else
                                                                                    <span class="text-muted">Sin archivo</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr class="fila-rechazo">
                                                                        <td class="align-middle text-center">
                                                                            <p class="mb-0">RECHAZO DE DOCUMENTOS EXTRANJEROS</p>
                                                                            <input type="hidden" name="tramite[]" value="JUBILACIÓN">
                                                                            <input type="hidden" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER">
                                                                            <input type="hidden" name="subprocedimiento[]" value="RECHAZO DE DOCUMENTOS EXTRANJEROS">
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <select class="form-control" name="tipodocumento[]">
                                                                                <option value="">Tipo Doc. Extranjero...</option>
                                                                                <option value="CERTIFICADO DE NACIMIENTO DEL ASEGURADO">CERTIFICADO DE NACIMIENTO DEL ASEGURADO</option>
                                                                                <option value="CERTIFICADO DE NACIMIENTO DEL CONYUGE">CERTIFICADO DE NACIMIENTO DEL CONYUGE</option>
                                                                                <option value="CERTIFICADO DE NACIMIENTO DE HIJO">CERTIFICADO DE NACIMIENTO DE HIJO</option>
                                                                                <option value="CERTIFICADO DE MATRIMONIO">CERTIFICADO DE MATRIMONIO</option>
                                                                                <option value="PASAPORTE">PASAPORTE</option>
                                                                                <option value="PROTOCOLIZADO DE PODER">PROTOCOLIZADO DE PODER</option>
                                                                            </select>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <input type="file" name="archivo[]" class="mx-auto d-block" accept="application/pdf">
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                            @if ($rechazosDocExt->count() == 0 && $validacionesDocExt->count() == 0)
                                                                <tr>
                                                                    <td colspan="4" class="text-left">
                                                                        <button type="button" id="agregar-fila-rechazo" class="btn btn-sm btn-verdocumento">AGREGAR RECHAZO</button>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            <script>
                                                                document.addEventListener('DOMContentLoaded', function () {
                                                                    const botonAgregar = document.getElementById('agregar-fila-rechazo');
                                                                    const contenedor = document.getElementById('contenedor-rechazos');
                                                                    if (botonAgregar) {
                                                                        botonAgregar.addEventListener('click', function () {
                                                                            const filasActuales = contenedor.querySelectorAll('.fila-rechazo').length;
                                                                            if (filasActuales >= 10) {
                                                                                alert('Solo se permiten hasta 10 documentos de rechazo.');
                                                                                return;
                                                                            }
                                                                            const filaOriginal = contenedor.querySelector('.fila-rechazo');
                                                                            const nuevaFila = filaOriginal.cloneNode(true);
                                                                            nuevaFila.querySelector('input[type="date"]').value = '{{ \Carbon\Carbon::now()->toDateString() }}';
                                                                            nuevaFila.querySelector('select').selectedIndex = 0;
                                                                            nuevaFila.querySelector('input[type="file"]').value = '';
                                                                            contenedor.appendChild(nuevaFila);
                                                                        });
                                                                    }
                                                                });
                                                            </script>
                                                        @endif

                                                        {{-- CORRECCION DE DOCUMENTOS EXTRANJEROS --}}
                                                        @if ($rechazodocext)
                                                            @php
                                                                $correccionesDocExt = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->get();
                                                            @endphp
                                                            <tbody id="contenedor-correccion">
                                                                @if ($correccionesDocExt->count() > 0)
                                                                    @foreach ($correccionesDocExt as $correccion)
                                                                        <tr class="fila-correccion">
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">CORRECCIÓN DE DOCUMENTOS EXTRANJEROS</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($correccion->fechasubida)->format('d/m/Y') }}</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">{{ $correccion->tipodocumento }}</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                @if ($correccion->document)
                                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/NOTIFICACIÓN DE PODER/{$correccion->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                                @else
                                                                                    <span class="text-muted">Sin archivo</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr class="fila-correccion">
                                                                        <td class="align-middle text-center">
                                                                            <p class="mb-0">CORRECCIÓN DE DOCUMENTOS EXTRANJEROS</p>
                                                                            <input type="hidden" name="tramite[]" value="JUBILACIÓN">
                                                                            <input type="hidden" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER">
                                                                            <input type="hidden" name="subprocedimiento[]" value="CORRECCIÓN DE DOCUMENTOS EXTRANJEROS">
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <select class="form-control" name="tipodocumento[]">
                                                                                <option value="">Tipo Doc. Extranjero...</option>
                                                                                <option value="CERTIFICADO DE NACIMIENTO DEL ASEGURADO">CERTIFICADO DE NACIMIENTO DEL ASEGURADO</option>
                                                                                <option value="CERTIFICADO DE NACIMIENTO DEL CONYUGE">CERTIFICADO DE NACIMIENTO DEL CONYUGE</option>
                                                                                <option value="CERTIFICADO DE NACIMIENTO DE HIJO">CERTIFICADO DE NACIMIENTO DE HIJO</option>
                                                                                <option value="CERTIFICADO DE MATRIMONIO">CERTIFICADO DE MATRIMONIO</option>
                                                                                <option value="PASAPORTE">PASAPORTE</option>
                                                                                <option value="PROTOCOLIZADO DE PODER">PROTOCOLIZADO DE PODER</option>
                                                                            </select>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <input type="file" name="archivo[]" class="mx-auto d-block" accept="application/pdf">
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                            @if ($correccionesDocExt->count() == 0)
                                                                <tr>
                                                                    <td colspan="4" class="text-left">
                                                                        <button type="button" id="agregar-fila-correccion" class="btn btn-sm btn-verdocumento">AGREGAR CORRECCIÓN</button>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            <script>
                                                                document.addEventListener('DOMContentLoaded', function () {
                                                                    const botonAgregar = document.getElementById('agregar-fila-correccion');
                                                                    const contenedor = document.getElementById('contenedor-correccion');
                                                                    if (botonAgregar) {
                                                                        botonAgregar.addEventListener('click', function () {
                                                                            const filasActuales = contenedor.querySelectorAll('.fila-correccion').length;
                                                                            if (filasActuales >= 10) {
                                                                                alert('Solo se permiten hasta 10 documentos de correccion.');
                                                                                return;
                                                                            }
                                                                            const filaOriginal = contenedor.querySelector('.fila-correccion');
                                                                            const nuevaFila = filaOriginal.cloneNode(true);
                                                                            nuevaFila.querySelector('input[type="date"]').value = '{{ \Carbon\Carbon::now()->toDateString() }}';
                                                                            nuevaFila.querySelector('select').selectedIndex = 0;
                                                                            nuevaFila.querySelector('input[type="file"]').value = '';
                                                                            contenedor.appendChild(nuevaFila);
                                                                        });
                                                                    }
                                                                });
                                                            </script>
                                                        @endif

                                                        {{-- VALIDACION DE DOCUMENTOS EXTRANJEROS --}}
                                                        @if (!$rechazodocext || $correcciondocext || $validaciondocext)
                                                            @php
                                                                $rechazosDocExt = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->get();
                                                                $correccionesDocExt = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->get();
                                                                $validacionesDocExt = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'JUBILACIÓN')->get();
                                                                $cantidadCamposNuevos = 0;

                                                                if ($rechazosDocExt->isEmpty() && $correccionesDocExt->isEmpty()) {
                                                                    if ($validacionesDocExt->isEmpty()) {
                                                                        $cantidadCamposNuevos = 1;
                                                                    }
                                                                } elseif ($correccionesDocExt->count() > 0) {
                                                                    $validacionesUsadas = collect();
                                                                    foreach ($correccionesDocExt as $correccion) {
                                                                        $validacionRelacionada = $validacionesDocExt->first(function($v) use ($correccion, $validacionesUsadas) {
                                                                            return \Carbon\Carbon::parse($v->created_at)->gt(\Carbon\Carbon::parse($correccion->created_at))
                                                                                && !$validacionesUsadas->contains($v->id);
                                                                        });
                                                                        if ($validacionRelacionada) {
                                                                            $validacionesUsadas->push($validacionRelacionada->id);
                                                                        } else {
                                                                            $cantidadCamposNuevos++;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            <tbody id="contenedor-validacion">
                                                                @if ($validacionesDocExt->count() > 0)
                                                                    @foreach ($validacionesDocExt as $validacion)
                                                                        <tr class="fila-validacion">
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">VALIDACIÓN DE DOCUMENTOS EXTRANJEROS</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($validacion->fechasubida)->format('d/m/Y') }} </p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">{{ $validacion->tipodocumento }}</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                @if ($validacion->document)
                                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/NOTIFICACIÓN DE PODER/{$validacion->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                                @else
                                                                                    <span class="text-muted">Sin archivo</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif

                                                                @if ($cantidadCamposNuevos > 0)
                                                                    @for ($i = 0; $i < $cantidadCamposNuevos; $i++)
                                                                        <tr class="fila-validacion">
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">VALIDACIÓN DE DOCUMENTOS EXTRANJEROS</p>
                                                                                <input type="hidden" name="tramite[]" value="JUBILACIÓN">
                                                                                <input type="hidden" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER">
                                                                                <input type="hidden" name="subprocedimiento[]" value="VALIDACIÓN DE DOCUMENTOS EXTRANJEROS">
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <select class="form-control" name="tipodocumento[]">
                                                                                    <option value="">Tipo Doc. Extranjero...</option>
                                                                                    <option value="CERTIFICADO DE NACIMIENTO DEL ASEGURADO">CERTIFICADO DE NACIMIENTO DEL ASEGURADO</option>
                                                                                    <option value="CERTIFICADO DE NACIMIENTO DEL CONYUGE">CERTIFICADO DE NACIMIENTO DEL CONYUGE</option>
                                                                                    <option value="CERTIFICADO DE NACIMIENTO DE HIJO">CERTIFICADO DE NACIMIENTO DE HIJO</option>
                                                                                    <option value="CERTIFICADO DE MATRIMONIO">CERTIFICADO DE MATRIMONIO</option>
                                                                                    <option value="PASAPORTE">PASAPORTE</option>
                                                                                    <option value="PROTOCOLIZADO DE PODER">PROTOCOLIZADO DE PODER</option>
                                                                                </select>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <input type="file" name="archivo[]" class="mx-auto d-block" accept="application/pdf">
                                                                            </td>
                                                                        </tr>
                                                                    @endfor
                                                                @endif
                                                            </tbody>
                                                            @if ($validacionesDocExt->count() == 0)
                                                                <tr>
                                                                    <td colspan="4" class="text-left">
                                                                        <button type="button" id="agregar-fila-validacion" class="btn btn-sm btn-verdocumento">AGREGAR VALIDACIÓN</button>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            <script>
                                                                document.addEventListener('DOMContentLoaded', function () {
                                                                    const botonAgregar = document.getElementById('agregar-fila-validacion');
                                                                    const contenedor = document.getElementById('contenedor-validacion');
                                                                    if (botonAgregar) {
                                                                        botonAgregar.addEventListener('click', function () {
                                                                            const filasActuales = contenedor.querySelectorAll('.fila-validacion').length;
                                                                            if (filasActuales >= 10) {
                                                                                alert('Solo se permiten hasta 10 documentos de validacion.');
                                                                                return;
                                                                            }
                                                                            const filaOriginal = contenedor.querySelector('.fila-validacion');
                                                                            const nuevaFila = filaOriginal.cloneNode(true);
                                                                            nuevaFila.querySelector('input[type="date"]').value = '{{ \Carbon\Carbon::now()->toDateString() }}';
                                                                            nuevaFila.querySelector('select').selectedIndex = 0;
                                                                            nuevaFila.querySelector('input[type="file"]').value = '';
                                                                            contenedor.appendChild(nuevaFila);
                                                                        });
                                                                    }
                                                                });
                                                            </script>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- @if (!$documento4 && !$validaciondocext) --}}
                                        <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto" target="_blank">GUARDAR</button>
                                    {{-- @endif --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL FIRMA EAP -->
                <div class="modal fade" id="modalFirmaEAP" tabindex="-1" role="dialog" aria-labelledby="modalFirmaEAPLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalIngresoTramiteLabel">FIRMA EAP</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                    {!! Form::hidden('clienteid', $cliente->id) !!}
                                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                                    {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                                    {!! Form::hidden('idtramite', $idTramite) !!}
                                    {!! Form::hidden('tramitenombreprog', 'JUBILACIÓN') !!}
                                    @csrf
                                    <div class="container">
                                        @php
                                            $documento5 = $cliente->tramites()->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->where('tramite', 'JUBILACIÓN')->first();
                                        @endphp
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm align-middle text-center">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 15%;">FECHA_EMISIÓN</th>
                                                        <th style="width: 15%;">FECHA_REMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_REGISTRO</th>
                                                        <th style="width: 10%;">FECHA_RETORNO</th>
                                                        <th style="width: 10%;">RECOJO_DOCUM.</th>
                                                        <th style="width: 10%;">TIPO_DOCUMEN.</th>
                                                        <th style="width: 10%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">ESTADO DE AHORRO PREVISIONAL</p>
                                                            @if (!$documento5)
                                                                <input type="hidden" name="tramite[]" value="JUBILACIÓN">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="FIRMA EAP">
                                                                <input type="hidden" name="subprocedimiento[]" value="ESTADO DE AHORRO PREVISIONAL">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento5)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento5->fechaemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" name="fechaemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento5)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento5->fecharemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" name="fecharemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento5)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento5->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                @php
                                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                    $fechaLimiteOriginal = \Carbon\Carbon::now()->addDays(30);
                                                                    $fechaLimite = $fechaLimiteOriginal->copy();
                                                                    if ($fechaLimite->isSaturday()) {
                                                                        $fechaLimite->subDay();
                                                                    } elseif ($fechaLimite->isSunday()) {
                                                                        $fechaLimite->addDay();
                                                                    }
                                                                    $fechaLimiteStr = $fechaLimite->toDateString();
                                                                @endphp
                                                                <input type="date" class="form-control text-center" id="fechasubida2" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }} onchange="actualizarFechaRetorno2(this)">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento5)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento5->fecharetorno)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fecharetorno2" name="fecharetorno[]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento5)
                                                                <p class="mb-0">{{ $documento5->recojodocumentacion }}</p>
                                                            @else
                                                                <select name="recojodocumentacion[]" class="form-control recojo-select">
                                                                    <option value="">Seleccione una opción...</option>
                                                                    <option value="FUNCIONARIO">FUNCIONARIO</option>
                                                                    <option value="CLIENTE">CLIENTE</option>
                                                                </select>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento5)
                                                                <p class="mb-0">{{ $documento5->tipodocumentacion }}</p>
                                                            @else
                                                                <select name="tipodocumentacion[]" class="form-control tipo-select">
                                                                    <option value="">Seleccione una opción...</option>
                                                                    <option value="ORIGINAL">ORIGINAL</option>
                                                                    <option value="COPIA">COPIA</option>
                                                                </select>
                                                                <input type="hidden" name="tipodocumentacion[]" class="hidden-tipo" value="">
                                                            @endif
                                                        </td>
                                                        <script>
                                                            document.addEventListener('DOMContentLoaded', function () {
                                                                const recojoSelects = document.querySelectorAll('.recojo-select');
                                                                const tipoSelects = document.querySelectorAll('.tipo-select');

                                                                recojoSelects.forEach((recojoSelect, index) => {
                                                                    recojoSelect.addEventListener('change', function () {
                                                                        const tipoSelect = tipoSelects[index];

                                                                        let hiddenInput = tipoSelect.parentElement.querySelector('.hidden-tipo');
                                                                        if (!hiddenInput) {
                                                                            hiddenInput = document.createElement('input');
                                                                            hiddenInput.type = 'hidden';
                                                                            hiddenInput.classList.add('hidden-tipo');
                                                                            hiddenInput.name = 'tipodocumentacion[]';
                                                                            tipoSelect.parentElement.appendChild(hiddenInput);
                                                                        }

                                                                        if (this.value === 'FUNCIONARIO') {
                                                                            tipoSelect.disabled = true;
                                                                            tipoSelect.value = 'ORIGINAL';
                                                                            hiddenInput.value = 'ORIGINAL';
                                                                        } else {
                                                                            tipoSelect.disabled = false;
                                                                            tipoSelect.value = '';
                                                                            hiddenInput.value = '';
                                                                        }
                                                                    });
                                                                });
                                                            });
                                                        </script>
                                                        <td class="align-middle text-center">
                                                            @if ($documento5)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/FIRMA EAP/{$documento5->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento5->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm align-middle text-center" id="tablaEmpresas">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 35%;">RAZON_SOCIAL_EMPLEADOR</th>
                                                        <th style="width: 30%;">PERIODO(S)</th>
                                                        <th style="width: 35%;">OBSERVACIONES</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (isset($documento5) && count($registrosAgrupados ?? []) > 0)
                                                        @foreach ($registrosAgrupados as $registro)
                                                            <tr>
                                                                <td class="align-middle text-center">{{ $registro['razonsocialempleador'] }}</td>
                                                                <td class="align-middle text-center">{{ implode(', ', $registro['periodos']) }}</td>
                                                                <td class="align-middle text-center">{{ $registro['observacion'] }}</td>
                                                            </tr>
                                                        @endforeach

                                                    @elseif (isset($documento5) && count($registrosAgrupados ?? []) === 0)
                                                        <tr>
                                                            <td class="align-middle text-center" colspan="3">SIN OBSERVACIONES</td>
                                                        </tr>
                                                    @else
                                                        <tr class="fila-dinamica" data-index="0">
                                                            <td class="align-middle text-center">
                                                                <select class="form-control" name="razonsocialempleador[]">
                                                                    <option value="" disabled selected>Seleccione una empresa...</option>
                                                                    @foreach ($empresas as $empresa)
                                                                        <option value="{{ $empresa->nombreempresa }}">{{ $empresa->nombreempresa }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="align-middle text-center contenedor-periodos">
                                                                <input type="month" class="form-control mb-1 text-center" name="periodo[0][]">
                                                                <button type="button" class="btn btn-sm btn-outline-primary btn-block agregarPeriodo">AGREGAR PERIODO</button>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <div class="input-group">
                                                                    <input list="observaciones" class="form-control input-observacion" placeholder="Seleccione o escriba..." />
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-secondary btn-agregar-observacion">+</button>
                                                                    </div>
                                                                </div>
                                                                <input type="text" name="observacion[]" class="form-control mt-1 campo-acumulado" readonly placeholder="Observaciones que se guardarán...">
                                                                <datalist id="observaciones">
                                                                    <option value="APORTES NO ACREDITADOS">
                                                                    <option value="APORTES EN EXCESO">
                                                                    <option value="APORTES EN MORA">
                                                                    <option value="PERIODO MENOR A 20 DÍAS">
                                                                    <option value="PROCESO DE REGULARIZACIÓN">
                                                                    <option value="PROCESO POR ACREDITACIÓN">
                                                                    <option value="MORA PRESUNTA POR DEFECTO">
                                                                    <option value="NO EXISTE MORA TIPIFICADA PARA EL EMPLEADOR">
                                                                </datalist>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>

                                            @if (!isset($documento5) && (count($registrosAgrupados ?? []) === 0))
                                                <div class="text-right mt-2">
                                                    <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarFila()">AGREGAR OBSERVACIÓN</button>
                                                </div>
                                            @endif
                                        </div>

                                        <script>
                                            const empresas = @json($empresas);
                                            let indiceFila = 1;

                                            function agregarFila() {
                                                const tabla = document.getElementById('tablaEmpresas').getElementsByTagName('tbody')[0];
                                                const fila = document.createElement('tr');
                                                fila.classList.add('fila-dinamica');
                                                fila.dataset.index = indiceFila;

                                                fila.innerHTML = `
                                                    <td class="align-middle text-center">
                                                        <select class="form-control" name="razonsocialempleador[]">
                                                            <option value="" disabled selected>Seleccione una empresa...</option>
                                                            ${empresas.map(e => `<option value="${e.nombreempresa}">${e.nombreempresa}</option>`).join('')}
                                                        </select>
                                                    </td>
                                                    <td class="align-middle text-center contenedor-periodos">
                                                        <input type="month" class="form-control mb-1 text-center" name="periodo[${indiceFila}][]">
                                                        <button type="button" class="btn btn-sm btn-outline-primary btn-block agregarPeriodo">AGREGAR PERIODO</button>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <div class="input-group">
                                                            <input list="observaciones" class="form-control input-observacion" placeholder="Seleccione o escriba..." />
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-secondary btn-agregar-observacion">+</button>
                                                            </div>
                                                        </div>
                                                        <input type="text" name="observacion[]" class="form-control mt-1 campo-acumulado" readonly placeholder="Observaciones que se guardarán...">
                                                        <datalist id="observaciones">
                                                            <option value="APORTES NO ACREDITADOS">
                                                            <option value="APORTES EN EXCESO">
                                                            <option value="APORTES EN MORA">
                                                            <option value="PERIODO MENOR A 20 DÍAS">
                                                            <option value="PROCESO DE REGULARIZACIÓN">
                                                            <option value="PROCESO POR ACREDITACIÓN">
                                                            <option value="MORA PRESUNTA POR DEFECTO">
                                                            <option value="NO EXISTE MORA TIPIFICADA PARA EL EMPLEADOR">
                                                        </datalist>
                                                    </td>
                                                `;

                                                tabla.appendChild(fila);
                                                indiceFila++;
                                            }

                                            document.addEventListener('click', function(e) {
                                                if (e.target.classList.contains('agregarPeriodo')) {
                                                    const contenedor = e.target.closest('.contenedor-periodos');
                                                    const fila = e.target.closest('tr');
                                                    const index = fila.dataset.index;

                                                    const nuevo = document.createElement('input');
                                                    nuevo.type = 'month';
                                                    nuevo.name = `periodo[${index}][]`;
                                                    nuevo.classList.add('form-control', 'mb-1', 'text-center');
                                                    contenedor.insertBefore(nuevo, e.target);
                                                }
                                            });
                                        </script>
                                        <script>
                                            document.addEventListener('click', function(e) {
                                                if (e.target.classList.contains('btn-agregar-observacion')) {
                                                    const fila = e.target.closest('td');
                                                    const inputTemporal = fila.querySelector('.input-observacion');
                                                    const inputAcumulado = fila.querySelector('.campo-acumulado');

                                                    let valor = inputTemporal.value.trim();
                                                    if (valor !== '') {
                                                        if (inputAcumulado.value === '') {
                                                            inputAcumulado.value = valor;
                                                        } else {
                                                            inputAcumulado.value += ', ' + valor;
                                                        }
                                                        inputTemporal.value = '';
                                                    }
                                                }
                                            });
                                        </script>
                                    </div>
                                    @if (!$documento5)
                                        <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div> 

                {{-- 3.- RESULTADO DEL PROCESO --}}
                <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamen">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-balance-scale fa-5x mb-2"></i>
                                    <span class="h6 mb-0 btn-block text-center">CONTRATO</span>
                                </div>
                            </button>
                            <br>
                            @php
                                $documento41 = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('tramite', 'JUBILACIÓN')->first();
                            @endphp
                            <div class="text-center">
                                @if ($documento41)
                                    @if ($documento41->estadodictamen == 'ACEPTADO')
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> ACEPTADO
                                        </span>
                                    @elseif ($documento41->estadodictamen == 'RECHAZADO')
                                        <span class="mb-0 checkrojo">
                                            <i class="fas fa-times-circle"></i> RECHAZADO
                                        </span>
                                    @else
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> PENDIENTE
                                        </span>
                                    @endif
                                @else
                                    <span class="mb-0 checkamarillo">
                                        <i class="fas fa-exclamation-triangle"></i> PENDIENTE
                                    </span>
                                @endif
                            </div>
                        </div>

                        @php
                            $documento43 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('estadodictamen', 'ACEPTADO')->where('tramite', 'JUBILACIÓN')->first();
                            $documentoRechazado = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('estadodictamen', 'RECHAZADO')->where('tramite', 'JUBILACIÓN')->first();
                        @endphp
                        <div class="col-12 col-md-6 mb-3">
                            @if ($documentoRechazado)
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenRechazado" disabled>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-left fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">CONTRATO ACEPTADO</span>
                                    </div>
                                </button>
                            @elseif ($documento43)
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenAceptado">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-left fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">CONTRATO ACEPTADO</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento43 = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'ACEPTACIÓN DE CONTRATO')->where('tramite', 'JUBILACIÓN')->first();
                                    $documento44 = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'FIRMA DE FORMULARIO')->where('tramite', 'JUBILACIÓN')->first();
                                    $documento45 = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'FIRMA DE CONTRATO')->where('tramite', 'JUBILACIÓN')->first();
                                    $documento46 = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'COBRO DE PENSIÓN')->where('tramite', 'JUBILACIÓN')->first();
                                    $documento47 = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->where('tramite', 'JUBILACIÓN')->first();

                                    $accedepensiondictamen = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE CONTRATO')->where('accesopension', 'SI')->where('tramite', 'JUBILACIÓN')->first();
                                    $noaccedepensiondictamene6 = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE CONTRATO (EXCESO DE 6 MESES)')->where('accesopension', 'NO')->where('motivonopension', 'EXCESO DE 6 MESES')->where('tramite', 'JUBILACIÓN')->first();
                                    $noaccedepensiondictamenfc = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE CONTRATO (FALTA DE COBERTURA)')->where('accesopension', 'NO')->where('motivonopension', 'FALTA DE COBERTURA')->where('tramite', 'JUBILACIÓN')->first();
                                    
                                @endphp
                                <div class="text-center">
                                    @if (!$accedepensiondictamen && !$noaccedepensiondictamene6 && !$noaccedepensiondictamenfc)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @elseif ($accedepensiondictamen)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> SI ACCEDE A PENSIÓN
                                        </span>
                                    @elseif ($noaccedepensiondictamene6)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> NO ACCEDE A PENSIÓN - EXCESO DE 6 MESES
                                        </span>
                                    @elseif ($noaccedepensiondictamenfc)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> NO ACCEDE A PENSIÓN - FALTA DE COBERTURA
                                        </span>
                                    @endif
                                </div>
                                @else
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenAceptado" disabled>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-left fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">ESTADO DE CONTRATO PENDIENTE</span>
                                    </div>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- CARTAS Y RECLAMOS --}}
<div class="modal fade" id="modalcartayreclamo" tabindex="-1" role="dialog" aria-labelledby="modalcartayreclamoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalcartayreclamoLabel">CARTA / RECLAMO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <form action="{{ route('admin.tramites.generarcartareclamo', $cliente) }}" method="GET" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', auth()->user()->name) !!}
                    <input type="text" class="form-control" id="tramite" name="tramite" value="JUBILACIÓN" hidden>
                    <input type="date" class="form-control" id="fechasubida" name="fechasubida" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                    @csrf
                    <div class="row">
                        @php
                            $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                            $documento5 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'FIRMA EAP')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                            $fechaingresoyeap = $documento1 && $documento5;

                            $primeracartasit = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'PRIMERA CARTA SIT')->first();
                            $segundacartasit = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'SEGUNDA CARTA SIT')->first();
                            $terceracartasit = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'TERCERA CARTA SIT')->first();
                            $primeracartareclamo = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'PRIMERA CARTA DE RECLAMO')->first();
                            $segundacartareclamo = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'SEGUNDA CARTA DE RECLAMO')->first();
                            $terceracartareclamo = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'TERCERA CARTA DE RECLAMO')->first();
                            $cartareclamoaps = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'CARTA DE RECLAMO APS')->first();
                        @endphp

                        <div class="col-lg-4">
                                <div class="form-group col-lg-12">
                                    {!! Form::label('tipo_pdf', 'Carta / Reclamo:') !!}
                                    {!! Form::select('tipo_pdf', $modelocartasreclamos, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                    @error('tipo_pdf')
                                        <small class="text-danger fas fa-exclamation-circle">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const selectElement = document.getElementById('tipoPdfSelectcartas');
                                    const options = selectElement.querySelectorAll('option');
                                    
                                    // Define the statuses
                                    const statuses = {
                                        'PRIMERA CARTA SIT': @json($primeracartasit),
                                        'SEGUNDA CARTA SIT': @json($segundacartasit),
                                        'TERCERA CARTA SIT': @json($terceracartasit),
                                        'PRIMERA CARTA DE RECLAMO': @json($primeracartareclamo),
                                        'SEGUNDA CARTA DE RECLAMO': @json($segundacartareclamo),
                                        'TERCERA CARTA DE RECLAMO': @json($terceracartareclamo),
                                        'CARTA DE RECLAMO APS': @json($cartareclamoaps),
                                    };

                                    options.forEach(option => {
                                        const value = option.value;
                                        if (statuses[value]) {
                                            // Option is registered
                                            option.disabled = true;
                                            option.classList.add('registered');
                                        } else {
                                            // Option is not registered
                                            option.classList.add('not-registered');
                                        }
                                    });
                                });
                            </script>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const primeracartasit = tipoPdfSelectcartas.querySelector('option[value="PRIMERA CARTA SIT"]');
                                    const segundacartasit = tipoPdfSelectcartas.querySelector('option[value="SEGUNDA CARTA SIT"]');
                                    const terceracartasit = tipoPdfSelectcartas.querySelector('option[value="TERCERA CARTA SIT"]');
                                    const primeracartareclamo = tipoPdfSelectcartas.querySelector('option[value="PRIMERA CARTA DE RECLAMO"]');
                                    const segundacartareclamo = tipoPdfSelectcartas.querySelector('option[value="SEGUNDA CARTA DE RECLAMO"]');
                                    const terceracartareclamo = tipoPdfSelectcartas.querySelector('option[value="TERCERA CARTA DE RECLAMO"]');
                                    const cartareclamoaps = tipoPdfSelectcartas.querySelector('option[value="CARTA DE RECLAMO APS"]');

                                    @if (!$fechaingresoyeap)
                                        primeracartasit.disabled = true;
                                    @endif
                                    @if (!$fechaingresoyeap)
                                        segundacartasit.disabled = true;
                                    @endif
                                    @if (!$fechaingresoyeap)
                                        terceracartasit.disabled = true;
                                    @endif
                                    @if (!$fechaingresoyeap)
                                        primeracartareclamo.disabled = true;
                                    @endif
                                    @if (!$fechaingresoyeap)
                                        segundacartareclamo.disabled = true;
                                    @endif
                                    @if (!$fechaingresoyeap)
                                        terceracartareclamo.disabled = true;
                                    @endif
                                    @if (!$fechaingresoyeap)
                                        cartareclamoaps.disabled = true;
                                    @endif
                                });
                            </script>

                            <div class="form-group  col-lg-12">
                                {!! Form::label('notaseguimiento', 'Nivel de procedimiento:') !!}
                                {!! Form::select('notaseguimiento', [
                                    'INGRESO DE TRÁMITE' => 'INGRESO DE TRÁMITE',
                                    'NOTIFICACIÓN DEL PODER' => 'NOTIFICACIÓN DEL PODER',
                                    'FIRMA EAP' => 'FIRMA EAP',
                                    'SITM ENTE GESTOR DE SALUD' => 'SITM ENTE GESTOR DE SALUD',
                                    'SITM NOTIFICACIÓN TMC' => 'SITM NOTIFICACIÓN TMC',
                                    'SITM NOTIFICACIÓN TMR' => 'SITM NOTIFICACIÓN TMR',
                                    'SITM EMPLEADOR' => 'SITM EMPLEADOR',
                                    'COMPRA DE SERVICIOS' => 'COMPRA DE SERVICIOS',
                                    'SIC ENTE GESTOR DE SALUD' => 'SIC ENTE GESTOR DE SALUDO',
                                    'SIC NOTIFICACIÓN TMC' => 'SIC NOTIFICACIÓN TMCO',
                                    'SIC NOTIFICACIÓN TMR' => 'SIC NOTIFICACIÓN TMR',
                                    'SIC EMPLEADOR' => 'SIC EMPLEADOR',
                                    'CONTRATO' => 'CONTRATO',
                                
                                ], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoPdfSelect', 'required' => 'required']) !!}
                                @error('tipo_pdf')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group  col-lg-12">
                                {!! Form::label('apoderado', 'Apoderado:') !!}
                                {!! Form::select('apoderado', $personal->pluck('nombrecompleto', 'id'), null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'personalSelect', 'required' => 'required']) !!}
                                @error('apoderado')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-12" id="folioContainer" style="display: none;">
                                {!! Form::label('folio', 'Cantidad Hojas Folio:') !!}
                                {!! Form::text('folio', null, ['class' => 'form-control', 'placeholder' => 'Cantidad de hojas', 'id' => 'folioInput']) !!}
                                @error('folio')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const tipoPdfSelect = document.getElementById('tipoPdfSelectcartas');
                                    const folioContainer = document.getElementById('folioContainer');
                                    
                                    tipoPdfSelect.addEventListener('change', function () {
                                        if (tipoPdfSelect.value === 'CARTA DE RECLAMO APS') {
                                            folioContainer.style.display = 'block';
                                        } else {
                                            folioContainer.style.display = 'none';
                                        }
                                    });
                        
                                    tipoPdfSelect.dispatchEvent(new Event('change'));
                                });
                            </script>
                            <input type="text" class="form-control" id="tipocartareclamo" name="tipocartareclamo" value="PENSIÓN DE JUBILACIÓN" hidden>
                            <input type="date" class="form-control" id="fechaactual" name="fechaactual" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                            <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">GENERAR CARTA / RECLAMO</button>
                        </div>
                        <div class="col-lg-8" style="margin-top: -40px">
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sub procedimiento</th>
                                                <th>Documento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cartasreclamos as $cartasreclamo)
                                                <tr>
                                                    <td>{{ $cartasreclamo->subprocedimiento }}</td>
                                                    <td>
                                                        <abbr title="Ver documento">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$cartasreclamo->document}") }}" class="btn btn-verdocumento fas fa-eye" target="_blank" ></a>
                                                        </abbr>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ADJUNTOS Y RESPUESTAS --}}
<div class="modal fade" id="modaladjuntosrespuestas" tabindex="-1" role="dialog" aria-labelledby="modaladjuntosrespuestasLabel" aria-hidden="true">
    <div class="modal-dialog modal-xxl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modaladjuntosrespuestasLabel">ADJUNTOS Y RESPUESTAS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.tramites.generaradjuntoyrespuesta', $cliente) }}" method="GET" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('idtramite', $idTramite) !!}
                    <input type="text" class="form-control" id="tramite" name="tramite" value="JUBILACIÓN" hidden>
                    <input type="date" class="form-control" id="fechasubida" name="fechasubida" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                    @csrf
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            {!! Form::label('tipo_pdf', 'Tipo de Adjunto y Respuesta:') !!}
                                            {!! Form::select('tipo_pdf', [
                                                'ADJUNTO DOCUMENTACIÓN MÉDICA' => 'ADJUNTO DOCUMENTACIÓN MÉDICA',
                                                'ADJUNTO INFORME DEL EMPLEADOR' => 'ADJUNTO INFORME DEL EMPLEADOR',
                                                'ADJUNTO Y RESPUESTA AL ACTA TMC' => 'ADJUNTO Y RESPUESTA AL ACTA TMC',
                                                'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO' => 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO',
                                                'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO' => 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO',
                                                
                                            ], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoPdfSelect', 'required' => 'required']) !!}
                                            @error('tipo_pdf')
                                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-lg-6">
                                            {!! Form::label('apoderado', 'Apoderado:') !!}
                                            <select name="apoderado" id="personalSelect" class="form-control" required>
                                                @foreach ($apoderadosList as $apoderado)
                                                    <option value="{{ $apoderado }}"
                                                        @if (isset($apoderadoAsignado) && $apoderadoAsignado == $apoderado) selected @endif>
                                                        {{ $apoderado }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('apoderado')
                                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group col-lg-8">
                                            {!! Form::label('notatecnicomedico', 'Nro. CITE:') !!}
                                            {!! Form::text('notatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        </div>
                                        <div class="form-group col-lg-4">
                                            {!! Form::label('fechanotatecnicomedico', 'Fecha:') !!}
                                            {!! Form::date('fechanotatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                        </div>

                                        <div class="col-lg-12">
                                            {!! Form::label('', 'Lista de Baterias:') !!}
                                            <div id="contenedor_areas" class="mt-3">
                                                @foreach ($programaciones as $fecha => $grupos)
                                                    <div class="card shadow-sm border mb-2">
                                                        
                                                        <div class="card-header py-2 px-3 bg-secondary text-white">
                                                            <button class="btn btn-link text-white text-left w-100 p-0" type="button"
                                                                data-toggle="collapse" data-target="#fecha_{{ \Str::slug($fecha) }}">
                                                                <strong>FECHA BATERIA:</strong> {{ $fecha }}
                                                            </button>
                                                        </div>

                                                        <div id="fecha_{{ \Str::slug($fecha) }}" class="collapse">
                                                            <div class="card-body py-2 px-3">
                                                                @foreach ($grupos->groupBy('areanombre') as $area => $acciones)
                                                                    <div class="card border mb-2">
                                                                        <div class="card-header py-2 px-3 bg-light">
                                                                            <button class="btn btn-sm btn-outline-secondary w-100 text-left p-1"
                                                                                type="button"
                                                                                data-toggle="collapse"
                                                                                data-target="#area_{{ \Str::slug($fecha . '_' . $area) }}">
                                                                                <strong>ÁREA:</strong> {{ $area }}
                                                                            </button>
                                                                        </div>

                                                                        <div id="area_{{ \Str::slug($fecha . '_' . $area) }}" class="collapse" style="margin-top: -30px;">
                                                                            <div class="card-body pt-2 pb-1 px-3">
                                                                                <div class="table-responsive">
                                                                                    <table class="table table-sm table-bordered mb-3" style="white-space: nowrap;">
                                                                                        <thead class="thead-light text-center">
                                                                                            <tr>
                                                                                                <th class="text-center"><input type="checkbox" class="seleccionar-todo-area" data-area="{{ \Str::slug($fecha . '_' . $area) }}" /></th>
                                                                                                <th>Ver</th>
                                                                                                <th>ID</th>
                                                                                                <th>Acción</th>
                                                                                                <th>Proveedor</th>
                                                                                                <th>Hojas</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach ($acciones as $doc)
                                                                                                <tr>
                                                                                                    <td class="text-center align-middle">
                                                                                                        <input type="checkbox" class="documento-checkbox"
                                                                                                            data-proveedor="{{ $doc->proveedor_real }}"
                                                                                                            data-area="{{ $doc->areanombre }}"
                                                                                                            data-accion="{{ $doc->accionnombre }}"
                                                                                                            data-hojas="{{ $doc->nro_hojas ?? 0 }}">
                                                                                                    </td>
                                                                                                    <td class="text-center align-middle">
                                                                                                        <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->document}") }}"
                                                                                                            target="_blank" class="btn btn-sm btn-verdoc" title="Ver documento">
                                                                                                            <i class="fas fa-eye"></i>
                                                                                                        </a>
                                                                                                    </td>
                                                                                                    <td class="align-middle">{{ $doc->doc_id }}</td>
                                                                                                    <td class="align-middle">{{ $doc->accionnombre }}</td>
                                                                                                    <td class="align-middle">{{ $doc->proveedor_real ?? 'Sin proveedor' }}</td>
                                                                                                    <td class="text-center align-middle">
                                                                                                        <span class="badge" style="background-color: #faa625; color: #ffffff; font-size: 0.8rem; padding: 0.2em 0.3em; line-height: 1;">
                                                                                                            {{ $doc->nro_hojas ?? '?' }}
                                                                                                        </span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <div class="text-right mt-3">
                                                                    <div class="form-group row justify-content-end">
                                                                        <div class="col-sm-4">
                                                                            <select id="tipoDocumento" name="tipoDocumento" class="form-control form-control-sm">
                                                                                <option value="">Seleccione una opción...</option>
                                                                                <option value="INFORME MÉDICO DE">INFORME MÉDICO DE</option>
                                                                                <option value="CERTIFICADO MÉDICO DE">CERTIFICADO MÉDICO DE</option>
                                                                                <option value="ESTUDIO DE">ESTUDIO DE</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <button id="btnAgregarSeleccionados" type="button" class="btn btn-sm btn-adjuntosrespuestas">
                                                                        AGREGAR SELECCIONADOS
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" style="margin-top: -5px;">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th class="col-lg-5">ESPECIALISTA</th>
                                                        <th class="col-lg-5">DETALLE</th>
                                                        <th class="col-lg-2">CANTIDAD</th>
                                                        <th class="col-lg-2">QUITAR</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tabla-especialistas">

                                                </tbody>
                                            </table>
                                            <input type="text" class="form-control" id="tipocartareclamo" name="tipocartareclamo" value="PENSIÓN DE JUBILACIÓN" hidden>
                                            <input type="date" class="form-control" id="fechaactual" name="fechaactual" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">GENERAR ADJUNTO Y RESPUESTA</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabla = document.getElementById('tabla-especialistas');
        
        function llenarTablaConSeleccionados() {
            const seleccionados = document.querySelectorAll('.documento-checkbox:checked');
            const tipoDocumento = document.getElementById('tipoDocumento').value.trim();
            console.log('Checkbox seleccionados:', seleccionados.length);

            const agrupados = {};

            seleccionados.forEach(input => {
                const proveedor = input.dataset.proveedor;
                const area = input.dataset.area;
                const hojas = parseInt(input.dataset.hojas || 0);

                if (!agrupados[proveedor]) {
                    agrupados[proveedor] = {
                        area: area,
                        totalHojas: 0
                    };
                }

                agrupados[proveedor].totalHojas += hojas;
            });

            let filasActuales = tabla.querySelectorAll('tr').length;
            let i = filasActuales + 1;

            for (const proveedor in agrupados) {
                if (i > 10) break;

                const area = agrupados[proveedor].area || '';
                const detalle = tipoDocumento && area ? `${tipoDocumento} ${area}` : area || tipoDocumento || '';

                const hojas = agrupados[proveedor].totalHojas;

                const fila = `<tr>
                    <td><input type="text" name="especialista${i}" class="form-control" value="${proveedor}" readonly /></td>
                    <td><input type="text" name="detalle${i}" class="form-control" value="${detalle}" readonly /></td>
                    <td><input type="text" name="cantidad${i}" class="form-control" value="${hojas}" readonly /></td>
                    <td><button type="button" class="btn btn-outline-danger btn-sm quitar-fila"><i class="fas fa-trash"></i></button></td>
                </tr>`;

                tabla.insertAdjacentHTML('beforeend', fila);
                i++;
                // Agrega el evento al nuevo botón
                const ultimaFila = tabla.querySelector('tr:last-child');
                const btnQuitar = ultimaFila.querySelector('.quitar-fila');

                btnQuitar.addEventListener('click', function () {
                    ultimaFila.remove();
                });
            }
        }

        document.getElementById('btnAgregarSeleccionados').addEventListener('click', function() {
            llenarTablaConSeleccionados();

            const seleccionados = document.querySelectorAll('.documento-checkbox:checked');
            seleccionados.forEach(checkbox => checkbox.checked = false);

            const checkboxesSeleccionarTodo = document.querySelectorAll('.seleccionar-todo-area');
            checkboxesSeleccionarTodo.forEach(chk => chk.checked = false);
        });

        document.querySelectorAll('.seleccionar-area').forEach(btn => {
            btn.addEventListener('click', function () {
                const area = btn.dataset.area;
                const checkboxes = document.querySelectorAll(`#area_${area} .documento-checkbox`);
                checkboxes.forEach(c => c.checked = true);
            });
        });

        document.querySelectorAll('.seleccionar-todo-area').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const area = this.dataset.area;
                const checkboxes = document.querySelectorAll(`#area_${area} .documento-checkbox`);
                checkboxes.forEach(c => c.checked = this.checked);
            });
        });
    });
</script>

<script>
    document.getElementById('fechabateria_select').addEventListener('change', function () {
        let todas = document.querySelectorAll('.grupo_fecha');
        todas.forEach(div => div.style.display = 'none');
        let seleccionada = this.value;
        if (seleccionada) {
            let slug = seleccionada.replaceAll(/[^a-zA-Z0-9]/g, '-').toLowerCase();
            let grupo = document.querySelector('.grupo_fecha_' + slug);
            if (grupo) grupo.style.display = 'block';
        }
    });
</script>

{{-- SOLICITUDES --}}
<div class="modal fade" id="modalsolicitudes" tabindex="-1" role="dialog" aria-labelledby="modalsolicitudesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalsolicitudesLabel">HISTORIAL DE MISIVAS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="tabs-solicitudes">
                        <li class="nav-item">
                            <a class="nav-link active" id="solicitudes-tab-1" data-toggle="tab" href="#solicitudes-content-1" role="tab" aria-controls="solicitudes-content-1" aria-selected="true">HIST. DE SOLICITUDES</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="solicitudes-tab-2" data-toggle="tab" href="#solicitudes-content-2" role="tab" aria-controls="solicitudes-content-2" aria-selected="false">HIST. DE ADJUNTOS / RESPUESTAS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="solicitudes-tab-3" data-toggle="tab" href="#solicitudes-content-3" role="tab" aria-controls="solicitudes-content-3" aria-selected="false">HIST. DE CARTAS / RECLAMOS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="solicitudes-tab-4" data-toggle="tab" href="#solicitudes-content-4" role="tab" aria-controls="solicitudes-content-4" aria-selected="false">HIST. DE MISIVAS LIBRES</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="tabs-solicitudes-contenido">
                        <div class="tab-pane fade show active" id="solicitudes-content-1" role="tabpanel" aria-labelledby="solicitudes-tab-1">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>Nro.</th>
                                            <th>Nivel Procedimiento</th>
                                            <th>Solicitud</th>
                                            <th>Solicitud_Generada</th>
                                            <th>Observación a Respuesta</th>
                                            <th>Nota Cite a Respuesta</th>
                                            <th>Fecha Cite a Respuesta</th>
                                            <th>Fecha Respuesta</th>
                                            <th>Documento Respuesta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($listasolicitudes as $solicitud)
                                            <tr class="text-center">
                                                <td>{{ $solicitud->id }}</td>
                                                <td>{{ $solicitud->nro }}</td>
                                                <td>{{ $solicitud->nivelprocedimiento }}</td>
                                                <td>{{ $solicitud->subprocedimiento }}</td>
                                                <td>
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/SOLICITUDES/{$solicitud->document}") }}"
                                                    class="btn btn-sm btn-verdocumento fas fa-eye"
                                                    title="VER SOLICITUD" target="_blank"></a>
                                                </td>
                                                <form action="{{ route('admin.tramites.guardarrespuesta', $cliente) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <td>
                                                        <input type="hidden" name="tramite_id" value="{{ $solicitud->id }}">
                                                        <input type="hidden" name="nombretramite" value="JUBILACIÓN">

                                                        @if ($solicitud->document2)
                                                            <div>{{ $solicitud->observaciones }}</div>
                                                        @else
                                                            <input type="text" name="observacionessolicitud" class="form-control form-control-sm" placeholder="Observación">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($solicitud->document2)
                                                            <div>{{ $solicitud->citenota }}</div>
                                                        @else
                                                            <input type="text" name="citenotasolicitud" class="form-control form-control-sm" placeholder="Cite Nota">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($solicitud->document2)
                                                            <div>{{ $solicitud->fechacitenota }}</div>
                                                        @else
                                                            <input type="date" name="fechacitenotasolicitud" class="form-control form-control-sm">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($solicitud->document2)
                                                            <div>{{ $solicitud->fechainclusion }}</div>
                                                        @else
                                                            <input type="date" name="fechainclusionsolicitud" class="form-control form-control-sm">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($solicitud->document2)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/SOLICITUDES/{$solicitud->document2}") }}"
                                                            class="btn btn-sm btn-verdocumento"
                                                            title="VER RESPUESTA" target="_blank"><i class="fas fa-eye"></i></a>
                                                        @else
                                                            <div class="row gx-2">
                                                                <div class="col">
                                                                    <input type="file" name="document2solicitud" 
                                                                        class="form-control form-control-sm archivo-input" 
                                                                        accept="application/pdf">
                                                                </div>
                                                                <div class="col-auto">
                                                                    <button type="submit" class="btn btn-guardarnuevo guardar-btn" disabled>
                                                                        <i class="fas fa-print"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const inputs = document.querySelectorAll('.archivo-input');
                                                            inputs.forEach(input => {
                                                                input.addEventListener('change', function() {
                                                                    const button = this.closest('.row').querySelector('.guardar-btn');
                                                                    if (this.files.length > 0) {
                                                                        button.removeAttribute('disabled');
                                                                    } else {
                                                                        button.setAttribute('disabled', true);
                                                                    }
                                                                });
                                                            });
                                                        });
                                                    </script>
                                                </form>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10">NO HAY REGISTROS DE TIPO "SOLICITUD"</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="solicitudes-content-2" role="tabpanel" aria-labelledby="solicitudes-tab-2">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>Nro.</th>
                                            <th>Nivel Procedimiento</th>
                                            <th>Sub Procedimiento</th>
                                            <th>Adj/Resp_Generada</th>
                                            <th>Observación a Respuesta</th>
                                            <th>Nota Cite a Respuesta</th>
                                            <th>Fecha Cite a Respuesta</th>
                                            <th>Fecha Respuesta</th>
                                            <th>Documento Respuesta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($listaadjuntos as $adjunto)
                                            <tr class="text-center">
                                                <td>{{ $adjunto->id }}</td>
                                                <td>{{ $adjunto->nro }}</td>
                                                <td>{{ $adjunto->nivelprocedimiento }}</td>
                                                <td>{{ $adjunto->subprocedimiento }}</td>
                                                <td>
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/ADJUNTOS Y RESPUESTAS/{$adjunto->document}") }}"
                                                    class="btn btn-sm btn-verdocumento fas fa-eye"
                                                    title="VER ADJUNTO/RESPUESTA" target="_blank"></a>
                                                </td>
                                                <form action="{{ route('admin.tramites.guardarrespuestaadjunto', $cliente) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <td>
                                                        <input type="hidden" name="tramite_id" value="{{ $adjunto->id }}">
                                                        <input type="hidden" name="nombretramite" value="JUBILACIÓN">

                                                        @if ($adjunto->document2)
                                                            <div>{{ $adjunto->observaciones }}</div>
                                                        @else
                                                            <input type="text" name="observacionesadjunto" class="form-control form-control-sm" placeholder="Observación">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($adjunto->document2)
                                                            <div>{{ $adjunto->citenota }}</div>
                                                        @else
                                                            <input type="text" name="citenotaadjunto" class="form-control form-control-sm" placeholder="Cite Nota">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($adjunto->document2)
                                                            <div>{{ $adjunto->fechacitenota }}</div>
                                                        @else
                                                            <input type="date" name="fechacitenotaadjunto" class="form-control form-control-sm">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($adjunto->document2)
                                                            <div>{{ $adjunto->fechainclusion }}</div>
                                                        @else
                                                            <input type="date" name="fechainclusionadjunto" class="form-control form-control-sm">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($adjunto->document2)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/ADJUNTOS Y RESPUESTAS/{$adjunto->document2}") }}"
                                                            class="btn btn-sm btn-verdocumento"
                                                            title="VER RESPUESTA" target="_blank"><i class="fas fa-eye"></i></a>
                                                        @else
                                                            <div class="row gx-2">
                                                                <div class="col">
                                                                    <input type="file" name="document2adjunto" 
                                                                        class="form-control form-control-sm archivo-input2" 
                                                                        accept="application/pdf">
                                                                </div>
                                                                <div class="col-auto">
                                                                    <button type="submit" class="btn btn-guardarnuevo guardar-btn2" disabled>
                                                                        <i class="fas fa-print"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const inputs = document.querySelectorAll('.archivo-input2');
                                                            inputs.forEach(input => {
                                                                input.addEventListener('change', function() {
                                                                    const button = this.closest('.row').querySelector('.guardar-btn2');
                                                                    if (this.files.length > 0) {
                                                                        button.removeAttribute('disabled');
                                                                    } else {
                                                                        button.setAttribute('disabled', true);
                                                                    }
                                                                });
                                                            });
                                                        });
                                                    </script>
                                                </form>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10">NO HAY REGISTROS DE TIPO "ADJUNTO / RESPUESTA"</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="solicitudes-content-3" role="tabpanel" aria-labelledby="solicitudes-tab-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>Nro.</th>
                                            <th>Nivel Procedimiento</th>
                                            <th>Sub Procedimiento</th>
                                            <th>Tipo Carta</th>
                                            <th>Carta/Reclamo_Generada</th>
                                            <th>Doc. Carta Sellada</th>
                                            <th>Observación a Respuesta</th>
                                            <th>Nota Cite a Respuesta</th>
                                            <th>Fecha Cite a Respuesta</th>
                                            <th>Fecha Respuesta</th>
                                            <th>Documento Respuesta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($listacartas as $carta)
                                            <tr class="text-center">
                                                <td>{{ $carta->id }}
                                                    @if(!$carta->document4) 
                                                        <button class="btn btn-verdocumento btn-sm" type="button"
                                                                data-toggle="collapse"
                                                                data-target="#formExtra{{ $carta->id }}">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                                <td>{{ $carta->nro }}</td>
                                                <td>{{ $carta->nivelprocedimiento }}</td>
                                                <td>{{ $carta->subprocedimiento }}</td>
                                                <td>{{ $carta->tipocarta }}</td>
                                                <td>
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CARTAS Y RECLAMOS/{$carta->document}") }}"
                                                    class="btn btn-sm btn-verdocumento fas fa-eye"
                                                    title="VER ADJUNTO/RESPUESTA" target="_blank"></a>
                                                </td>
                                                <form action="{{ route('admin.tramites.guardarrespuestacarta', $cliente) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <td>
                                                        @if ($carta->document2)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CARTAS Y RECLAMOS/{$carta->document2}") }}"
                                                            class="btn btn-sm btn-verdocumento"
                                                            title="VER CARTA SELLADA" target="_blank"><i class="fas fa-eye"></i></a>
                                                        @else
                                                            <div class="row gx-2">
                                                                <div class="col">
                                                                    <input type="file" name="document2carta" 
                                                                        class="form-control form-control-sm" 
                                                                        accept="application/pdf">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="tramite_id" value="{{ $carta->id }}">
                                                        <input type="hidden" name="nombretramite" value="JUBILACIÓN">

                                                        @if ($carta->observaciones)
                                                            <div>{{ $carta->observaciones }}</div>
                                                        @else
                                                            <input type="text" name="observacionescarta" class="form-control form-control-sm" placeholder="Observación">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($carta->citenota)
                                                            <div>{{ $carta->citenota }}</div>
                                                        @else
                                                            <input type="text" name="citenotacarta" class="form-control form-control-sm" placeholder="Cite Nota">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($carta->fechacitenota)
                                                            <div>{{ $carta->fechacitenota }}</div>
                                                        @else
                                                            <input type="date" name="fechacitenotacarta" class="form-control form-control-sm">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($carta->fechainclusion)
                                                            <div>{{ $carta->fechainclusion }}</div>
                                                        @else
                                                            <input type="date" name="fechainclusioncarta" class="form-control form-control-sm">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($carta->document3)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CARTAS Y RECLAMOS/{$carta->document3}") }}"
                                                            class="btn btn-sm btn-verdocumento"
                                                            title="VER RESPUESTA" target="_blank"><i class="fas fa-eye"></i></a>
                                                        @else
                                                            <div class="row gx-2">
                                                                <div class="col">
                                                                    <input type="file" name="document3carta" 
                                                                        class="form-control form-control-sm archivo-input3" 
                                                                        accept="application/pdf">
                                                                </div>
                                                                <div class="col-auto">
                                                                    <button type="submit" class="btn btn-guardarnuevo guardar-btn3">
                                                                        <i class="fas fa-print"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const inputs = document.querySelectorAll('.archivo-input3');
                                                            inputs.forEach(input => {
                                                                input.addEventListener('change', function() {
                                                                    const button = this.closest('.row').querySelector('.guardar-btn3');
                                                                    if (this.files.length > 0) {
                                                                        button.removeAttribute('disabled');
                                                                    } else {
                                                                        button.setAttribute('disabled', true);
                                                                    }
                                                                });
                                                            });
                                                        });
                                                    </script>
                                                </form>
                                            </tr>
                                            @if($carta->document4)
                                                <tr class="bg-light">
                                                    <td colspan="13">
                                                        <div class="row g-2">
                                                            <div class="col-md-5">
                                                                <strong>Nombre de Formulario:</strong> FORMULARIO DE RECLAMO Y SUGERENCIA - SIP APS
                                                            </div>
                                                            <div class="col-md-3">
                                                                <strong>Nro. Formulario:</strong> {{ $carta->nroformulario ?? '-' }}
                                                            </div>
                                                            <div class="col-md-2">
                                                                <strong>Fecha:</strong> {{ $carta->fechaestadotramite ?? '-' }}
                                                            </div>
                                                            <div class="col-md-2">
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CARTAS Y RECLAMOS/{$carta->document4}") }}"
                                                                class="btn btn-sm btn-verdocumento"
                                                                target="_blank">
                                                                <i class="fas fa-eye"></i> Ver Formulario
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr class="collapse bg-light" id="formExtra{{ $carta->id }}">
                                                    <td colspan="13">
                                                        <form action="{{ route('admin.tramites.guardarrespuestacartaformulario', $cliente) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="tramite_id" value="{{ $carta->id }}">
                                                            <input type="hidden" name="nombretramite" value="JUBILACIÓN">

                                                            <div class="row g-2 align-items-end">
                                                                <div class="col-md-3">
                                                                    <input type="text" name="corsolicitudcarta" 
                                                                        class="form-control form-control-sm" 
                                                                        value="FORMULARIO DE RECLAMO Y SUGERENCIA - SIP APS" readonly>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="text" name="nroformulariocarta" class="form-control form-control-sm" placeholder="Nro. Formulario" required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input type="date" name="fechaestadotramitecarta" 
                                                                        class="form-control form-control-sm" required>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="file" name="document4carta" 
                                                                        class="form-control form-control-sm" 
                                                                        accept="application/pdf" required>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <button type="submit" class="btn btn-guardarnuevo btn-sm" title="GUARDAR FORMULARIO">
                                                                        <i class="fas fa-save"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="10">NO HAY REGISTROS DE TIPO "CARTA / RECLAMO"</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="solicitudes-content-4" role="tabpanel" aria-labelledby="solicitudes-tab-4">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>Nro.</th>
                                            <th>Nivel Procedimiento</th>
                                            <th>Sub Procedimiento</th>
                                            <th>Tipo Misiva</th>
                                            <th>Misiva_Generada</th>
                                            <th>Doc. Misiva Sellada</th>
                                            <th>Observación a Respuesta</th>
                                            <th>Nota Cite a Respuesta</th>
                                            <th>Fecha Cite a Respuesta</th>
                                            <th>Fecha Respuesta</th>
                                            <th>Documento Respuesta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($listamisivas as $misiva)
                                            <tr class="text-center">
                                                <td>{{ $misiva->id }}</td>
                                                <td>{{ $misiva->nro }}</td>
                                                <td>{{ $misiva->nivelprocedimiento }}</td>
                                                <td>{{ $misiva->subprocedimiento }}</td>
                                                <td>{{ $misiva->tipocarta }}</td>
                                                <td>
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/MISIVAS LIBRES/{$misiva->document}") }}"
                                                    class="btn btn-sm btn-verdocumento fas fa-eye"
                                                    title="VER MISIVA" target="_blank"></a>
                                                </td>
                                                <form action="{{ route('admin.tramites.guardarrespuestamisivalibre', $cliente) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <td>
                                                        @if ($misiva->document2)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/MISIVAS LIBRES/{$misiva->document2}") }}"
                                                            class="btn btn-sm btn-verdocumento"
                                                            title="VER MISIVA SELLADA" target="_blank"><i class="fas fa-eye"></i></a>
                                                        @else
                                                            <div class="row gx-2">
                                                                <div class="col">
                                                                    <input type="file" name="document2ml" 
                                                                        class="form-control form-control-sm" 
                                                                        accept="application/pdf">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="tramite_id" value="{{ $misiva->id }}">
                                                        <input type="hidden" name="nombretramite" value="JUBILACIÓN">

                                                        @if ($misiva->observaciones)
                                                            <div>{{ $misiva->observaciones }}</div>
                                                        @else
                                                            <input type="text" name="observacionesml" class="form-control form-control-sm" placeholder="Observación">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($misiva->citenota)
                                                            <div>{{ $misiva->citenota }}</div>
                                                        @else
                                                            <input type="text" name="citenotaml" class="form-control form-control-sm" placeholder="Cite Nota">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($misiva->fechacitenota)
                                                            <div>{{ $misiva->fechacitenota }}</div>
                                                        @else
                                                            <input type="date" name="fechacitenotaml" class="form-control form-control-sm">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($misiva->fechainclusion)
                                                            <div>{{ $misiva->fechainclusion }}</div>
                                                        @else
                                                            <input type="date" name="fechainclusionml" class="form-control form-control-sm">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($misiva->document3)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/MISIVAS LIBRES/{$misiva->document3}") }}"
                                                            class="btn btn-sm btn-verdocumento"
                                                            title="VER RESPUESTA" target="_blank"><i class="fas fa-eye"></i></a>
                                                        @else
                                                            <div class="row gx-2">
                                                                <div class="col">
                                                                    <input type="file" name="document3ml" 
                                                                        class="form-control form-control-sm archivo-input4" 
                                                                        accept="application/pdf">
                                                                </div>
                                                                <div class="col-auto">
                                                                    <button type="submit" class="btn btn-guardarnuevo guardar-btn4">
                                                                        <i class="fas fa-print"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const inputs = document.querySelectorAll('.archivo-input4');
                                                            inputs.forEach(input => {
                                                                input.addEventListener('change', function() {
                                                                    const button = this.closest('.row').querySelector('.guardar-btn4');
                                                                    if (this.files.length > 0) {
                                                                        button.removeAttribute('disabled');
                                                                    } else {
                                                                        button.setAttribute('disabled', true);
                                                                    }
                                                                });
                                                            });
                                                        });
                                                    </script>
                                                </form>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10">NO HAY REGISTROS DE TIPO "MISIVAS LIBRES"</td>
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
    </div>
</div>

<!-- CONTRATO -->
<div class="modal fade" id="modalDictamen" tabindex="-1" role="dialog" aria-labelledby="modalDictamenLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalDictamenLabel">CONTRATO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- NOTIFICACION DE CONTRATO --}}
                <form id="formTramite2" action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                    {!! Form::hidden('idtramite', $idTramite) !!}
                    @csrf
                    <div class="container">
                        @php
                            $dictamennotificacion = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                        @endphp
                        <div class="table-responsive">
                            <div class="scroll-shadow-wrapper">
                                <div class="scroll-shadow-container">
                                    <table class="table table-bordered table-sm align-middle text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 5%;">ID</th>
                                                <th style="width: 5%;">NRO.</th>
                                                <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                <th style="width: 5%;">FECHA_CITE_NOTIF.</th>
                                                <th style="width: 5%;">CITE_NOTIFICACIÓN</th>
                                                <th style="width: 5%;">FECHA_RECOJO</th>
                                                <th style="width: 5%;">NRO_CONTRATO</th>
                                                {{-- <th style="width: 10%;">RIESGO_ACEPTACIÓN/RECHAZO</th>
                                                <th style="width: 10%;">PORCENTAJE_RIESGO</th>
                                                <th style="width: 10%;">TIPO_RIESGO_CONTRATO</th>
                                                <th style="width: 5%;">¿ACCEDE_PENSIÓN?</th> --}}
                                                <th style="width: 5%;">MONTO_DEFINIDO</th>
                                                <th style="width: 5%;">APROB_CLIENTE</th>
                                                <th style="width: 5%;">FECHA_REGISTRO</th>
                                                <th style="width: 10%;">DOCUMENTO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dictamennotificacion as $documento)
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">NOTIFICACIÓN DE CONTRATO</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenotificacion)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->citenotificacion }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitanota)->format('d-m-Y') }} - {{ $documento->corsolicitud }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nrodictamen }}</p>
                                                    </td>
                                                    {{-- <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->riesgodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->porcentajeriesgodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->tiporiesgodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">
                                                            {{ $documento->accesopension }}
                                                            @if(!empty($documento->motivonopension))
                                                                - {{ $documento->motivonopension }}
                                                            @endif
                                                        </p>
                                                    </td> --}}
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->montocontrato ?? 'NO DEFINIDO' }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">
                                                            {{ $documento->estadodictamen }}
                                                            @if($documento->estadodictamen === 'RECHAZADO')
                                                                - SE DERIVÓ A APELACIÓN
                                                            @endif
                                                        </p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($dictamennotificacion->isEmpty())
                                                <tr class="fila-dictamennotificacion">
                                            @else
                                                <tr class="fila-dictamennotificacion d-none">
                                            @endif
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">NOTIFICACIÓN DE CONTRATO</p>
                                                    <input type="hidden" name="tramite[]" value="JUBILACIÓN">
                                                    <input type="hidden" name="nivelprocedimiento[]" value="CONTRATO">
                                                    <input type="hidden" name="subprocedimiento[]" value="NOTIFICACIÓN DE CONTRATO">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="citenotificacion[]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenota[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    <select class="form-control" name="corsolicitud[]">
                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                        <option value="RECOJO POR ASEGURADO">RECOJO POR ASEGURADO</option>
                                                        <option value="RECOJO POR APODERADO">RECOJO POR APODERADO</option>
                                                        <option value="PUBLICADO EN PRENSA">PUBLICADO EN PRENSA</option>
                                                    </select>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="nrodictamen[]">
                                                </td>
                                                {{-- <td class="align-middle text-center">
                                                    <select class="form-control riesgo" name="riesgodictamen[]">
                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                        <option value="RIESGO COMÚN">RIESGO COMÚN</option>
                                                        <option value="RIESGO LABORAL">RIESGO LABORAL</option>
                                                        <option value="RIESGO PROFESIONAL">RIESGO PROFESIONAL</option>
                                                    </select>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <div class="input-group">
                                                        <input type="number" class="form-control text-center porcentaje" name="porcentajeriesgodictamen[]" min="0" max="100">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <select class="form-control tipo" name="tiporiesgodictamen_disabled[]" disabled>
                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                        <option value="NO CALIFICA">NO CALIFICA</option>
                                                        <option value="PAGO GLOBAL">PAGO GLOBAL</option>
                                                        <option value="PAGO MENSUAL">PAGO MENSUAL</option>
                                                        <option value="GRAN JUBILACIÓN">GRAN JUBILACIÓN</option>
                                                    </select>
                                                    <input type="hidden" class="tipo-hidden" name="tiporiesgodictamen[]">
                                                </td>
                                                <script>
                                                    document.addEventListener('input', function(e) {
                                                        if (e.target.classList.contains('porcentaje') || e.target.classList.contains('riesgo')) {
                                                            const row = e.target.closest('tr');
                                                            const riesgo = row.querySelector('.riesgo').value;
                                                            const porcentajeInput = row.querySelector('.porcentaje');
                                                            const porcentaje = parseInt(porcentajeInput.value);
                                                            const tipoSelect = row.querySelector('.tipo');
                                                            const tipoHidden = row.querySelector('.tipo-hidden');

                                                            if (porcentaje < 0) {
                                                                porcentajeInput.value = 0;
                                                                return;
                                                            } else if (porcentaje > 100) {
                                                                porcentajeInput.value = 100;
                                                                return;
                                                            }

                                                            if (!riesgo || isNaN(porcentaje)) return;

                                                            let tipo = '';

                                                            if (riesgo === 'RIESGO PROFESIONAL') {
                                                                if (porcentaje <= 9) tipo = 'NO CALIFICA';
                                                                else if (porcentaje <= 25) tipo = 'PAGO GLOBAL';
                                                                else if (porcentaje <= 59) tipo = 'PAGO MENSUAL';
                                                                else tipo = 'GRAN JUBILACIÓN';
                                                            } else if (riesgo === 'RIESGO COMÚN' || riesgo === 'RIESGO LABORAL') {
                                                                if (porcentaje <= 49) tipo = 'NO CALIFICA';
                                                                else if (porcentaje <= 79) tipo = 'PAGO MENSUAL';
                                                                else tipo = 'GRAN JUBILACIÓN';
                                                            }
                                                            if (tipo) {
                                                                tipoSelect.value = tipo;
                                                                tipoHidden.value = tipo;
                                                            }
                                                        }
                                                    });
                                                </script>
                                                <td class="align-middle text-center">
                                                    <select class="form-control accesopension" name="accesopension[]">
                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                        <option value="SI">SI</option>
                                                        <option value="NO">NO</option>
                                                    </select>

                                                    <select class="form-control motivonopension" name="motivonopension[]" style="display:none;">
                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                        <option value="EXCESO DE 6 MESES">EXCESO DE 6 MESES</option>
                                                        <option value="FALTA DE COBERTURA">FALTA DE COBERTURA</option>
                                                    </select>
                                                </td>
                                                <script>
                                                    document.querySelectorAll('.accesopension').forEach(function(select) {
                                                        select.addEventListener('change', function() {
                                                            let motivoSelect = this.closest('td').querySelector('.motivonopension');
                                                            if (this.value === 'NO') {
                                                                motivoSelect.style.display = 'block';
                                                            } else {
                                                                motivoSelect.style.display = 'none';
                                                                motivoSelect.value = '';
                                                            }
                                                        });
                                                    });
                                                </script> --}}

                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="montocontrato[]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <select class="form-control" name="estadodictamen[]" onchange="mostrarMensaje(this)">
                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                        <option value="ACEPTADO">ACEPTADO</option>
                                                        <option value="RECHAZADO">RECHAZADO</option>
                                                    </select>
                                                    <div class="mensaje-apelacion text-danger mt-2" style="display:none;">
                                                        SE DERIVARÁ A APELACIÓN
                                                    </div>
                                                </td>
                                                <script>
                                                    function mostrarMensaje(select) {
                                                        const mensaje = select.parentElement.querySelector('.mensaje-apelacion');
                                                        if (select.value === 'NO') {
                                                            mensaje.style.display = 'block';
                                                        } else {
                                                            mensaje.style.display = 'none';
                                                        }
                                                    }
                                                </script>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                    @endphp
                                                    <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @if (!$dictamennotificacion->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregardictamennotificacion()">AGREGAR MÁS</button>
                                    </div>
                                    @endif
                                    <script>
                                        function agregardictamennotificacion() {
                                            const filaOculta = document.querySelector('.fila-dictamennotificacion.d-none');
                                            if (filaOculta) {
                                                filaOculta.classList.remove('d-none');
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                </form>

                {{-- @php
                    $dictamennotificacionexiste = $cliente->tramites()
                        ->where('nivelprocedimiento', 'CONTRATO')
                        ->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')
                        ->where('tramite', 'JUBILACIÓN')
                    ->first();
                @endphp
                @if (!$dictamennotificacionexiste)
                    <div class="d-flex justify-content-center" style="margin-top: 20px;">
                        <button type="button" class="btn btn-sm btn-subirarchivos" id="btnGuardarTodo" onclick="guardarAmbos()">GUARDAR CONTRATO</button>
                    </div>
                @endif
                <script>
                    function guardarAmbos() {
                        let btn = document.getElementById('btnGuardarTodo');
                        btn.disabled = true;
                        btn.innerText = 'Guardando...';

                        let form1 = document.getElementById('formTramite2');
                        let form2 = document.getElementById('formCriterios');

                        let data1 = new FormData(form1);
                        let data2 = new FormData(form2);

                        fetch(form1.action, {
                            method: 'POST',
                            body: data1,
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                        })
                        .then(() => {
                            return fetch(form2.action, {
                                method: 'POST',
                                body: data2,
                                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                            });
                        })
                        .then(() => {
                            alert('¡CONTRATO GUARDADO EXITOSAMENTE!');
                            location.reload();
                        })
                        .catch(err => {
                            console.error(err);
                            btn.disabled = false;
                            btn.innerText = 'GUARDAR TODO';
                        });
                    }
                </script> --}}
            </div>
        </div>
    </div>
</div>

<!-- CONTRATO ACEPTADO -->
<div class="modal fade" id="modalDictamenAceptado" tabindex="-1" role="dialog" aria-labelledby="modalDictamenAceptadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalDictamenAceptadoLabel">CONTRATO ACEPTADO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                    {!! Form::hidden('idtramite', $idTramite) !!}
                    @csrf
                    <div class="container">
                        @php
                            $documento29 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE REQUERIMIENTO')->where('tramite', 'JUBILACIÓN')->first();
                            $documento30 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ CARTA SOLICITUD A EMPLEADOR')->where('tramite', 'JUBILACIÓN')->first();
                            $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE MODIFICACIÓN DE CITE')->where('tramite', 'JUBILACIÓN')->first();

                            $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE MODIFICACIÓN DE CITE')->where('tramite', 'JUBILACIÓN')->first();
                            
                            $dictamenexiste = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('tramite', 'JUBILACIÓN')->first();
                            $existeexceso6mes = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('motivonopension', 'EXCESO DE 6 MESES')->first();
                            $existefaltacobertura = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('motivonopension', 'FALTA DE COBERTURA')->first();
                            $existeaccesopensionsi = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('estadodictamen', 'ACEPTADO')->first();

                            $dictamennotificacion = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();

                            $renunciarevdictamen = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE CONTRATO')
                                ->where('accesopension', 'SI')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                            $firmacontratodictamen = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'FIRMA DE CONTRATO')
                                ->where('accesopension', 'SI')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();

                            $renunciarevdictamenexceso6mes = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE CONTRATO (EXCESO DE SEIS MESES)')
                                ->where('accesopension', 'NO')
                                ->where('motivonopension', 'EXCESO DE 6 MESES')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                            $rechazosolinvdictamenexceso6mes = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN (EXCESO DE SEIS MESES)')
                                ->where('accesopension', 'NO')
                                ->where('motivonopension', 'EXCESO DE 6 MESES')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                            $formulariodictamenexceso6mes = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'FORMULARIO DE MODIFICACIÓN DE FECHA (EXCESO DE SEIS MESES)')
                                ->where('accesopension', 'NO')
                                ->where('motivonopension', 'EXCESO DE 6 MESES')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                            $firmacontratodictamenexceso6mes = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'FIRMA DE CONTRATO (EXCESO DE SEIS MESES)')
                                ->where('accesopension', 'NO')
                                ->where('motivonopension', 'EXCESO DE 6 MESES')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();

                            $renunciarevdictamenfaltacober = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE CONTRATO (FALTA DE COBERTURA)')
                                ->where('accesopension', 'NO')
                                ->where('motivonopension', 'FALTA DE COBERTURA')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                            $rechazosolinvdictamenfaltacober = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CONTRATO')
                                ->where('subprocedimiento', 'RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN (FALTA DE COBERTURA)')
                                ->where('accesopension', 'NO')
                                ->where('motivonopension', 'FALTA DE COBERTURA')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();

                            $fecha = \Carbon\Carbon::now();
                            // Determinar mes cierre
                            $mesCierre = $fecha->day <= 15 ? $fecha : $fecha->copy()->addMonth();
                            $mesCierreFormateado = $mesCierre->format('m/y');

                            // Mes cobro = mes siguiente a mes cierre
                            $mesCobro = $mesCierre->copy()->addMonth();
                            $mesCobroFormateado = $mesCobro->format('m/y');
                        @endphp
                        <div class="table-responsive">
                        {{-- CONTRATO SI ACCEDE A PENSION --}}
                            @if($existeaccesopensionsi)
                                {{-- RENUNCIA A REVISION DE CONTRATO --}}
                                <table class="table table-bordered table-sm align-middle text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 10%;">ID</th>
                                            <th style="width: 10%;">NRO.</th>
                                            <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                            <th style="width: 20%;">FECHA_REGISTRO</th>
                                            <th style="width: 20%;">FECHA_RETORNO</th>
                                            <th style="width: 20%;">FORMULARIO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($renunciarevdictamen as $documento)
                                            <tr>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->id }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->nro }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">RENUNCIA A REVISIÓN DE CONTRATO</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fecharetorno)->format('d-m-Y') }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($renunciarevdictamen->isEmpty())
                                            <tr class="fila-renunciarevdictamen">
                                        @else
                                            <tr class="fila-renunciarevdictamen d-none">
                                        @endif
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">RENUNCIA A REVISIÓN DE CONTRATO</p>
                                                <input type="hidden" name="tramite[renunciarevdictamen]" value="JUBILACIÓN">
                                                <input type="hidden" name="nivelprocedimiento[renunciarevdictamen]" value="CONTRATO">
                                                <input type="hidden" name="subprocedimiento[renunciarevdictamen]" value="RENUNCIA A REVISIÓN DE CONTRATO">
                                                <input type="hidden" name="accesopension[renunciarevdictamen]" value="SI">
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                    $fechaLimiteOriginal = \Carbon\Carbon::now()->addDays(5);
                                                    $fechaLimite = $fechaLimiteOriginal->copy();
                                                    if ($fechaLimite->isSaturday()) {
                                                        $fechaLimite->subDay();
                                                    } elseif ($fechaLimite->isSunday()) {
                                                        $fechaLimite->addDay();
                                                    }
                                                    $fechaLimiteStr = $fechaLimite->toDateString();
                                                @endphp
                                                <input type="date" class="form-control text-center" id="fechasubida11" name="fechasubida[renunciarevdictamen]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }} onchange="actualizarFechaRetorno11(this)">
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="date" class="form-control text-center" id="fecharetorno11" name="fecharetorno[renunciarevdictamen]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="file" name="archivo[renunciarevdictamen]" class="dropify mx-auto d-block" accept="application/pdf">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                @if (!$renunciarevdictamen->isEmpty())
                                <div class="text-left mt-2" style="margin-bottom: 10px;">
                                    <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarrenunciarevdictamen()">AGREGAR MÁS</button>
                                </div>
                                @endif
                                <script>
                                    function agregarrenunciarevdictamen() {
                                        const filaOculta = document.querySelector('.fila-renunciarevdictamen.d-none');
                                        if (filaOculta) {
                                            filaOculta.classList.remove('d-none');
                                        }
                                    }
                                </script>
                                {{-- FIRMA DE CONTRATO --}}
                                <div class="scroll-shadow-wrapper">
                                    <div class="scroll-shadow-container">
                                        <table class="table table-bordered table-sm align-middle text-center">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 5%;">ID</th>
                                                    <th style="width: 5%;">NRO.</th>
                                                    <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                    <th style="width: 5%;">CITE_NOTIFICACIÓN</th>
                                                    <th style="width: 5%;">FECHA_CITE_NOTIFICACIÓN</th>
                                                    <th style="width: 5%;">CITE_NOTA</th>
                                                    <th style="width: 5%;">FECHA_CITE_NOTA</th>
                                                    <th style="width: 5%;">MONTO_DEFINIDO</th>
                                                    <th style="width: 10%;">MES_CIERRE_PLANILLA</th>
                                                    <th style="width: 5%;">MES_COBRO</th>
                                                    <th style="width: 10%;">MES_RETROACTIVO</th>
                                                    <th style="width: 10%;">FECHA_REGISTRO</th>
                                                    <th style="width: 10%;">DOCUMENTO</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($firmacontratodictamen as $documento)
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->id }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->nro }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">FIRMA DE CONTRATO</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->citenotificacion }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenotificacion)->format('d-m-Y') }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->citenota }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenota)->format('d-m-Y') }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->montocontrato ?? 'NO DEFINIDO' }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->mescierre }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->mescobro }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $mescierreinicio }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if ($firmacontratodictamen->isEmpty())
                                                    <tr class="fila-firmacontratodictamen">
                                                @else
                                                    <tr class="fila-firmacontratodictamen d-none">
                                                @endif
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control" disabled>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control" disabled>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">FIRMA DE CONTRATO</p>
                                                        <input type="hidden" name="tramite[firmacontratodictamen]" value="JUBILACIÓN">
                                                        <input type="hidden" name="nivelprocedimiento[firmacontratodictamen]" value="CONTRATO">
                                                        <input type="hidden" name="subprocedimiento[firmacontratodictamen]" value="FIRMA DE CONTRATO">
                                                        <input type="hidden" name="accesopension[firmacontratodictamen]" value="SI">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control text-center" name="citenotificacion[firmacontratodictamen]">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="date" class="form-control text-center" name="fechacitenotificacion[firmacontratodictamen]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control text-center" name="citenota[firmacontratodictamen]">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="date" class="form-control text-center" name="fechacitenota[firmacontratodictamen]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control text-center" name="montocontrato[firmacontratodictamen]">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control text-center" name="mescierre[firmacontratodictamen]" value="{{ $mesCierreFormateado }}" readonly>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control text-center" name="mescobro[firmacontratodictamen]" value="{{ $mesCobroFormateado }}" readonly>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $mescierreinicio }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        @php
                                                            $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                        @endphp
                                                        <input type="date" class="form-control text-center" name="fechasubida[firmacontratodictamen]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="file" name="archivo[firmacontratodictamen]" class="dropify mx-auto d-block" accept="application/pdf">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        @if (!$firmacontratodictamen->isEmpty())
                                        <div class="text-left mt-2" style="margin-bottom: 10px;">
                                            <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarfirmacontratodictamen()">AGREGAR MÁS</button>
                                        </div>
                                        @endif
                                        <script>
                                            function agregarfirmacontratodictamen() {
                                                const filaOculta = document.querySelector('.fila-firmacontratodictamen.d-none');
                                                if (filaOculta) {
                                                    filaOculta.classList.remove('d-none');
                                                }
                                            }
                                        </script>
                                    </div>
                                </div>
                            @endif
                        
                        @if($existeexceso6mes)
                        {{-- CONTRATO NO ACCEDE A PENSION CON EXCESO DE SEIS MESES --}}
                            {{-- RENUNCIA A REVISION DE CONTRATO --}}
                            <div class="scroll-shadow-wrapper">
                                <div class="scroll-shadow-container">
                                    <table class="table table-bordered table-sm align-middle text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 10%;">ID</th>
                                                <th style="width: 10%;">NRO.</th>
                                                <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                                <th style="width: 10%;">FECHA_LÍMITE</th>
                                                <th style="width: 10%;">FECHA_FIRMA</th>
                                                <th style="width: 10%;">FECHA_REGISTRO</th>
                                                <th style="width: 10%;">NRO_FORM.</th>
                                                <th style="width: 20%;">FORMULARIO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($renunciarevdictamenexceso6mes as $documento)
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">RENUNCIA A REVISIÓN DE CONTRATO (EXCESO DE SEIS MESES)</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenota)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechainclusion)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nroformulario }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($renunciarevdictamenexceso6mes->isEmpty())
                                                <tr class="fila-renunciarevdictamenexceso6mes">
                                            @else
                                                <tr class="fila-renunciarevdictamenexceso6mes d-none">
                                            @endif
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">RENUNCIA A REVISIÓN DE CONTRATO (EXCESO DE SEIS MESES)</p>
                                                    <input type="hidden" name="tramite[renunciarevdictamenexceso6mes]" value="JUBILACIÓN">
                                                    <input type="hidden" name="nivelprocedimiento[renunciarevdictamenexceso6mes]" value="CONTRATO">
                                                    <input type="hidden" name="subprocedimiento[renunciarevdictamenexceso6mes]" value="RENUNCIA A REVISIÓN DE CONTRATO (EXCESO DE SEIS MESES)">
                                                    <input type="hidden" name="accesopension[renunciarevdictamenexceso6mes]" value="NO">
                                                    <input type="hidden" name="motivonopension[renunciarevdictamenexceso6mes]" value="EXCESO DE 6 MESES">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenota[renunciarevdictamenexceso6mes]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechainclusion[renunciarevdictamenexceso6mes]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechasubida[renunciarevdictamenexceso6mes]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="nroformulario[renunciarevdictamenexceso6mes]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="file" name="archivo[renunciarevdictamenexceso6mes]" class="dropify mx-auto d-block" accept="application/pdf">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @if (!$renunciarevdictamenexceso6mes->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarrenunciarevdictamenexceso6mes()">AGREGAR MÁS</button>
                                    </div>
                                    @endif
                                    <script>
                                        function agregarrenunciarevdictamenexceso6mes() {
                                            const filaOculta = document.querySelector('.fila-renunciarevdictamenexceso6mes.d-none');
                                            if (filaOculta) {
                                                filaOculta.classList.remove('d-none');
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                            {{-- RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN --}}
                            <div class="scroll-shadow-wrapper">
                                <div class="scroll-shadow-container">
                                    <table class="table table-bordered table-sm align-middle text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 10%;">ID</th>
                                                <th style="width: 10%;">NRO.</th>
                                                <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                                <th style="width: 5%;">CITE_NOTIFICACIÓN</th>
                                                <th style="width: 5%;">FECHA_CITE_NOTIF.</th>
                                                <th style="width: 15%;">FECHA_REGISTRO</th>
                                                <th style="width: 15%;">FECHA_RETORNO</th>
                                                <th style="width: 20%;">DOCUMENTO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rechazosolinvdictamenexceso6mes as $documento)
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN (EXCESO DE SEIS MESES)</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->citenotificacion }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenotificacion)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fecharetorno)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($rechazosolinvdictamenexceso6mes->isEmpty())
                                                <tr class="fila-rechazosolinvdictamenexceso6mes">
                                            @else
                                                <tr class="fila-rechazosolinvdictamenexceso6mes d-none">
                                            @endif
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN (EXCESO DE SEIS MESES)</p>
                                                    <input type="hidden" name="tramite[rechazosolinvdictamenexceso6mes]" value="JUBILACIÓN">
                                                    <input type="hidden" name="nivelprocedimiento[rechazosolinvdictamenexceso6mes]" value="CONTRATO">
                                                    <input type="hidden" name="subprocedimiento[rechazosolinvdictamenexceso6mes]" value="RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN (EXCESO DE SEIS MESES)">
                                                    <input type="hidden" name="accesopension[rechazosolinvdictamenexceso6mes]" value="NO">
                                                    <input type="hidden" name="motivonopension[rechazosolinvdictamenexceso6mes]" value="EXCESO DE 6 MESES">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="citenotificacion[rechazosolinvdictamenexceso6mes]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[rechazosolinvdictamenexceso6mes]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                    @endphp
                                                    <input type="date" class="form-control text-center" name="fechasubida[rechazosolinvdictamenexceso6mes]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fecharetorno[rechazosolinvdictamenexceso6mes]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="file" name="archivo[rechazosolinvdictamenexceso6mes]" class="dropify mx-auto d-block" accept="application/pdf">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @if (!$rechazosolinvdictamenexceso6mes->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarrechazosolinvdictamenexceso6mes()">AGREGAR MÁS</button>
                                    </div>
                                    @endif
                                    <script>
                                        function agregarrechazosolinvdictamenexceso6mes() {
                                            const filaOculta = document.querySelector('.fila-rechazosolinvdictamenexceso6mes.d-none');
                                            if (filaOculta) {
                                                filaOculta.classList.remove('d-none');
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                            {{-- FORMULARIO DE MODIFICACIÓN DE FECHA --}}
                            <table class="table table-bordered table-sm align-middle text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 10%;">ID</th>
                                        <th style="width: 10%;">NRO.</th>
                                        <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                        <th style="width: 15%;">FECHA_REGISTRO</th>
                                        <th style="width: 10%;">NRO_FORMULARIO</th>
                                        <th style="width: 20%;">FORMULARIO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($formulariodictamenexceso6mes as $documento)
                                        <tr>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ $documento->id }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ $documento->nro }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">FORMULARIO DE MODIFICACIÓN DE FECHA (EXCESO DE SEIS MESES)</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ $documento->nroformulario }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($formulariodictamenexceso6mes->isEmpty())
                                        <tr class="fila-formulariodictamenexceso6mes">
                                    @else
                                        <tr class="fila-formulariodictamenexceso6mes d-none">
                                    @endif
                                        <td class="align-middle text-center">
                                            <input type="text" class="form-control" disabled>
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="text" class="form-control" disabled>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="mb-0">FORMULARIO DE MODIFICACION DE FECHA (EXCESO DE SEIS MESES)</p>
                                            <input type="hidden" name="tramite[formulariodictamenexceso6mes]" value="JUBILACIÓN">
                                            <input type="hidden" name="nivelprocedimiento[formulariodictamenexceso6mes]" value="CONTRATO">
                                            <input type="hidden" name="subprocedimiento[formulariodictamenexceso6mes]" value="FORMULARIO DE MODIFICACIÓN DE FECHA (EXCESO DE SEIS MESES)">
                                            <input type="hidden" name="accesopension[formulariodictamenexceso6mes]" value="NO">
                                            <input type="hidden" name="motivonopension[formulariodictamenexceso6mes]" value="EXCESO DE 6 MESES">
                                        </td>
                                        <td class="align-middle text-center">
                                            @php
                                                $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                            @endphp
                                            <input type="date" class="form-control text-center" name="fechasubida[formulariodictamenexceso6mes]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="text" class="form-control text-center" name="nroformulario[formulariodictamenexceso6mes]">
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="file" name="archivo[formulariodictamenexceso6mes]" class="dropify mx-auto d-block" accept="application/pdf">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @if (!$formulariodictamenexceso6mes->isEmpty())
                            <div class="text-left mt-2" style="margin-bottom: 10px;">
                                <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarformulariodictamenexceso6mes()">AGREGAR MÁS</button>
                            </div>
                            @endif
                            <script>
                                function agregarformulariodictamenexceso6mes() {
                                    const filaOculta = document.querySelector('.fila-formulariodictamenexceso6mes.d-none');
                                    if (filaOculta) {
                                        filaOculta.classList.remove('d-none');
                                    }
                                }
                            </script>
                            {{-- FIRMA DE CONTRATO --}}
                            <div class="scroll-shadow-wrapper">
                                <div class="scroll-shadow-container">
                                    <table class="table table-bordered table-sm align-middle text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 5%;">ID</th>
                                                <th style="width: 5%;">NRO.</th>
                                                <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                <th style="width: 5%;">CITE_NOTIFICACIÓN</th>
                                                <th style="width: 5%;">FECHA_CITE_NOTIFICACIÓN</th>
                                                <th style="width: 5%;">CITE_NOTA</th>
                                                <th style="width: 5%;">FECHA_CITE_NOTA</th>
                                                <th style="width: 10%;">MONTO_DEFINIDO</th>
                                                <th style="width: 10%;">MES_CIERRE_PLANILLA</th>
                                                <th style="width: 5%;">MES_COBRO</th>
                                                <th style="width: 10%;">MES_RETROACTIVO</th>
                                                <th style="width: 10%;">FECHA_REGISTRO</th>
                                                <th style="width: 10%;">DOCUMENTO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($firmacontratodictamenexceso6mes as $documento)
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">FIRMA DE CONTRATO (EXCESO DE SEIS MESES)</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->citenotificacion }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenotificacion)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->citenota }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenota)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->montocontrato ?? 'NO DEFINIDO' }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->mescierre }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->mescobro }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $mescierreinicio }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($firmacontratodictamenexceso6mes->isEmpty())
                                                <tr class="fila-firmacontratodictamenexceso6mes">
                                            @else
                                                <tr class="fila-firmacontratodictamenexceso6mes d-none">
                                            @endif
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">FIRMA DE CONTRATO (EXCESO DE SEIS MESES)</p>
                                                    <input type="hidden" name="tramite[firmacontratodictamenexceso6mes]" value="JUBILACIÓN">
                                                    <input type="hidden" name="nivelprocedimiento[firmacontratodictamenexceso6mes]" value="CONTRATO">
                                                    <input type="hidden" name="subprocedimiento[firmacontratodictamenexceso6mes]" value="FIRMA DE CONTRATO (EXCESO DE SEIS MESES)">
                                                    <input type="hidden" name="accesopension[firmacontratodictamenexceso6mes]" value="NO">
                                                    <input type="hidden" name="motivonopension[firmacontratodictamenexceso6mes]" value="EXCESO DE 6 MESES">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="citenotificacion[firmacontratodictamenexceso6mes]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[firmacontratodictamenexceso6mes]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="citenota[firmacontratodictamenexceso6mes]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenota[firmacontratodictamenexceso6mes]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="montocontrato[firmacontratodictamenexceso6mes]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="mescierre[firmacontratodictamenexceso6mes]" value="{{ $mesCierreFormateado }}" readonly>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="mescobro[firmacontratodictamenexceso6mes]" value="{{ $mesCobroFormateado }}" readonly>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $mescierreinicio }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                    @endphp
                                                    <input type="date" class="form-control text-center" name="fechasubida[firmacontratodictamenexceso6mes]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="file" name="archivo[firmacontratodictamenexceso6mes]" class="dropify mx-auto d-block" accept="application/pdf">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @if (!$firmacontratodictamenexceso6mes->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarfirmacontratodictamenexceso6mes()">AGREGAR MÁS</button>
                                    </div>
                                    @endif
                                    <script>
                                        function agregarfirmacontratodictamenexceso6mes() {
                                            const filaOculta = document.querySelector('.fila-firmacontratodictamenexceso6mes.d-none');
                                            if (filaOculta) {
                                                filaOculta.classList.remove('d-none');
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                        @endif

                        @if($existefaltacobertura)
                        {{-- CONTRATO NO ACCEDE A PENSION CON FALTA DE COBERTURA --}}
                            {{-- RENUNCIA A REVISION DE CONTRATO --}}
                            <div class="scroll-shadow-wrapper">
                                <div class="scroll-shadow-container">
                                    <table class="table table-bordered table-sm align-middle text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 10%;">ID</th>
                                                <th style="width: 10%;">NRO.</th>
                                                <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                                <th style="width: 5%;">CITE_NOTIFICACIÓN</th>
                                                <th style="width: 5%;">FECHA_CITE_NOTIF.</th>
                                                <th style="width: 15%;">FECHA_REGISTRO</th>
                                                <th style="width: 15%;">FECHA_RETORNO</th>
                                                <th style="width: 20%;">FORMULARIO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($renunciarevdictamenfaltacober as $documento)
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">RENUNCIA A REVISIÓN DE CONTRATO (FALTA DE COBERTURA)</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->citenotificacion }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenotificacion)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fecharetorno)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($renunciarevdictamenfaltacober->isEmpty())
                                                <tr class="fila-renunciarevdictamenfaltacober">
                                            @else
                                                <tr class="fila-renunciarevdictamenfaltacober d-none">
                                            @endif
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">RENUNCIA A REVISIÓN DE CONTRATO (FALTA DE COBERTURA)</p>
                                                    <input type="hidden" name="tramite[renunciarevdictamenfaltacober]" value="JUBILACIÓN">
                                                    <input type="hidden" name="nivelprocedimiento[renunciarevdictamenfaltacober]" value="CONTRATO">
                                                    <input type="hidden" name="subprocedimiento[renunciarevdictamenfaltacober]" value="RENUNCIA A REVISIÓN DE CONTRATO (FALTA DE COBERTURA)">
                                                    <input type="hidden" name="accesopension[renunciarevdictamenfaltacober]" value="NO">
                                                    <input type="hidden" name="motivonopension[renunciarevdictamenfaltacober]" value="FALTA DE COBERTURA">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="citenotificacion[renunciarevdictamenfaltacober]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[renunciarevdictamenfaltacober]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                    @endphp
                                                    <input type="date" class="form-control text-center" name="fechasubida[renunciarevdictamenfaltacober]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fecharetorno[renunciarevdictamenfaltacober]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="file" name="archivo[renunciarevdictamenfaltacober]" class="dropify mx-auto d-block" accept="application/pdf">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @if (!$renunciarevdictamenfaltacober->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarrenunciarevdictamenfaltacober()">AGREGAR MÁS</button>
                                    </div>
                                    @endif
                                    <script>
                                        function agregarrenunciarevdictamenfaltacober() {
                                            const filaOculta = document.querySelector('.fila-renunciarevdictamenfaltacober.d-none');
                                            if (filaOculta) {
                                                filaOculta.classList.remove('d-none');
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                            {{-- RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN --}}
                            <div class="scroll-shadow-wrapper">
                                <div class="scroll-shadow-container">
                                    <table class="table table-bordered table-sm align-middle text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 10%;">ID</th>
                                                <th style="width: 10%;">NRO.</th>
                                                <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                                <th style="width: 5%;">CITE_NOTIFICACIÓN</th>
                                                <th style="width: 5%;">FECHA_CITE_NOTIFICACIÓN</th>
                                                <th style="width: 5%;">CITE_NOTA</th>
                                                <th style="width: 5%;">FECHA_CITE_NOTA</th>
                                                <th style="width: 10%;">FECHA_REGISTRO</th>
                                                <th style="width: 10%;">FECHA_RETORNO</th>
                                                <th style="width: 10%;">ACCESO_TRÁMITE</th>
                                                <th style="width: 10%;">DOCUMENTO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rechazosolinvdictamenfaltacober as $documento)
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN (FALTA DE COBERTURA)</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->citenotificacion }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenotificacion)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->citenota }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenota)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fecharetorno)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($rechazosolinvdictamenfaltacober->isEmpty())
                                                <tr class="fila-rechazosolinvdictamenfaltacober">
                                            @else
                                                <tr class="fila-rechazosolinvdictamenfaltacober d-none">
                                            @endif
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN (FALTA DE COBERTURA)</p>
                                                    <input type="hidden" name="tramite[rechazosolinvdictamenfaltacober]" value="JUBILACIÓN">
                                                    <input type="hidden" name="nivelprocedimiento[rechazosolinvdictamenfaltacober]" value="CONTRATO">
                                                    <input type="hidden" name="subprocedimiento[rechazosolinvdictamenfaltacober]" value="RECHAZO DE SOLICITUD DE PENSIÓN POR JUBILACIÓN (FALTA DE COBERTURA)">
                                                    <input type="hidden" name="accesopension[rechazosolinvdictamenfaltacober]" value="NO">
                                                    <input type="hidden" name="motivonopension[rechazosolinvdictamenfaltacober]" value="FALTA DE COBERTURA">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="citenotificacion[rechazosolinvdictamenfaltacober]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[rechazosolinvdictamenfaltacober]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="citenota[rechazosolinvdictamenfaltacober]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenota[rechazosolinvdictamenfaltacober]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                    @endphp
                                                    <input type="date" class="form-control text-center" name="fechasubida[rechazosolinvdictamenfaltacober]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fecharetorno[rechazosolinvdictamenfaltacober]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <select class="form-control accedepension" name="opcioncorsolicitud[rechazosolinvdictamenfaltacober]">
                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                        <option value="SI">SI</option>
                                                        <option value="NO">NO</option>
                                                    </select>
                                                    <select class="form-control opcionaccedepension" name="corsolicitud[rechazosolinvdictamenfaltacober]">
                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                        <option value="JUBILACIÓN">JUBILACIÓN</option>
                                                        <option value="RETIRO DE APORTES PARCIAL">RETIRO DE APORTES PARCIAL</option>
                                                        <option value="RETIRO DE APORTES TOTAL">RETIRO DE APORTES TOTAL</option>
                                                        <option value="CLIENTE JUBILADO">CLIENTE JUBILADO</option>
                                                    </select>
                                                </td>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function () {
                                                        document.querySelectorAll('.accedepension').forEach(function(select) {
                                                            select.addEventListener('change', function () {
                                                                const opcionaccedepension = this.closest('td').querySelector('.opcionaccedepension');
                                                                if (!opcionaccedepension.dataset.originalOptions) {
                                                                    opcionaccedepension.dataset.originalOptions = opcionaccedepension.innerHTML;
                                                                }
                                                                opcionaccedepension.innerHTML = opcionaccedepension.dataset.originalOptions;
                                                                if (this.value === 'SI') {
                                                                    opcionaccedepension.querySelectorAll('option').forEach(opt => {
                                                                        if (
                                                                            opt.value !== '' &&
                                                                            opt.value !== 'JUBILACIÓN' &&
                                                                            opt.value !== 'RETIRO DE APORTES PARCIAL' &&
                                                                            opt.value !== 'RETIRO DE APORTES TOTAL'
                                                                        ) {
                                                                            opt.remove();
                                                                        }
                                                                    });
                                                                } else if (this.value === 'NO') {
                                                                    opcionaccedepension.querySelectorAll('option').forEach(opt => {
                                                                        if (
                                                                            opt.value !== '' &&
                                                                            opt.value !== 'CLIENTE JUBILADO'
                                                                        ) {
                                                                            opt.remove();
                                                                        }
                                                                    });
                                                                }
                                                                opcionaccedepension.value = '';
                                                            });
                                                        });
                                                    });
                                                </script>
                                                <td class="align-middle text-center">
                                                    <input type="file" name="archivo[rechazosolinvdictamenfaltacober]" class="dropify mx-auto d-block" accept="application/pdf">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @if (!$rechazosolinvdictamenfaltacober->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarrechazosolinvdictamenfaltacober()">AGREGAR MÁS</button>
                                    </div>
                                    @endif
                                    <script>
                                        function agregarrechazosolinvdictamenfaltacober() {
                                            const filaOculta = document.querySelector('.fila-rechazosolinvdictamenfaltacober.d-none');
                                            if (filaOculta) {
                                                filaOculta.classList.remove('d-none');
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                        @endif
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CONTRATO RECHAZADO -->
<div class="modal fade" id="modalDictamenRechazado" tabindex="-1" role="dialog" aria-labelledby="modalDictamenRechazadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalDictamenRechazadoLabel">CONTRATO RECHAZADO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', auth()->user()->name) !!}
                    @csrf
                    <br>
                    <div class="container">
                        <div class="row mb-3 titulos">
                            <div class="col-md-4 text-center">
                                <strong>SUB PROCEDIMIENTO</strong>
                            </div>
                            <div class="col-md-4 text-center">
                                <strong>FECHA DE SUBIDA</strong>
                            </div>
                            <div class="col-md-4 text-center">
                                <strong>DOCUMENTO</strong>
                            </div>
                        </div><br>
                        @php
                            $documento48 = $cliente->tramites()->where('subprocedimiento', 'INICIO PROCESO DE APELACIÓN')->where('tramite', 'JUBILACIÓN')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento48 ? 'no-documento' : '' }}">
                            <div class="col-md-4 text-center">
                                <p class="mb-0">ABRIR PROCESO DE APELACIÓN</p>
                                <input type="text" class="form-control" id="tramite1" name="tramite[]" value="JUBILACIÓN" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="CONTRATO" hidden>
                                <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="INICIO PROCESO DE APELACIÓN" hidden>
                            </div>
                            <div class="col-md-4 text-center">
                                @php
                                    $documento48 = $cliente->tramites()->where('subprocedimiento', 'INICIO PROCESO DE APELACIÓN')->where('tramite', 'JUBILACIÓN')->first();
                                @endphp
                                @if ($documento48)
                                    <p class="mb-0">{{ $documento48->fechasubida }}</p>
                                @else
                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-4 text-center">
                                @if ($documento48)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento48->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>
                    </div>
                    @php
                        $documento48 = $cliente->tramites()->where('subprocedimiento', 'INICIO PROCESO DE APELACIÓN')->where('tramite', 'JUBILACIÓN')->first();
                    @endphp
                    @if (!$documento48)
                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Iniciar proceso de Apelación</button>
                    @endif


                   {{--  @php
                        $documento41 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->first();
                    @endphp

                    @if ($documento41 && !in_array($documento41->estadodictamen, ['ACEPTADO', 'RECHAZADO'])) --}}
                        <button type="submit" name="volver_programar" class="btn btn-subirarchivos d-block mx-auto mb-3" style="width: fit-content;">Volver a programar</button>
                    {{-- @endif --}}
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SEGUIMIENTO DE PROCESO --}}
<div class="modal fade" id="modalseguimientoproceso" tabindex="-1" role="dialog" aria-labelledby="modalseguimientoprocesoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalseguimientoprocesoLabel">SEGUIMIENTO DE PROCESO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="seguimientoForm" action="{{ route('admin.tramites.guardartramitesclienteitaseguimiento', $cliente) }}" method="POST" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', auth()->user()->name) !!}
                    @csrf
                    <br>
                    <div class="container">
                        <div class="row mb-3 titulos">
                            <div class="col-md-3 text-center">
                                <strong>SEGUIMIENTO</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>FECHA</strong>
                            </div>
                            <div class="col-md-6 text-center">
                                <strong>NOTA</strong>
                            </div>
                        </div>

                        @php
                            $seguimientos = $cliente->tramites()
                                ->where('nivelprocedimiento', 'SEGUIMIENTO')
                                ->where('tramite', 'JUBILACIÓN')
                                ->get();
                        @endphp
                        
                        @foreach ($seguimientos as $index => $tramite)
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3 text-center">
                                    <p class="mb-0">{{ $tramite->subprocedimiento }}</p>
                                </div>
                                <div class="col-md-3 text-center">
                                    <p class="mb-0">{{ $tramite->fechasubida }}</p>
                                </div>
                                <div class="col-md-6 text-center">
                                    <p class="mb-0">{{ $tramite->notaseguimiento }}</p>
                                </div>
                            </div>
                        @endforeach

                        <div class="row mb-3 align-items-center no-documento">
                            <div class="col-md-3 text-center" hidden>
                                <input type="text" class="form-control" name="tramite[]" value="JUBILACIÓN" hidden>
                                <input type="text" class="form-control" name="nivelprocedimiento[]" value="SEGUIMIENTO" hidden>
                            </div>
                            <div class="col-md-3 text-center">
                                <input type="text" class="form-control" name="subprocedimiento[]" required>
                            </div>
                            <div class="col-md-3 text-center">
                                <input type="date" class="form-control" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            </div>
                            <div class="col-md-6 text-center">
                                <input type="text" class="form-control" name="notaseguimiento[]" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Registrar seguimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- COMUNICACIONES --}}
<div class="modal fade" id="modalcomunicaciones" tabindex="-1" role="dialog" aria-labelledby="modalcomunicacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalcomunicacionesLabel">COMUNICACIONES</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <style>
                .tabla-comunicaciones th,
                .tabla-comunicaciones td {
                    white-space: normal !important;
                    word-wrap: break-word;
                    word-break: break-word;
                }
            </style>
            <div class="modal-body">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="tabs-comunicacion">
                        <li class="nav-item">
                            <a class="nav-link active" id="comunicacion-tab-1" data-toggle="tab" href="#comunicacion-content-1" role="tab" aria-controls="comunicacion-content-1" aria-selected="true">COMUNICACIÓN PROCESO</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="comunicacion-tab-2" data-toggle="tab" href="#comunicacion-content-2" role="tab" aria-controls="comunicacion-content-2" aria-selected="false">COMUNICACIÓN SEGUIMIENTO</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="tabs-comunicacion-contenido">
                        <div class="tab-pane fade show active" id="comunicacion-content-1" role="tabpanel" aria-labelledby="comunicacion-tab-1">
                            <div class="table-responsive">
                                <div class="scroll-shadow-wrapper">
                                    <div class="scroll-shadow-container">
                                        <table class="table table-striped table-bordered align-middle text-center table-sm tabla-comunicaciones" style="table-layout: fixed; width: 100%;">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th style="width: 150px;">TIPO</th>
                                                    <th style="width: 250px;">NIVEL_PROCEDIMIENTO</th>
                                                    <th style="width: 250px;">SUB_PROCEDIMIENTO</th>
                                                    <th style="width: 120px;">COMUNICAR</th>
                                                    <th style="width: 250px;">USUARIO_EMISOR</th>
                                                    <th style="width: 250px;">USUARIO_RECEPTOR</th>
                                                    <th style="width: 130px;">MODO_COMUNIC.</th>
                                                    <th style="width: 150px;">TIPO_INTERACCIÓN</th>
                                                    <th style="width: 300px;">DETALLE_COMUNICACIÓN</th>
                                                    <th style="width: 120px;">TIPO_ENTREGA</th>
                                                    <th style="width: 140px;">TIPO_DOCUMENTO</th>
                                                    <th style="width: 200px;">CAPTURA_COMUNIC.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $subprocedimientosEspeciales = [
                                                        'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS',
                                                        'RECHAZO DE DOCUMENTOS EXTRANJEROS',
                                                        'CORRECCIÓN DE DOCUMENTOS EXTRANJEROS',
                                                    ];
                                                    $agrupados = collect();

                                                    foreach ($procedimientotramites as $pt) {
                                                        if (in_array($pt->subprocedimiento, $subprocedimientosEspeciales)) {
                                                            $clave = $pt->subprocedimiento . '_' . \Carbon\Carbon::parse($pt->created_at)->format('Y-m-d');
                                                            if (!$agrupados->has($clave)) {
                                                                $agrupados->put($clave, $pt);
                                                            }
                                                        } else {
                                                            $agrupados->put(uniqid(), $pt);
                                                        }
                                                    }
                                                    $filtrados = $agrupados->values();
                                                @endphp

                                                @foreach ($filtrados as $procedimientotramite)
                                                    <tr>
                                                        <td class="align-middle text-center" style="width: 150px;">{{ $procedimientotramite->tipo }}</td>
                                                        <td class="align-middle text-center" style="width: 250px;">{{ $procedimientotramite->nivelprocedimiento }}</td>
                                                        <td class="align-middle text-center" style="width: 250px;">{{ $procedimientotramite->subprocedimiento }}{{ $procedimientotramite->tipocarta ? ' - ' . $procedimientotramite->tipocarta : '' }}</td>
                                                        <td class="align-middle text-center" style="width: 120px;">
                                                            @if ($procedimientotramite->estadocomunicado !== 'COMUNICADO')
                                                                <a href="{{ route('tramites.actualizarEstado', ['id' => $procedimientotramite->id, 'clienteId' => $cliente->id]) }}" class="btn btn-sm btn-comunicar" target="_blank">COMUNICAR</a>
                                                            @else
                                                                <span class="badge text-white px-2 py-1" style="background-color: #94c93b; font-size: 0.8rem;">
                                                                    COMUNICADO
                                                                </span>
                                                            @endif
                                                        </td>

                                                        @if ($procedimientotramite->capturacomunicacion)
                                                            <td class="align-middle text-center">{{ $procedimientotramite->comusuemisor }}</td>
                                                            <td class="align-middle text-center">{{ $procedimientotramite->comusureceptor }}</td>
                                                            <td class="align-middle text-center">{{ $procedimientotramite->commodo }}</td>
                                                            <td class="align-middle text-center">{{ $procedimientotramite->comtipointerac }}</td>
                                                            <td class="align-middle text-center">{{ $procedimientotramite->comdetalle }}</td>
                                                            <td class="align-middle text-center">{{ $procedimientotramite->comtipoentrega }}</td>
                                                            <td class="align-middle text-center">{{ $procedimientotramite->comtipodoc }}</td>
                                                            <td class="align-middle text-center">
                                                                @if($procedimientotramite->capturacomunicacion == 'VACIO')
                                                                    <span class="badge bg-warning text-dark">VACÍO</span>
                                                                @else
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/COMUNICACIONES/{$procedimientotramite->capturacomunicacion}") }}"
                                                                    class="btn btn-sm btn-verdocumento"
                                                                    target="_blank">
                                                                    VER CAPTURA
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        @else
                                                            <td class="align-middle text-center" colspan="8">
                                                                <form action="{{ route('tramites.subirArchivo', ['id' => $procedimientotramite->id, 'clienteId' => $cliente->id]) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                                                                    @csrf
                                                                    <input type="hidden" name="tramitenombre" value="JUBILACIÓN">
                                                                    <select class="form-control form-control-sm" name="comusuemisor" style="width: 240px;">
                                                                        <option value="">Selecciona una opción...</option>
                                                                        @foreach ($apoderadosList as $apoderado)
                                                                            <option value="{{ $apoderado }}">{{ $apoderado }}</option>
                                                                        @endforeach
                                                                        <option value="{{ $cliente->nombrecompleto }}">{{ $cliente->nombrecompleto }}</option>
                                                                    </select>
                                                                    <select class="form-control form-control-sm" name="comusureceptor" style="width: 250px;">
                                                                        <option value="">Selecciona una opción...</option>
                                                                        @foreach ($contactos as $nombrecontacto)
                                                                            <option value="{{ $nombrecontacto }}">{{ $nombrecontacto }}</option>
                                                                        @endforeach
                                                                        <option value="{{ $cliente->nombrecompleto }}">{{ $cliente->nombrecompleto }}</option>
                                                                    </select>

                                                                    <select class="form-control form-control-sm" name="commodo" style="width: 130px;">
                                                                        <option value="">Selecciona una opción...</option>
                                                                        <option value="MENSAJE">MENSAJE</option>
                                                                        <option value="LLAMADA">LLAMADA</option>
                                                                        <option value="EMAIL">EMAIL</option>
                                                                        <option value="VISITA">VISITA</option>
                                                                    </select>

                                                                    <select class="form-control form-control-sm" name="comtipointerac" style="width: 150px;">
                                                                        <option value="">Selecciona una opción...</option>
                                                                        <option value="ENTRANTE">ENTRANTE</option>
                                                                        <option value="SALIENTE">SALIENTE</option>
                                                                        <option value="PENDIENTE">PENDIENTE</option>
                                                                    </select>

                                                                    <input type="text" class="form-control form-control-sm" name="comdetalle" style="width: 300px;" placeholder="Escriba un detalle breve...">

                                                                    <select class="form-control form-control-sm" name="comtipoentrega" style="width: 120px;">
                                                                        <option value="">Selecciona una opción...</option>
                                                                        <option value="DIGITAL">DIGITAL</option>
                                                                        <option value="PRESENCIAL">PRESENCIAL</option>
                                                                        <option value="DELIVERY">DELIVERY</option>
                                                                        <option value="TRANSPORTE AÉREO">TRANSPORTE AÉREO</option>
                                                                        <option value="TRANSPORTE TERRESTRE">TRANSPORTE TERRESTRE</option>
                                                                    </select>

                                                                    <select class="form-control form-control-sm" name="comtipodoc" style="width: 140px;">
                                                                        <option value="">Selecciona una opción...</option>
                                                                        <option value="ORIGINAL">ORIGINAL</option>
                                                                        <option value="COPIA">COPIA</option>
                                                                    </select>
                                                                    <div class="input-especial">
                                                                        <input type="file" name="documento" class="form-control form-control-sm dropify" accept=".jpg,.jpeg,.png" style="width: 150px;" required>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-sm btn-subircaptura" title="GUARDAR"><i class="fas fa-print"></i></button>
                                                                </form>
                                                                <style>
                                                                    .input-especial .dropify-wrapper {
                                                                        height: 32px !important;
                                                                        width: 150px !important;
                                                                    }
                                                                </style>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="comunicacion-content-2" role="tabpanel" aria-labelledby="comunicacion-tab-2">
                            <div class="table-responsive">
                                <div class="scroll-shadow-wrapper">
                                    <div class="scroll-shadow-container">
                                        <form action="{{ route('admin.tramites.guardarseguimientoclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                                            {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                            {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                            {!! Form::hidden('clienteid', $cliente->id) !!}
                                            {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                                            {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                                            {!! Form::hidden('idtramite', $idTramite) !!}
                                            @csrf
                                            <table class="table table-striped table-bordered align-middle text-center table-sm tabla-comunicaciones" style="table-layout: fixed; width: 100%;">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th style="width: 400px;">DETALLE</th>
                                                        <th style="width: 200px;">MODO_COMUNIC.</th>
                                                        <th style="width: 300px;">USUARIO_EMISOR</th>
                                                        <th style="width: 300px;">USUARIO_RECEPTOR</th>
                                                        <th style="width: 200px;">TIPO_INTERACCIÓN</th>
                                                        <th style="width: 200px;">TIPO_ENTREGA</th>
                                                        <th style="width: 200px;">TIPO_DOCUMENTO</th>
                                                        <th style="width: 200px;">CAPTURA_COMUNIC.</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($comseguimientos as $seguimiento)
                                                        <tr>
                                                            <td>{{ $seguimiento->comdetalle }}</td>
                                                            <td>{{ $seguimiento->commodo }}</td>
                                                            <td>{{ $seguimiento->comusuemisor }}</td>
                                                            <td>{{ $seguimiento->comusureceptor }}</td>
                                                            <td>{{ $seguimiento->comtipointerac }}</td>
                                                            <td>{{ $seguimiento->comtipoentrega }}</td>
                                                            <td>{{ $seguimiento->comtipodoc ?? 0 }}</td>
                                                            <td class="align-middle text-center">
                                                                @if(!$seguimiento->capturacomunicacion)
                                                                    <span class="badge bg-warning text-dark">VACÍO</span>
                                                                @else
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/COMUNICACIONES/{$seguimiento->capturacomunicacion}") }}"
                                                                        class="btn btn-sm btn-verdocumento"
                                                                        target="_blank">VER CAPTURA</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="comtramitenombre" value="JUBILACIÓN">
                                                            <input type="text" name="comdetalle" class="form-control form-control-sm" placeholder="Escriba un motivo..." requerid>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm" name="commodo2" requerid>
                                                                <option value="">Selecciona una opción...</option>
                                                                <option value="MENSAJE">MENSAJE</option>
                                                                <option value="LLAMADA">LLAMADA</option>
                                                                <option value="EMAIL">EMAIL</option>
                                                                <option value="VISITA">VISITA</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input list="lista_emisores" 
                                                                name="comusuemisor2" 
                                                                class="form-control form-control-sm" 
                                                                placeholder="Escriba o seleccione..." requerid>

                                                            <datalist id="lista_emisores">
                                                                @foreach ($provintext as $razonsocial)
                                                                    <option value="{{ $razonsocial }}">
                                                                @endforeach
                                                                <option value="{{ $cliente->nombrecompleto }}">
                                                                @foreach ($contactos as $nombrecontacto)
                                                                    <option value="{{ $nombrecontacto }}">
                                                                @endforeach
                                                            </datalist>
                                                        </td>
                                                        <td>
                                                            <input list="lista_receptores" 
                                                                name="comusureceptor2" 
                                                                class="form-control form-control-sm" 
                                                                placeholder="Escriba o seleccione..." requerid>

                                                            <datalist id="lista_receptores">
                                                                @foreach ($provintext as $razonsocial)
                                                                    <option value="{{ $razonsocial }}">
                                                                @endforeach
                                                                <option value="{{ $cliente->nombrecompleto }}">
                                                                @foreach ($contactos as $nombrecontacto)
                                                                    <option value="{{ $nombrecontacto }}">
                                                                @endforeach
                                                            </datalist>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm" name="comtipointerac2" requerid>
                                                                <option value="">Selecciona una opción...</option>
                                                                <option value="ENTRANTE">ENTRANTE</option>
                                                                <option value="SALIENTE">SALIENTE</option>
                                                                <option value="PENDIENTE">PENDIENTE</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm" name="comtipoentrega2" requerid>
                                                                <option value="">Selecciona una opción...</option>
                                                                <option value="DIGITAL">DIGITAL</option>
                                                                <option value="PRESENCIAL">PRESENCIAL</option>
                                                                <option value="DELIVERY">DELIVERY</option>
                                                                <option value="TRANSPORTE AÉREO">TRANSPORTE AÉREO</option>
                                                                <option value="TRANSPORTE TERRESTRE">TRANSPORTE TERRESTRE</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm" name="comtipodoc2">
                                                                <option value="">Selecciona una opción...</option>
                                                                <option value="ORIGINAL">ORIGINAL</option>
                                                                <option value="COPIA">COPIA</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <input type="file" name="documentoseguimiento2"
                                                                    class="form-control form-control-sm"
                                                                    accept=".jpg,.jpeg,.png">

                                                                <button type="submit"
                                                                        class="btn btn-sm btn-subircaptura"
                                                                        title="GUARDAR">
                                                                    <i class="fas fa-print"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </form>
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

<!-- CANCELACION DE TRAMITE -->
<div class="modal fade" id="modalCancelacion" tabindex="-1" role="dialog" aria-labelledby="modalCancelacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalCancelacionLabel">CANCELACIÓN DE TRAMITE</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                    {!! Form::hidden('idtramite', $idTramite) !!}
                    @csrf
                    <div class="container">
                        @php
                            $documento29 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE REQUERIMIENTO')->where('tramite', 'JUBILACIÓN')->first();
                            $documento30 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ CARTA SOLICITUD A EMPLEADOR')->where('tramite', 'JUBILACIÓN')->first();
                            $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE MODIFICACIÓN DE CITE')->where('tramite', 'JUBILACIÓN')->first();

                            $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE MODIFICACIÓN DE CITE')->where('tramite', 'JUBILACIÓN')->first();
                            
                            $dictamenexiste = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('tramite', 'JUBILACIÓN')->first();
                            $existeexceso6mes = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('motivonopension', 'EXCESO DE 6 MESES')->first();
                            $existefaltacobertura = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('motivonopension', 'FALTA DE COBERTURA')->first();
                            $existeaccesopensionsi = $cliente->tramites()->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'NOTIFICACIÓN DE CONTRATO')->where('accesopension', 'SI')->first();

                            $cancelaciontramitegestoraexiste = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CANCELACIÓN')
                                ->where('subprocedimiento', 'CANCELACIÓN DE TRÁMITE')
                                ->where('usuarioingreso', 'GESTORA')
                                ->where('tramite', 'JUBILACIÓN')
                            ->first();

                            $cancelaciontramitegestora = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CANCELACIÓN')
                                ->where('subprocedimiento', 'CANCELACIÓN DE TRÁMITE')
                                ->where('usuarioingreso', 'GESTORA')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();

                            $cancelaciontramitecliente = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CANCELACIÓN')
                                ->where('subprocedimiento', 'CANCELACIÓN DE TRÁMITE')
                                ->where('usuarioingreso', $cliente->nombrecompleto)
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                            $hayGestora = $cancelaciontramitegestora->count() > 0;
                            $hayCliente = $cancelaciontramitecliente->count() > 0;

                            $rechazocancelaciontramite = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CANCELACIÓN')
                                ->where('subprocedimiento', 'RECHAZO DE CANCELACIÓN DE TRÁMITE GP')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                            $rechazocancelaciontramiteaps = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CANCELACIÓN')
                                ->where('subprocedimiento', 'RECHAZO DE CANCELACIÓN DE TRÁMITE APS')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                            $desistimientocancelaciontramite = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CANCELACIÓN')
                                ->where('subprocedimiento', 'DESISTIMIENTO A CANCELACIÓN DE TRÁMITE')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                            $aceptacioncancelaciontramite = $cliente->tramites()
                                ->where('nivelprocedimiento', 'CANCELACIÓN')
                                ->where('subprocedimiento', 'ACEPTACIÓN DE CANCELACIÓN DE TRÁMITE')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                        @endphp
                        <div class="table-responsive">
                            
                            @if(!$hayGestora && !$hayCliente)
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-center mb-3">ENCARGADO DE CANCELACIÓN DE TRÁMITE</h5>
                                    <div class="text-center mb-3">
                                        <button type="button" class="btn btn-adjuntosrespuestas btn-sm" id="btnGestora">GESTORA</button>
                                        <button type="button" class="btn btn-adjuntosrespuestas btn-sm" id="btnCliente">{{ $cliente->nombrecompleto }}</button>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($hayGestora || (!$hayGestora && !$hayCliente))
                                <div class="scroll-shadow-wrapper" id="wrapperGestora" style="{{ !$hayGestora && !$hayCliente ? 'display:none;' : '' }}">
                                    <div class="scroll-shadow-container">
                                        <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -5px;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 5%;">ID</th>
                                                    <th style="width: 5%;">NRO.</th>
                                                    <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                    <th style="width: 10%;">ENCARGADO_CANCELACIÓN</th>
                                                    <th style="width: 10%;">CITE_NOTIFICACIÓN</th>
                                                    <th style="width: 5%;">FECHA_CITE_NOTIFICACIÓN</th>
                                                    <th style="width: 10%;">CITE_NOTA</th>
                                                    <th style="width: 5%;">FECHA_CITE_NOTA</th>
                                                    <th style="width: 20%;">MOTIVO_CANCELACIÓN</th>
                                                    <th style="width: 10%;">FECHA_REGISTRO</th>
                                                    <th style="width: 10%;">DOCUMENTO</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cancelaciontramitegestora as $documento)
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->id }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->nro }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">CANCELACIÓN DE TRAMITE</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->usuarioingreso }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->citenotificacion }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenotificacion)->format('d-m-Y') }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->citenota }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenota)->format('d-m-Y') }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->motivorechazo }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if ($cancelaciontramitegestora->isEmpty())
                                                    <tr class="fila-cancelaciontramitegestora">
                                                @else
                                                    <tr class="fila-cancelaciontramitegestora d-none">
                                                @endif
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control" disabled>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control" disabled>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">CANCELACIÓN DE TRAMITE</p>
                                                        <input type="hidden" name="tramite[cancelaciontramitegestora]" value="JUBILACIÓN">
                                                        <input type="hidden" name="nivelprocedimiento[cancelaciontramitegestora]" value="CANCELACIÓN">
                                                        <input type="hidden" name="subprocedimiento[cancelaciontramitegestora]" value="CANCELACIÓN DE TRAMITE">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control text-center" name="usuarioingreso[cancelaciontramitegestora]" value="GESTORA" >
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control text-center" name="citenotificacion[cancelaciontramitegestora]">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="date" class="form-control text-center" name="fechacitenotificacion[cancelaciontramitegestora]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control text-center" name="citenota[cancelaciontramitegestora]">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="date" class="form-control text-center" name="fechacitenota[cancelaciontramitegestora]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <select class="form-control" name="motivorechazo[cancelaciontramitegestora]">
                                                            <option value="" disabled selected>Seleccione una opción...</option>
                                                            <option value="TRAMITE SIN MOVIMIENTO POR AFP/GESTORA">TRAMITE SIN MOVIMIENTO POR AFP/GESTORA</option>
                                                            <option value="TRAMITE SIN MOVIMIENTO POR AFILIADO">TRAMITE SIN MOVIMIENTO POR AFILIADO</option>
                                                            <option value="TRAMITE SIN MOVIMIENTO POR EGS">TRAMITE SIN MOVIMIENTO POR EGS</option>
                                                            <option value="TRAMITE SIN MOVIMIENTO POR CS">TRAMITE SIN MOVIMIENTO POR CS</option>
                                                            <option value="TRAMITE SIN MOVIMIENTO POR ACTA TMC">TRAMITE SIN MOVIMIENTO POR ACTA TMC</option>
                                                        </select>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        @php
                                                            $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                        @endphp
                                                        <input type="date" class="form-control text-center" name="fechasubida[cancelaciontramitegestora]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="file" name="archivo[cancelaciontramitegestora]" class="dropify mx-auto d-block" accept="application/pdf">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        @if (!$cancelaciontramitegestora->isEmpty())
                                        <div class="text-left mt-2" style="margin-bottom: 10px;">
                                            <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarcancelaciontramitegestora()">AGREGAR MÁS</button>
                                        </div>
                                        @endif
                                        <script>
                                            function agregarcancelaciontramitegestora() {
                                                const filaOculta = document.querySelector('.fila-cancelaciontramitegestora.d-none');
                                                if (filaOculta) {
                                                    filaOculta.classList.remove('d-none');
                                                }
                                            }
                                        </script>
                                    </div>
                                </div>
                            @endif

                            @if($hayCliente || (!$hayGestora && !$hayCliente))
                                <div class="scroll-shadow-wrapper" id="wrapperCliente" style="{{ !$hayCliente ? 'display:none;' : '' }}">
                                    <div class="scroll-shadow-container">
                                        <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -5px;">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 5%;">ID</th>
                                                    <th style="width: 5%;">NRO.</th>
                                                    <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                    <th style="width: 10%;">ENCARGADO_CANCELACIÓN</th>
                                                    <th style="width: 20%;">MOTIVO_CANCELACIÓN</th>
                                                    <th style="width: 10%;">FECHA_REGISTRO</th>
                                                    <th style="width: 10%;">CARTA_FIRMADA</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cancelaciontramitecliente as $documento)
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->id }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->nro }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">CANCELACIÓN DE TRAMITE</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->usuarioingreso }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ $documento->motivorechazo }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CONTRATO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if ($cancelaciontramitecliente->isEmpty())
                                                    <tr class="fila-cancelaciontramitecliente">
                                                @else
                                                    <tr class="fila-cancelaciontramitecliente d-none">
                                                @endif
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control" disabled>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control" disabled>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">CANCELACIÓN DE TRAMITE</p>
                                                        <input type="hidden" name="tramite[]" value="JUBILACIÓN">
                                                        <input type="hidden" name="nivelprocedimiento[]" value="CANCELACIÓN">
                                                        <input type="hidden" name="subprocedimiento[]" value="CANCELACIÓN DE TRAMITE">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="text" class="form-control text-center" name="usuarioingreso[]" value="{{ $cliente->nombrecompleto }}" >
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <select class="form-control" name="motivorechazo[]">
                                                            <option value="" disabled selected>Seleccione una opción...</option>
                                                            <option value="PÉRDIDA DE CONFIANZA EN EL APODERADO">PÉRDIDA DE CONFIANZA EN EL APODERADO</option>
                                                            <option value="CAMBIO DE DECISIÓN PERSONAL">CAMBIO DE DECISIÓN PERSONAL</option>
                                                            <option value="COSTOS O GASTOS EXCESIVOS">COSTOS O GASTOS EXCESIVOS</option>
                                                            <option value="PROBLEMAS LEGALES O FORMALES">PROBLEMAS LEGALES O FORMALES</option>
                                                            <option value="RELACIÓN PERSONAL DETERIORADA">RELACIÓN PERSONAL DETERIORADA</option>
                                                            <option value="CAMBIO DE APODERADO">CAMBIO DE APODERADO</option>
                                                            <option value="PÉRDIDA DE CAPACIDAD DEL APODERADO">PÉRDIDA DE CAPACIDAD DEL APODERADO</option>
                                                            <option value="FALLECIMIENTO">FALLECIMIENTO</option>
                                                        </select>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        @php
                                                            $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                        @endphp
                                                        <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        @if (!$cancelaciontramitecliente->isEmpty())
                                        <div class="text-left mt-2" style="margin-bottom: 10px;">
                                            <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarcancelaciontramitecliente()">AGREGAR MÁS</button>
                                        </div>
                                        @endif
                                        <script>
                                            function agregarcancelaciontramitecliente() {
                                                const filaOculta = document.querySelector('.fila-cancelaciontramitecliente.d-none');
                                                if (filaOculta) {
                                                    filaOculta.classList.remove('d-none');
                                                }
                                            }
                                        </script>
                                    </div>
                                </div>
                            @endif

                            @if($cancelaciontramitegestoraexiste)
                                {{-- RECHAZO A CANCELACION DE TRAMITE --}}
                                <table class="table table-bordered table-sm align-middle text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 10%;">ID</th>
                                            <th style="width: 10%;">NRO.</th>
                                            <th style="width: 30%;">SUB_PROCEDIMIENTO</th>
                                            <th style="width: 30%;">FECHA_REGISTRO</th>
                                            <th style="width: 20%;">CARTA_RECHAZO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rechazocancelaciontramite as $documento)
                                            <tr>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->id }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->nro }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">RECHAZO DE CANCELACIÓN DE TRÁMITE GP</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CANCELACIÓN/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER CARTA</a>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($rechazocancelaciontramite->isEmpty())
                                            <tr class="fila-rechazocancelaciontramite">
                                        @else
                                            <tr class="fila-rechazocancelaciontramite d-none">
                                        @endif
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">RECHAZO DE CANCELACIÓN DE TRÁMITE GP</p>
                                                <input type="hidden" name="tramite[rechazocancelaciontramite]" value="JUBILACIÓN">
                                                <input type="hidden" name="nivelprocedimiento[rechazocancelaciontramite]" value="CANCELACIÓN">
                                                <input type="hidden" name="subprocedimiento[rechazocancelaciontramite]" value="RECHAZO DE CANCELACIÓN DE TRÁMITE GP">
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                @endphp
                                                <input type="date" class="form-control text-center" name="fechasubida[rechazocancelaciontramite]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="file" name="archivo[rechazocancelaciontramite]" class="dropify mx-auto d-block" accept="application/pdf">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                @if (!$rechazocancelaciontramite->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarrechazocancelaciontramite()">AGREGAR MÁS</button>
                                    </div>
                                @endif
                                <script>
                                    function agregarrechazocancelaciontramite() {
                                        const filaOculta = document.querySelector('.fila-rechazocancelaciontramite.d-none');
                                        if (filaOculta) {
                                            filaOculta.classList.remove('d-none');
                                        }
                                    }
                                </script>

                                {{-- RECHAZO A CANCELACION DE TRAMITE APS --}}
                                <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -5px;">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 10%;">ID</th>
                                            <th style="width: 10%;">NRO.</th>
                                            <th style="width: 30%;">SUB_PROCEDIMIENTO</th>
                                            <th style="width: 30%;">FECHA_REGISTRO</th>
                                            <th style="width: 20%;">CARTA_RECHAZO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rechazocancelaciontramiteaps as $documento)
                                            <tr>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->id }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->nro }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">RECHAZO DE CANCELACIÓN DE TRÁMITE APS</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CANCELACIÓN/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER CARTA</a>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($rechazocancelaciontramiteaps->isEmpty())
                                            <tr class="fila-rechazocancelaciontramiteaps">
                                        @else
                                            <tr class="fila-rechazocancelaciontramiteaps d-none">
                                        @endif
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">RECHAZO DE CANCELACIÓN DE TRÁMITE APS</p>
                                                <input type="hidden" name="tramite[rechazocancelaciontramiteaps]" value="JUBILACIÓN">
                                                <input type="hidden" name="nivelprocedimiento[rechazocancelaciontramiteaps]" value="CANCELACIÓN">
                                                <input type="hidden" name="subprocedimiento[rechazocancelaciontramiteaps]" value="RECHAZO DE CANCELACIÓN DE TRÁMITE APS">
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                @endphp
                                                <input type="date" class="form-control text-center" name="fechasubida[rechazocancelaciontramiteaps]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="file" name="archivo[rechazocancelaciontramiteaps]" class="dropify mx-auto d-block" accept="application/pdf">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                @if (!$rechazocancelaciontramiteaps->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarrechazocancelaciontramiteaps()">AGREGAR MÁS</button>
                                    </div>
                                @endif
                                <script>
                                    function agregarrechazocancelaciontramiteaps() {
                                        const filaOculta = document.querySelector('.fila-rechazocancelaciontramiteaps.d-none');
                                        if (filaOculta) {
                                            filaOculta.classList.remove('d-none');
                                        }
                                    }
                                </script>

                                {{-- ACEPTACIÓN DE RECHAZO A CANCELACIÓN DE TRÁMITE --}}
                                <table class="table table-bordered table-sm align-middle text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 10%;">ID</th>
                                            <th style="width: 10%;">NRO.</th>
                                            <th style="width: 25%;">SUB_PROCEDIMIENTO</th>
                                            <th style="width: 25%;">FECHA_REGISTRO</th>
                                            <th style="width: 30%;">DOCUMENTO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($aceptacioncancelaciontramite as $documento)
                                            <tr>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->id }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->nro }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">ACEPTACIÓN DE CANCELACIÓN DE TRÁMITE</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CANCELACIÓN/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER CARTA</a>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($aceptacioncancelaciontramite->isEmpty())
                                            <tr class="fila-aceptacioncancelaciontramite">
                                        @else
                                            <tr class="fila-aceptacioncancelaciontramite d-none">
                                        @endif
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">ACEPTACIÓN DE CANCELACIÓN DE TRÁMITE</p>
                                                <input type="hidden" name="tramite[aceptacioncancelaciontramite]" value="JUBILACIÓN">
                                                <input type="hidden" name="nivelprocedimiento[aceptacioncancelaciontramite]" value="CANCELACIÓN">
                                                <input type="hidden" name="subprocedimiento[aceptacioncancelaciontramite]" value="ACEPTACIÓN DE CANCELACIÓN DE TRÁMITE">
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                @endphp
                                                <input type="date" class="form-control text-center" name="fechasubida[aceptacioncancelaciontramite]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="file" name="archivo[aceptacioncancelaciontramite]" class="dropify mx-auto d-block" accept="application/pdf">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                @if (!$aceptacioncancelaciontramite->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregaraceptacioncancelaciontramite()">AGREGAR MÁS</button>
                                    </div>
                                @endif
                                <script>
                                    function agregaraceptacioncancelaciontramite() {
                                        const filaOculta = document.querySelector('.fila-aceptacioncancelaciontramite.d-none');
                                        if (filaOculta) {
                                            filaOculta.classList.remove('d-none');
                                        }
                                    }
                                </script>

                                {{-- DESISTIMIENTO A CANCELACION DE TRAMITE --}}
                                <table class="table table-bordered table-sm align-middle text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 10%;">ID</th>
                                            <th style="width: 10%;">NRO.</th>
                                            <th style="width: 25%;">SUB_PROCEDIMIENTO</th>
                                            <th style="width: 25%;">FECHA_REGISTRO</th>
                                            <th style="width: 30%;">DOCUMENTO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($desistimientocancelaciontramite as $documento)
                                            <tr>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->id }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ $documento->nro }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">DESISTIMIENTO A CANCELACIÓN DE TRÁMITE</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/CANCELACIÓN/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER CARTA</a>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($desistimientocancelaciontramite->isEmpty())
                                            <tr class="fila-desistimientocancelaciontramite">
                                        @else
                                            <tr class="fila-desistimientocancelaciontramite d-none">
                                        @endif
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="text" class="form-control" disabled>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">DESISTIMIENTO A CANCELACIÓN DE TRÁMITE</p>
                                                <input type="hidden" name="tramite[desistimientocancelaciontramite]" value="JUBILACIÓN">
                                                <input type="hidden" name="nivelprocedimiento[desistimientocancelaciontramite]" value="CANCELACIÓN">
                                                <input type="hidden" name="subprocedimiento[desistimientocancelaciontramite]" value="DESISTIMIENTO A CANCELACIÓN DE TRÁMITE">
                                            </td>
                                            <td class="align-middle text-center">
                                                @php
                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                @endphp
                                                <input type="date" class="form-control text-center" name="fechasubida[desistimientocancelaciontramite]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                            </td>
                                            <td class="align-middle text-center">
                                                <input type="file" name="archivo[desistimientocancelaciontramite]" class="dropify mx-auto d-block" accept="application/pdf">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                @if (!$desistimientocancelaciontramite->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregardesistimientocancelaciontramite()">AGREGAR MÁS</button>
                                    </div>
                                @endif
                                <script>
                                    function agregardesistimientocancelaciontramite() {
                                        const filaOculta = document.querySelector('.fila-desistimientocancelaciontramite.d-none');
                                        if (filaOculta) {
                                            filaOculta.classList.remove('d-none');
                                        }
                                    }
                                </script>

                            @endif

                            @if(!$hayGestora && !$hayCliente)
                                <script>
                                    document.getElementById('btnGestora').addEventListener('click', function() {
                                        document.getElementById('wrapperGestora').style.display = 'block';
                                        document.getElementById('wrapperCliente').style.display = 'none';
                                    });
                                    document.getElementById('btnCliente').addEventListener('click', function() {
                                        document.getElementById('wrapperGestora').style.display = 'none';
                                        document.getElementById('wrapperCliente').style.display = 'block';
                                    });
                                </script>
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- NOTIFICACIONES ERRONEAS -->
<div class="modal fade" id="modalNotifErroneas" tabindex="-1" role="dialog" aria-labelledby="modalNotifErroneasLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalNotifErroneasLabel">NOTIFICACIONES ERRÓNEAS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                    {!! Form::hidden('idtramite', $idTramite) !!}
                    @csrf
                    <div class="container">
                        @php
                            $notiferronea = $cliente->tramites()
                                ->where('nivelprocedimiento', 'NOTIFICACIÓN ERRÓNEA')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 10%;">ID</th>
                                        <th style="width: 10%;">NRO.</th>
                                        <th style="width: 30%;">SUB_PROCEDIMIENTO</th>
                                        <th style="width: 25%;">CITE_NOTA</th>
                                        <th style="width: 25%;">FECHA_CITE_NOTA</th>
                                        <th style="width: 25%;">FECHA_REGISTRO</th>
                                        <th style="width: 25%;">DOCUMENTO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($notiferronea as $documento)
                                        <tr>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ $documento->id }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ $documento->nro }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ $documento->subprocedimiento }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ $documento->citenota }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ $documento->fechacitenota }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/NOTIFICACIÓN ERRÓNEA/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($notiferronea->isEmpty())
                                        <tr class="fila-notiferronea">
                                    @else
                                        <tr class="fila-notiferronea d-none">
                                    @endif
                                        <td class="align-middle text-center">
                                            <input type="text" class="form-control" disabled>
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="text" class="form-control" disabled>
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="hidden" name="tramite[notiferronea]" value="JUBILACIÓN">
                                            <input type="hidden" name="nivelprocedimiento[notiferronea]" value="NOTIFICACIÓN ERRÓNEA">
                                            <select class="form-control" name="subprocedimiento[notiferronea]">
                                                <option value="" disabled selected>Seleccione una opción...</option>
                                                <option value="SITM ENTE GESTOR DE SALUD">SITM ENTE GESTOR DE SALUD</option>
                                                <option value="SITM NOTIFICACION TMC">SITM NOTIFICACION TMC</option>
                                                <option value="SITM EMPLEADOR">SITM EMPLEADOR</option>
                                                <option value="COMPRA DE SERVICIOS">COMPRA DE SERVICIOS</option>
                                                <option value="SIC ENTE GESTOR DE SALUD">SIC ENTE GESTOR DE SALUD</option>
                                                <option value="SIC NOTIFICACION TMC">SIC NOTIFICACION TMC</option>
                                                <option value="SIC EMPLEADOR">SIC EMPLEADOR</option>
                                                <option value="CONTRATO">CONTRATO</option>
                                            </select>
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="text" class="form-control" name="citenota[notiferronea]">
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="date" class="form-control" name="fechacitenota[notiferronea]">
                                        </td>
                                        <td class="align-middle text-center">
                                            @php
                                                $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                            @endphp
                                            <input type="date" class="form-control text-center" name="fechasubida[notiferronea]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                        </td>
                                        <td class="align-middle text-center">
                                            <input type="file" name="archivo[notiferronea]" class="dropify mx-auto d-block" accept="application/pdf">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            @if (!$notiferronea->isEmpty())
                                <div class="text-left mt-2" style="margin-bottom: 10px;">
                                    <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarnotiferronea()">AGREGAR MÁS</button>
                                </div>
                            @endif
                            <script>
                                function agregarnotiferronea() {
                                    const filaOculta = document.querySelector('.fila-notiferronea.d-none');
                                    if (filaOculta) {
                                        filaOculta.classList.remove('d-none');
                                    }
                                }
                            </script>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- NUEVO 130226 --}}
<!-- NOTIFICACIONES OBSERVADAS -->
<div class="modal fade" id="modalNotifObservadas" tabindex="-1" role="dialog" aria-labelledby="modalNotifObservadasLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalNotifObservadasLabel">NOTIFICACIONES OBSERVADAS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                    {!! Form::hidden('idtramite', $idTramite) !!}
                    @csrf
                    <div class="container">
                        @php
                            $notifobservadas = $cliente->tramites()
                                ->where('nivelprocedimiento', 'NOTIFICACIÓN OBSERVADA')
                                ->where('tramite', 'JUBILACIÓN')
                            ->get();
                        @endphp
                        <div class="table-responsive">
                            <div class="scroll-shadow-wrapper">
                                <div class="scroll-shadow-container">
                                    <table class="table table-bordered table-sm align-middle text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 10%;">ID</th>
                                                <th style="width: 10%;">NRO.</th>
                                                <th style="width: 30%;">SUB_PROCEDIMIENTO</th>
                                                <th style="width: 25%;">CITE_NOTA</th>
                                                <th style="width: 25%;">FECHA_CITE_NOTA</th>
                                                <th style="width: 25%;">FECHA_REGISTRO</th>
                                                <th style="width: 25%;">DOCUMENTO</th>
                                                <th style="width: 25%;">FECHA_RESPUESTA</th>
                                                <th style="width: 25%;">DOCUMENTO_RESPUESTA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($notifobservadas as $documento)
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->subprocedimiento }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->citenota }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->fechacitenota }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/NOTIFICACIÓN OBSERVADA/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                    </td>
                                                    <td>
                                                        @if($documento->fechainclusion)
                                                            {{ $documento->fechainclusion }}
                                                        @else
                                                            <input type="date"
                                                                class="form-control form-control-sm"
                                                                name="fechainclusion[{{ $documento->id }}]">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($documento->document2)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/JUBILACIÓN/NOTIFICACIÓN OBSERVADA/{$documento->document2}") }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-verdocumento">
                                                            VER RESPUESTA
                                                            </a>
                                                        @else
                                                            <input type="file"
                                                                name="document2[{{ $documento->id }}]"
                                                                class="form-control form-control-sm"
                                                                accept="application/pdf">
                                                        @endif
                                                        @if(!$documento->fechainclusion || !$documento->document2)
                                                            <button type="submit"
                                                                    name="actualizar_id"
                                                                    value="{{ $documento->id }}"
                                                                    class="btn btn-sm btn-subirarchivos">
                                                                ACTUALIZAR RESPUESTA
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($notifobservadas->isEmpty())
                                                <tr class="fila-notifobservadas">
                                            @else
                                                <tr class="fila-notifobservadas d-none">
                                            @endif
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="hidden" name="tramite[notifobservadas]" value="JUBILACIÓN">
                                                    <input type="hidden" name="nivelprocedimiento[notifobservadas]" value="NOTIFICACIÓN OBSERVADA">
                                                    <input type="text" class="form-control" name="subprocedimiento[notifobservadas]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" name="citenota[notifobservadas]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control" name="fechacitenota[notifobservadas]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                    @endphp
                                                    <input type="date" class="form-control text-center" name="fechasubida[notifobservadas]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="file" name="archivo[notifobservadas]" class="dropify mx-auto d-block" accept="application/pdf">
                                                </td>
                                                <td class="align-middle text-center">
                                                    
                                                </td>
                                                <td class="align-middle text-center">
                                                    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if (!$notifobservadas->isEmpty())
                                <div class="text-left mt-2" style="margin-bottom: 10px;">
                                    <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarnotifobservadas()">AGREGAR MÁS</button>
                                </div>
                            @endif
                            <script>
                                function agregarnotifobservadas() {
                                    const filaOculta = document.querySelector('.fila-notifobservadas.d-none');
                                    if (filaOculta) {
                                        filaOculta.classList.remove('d-none');
                                    }
                                }
                            </script>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                </form>
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('.file-input');
        const fileLabels = document.querySelectorAll('.file-label');
    
        fileInputs.forEach((input, index) => {
            input.addEventListener('change', function() {
                const fileName = input.files[0] ? input.files[0].name : 'Subir Documento';
                fileLabels[index].innerHTML = fileName;
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.dropify').dropify({
            messages: {
                'default': '',
                'replace': '',
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
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>

{{-- FECHA RETORNO EN INGRESO DE TRAMITE --}}
<script>
    function ajustarFechaFinDeSemana(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida', '');
        const fechaRetornoInput = document.getElementById('fecharetorno' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 15);
            fechaMax = ajustarFechaFinDeSemana(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida"]').forEach(input => {
            actualizarFechaRetorno(input);
        });
    });
</script>

{{-- FECHA RETORNO EN CONTINUIDAD DE TRAMITE--}}
<script>
    function ajustarFechaFinDeSemana3(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno3(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida3', '');
        const fechaRetornoInput = document.getElementById('fecharetorno3' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 15);
            fechaMax = ajustarFechaFinDeSemana3(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida3"]').forEach(input => {
            actualizarFechaRetorno3(input);
        });
    });
</script>

{{-- FECHA RETORNO EN FIRMA EAP --}}
<script>
    function ajustarFechaFinDeSemana2(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno2(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida2', '');
        const fechaRetornoInput = document.getElementById('fecharetorno2' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 30);
            fechaMax = ajustarFechaFinDeSemana2(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida2"]').forEach(input => {
            actualizarFechaRetorno2(input);
        });
    });
</script>

{{-- FECHA RETORNO EN SITM Y SIC NOTIFICACION TMC --}}
<script>
    function ajustarFechaFinDeSemana4(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno4(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida4', '');
        const fechaRetornoInput = document.getElementById('fecharetorno4' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 30);
            fechaMax = ajustarFechaFinDeSemana4(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida4"]').forEach(input => {
            actualizarFechaRetorno4(input);
        });
    });
</script>

{{-- FECHA RETORNO EN SITM Y SIC NOTIFICACION TMR --}}
<script>
    function ajustarFechaFinDeSemana5(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno5(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida5', '');
        const fechaRetornoInput = document.getElementById('fecharetorno5' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 30);
            fechaMax = ajustarFechaFinDeSemana5(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida5"]').forEach(input => {
            actualizarFechaRetorno5(input);
        });
    });
</script>

{{-- FECHA RETORNO EN COMPRA DE SERVICIOS --}}
<script>
    function ajustarFechaFinDeSemana6(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno6(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida6', '');
        const fechaRetornoInput = document.getElementById('fecharetorno6' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 20);
            fechaMax = ajustarFechaFinDeSemana6(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida6"]').forEach(input => {
            actualizarFechaRetorno6(input);
        });
    });
</script>

{{-- FECHA RETORNO EN SITM EMPLEADOR - NOTIFICACION REQUERIMIENTO --}}
<script>
    function ajustarFechaFinDeSemana7(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno7(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida7', '');
        const fechaRetornoInput = document.getElementById('fecharetorno7' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 30);
            fechaMax = ajustarFechaFinDeSemana7(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida7"]').forEach(input => {
            actualizarFechaRetorno7(input);
        });
    });
</script>

{{-- FECHA RETORNO EN SITM EMPLEADOR - RESPUESTA A REQUERIMIENTO --}}
<script>
    function ajustarFechaFinDeSemana8(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno8(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida8', '');
        const fechaRetornoInput = document.getElementById('fecharetorno8' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 45);
            fechaMax = ajustarFechaFinDeSemana8(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida8"]').forEach(input => {
            actualizarFechaRetorno8(input);
        });
    });
</script>

{{-- FECHA RETORNO EN COMPRA DE SERVICIOS - SOLICITUD DE REQUERIMIENTO --}}
<script>
    function ajustarFechaFinDeSemana9(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno9(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida9', '');
        const fechaRetornoInput = document.getElementById('fecharetorno9' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 30);
            fechaMax = ajustarFechaFinDeSemana9(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida9"]').forEach(input => {
            actualizarFechaRetorno9(input);
        });
    });
</script>

{{-- FECHA RETORNO EN COMPRA DE SERVICIOS - SEGUNDA NOTIFICACION --}}
<script>
    function ajustarFechaFinDeSemana10(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno10(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida10', '');
        const fechaRetornoInput = document.getElementById('fecharetorno10' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 30);
            fechaMax = ajustarFechaFinDeSemana10(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida10"]').forEach(input => {
            actualizarFechaRetorno10(input);
        });
    });
</script>

{{-- FECHA RETORNO EN CONTRATO - RENUNCIA A REVISION DE CONTRATO --}}
<script>
    function ajustarFechaFinDeSemana11(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno11(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida11', '');
        const fechaRetornoInput = document.getElementById('fecharetorno11' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 5);
            fechaMax = ajustarFechaFinDeSemana11(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida11"]').forEach(input => {
            actualizarFechaRetorno11(input);
        });
    });
</script>

{{-- FECHA RETORNO EN COMPRA DE SERVICIOS - AGENDAMIENTO --}}
<script>
    function ajustarFechaFinDeSemana12(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno12(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida12', '');
        const fechaRetornoInput = document.getElementById('fecharetorno12' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 5);
            fechaMax = ajustarFechaFinDeSemana12(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida12"]').forEach(input => {
            actualizarFechaRetorno12(input);
        });
    });
</script>

{{-- FECHA RETORNO EN SIC EMPLEADOR - NOTIFICACION REQUERIMIENTO --}}
<script>
    function ajustarFechaFinDeSemana13(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno13(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida13', '');
        const fechaRetornoInput = document.getElementById('fecharetorno13' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 30);
            fechaMax = ajustarFechaFinDeSemana13(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida13"]').forEach(input => {
            actualizarFechaRetorno13(input);
        });
    });
</script>

{{-- FECHA RETORNO EN SIC EMPLEADOR - RESPUESTA A REQUERIMIENTO --}}
<script>
    function ajustarFechaFinDeSemana8(fecha) {
        const dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() - 1);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }
    function actualizarFechaRetorno14(inputFechaSubida) {
        const id = inputFechaSubida.id.replace('fechasubida14', '');
        const fechaRetornoInput = document.getElementById('fecharetorno14' + id);
        const fechaSubida = new Date(inputFechaSubida.value);
        if (!isNaN(fechaSubida.getTime())) {
            let fechaMax = new Date(fechaSubida);
            fechaMax.setDate(fechaMax.getDate() + 45);
            fechaMax = ajustarFechaFinDeSemana14(fechaMax);
            const formatoFecha = f => f.toISOString().split('T')[0];
            fechaRetornoInput.min = inputFechaSubida.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('input[id^="fechasubida14"]').forEach(input => {
            actualizarFechaRetorno14(input);
        });
    });
</script>
@endsection

