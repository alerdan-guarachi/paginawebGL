@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.tramites.index') }}">REGRESAR</a>
@if($inicioocontinuidad)
    <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalseguimientoproceso">SEGUIMIENTO</a>
    <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalcartayreclamo">CARTA / RECLAMO</a>
    <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modaladjuntosrespuestas">ADJUNTOS Y RESPUESTAS</a>
    <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalcomunicaciones">COMUNICACIONES</a>
    <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalsolicitudes">SOLICITUDES</a>
    <a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalDatos">DATOS CLIENTE</a>
@endif
<a class="btn btn-sm float-right btn-seguimiento" data-toggle="modal" data-target="#modalCodigo">CÓDIGO PERMISO</a>

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
                    </div>
                {!! Form::submit('ACTUALIZAR DATOS', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<h5>PROCEDIMIENTO DE INVALIDEZ DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
{{-- <link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}?v={{ filemtime(public_path('css/tramitesgestora.css')) }}"> --}}
<style>
    .btn-guardarnuevo {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        font-weight: bold;
        padding: 1px 4px;
    }
    .btn-guardarnuevo:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .disabled-link {
        pointer-events: none;
        color: gray !important;
        opacity: 0.6;
        cursor: default;
    }

    .no-seguro-label {
        color: rgb(239, 124, 16);
        font-size: 0.85em;
        margin-left: 5px;
    }

    .dropdown-item.disabled-link:hover {
        background-color: inherit !important;
    }
    .scroll-shadow-wrapper {
        position: relative;
    }
    .scroll-shadow-container {
        overflow-x: auto;
    }
    .scroll-shadow-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 30px;
        height: 100%;
        pointer-events: none;
        background: linear-gradient(to left, rgb(201, 244, 182), transparent);
        transition: opacity 0.3s;
        opacity: 1;
        z-index: 10;
    }
    .scroll-shadow-wrapper.scrolled-right::after {
        opacity: 0;
    }
</style>
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
                        {!! Form::hidden('apoderado', auth()->user()->name) !!}
                        {!! Form::hidden('tramite', 'INVALIDEZ') !!}
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
                                    onclick="{{ $permisoContinuidad ? "confirmarTramite('CONTINUIDAD DE TRÁMITE')" : '' }}"
                                    {{ $permisoContinuidad ? '' : 'disabled' }}>
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
                    $documento5 = $cliente->tramites()->where('tramite', 'INVALIDEZ')
                        ->where('nivelprocedimiento', 'FIRMA EAP')
                        ->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')
                        ->where('estadocomunicado', 'COMUNICADO')
                        ->where(function ($query) {
                            $query->whereNotNull('capturacomunicacion')
                                ->where(function ($subQuery) {
                                    $subQuery->where('capturacomunicacion', 'like', '%.jpg');
                                });
                        })
                    ->first();
                    $documento3 = $cliente->tramites()->where('subprocedimiento', 'FIRMA EAP')
                        ->where('tramite', 'INVALIDEZ')
                        ->where('estadocomunicado', 'COMUNICADO')
                        ->where(function ($query) {
                            $query->whereNotNull('capturacomunicacion')
                                ->where(function ($subQuery) {
                                    $subQuery->where('capturacomunicacion', 'like', '%.jpg');
                                });
                        })
                    ->first();
                    $estadotramite = $cliente->tramites()->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')
                        ->where('subprocedimiento', 'INCLUSIÓN DE PODER')
                        ->where('tramite', 'INVALIDEZ')
                        ->whereIn('estadotramite', ['INGRESO DE TRÁMITE', 'FIRMA EAP'])
                    ->first();
                @endphp
                @if (!$documento5 && !$documento3 && !$estadotramite)
                    <li class="nav-item">
                        <a class="nav-link disabled" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="false">PROCESO EN CURSO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="false">RESULTADO DEL PROCESO</a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="false">PROCESO EN CURSO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="false">RESULTADO DEL PROCESO</a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                {{-- 1.- INICIO DE TRÁMITE / ESTRUCTURA DE VISTA--}}
                <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    @if($tramiteinicio)
                        <div class="row">
                            {{-- INGRESO DE TRAMITE --}}
                            <div class="col-12 col-md-4 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalIngresoTramite">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-folder-plus fa-5x mb-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">INGRESO DE TRÁMITE</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento1 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                                    $documento2 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'INCLUSIÓN DE PODER')->first();
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
                                $documento1 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('subprocedimiento', 'RECEPCION DE TRÁMITE')->where('estadocomunicado', 'COMUNICADO')->first();
                                $documento2 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('subprocedimiento', 'INCLUSION DE PODER')->where('estadocomunicado', 'COMUNICADO')->first();
                            @endphp
                            <div class="col-12 col-md-4 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalNotificacionPoder" @if (!$documento1 && !$documento2) disabled @endif>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-envelope-open-text fa-5x mb-2"></i>
                                        <span class="h6 mb-0">NOTIFICACIÓN DEL PODER</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento3 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->whereIn('subprocedimiento', ['VALIDACIÓN DE PODER', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS'])->first();
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
                                $documento3 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->whereIn('subprocedimiento', ['VALIDACIÓN DE PODER', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS'])->where('estadocomunicado', 'COMUNICADO')->first();
                            @endphp
                            <div class="col-12 col-md-4 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalFirmaEAP" @if (!$documento3) disabled @endif>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-signature fa-5x mb-2"></i>
                                        <span class="h6 mb-0">FIRMA EAP</span>
                                        @php
                                            $documento1 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
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
                                    $documento5 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
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
                            {{-- INGRESO DE TRAMITE --}}
                            <div class="col-12 col-md-4 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalIngresoTramite">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-folder-plus fa-5x mb-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">INGRESO DE PODER</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento1 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                                    $documento2 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'INCLUSIÓN DE PODER')->first();
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
                                $documento1 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('subprocedimiento', 'RECEPCION DE TRÁMITE')->where('estadocomunicado', 'COMUNICADO')->first();
                                $documento2 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('subprocedimiento', 'INCLUSION DE PODER')->where('estadocomunicado', 'COMUNICADO')->first();
                            @endphp
                            <div class="col-12 col-md-4 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalNotificacionPoder" @if (!$documento1 && !$documento2) disabled @endif>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-envelope-open-text fa-5x mb-2"></i>
                                        <span class="h6 mb-0">NOTIFICACIÓN DEL PODER</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento3 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'VALIDACIÓN DE PODER')->first();
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
                                $documento3 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'VALIDACIÓN DE PODER')->where('estadocomunicado', 'COMUNICADO')->first();
                                $estadotramite = $cliente->tramites()->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')->where('subprocedimiento', 'INCLUSIÓN DE PODER')->where('tramite', 'INVALIDEZ')->whereIn('estadotramite', ['INGRESO DE TRÁMITE', 'FIRMA EAP'])->first();
                            @endphp
                            <div class="col-12 col-md-4 mb-3">
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalFirmaEAP" @if (!$documento3 || !$estadotramite) disabled @endif>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-signature fa-5x mb-2"></i>
                                        <span class="h6 mb-0">FIRMA EAP</span>
                                        @php
                                            $documento1 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
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
                                    $documento5 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where(function ($query) {$query->whereNotNull('capturacomunicacion')->where(function ($subQuery) {$subQuery->where('capturacomunicacion', 'like', '%.jpg');});})->where('estadocomunicado', 'COMUNICADO')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
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
                <!-- MODAL INGRESO DE TRÁMITE -->
                <div class="modal fade" id="modalIngresoTramite" tabindex="-1" role="dialog" aria-labelledby="modalIngresoTramiteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                @if($tramiteinicio)
                                    <h5 class="modal-title titulomodal" id="modalIngresoTramiteLabel">INGRESO DE TRÁMITE</h5>
                                @endif
                                @if($tramitecontinuidad)
                                    <h5 class="modal-title titulomodal" id="modalIngresoTramiteLabel">INGRESO DE PODER</h5>
                                @endif
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
                                            $documento1 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                                            $fecha = \Carbon\Carbon::now();
                                            $valor = $fecha->day <= 15 ? $fecha : $fecha->copy()->addMonth();
                                            $valorFormateado = $valor->format('m/y');
                                            $documento2 = $cliente->tramites()->where('subprocedimiento', 'INCLUSIÓN DE PODER')->where('tramite', 'INVALIDEZ')->first();
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
                                                                            <input type="hidden" name="tramite[]" value="INVALIDEZ">
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
                                                                            </select>
                                                                        @endif
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        @if ($documento1)
                                                                            {{ \Carbon\Carbon::parse($documento1->mescierre)->format('m-Y') }}
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
                                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/INGRESO DE TRÁMITE/{$documento1->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
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
                                                                            <input type="hidden" name="tramite[]" value="INVALIDEZ">
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
                                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/INGRESO DE TRÁMITE/{$documento2->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
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
                                                                            {{ \Carbon\Carbon::parse($documento2->mescierre)->format('m-Y') }}
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
                                            $documento4 = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE PODER')->where('tramite', 'INVALIDEZ')->first();
                                            $documento3 = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE PODER')->where('tramite', 'INVALIDEZ')->first();
                                            $correccionpoder = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE PODER')->where('tramite', 'INVALIDEZ')->first();
                                            $rechazodocext = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->first();
                                            $validaciondocext = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->first();
                                            $correcciondocext = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->first();
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
                                                    {{-- VALIDACION DE PODER --}}
                                                    @if (!$documento4 || $correccionpoder)
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">VALIDACIÓN DE PODER</p>
                                                                @if (!$documento3)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER">
                                                                    <input type="hidden" name="subprocedimiento[]" value="VALIDACIÓN DE PODER">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento3)
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento3->fechasubida)->format('d-m-Y') }}</p>
                                                                @else
                                                                    <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento3)
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/NOTIFICACIÓN DE PODER/{$documento3->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
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
                                                                    <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    @if ($cliente->paisnacimiento !== 'BOLIVIA')
                                                        {{-- RECHAZO DE DOCUMENTOS EXTRANJEROS --}}
                                                        @if (!$validaciondocext || $rechazodocext && (!$rechazodocext || $rechazodocext))
                                                            @php
                                                                $rechazosDocExt = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->get();
                                                                $validacionesDocExt = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->get();
                                                            @endphp
                                                            <tbody id="contenedor-rechazos">
                                                                @if ($rechazosDocExt->count() > 0)
                                                                    @foreach ($rechazosDocExt as $rechazo)
                                                                        <tr class="fila-rechazo">
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">RECHAZO DE DOC. EXTRANJEROS</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($rechazo->fechasubida)->format('d/m/Y') }} - {{ $rechazo->tipodocumento }}</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                @if ($rechazo->document)
                                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/NOTIFICACIÓN DE PODER/{$rechazo->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                                @else
                                                                                    <span class="text-muted">Sin archivo</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr class="fila-rechazo">
                                                                        <td class="align-middle text-center">
                                                                            <p class="mb-0">RECHAZO DE DOC. EXTRANJEROS</p>
                                                                            <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                            <input type="hidden" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER">
                                                                            <input type="hidden" name="subprocedimiento[]" value="RECHAZO DE DOCUMENTOS EXTRANJEROS">
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <div class="d-flex justify-content-center gap-2">
                                                                                <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" style="max-width: 130px;" {{ $puedeEditarFecha ? '' : 'readonly' }} style="max-width: 130px;">
                                                                                <select class="form-control" name="tipodocumento[]" style="max-width: 200px;">
                                                                                    <option value=""></option>
                                                                                    <option value="CERTIFICADO DE NACIMIENTO">CERTIFICADO DE NACIMIENTO</option>
                                                                                    <option value="CERTIFICADO DE MATRIMONIO">CERTIFICADO DE MATRIMONIO</option>
                                                                                    <option value="PASAPORTE">PASAPORTE</option>
                                                                                    <option value="PROTOCOLIZADO DE PODER">PROTOCOLIZADO DE PODER</option>
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                            @if ($rechazosDocExt->count() == 0 && $validacionesDocExt->count() == 0)
                                                                <tr>
                                                                    <td colspan="3" class="text-left">
                                                                        <button type="button" id="agregar-fila-rechazo" class="btn btn-sm btn-outline-primary">AGREGAR RECHAZO DOC. EXTRANJERO</button>
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
                                                                            if (filasActuales >= 4) {
                                                                                alert('Solo se permiten hasta 4 documentos de rechazo.');
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
                                                                $correccionesDocExt = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->get();
                                                            @endphp

                                                            <tbody id="contenedor-correccion">
                                                                @if ($correccionesDocExt->count() > 0)
                                                                    @foreach ($correccionesDocExt as $correccion)
                                                                        <tr class="fila-correccion">
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">CORRECCIÓN DE DOC. EXTRANJEROS</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($correccion->fechasubida)->format('d/m/Y') }} - {{ $correccion->tipodocumento }}</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                @if ($correccion->document)
                                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/NOTIFICACIÓN DE PODER/{$correccion->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                                @else
                                                                                    <span class="text-muted">Sin archivo</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr class="fila-correccion">
                                                                        <td class="align-middle text-center">
                                                                            <p class="mb-0">CORRECCIÓN DE DOC. EXTRANJEROS</p>
                                                                            <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                            <input type="hidden" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER">
                                                                            <input type="hidden" name="subprocedimiento[]" value="CORRECCIÓN DE DOCUMENTOS EXTRANJEROS">
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <div class="d-flex justify-content-center gap-2">
                                                                                <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" style="max-width: 130px;" {{ $puedeEditarFecha ? '' : 'readonly' }} style="max-width: 130px;">
                                                                                <select class="form-control" name="tipodocumento[]" style="max-width: 200px;">
                                                                                    <option value=""></option>
                                                                                    <option value="CERTIFICADO DE NACIMIENTO">CERTIFICADO DE NACIMIENTO</option>
                                                                                    <option value="CERTIFICADO DE MATRIMONIO">CERTIFICADO DE MATRIMONIO</option>
                                                                                    <option value="PASAPORTE">PASAPORTE</option>
                                                                                    <option value="PROTOCOLIZADO DE PODER">PROTOCOLIZADO DE PODER</option>
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td class="align-middle text-center">
                                                                            <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                            @if ($correccionesDocExt->count() == 0)
                                                                <tr>
                                                                    <td colspan="3" class="text-left">
                                                                        <button type="button" id="agregar-fila-correccion" class="btn btn-sm btn-outline-primary">AGREGAR CORRECCIÓN DOC. EXTRANJERO</button>
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
                                                                            if (filasActuales >= 4) {
                                                                                alert('Solo se permiten hasta 4 documentos de correccion.');
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
                                                                $rechazosDocExt = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->get();
                                                                $correccionesDocExt = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->get();
                                                                $validacionesDocExt = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->get();
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
                                                                                <p class="mb-0">VALIDACIÓN DE DOC. EXTRANJEROS</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($validacion->fechasubida)->format('d/m/Y') }} - {{ $validacion->tipodocumento }}</p>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                @if ($validacion->document)
                                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/NOTIFICACIÓN DE PODER/{$validacion->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
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
                                                                                <p class="mb-0">VALIDACIÓN DE DOC. EXTRANJEROS</p>
                                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                                <input type="hidden" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER">
                                                                                <input type="hidden" name="subprocedimiento[]" value="VALIDACIÓN DE DOCUMENTOS EXTRANJEROS">
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <div class="d-flex justify-content-center gap-2">
                                                                                    <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" style="max-width: 130px;" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                                    <select class="form-control" name="tipodocumento[]" style="max-width: 200px;">
                                                                                        <option value=""></option>
                                                                                        <option value="CERTIFICADO DE NACIMIENTO">CERTIFICADO DE NACIMIENTO</option>
                                                                                        <option value="CERTIFICADO DE MATRIMONIO">CERTIFICADO DE MATRIMONIO</option>
                                                                                        <option value="PASAPORTE">PASAPORTE</option>
                                                                                        <option value="PROTOCOLIZADO DE PODER">PROTOCOLIZADO DE PODER</option>
                                                                                    </select>
                                                                                </div>
                                                                            </td>
                                                                            <td class="align-middle text-center">
                                                                                <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                            </td>
                                                                        </tr>
                                                                    @endfor
                                                                @endif
                                                            </tbody>
                                                            @if ($validacionesDocExt->count() == 0)
                                                                <tr>
                                                                    <td colspan="3" class="text-left">
                                                                        <button type="button" id="agregar-fila-validacion" class="btn btn-sm btn-outline-primary">AGREGAR VALIDACIÓN DOC. EXTRANJERO</button>
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
                                                                            if (filasActuales >= 4) {
                                                                                alert('Solo se permiten hasta 4 documentos de validacion.');
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
                                                    @endif
                                                </tbody>
                                            </table>
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
                                    @csrf
                                    <div class="container">
                                        @php
                                            $documento5 = $cliente->tramites()->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->where('tramite', 'INVALIDEZ')->first();
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
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
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
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/FIRMA EAP/{$documento5->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
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
                                                                <select class="form-control" name="observacion[]">
                                                                    <option value="">Seleccione una opción...</option>
                                                                    <option value="APORTES NO ACREDITADOS">APORTES NO ACREDITADOS</option>
                                                                    <option value="APORTES EN EXCESO">APORTES EN EXCESO</option>
                                                                    <option value="APORTES EN MORA">APORTES EN MORA</option>
                                                                    <option value="PROCESO DE REGULARIZACION">PROCESO DE REGULARIZACION</option>
                                                                    <option value="PROCESO POR ACREDITACION">PROCESO POR ACREDITACION</option>
                                                                </select>
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
                                                        <select class="form-control" name="observacion[]">
                                                            <option value="">Seleccione una opción...</option>
                                                            <option value="APORTES NO ACREDITADOS">APORTES NO ACREDITADOS</option>
                                                            <option value="APORTES EN EXCESO">APORTES EN EXCESO</option>
                                                            <option value="APORTES EN MORA">APORTES EN MORA</option>
                                                            <option value="PROCESO DE REGULARIZACION">PROCESO DE REGULARIZACION</option>
                                                            <option value="PROCESO POR ACREDITACION">PROCESO POR ACREDITACION</option>
                                                        </select>
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
                                    </div>
                                    @if (!$documento5)
                                        <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2.- PROCESO EN CURSO --}}
                <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                    <div class="row">
                        {{-- SOLICITUD DE INFORMACION TECNICO MEDICO --}}
                        <div class="col-12 col-md-4 mb-3">
                            <div class="dropdown">
                                <button class="btn btn-custom btn-block text-center" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-briefcase-medical fa-5x mb-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documentos = [
                                        $documento21 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first(),
                                        $documento25 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first(),
                                        $documento27 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first(),
                                        $documento29 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first(),
                                    ];
                                    function getParteAntesDelGuion($subprocedimiento) {
                                        return explode(' _', $subprocedimiento)[0] ?? $subprocedimiento;
                                    }
                                    $subprocedimientos = collect($documentos)
                                        ->filter()
                                        ->map(function ($documento) {
                                            return getParteAntesDelGuion($documento->subprocedimiento);
                                        })
                                        ->unique()
                                        ->implode(', ');
                                @endphp

                                <div class="text-center">
                                    @if ($subprocedimientos)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> {{ $subprocedimientos }}
                                        </span>
                                    @else
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> NINGUNO
                                        </span>
                                    @endif
                                </div>

                                @php
                                    $documento21 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento99 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                    $documento22 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS')->where('tramite', 'INVALIDEZ')->first();
                                    $documento23 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento24 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ RESPUESTA TÉCNICO MÉDICO')->where('tramite', 'INVALIDEZ')->first();

                                    $documento25 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento26 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC')->where('tramite', 'INVALIDEZ')->first();
                                    $documentoprof = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ REGISTRO DE DOCUMENTACIÓN PROFESIONAL')->where('tramite', 'INVALIDEZ')->first();
                                
                                    $documento29 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento30 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                    $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                    $documento32 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ REMISIÓN DE RESPUESTA')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                {{-- <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item @if($documento21 || $documento99 || $documento22 || $documento23 || $documento24) text-success @endif" href="#" data-toggle="modal" data-target="#modalEnteGestorSalud">ENTE GESTOR DE SALUD</a>
                                    <a class="dropdown-item @if($documento25 || $documento26 || $documentoprof) text-success @endif" href="#" data-toggle="modal" data-target="#modalNotificacionTMC">NOTIFICACIÓN TMC</a>
                                    <a class="dropdown-item @if($documento29 || $documento30 || $documento31 || $documento32) text-success @endif" href="#" data-toggle="modal" data-target="#modalEmpleador">EMPLEADOR</a>
                                </div> --}}
                                @php
                                    $aseguradoraInvalida = is_null($aseguradora) || strtoupper($aseguradora) == 'NO REGISTRA';
                                @endphp
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item 
                                        @if($documento21 || $documento99 || $documento22 || $documento23 || $documento24) text-success @endif
                                        @if($aseguradoraInvalida) disabled-link @endif" 
                                        href="{{ $aseguradoraInvalida ? '#' : '' }}" 
                                        @unless($aseguradoraInvalida)
                                            data-toggle="modal" data-target="#modalEnteGestorSalud"
                                        @endunless>
                                        ENTE GESTOR DE SALUD
                                        @if($aseguradoraInvalida)
                                            <span class="no-seguro-label">(NO TIENE SEGURO)</span>
                                        @endif
                                    </a>
                                    <a class="dropdown-item 
                                        @if($documento25 || $documento26 || $documentoprof) text-success @endif
                                        @if($aseguradoraInvalida) disabled-link @endif" 
                                        href="{{ $aseguradoraInvalida ? '#' : '' }}" 
                                        @unless($aseguradoraInvalida)
                                            data-toggle="modal" data-target="#modalNotificacionTMC"
                                        @endunless>
                                        NOTIFICACIÓN TMC
                                        @if($aseguradoraInvalida)
                                            <span class="no-seguro-label">(NO TIENE SEGURO)</span>
                                        @endif
                                    </a>
                                    <a class="dropdown-item 
                                        @if($documento29 || $documento30 || $documento31 || $documento32) text-success @endif" 
                                        href="#" 
                                        data-toggle="modal" data-target="#modalEmpleador">
                                        EMPLEADOR
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- COMPRA DE SERVICIOS --}}
                        <div class="col-12 col-md-4 mb-3">
                            <button class="btn btn-custom btn-block text-center" type="button" data-toggle="modal" data-target="#modalCompradeservicios">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-money-bill-wave fa-5x mb-2"></i>
                                    <span class="h6 mb-0 btn-block text-center">COMPRA DE SERVICIOS</span>
                                </div>
                            </button>

                            <br>
                            @php
                                $documento10 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                $documento103 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'AGENDAMIENTO')->where('tramite', 'INVALIDEZ')->first();
                                $documento104 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'ORDENES DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                            @endphp
                            <div class="text-center">
                                @if (!$documento10 || !$documento103 || !$documento104)
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

                        {{-- SOLICITUD DE INFORMACION COMPLEMENTARIA --}}
                        <div class="col-12 col-md-4 mb-3">
                            <div class="dropdown">
                                <button class="btn btn-custom btn-block text-center" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-paste fa-5x mb-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">SOLICITUD DE INFORMACIÓN COMPLEMENTARIA</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documentos = [
                                        $documento210 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first(),
                                        $documento250 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first(),
                                        $documento270 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first(),
                                        $documento290 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first(),
                                        $documentocancelacion = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'CANCELACIÓN DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first(),
                                    ];
                                    function getParteAntesDelGuion2($subprocedimiento) {
                                        return explode(' _', $subprocedimiento)[0] ?? $subprocedimiento;
                                    }
                                    $subprocedimientos = collect($documentos)
                                        ->filter()
                                        ->map(function ($documento) {
                                            return getParteAntesDelGuion2($documento->subprocedimiento);
                                        })
                                        ->unique()
                                        ->implode(', ');
                                @endphp

                                <div class="text-center">
                                    @if ($subprocedimientos)
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> {{ $subprocedimientos }}
                                        </span>
                                    @else
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> NINGUNO
                                        </span>
                                    @endif
                                </div>
                                @php
                                    $documento210 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento990 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                    $documento220 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS')->where('tramite', 'INVALIDEZ')->first();
                                    $documento230 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA')->where('tramite', 'INVALIDEZ')->first();
                                    /* $documento240 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ RESPUESTA COMPLEMENTARIA')->where('tramite', 'INVALIDEZ')->first(); */

                                    $documento250 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento260 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC')->where('tramite', 'INVALIDEZ')->first();
                                    $documentoprofes = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ REGISTRO DE DOCUMENTACIÓN PROFESIONAL')->where('tramite', 'INVALIDEZ')->first();

                                    $documento290 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento300 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                    $documento310 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                    $documento320 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ REMISIÓN DE RESPUESTA')->where('tramite', 'INVALIDEZ')->first();

                                    $documentocancelacion = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'CANCELACIÓN DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item @if($documento210 || $documento990 || $documento220 || $documento230/*  || $documento240 */) text-success @endif" href="#" data-toggle="modal" data-target="#modalEnteGestorSalud2">ENTE GESTOR DE SALUD</a>
                                    <a class="dropdown-item @if($documento250 || $documento260 || $documentoprofes) text-success @endif" href="#" data-toggle="modal" data-target="#modalNotificacionTMC2">NOTIFICACIÓN TMC</a>
                                    <a class="dropdown-item @if($documento290 || $documento300 || $documento310 || $documento320) text-success @endif" href="#" data-toggle="modal" data-target="#modalEmpleador2">EMPLEADOR</a>
                                    <a class="dropdown-item @if($documentocancelacion) text-success @endif" href="#" data-toggle="modal" data-target="#modalCancelacion">CANCELACIÓN DE TRÁMITE</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                
                {{-- SOLICITUD DE INFORMACION TECNICO MEDICO --}}
                <!-- MODAL ENTE GESTOR DE SALUD -->
                <div class="modal fade" id="modalEnteGestorSalud" tabindex="-1" role="dialog" aria-labelledby="modalEnteGestorSaludLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalEnteGestorSaludLabel">ENTE GESTOR DE SALUD</h5>
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
                                            $documento21 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE REQUERIMIENTO')->where('tramite', 'INVALIDEZ')->first();
                                            $sitmentegestor = $cliente->tramites()
                                                ->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
                                                ->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE REQUERIMIENTO')
                                                ->where('tramite', 'INVALIDEZ')
                                            ->get();
                                            $documento22 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN MT')->where('tramite', 'INVALIDEZ')->first();
                                            $documento23 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento24 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD A EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                            $documento99 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                            $documento99otro = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICTUD DE MODIFICACION DE REQUERIMIENTO')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        <div class="table-responsive">
                                            <div class="scroll-shadow-wrapper">
                                                <div class="scroll-shadow-container">
                                                    <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -8px;">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width: 5%;">ID</th>
                                                                <th style="width: 5%;">NRO.</th>
                                                                <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                                <th style="width: 10%;">CITE_NOTIFICACIÓN</th>
                                                                <th style="width: 10%;">FECHA_CITE_NOTIF.</th>
                                                                <th style="width: 10%;">CITE_NOTA</th>
                                                                <th style="width: 10%;">FECHA_CITE_NOTA</th>
                                                                <th style="width: 10%;">ASEGURADORA</th>
                                                                <th style="width: 10%;">ESTADO_LABORAL</th>
                                                                <th style="width: 10%;">FECHA_REGISTRO</th>
                                                                <th style="width: 10%;">DOCUMENTO</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($sitmentegestor as $documento)
                                                                <tr>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">NOTIFICACIÓN DE REQUERIMIENTO</p>
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
                                                                        <span>{{ $aseguradora }}</span>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <span>{{ strtoupper($estadolaboral) }}</span>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                            @if ($sitmentegestor->isEmpty())
                                                                <tr class="fila-sitmentegestor">
                                                            @else
                                                                <tr class="fila-sitmentegestor d-none">
                                                            @endif
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">NOTIFICACIÓN DE REQUERIMIENTO</p>
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO">
                                                                    <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE REQUERIMIENTO">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control text-center" name="citenotificacion[]">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control text-center" name="citenota[]">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" name="fechacitenota[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <span>{{ $aseguradora }}</span>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <span>{{ strtoupper($estadolaboral) }}</span>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @if (!$sitmentegestor->isEmpty())
                                                        <div class="text-left mt-2" style="margin-bottom: 10px;">
                                                            <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarSITMentegestor()">AGREGAR MÁS</button>
                                                        </div>
                                                    @endif
                                                    <script>
                                                        function agregarSITMentegestor() {
                                                            const filaOculta = document.querySelector('.fila-sitmentegestor.d-none');
                                                            if (filaOculta) {
                                                                filaOculta.classList.remove('d-none');
                                                            }
                                                        }
                                                    </script>
                                                </div>
                                            </div>

                                            {{-- PROGRAMACION AL CLIENTE Y PROGRAMACIONES MEDICAS --}}
                                            @if (!$documento21)
                                                <div class="mb-3">
                                                    <label><strong>SELECCIONAR ESPECIALIDADES</strong></label><br>
                                                    <div class="form-check form-check-inline" hidden>
                                                        <input class="form-check-input" type="checkbox" value="PROGRAMACIÓN AL CLIENTE" onchange="agregarEspecialidad(this)" checked hidden id="checkProgCliente">
                                                        <label class="form-check-label" hidden>PROGRAMACIÓN AL CLIENTE</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" value="MEDICINA DEL TRABAJO" onchange="agregarEspecialidad(this)">
                                                        <label class="form-check-label">MEDICINA DEL TRABAJO</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" value="TRABAJO SOCIAL" onchange="agregarEspecialidad(this)">
                                                        <label class="form-check-label">TRABAJO SOCIAL</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="checkbox" value="HISTORIA CLINICA" onchange="agregarEspecialidad(this)">
                                                        <label class="form-check-label">HISTORIA CLINICA</label>
                                                    </div>
                                                </div>
                                            @endif
                                            <table class="table table-bordered table-sm align-middle" id="tablaProgramaciones2">
                                                @if (isset($registrosGuardadosProgramacion) && collect($registrosGuardadosProgramacion)->contains('estudioespecialidad', 'PROGRAMACIÓN AL CLIENTE'))
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th class="text-center">SUB_PROCEDIMIENTO</th>
                                                            <th class="text-center">ASEGURADORA</th>
                                                            <th class="text-center">FECHA</th>
                                                            <th class="text-center">HORA</th>
                                                            <th class="text-center">ASISTIÓ</th>
                                                            <th class="text-center">REPROG.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($registrosGuardadosProgramacion as $registro)
                                                            @if ($registro->estudioespecialidad === 'PROGRAMACIÓN AL CLIENTE')
                                                                <tr class="text-center">
                                                                    <input type="hidden" name="tramitenombreprog" value="INVALIDEZ">
                                                                    <td>
                                                                        {{ $registro->estudioespecialidad }}
                                                                        <input type="hidden" name="subtramite_id[]" value="{{ $registro->id }}">
                                                                        <input type="hidden" name="estudioespecialidad[]" value="{{ $registro->estudioespecialidad }}">
                                                                    </td>
                                                                    <td class="align-middle text-center"> 
                                                                        <span>{{$aseguradora}}</span>
                                                                    </td>
                                                                    <td hidden>
                                                                        <input type="text" name="nombremedicoprog[]" class="form-control form-control-sm" value="">
                                                                    </td>
                                                                    <td hidden>
                                                                        <input type="text" name="opcionatencion[]" class="form-control form-control-sm" value="">
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->fechaprogramacion)
                                                                            {{ $registro->fechareprogramacion ?? $registro->fechaprogramacion }}
                                                                            <input type="hidden" name="fechaprogramacion[]" value="{{ $registro->fechaprogramacion }}">
                                                                        @else
                                                                            <input type="date" name="fechaprogramacion[]" class="form-control form-control-sm" value="{{ old('fechaprogramacion[]', $registro->fechaprogramacion) }}" min="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->horaprogramacion)
                                                                            {{ $registro->horareprogramacion ?? $registro->horaprogramacion }}
                                                                            <input type="hidden" name="horaprogramacion[]" value="{{ $registro->horaprogramacion }}">
                                                                        @else
                                                                            <input type="time" name="horaprogramacion[]" class="form-control form-control-sm" value="{{ old('horaprogramacion[]', $registro->horaprogramacion) }}">
                                                                        @endif
                                                                    </td>
                                                                    <td hidden>
                                                                        <input type="file" name="ordenprogramacion[{{ $loop->index }}]" value="" class="form-control form-control-sm">
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->fechaprogramacion)
                                                                            @if ($registro->asistenciaprogramacion == 1)
                                                                                SI
                                                                                <input type="hidden" name="asistenciaprogramacion[]" value="{{ $registro->id }}">
                                                                            @else
                                                                                <input type="checkbox" name="asistenciaprogramacion[]" value="{{ $registro->id }}">
                                                                            @endif
                                                                        @else
                                                                            <input type="checkbox" disabled>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->fechaprogramacion && $registro->asistenciaprogramacion == 0)
                                                                            <button type="button" class="btn btn-sm btn-verdocumento" onclick="habilitarReprogramacion(this)">REPROGRAMAR</button>
                                                                            <div class="mt-2 d-none">
                                                                                <input type="date" name="fechareprogramacion[{{ $registro->id }}]" class="form-control form-control-sm mb-1" value="{{ old('fechareprogramacion[]', $registro->fechareprogramacion) }}">
                                                                                <input type="time" name="horareprogramacion[{{ $registro->id }}]" class="form-control form-control-sm mb-1" value="{{ old('horareprogramacion[]', $registro->horareprogramacion) }}">
                                                                                <input type="text" name="motivoreprogramacion[{{ $registro->id }}]" class="form-control form-control-sm" placeholder="Motivo" value="{{ old('motivoreprogramacion[]', $registro->motivoreprogramacion) }}">
                                                                            </div>
                                                                        @elseif ($registro->motivoreprogramacion)
                                                                            CON REPROG. - MOTIVO: {{ $registro->motivoreprogramacion }}
                                                                        @else
                                                                            SIN REPROG.
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                @endif
                                            </table>
                                            <table class="table table-bordered table-sm align-middle" id="tablaProgramaciones" style="margin-bottom: -8px;">
                                                {{-- @if (isset($registrosGuardadosProgramacion) && collect($registrosGuardadosProgramacion)->contains(function($item) {
                                                    return $item->estudioespecialidad !== 'PROGRAMACIÓN AL CLIENTE';
                                                })) --}}
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th class="text-center">ESTUDIO/ESPECIALIDAD</th>
                                                            @if (isset($registrosGuardadosProgramacion) && collect($registrosGuardadosProgramacion)->contains(function($item) {
                                                                return $item->estudioespecialidad !== 'PROGRAMACIÓN AL CLIENTE';
                                                            }))
                                                            <th class="text-center">MÉDICO</th>
                                                            <th class="text-center">FECHA</th>
                                                            <th class="text-center">HORA</th>
                                                            <th class="text-center">OPCION_ATENCIÓN</th>
                                                            <th class="text-center">ORDEN</th>
                                                            <th class="text-center">ASISTIÓ</th>
                                                            <th class="text-center">REPROG.</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($registrosGuardadosProgramacion as $registro)
                                                            @if ($registro->estudioespecialidad !== 'PROGRAMACIÓN AL CLIENTE')
                                                                <tr class="text-center">
                                                                    <input type="hidden" name="tramitenombreprog" value="INVALIDEZ">
                                                                    <td>
                                                                        {{ $registro->estudioespecialidad }}
                                                                        <input type="hidden" name="subtramite_id[]" value="{{ $registro->id }}">
                                                                        <input type="hidden" name="estudioespecialidad[]" value="{{ $registro->estudioespecialidad }}">
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->fechaprogramacion || $registro->nombremedico)
                                                                            {{ $registro->nombremedico }}
                                                                            <input type="hidden" name="nombremedicoprog[]" value="{{ $registro->nombremedico }}">
                                                                        @else
                                                                            <input type="text" name="nombremedicoprog[]" class="form-control form-control-sm" value="{{ old('nombremedicoprog[]', $registro->nombremedico) }}">
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->fechaprogramacion)
                                                                            {{ $registro->fechareprogramacion ?? $registro->fechaprogramacion }}
                                                                            <input type="hidden" name="fechaprogramacion[]" value="{{ $registro->fechaprogramacion }}">
                                                                        @else
                                                                            <input type="date" name="fechaprogramacion[]" class="form-control form-control-sm" value="{{ old('fechaprogramacion[]', $registro->fechaprogramacion) }}" min="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->horaprogramacion)
                                                                            {{ $registro->horareprogramacion ?? $registro->horaprogramacion }}
                                                                            <input type="hidden" name="horaprogramacion[]" value="{{ $registro->horaprogramacion }}">
                                                                        @else
                                                                            <input type="time" name="horaprogramacion[]" class="form-control form-control-sm" value="{{ old('horaprogramacion[]', $registro->horaprogramacion) }}">
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->opcionatencion)
                                                                            {{ $registro->opcionatencion }}
                                                                            <input type="hidden" name="opcionatencion[]" value="{{ $registro->opcionatencion }}">
                                                                        @else
                                                                            <select name="opcionatencion[]" class="form-control form-control-sm">
                                                                                <option value="" disabled selected>Seleccione una opción</option>
                                                                                <option value="{{ $aseguradora }}">{{ $aseguradora }}</option>
                                                                                <option value="COMPRA DE SERVICIOS">COMPRA DE SERVICIOS</option>
                                                                            </select>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->ordenprogramacion)
                                                                            <a href="{{ asset("tramitesclientesita/{$cliente->id}/INVALIDEZ/ORDENES/{$registro->ordenprogramacion}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER ORDEN</a>
                                                                        @else
                                                                            <input type="file" name="ordenprogramacion[{{ $loop->index }}]" class="form-control form-control-sm">
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->fechaprogramacion)
                                                                            @if ($registro->asistenciaprogramacion == 1)
                                                                                SI
                                                                                <input type="hidden" name="asistenciaprogramacion[]" value="{{ $registro->id }}">
                                                                            @else
                                                                                <input type="checkbox" name="asistenciaprogramacion[]" value="{{ $registro->id }}">
                                                                            @endif
                                                                        @else
                                                                            <input type="checkbox" disabled>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($registro->fechaprogramacion)
                                                                            @if ($registro->asistenciaprogramacion == 0)
                                                                                <button type="button" class="btn btn-sm btn-verdocumento" onclick="habilitarReprogramacion(this)">REPROGRAMAR</i></button>
                                                                                <div class="mt-2 d-none">
                                                                                    <input type="date" name="fechareprogramacion[{{ $registro->id }}]" class="form-control form-control-sm mb-1" value="{{ old('fechareprogramacion[]', $registro->fechareprogramacion) }}">
                                                                                    <input type="time" name="horareprogramacion[{{ $registro->id }}]" class="form-control form-control-sm mb-1" value="{{ old('horareprogramacion[]', $registro->horareprogramacion) }}">
                                                                                    <input type="text" name="motivoreprogramacion[{{ $registro->id }}]" class="form-control form-control-sm" placeholder="Motivo" value="{{ old('motivoreprogramacion[]', $registro->motivoreprogramacion) }}">
                                                                                </div>
                                                                            @else
                                                                                @if ($registro->motivoreprogramacion)
                                                                                    CON REPROG. - MOTIVO: {{ $registro->motivoreprogramacion }}
                                                                                @else
                                                                                    SIN REPROG.
                                                                                @endif
                                                                            @endif
                                                                        @else
                                                                            VACIO
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    {{-- @elseif (isset($documento21) && count($registrosGuardadosProgramacion ?? []) === 0)
                                                        <tr>
                                                            <td colspan="6">SIN OBSERVACIONES</td>
                                                        </tr>
                                                    @endif --}}
                                                </tbody>
                                            </table>
                                            <div class="text-left mt-2">
                                                <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarFilaManual()">AGREGAR MÁS</button>
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function () {
                                                    const checkbox = document.getElementById('checkProgCliente');
                                                    if (checkbox && checkbox.checked) {
                                                        agregarEspecialidad(checkbox);
                                                    }
                                                });
                                            </script>
                                            <script>
                                                function habilitarReprogramacion(btn) {
                                                    const contenedor = btn.nextElementSibling;
                                                    if (contenedor.classList.contains('d-none')) {
                                                        contenedor.classList.remove('d-none');
                                                    } else {
                                                        contenedor.classList.add('d-none');
                                                    }
                                                }
                                            </script>
                                            <script>
                                                const especialidadesAgregadas = new Set();
                                                const todasareas = @json($todasareas);

                                                function agregarEspecialidad(checkbox) {
                                                    const tabla = document.getElementById('tablaProgramaciones').getElementsByTagName('tbody')[0];
                                                    const valor = checkbox.value;

                                                    if (checkbox.checked && !especialidadesAgregadas.has(valor)) {
                                                        const fila = tabla.insertRow();
                                                        const celda = fila.insertCell();
                                                        celda.setAttribute("colspan", 8);

                                                        if (valor === "PROGRAMACIÓN AL CLIENTE") {
                                                            celda.innerHTML = `<input type="hidden" name="estudioespecialidad[]" value="${valor}">`;
                                                            fila.style.display = 'none';
                                                        } else {
                                                            celda.innerHTML = `<input type="hidden" name="estudioespecialidad[]" value="${valor}">${valor}`;
                                                        }

                                                        especialidadesAgregadas.add(valor);
                                                    } else if (!checkbox.checked && especialidadesAgregadas.has(valor)) {
                                                        for (let i = 0; i < tabla.rows.length; i++) {
                                                            const input = tabla.rows[i].querySelector(`input[value="${valor}"]`);
                                                            if (input) {
                                                                tabla.deleteRow(i);
                                                                break;
                                                            }
                                                        }
                                                        especialidadesAgregadas.delete(valor);
                                                    }
                                                }

                                                function agregarFilaManual() {
                                                    const tabla = document.getElementById('tablaProgramaciones').getElementsByTagName('tbody')[0];
                                                    const fila = tabla.insertRow();

                                                    const celda = fila.insertCell();
                                                    celda.setAttribute("colspan", 8);
                                                    celda.innerHTML = `
                                                        <select class="form-control form-control-sm" name="estudioespecialidad[]">
                                                            <option value="" disabled selected>Seleccione un estudio/especialidad...</option>
                                                            ${todasareas.map(area => `<option value="${area.area}">${area.area}</option>`).join('')}
                                                        </select>
                                                    `;
                                                }
                                            </script>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto" target="_blank">GUARDAR</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL NOTIFICACION TMC -->
                <div class="modal fade" id="modalNotificacionTMC" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionTMCLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalNotificacionTMCLabel">NOTIFICACIÓN TMC</h5>
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
                                            $documento25 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento26 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC')->where('tramite', 'INVALIDEZ')->first();
                                            $documento26reg = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ REGISTRO DE DOCUMENTACIÓN PROFESIONAL')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        <div class="table-responsive">
                                            {{-- NOTIFICACIÓN DE GESTORA --}}
                                            <table class="table table-bordered table-sm align-middle text-center">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 10%;">CITE_EMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_EMISIÓN</th>
                                                        <th style="width: 10%;">CITE_REMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_REMISIÓN</th>
                                                        <th style="width: 10%;">ASEGURADORA</th>
                                                        <th style="width: 10%;">FECHA_REGISTRO</th>
                                                        <th style="width: 10%;">FECHA_RETORNO</th>
                                                        <th style="width: 10%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                                            @if (!$documento25)
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO">
                                                                <input type="hidden" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento25)
                                                                <p class="mb-0">{{ $documento25->citeemision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeemision1" name="citeemision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento25)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento25->fechaemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fechaemision1" name="fechaemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento25)
                                                                <p class="mb-0">{{ $documento25->citeremision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeremision1" name="citeremision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento25)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento25->fecharemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fecharemision1" name="fecharemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center"> 
                                                            <span>{{$aseguradora}}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento25)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento25->fechasubida)->format('d-m-Y') }}</p>
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
                                                                <input type="date" class="form-control text-center" id="fechasubida4" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }} onchange="actualizarFechaRetorno4(this)">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento25)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento25->fecharetorno)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fecharetorno4" name="fecharetorno[]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento25)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento25->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento25->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" id="archivo1" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- RESPUESTA A NOTIFICACIÓN TMC --}}
                                            <table class="table table-bordered table-sm align-middle text-center">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 30%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 15%;">TIPO_MÉDICO</th>
                                                        <th style="width: 25%;">NOMBRE_MÉDICO</th>
                                                        <th style="width: 15%;">FECHA_REGISTRO</th>
                                                        <th style="width: 15%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">RESPUESTA A NOTIFICACIÓN TMC</p>
                                                            @if (!$documento26)
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO">
                                                                <input type="hidden" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento26)
                                                                <p class="mb-0">{{ $documento26->tipomedico }}</p>
                                                            @else
                                                                <select class="form-control" name="tipomedico[]">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    <option value="INTERNO">INTERNO</option>
                                                                    <option value="EXTERNO">EXTERNO</option>
                                                                </select>                                            
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento26)
                                                                <p class="mb-0">{{ $documento26->nombremedico }} @if (!empty($documento26->nombremedico2)) - {{ $documento26->nombremedico2 }}@endif</p>
                                                            @else
                                                                <select class="form-control" name="nombremedico[]">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    @foreach ($proveedoresmedicos as $id => $nombre)
                                                                        <option value="{{ $nombre }}">{{ $nombre }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <select class="form-control" name="nombremedico2[]">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    @foreach ($proveedoresmedicos as $id => $nombre)
                                                                        <option value="{{ $nombre }}">{{ $nombre }}</option>
                                                                    @endforeach
                                                                </select>                                            
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center"> 
                                                            @if ($documento26)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento26->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center"
                                                                    id="fechasubida2"
                                                                    name="fechasubida[]"
                                                                    value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                    {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento26)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento26->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento26->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" id="archivo2" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- REGISTRO DE DOCUMENTACIÓN PROFESIONAL --}}
                                            @if (!$documento26reg)
                                                <div class="text-right mb-2">
                                                    <button type="button" class="btn btn-sm btn-verdocumento" onclick="document.getElementById('tablaRegistroDoc').style.display = '';">
                                                        AGREGAR REGISTRO DE DOC. PROF.
                                                    </button>
                                                </div>
                                            @endif
                                            <table class="table table-bordered table-sm align-middle text-center" id="tablaRegistroDoc" style="{{ !$documento26reg ? 'display: none;' : '' }}; margin-top: -5px;">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 40%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 30%;">FECHA_REGISTRO</th>
                                                        <th style="width: 30%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">REGISTRO DE DOCUMENTACIÓN PROFESIONAL</p>
                                                            @if (!$documento26reg)
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO">
                                                                <input type="hidden" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ REGISTRO DE DOCUMENTACIÓN PROFESIONAL">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center"> 
                                                            @if ($documento26reg)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento26reg->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center"
                                                                    id="fechasubida3"
                                                                    name="fechasubida[]"
                                                                    value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                    {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento26reg)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento26reg->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento26reg->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" id="archivo3" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @if (!$documento25 || !$documento26 || !$documento26reg)
                                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">SUBIR ARCHIVOS</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL EMPLEADOR -->
                <div class="modal fade" id="modalEmpleador" tabindex="-1" role="dialog" aria-labelledby="modalEmpleadorLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalEmpleadorLabel">EMPLEADOR</h5>
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
                                            $fecha = \Carbon\Carbon::now();
                                            $valor = $fecha->day <= 15 ? $fecha : $fecha->copy()->addMonth();
                                            $valorFormateado = $valor->format('m/y');

                                            $documento29 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE REQUERIMIENTO')->where('tramite', 'INVALIDEZ')->first();
                                            $documento30 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ CARTA SOLICITUD A EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                            $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE MODIFICACIÓN DE CITE')->where('tramite', 'INVALIDEZ')->first();

                                            $sitmempleadornotificacion = $cliente->tramites()
                                                ->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
                                                ->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE REQUERIMIENTO')
                                                ->where('tramite', 'INVALIDEZ')
                                            ->get();
                                            $sitmempleadorrespuesta = $cliente->tramites()
                                                ->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')
                                                ->where('subprocedimiento', 'EMPLEADOR _ RESPUESTA A REQUERIMIENTO')
                                                ->where('tramite', 'INVALIDEZ')
                                            ->get();
                                        @endphp
                                        <div class="table-responsive">
                                            <div class="scroll-shadow-wrapper">
                                                <div class="scroll-shadow-container">
                                                    <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -8px;">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width: 5%;">ID</th>
                                                                <th style="width: 5%;">NRO.</th>
                                                                <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                                <th style="width: 10%;">CITE_NOTIFICACIÓN</th>
                                                                <th style="width: 10%;">FECHA_CITE_NOTIF.</th>
                                                                <th style="width: 10%;">CITE_NOTA</th>
                                                                <th style="width: 10%;">FECHA_CITE_NOTA</th>
                                                                <th style="width: 10%;">TIPO_DOCUMENTO</th>
                                                                <th style="width: 10%;">FECHA_REGISTRO</th>
                                                                <th style="width: 10%;">FECHA_RETORNO</th>
                                                                <th style="width: 10%;">DOCUMENTO</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($sitmempleadornotificacion as $documento)
                                                                <tr>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">NOTIFICACIÓN DE REQUERIMIENTO</p>
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
                                                                        <p class="mb-0">{{ $documento->tipodocumento }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fecharetorno)->format('d-m-Y') }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                            @if ($sitmempleadornotificacion->isEmpty())
                                                                <tr class="fila-sitmempleadornotificacion">
                                                            @else
                                                                <tr class="fila-sitmempleadornotificacion d-none">
                                                            @endif
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">NOTIFICACIÓN DE REQUERIMIENTO</p>
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO">
                                                                    <input type="hidden" name="subprocedimiento[]" value="EMPLEADOR _ NOTIFICACIÓN DE REQUERIMIENTO">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control text-center" name="citenotificacion[]">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control text-center" name="citenota[]">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" name="fechacitenota[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <select class="form-control" name="tipodocumento[]">
                                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                                        <option value="INFORME DEL EMPLEADOR">INFORME DEL EMPLEADOR</option>
                                                                        <option value="RECORD DE SERVICIOS">RECORD DE SERVICIOS</option>
                                                                        <option value="COPIA DE EXAMENES PERIODICOS ANUALES DE EMPRESA">COPIA DE EXAMENES PERIODICOS ANUALES DE EMPRESA</option>
                                                                        <option value="CERTIFICADO DE AÑOS DE APORTES">CERTIFICADO DE AÑOS DE APORTES</option>
                                                                        <option value="CERTIFICADO DE TRABAJO">CERTIFICADO DE TRABAJO</option>
                                                                        <option value="PLANILLA DE PAGOS">PLANILLA DE PAGOS</option>
                                                                        <option value="BOLETAS DE PAGO">BOLETAS DE PAGO</option>
                                                                        <option value="DENUNCIA DE ACCIDENTE">DENUNCIA DE ACCIDENTE</option>
                                                                    </select>  
                                                                </td>
                                                                <td class="align-middle text-center">
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
                                                                    <input type="date" class="form-control text-center" id="fechasubida7" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }} onchange="actualizarFechaRetorno7(this)">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" id="fecharetorno7" name="fecharetorno[]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @if (!$sitmempleadornotificacion->isEmpty())
                                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarSITMempleadornotificacion()">AGREGAR MÁS</button>
                                                    </div>
                                                    @endif
                                                    <script>
                                                        function agregarSITMempleadornotificacion() {
                                                            const filaOculta = document.querySelector('.fila-sitmempleadornotificacion.d-none');
                                                            if (filaOculta) {
                                                                filaOculta.classList.remove('d-none');
                                                            }
                                                        }
                                                    </script>
                                                </div>
                                            </div>

                                            {{-- <div class="scroll-shadow-wrapper">
                                                <div class="scroll-shadow-container"> --}}
                                                    <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -8px;">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width: 5%;">ID</th>
                                                                <th style="width: 5%;">NRO.</th>
                                                                <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                                                <th style="width: 15%;">FECHA_REGISTRO</th>
                                                                <th style="width: 15%;">FECHA_RETORNO</th>
                                                                <th style="width: 20%;">CARTA</th>
                                                                <th style="width: 20%;">FORMULARIO</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($sitmempleadorrespuesta as $documento)
                                                                <tr>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">RESPUESTA A REQUERIMIENTO</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fecharetorno)->format('d-m-Y') }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO/{$documento->document2}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                            @if ($sitmempleadorrespuesta->isEmpty())
                                                                <tr class="fila-sitmempleadorrespuesta">
                                                            @else
                                                                <tr class="fila-sitmempleadorrespuesta d-none">
                                                            @endif
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">RESPUESTA A REQUERIMIENTO</p>
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO">
                                                                    <input type="hidden" name="subprocedimiento[]" value="EMPLEADOR _ RESPUESTA A REQUERIMIENTO">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    @php
                                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                        $fechaLimiteOriginal = \Carbon\Carbon::now()->addDays(45);
                                                                        $fechaLimite = $fechaLimiteOriginal->copy();
                                                                        if ($fechaLimite->isSaturday()) {
                                                                            $fechaLimite->subDay();
                                                                        } elseif ($fechaLimite->isSunday()) {
                                                                            $fechaLimite->addDay();
                                                                        }
                                                                        $fechaLimiteStr = $fechaLimite->toDateString();
                                                                    @endphp
                                                                    <input type="date" class="form-control text-center" id="fechasubida8" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }} onchange="actualizarFechaRetorno8(this)">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" id="fecharetorno8" name="fecharetorno[]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="file" name="archivo2" class="dropify mx-auto d-block" accept="application/pdf">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @if (!$sitmempleadorrespuesta->isEmpty())
                                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarSITMempleadorrespuesta()">AGREGAR MÁS</button>
                                                    </div>
                                                    @endif
                                                    <script>
                                                        function agregarSITMempleadorrespuesta() {
                                                            const filaOculta = document.querySelector('.fila-sitmempleadorrespuesta.d-none');
                                                            if (filaOculta) {
                                                                filaOculta.classList.remove('d-none');
                                                            }
                                                        }
                                                    </script>
                                                {{-- </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        $('#tipoPdfSelect').change(function() {
                            var selectedOption = $(this).val();
                            $('.modal').modal('hide');
                            switch (selectedOption) {
                                case 'ENTE GESTOR DE SALUD':
                                    $('#modalEnteGestorSalud').modal('show');
                                    break;
                                case 'NOTIFICACIÓN TMC':
                                    $('#modalNotificacionTMC').modal('show');
                                    break;
                                case 'NOTIFICACIÓN TMR':
                                    $('#modalNotificacionTMR').modal('show');
                                    break;
                                case 'EMPLEADOR':
                                    $('#modalEmpleador').modal('show');
                                    break;
                                default:
                                    break;
                            }
                        });
                    });
                </script>

                {{-- COMPRA DE SERVICIOS --}}
                <!-- MODAL COMPRA DE SERVICIOS -->
                <div class="modal fade" id="modalCompradeservicios" tabindex="-1" role="dialog" aria-labelledby="modalCompradeserviciosLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalCompradeserviciosLabel">COMPRA DE SERVICIOS</h5>
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
                                            $compraser1 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'SOLICITUD DE REQUERIMIENTO')->where('tramite', 'INVALIDEZ')->first();
                                            $cssolicitud = $cliente->tramites()
                                                ->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')
                                                ->where('subprocedimiento', 'SOLICITUD DE REQUERIMIENTO')
                                                ->where('tramite', 'INVALIDEZ')
                                            ->get();
                                            $csavisoviajeasegurado = $cliente->tramites()
                                                ->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')
                                                ->where('subprocedimiento', 'AVISO DE VIAJE DEL ASEGURADO')
                                                ->where('tramite', 'INVALIDEZ')
                                            ->get();
                                            $csordentraslado = $cliente->tramites()
                                                ->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')
                                                ->where('subprocedimiento', 'ORDEN DE TRASLADO')
                                                ->where('tramite', 'INVALIDEZ')
                                            ->get();
                                            $cssegundanotificacion = $cliente->tramites()
                                                ->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')
                                                ->where('subprocedimiento', 'SEGUNDA NOTIFICACIÓN')
                                                ->where('tramite', 'INVALIDEZ')
                                            ->get();
                                            $csdevolucionviaticos = $cliente->tramites()
                                                ->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')
                                                ->where('subprocedimiento', 'DEVOLUCIÓN DE VIÁTICOS')
                                                ->where('tramite', 'INVALIDEZ')
                                            ->get();
                                            $viaticosi = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'SEGUNDA NOTIFICACIÓN')->where('viaticos', 'SI')->where('tramite', 'INVALIDEZ')->first();
                                            $compraser1acep = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'SOLICITUD DE REQUERIMIENTO')->where('viaja', 'SI')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        <div class="table-responsive">
                                            {{-- SOLICITUD DE REQUERIMIENTO --}}
                                            <div class="scroll-shadow-wrapper">
                                                <div class="scroll-shadow-container">
                                                    <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -8px;">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width: 5%;">ID</th>
                                                                <th style="width: 5%;">NRO.</th>
                                                                <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                                <th style="width: 10%;">CITE_NOTIFICACIÓN</th>
                                                                <th style="width: 5%;">FECHA_CITE_NOTIF.</th>
                                                                <th style="width: 10%;">CITE_NOTA</th>
                                                                <th style="width: 5%;">FECHA_CITE_NOTA</th>
                                                                <th style="width: 10%;">¿TIENE_TRASLADO?</th>
                                                                <th style="width: 10%;">CIUDAD_TRASLADO</th>
                                                                <th style="width: 5%;">FECHA_REGISTRO</th>
                                                                <th style="width: 5%;">FECHA_RETORNO</th>
                                                                <th style="width: 10%;">CARTA</th>
                                                                <th style="width: 10%;">FORMULARIO</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($cssolicitud as $documento)
                                                                <tr>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">SOLICITUD DE REQUERIMIENTO</p>
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
                                                                        <p class="mb-0">{{ $documento->viaja }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">
                                                                            {{ $documento->departamentoviaja ?? 'NINGUNO' }}
                                                                            @if (!empty($documento->transporteviaja))
                                                                                - {{ $documento->transporteviaja }}
                                                                            @endif
                                                                            @if (!empty($documento->decisionviaja))
                                                                                - {{ $documento->decisionviaja }}
                                                                            @endif
                                                                        </p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fecharetorno)->format('d-m-Y') }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/COMPRA DE SERVICIOS/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/COMPRA DE SERVICIOS/{$documento->document2}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                            @if ($cssolicitud->isEmpty())
                                                                <tr class="fila-cssolicitud">
                                                            @else
                                                                <tr class="fila-cssolicitud d-none">
                                                            @endif
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">SOLICITUD DE REQUERIMIENTO</p>
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS">
                                                                    <input type="hidden" name="subprocedimiento[]" value="SOLICITUD DE REQUERIMIENTO">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control text-center" name="citenotificacion[]">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control text-center" name="citenota[]">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" name="fechacitenota[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <select class="form-control" id="viaja1" name="viaja[]" onchange="toggleVisibility2(this)">
                                                                        <option value="" disabled selected>¿Viaja?</option>
                                                                        <option value="SI">SI</option>
                                                                        <option value="NO">NO</option>
                                                                    </select>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <select class="form-control" id="departamentoviaja1" name="departamentoviaja[]" disabled>
                                                                        <option value="" disabled selected>Departamento</option>
                                                                        <option value="BENI">BENI</option>
                                                                        <option value="COCHABAMBA">COCHABAMBA</option>
                                                                        <option value="LA PAZ">LA PAZ</option>
                                                                        <option value="ORURO">ORURO</option>
                                                                        <option value="PANDO">PANDO</option>
                                                                        <option value="POTOSI">POTOSI</option>
                                                                        <option value="SANTA CRUZ">SANTA CRUZ</option>
                                                                        <option value="SUCRE">SUCRE</option>
                                                                        <option value="TARIJA">TARIJA</option>
                                                                    </select>

                                                                    <select class="form-control" id="transporteviaja1" name="transporteviaja[]" onchange="mostrarTablaTraslado(this)" style="display: none;">
                                                                        <option value="" disabled selected>Transporte...</option>
                                                                        <option value="TERRESTRE">TERRESTRE</option>
                                                                        <option value="AÉREO">AÉREO</option>
                                                                    </select>

                                                                    <select class="form-control" id="decisionviaja1" name="decisionviaja[]" onchange="mostrarTablaTraslado(this)" style="display: none;">
                                                                        <option value="" disabled selected>Decisión...</option>
                                                                        <option value="ACEPTADO">ACEPTADO</option>
                                                                        <option value="RECHAZADO">RECHAZADO</option>
                                                                    </select>
                                                                </td>

                                                                <script>
                                                                    function toggleVisibility2(selectElement) {
                                                                        const id = selectElement.id.replace('viaja', '');
                                                                        const transporteviaja = document.getElementById('transporteviaja' + id);
                                                                        const decisionviaja = document.getElementById('decisionviaja' + id);
                                                                        const departamentoviaja = document.getElementById('departamentoviaja' + id);

                                                                        if (selectElement.value === "SI") {
                                                                            transporteviaja.style.display = "block";
                                                                            decisionviaja.style.display = "block";
                                                                            departamentoviaja.disabled = false;
                                                                        } else {
                                                                            transporteviaja.style.display = "none";
                                                                            transporteviaja.value = "";
                                                                            decisionviaja.style.display = "none";
                                                                            decisionviaja.value = "";
                                                                            departamentoviaja.disabled = true;
                                                                            departamentoviaja.selectedIndex = 0;
                                                                        }
                                                                    }
                                                                </script>

                                                                <td class="align-middle text-center">
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
                                                                    <input type="date" class="form-control text-center" id="fechasubida9" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }} onchange="actualizarFechaRetorno9(this)">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" id="fecharetorno9" name="fecharetorno[]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="file" name="archivo2" class="dropify mx-auto d-block" accept="application/pdf">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @if (!$cssolicitud->isEmpty())
                                                        <div class="text-left mt-2" style="margin-bottom: 10px;">
                                                            <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarCSsolicitud()">AGREGAR MÁS</button>
                                                        </div>
                                                    @endif
                                                    <script>
                                                        function agregarCSsolicitud() {
                                                            const filaOculta = document.querySelector('.fila-cssolicitud.d-none');
                                                            if (filaOculta) {
                                                                filaOculta.classList.remove('d-none');
                                                            }
                                                        }
                                                    </script>
                                                </div>
                                            </div>
                                            
                                            @if ($compraser1acep)
                                                {{-- AVISO DE VIAJE DEL ASEGURADO --}}
                                                <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -8px;">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="width: 10%;">ID</th>
                                                            <th style="width: 10%;">NRO.</th>
                                                            <th style="width: 30%;">SUB_PROCEDIMIENTO</th>
                                                            <th style="width: 25%;">FECHA_REGISTRO</th>
                                                            <th style="width: 25%;">DOCUMENTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($csavisoviajeasegurado as $documento)
                                                            <tr>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ $documento->id }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ $documento->nro }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">AVISO DE VIAJE DEL ASEGURADO</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/COMPRA DE SERVICIOS/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        @if ($csavisoviajeasegurado->isEmpty())
                                                            <tr class="fila-csavisoviajeasegurado">
                                                        @else
                                                            <tr class="fila-csavisoviajeasegurado d-none">
                                                        @endif
                                                            <td class="align-middle text-center">
                                                                <input type="text" class="form-control" disabled>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="text" class="form-control" disabled>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">AVISO DE VIAJE DEL ASEGURADO</p>
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS">
                                                                <input type="hidden" name="subprocedimiento[]" value="AVISO DE VIAJE DEL ASEGURADO">
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @php
                                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                @endphp
                                                                <input type="date" class="form-control text-center" id="fechasubida9" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @if (!$csavisoviajeasegurado->isEmpty())
                                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarCSavisoviajeasegurado()">AGREGAR MÁS</button>
                                                    </div>
                                                @endif
                                                <script>
                                                    function agregarCSavisoviajeasegurado() {
                                                        const filaOculta = document.querySelector('.fila-csavisoviajeasegurado.d-none');
                                                        if (filaOculta) {
                                                            filaOculta.classList.remove('d-none');
                                                        }
                                                    }
                                                </script>

                                                {{-- ORDEN DE TRASLADO --}}
                                                <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -8px;">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="width: 10%;">ID</th>
                                                            <th style="width: 10%;">NRO.</th>
                                                            <th style="width: 30%;">SUB_PROCEDIMIENTO</th>
                                                            <th style="width: 25%;">FECHA_REGISTRO</th>
                                                            <th style="width: 25%;">DOCUMENTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($csordentraslado as $documento)
                                                            <tr>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ $documento->id }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ $documento->nro }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">ORDEN DE TRASLADO</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/COMPRA DE SERVICIOS/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        @if ($csordentraslado->isEmpty())
                                                            <tr class="fila-csordentraslado">
                                                        @else
                                                            <tr class="fila-csordentraslado d-none">
                                                        @endif
                                                            <td class="align-middle text-center">
                                                                <input type="text" class="form-control" disabled>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="text" class="form-control" disabled>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">ORDEN DE TRASLADO</p>
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS">
                                                                <input type="hidden" name="subprocedimiento[]" value="ORDEN DE TRASLADO">
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @php
                                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                @endphp
                                                                <input type="date" class="form-control text-center" id="fechasubida9" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @if (!$csordentraslado->isEmpty())
                                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarCSordentraslado()">AGREGAR MÁS</button>
                                                    </div>
                                                @endif
                                                <script>
                                                    function agregarCSordentraslado() {
                                                        const filaOculta = document.querySelector('.fila-csordentraslado.d-none');
                                                        if (filaOculta) {
                                                            filaOculta.classList.remove('d-none');
                                                        }
                                                    }
                                                </script>
                                            @endif

                                            {{-- SEGUNDA NOTIFICACION --}}
                                            <div class="scroll-shadow-wrapper">
                                                <div class="scroll-shadow-container">
                                                    <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -8px;">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th style="width: 5%;">ID</th>
                                                                <th style="width: 5%;">NRO.</th>
                                                                <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                                <th style="width: 10%;">CITE_NOTIFICACIÓN</th>
                                                                <th style="width: 5%;">FECHA_CITE_NOTIF.</th>
                                                                <th style="width: 10%;">CITE_NOTA</th>
                                                                <th style="width: 5%;">FECHA_CITE_NOTA</th>
                                                                <th style="width: 10%;">¿TIENE_TRASLADO?</th>
                                                                <th style="width: 10%;">CIUDAD_TRASLADO</th>
                                                                <th style="width: 5%;">FECHA_REGISTRO</th>
                                                                <th style="width: 5%;">FECHA_RETORNO</th>
                                                                <th style="width: 10%;">¿VIÁTICOS?</th>
                                                                <th style="width: 10%;">DOCUMENTO</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($cssegundanotificacion as $documento)
                                                                <tr>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">SEGUNDA NOTIFICACIÓN</p>
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
                                                                        <p class="mb-0">{{ $documento->viaja }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">
                                                                            {{ $documento->departamentoviaja ?? 'NINGUNO' }}
                                                                            @if (!empty($documento->transporteviaja))
                                                                                - {{ $documento->transporteviaja }}
                                                                            @endif
                                                                            @if (!empty($documento->decisionviaja))
                                                                                - {{ $documento->decisionviaja }}
                                                                            @endif
                                                                        </p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fecharetorno)->format('d-m-Y') }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <p class="mb-0">{{ $documento->viaticos }}</p>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/COMPRA DE SERVICIOS/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                            @if ($cssegundanotificacion->isEmpty())
                                                                <tr class="fila-cssegundanotificacion">
                                                            @else
                                                                <tr class="fila-cssegundanotificacion d-none">
                                                            @endif
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control" disabled>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">SEGUNDA NOTIFICACIÓN</p>
                                                                    <input type="hidden" name="tramite[segundasolicitud]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[segundasolicitud]" value="COMPRA DE SERVICIOS">
                                                                    <input type="hidden" name="subprocedimiento[segundasolicitud]" value="SEGUNDA NOTIFICACIÓN">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control text-center" name="citenotificacion[segundasolicitud]">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[segundasolicitud]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="text" class="form-control text-center" name="citenota[segundasolicitud]">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" name="fechacitenota[segundasolicitud]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <select class="form-control" id="viaja2" name="viaja[segundasolicitud]" onchange="toggleVisibility3(this)">
                                                                        <option value="" disabled selected>¿Viaja?</option>
                                                                        <option value="SI">SI</option>
                                                                        <option value="NO">NO</option>
                                                                    </select>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <select class="form-control" id="departamentoviaja2" name="departamentoviaja[segundasolicitud]" disabled>
                                                                        <option value="" disabled selected>Departamento</option>
                                                                        <option value="BENI">BENI</option>
                                                                        <option value="COCHABAMBA">COCHABAMBA</option>
                                                                        <option value="LA PAZ">LA PAZ</option>
                                                                        <option value="ORURO">ORURO</option>
                                                                        <option value="PANDO">PANDO</option>
                                                                        <option value="POTOSI">POTOSI</option>
                                                                        <option value="SANTA CRUZ">SANTA CRUZ</option>
                                                                        <option value="SUCRE">SUCRE</option>
                                                                        <option value="TARIJA">TARIJA</option>
                                                                    </select>

                                                                    <select class="form-control" id="transporteviaja2" name="transporteviaja[segundasolicitud]" onchange="mostrarTablaTraslado(this)" style="display: none;">
                                                                        <option value="" disabled selected>Transporte...</option>
                                                                        <option value="TERRESTRE">TERRESTRE</option>
                                                                        <option value="AÉREO">AÉREO</option>
                                                                    </select>

                                                                    <select class="form-control" id="decisionviaja2" name="decisionviaja[segundasolicitud]" onchange="mostrarTablaTraslado(this)" style="display: none;">
                                                                        <option value="" disabled selected>Decisión...</option>
                                                                        <option value="ACEPTADO">ACEPTADO</option>
                                                                        <option value="RECHAZADO">RECHAZADO</option>
                                                                    </select>
                                                                </td>

                                                                <script>
                                                                    function toggleVisibility3(selectElement) {
                                                                        const id = selectElement.id.replace('viaja', '');
                                                                        const transporteviaja = document.getElementById('transporteviaja' + id);
                                                                        const decisionviaja = document.getElementById('decisionviaja' + id);
                                                                        const departamentoviaja = document.getElementById('departamentoviaja' + id);

                                                                        if (selectElement.value === "SI") {
                                                                            transporteviaja.style.display = "block";
                                                                            decisionviaja.style.display = "block";
                                                                            departamentoviaja.disabled = false;
                                                                        } else {
                                                                            transporteviaja.style.display = "none";
                                                                            transporteviaja.value = "";
                                                                            decisionviaja.style.display = "none";
                                                                            decisionviaja.value = "";
                                                                            departamentoviaja.disabled = true;
                                                                            departamentoviaja.selectedIndex = 0;
                                                                        }
                                                                    }
                                                                </script>

                                                                <td class="align-middle text-center">
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
                                                                    <input type="date" class="form-control text-center" id="fechasubida10" name="fechasubida[segundasolicitud]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }} onchange="actualizarFechaRetorno10(this)">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="date" class="form-control text-center" id="fecharetorno10" name="fecharetorno[segundasolicitud]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <select class="form-control" name="viaticos[segundasolicitud]">
                                                                        <option value="" disabled selected>¿Viáticos?</option>
                                                                        <option value="SI">SI</option>
                                                                        <option value="NO">NO</option>
                                                                    </select>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <input type="file" name="archivo[segundasolicitud]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    @if (!$cssegundanotificacion->isEmpty())
                                                        <div class="text-left mt-2" style="margin-bottom: 10px;">
                                                            <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarCSsegundanotificacion()">AGREGAR MÁS</button>
                                                        </div>
                                                    @endif
                                                    <script>
                                                        function agregarCSsegundanotificacion() {
                                                            const filaOculta = document.querySelector('.fila-cssegundanotificacion.d-none');
                                                            if (filaOculta) {
                                                                filaOculta.classList.remove('d-none');
                                                            }
                                                        }
                                                    </script>
                                                </div>
                                            </div>

                                            {{-- PROGRAMACION AL CLIENTE Y PROGRAMACIONES MEDICAS --}}
                                            <table class="table table-bordered table-sm align-middle" id="tablaProgramaciones3" style="margin-bottom: -8px;">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th class="text-center">ESTUDIO/ESPECIALIDAD</th>
                                                        @if (isset($registrosGuardadosProgramacioCS) && collect($registrosGuardadosProgramacioCS)->contains(function($item) {
                                                            return $item->opcionatencion === 'COMPRA DE SERVICIOS';
                                                        }))
                                                        <th class="text-center">MÉDICO</th>
                                                        <th class="text-center">FECHA</th>
                                                        <th class="text-center">HORA</th>
                                                        <th class="text-center">ORDEN</th>
                                                        <th class="text-center">ASISTIÓ</th>
                                                        <th class="text-center">REPROG.</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($registrosGuardadosProgramacioCS as $registro)
                                                        @if ($registro->opcionatencion === 'COMPRA DE SERVICIOS')
                                                            <tr class="text-center">
                                                                <input type="hidden" name="tramitenombreprog" value="INVALIDEZ">
                                                                <td>
                                                                    {{ $registro->estudioespecialidad }}
                                                                    <input type="hidden" name="subtramite_id3[]" value="{{ $registro->id }}">
                                                                    <input type="hidden" name="estudioespecialidad3[]" value="{{ $registro->estudioespecialidad }}">
                                                                </td>
                                                                <td>
                                                                    @if ($registro->fechaprogramacion || $registro->nombremedico)
                                                                        {{ $registro->nombremedico }}
                                                                        <input type="hidden" name="nombremedicoprog3[]" value="{{ $registro->nombremedico }}">
                                                                    @else
                                                                        <input type="text" name="nombremedicoprog3[]" class="form-control form-control-sm" value="{{ old('nombremedicoprog[]', $registro->nombremedico) }}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($registro->fechaprogramacion)
                                                                        {{ $registro->fechareprogramacion ?? $registro->fechaprogramacion }}
                                                                        <input type="hidden" name="fechaprogramacion3[]" value="{{ $registro->fechaprogramacion }}">
                                                                    @else
                                                                        <input type="date" name="fechaprogramacion3[]" class="form-control form-control-sm" value="{{ old('fechaprogramacion[]', $registro->fechaprogramacion) }}" min="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($registro->horaprogramacion)
                                                                        {{ $registro->horareprogramacion ?? $registro->horaprogramacion }}
                                                                        <input type="hidden" name="horaprogramacion3[]" value="{{ $registro->horaprogramacion }}">
                                                                    @else
                                                                        <input type="time" name="horaprogramacion3[]" class="form-control form-control-sm" value="{{ old('horaprogramacion[]', $registro->horaprogramacion) }}">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($registro->ordenprogramacion)
                                                                        <a href="{{ asset("tramitesclientesita/{$cliente->id}/INVALIDEZ/ORDENES/{$registro->ordenprogramacion}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER ORDEN</a>
                                                                    @else
                                                                        <input type="file" name="ordenprogramacion3[{{ $loop->index }}]" class="form-control form-control-sm">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($registro->fechaprogramacion)
                                                                        @if ($registro->asistenciaprogramacion == 1)
                                                                            SI
                                                                            <input type="hidden" name="asistenciaprogramacion3[]" value="{{ $registro->id }}">
                                                                        @else
                                                                            <input type="checkbox" name="asistenciaprogramacion3[]" value="{{ $registro->id }}">
                                                                        @endif
                                                                    @else
                                                                        <input type="checkbox" disabled>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($registro->fechaprogramacion)
                                                                        @if ($registro->asistenciaprogramacion == 0)
                                                                            <button type="button" class="btn btn-sm btn-verdocumento" onclick="habilitarReprogramacion3(this)">REPROGRAMAR</i></button>
                                                                            <div class="mt-2 d-none">
                                                                                <input type="date" name="fechareprogramacion3[{{ $registro->id }}]" class="form-control form-control-sm mb-1" value="{{ old('fechareprogramacion[]', $registro->fechareprogramacion) }}">
                                                                                <input type="time" name="horareprogramacion3[{{ $registro->id }}]" class="form-control form-control-sm mb-1" value="{{ old('horareprogramacion[]', $registro->horareprogramacion) }}">
                                                                                <input type="text" name="motivoreprogramacion3[{{ $registro->id }}]" class="form-control form-control-sm" placeholder="Motivo" value="{{ old('motivoreprogramacion[]', $registro->motivoreprogramacion) }}">
                                                                            </div>
                                                                        @else
                                                                            @if ($registro->motivoreprogramacion)
                                                                                CON REPROG. - MOTIVO: {{ $registro->motivoreprogramacion }}
                                                                            @else
                                                                                SIN REPROG.
                                                                            @endif
                                                                        @endif
                                                                    @else
                                                                        VACIO
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="text-left mt-2">
                                                <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarFilaManualCS()">AGREGAR MÁS</button>
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function () {
                                                    const checkbox = document.getElementById('checkProgCliente');
                                                    if (checkbox && checkbox.checked) {
                                                        agregarEspecialidad3(checkbox);
                                                    }
                                                });
                                            </script>
                                            <script>
                                                function habilitarReprogramacion3(btn) {
                                                    const contenedor = btn.nextElementSibling;
                                                    if (contenedor.classList.contains('d-none')) {
                                                        contenedor.classList.remove('d-none');
                                                    } else {
                                                        contenedor.classList.add('d-none');
                                                    }
                                                }
                                            </script>
                                            <script>
                                                const especialidadesAgregadasCS = new Set();
                                                const todasareasCS = @json($todasareas);

                                                function agregarEspecialidadCS(checkbox) {
                                                    const tabla = document.getElementById('tablaProgramaciones3').getElementsByTagName('tbody')[0];
                                                    const valor = checkbox.value;

                                                    if (checkbox.checked && !especialidadesAgregadasCS.has(valor)) {
                                                        const fila = tabla.insertRow();
                                                        const celda = fila.insertCell();
                                                        celda.setAttribute("colspan", 8);

                                                        if (valor === "PROGRAMACIÓN AL CLIENTE") {
                                                            celda.innerHTML = `<input type="hidden" name="estudioespecialidad3[]" value="${valor}">`;
                                                            fila.style.display = 'none';
                                                        } else {
                                                            celda.innerHTML = `<input type="hidden" name="estudioespecialidad3[]" value="${valor}">${valor}`;
                                                        }

                                                        especialidadesAgregadasCS.add(valor);
                                                    } else if (!checkbox.checked && especialidadesAgregadasCS.has(valor)) {
                                                        for (let i = 0; i < tabla.rows.length; i++) {
                                                            const input = tabla.rows[i].querySelector(`input[value="${valor}"]`);
                                                            if (input) {
                                                                tabla.deleteRow(i);
                                                                break;
                                                            }
                                                        }
                                                        especialidadesAgregadasCS.delete(valor);
                                                    }
                                                }

                                                function agregarFilaManualCS() {
                                                    const tabla = document.getElementById('tablaProgramaciones3').getElementsByTagName('tbody')[0];
                                                    const fila = tabla.insertRow();

                                                    const celda = fila.insertCell();
                                                    celda.setAttribute("colspan", 8);
                                                    celda.innerHTML =
                                                        `<select class="form-control form-control-sm" name="estudioespecialidad3[]">
                                                            <option value="" disabled selected>Seleccione un estudio/especialidad...</option>
                                                            ${todasareasCS.map(area => `<option value="${area.area}">${area.area}</option>`).join('')}
                                                        </select>`;
                                                }
                                            </script>

                                            @if ($viaticosi)
                                                {{-- DEVOLUCIÓN DE VIÁTICOS --}}
                                                <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -8px;">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="width: 10%;">ID</th>
                                                            <th style="width: 10%;">NRO.</th>
                                                            <th style="width: 25%;">SUB_PROCEDIMIENTO</th>
                                                            <th style="width: 25%;">FECHA_REGISTRO</th>
                                                            <th style="width: 15%;">CARTA</th>
                                                            <th style="width: 15%;">FORMULARIO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($csdevolucionviaticos as $documento)
                                                            <tr>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ $documento->id }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ $documento->nro }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">DEVOLUCIÓN DE VIÁTICOS</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/COMPRA DE SERVICIOS/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/COMPRA DE SERVICIOS/{$documento->document2}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        @if ($csdevolucionviaticos->isEmpty())
                                                            <tr class="fila-csdevolucionviaticos">
                                                        @else
                                                            <tr class="fila-csdevolucionviaticos d-none">
                                                        @endif
                                                            <td class="align-middle text-center">
                                                                <input type="text" class="form-control" disabled>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="text" class="form-control" disabled>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">DEVOLUCIÓN DE VIÁTICOS</p>
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS">
                                                                <input type="hidden" name="subprocedimiento[]" value="DEVOLUCIÓN DE VIÁTICOS">
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @php
                                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                @endphp
                                                                <input type="date" class="form-control text-center" id="fechasubida9" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="file" name="archivo2" class="dropify mx-auto d-block" accept="application/pdf">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @if (!$csdevolucionviaticos->isEmpty())
                                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarCSdevolucionviaticos()">AGREGAR MÁS</button>
                                                    </div>
                                                @endif
                                                <script>
                                                    function agregarCSdevolucionviaticos() {
                                                        const filaOculta = document.querySelector('.fila-csdevolucionviaticos.d-none');
                                                        if (filaOculta) {
                                                            filaOculta.classList.remove('d-none');
                                                        }
                                                    }
                                                </script>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">SUBIR ARCHIVOS</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- SOLICITUD DE INFORMACION TECNICO COMPLEMENTARIA --}}
                <!-- MODAL ENTE GESTOR DE SALUD -->
                <div class="modal fade" id="modalEnteGestorSalud2" tabindex="-1" role="dialog" aria-labelledby="modalEnteGestorSalud2Label" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalEnteGestorSalud2Label">ENTE GESTOR DE SALUD</h5>
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
                                            $documento210 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento220 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN MT')->where('tramite', 'INVALIDEZ')->first();
                                            $documento230 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento240 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD A EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                            $documento990 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                            $documento990otro = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICTUD DE MODIFICACION DE REQUERIMIENTO')->where('tramite', 'INVALIDEZ')->first();
                                            $documento210nobaja = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('seguro', 'NO')->where('motivonoseguro', 'BAJA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento210nootro = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('seguro', 'NO')->whereIn('motivonoseguro', ['EGS INCORRECTA', 'ESPECIALIDAD INCORRECTA', 'ACTA TCM INCORRECTA'])->where('tramite', 'INVALIDEZ')->first();
                                            $documento210si = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('seguro', 'SI')->where('tramite', 'INVALIDEZ')->first();
                                            $documento220reiteracion = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ REITERACIÓN SOLICITUD DE EVALUACIÓN MT')->where('tramite', 'INVALIDEZ')->first();
                                            $documento230reiteracion = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ REITERACIÓN SOLICITUD DE HISTORIA CLÍNICA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento240reiteracion = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ REITERACIÓN SOLICITUD A EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm align-middle text-center">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 10%;">CITE_EMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_EMISIÓN</th>
                                                        <th style="width: 10%;">CITE_REMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_REMISIÓN</th>
                                                        <th style="width: 10%;">ASEGURADORA</th>
                                                        <th style="width: 20%;">¿TIENE_SEGURO?</th>
                                                        <th style="width: 10%;">FECHA_REGISTRO</th>
                                                        <th style="width: 10%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                                            @if (!$documento210)
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento210)
                                                                <p class="mb-0">{{ $documento21->citeemision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeemision1" name="citeemision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento210)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento210->fechaemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fechaemision1" name="fechaemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento210)
                                                                <p class="mb-0">{{ $documento210->citeremision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeremision1" name="citeremision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento210)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento210->fecharemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fecharemision1" name="fecharemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center"> 
                                                            <span>{{$aseguradora}}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento210)
                                                                <p class="mb-0">{{ $documento210->seguro }}{{ $documento210->motivonoseguro ? ' - ' . $documento210->motivonoseguro : '' }}</p>
                                                            @else
                                                                <select class="form-control seguro-select" name="seguro[]" style="max-width: 200px;">
                                                                    <option value="" disabled selected>Seleccione una opción</option>
                                                                    <option value="SI">SI</option>
                                                                    <option value="NO">NO</option>
                                                                </select>
                                                                <select class="form-control motivo-select" name="motivonoseguro[]" style="max-width: 200px; display: none;">
                                                                    <option value="" disabled selected>Motivo...</option>
                                                                    <option value="BAJA">BAJA</option>
                                                                    <option value="EGS INCORRECTA">EGS INCORRECTA</option>
                                                                    <option value="ESPECIALIDAD INCORRECTA">ESPECIALIDAD INCORRECTA</option>
                                                                    <option value="ACTA TCM INCORRECTA">ACTA TCM INCORRECTA</option>
                                                                </select>
                                                                <script>
                                                                    document.addEventListener('DOMContentLoaded', function () {
                                                                        const seguros = document.querySelectorAll('.seguro-select');
                                                                        seguros.forEach(function (seguro) {
                                                                            seguro.addEventListener('change', function () {
                                                                                const motivo = this.parentElement.querySelector('.motivo-select');
                                                                                if (this.value === 'NO') {
                                                                                    motivo.style.display = 'inline-block';
                                                                                } else {
                                                                                    motivo.style.display = 'none';
                                                                                    motivo.value = '';
                                                                                }
                                                                            });
                                                                        });
                                                                    });
                                                                </script>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento210)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento210->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center"
                                                                    id="fechasubida1"
                                                                    name="fechasubida[]"
                                                                    value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                    {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento210)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento210->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento210->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" id="archivo1" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table class="table table-bordered table-sm align-middle text-center" id="tablaProgramacionesSIC">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 40%;">ESTUDIO/ESPECIALIDAD</th>
                                                        <th style="width: 30%;">FECHA PROGRAMACIÓN</th>
                                                        <th style="width: 30%;">HORA PROGRAMACIÓN</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (isset($documento210) && count($registrosGuardadosProgramacionSIC ?? []) > 0)
                                                        @foreach ($registrosGuardadosProgramacionSIC as $registro)
                                                            <tr>
                                                                <td class="align-middle text-center">{{ $registro->estudioespecialidad }}</td>
                                                                <td class="align-middle text-center">
                                                                    {{ $registro->fechaprogramacion ? \Carbon\Carbon::parse($registro->fechaprogramacion)->format('Y-m-d') : 0 }}
                                                                </td>
                                                                <td class="align-middle text-center">{{ $registro->horaprogramacion ?? 0 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @elseif (isset($documento210) && count($registrosGuardadosProgramacionSIC ?? []) === 0)
                                                        <tr>
                                                            <td class="align-middle text-center" colspan="3">SIN OBSERVACIONES</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <select class="form-control" name="estudioespecialidad2[]">
                                                                    <option value="" disabled selected>Seleccione un estudio/especialidad...</option>
                                                                    @foreach ($todasareas as $area)
                                                                        <option value="{{ $area->area }}">{{ $area->area }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="date" class="form-control text-center" name="fechaprogramacion2[]">
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="time" class="form-control text-center" name="horaprogramacion2[]">
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                            @if (!isset($documento210) && (count($registrosGuardadosProgramacionSIC ?? []) === 0))
                                                <div class="text-right mt-2">
                                                    <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregarFilaProgramacionSIC()">AGREGAR PROGRAMACIÓN</button>
                                                </div>
                                            @endif
                                            <script>
                                                function agregarFilaProgramacionSIC() {
                                                    const tabla = document.getElementById('tablaProgramacionesSIC').getElementsByTagName('tbody')[0];
                                                    const nuevaFila = tabla.insertRow();

                                                    const todasareas = @json($todasareas);

                                                    nuevaFila.innerHTML = `
                                                        <td class="align-middle text-center">
                                                            <select class="form-control" name="estudioespecialidad2[]">
                                                                <option value="" disabled selected>Seleccione un estudio/especialidad...</option>
                                                                ${todasareas.map(a => `<option value="${a.area}">${a.area}</option>`).join('')}
                                                            </select>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <input type="date" class="form-control text-center" name="fechaprogramacion2[]">
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <input type="time" class="form-control text-center" name="horaprogramacion2[]">
                                                        </td>
                                                    `;
                                                }
                                            </script>

                                            <table class="table table-bordered table-sm align-middle text-center">
                                                @if ($documento210nobaja)
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="width: 40%;">SUB_PROCEDIMIENTO</th>
                                                            <th style="width: 30%;">FECHA_REGISTRO</th>
                                                            <th style="width: 30%;">DOCUMENTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">SOLICITUD DE COMPRA DE SERVICIOS</p>
                                                                @if (!$documento990)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento990)
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento990->fechasubida)->format('d-m-Y') }}</p>
                                                                @else
                                                                    <input type="date" class="form-control text-center"
                                                                        id="fechasubida2"
                                                                        name="fechasubida[]"
                                                                        value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                        {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento990)
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento990->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento990->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" id="archivo2" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                @endif

                                                @if ($documento210nootro)
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="width: 40%;">SUB_PROCEDIMIENTO</th>
                                                            <th style="width: 30%;">FECHA_REGISTRO</th>
                                                            <th style="width: 30%;">DOCUMENTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">SOLICTUD DE MODIFICACION DE REQUERIMIENTO</p>
                                                                @if (!$documento990otro)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICTUD DE MODIFICACION DE REQUERIMIENTO">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento990otro)
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento990otro->fechasubida)->format('d-m-Y') }}</p>
                                                                @else
                                                                    <input type="date" class="form-control text-center"
                                                                        id="fechasubida2otro"
                                                                        name="fechasubida[]"
                                                                        value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                        {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento990otro)
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento990otro->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento990otro->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" id="archivo2otro" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                @endif

                                                @if ($documento210si)
                                                <div class="d-flex justify-content-center gap-3 mb-3">
                                                    @if (!$documento220)
                                                        <button type="button" class="btn btn-sm btn-outline-primary" style="margin-right: 5px; margin-bottom: -15px;" onclick="toggleBloque0('bloque-reiteracion-10')">SOLICITUD DE EVALUACION MT</button>
                                                    @endif
                                                    @if (!$documento230)
                                                        <button type="button" class="btn btn-sm btn-outline-primary" style="margin-right: 5px; margin-bottom: -15px;" onclick="toggleBloque0('bloque-reiteracion-20')">SOLICITUD DE HISTORIA CLINICA</button>
                                                    @endif
                                                    @if (!$documento240)
                                                        <button type="button" class="btn btn-sm btn-outline-primary" style="margin-bottom: -15px;" onclick="toggleBloque0('bloque-reiteracion-30')">SOLICITUD A EMPLEADOR</button>
                                                    @endif
                                                </div>
                                                <script>
                                                    function toggleBloque0(className) {
                                                        const filas = document.querySelectorAll(`.${className}`);
                                                        filas.forEach(fila => {
                                                            fila.style.display = (fila.style.display === 'none' || fila.style.display === '') ? 'table-row' : 'none';
                                                        });
                                                    }
                                                </script>
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="width: 40%;">SUB_PROCEDIMIENTO</th>
                                                            <th style="width: 30%;">FECHA_REGISTRO</th>
                                                            <th style="width: 30%;">DOCUMENTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {{-- SOLICITUD DE EVALUACIÓN MT --}}
                                                        <tr class="bloque-reiteracion-10" style="display: {{ $documento220 ? 'table-row' : 'none' }};">
                                                            <td class="align-middle text-center">
                                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                                    @if (!$documento220reiteracion)
                                                                        <button type="button" class="btn btn-sm btn-botonpequeno" onclick="toggleReiteracionevaluacion0()" title="AGREGAR REITERACIÓN SOLICITUD DE EVALUACIÓN MT"><i class="fas fa-plus"></i></button>
                                                                    @endif
                                                                    <p class="mb-0">SOLICITUD DE EVALUACIÓN MT</p>
                                                                </div>
                                                                @if (!$documento220)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN MT">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento220)
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento220->fechasubida)->format('d-m-Y') }}</p>
                                                                @else
                                                                    <input type="date" class="form-control text-center"
                                                                        id="fechasubida3"
                                                                        name="fechasubida[]"
                                                                        value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                        {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento220)
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento220->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento220->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" id="archivo3" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr id="fila-reiteracionevaluacion0" style="display: none;">
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">REITERACIÓN SOLICITUD DE EVALUACIÓN MT</p>
                                                                @if (!$documento220reiteracion)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ REITERACIÓN SOLICITUD DE EVALUACIÓN MT">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="date" class="form-control text-center"
                                                                    id="fechasubida3reiteracion"
                                                                    name="fechasubida[]"
                                                                    value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                    {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento220reiteracion)
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento220reiteracion->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" id="archivo3reiteracion" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @if ($documento220reiteracion)
                                                            <tr>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">REITERACIÓN SOLICITUD DE EVALUACIÓN MT</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento220reiteracion->fechasubida)->format('d-m-Y') }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento220reiteracion->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <script>
                                                            function toggleReiteracionevaluacion0() {
                                                                const fila = document.getElementById('fila-reiteracionevaluacion0');
                                                                if (fila.style.display === 'none') {
                                                                    fila.style.display = '';
                                                                } else {
                                                                    fila.style.display = 'none';
                                                                }
                                                            }
                                                        </script>

                                                        {{-- SOLICITUD DE HISTORIA CLÍNICA --}}
                                                        <tr class="bloque-reiteracion-20" style="display: {{ $documento230 ? 'table-row' : 'none' }};">
                                                            <td class="align-middle text-center">
                                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                                    @if (!$documento230reiteracion)
                                                                        <button type="button" class="btn btn-sm btn-botonpequeno" onclick="toggleReiteracionhistoria0()" title="AGREGAR REITERACIÓN SOLICITUD DE HISTORIA CLÍNICA"><i class="fas fa-plus"></i></button>
                                                                    @endif
                                                                    <p class="mb-0">SOLICITUD DE HISTORIA CLÍNICA</p>
                                                                </div>
                                                                @if (!$documento230)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento230)
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento230->fechasubida)->format('d-m-Y') }}</p>
                                                                @else
                                                                    <input type="date" class="form-control text-center"
                                                                        id="fechasubida4"
                                                                        name="fechasubida[]"
                                                                        value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                        {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento230)
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento230->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento230->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" id="archivo4" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr id="fila-reiteracionhistoria0" style="display: none;">
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">REITERACIÓN SOLICITUD DE HISTORIA CLÍNICA</p>
                                                                @if (!$documento230reiteracion)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ REITERACIÓN SOLICITUD DE HISTORIA CLÍNICA">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="date" class="form-control text-center"
                                                                    id="fechasubida4reiteracion"
                                                                    name="fechasubida[]"
                                                                    value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                    {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento230reiteracion)
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento230reiteracion->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" id="archivo4reiteracion" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @if ($documento230reiteracion)
                                                            <tr>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">REITERACIÓN SOLICITUD DE HISTORIA CLÍNICA</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento230reiteracion->fechasubida)->format('d-m-Y') }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento230reiteracion->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <script>
                                                            function toggleReiteracionhistoria0() {
                                                                const fila = document.getElementById('fila-reiteracionhistoria0');
                                                                if (fila.style.display === 'none') {
                                                                    fila.style.display = '';
                                                                } else {
                                                                    fila.style.display = 'none';
                                                                }
                                                            }
                                                        </script>

                                                        {{-- SOLICITUD A EMPLEADOR --}}
                                                        <tr class="bloque-reiteracion-30" style="display: {{ $documento240 ? 'table-row' : 'none' }};">
                                                            <td class="align-middle text-center">
                                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                                    @if (!$documento240reiteracion)
                                                                        <button type="button" class="btn btn-sm btn-botonpequeno" onclick="toggleReiteracionempleador0()" title="AGREGAR REITERACIÓN SOLICITUD A EMPLEADOR"><i class="fas fa-plus"></i></button>
                                                                    @endif
                                                                    <p class="mb-0">SOLICITUD A EMPLEADOR</p>
                                                                </div>
                                                                @if (!$documento240)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD A EMPLEADOR">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento240)
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento240->fechasubida)->format('d-m-Y') }}</p>
                                                                @else
                                                                    <input type="date" class="form-control text-center"
                                                                        id="fechasubida5"
                                                                        name="fechasubida[]"
                                                                        value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                        {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento240)
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento240->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento240->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" id="archivo5" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr id="fila-reiteracionempleador0" style="display: none;">
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">REITERACIÓN SOLICITUD A EMPLEADOR</p>
                                                                @if (!$documento240reiteracion)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ REITERACIÓN SOLICITUD A EMPLEADOR">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="date" class="form-control text-center"
                                                                    id="fechasubida5reiteracion"
                                                                    name="fechasubida[]"
                                                                    value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                    {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento240reiteracion)
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento240reiteracion->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" id="archivo5reiteracion" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @if ($documento240reiteracion)
                                                            <tr>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">REITERACIÓN SOLICITUD A EMPLEADOR</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento240reiteracion->fechasubida)->format('d-m-Y') }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento240reiteracion->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <script>
                                                            function toggleReiteracionempleador0() {
                                                                const fila = document.getElementById('fila-reiteracionempleador0');
                                                                if (fila.style.display === 'none') {
                                                                    fila.style.display = '';
                                                                } else {
                                                                    fila.style.display = 'none';
                                                                }
                                                            }
                                                        </script>
                                                    </tbody>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                    @if (!$documento990 && !$documento990otro)
                                        <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto" target="_blank">SUBIR ARCHIVOS</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL NOTIFICACION TMC -->
                <div class="modal fade" id="modalNotificacionTMC2" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionTMC2CLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalNotificacionTMC2Label">NOTIFICACIÓN TMC</h5>
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
                                            $documento250 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento206 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC')->where('tramite', 'INVALIDEZ')->first();
                                            $documento260reg = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ REGISTRO DE DOCUMENTACIÓN PROFESIONAL')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        <div class="table-responsive">
                                            {{-- NOTIFICACIÓN DE GESTORA --}}
                                            <table class="table table-bordered table-sm align-middle text-center">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 10%;">CITE_EMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_EMISIÓN</th>
                                                        <th style="width: 10%;">CITE_REMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_REMISIÓN</th>
                                                        <th style="width: 10%;">ASEGURADORA</th>
                                                        <th style="width: 10%;">FECHA_REGISTRO</th>
                                                        <th style="width: 10%;">FECHA_RETORNO</th>
                                                        <th style="width: 10%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                                            @if (!$documento250)
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                <input type="hidden" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento250)
                                                                <p class="mb-0">{{ $documento250->citeemision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeemision1" name="citeemision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento250)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento250->fechaemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fechaemision1" name="fechaemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento250)
                                                                <p class="mb-0">{{ $documento250->citeremision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeremision1" name="citeremision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento250)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento250->fecharemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fecharemision1" name="fecharemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center"> 
                                                            <span>{{$aseguradora}}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento250)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento250->fechasubida)->format('d-m-Y') }}</p>
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
                                                                <input type="date" class="form-control text-center" id="fechasubida4" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" data-max="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }} onchange="actualizarFechaRetorno4(this)">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento250)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento250->fecharetorno)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fecharetorno4" name="fecharetorno[]" value="{{ $fechaLimiteStr }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento250)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento250->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento250->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" id="archivo1" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- RESPUESTA A NOTIFICACIÓN TMC --}}
                                            <table class="table table-bordered table-sm align-middle text-center">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 30%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 15%;">TIPO_MÉDICO</th>
                                                        <th style="width: 25%;">NOMBRE_MÉDICO</th>
                                                        <th style="width: 15%;">FECHA_REGISTRO</th>
                                                        <th style="width: 15%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">RESPUESTA A NOTIFICACIÓN TMC</p>
                                                            @if (!$documento260)
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                <input type="hidden" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento260)
                                                                <p class="mb-0">{{ $documento260->tipomedico }}</p>
                                                            @else
                                                                <select class="form-control" name="tipomedico[]">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    <option value="INTERNO">INTERNO</option>
                                                                    <option value="EXTERNO">EXTERNO</option>
                                                                </select>                                            
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento260)
                                                                <p class="mb-0">{{ $documento260->nombremedico }} @if (!empty($documento260->nombremedico2)) - {{ $documento260->nombremedico2 }}@endif</p>
                                                            @else
                                                                <select class="form-control" name="nombremedico[]">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    @foreach ($proveedoresmedicos as $id => $nombre)
                                                                        <option value="{{ $nombre }}">{{ $nombre }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <select class="form-control" name="nombremedico2[]">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    @foreach ($proveedoresmedicos as $id => $nombre)
                                                                        <option value="{{ $nombre }}">{{ $nombre }}</option>
                                                                    @endforeach
                                                                </select>                                            
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center"> 
                                                            @if ($documento260)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento260->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center"
                                                                    id="fechasubida2"
                                                                    name="fechasubida[]"
                                                                    value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                    {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento260)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento260->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento260->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" id="archivo2" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- REGISTRO DE DOCUMENTACIÓN PROFESIONAL --}}
                                            @if (!$documento260reg)
                                                <div class="text-right mb-2">
                                                    <button type="button" class="btn btn-sm btn-verdocumento" onclick="document.getElementById('tablaRegistroDoc0').style.display = '';">
                                                        AGREGAR REGISTRO DE DOC. PROF.
                                                    </button>
                                                </div>
                                            @endif
                                            <table class="table table-bordered table-sm align-middle text-center" id="tablaRegistroDoc0" style="{{ !$documento260reg ? 'display: none;' : '' }}; margin-top: -5px;">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 40%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 30%;">FECHA_REGISTRO</th>
                                                        <th style="width: 30%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">REGISTRO DE DOCUMENTACIÓN PROFESIONAL</p>
                                                            @if (!$documento260reg)
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                <input type="hidden" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ REGISTRO DE DOCUMENTACIÓN PROFESIONAL">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center"> 
                                                            @if ($documento260reg)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento260reg->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center"
                                                                    id="fechasubida3"
                                                                    name="fechasubida[]"
                                                                    value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                                    {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento260reg)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento260reg->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento260reg->id }}">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" id="archivo3" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @if (!$documento250 || !$documento260 || !$documento260reg)
                                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">SUBIR ARCHIVOS</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL EMPLEADOR -->
                <div class="modal fade" id="modalEmpleador2" tabindex="-1" role="dialog" aria-labelledby="modalEmpleador2Label" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalEmpleador2Label">EMPLEADOR</h5>
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
                                            $fecha = \Carbon\Carbon::now();
                                            $valor = $fecha->day <= 15 ? $fecha : $fecha->copy()->addMonth();
                                            $valorFormateado = $valor->format('m/y');

                                            $documento290 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento300 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ CARTA SOLICITUD A EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                            $documento300re = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ REITERACIÓN CARTA SOLICITUD A EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                            $documento310 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE MODIFICACIÓN DE CITE')->where('tramite', 'INVALIDEZ')->first();

                                            $documento290si = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('corsolicitud', 'SI')->where('tramite', 'INVALIDEZ')->first();
                                            $documento290no = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('corsolicitud', 'NO')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm align-middle text-center">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 15%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 10%;">CITE_EMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_EMISIÓN</th>
                                                        <th style="width: 10%;">CITE_REMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_REMISIÓN</th>
                                                        <th style="width: 15%;">TIPO_DOCUMENTACIÓN</th>
                                                        <th style="width: 10%;">CORRESPONDE_SOLICITUD?</th>
                                                        <th style="width: 10%;">FECHA_REGISTRO</th>
                                                        <th style="width: 10%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                                            @if (!$documento290)
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                <input type="hidden" name="subprocedimiento[]" value="EMPLEADOR _ NOTIFICACIÓN DE GESTORA">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento290)
                                                                <p class="mb-0">{{ $documento290->citeemision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeemision1" name="citeemision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento290)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento290->fechaemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fechaemision1" name="fechaemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento290)
                                                                <p class="mb-0">{{ $documento290->citeremision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeremision1" name="citeremision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento290)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento290->fecharemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fecharemision1" name="fecharemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento290)
                                                                <p class="mb-0">{{ $documento290->tipodocumento }}</p>
                                                            @else
                                                                <select class="form-control" name="tipodocumento[]">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    <option value="INFORME DEL EMPLEADOR">INFORME DEL EMPLEADOR</option>
                                                                    <option value="RECORD DE SERVICIOS">RECORD DE SERVICIOS</option>
                                                                    <option value="COPIA DE EXAMENES PERIODICOS ANUALES DE EMPRESA">COPIA DE EXAMENES PERIODICOS ANUALES DE EMPRESA</option>
                                                                    <option value="CERTIFICADO DE AÑOS DE APORTES">CERTIFICADO DE AÑOS DE APORTES</option>
                                                                    <option value="CERTIFICADO DE TRABAJO">CERTIFICADO DE TRABAJO</option>
                                                                    <option value="PLANILLA DE PAGOS">PLANILLA DE PAGOS</option>
                                                                    <option value="BOLETAS DE PAGO">BOLETAS DE PAGO</option>
                                                                    <option value="DENUNCIA DE ACCIDENTE">DENUNCIA DE ACCIDENTE</option>
                                                                </select>                                            
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento290)
                                                                <p class="mb-0">{{ $documento290->corsolicitud }} - {{ $documento290->opcioncorsolicitud }}</p>
                                                            @else
                                                                <select class="form-control mb-2" name="corsolicitud[]" onchange="mostrarOpciones2(this)">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    <option value="SI">SI</option>
                                                                    <option value="NO">NO</option>
                                                                </select>
                                                                <select class="form-control mb-2 opcion-si d-none" name="opcioncorsolicitud[]">
                                                                    <option value="" disabled selected>Seleccione una empresa...</option>
                                                                    @foreach ($empresas as $empresa)
                                                                        <option value="{{ $empresa->nombreempresa }}">{{ $empresa->nombreempresa }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <select class="form-control mb-2 opcion-no d-none" name="opcioncorsolicitud[]">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    <option value="REQUERIMIENTO INCORRECTO">REQUERIMIENTO INCORRECTO</option>
                                                                </select>
                                                            @endif
                                                        </td>
                                                        <script>
                                                            function mostrarOpciones2(select) {
                                                                const contenedor = select.closest('td');
                                                                const opcionSi = contenedor.querySelector('.opcion-si');
                                                                const opcionNo = contenedor.querySelector('.opcion-no');
                                                                opcionSi.classList.add('d-none');
                                                                opcionNo.classList.add('d-none');
                                                                opcionSi.selectedIndex = 0;
                                                                opcionNo.selectedIndex = 0;
                                                                if (select.value === 'SI') {
                                                                    opcionSi.classList.remove('d-none');
                                                                } else if (select.value === 'NO') {
                                                                    opcionNo.classList.remove('d-none');
                                                                }
                                                            }
                                                        </script>
                                                        <td class="align-middle text-center">
                                                            @if ($documento290)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento290->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                @php
                                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                @endphp
                                                                <input type="date" class="form-control text-center" id="fechasubida" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documento290)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento290->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento29->id }}">
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
                                                        <th style="width: 40%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 30%;">FECHA_REGISTRO</th>
                                                        <th style="width: 30%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($documento290si)
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">CARTA SOLICITUD A EMPLEADOR</p>
                                                                @if (!$documento300)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="EMPLEADOR _ CARTA SOLICITUD A EMPLEADOR">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento300)
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento300->fechasubida)->format('d-m-Y') }}</p>
                                                                @else
                                                                    @php
                                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                    @endphp
                                                                    <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento300)
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento300->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento300->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    @if ($documento300)
                                                        @if ($documento300re)
                                                            <tr>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">REITERACIÓN CARTA SOLICITUD A EMPLEADOR</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento300re->fechasubida)->format('d-m-Y') }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento300re->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td colspan="3">
                                                                    <button type="button" class="btn btn-sm btn-verdocumento" onclick="document.getElementById('filaDocumento300').classList.toggle('d-none')">
                                                                        REITERACION CARTA SOLICITUD A EMPLEADOR
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <tr id="filaDocumento300" class="d-none">
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">REITERACION CARTA SOLICITUD A EMPLEADOR</p>
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="EMPLEADOR _ REITERACIÓN CARTA SOLICITUD A EMPLEADOR">
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
                                                        @endif
                                                    @endif

                                                    @if ($documento290no)
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">SOLICITUD DE MODIFICACIÓN DE CITE</p>
                                                                @if (!$documento310)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="EMPLEADOR _ SOLICITUD DE MODIFICACIÓN DE CITE">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento310)
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documento310->fechasubida)->format('d-m-Y') }}</p>
                                                                @else
                                                                    @php
                                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                    @endphp
                                                                    <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documento310)
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento310->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documento310->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @if (!$documento290 || !$documento300 || !$documento310)
                                        <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">SUBIR ARCHIVOS</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- MODAL CANCELACION DE TRAMITE -->
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
                                            $documentocan1 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'CANCELACIÓN DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first();
                                            $documentocan2 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'CANCELACIÓN DE TRÁMITE _ CARTA DE RECHAZO A TRÁMITE CANCELADO')->where('tramite', 'INVALIDEZ')->first();
                                            $documentocan3 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'CANCELACIÓN DE TRÁMITE _ REITERACIÓN DE CARTA DE RECHAZO')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm align-middle text-center">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 15%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 10%;">CITE_EMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_EMISIÓN</th>
                                                        <th style="width: 10%;">CITE_REMISIÓN</th>
                                                        <th style="width: 10%;">FECHA_REMISIÓN</th>
                                                        <th style="width: 15%;">MOTIVO_CANCELACIÓN</th>
                                                        <th style="width: 15%;">FECHA_REGISTRO</th>
                                                        <th style="width: 15%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <p class="mb-0">CANCELACIÓN DE TRÁMITE</p>
                                                            @if (!$documentocan1)
                                                                <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                <input type="hidden" name="subprocedimiento[]" value="CANCELACIÓN DE TRÁMITE">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documentocan1)
                                                                <p class="mb-0">{{ $documentocan1->citeemision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeemision1" name="citeemision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documentocan1)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documentocan1->fechaemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fechaemision1" name="fechaemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documentocan1)
                                                                <p class="mb-0">{{ $documentocan1->citeremision }}</p>
                                                            @else
                                                                <input type="text" class="form-control text-center" id="citeremision1" name="citeremision[]">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documentocan1)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documentocan1->fecharemision)->format('d-m-Y') }}</p>
                                                            @else
                                                                <input type="date" class="form-control text-center" id="fecharemision1" name="fecharemision[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documentocan1)
                                                                <p class="mb-0">{{ $documentocan1->motivorechazo }}</p>
                                                            @else
                                                                <select class="form-control" name="motivorechazo[]">
                                                                    <option value="" disabled selected>Seleccione una opción...</option>
                                                                    <option value="TRAMITE SIN MOVIMIENTO POR AFP/GESTORA">TRAMITE SIN MOVIMIENTO POR AFP/GESTORA</option>
                                                                    <option value="TRAMITE SIN MOVIMIENTO POR AFILIADO">TRAMITE SIN MOVIMIENTO POR AFILIADO</option>
                                                                    <option value="TRAMITE SIN MOVIMIENTO POR EGS">TRAMITE SIN MOVIMIENTO POR EGS</option>
                                                                    <option value="TRAMITE SIN MOVIMIENTO POR CS">TRAMITE SIN MOVIMIENTO POR CS</option>
                                                                    <option value="TRAMITE SIN MOVIMIENTO POR ACTA TMC">TRAMITE SIN MOVIMIENTO POR ACTA TMC</option>
                                                                </select>                                            
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documentocan1)
                                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documentocan1->fechasubida)->format('d-m-Y') }}</p>
                                                            @else
                                                                @php
                                                                    $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                @endphp
                                                                <input type="date" class="form-control text-center" id="fechasubida" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($documentocan1)
                                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documentocan1->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                @if ($puedeEditarArchivo)
                                                                    <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                        <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                        <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                            <i class="fas fa-upload"></i>
                                                                        </button>
                                                                        <input type="hidden" name="tramite_reemplazo_id" value="{{ $documentocan1->id }}">
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
                                                        <th style="width: 40%;">SUB_PROCEDIMIENTO</th>
                                                        <th style="width: 30%;">FECHA_REGISTRO</th>
                                                        <th style="width: 30%;">DOCUMENTO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($documentocan1)
                                                        <tr>
                                                            <td class="align-middle text-center">
                                                                <p class="mb-0">CARTA DE RECHAZO A TRÁMITE CANCELADO</p>
                                                                @if (!$documentocan2)
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="CANCELACIÓN DE TRÁMITE _ CARTA DE RECHAZO A TRÁMITE CANCELADO">
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documentocan2)
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documentocan2->fechasubida)->format('d-m-Y') }}</p>
                                                                @else
                                                                    @php
                                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                                    @endphp
                                                                    <input type="date" class="form-control text-center" name="fechasubida[]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if ($documentocan2)
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documentocan2->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                    @if ($puedeEditarArchivo)
                                                                        <div class="d-flex align-items-center justify-content-center gap-2" style="margin-top:5px;">
                                                                            <input type="file" name="archivo_reemplazo" class="dropify" accept="application/pdf">
                                                                            <button type="submit" name="accion" value="reemplazarArchivo" class="btn btn-sm btn-subirarchivos" title="REEMPLAZAR ARCHIVO">
                                                                                <i class="fas fa-upload"></i>
                                                                            </button>
                                                                            <input type="hidden" name="tramite_reemplazo_id" value="{{ $documentocan2->id }}">
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <input type="file" name="archivo[]" class="dropify mx-auto d-block" accept="application/pdf">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>

                                            @if ($documentocan2)
                                                <table class="table table-bordered table-sm align-middle text-center">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th style="width: 25%;">SUB_PROCEDIMIENTO</th>
                                                            <th style="width: 25%;">DESICIÓN</th>
                                                            <th style="width: 25%;">FECHA_REGISTRO</th>
                                                            <th style="width: 25%;">DOCUMENTO</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($documentocan3)
                                                            <tr>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">REITERACION DE CARTA DE RECHAZO</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ $documentocan3->estadotramite }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">{{ \Carbon\Carbon::parse($documentocan3->fechasubida)->format('d-m-Y') }}</p>
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documentocan3->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td colspan="4">
                                                                    <button type="button" class="btn btn-sm btn-verdocumento" onclick="document.getElementById('filaDocumento3001').classList.toggle('d-none')">
                                                                        REITERACION DE CARTA DE RECHAZO
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <tr id="filaDocumento3001" class="d-none">
                                                                <td class="align-middle text-center">
                                                                    <p class="mb-0">REITERACION DE CARTA DE RECHAZO</p>
                                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                                    <input type="hidden" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA">
                                                                    <input type="hidden" name="subprocedimiento[]" value="CANCELACIÓN DE TRÁMITE _ REITERACIÓN DE CARTA DE RECHAZO">
                                                                </td>
                                                                <td class="align-middle text-center">
                                                                    <select class="form-control" name="estadotramite[]">
                                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                                        <option value="ACEPTADO">ACEPTADO</option>
                                                                        <option value="RECHAZADO">RECHAZADO</option>
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
                                                        @endif
                                                    </tbody>
                                                </table>
                                            @endif
                                        </div>
                                    </div>
                                    @if (!$documentocan1 || !$documentocan2 || !$documentocan3)
                                        <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">SUBIR ARCHIVOS</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        $('#tipoPdfSelect').change(function() {
                            var selectedOption = $(this).val();
                            $('.modal').modal('hide');
                            switch (selectedOption) {
                                case 'ENTE GESTOR DE SALUD':
                                    $('#modalEnteGestorSalud2').modal('show');
                                    break;
                                case 'NOTIFICACIÓN TMC':
                                    $('#modalNotificacionTMC2').modal('show');
                                    break;
                                case 'NOTIFICACIÓN TMR':
                                    $('#modalNotificacionTMR2').modal('show');
                                    break;
                                case 'EMPLEADOR':
                                    $('#modalEmpleador2').modal('show');
                                    break;
                                default:
                                    break;
                            }
                        });
                    });
                </script>  

                {{-- 3.- RESULTADO DEL PROCESO --}}
                <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamen">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-balance-scale fa-5x mb-2"></i>
                                    <span class="h6 mb-0 btn-block text-center">DICTAMEN</span>
                                </div>
                            </button>
                            <br>
                            @php
                                $documento41 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
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
                            $documento43 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('estadodictamen', 'ACEPTADO')->where('tramite', 'INVALIDEZ')->first();
                            $documentoRechazado = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('estadodictamen', 'RECHAZADO')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="col-12 col-md-4 mb-3">
                            @if ($documentoRechazado)
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenRechazado" disabled>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-left fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">DICTAMEN ACEPTADO</span>
                                    </div>
                                </button>
                            @elseif ($documento43)
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenAceptado">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-left fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">DICTAMEN ACEPTADO</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento43 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'ACEPTACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                                    $documento44 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'FIRMA DE FORMULARIO')->where('tramite', 'INVALIDEZ')->first();
                                    $documento45 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'FIRMA DE CONTRATO')->where('tramite', 'INVALIDEZ')->first();
                                    $documento46 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'COBRO DE PENSIÓN')->where('tramite', 'INVALIDEZ')->first();
                                    $documento47 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                <div class="text-center">
                                    @if (!$documento43 || !$documento44 || !$documento45 || !$documento46 || !$documento47)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @else
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> COMPLETO
                                        </span>
                                    @endif
                                </div>
                                @else
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenAceptado" disabled>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-left fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">ESTADO DE DICTAMEN PENDIENTE</span>
                                    </div>
                                </button>
                            @endif
                        </div>

                        @php
                            $documento43 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('estadodictamen', 'ACEPTADO')->where('tramite', 'INVALIDEZ')->first();
                            $documentoRechazado = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('estadodictamen', 'RECHAZADO')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="col-12 col-md-4 mb-3">
                            @if ($documento43)
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenRechazado" disabled>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-right fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">DICTAMEN RECHAZADO</span>
                                    </div>
                                </button>
                            @elseif ($documentoRechazado)
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenRechazado">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-right fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">DICTAMEN RECHAZADO</span>
                                    </div>
                                </button>
                                <br>
                                @php
                                    $documento48 = $cliente->tramites()->where('subprocedimiento', 'INICIO PROCESO DE APELACIÓN')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                <div class="text-center">
                                    @if (!$documento48)
                                        <span class="mb-0 checkamarillo">
                                            <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                        </span>
                                    @else
                                        <span class="mb-0 checkverde">
                                            <i class="fas fa-check-circle"></i> COMPLETO
                                        </span>
                                    @endif
                                </div>
                            @else
                                <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalDictamenRechazado" disabled>
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-balance-scale-right fa-5x mr-2"></i>
                                        <span class="h6 mb-0 btn-block text-center">ESTADO DE DICTAMEN PENDIENTE</span>
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
                    <input type="text" class="form-control" id="tramite" name="tramite" value="INVALIDEZ" hidden>
                    <input type="date" class="form-control" id="fechasubida" name="fechasubida" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                    @csrf
                    <div class="row">
                        @php
                            $documento1 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                            $documento5 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('nivelprocedimiento', 'FIRMA EAP')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                            $fechaingresoyeap = $documento1 && $documento5;

                            $primeracartasit = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'PRIMERA CARTA SIT')->first();
                            $segundacartasit = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'SEGUNDA CARTA SIT')->first();
                            $terceracartasit = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'TERCERA CARTA SIT')->first();
                            $primeracartareclamo = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'PRIMERA CARTA DE RECLAMO')->first();
                            $segundacartareclamo = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'SEGUNDA CARTA DE RECLAMO')->first();
                            $terceracartareclamo = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'TERCERA CARTA DE RECLAMO')->first();
                            $cartareclamoaps = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'CARTA DE RECLAMO APS')->first();
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
                                    'DICTAMEN' => 'DICTAMEN',
                                
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
                            <input type="text" class="form-control" id="tipocartareclamo" name="tipocartareclamo" value="PENSIÓN DE INVALIDEZ" hidden>
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
                    <input type="text" class="form-control" id="tramite" name="tramite" value="INVALIDEZ" hidden>
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
                                            <input type="text" class="form-control" id="tipocartareclamo" name="tipocartareclamo" value="PENSIÓN DE INVALIDEZ" hidden>
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
                <h5 class="modal-title titulomodal" id="modalsolicitudesLabel">SOLICITUDES</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="tabs-solicitudes">
                        <li class="nav-item">
                            <a class="nav-link active" id="solicitudes-tab-1" data-toggle="tab" href="#solicitudes-content-1" role="tab" aria-controls="solicitudes-content-1" aria-selected="true">NUEVA SOLICITUD</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="solicitudes-tab-2" data-toggle="tab" href="#solicitudes-content-2" role="tab" aria-controls="solicitudes-content-2" aria-selected="false"> HISTORIAL DE SOLICITUDES</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="tabs-solicitudes-contenido">
                        <div class="tab-pane fade show active" id="solicitudes-content-1" role="tabpanel" aria-labelledby="solicitudes-tab-1">
                            <form action="{{ route('admin.tramites.generarsolicitud', $cliente) }}" method="GET" enctype="multipart/form-data">
                                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                {!! Form::hidden('clienteid', $cliente->id) !!}
                                {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                                {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                                {!! Form::hidden('idtramite', $idTramite) !!}
                                <input type="text" class="form-control" id="tramite" name="tramite" value="INVALIDEZ" hidden>
                                <input type="date" class="form-control" id="fechasubida" name="fechasubida" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="form-group col-lg-3">
                                                {!! Form::label('nivelprocedimiento', 'Nivel Procedimiento:') !!}
                                                {!! Form::select('nivelprocedimiento', [
                                                    'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO' => 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO',
                                                    'COMPRA DE SERVICIOS' => 'COMPRA DE SERVICIOS',
                                                    'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA' => 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA',
                                                ], null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                                            </div>
                                            <div class="form-group col-lg-3">
                                                {!! Form::label('tipo_pdf', 'Tipo de Solicitud:') !!}
                                                {!! Form::select('tipo_pdf', [
                                                    'EVALUACIÓN POR MEDICINA DEL TRABAJO' => 'EVALUACIÓN POR MEDICINA DEL TRABAJO',
                                                    'INCLUSIÓN DE INFORMES MÉDICOS' => 'INCLUSIÓN DE INFORMES MÉDICOS',
                                                    'HISTORIA CLÍNICA LEGALIZADA' => 'HISTORIA CLÍNICA LEGALIZADA',
                                                    'ACTUALIZACIÓN DE DATOS' => 'ACTUALIZACIÓN DE DATOS',
                                                    'COMPRA DE SERVICIOS' => 'COMPRA DE SERVICIOS',
                                                    'INFORME AL EMPLEADOR' => 'INFORME AL EMPLEADOR',
                                                ], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoPdfSelect2', 'required' => 'required']) !!}
                                                @error('tipo_pdf')
                                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="form-group col-lg-3">
                                                {!! Form::label('apoderado', 'Apoderado:') !!}
                                                {!! Form::text('apoderado', $apoderadoAsignado, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                                            </div>
                                            <div class="form-group col-lg-3">
                                                {!! Form::label('aseguradora', 'Aseguradora:') !!}
                                                {!! Form::text('aseguradora', $aseguradora, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-12" id="cambioactualizacionContainer" style="display: none;">
                                            {!! Form::label('cambioactualizacion', 'Modificación:', ['class' => 'font-weight-bold']) !!}
                                            <div class="form-row mt-2">
                                                <div class="col-md-12">
                                                    <p class="mb-0">
                                                        Mediante la presente me dirijo a ustedes para solicitar ACTUALIZACIÓN de datos personales de acuerdo con Cédula de Identidad,
                                                        <span class="font-weight-bold">CAMBIAR</span> 
                                                        {!! Form::text('cambioactualizacion', null, ['class' => 'form-control d-inline-block', 'placeholder' => 'Ej: el estado Civil de SOLTERO a DIVORCIADO', 'id' => 'cambioactualizacionInput']) !!}
                                                        <span>como figura en la Cédula de Identidad, por motivo de Trámite de PENSIÓN DE INVALIDEZ.</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            $(document).ready(function() {
                                                const input = $('#cambioactualizacionInput');
                                                const placeholderText = input.attr('placeholder');
                                                input.css('width', `${placeholderText.length * 0.95}ch`);
                                                input.on('input', function() {
                                                    const inputLength = $(this).val().length;
                                                    const newWidth = Math.max(inputLength * 0.95, placeholderText.length * 0.95);
                                                    $(this).css('width', `${newWidth}ch`);
                                                });
                                            });
                                        </script>
                                    </div>

                                    <script>
                                        $(document).ready(function() {
                                            $('#tipoPdfSelect2').on('change', function() {
                                                $('#cambioactualizacionContainer').hide();
                                                $('#apoderadoContainer').hide();
                                                $('#notatecnicomedicoContainer').hide();
                                                $('#fechanotatecnicomedicoContainer').hide();
                                                $('#tablaadjuntosContainer').hide();
                                                $('#tablaadjuntos222Container').hide();
                                                $('#tablaadjuntosContainer222').hide();
                                                $('#matriculaContainer').hide();
                                                $('#fechainformeestudioContainer').hide();
                                                $('#afpgestoraContainer').hide();
                                                $('#nombremedicoContainer').hide();
                                                $('#cargomedicoContainer').hide();

                                                var selectedValue = $(this).val();
                                    
                                                if (selectedValue === 'ACTUALIZACIÓN DE DATOS') {
                                                    $('#cambioactualizacionContainer').show();
                                                } else if (selectedValue === 'COMPRA DE SERVICIOS') {
                                                    $('#notatecnicomedicoContainer').show();
                                                    $('#fechanotatecnicomedicoContainer').show();
                                                    $('#tablaadjuntosContainer').show();
                                                } else if (selectedValue === 'HISTORIA CLÍNICA LEGALIZADA') {            
                                                    $('#matriculaContainer').show();
                                                    $('#afpgestoraContainer').show();
                                                    $('#nombremedicoContainer').show();
                                                    $('#cargomedicoContainer').show();
                                                } else if (selectedValue === 'INCLUSIÓN DE INFORMES MÉDICOS') {            
                                                    $('#tablaadjuntos222Container').show();
                                                } else if (selectedValue === 'INFORME AL EMPLEADOR') {            
                                                    $('#tablaadjuntosContainer222').show();
                                                    $('#tablaadjuntosContainer').show();
                                                } else if (selectedValue === 'EVALUACIÓN POR MEDICINA DEL TRABAJO') {            
                                                    $('#matriculaContainer').show();
                                                    $('#afpgestoraContainer').show();
                                                    $('#nombremedicoContainer').show();
                                                    $('#cargomedicoContainer').show();
                                                }
                                            });
                                        });
                                    </script>
                                    
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="form-group col-lg-3" id="afpgestoraContainer" style="display: none;">
                                                {!! Form::label('afpgestora', 'Gestora/Afp:') !!}
                                                {!! Form::text('afpgestora', $afpgestora, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                                            </div> 
                                            <div class="form-group col-lg-3" id="nombremedicoContainer" style="display: none;">
                                                {!! Form::label('nombremedico', 'Nombre Médico:') !!}
                                                {!! Form::text('nombremedico', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-3" id="cargomedicoContainer" style="display: none;">
                                                {!! Form::label('cargomedico', 'Cargo Médico:') !!}
                                                {!! Form::text('cargomedico', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-3" id="notatecnicomedicoContainer" style="display: none;">
                                                {!! Form::label('notatecnicomedico', 'Nota:') !!}
                                                {!! Form::text('notatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-3" id="fechanotatecnicomedicoContainer" style="display: none;">
                                                {!! Form::label('fechanotatecnicomedico', 'Fecha de Nota:') !!}
                                                {!! Form::date('fechanotatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                            </div> 
                                            <div class="form-group col-lg-3" id="matriculaContainer" style="display: none;">
                                                {!! Form::label('matricula', 'Matricula:') !!}
                                                {!! Form::text('matricula', $matriculacliente, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                                            </div>
                                            <div class="form-group col-lg-3" id="fechainformeestudioContainer" style="display: none;">
                                                {!! Form::label('fechainformeestudio', 'Fecha de Informes y Estudios:') !!}
                                                {!! Form::date('fechainformeestudio', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-lg-12" id="tablaadjuntos222Container" style="display: none;">
                                        <table class="table" style="margin-top: -5px;" >
                                            <thead>
                                                <tr>
                                                    <th class="col-lg-5" style="text-align: center">Requerimiento</th>
                                                    <th class="col-lg-5" style="text-align: center">Tipo</th>
                                                    <th class="col-lg-2" style="text-align: center">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <tr>
                                                        <td class="col-lg-5"><input type="text" name="especialista{{ $i }}" class="form-control" /></td>
                                                        <td class="col-lg-5"><input type="text" name="detalle{{ $i }}" class="form-control" /></td>
                                                        <td class="col-lg-2"><input type="text" name="cantidad{{ $i }}" class="form-control" /></td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-12" id="tablaadjuntosContainer222" style="display: none;">
                                        <table class="table" style="margin-top: -5px;" >
                                            <thead>
                                                <tr>
                                                    <th class="col-lg-5" style="text-align: center">Información</th>
                                                    <th class="col-lg-5" style="text-align: center">Tipo</th>
                                                    {{-- <th class="col-lg-2" style="text-align: center">Cantidad</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <tr>
                                                        <td class="col-lg-5"><input type="text" name="requerimiento2{{ $i }}" class="form-control" /></td>
                                                        <td class="col-lg-5"><input type="text" name="tipo2{{ $i }}" class="form-control" /></td>
                                                        {{-- <td class="col-lg-2"><input type="text" name="cantidad{{ $i }}" class="form-control" /></td> --}}
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-12" id="tablaadjuntosContainer" style="display: none;">
                                        <table class="table" style="margin-top: -5px;" >
                                            <thead>
                                                <tr>
                                                    <th class="col-lg-5" style="text-align: center">Requerimiento</th>
                                                    <th class="col-lg-5" style="text-align: center">Tipo</th>
                                                    {{-- <th class="col-lg-2" style="text-align: center">Cantidad</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <tr>
                                                        <td class="col-lg-5"><input type="text" name="requerimiento{{ $i }}" class="form-control" /></td>
                                                        <td class="col-lg-5"><input type="text" name="tipo{{ $i }}" class="form-control" /></td>
                                                        {{-- <td class="col-lg-2"><input type="text" name="cantidad{{ $i }}" class="form-control" /></td> --}}
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                        
                                        <style>
                                            .table {
                                                width: 100%;
                                                border-collapse: collapse;
                                                margin: 20px 0;
                                                text-align: left;
                                            }
                                            .table th, .table td {
                                                padding: 5px;
                                            }
                                        </style>
                                        
                                        <input type="text" class="form-control" id="tipocartareclamo" name="tipocartareclamo" value="PENSIÓN DE INVALIDEZ" hidden>
                                        <input type="date" class="form-control" id="fechaactual" name="fechaactual" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">GENERAR SOLICITUD</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="solicitudes-content-2" role="tabpanel" aria-labelledby="solicitudes-tab-2">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nivel Procedimiento</th>
                                            <th>Nro.</th>
                                            <th>Subprocedimiento</th>
                                            <th class="text-center">Solicitud</th>
                                            <th>Observación</th>
                                            <th class="text-center">Respuesta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($listasolicitudes as $solicitud)
                                            <tr>
                                                <td>{{ $solicitud->id }}</td>
                                                <td>{{ $solicitud->nivelprocedimiento }}</td>
                                                <td>{{ $solicitud->nro }}</td>
                                                <td>{{ $solicitud->subprocedimiento }}</td>
                                                <td class="text-center">
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/SOLICITUDES/{$solicitud->document}") }}"
                                                    class="btn btn-sm btn-verdocumento fas fa-eye"
                                                    title="VER SOLICITUD" target="_blank"></a>
                                                </td>
                                                <td>
                                                    <form action="{{ route('admin.tramites.guardarrespuesta', $cliente) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="tramite_id" value="{{ $solicitud->id }}">
                                                        <input type="hidden" name="nombretramite" value="INVALIDEZ">

                                                        @if ($solicitud->document2)
                                                            <div>{{ $solicitud->observaciones }}</div>
                                                        @else
                                                            <input type="text" name="observaciones" class="form-control form-control-sm" placeholder="Observación" required>
                                                        @endif
                                                </td>
                                                <td class="text-center">
                                                        @if ($solicitud->document2)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/SOLICITUDES/{$solicitud->document2}") }}"
                                                            class="btn btn-sm btn-verdocumento"
                                                            title="VER RESPUESTA" target="_blank"><i class="fas fa-eye"></i></a>
                                                        @else
                                                            <div class="row gx-2">
                                                                <div class="col">
                                                                    <input type="file" name="document2" class="form-control form-control-sm" accept="application/pdf" required>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <button type="submit" class="btn btn-guardarnuevo"><i class="fas fa-print"></i></button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7">NO HAY REGISTROS DE TIPO "SOLICITUD"</td>
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

<!-- DICTAMEN -->
<div class="modal fade" id="modalDictamen" tabindex="-1" role="dialog" aria-labelledby="modalDictamenLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalDictamenLabel">DICTAMEN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <div class="modal-body">
                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteid', $cliente->id) !!}
                    {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', auth()->user()->name) !!}
                    @csrf
                    <div class="container">
                        <div class="row mb-3 odd-row">
                            <div class="col-md-6">
                                <strong>SUB PROCEDIMIENTO</strong>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-0">NOTIFICACIÓN DE DICTAMEN</p>
                                <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="DICTAMEN" hidden>
                                <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="NOTIFICACIÓN DE DICTAMEN" hidden>
                            </div>
                        </div>

                        <div class="row mb-3 odd-row2">
                            <div class="col-md-6">
                                <strong>FECHA DE SUBIDA</strong>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $documento41 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento41)
                                    <p class="mb-0">{{ $documento41->fechasubida }}</p>
                                @else
                                {!! Form::date('fechasubida[]', \Carbon\Carbon::now()->toDateString(), ['class' => 'form-control', 'readonly']) !!}
                                @endif
                            </div>
                        </div>
            
                        <div class="row mb-3 odd-row">
                            <div class="col-md-6">
                                <strong>PORCENTAJE DE RIESGO DE ACEPTACIÓN/RECHAZO</strong>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $documento41 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento41)
                                    <p class="mb-3" style="margin-top: 10px;">{{ $documento41->porcentajeaceptorechazodictamen }}</p>
                                @else
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control text-center" id="porcentajeaceptorechazodictamen" name="porcentajeaceptorechazodictamen[]" maxlength="3" readonly style="width: 80px;">
                                        <input type="range" class="form-control-range ml-2" id="porcentajeRange" min="0" max="100" value="0" oninput="updateTextInput(this.value);" style="flex-grow: 1;">
                                    </div>
                                    <script>
                                        function updateTextInput(val) {
                                            document.getElementById('porcentajeaceptorechazodictamen').value = val + '%';
                                        }
                                    </script>
                                @endif
                            </div>
                        </div>
            
                        <div class="row mb-3 odd-row2">
                            <div class="col-md-6">
                                <strong>RIESGO DE ACEPTACIÓN/RECHAZO</strong>
                            </div>
                            <div class="col-md-6">
                                @if ($documento41)
                                    <p class="mb-0">{{ $documento41->riesgodictamen }}</p>
                                @else
                                    <div class="form-group">
                                        {!! Form::select('riesgodictamen[]', [
                                            'RIESGO COMÚN' => 'RIESGO COMÚN',
                                            'RIESGO PROFESIONAL' => 'RIESGO PROFESIONAL',
                                            'RIESGO LABORAL' => 'RIESGO LABORAL',
                                        ], null, ['class' => 'form-control', 'id' => 'riesgodictamenSelect', 'placeholder' => '']) !!}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3 odd-row">
                            <div class="col-md-6">
                                <strong>TIPO RIESGO DE ACEPTACIÓN/RECHAZO</strong>
                            </div>
                            <div class="col-md-6">   
                                @if ($documento41)
                                    <p class="mb-0">{{ $documento41->tiporiesgodictamen }}</p>
                                @else
                                    <div id="opciones-adicionales" style="display: none;">
                                        <div class="form-group">
                                            {!! Form::select('tiporiesgodictamen[]', [
                                                'PAGO GLOBAL' => 'PAGO GLOBAL',
                                                'PAGO MENSUAL' => 'PAGO MENSUAL',
                                            ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3 odd-row2">
                            <div class="col-md-6">
                                <strong>DOCUMENTO</strong>
                            </div>
                            <div class="col-md-6">
                                @if ($documento41)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento41->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                    <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3 odd-row">
                            @if ($documento41)
                                <div class="col-md-6">
                                    <strong>ESTADO DE DICTAMEN</strong>
                                </div>
                                <div class="col-md-6">
                                    @if ($documento41->estadodictamen)
                                    <p class="mb-0">{{ $documento41->estadodictamen }}</p>
                                    @else
                                        <div class="form-group">
                                            {!! Form::select('estadodictamen', [
                                                'ACEPTADO' => 'ACEPTADO',
                                                'RECHAZADO' => 'RECHAZADO',
                                            ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                        </div>
                                        
                                    @endif
                                    
                                </div>
                            @endif
                        </div>
                        
                        @php
                            $documento41 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                        @endphp

                        @if ($documento41 && !in_array($documento41->estadodictamen, ['ACEPTADO', 'RECHAZADO']))
                            <button type="submit" name="guardar_estado_dictamen" class="btn btn-subirarchivos d-block mx-auto mb-3" style="width: fit-content;">Actualizar estado</button>
                        @endif

                        @php
                            $documento41 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        @if (!$documento41)
                            <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button>
                        @endif
                    </div>
                </form>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var selectRiesgo = document.getElementById('riesgodictamenSelect');
                    var opcionesAdicionales = document.getElementById('opciones-adicionales');
            
                    selectRiesgo.addEventListener('change', function() {
                        if (selectRiesgo.value === 'RIESGO PROFESIONAL' || selectRiesgo.value === 'RIESGO LABORAL') {
                            opcionesAdicionales.style.display = 'block';
                        } else {
                            opcionesAdicionales.style.display = 'none';
                        }
                    });
                });
            </script> --}}

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
                            $documento29 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE REQUERIMIENTO')->where('tramite', 'INVALIDEZ')->first();
                            $documento30 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ CARTA SOLICITUD A EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                            $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE MODIFICACIÓN DE CITE')->where('tramite', 'INVALIDEZ')->first();

                            $dictamenexiste = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                            $dictamennotificacion = $cliente->tramites()
                                ->where('nivelprocedimiento', 'DICTAMEN')
                                ->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')
                                ->where('tramite', 'INVALIDEZ')
                            ->get();
                            $renunciarevdictamen = $cliente->tramites()
                                ->where('nivelprocedimiento', 'DICTAMEN')
                                ->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE DICTAMEN')
                                ->where('tramite', 'INVALIDEZ')
                            ->get();
                            $firmacontratodictamen = $cliente->tramites()
                                ->where('nivelprocedimiento', 'DICTAMEN')
                                ->where('subprocedimiento', 'FIRMA DE CONTRATO')
                                ->where('tramite', 'INVALIDEZ')
                            ->get();

                            $dictamennotificacionexceso6mes = $cliente->tramites()
                                ->where('nivelprocedimiento', 'DICTAMEN')
                                ->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN (EXCESO DE SEIS MESES)')
                                ->where('tramite', 'INVALIDEZ')
                            ->get();
                            $renunciarevdictamenexceso6mes = $cliente->tramites()
                                ->where('nivelprocedimiento', 'DICTAMEN')
                                ->where('subprocedimiento', 'RENUNCIA A REVISIÓN DE DICTAMEN (EXCESO DE SEIS MESES)')
                                ->where('tramite', 'INVALIDEZ')
                            ->get();
                        @endphp
                        <div class="table-responsive">
                            {{-- DICTAMEN ACEPTADO --}}
                            {{-- NOTIFICACION DE DICTAMEN --}}
                            <div class="scroll-shadow-wrapper">
                                <div class="scroll-shadow-container">
                                    <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -5px;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 5%;">ID</th>
                                                <th style="width: 5%;">NRO.</th>
                                                <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                <th style="width: 5%;">FECHA_EMISIÓN</th>
                                                <th style="width: 5%;">FECHA_REMISIÓN</th>
                                                <th style="width: 10%;">NRO_DICTAMEN</th>
                                                <th style="width: 10%;">RIESGO_ACEPTACIÓN/RECHAZO</th>
                                                <th style="width: 10%;">PORCENTAJE_RIESGO</th>
                                                <th style="width: 10%;">TIPO_RIESGO_DICTAMEN</th>
                                                <th style="width: 10%;">MONTO_DEFINIDO</th>
                                                <th style="width: 10%;">FECHA_REGISTRO</th>
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
                                                        <p class="mb-0">NOTIFICACIÓN DE DICTAMEN</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenotificacion)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenota)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nrodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->riesgodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->porcentajeriesgodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->tiporiesgodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->montocontrato ?? 'NO DEFINIDO' }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/DICTAMEN/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
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
                                                    <p class="mb-0">NOTIFICACIÓN DE DICTAMEN</p>
                                                    <input type="hidden" name="tramite[]" value="INVALIDEZ">
                                                    <input type="hidden" name="nivelprocedimiento[]" value="DICTAMEN">
                                                    <input type="hidden" name="subprocedimiento[]" value="NOTIFICACIÓN DE DICTAMEN">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenota[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="nrodictamen[]">
                                                </td>
                                                <td class="align-middle text-center">
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
                                                        <option value="GRAN INVALIDEZ">GRAN INVALIDEZ</option>
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
                                                                else tipo = 'GRAN INVALIDEZ';
                                                            } else if (riesgo === 'RIESGO COMÚN' || riesgo === 'RIESGO LABORAL') {
                                                                if (porcentaje <= 49) tipo = 'NO CALIFICA';
                                                                else if (porcentaje <= 79) tipo = 'PAGO MENSUAL';
                                                                else tipo = 'GRAN INVALIDEZ';
                                                            }
                                                            if (tipo) {
                                                                tipoSelect.value = tipo;
                                                                tipoHidden.value = tipo;
                                                            }
                                                        }
                                                    });
                                                </script>
                                                <td class="align-middle text-center">
                                                    <input type="number" class="form-control text-center" name="montocontrato[]">
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
                            {{-- RENUNCIA A REVISION DE DICTAMEN --}}
                            <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -5px;">
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
                                                <p class="mb-0">RENUNCIA A REVISIÓN DE DICTAMEN</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fecharetorno)->format('d-m-Y') }}</p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/DICTAMEN/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
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
                                            <p class="mb-0">RENUNCIA A REVISIÓN DE DICTAMEN</p>
                                            <input type="hidden" name="tramite[renunciarevdictamen]" value="INVALIDEZ">
                                            <input type="hidden" name="nivelprocedimiento[renunciarevdictamen]" value="DICTAMEN">
                                            <input type="hidden" name="subprocedimiento[renunciarevdictamen]" value="RENUNCIA A REVISIÓN DE DICTAMEN">
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
                                    <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -5px;">
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
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/DICTAMEN/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
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
                                                    <input type="hidden" name="tramite[firmacontratodictamen]" value="INVALIDEZ">
                                                    <input type="hidden" name="nivelprocedimiento[firmacontratodictamen]" value="DICTAMEN">
                                                    <input type="hidden" name="subprocedimiento[firmacontratodictamen]" value="FIRMA DE CONTRATO">
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
                                                    <input type="number" class="form-control text-center" name="montocontrato[firmacontratodictamen]">
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

                            {{-- DICTAMEN ACEPTADO CON EXCESO DE SEIS MESES --}}
                            {{-- NOTIFICACION DE DICTAMEN (EXCESO DE SEIS MESES) --}}
                            <div class="scroll-shadow-wrapper">
                                <div class="scroll-shadow-container">
                                    <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -5px;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 5%;">ID</th>
                                                <th style="width: 5%;">NRO.</th>
                                                <th style="width: 10%;">SUB_PROCEDIMIENTO</th>
                                                <th style="width: 5%;">FECHA_EMISIÓN</th>
                                                <th style="width: 5%;">FECHA_REMISIÓN</th>
                                                <th style="width: 10%;">NRO_DICTAMEN</th>
                                                <th style="width: 10%;">RIESGO_ACEPTACIÓN/RECHAZO</th>
                                                <th style="width: 10%;">PORCENTAJE_RIESGO</th>
                                                <th style="width: 10%;">TIPO_RIESGO_DICTAMEN</th>
                                                <th style="width: 10%;">MONTO_DEFINIDO</th>
                                                <th style="width: 10%;">FECHA_REGISTRO</th>
                                                <th style="width: 10%;">DOCUMENTO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dictamennotificacionexceso6mes as $documento)
                                                <tr>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->id }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nro }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">NOTIFICACIÓN DE DICTAMEN (EXCESO DE SEIS MESES)</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenotificacion)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechacitenota)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->nrodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->riesgodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->porcentajeriesgodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->tiporiesgodictamen }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ $documento->montocontrato ?? 'NO DEFINIDO' }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="mb-0">{{ \Carbon\Carbon::parse($documento->fechasubida)->format('d-m-Y') }}</p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/DICTAMEN/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            @if ($dictamennotificacionexceso6mes->isEmpty())
                                                <tr class="fila-dictamennotificacionexceso6mes">
                                            @else
                                                <tr class="fila-dictamennotificacionexceso6mes d-none">
                                            @endif
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control" disabled>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="mb-0">NOTIFICACIÓN DE DICTAMEN (EXCESO DE SEIS MESES)</p>
                                                    <input type="hidden" name="tramite[dictamennotificacionexceso6mes]" value="INVALIDEZ">
                                                    <input type="hidden" name="nivelprocedimiento[dictamennotificacionexceso6mes]" value="DICTAMEN">
                                                    <input type="hidden" name="subprocedimiento[dictamennotificacionexceso6mes]" value="NOTIFICACIÓN DE DICTAMEN (EXCESO DE SEIS MESES)">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenotificacion[dictamennotificacionexceso6mes]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="date" class="form-control text-center" name="fechacitenota[dictamennotificacionexceso6mes]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="text" class="form-control text-center" name="nrodictamen[dictamennotificacionexceso6mes]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    <select class="form-control riesgo" name="riesgodictamen[dictamennotificacionexceso6mes]">
                                                        <option value="" disabled selected>Seleccione una opción...</option>
                                                        <option value="RIESGO COMÚN">RIESGO COMÚN</option>
                                                        <option value="RIESGO LABORAL">RIESGO LABORAL</option>
                                                        <option value="RIESGO PROFESIONAL">RIESGO PROFESIONAL</option>
                                                    </select>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <div class="input-group">
                                                        <input type="number" class="form-control text-center porcentaje" name="porcentajeriesgodictamen[dictamennotificacionexceso6mes]" min="0" max="100">
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
                                                        <option value="GRAN INVALIDEZ">GRAN INVALIDEZ</option>
                                                    </select>
                                                    <input type="hidden" class="tipo-hidden" name="tiporiesgodictamen[dictamennotificacionexceso6mes]">
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
                                                                else tipo = 'GRAN INVALIDEZ';
                                                            } else if (riesgo === 'RIESGO COMÚN' || riesgo === 'RIESGO LABORAL') {
                                                                if (porcentaje <= 49) tipo = 'NO CALIFICA';
                                                                else if (porcentaje <= 79) tipo = 'PAGO MENSUAL';
                                                                else tipo = 'GRAN INVALIDEZ';
                                                            }
                                                            if (tipo) {
                                                                tipoSelect.value = tipo;
                                                                tipoHidden.value = tipo;
                                                            }
                                                        }
                                                    });
                                                </script>
                                                <td class="align-middle text-center">
                                                    <input type="number" class="form-control text-center" name="montocontrato[dictamennotificacionexceso6mes]">
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $fechaSubidaDefault = \Carbon\Carbon::now()->toDateString();
                                                    @endphp
                                                    <input type="date" class="form-control text-center" name="fechasubida[dictamennotificacionexceso6mes]" value="{{ $fechaSubidaDefault }}" {{ $puedeEditarFecha ? '' : 'readonly' }}>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <input type="file" name="archivo[dictamennotificacionexceso6mes]" class="dropify mx-auto d-block" accept="application/pdf">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @if (!$dictamennotificacionexceso6mes->isEmpty())
                                    <div class="text-left mt-2" style="margin-bottom: 10px;">
                                        <button type="button" class="btn btn-sm btn-verdocumento" onclick="agregardictamennotificacionexceso6mes()">AGREGAR MÁS</button>
                                    </div>
                                    @endif
                                    <script>
                                        function agregardictamennotificacionexceso6mes() {
                                            const filaOculta = document.querySelector('.fila-dictamennotificacionexceso6mes.d-none');
                                            if (filaOculta) {
                                                filaOculta.classList.remove('d-none');
                                            }
                                        }
                                    </script>
                                </div>
                            </div>
                            {{-- RENUNCIA A REVISION DE DICTAMEN (EXCESO DE SEIS MESES) --}}
                            <table class="table table-bordered table-sm align-middle text-center" style="margin-bottom: -5px;">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 10%;">ID</th>
                                        <th style="width: 10%;">NRO.</th>
                                        <th style="width: 20%;">SUB_PROCEDIMIENTO</th>
                                        <th style="width: 5%;">CITE_NOTIFICACIÓN</th>
                                        <th style="width: 5%;">FECHA_CITE_NOTIFICACIÓN</th>
                                        <th style="width: 5%;">CITE_NOTA</th>
                                        <th style="width: 5%;">FECHA_CITE_NOTA</th>
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
                                                <p class="mb-0">RENUNCIA A REVISIÓN DE DICTAMEN (EXCESO DE SEIS MESES)</p>
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
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/DICTAMEN/{$documento->document}") }}" class="btn btn-sm btn-verdocumento" target="_blank">VER DOCUMENTO</a>
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
                                            <p class="mb-0">RENUNCIA A REVISIÓN DE DICTAMEN (EXCESO DE SEIS MESES)</p>
                                            <input type="hidden" name="tramite[renunciarevdictamen]" value="INVALIDEZ">
                                            <input type="hidden" name="nivelprocedimiento[renunciarevdictamen]" value="DICTAMEN">
                                            <input type="hidden" name="subprocedimiento[renunciarevdictamen]" value="RENUNCIA A REVISIÓN DE DICTAMEN (EXCESO DE SEIS MESES)">
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
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-subirarchivos d-block mx-auto">GUARDAR</button>
                </form>

                @if ($dictamenexiste)
                    <form action="{{ route('admin.tramites.guardarcriterios', $cliente) }}" method="POST" style="margin-top: 20px;">
                        @csrf
                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                        {!! Form::hidden('clienteid', $cliente->id) !!}
                        {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                        {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                        {!! Form::hidden('idtramite', $idTramite) !!}
                        {!! Form::hidden('tramite', 'INVALIDEZ') !!}

                        @php
                            $nroordengravedad = DB::table('criteriosdictamen')
                            ->where('clienteid', $cliente->id)
                            ->where('tramite', 'INVALIDEZ')
                            ->where('nivel', 'NRO ORDEN POR GRAVEDAD DEL DETERIORO')
                            ->get();
                        @endphp
                        <p><strong>I: NOTA: # ORDEN, CORRESPONDE AL ORDENAMIENTO POR GRAVEDAD DEL DETERIORO</strong></p>
                        <table class="table table-bordered table-sm align-middle text-center" id="tablaDeterioro" style="margin-top: -10px;">
                            <thead>
                                <tr>
                                    <th>DESCRIPCION</th>
                                    <th>% ASIGNADO</th>
                                    @if ($nroordengravedad->isEmpty())
                                        <th>Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if ($nroordengravedad->isNotEmpty())
                                    @foreach ($nroordengravedad as $item)
                                        <tr>
                                            <td>{{ $item->subnivel }}</td>
                                            <td>{{ rtrim($item->porcentaje, '%') }}%</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td><input type="text" class="form-control form-control-sm" name="descripcion[]"></td>
                                        <td><input type="number" class="form-control form-control-sm" name="porcentaje[]" step="0.01"></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm eliminarFila">Eliminar</button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        @if ($nroordengravedad->isEmpty())
                            <button type="button" class="btn btn-verdocumento btn-sm" id="agregarFila" style="margin-top: -15px; margin-bottom: 20px;">AGREGAR MÁS</button>
                        @endif
                        @if ($nroordengravedad->isEmpty())
                        <script>
                            document.getElementById('agregarFila').addEventListener('click', function() {
                                let tabla = document.querySelector('#tablaDeterioro tbody');
                                let nuevaFila = `
                                    <tr>
                                        <td><input type="text" class="form-control form-control-sm" name="descripcion[]"></td>
                                        <td><input type="number" class="form-control form-control-sm" name="porcentaje[]" step="0.01"></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm eliminarFila">Eliminar</button>
                                        </td>
                                    </tr>
                                `;
                                tabla.insertAdjacentHTML('beforeend', nuevaFila);
                            });
                            document.addEventListener('click', function(e) {
                                if (e.target.classList.contains('eliminarFila')) {
                                    e.target.closest('tr').remove();
                                }
                            });
                        </script>
                        @endif
                        
                        @php
                            $desempenoocupacional = DB::table('criteriosdictamen')
                                ->where('clienteid', $cliente->id)
                                ->where('tramite', 'INVALIDEZ')
                                ->where('nivel', 'CALIFICACIÓN DE LAS VARIABLES DEPENDIENTES: DESEMPEÑO OCUPACIONAL')
                                ->get();

                            $valoresSeleccionados = [];
                            foreach ($desempenoocupacional as $registro) {
                                $valoresSeleccionados[$registro->subnivel] = $registro->nrocriterio . '|' . rtrim($registro->porcentaje, '%');
                            }

                            $subtotal = $desempenoocupacional->first()->subtotal ?? '0';
                            $totalasignar = $desempenoocupacional->first()->totalasignar ?? '0';
                        @endphp
                        <p><strong>II: CALIFICACIÓN DE LAS VARIABLES DEPENDIENTES</strong></p>
                        <p><strong>DESEMPEÑO OCUPACIONAL</strong></p>
                        <table class="table table-bordered table-sm align-middle text-center" id="tablaDesempeno" style="margin-top: -10px;">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">ACTIVIDADES DE LA VIDA DIARIA</th>
                                    <th colspan="7" class="align-middle text-center">CRITERIOS DE CALIFICACIÓN</th>
                                    <th rowspan="2" class="align-middle text-center">PUNTAJE</th>
                                </tr>
                                <tr>
                                    <th>0<br>0%</th>
                                    <th>1<br>15%</th>
                                    <th>2<br>30%</th>
                                    <th>3<br>60%</th>
                                    <th>4<br>75%</th>
                                    <th>5<br>85%</th>
                                    <th>6<br>99%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $actividades = ['ALIMENTACIÓN', 'HIGIENE', 'VESTIDO', 'MOVILIDAD FUNCIONAL', 'ACTIVIDADES DOMÉSTICAS'];
                                    $valoresPosibles = [
                                        '0|0', '1|15', '2|30', '3|60', '4|75', '5|85', '6|99'
                                    ];
                                    $subnivelesGuardados = array_keys($valoresSeleccionados);
                                @endphp

                                @foreach ($actividades as $actividad)
                                    <tr>
                                        <td class="text-left">{{ $actividad }}</td>
                                        @foreach ($valoresPosibles as $val)
                                            <td>
                                                <input class="desempeno" type="radio" 
                                                    name="desempeno[{{ $actividad }}]" 
                                                    value="{{ $val }}"
                                                    {{ (isset($valoresSeleccionados[$actividad]) && $valoresSeleccionados[$actividad] === $val) ? 'checked' : '' }}
                                                    @if(in_array($actividad, $subnivelesGuardados)) disabled @endif
                                                >
                                            </td>
                                        @endforeach
                                        <td class="puntaje">0%</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="8" class="text-left"><strong>SUBTOTAL</strong></td>
                                    <td id="subtotal">{{ $subtotal }}%</td>
                                    <input type="hidden" name="subtotal" id="inputSubtotal" value="{{ $subtotal }}">
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-left"><strong>TOTAL A ASIGNAR</strong></td>
                                    <td id="totalAsignar">B% {{ $totalasignar }}</td>
                                    <input type="hidden" name="totalasignar" id="inputTotalAsignar" value="{{ $totalasignar }}">
                                </tr>
                            </tbody>
                        </table>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                function procesarRadio(radio) {
                                    const fila = radio.closest('tr');
                                    const puntajeCell = fila.querySelector('.puntaje');
                                    if (!puntajeCell) return;
                                    const val = radio.value || '';
                                    const parts = val.split('|');
                                    const porcentajeRaw = parts.length > 1 ? parts[1] : parts[0];
                                    const porcentaje = parseFloat(porcentajeRaw) || 0;
                                    puntajeCell.textContent = porcentaje + '%';
                                    puntajeCell.dataset.valor = porcentaje;
                                }
                                const radios = document.querySelectorAll('#tablaDesempeno .desempeno');
                                radios.forEach(radio => {
                                    radio.addEventListener('change', function() {
                                        procesarRadio(this);
                                        calcularTotales();
                                    });
                                    if (radio.checked) procesarRadio(radio);
                                });
                                document.querySelectorAll('#tablaDesempeno .puntaje').forEach(cell => {
                                    if (!cell.dataset.valor) cell.dataset.valor = parseFloat(cell.textContent) || 0;
                                });
                                function calcularTotales() {
                                    let subtotal = 0;
                                    document.querySelectorAll('#tablaDesempeno .puntaje').forEach(cell => {
                                        subtotal += parseFloat(cell.dataset.valor) || 0;
                                    });
                                    const totalAsignar = (subtotal / 5).toFixed(2);
                                    document.getElementById('subtotal').textContent = subtotal + '%';
                                    document.getElementById('totalAsignar').textContent = 'B% ' + totalAsignar;
                                    document.getElementById('inputSubtotal').value = subtotal;
                                    document.getElementById('inputTotalAsignar').value = totalAsignar;
                                }
                                calcularTotales();
                            });
                        </script>

                        @php
                            $ocupaciontrabajo = DB::table('criteriosdictamen')
                                ->where('clienteid', $cliente->id)
                                ->where('tramite', 'INVALIDEZ')
                                ->where('nivel', 'CALIFICACIÓN DE LAS VARIABLES DEPENDIENTES: OCUPACIÓN - TRABAJO')
                                ->get();

                            $valoresSeleccionados = [];
                            foreach ($ocupaciontrabajo as $registro) {
                                $valoresSeleccionados[$registro->subnivel] = $registro->nrocriterio . '|' . rtrim($registro->porcentaje, '%');
                            }
                        @endphp
                        <p><strong>OCUPACIÓN - TRABAJO</strong></p>
                        <table class="table table-bordered table-sm align-middle text-center" id="tablaOcupacionTrabajo" style="margin-top: -10px;">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">OCUPACIÓN - TRABAJO</th>
                                    <th colspan="9" class="align-middle text-center">CRITERIOS DE CALIFICACIÓN</th>
                                    <th rowspan="2" class="align-middle text-center">PUNTAJE</th>
                                </tr>
                                <tr>
                                    <th>0<br>0%</th>
                                    <th>1<br>10%</th>
                                    <th>2<br>20%</th>
                                    <th>3<br>40%</th>
                                    <th>4<br>60%</th>
                                    <th>5<br>70%</th>
                                    <th>6<br>80%</th>
                                    <th>7<br>90%</th>
                                    <th>8<br>99%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $actividades = [
                                        'OCUPACIÓN HABITUAL GENERAL / ORIGEN COMÚN',
                                        'OCUPACIÓN HABITUAL ORIGEN PROFESIONAL'
                                    ];
                                    $valoresPosibles = ['0|0','1|10','2|20','3|40','4|60','5|70','6|80','7|90','8|99'];
                                @endphp

                                @foreach ($actividades as $actividad)
                                    <tr>
                                        <td class="text-left">{{ $actividad }}</td>
                                        @foreach ($valoresPosibles as $val)
                                            <td>
                                                <input class="ocupaciontrabajo" type="radio" 
                                                    name="ocupaciontrabajo[{{ $actividad }}]" 
                                                    value="{{ $val }}" 
                                                    {{ (isset($valoresSeleccionados[$actividad]) && $valoresSeleccionados[$actividad] === $val) ? 'checked' : '' }}
                                                    {{ isset($valoresSeleccionados[$actividad]) ? 'disabled' : '' }}
                                                >
                                            </td>
                                        @endforeach
                                        <td class="puntaje2">
                                            @php
                                                $p = isset($valoresSeleccionados[$actividad]) ? explode('|', $valoresSeleccionados[$actividad])[1] : '0';
                                            @endphp
                                            C% {{ $p }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <script>
                            document.querySelectorAll('#tablaOcupacionTrabajo .ocupaciontrabajo').forEach(radio => {
                                radio.addEventListener('change', function() {
                                    const fila = this.closest('tr');
                                    const puntaje2Cell = fila.querySelector('.puntaje2');
                                    const [nroCriterio, porcentaje] = this.value.split('|');
                                    puntaje2Cell.textContent = `C% ${porcentaje}`;
                                });
                            });
                        </script>

                        @php
                            $actividadessociales = DB::table('criteriosdictamen')
                                ->where('clienteid', $cliente->id)
                                ->where('tramite', 'INVALIDEZ')
                                ->where('nivel', 'CALIFICACIÓN DE LAS VARIABLES DEPENDIENTES: ACTIVIDADES SOCIALES')
                                ->get();

                            $valoresSeleccionadosActividades = [];
                            foreach ($actividadessociales as $registro) {
                                $valoresSeleccionadosActividades[$registro->subnivel] = $registro->nrocriterio . '|' . rtrim($registro->porcentaje, '%');
                            }
                        @endphp
                        <p><strong>ACTIVIDADES SOCIALES</strong></p>
                        <table class="table table-bordered table-sm align-middle text-center" id="tablaActividadesSociales" style="margin-top: -10px;">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">ACTIVIDADES SOCIALES</th>
                                    <th colspan="9" class="align-middle text-center">CRITERIOS DE CALIFICACIÓN</th>
                                    <th rowspan="2" class="align-middle text-center">PUNTAJE</th>
                                </tr>
                                <tr>
                                    <th>0<br>0%</th>
                                    <th>1<br>15%</th>
                                    <th>2<br>30%</th>
                                    <th>3<br>60%</th>
                                    <th>4<br>65%</th>
                                    <th>5<br>75%</th>
                                    <th>6<br>85%</th>
                                    <th>7<br>90%</th>
                                    <th>8<br>99%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $actividadesSocialesList = ['INTEGRACIÓN SOCIAL'];
                                    $valoresPosibles = ['0|0','1|15','2|30','3|60','4|65','5|75','6|85','7|90','8|99'];
                                @endphp
                                @foreach ($actividadesSocialesList as $actividad)
                                    @php
                                        $porcentajeGuardado = '0';
                                        if (isset($valoresSeleccionadosActividades[$actividad])) {
                                            $partes = explode('|', $valoresSeleccionadosActividades[$actividad]);
                                            $porcentajeGuardado = $partes[1] ?? '0';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-left">{{ $actividad }}</td>
                                        @foreach ($valoresPosibles as $val)
                                            <td>
                                                <input class="actividadessociales" type="radio"
                                                    name="actividadessociales[{{ $actividad }}]"
                                                    value="{{ $val }}"
                                                    {{ (isset($valoresSeleccionadosActividades[$actividad]) && $valoresSeleccionadosActividades[$actividad] === $val) ? 'checked' : '' }}
                                                    {{ isset($valoresSeleccionadosActividades[$actividad]) ? 'disabled' : '' }}
                                                >
                                            </td>
                                        @endforeach
                                        <td class="puntaje3">D% {{ $porcentajeGuardado }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <script>
                            document.querySelectorAll('#tablaActividadesSociales .actividadessociales').forEach(radio => {
                                radio.addEventListener('change', function() {
                                    const fila = this.closest('tr');
                                    const puntaje3Cell = fila.querySelector('.puntaje3');
                                    const [nroCriterio, porcentaje] = this.value.split('|');
                                    puntaje3Cell.textContent = `D% ${porcentaje}`;
                                });
                            });
                        </script>

                        @php
                            $factorajusteeconomico = DB::table('criteriosdictamen')
                                ->where('clienteid', $cliente->id)
                                ->where('tramite', 'INVALIDEZ')
                                ->where('nivel', 'APLICACIÓN DE FACTORES DE AJUSTE: FACTOR DE AJUSTE ECONÓMICO')
                                ->get();

                            $valoresSeleccionadosFactor = [];
                            foreach ($factorajusteeconomico as $registro) {
                                $valoresSeleccionadosFactor[$registro->subnivel] = $registro->nrocriterio . '|' . rtrim($registro->porcentaje, '%');
                            }
                        @endphp
                        <p><strong>III: APLICACIÓN DE FACTORES DE AJUSTE</strong></p>
                        <p><strong>FACTOR DE AJUSTE ECONÓMICO</strong></p>
                        <table class="table table-bordered table-sm align-middle text-center" id="tablaFactorAjusteEconomico" style="margin-top: -10px;">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">FACTOR DE AJUSTE ECONÓMICO</th>
                                </tr>
                                <tr>
                                    <th>0.01<br>0</th>
                                    <th>0.02<br>1</th>
                                    <th>0.03<br>2</th>
                                    <th>0.04<br>3</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $factorList = ['FACTOR DE AJUSTE ECONÓMICO'];
                                    $valoresFactor = ['0.01|0','0.02|1','0.03|2','0.04|3'];
                                @endphp
                                @foreach ($factorList as $factor)
                                    <tr>
                                        <td class="text-left">{{ $factor }}</td>
                                        @foreach ($valoresFactor as $val)
                                            <td>
                                                <input class="factorajusteeconomico" type="radio"
                                                    name="factorajusteeconomico[{{ $factor }}]"
                                                    value="{{ $val }}"
                                                    {{ (isset($valoresSeleccionadosFactor[$factor]) && $valoresSeleccionadosFactor[$factor] === $val) ? 'checked' : '' }}
                                                    {{ isset($valoresSeleccionadosFactor[$factor]) ? 'disabled' : '' }}
                                                >
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @php
                            $calificacionFinal = DB::table('criteriosdictamen')
                                ->where('clienteid', $cliente->id)
                                ->where('tramite', 'INVALIDEZ')
                                ->where('nivel', 'APLICACIÓN DE FACTORES DE AJUSTE: CALIFICACIÓN FINAL')
                                ->first();

                            $tieneDatosGuardados = !empty($calificacionFinal);

                            $vtr = $calificacionFinal->vtr ?? '';
                            $vtr1 = $calificacionFinal->vtr1 ?? '';
                            $vtr2 = $calificacionFinal->vtr2 ?? '';
                            $subtotal = $calificacionFinal->subtotal ?? '';
                        @endphp
                        <p><strong>CALIFICACIÓN FINAL</strong></p> 
                        <table class="table table-bordered table-sm align-middle text-center" id="tablaCalificacionFinal" style="margin-top: -10px;">
                            <thead>
                                <tr>
                                    <th>VTR</th>
                                    <th>VTR1</th>
                                    <th>VTR2</th>
                                    <th>CALIF. FINAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @if ($tieneDatosGuardados)
                                        <td>{{ $vtr }}</td>
                                        <td>{{ $vtr1 }}</td>
                                        <td>{{ $vtr2 }}</td>
                                        <td>{{ $subtotal }}</td>
                                    @else
                                        <td><input class="calificacionfinal" type="number" step="any" name="calificacionfinal[vtr]" value="{{ old('calificacionfinal.vtr') }}"></td>
                                        <td><input class="calificacionfinal" type="number" step="any" name="calificacionfinal[vtr1]" value="{{ old('calificacionfinal.vtr1') }}"></td>
                                        <td><input class="calificacionfinal" type="number" step="any" name="calificacionfinal[vtr2]" value="{{ old('calificacionfinal.vtr2') }}"></td>
                                        <td><input class="calificacionfinal" type="number" step="any" name="subtotal[calificacion_final]" readonly></td>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                        @if (!$tieneDatosGuardados)
                            <script>
                                document.addEventListener("input", function () {
                                    const filas = document.querySelectorAll("#tablaCalificacionFinal tbody tr");
                                    filas.forEach(fila => {
                                        const inputs = fila.querySelectorAll(".calificacionfinal");
                                        const vtr = parseFloat(inputs[0].value) || 0;
                                        const vtr1 = parseFloat(inputs[1].value) || 0;
                                        const vtr2 = parseFloat(inputs[2].value) || 0;
                                        inputs[3].value = vtr + vtr1 + vtr2;
                                    });
                                });
                            </script>
                        @endif

                        @php
                            $recalificacion = DB::table('criteriosdictamen')
                                ->where('clienteid', $cliente->id)
                                ->where('tramite', 'INVALIDEZ')
                                ->where('nivel', 'NECESIDAD DE RECALIFICACIÓN')
                                ->first();

                            $decisionrecal = $recalificacion->decisionrecal ?? '';
                            $mesGuardado = $recalificacion->mes ?? '';
                            $annoGuardado = $recalificacion->anno ?? '';
                            $yaGuardado = $recalificacion !== null;
                        @endphp

                        <p><strong>IV: NECESIDAD DE RECALIFICACIÓN</strong></p>
                        <table class="table table-bordered table-sm align-middle text-center" id="tablaNecesidadRecalificacion" style="margin-top: -10px;">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-left align-middle" style="font-weight: normal;">EL TRÁMITE SE SUJETO A RECALIFICACIÓN</th>
                                    <th>SI</th>
                                    <th>NO</th>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="necesidadrecalificacion" type="radio" name="necesidadrecalificacion[decisionrecal]" value="SI"
                                            {{ $decisionrecal === 'SI' ? 'checked' : '' }} {{ $yaGuardado ? 'disabled' : '' }}>
                                    </td>
                                    <td>
                                        <input class="necesidadrecalificacion" type="radio" name="necesidadrecalificacion[decisionrecal]" value="NO"
                                            {{ $decisionrecal === 'NO' ? 'checked' : '' }} {{ $yaGuardado ? 'disabled' : '' }}>
                                    </td>
                                </tr>
                            </thead>
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-left align-middle" style="font-weight: normal;">FECHA EN QUE DEBE REALIZARSE LA RECALIFICACIÓN</th>
                                    <th>MES</th>
                                    <th>AÑO</th>
                                </tr>
                                <tr>
                                    <td>
                                        @if ($yaGuardado)
                                            <p class="form-control-plaintext">{{ $mesGuardado }}</p>
                                        @else
                                            <select name="necesidadrecalificacion[mes]" class="form-control">
                                                <option value="">--Seleccione--</option>
                                                @foreach(['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'] as $mes)
                                                    <option value="{{ $mes }}" {{ $mesGuardado === $mes ? 'selected' : '' }}>{{ $mes }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($yaGuardado)
                                            <p class="form-control-plaintext">{{ $annoGuardado }}</p>
                                        @else
                                            <input type="text" name="necesidadrecalificacion[anno]" class="form-control" placeholder="Año" value="{{ $annoGuardado }}">
                                        @endif
                                    </td>
                                </tr>
                            </thead>
                        </table>

                        @if ($nroordengravedad)
                        <button type="submit" class="btn btn-sm btn-outline-primary">GUARDAR CRITERIOS DICTAMEN</button>
                        @endif
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- DICTAMEN ACEPTADO -->
<div class="modal fade" id="modalDictamenAceptado" tabindex="-1" role="dialog" aria-labelledby="modalDictamenAceptadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalDictamenAceptadoLabel">DICTAMEN ACEPTADO</h5>
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
                    <div class="container">
                        <div class="row mb-3 titulos">
                            <div class="col-md-3 text-center">
                                <strong>SUB PROCEDIMIENTO</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>FECHA DE SUBIDA</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>FECHA DE GESTORA</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>DOCUMENTO</strong>
                            </div>
                        </div>
                        @php
                            $documento43 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'ACEPTACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento43 ? 'no-documento' : '' }}" style="margin-top: -10px;">
                            <div class="col-md-3 text-center">
                                <p class="mb-0">ACEPTACIÓN DE DICTAMEN</p>
                                @if (!$documento43)
                                <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="DICTAMEN" hidden>
                                <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="ACEPTACIÓN DE DICTAMEN" hidden>
                            @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @php
                                    $documento43 = $cliente->tramites()->where('subprocedimiento', 'ACEPTACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento43)
                                    <p class="mb-0">{{ $documento43->fechasubida }}</p>
                                @else
                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @php
                                    $documento43 = $cliente->tramites()->where('subprocedimiento', 'ACEPTACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento43)
                                    <p class="mb-0">{{ $documento43->fechagestoradictamen }}</p>
                                @else
                                <input type="date" class="form-control" id="fechagestoradictamen1" name="fechagestoradictamen[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @if ($documento43)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento43->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3 titulos">
                            <div class="col-md-3 text-center">
                                <strong>SUB PROCEDIMIENTO</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>FECHA DE INGRESO</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>FECHA DE SINESTRO</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>DOCUMENTO</strong>
                            </div>
                        </div>
                        @php
                            $documento44 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'FIRMA DE FORMULARIO')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento44 ? 'no-documento' : '' }}" style="margin-top: -10px;">
                            <div class="col-md-3 text-center">
                                <p class="mb-0">FIRMA DE FORMULARIO</p>
                                @if (!$documento44)
                                <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="DICTAMEN" hidden>
                                <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="FIRMA DE FORMULARIO" hidden>
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @php
                                    $documento44 = $cliente->tramites()->where('subprocedimiento', 'FIRMA DE FORMULARIO')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento44)
                                    <p class="mb-0">{{ $documento44->fechasubida }}</p>
                                @else
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @php
                                    $documento44 = $cliente->tramites()->where('subprocedimiento', 'FIRMA DE FORMULARIO')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento44)
                                    <p class="mb-0">{{ $documento44->fechasinestro }}</p>
                                @else
                                <input type="date" class="form-control" id="fechasinestro1" name="fechasinestro[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @if ($documento44)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento44->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3 titulos">
                            <div class="col-md-3 text-center">
                                <strong>SUB PROCEDIMIENTO</strong>
                            </div>
                            <div class="col-md-2 text-center">
                                <strong>FECHA DE SUBIDA</strong>
                            </div>
                            <div class="col-md-2 text-center">
                                <strong>FECHA DE COBRO</strong>
                            </div>
                            <div class="col-md-2 text-center">
                                <strong>MONTO</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>DOCUMENTO</strong>
                            </div>
                        </div>
                        @php
                            $documento45 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'FIRMA DE CONTRATO')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento45 ? 'no-documento' : '' }}" style="margin-top: -10px;">
                            <div class="col-md-3 text-center">
                                <p class="mb-0">FIRMA DE CONTRATO</p>
                                @if (!$documento45)
                                <input type="text" class="form-control" id="tramite3" name="tramite[]" value="INVALIDEZ" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento3" name="nivelprocedimiento[]" value="DICTAMEN" hidden>
                                <input type="text" class="form-control" id="subprocedimiento3" name="subprocedimiento[]" value="FIRMA DE CONTRATO" hidden>
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @php
                                    $documento45 = $cliente->tramites()->where('subprocedimiento', 'FIRMA DE CONTRATO')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento45)
                                    <p class="mb-0">{{ $documento45->fechasubida }}</p>
                                @else
                                <input type="date" class="form-control" id="fechasubida3" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @php
                                    $documento45 = $cliente->tramites()->where('subprocedimiento', 'FIRMA DE CONTRATO')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento45)
                                    <p class="mb-0">{{ $documento45->fechacobrocontrato }}</p>
                                @else
                                <input type="date" class="form-control" id="fechacobrocontrato1" name="fechacobrocontrato[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @php
                                    $documento45 = $cliente->tramites()->where('subprocedimiento', 'FIRMA DE CONTRATO')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento45)
                                    <p class="mb-0">{{ $documento45->montocontrato }}</p>
                                @else
                                <input type="text" class="form-control" id="montocontrato1" name="montocontrato[]">
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @if ($documento45)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento45->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                <input type="file" name="archivo[]" id="archivo3" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3 titulos">
                            <div class="col-md-3 text-center">
                                <strong>SUB PROCEDIMIENTO</strong>
                            </div>
                            <div class="col-md-6 text-center">
                                <strong>FECHA DE SUBIDA</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>DOCUMENTO</strong>
                            </div>
                        </div>
                        @php
                            $documento46 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'COBRO DE PENSIÓN')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento46 ? 'no-documento' : '' }}" style="margin-top: -10px;">
                            <div class="col-md-3 text-center">
                                <p class="mb-0">COBRO DE PENSIÓN</p>
                                @if (!$documento46)
                                <input type="text" class="form-control" id="tramite4" name="tramite[]" value="INVALIDEZ" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento4" name="nivelprocedimiento[]" value="DICTAMEN" hidden>
                                <input type="text" class="form-control" id="subprocedimiento4" name="subprocedimiento[]" value="COBRO DE PENSIÓN" hidden>
                                @endif
                            </div>
                            <div class="col-md-6 text-center">
                                @php
                                    $documento46 = $cliente->tramites()->where('subprocedimiento', 'COBRO DE PENSIÓN')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento46)
                                    <p class="mb-0">{{ $documento46->fechasubida }}</p>
                                @else
                                <input type="date" class="form-control" id="fechasubida4" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @if ($documento46)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento46->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                <input type="file" name="archivo[]" id="archivo4" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3 titulos">
                            <div class="col-md-3 text-center">
                                <strong>SUB PROCEDIMIENTO</strong>
                            </div>
                            <div class="col-md-2 text-center">
                                <strong>FECHA DE SUBIDA</strong>
                            </div>
                            <div class="col-md-2 text-center">
                                <strong>MOTIVO DE RECHAZO</strong>
                            </div>
                            <div class="col-md-2 text-center">
                                <strong>PORCENTAJE</strong>
                            </div>
                            <div class="col-md-3 text-center">
                                <strong>DOCUMENTO</strong>
                            </div>
                        </div>
                        @php
                            $documento47 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento47 ? 'no-documento' : '' }}" style="margin-top: -10px;">
                            <div class="col-md-3 text-center">
                                <p class="mb-0">NOTA DE RECHAZO DE TRÁMITE</p>
                                @if (!$documento47)
                                <input type="text" class="form-control" id="tramite4" name="tramite[]" value="INVALIDEZ" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento4" name="nivelprocedimiento[]" value="DICTAMEN" hidden>
                                <input type="text" class="form-control" id="subprocedimiento4" name="subprocedimiento[]" value="NOTA DE RECHAZO DE TRÁMITE" hidden>
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @php
                                    $documento47 = $cliente->tramites()->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento47)
                                    <p class="mb-0">{{ $documento47->fechasubida }}</p>
                                @else
                                <input type="date" class="form-control" id="fechasubida4" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @php
                                    $documento47 = $cliente->tramites()->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento47)
                                    <p class="mb-0">{{ $documento47->motivorechazo }}</p>
                                @else
                                <input type="text" class="form-control" id="motivorechazo1" name="motivorechazo[]">
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @php
                                    $documento47 = $cliente->tramites()->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if ($documento47)
                                <p class="text-center mb-3" style="margin-top: 10px;">{{ $documento47->porcentajeaceptorechazodictamen }}</p>
                                @else
                                    <div class="input-group">
                                        <input type="text" class="form-control text-center" id="porcentajeaceptorechazodictamen" name="porcentajeaceptorechazodictamen[]" maxlength="3" readonly>
                                    </div>
                                    <div class="mt-3">
                                        <input type="range" class="form-control-range" id="porcentajeRange" min="0" max="100" value="0" oninput="updateTextInput(this.value);">
                                    </div>
                                    <script>
                                        function updateTextInput(val) {
                                            document.getElementById('porcentajeaceptorechazodictamen').value = val + '%';
                                        }
                                    </script>
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @if ($documento47)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento47->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                <input type="file" name="archivo[]" id="archivo4" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>
                    </div>
                    @php
                        $documento43 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'ACEPTACIÓN DE DICTAMEN')->where('tramite', 'INVALIDEZ')->first();
                        $documento44 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'FIRMA DE FORMULARIO')->where('tramite', 'INVALIDEZ')->first();
                        $documento45 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'FIRMA DE CONTRATO')->where('tramite', 'INVALIDEZ')->first();
                        $documento46 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'COBRO DE PENSIÓN')->where('tramite', 'INVALIDEZ')->first();
                        $documento47 = $cliente->tramites()->where('nivelprocedimiento', 'DICTAMEN')->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first();
                    @endphp
                    @if (!$documento43 || !$documento44 || !$documento45 || !$documento46 || !$documento47)
                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button> <!-- Añadido 'btn-block', 'mx-auto' y 'd-block' para centrar -->
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- DICTAMEN RECHAZADO -->
<div class="modal fade" id="modalDictamenRechazado" tabindex="-1" role="dialog" aria-labelledby="modalDictamenRechazadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalDictamenRechazadoLabel">DICTAMEN RECHAZADO</h5>
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
                            $documento48 = $cliente->tramites()->where('subprocedimiento', 'INICIO PROCESO DE APELACIÓN')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento48 ? 'no-documento' : '' }}">
                            <div class="col-md-4 text-center">
                                <p class="mb-0">ABRIR PROCESO DE APELACIÓN</p>
                                <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="DICTAMEN" hidden>
                                <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="INICIO PROCESO DE APELACIÓN" hidden>
                            </div>
                            <div class="col-md-4 text-center">
                                @php
                                    $documento48 = $cliente->tramites()->where('subprocedimiento', 'INICIO PROCESO DE APELACIÓN')->where('tramite', 'INVALIDEZ')->first();
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
                        $documento48 = $cliente->tramites()->where('subprocedimiento', 'INICIO PROCESO DE APELACIÓN')->where('tramite', 'INVALIDEZ')->first();
                    @endphp
                    @if (!$documento48)
                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Iniciar proceso de Apelación</button>
                    @endif


                   {{--  @php
                        $documento41 = $cliente->tramites()->where('subprocedimiento', 'NOTIFICACIÓN DE DICTAMEN')->first();
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
                                ->where('tramite', 'INVALIDEZ')
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
                                <input type="text" class="form-control" name="tramite[]" value="INVALIDEZ" hidden>
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
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th>NIVEL_PROCEDIMIENTO</th>
                                <th>SUB_PROCEDIMIENTO</th>
                                <th>COMUNICAR</th>
                                <th>CAPTURA</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($procedimientotramites as $procedimientotramite)
                                <tr>
                                    <td class="align-middle text-center">{{ $procedimientotramite->nivelprocedimiento }}</td>
                                    <td class="align-middle text-center">{{ $procedimientotramite->subprocedimiento }}</td>
                                    <td class="align-middle text-center">
                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$procedimientotramite->document}") }}" class="btn btn-sm btn-verdocumento fas fa-eye" target="_blank" title="VER DOCUMENTO"></a>
                                    </td>
                                    <td class="align-middle text-center">
                                        @if ($procedimientotramite->estadocomunicado !== 'COMUNICADO')
                                            <a href="{{ route('tramites.actualizarEstado', ['id' => $procedimientotramite->id, 'clienteId' => $cliente->id]) }}" class="btn btn-sm btn-comunicar" target="_blank">COMUNICAR</a>
                                        @else
                                            <span class="badge text-white px-2 py-1" style="background-color: #94c93b; font-size: 0.8rem;">
                                                COMUNICADO
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if ($procedimientotramite->capturacomunicacion)
                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$procedimientotramite->capturacomunicacion}") }}" class="btn btn-sm btn-verdocumento fas fa-eye" title="VER CAPTURA" target="_blank"></a>
                                        @else
                                            <form action="{{ route('tramites.subirArchivo', ['id' => $procedimientotramite->id, 'clienteId' => $cliente->id]) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                                @csrf
                                                <input type="file" name="documento" class="form-control form-control-sm dropify dropify-custom" accept=".jpg" required style="max-height: 20px; padding: 4px 6px;">
                                                <button type="submit" class="btn btn-sm btn-subircaptura fas fa-share-square" title="SUBIR CAPTURA"></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach --}}
                            @php
                                $subprocedimientosEspeciales = [
                                    'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS',
                                    'RECHAZO DE DOCUMENTOS EXTRANJEROS',
                                    'CORRECCIÓN DE DOCUMENTOS EXTRANJEROS',
                                ];

                                $agrupados = collect();

                                foreach ($procedimientotramites as $pt) {
                                    if (in_array($pt->subprocedimiento, $subprocedimientosEspeciales)) {
                                        // Clave única por subprocedimiento y fecha
                                        $clave = $pt->subprocedimiento . '_' . \Carbon\Carbon::parse($pt->created_at)->format('Y-m-d');

                                        if (!$agrupados->has($clave)) {
                                            $agrupados->put($clave, $pt);
                                        }
                                    } else {
                                        // Para los que no son especiales, no agrupar
                                        $agrupados->put(uniqid(), $pt);
                                    }
                                }

                                // Obtener colección final
                                $filtrados = $agrupados->values();
                            @endphp


                            @foreach ($filtrados as $procedimientotramite)
                                <tr>
                                    <td class="align-middle text-center">{{ $procedimientotramite->nivelprocedimiento }}</td>
                                    <td class="align-middle text-center">{{ $procedimientotramite->subprocedimiento }}</td>
                                    <td class="align-middle text-center">
                                        @if ($procedimientotramite->estadocomunicado !== 'COMUNICADO')
                                            <a href="{{ route('tramites.actualizarEstado', ['id' => $procedimientotramite->id, 'clienteId' => $cliente->id]) }}" class="btn btn-sm btn-comunicar" target="_blank">COMUNICAR</a>
                                        @else
                                            <span class="badge text-white px-2 py-1" style="background-color: #94c93b; font-size: 0.8rem;">
                                                COMUNICADO
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if ($procedimientotramite->capturacomunicacion)
                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/INVALIDEZ/COMUNICACIONES/{$procedimientotramite->capturacomunicacion}") }}" class="btn btn-sm btn-verdocumento fas fa-eye" title="VER CAPTURA" target="_blank"></a>
                                        @else
                                            <form action="{{ route('tramites.subirArchivo', ['id' => $procedimientotramite->id, 'clienteId' => $cliente->id]) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                                @csrf
                                                <input type="hidden" name="tramitenombre" value="INVALIDEZ">
                                                <input type="file" name="documento" class="form-control form-control-sm dropify dropify-custom" accept=".jpg" required style="max-height: 20px; padding: 4px 6px;">
                                                <button type="submit" class="btn btn-sm btn-subircaptura fas fa-share-square" title="SUBIR CAPTURA"></button>
                                            </form>
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

{{-- FECHA RETORNO EN DICTAMEN - RENUNCIA A REVISION DE DICTAMEN --}}
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
@endsection

