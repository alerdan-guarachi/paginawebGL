@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.tramites.index') }}">REGRESAR</a>
@if($inicioocontinuidad)
<a class="btn btn-sm float-right btn-seguimiento" data-toggle="modal" data-target="#modalseguimientoproceso">SEGUIMIENTO</a>
<a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalcartayreclamo">CARTA / RECLAMO</a>
<a class="btn btn-sm float-right btn-adjuntosrespuestas" data-toggle="modal" data-target="#modaladjuntosrespuestas">ADJUNTOS Y RESPUESTAS</a>
<a class="btn btn-sm float-right btn-comunicaciones" data-toggle="modal" data-target="#modalcomunicaciones">COMUNICACIONES</a>
<a class="btn btn-sm float-right btn-solicitudes" data-toggle="modal" data-target="#modalsolicitudes">SOLICITUDES</a>
@endif
<h5>PROCEDIMIENTO DE INVALIDEZ DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
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
    @if(!$inicioocontinuidad)
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    <form id="formTramite" action="{{ route('admin.tramites.guardariniciotramiteclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                        {!! Form::hidden('clienteitaid', $cliente->id) !!}
                        {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                        {!! Form::hidden('apoderado', auth()->user()->name) !!}
                        {!! Form::hidden('tramite', 'INVALIDEZ') !!}
                        {!! Form::hidden('nivelprocedimiento', '', ['id' => 'nivelprocedimiento']) !!}
                        @csrf
                        <h5 style="text-align: center; font-size: 25px; margin-bottom:30px; margin-top:20px;">ELIGE UNA OPCIÓN</h5>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="confirmarTramite('INICIO DE TRÁMITE')">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-file-signature fa-5x mb-2"></i>
                                        <span class="h6 mb-0">INICIO DE TRAMITE</span>
                                    </div>
                                </button>
                            </div>
                            <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="confirmarTramite('CONTINUIDAD DE TRÁMITE')">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="fas fa-sync-alt fa-5x mb-2"></i>
                                        <span class="h6 mb-0">CONTINUIDAD DE TRAMITE</span>
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
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
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
                    title: '¡Éxito!',
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
            @endphp
            @if (!$documento5 && !$documento3)
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
            {{-- INICIO DE TRÁMITE / ESTRUCTURA DE VISTA--}}
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
                                $documento3 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('subprocedimiento', 'VALIDACIÓN DE PODER')->first();
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
                                            <span class="badge badge-orange mt-2">{{ $mensajeDias }}</span>
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
                        <div class="col-12 col-md-6 mb-3">
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
                            <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalNotificacionPoder" @if (!$documento1 || !$documento2) disabled @endif>
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-envelope-open-text fa-5x mb-2"></i>
                                    <span class="h6 mb-0">NOTIFICACIÓN DEL PODER</span>
                                </div>
                            </button>
                            <br>
                            @php
                                $documento3 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('subprocedimiento', 'VALIDACIÓN DE PODER')->first();
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
                        <div class="col-12 col-md-6 mb-3">
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
                    </div>
                @endif
            </div>
                <!-- MODAL INGRESO DE TRÁMITE -->
                <div class="modal fade" id="modalIngresoTramite" tabindex="-1" role="dialog" aria-labelledby="modalIngresoTramiteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title titulomodal" id="modalIngresoTramiteLabel">INGRESO DE TRÁMITE</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
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
                                        </div>
                                        @php
                                            $documento1 = $cliente->tramites()->where('tramite', 'INVALIDEZ')->where('subprocedimiento', 'RECEPCION DE TRÁMITE')->where('estadocomunicado', 'COMUNICADO')->first();
                                        @endphp
                                        @if($tramiteinicio)
                                            <div class="row mb-3 align-items-center {{ !$documento1 ? 'no-documento' : '' }}">
                                                <div class="col-md-4 text-center">
                                                    <p class="mb-0">RECEPCIÓN DE TRÁMITE</p>
                                                    @if (!$documento1)
                                                    <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="INGRESO DE TRÁMITE" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="RECEPCIÓN DE TRÁMITE" hidden>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @php
                                                        $documento1 = $cliente->tramites()->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->where('tramite', 'INVALIDEZ')->first();
                                                    @endphp
                                                    @if ($documento1)
                                                        <p class="mb-0">{{ $documento1->fechasubida }}</p>
                                                    @else
                                                    <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @if ($documento1)
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento1->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                    @else
                                                    <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept="application/pdf">
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if($tramitecontinuidad)
                                            <div class="row mb-3 align-items-center {{ !$documento2 ? 'no-documento' : '' }}">
                                                <div class="col-md-4 text-center">
                                                    <p class="mb-0">INCLUSIÓN DE PODER</p>
                                                    <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="INGRESO DE TRÁMITE" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="INCLUSIÓN DE PODER" hidden>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @php
                                                        $documento2 = $cliente->tramites()->where('subprocedimiento', 'INCLUSIÓN DE PODER')->where('tramite', 'INVALIDEZ')->first();
                                                    @endphp
                                                    @if ($documento2)
                                                        <p class="mb-0">{{ $documento2->fechasubida }}</p>
                                                    @else
                                                    <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @if ($documento2)
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento2->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                    @else

                                                    <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept="application/pdf">
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if (!$documento1 && !$documento2)
                                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mt-3" style="width: 180px;">SUBIR ARCHIVOS</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Notificación del Poder -->
                <div class="modal fade" id="modalNotificacionPoder" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionPoderLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
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
                                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                    {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                    @csrf
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
                                        </div>

                                        <!-- RECHAZO DE PODER -->
                                        @php
                                            $documento4 = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE PODER')->where('tramite', 'INVALIDEZ')->first();
                                            $documento3 = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE PODER')->where('tramite', 'INVALIDEZ')->first();
                                            $correccionpoder = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE PODER')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        @if (!$documento3 || $documento4 && (!$documento4 || $documento4))
                                        <div class="row mb-3 align-items-center {{ !$documento4 ? 'no-documento' : '' }}">
                                            <div class="col-md-4 text-center">
                                                <p class="mb-0">RECHAZO DE PODER</p>
                                                @if (!$documento4)
                                                    <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="RECHAZO DE PODER" hidden>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($documento4)
                                                    <p class="mb-0">{{ $documento4->fechasubida }}</p>
                                                @else
                                                    <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($documento4)
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento4->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                @else
                                                    <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept="application/pdf">
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <!-- CORRECCIÓN DE PODER -->
                                        @if ($documento4)
                                        <div class="row mb-3 align-items-center {{ !$correccionpoder ? 'no-documento' : '' }}">
                                            <div class="col-md-4 text-center">
                                                <p class="mb-0">CORRECCIÓN DE PODER</p>
                                                @if (!$correccionpoder)
                                                <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                                <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER" hidden>
                                                <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="CORRECCIÓN DE PODER" hidden>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($correccionpoder)
                                                    <p class="mb-0">{{ $correccionpoder->fechasubida }}</p>
                                                @else
                                                    <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($correccionpoder)
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$correccionpoder->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                @else
                                                    <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept="application/pdf">
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        {{-- VALIDACIÓN DE PODER --}}
                                        @if (!$documento4 || $correccionpoder)
                                        <div class="row mb-3 align-items-center {{ !$documento3 ? 'no-documento' : '' }}">
                                            <div class="col-md-4 text-center">
                                                <p class="mb-0">VALIDACIÓN DE PODER</p>
                                                @if (!$documento3)
                                                <input type="text" class="form-control" id="tramite3" name="tramite[]" value="INVALIDEZ" hidden>
                                                <input type="text" class="form-control" id="nivelprocedimiento3" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER" hidden>
                                                <input type="text" class="form-control" id="subprocedimiento3" name="subprocedimiento[]" value="VALIDACIÓN DE PODER" hidden>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($documento3)
                                                    <p class="mb-0">{{ $documento3->fechasubida }}</p>
                                                @else
                                                    <input type="date" class="form-control" id="fechasubida3" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($documento3)
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento3->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                @else
                                                    <input type="file" name="archivo[]" id="archivo3" class="dropify mx-auto d-block" accept="application/pdf">
                                                @endif
                                            </div>
                                        </div>
                                        @endif



                                        <!-- RECHAZO DE DOCUMENTOS EXTRANJEROS -->
                                        @php
                                            $rechazodocext = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->first();
                                            $validaciondocext = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->first();
                                            $correcciondocext = $cliente->tramites()->where('subprocedimiento', 'CORRECCIÓN DE DOCUMENTOS EXTRANJEROS')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        @if (!$validaciondocext || $rechazodocext && (!$rechazodocext || $rechazodocext))
                                        <div class="row mb-3 align-items-center {{ !$rechazodocext ? 'no-documento' : '' }}">
                                            <div class="col-md-4 text-center">
                                                <p class="mb-0">RECHAZO DE DOC. EXTRANJEROS</p>
                                                @if (!$rechazodocext)
                                                    <input type="text" class="form-control" id="tramite4" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento4" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento4" name="subprocedimiento[]" value="RECHAZO DE DOCUMENTOS EXTRANJEROS" hidden>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($rechazodocext)
                                                    <p class="mb-0">{{ $rechazodocext->fechasubida }}</p>
                                                @else
                                                    <input type="date" class="form-control" id="fechasubida4" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($rechazodocext)
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$rechazodocext->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                @else
                                                    <input type="file" name="archivo[]" id="archivo4" class="dropify mx-auto d-block" accept="application/pdf">
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        <!-- CORRECCIÓN DE DOCUMENTOS EXTRANJEROS -->
                                        @if ($rechazodocext)
                                        <div class="row mb-3 align-items-center {{ !$correcciondocext ? 'no-documento' : '' }}">
                                            <div class="col-md-4 text-center">
                                                <p class="mb-0">CORRECCIÓN DE DOC. EXTRANJEROS</p>
                                                @if (!$correcciondocext)
                                                <input type="text" class="form-control" id="tramite5" name="tramite[]" value="INVALIDEZ" hidden>
                                                <input type="text" class="form-control" id="nivelprocedimiento5" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER" hidden>
                                                <input type="text" class="form-control" id="subprocedimiento5" name="subprocedimiento[]" value="CORRECCIÓN DE DOCUMENTOS EXTRANJEROS" hidden>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($correcciondocext)
                                                    <p class="mb-0">{{ $correcciondocext->fechasubida }}</p>
                                                @else
                                                    <input type="date" class="form-control" id="fechasubida5" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($correcciondocext)
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$correcciondocext->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                @else
                                                    <input type="file" name="archivo[]" id="archivo5" class="dropify mx-auto d-block" accept="application/pdf">
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        {{-- VALIDACIÓN DE PODER --}}
                                        @if (!$rechazodocext || $correcciondocext)
                                        <div class="row mb-3 align-items-center {{ !$validaciondocext ? 'no-documento' : '' }}">
                                            <div class="col-md-4 text-center">
                                                <p class="mb-0">VALIDACIÓN DE DOC. EXTRANJEROS</p>
                                                @if (!$validaciondocext)
                                                <input type="text" class="form-control" id="tramite6" name="tramite[]" value="INVALIDEZ" hidden>
                                                <input type="text" class="form-control" id="nivelprocedimiento6" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER" hidden>
                                                <input type="text" class="form-control" id="subprocedimiento6" name="subprocedimiento[]" value="VALIDACIÓN DE DOCUMENTOS EXTRANJEROS" hidden>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($validaciondocext)
                                                    <p class="mb-0">{{ $validaciondocext->fechasubida }}</p>
                                                @else
                                                    <input type="date" class="form-control" id="fechasubida6" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($validaciondocext)
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$validaciondocext->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                @else
                                                    <input type="file" name="archivo[]" id="archivo6" class="dropify mx-auto d-block" accept="application/pdf">
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @if (!$documento3 || !$validaciondocext)
                                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">SUBIR ARCHIVOS</button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Firma EAP -->
                <div class="modal fade" id="modalFirmaEAP" tabindex="-1" role="dialog" aria-labelledby="modalFirmaEAPLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
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
                                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
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
                                            $documento5 = $cliente->tramites()->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        <div class="row mb-3 align-items-center {{ !$documento5 ? 'no-documento' : '' }}">
                                            <div class="col-md-4 text-center">
                                                <p class="mb-0">ESTADO DE AHORRO PREVISIONAL</p>
                                                <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                                <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="FIRMA EAP" hidden>
                                                <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="ESTADO DE AHORRO PREVISIONAL" hidden>
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @php
                                                    $documento5 = $cliente->tramites()->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->where('tramite', 'INVALIDEZ')->first();
                                                @endphp
                                                @if ($documento5)
                                                    <p class="mb-0">{{ $documento5->fechasubida }}</p>
                                                @else
                                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-center">
                                                @if ($documento5)
                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento5->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                @else
                                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $documento5 = $cliente->tramites()->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->where('tramite', 'INVALIDEZ')->first();
                                    @endphp
                                    @if (!$documento5)
                                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">SUBIR ARCHIVOS</button>
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
                        
                            $documento27 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                            $documento28 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ RESPUESTA A NOTIFICACIÓN TMR')->where('tramite', 'INVALIDEZ')->first();
                        
                            $documento29 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                            $documento30 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                            $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                            $documento32 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ REMISIÓN DE RESPUESTA')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item @if($documento21 || $documento99 || $documento22 || $documento23 || $documento24) text-success @endif" href="#" data-toggle="modal" data-target="#modalEnteGestorSalud">ENTE GESTOR DE SALUD</a>
                            <a class="dropdown-item @if($documento25 || $documento26) text-success @endif" href="#" data-toggle="modal" data-target="#modalNotificacionTMC">NOTIFICACIÓN TMC</a>
                            <a class="dropdown-item @if($documento27 || $documento28) text-success @endif" href="#" data-toggle="modal" data-target="#modalNotificacionTMR">NOTIFICACIÓN TMR</a>
                            <a class="dropdown-item @if($documento29 || $documento30 || $documento31 || $documento32) text-success @endif" href="#" data-toggle="modal" data-target="#modalEmpleador">EMPLEADOR</a>
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
                    <!-- Modal Compra de Servicios -->
                    <div class="modal fade" id="modalCompradeservicios" tabindex="-1" role="dialog" aria-labelledby="modalCompradeserviciosLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalCompradeserviciosLabel">COMPRA DE SERVICIOS</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                        {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                        {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                        {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                        @csrf
                                        <div class="container">
                                            <div class="row mb-3 titulos">
                                                <div class="col-md-3 text-center">
                                                    <strong>SUB PROCEDIMIENTO</strong>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <strong>FECHA DE SUBIDA</strong>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <strong>¿VIAJA?</strong>
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    <strong>DEP. VIAJA</strong>
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    <strong>DOCUMENTO</strong>
                                                </div>
                                            </div>
                                            @php
                                                $documento10 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            <div class="row mb-3 align-items-center {{ !$documento10 ? 'no-documento' : '' }}">
                                                <div class="col-md-3 text-center">
                                                    <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                                    @if (!$documento10)
                                                    <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="NOTIFICACIÓN DE GESTORA" hidden>
                                                    @endif
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    @php
                                                        $documento10 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                                    @endphp
                                                    @if ($documento10)
                                                        <p class="mb-0">{{ $documento10->fechasubida }}</p>
                                                    @else
                                                        <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    @if ($documento10)
                                                        <p class="mb-0">{{ $documento10->viaja }}</p>
                                                    @else
                                                        <select class="form-control" id="viaja1" name="viaja[]" onchange="toggleVisibility()">
                                                            <option value="" disabled selected>¿Viaja?</option>
                                                            <option value="SI">SI</option>
                                                            <option value="NO">NO</option>
                                                        </select>                                            
                                                    @endif
                                                </div>
                                                <div class="col-md-2 text-center">
                                                    @if ($documento10)
                                                        <p class="mb-0">{{ $documento10->departamentoviaja }}</p>
                                                    @else
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
                                                    @endif
                                                </div>
                                                <div class="col-md-3 text-center">
                                                    @if ($documento10)
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento10->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                    @else
                                                        <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block">
                                                    @endif
                                                </div>
                                                
                                                @if ($documento10)
                                                <div class="col-md-12" style="margin-top: 20px;">
                                                    <div class="col-md-12 text-center">
                                                        <strong>ESPECIALIDADES</strong>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 text-center">
                                                            <strong>ESPECIALIDAD</strong>
                                                            <p class="mb-0">{{ $documento10->dep1viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->dep2viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->dep3viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->dep4viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->dep5viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->dep6viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->dep7viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->dep8viaja }}</p>
                                                        </div>
                                                        <div class="col-md-6 text-center">
                                                            <strong>FECHA DE ATENCIÓN</strong>
                                                            <p class="mb-0">{{ $documento10->fechadep1viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->fechadep2viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->fechadep3viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->fechadep4viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->fechadep5viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->fechadep6viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->fechadep7viaja }}</p>
                                                            <p class="mb-0">{{ $documento10->fechadep8viaja }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <div id="especialidades" class="col-md-12 d-none" style="margin-top: 20px;">
                                                    <div class="col-md-12 text-center">
                                                        <strong>ESPECIALIDADES</strong>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 text-left">
                                                            <div class="text-center">
                                                                <strong>ESPECIALIDAD</strong>
                                                            </div>
                                                            <input type="text" class="form-control" id="dep1viaja1" name="dep1viaja[]" placeholder="ESPECIALIDAD 1">
                                                            <input type="text" class="form-control" id="dep2viaja1" name="dep2viaja[]" placeholder="ESPECIALIDAD 2">
                                                            <input type="text" class="form-control" id="dep3viaja1" name="dep3viaja[]" placeholder="ESPECIALIDAD 3">
                                                            <input type="text" class="form-control" id="dep4viaja1" name="dep4viaja[]" placeholder="ESPECIALIDAD 4">
                                                            <input type="text" class="form-control" id="dep5viaja1" name="dep5viaja[]" placeholder="ESPECIALIDAD 5">
                                                            <input type="text" class="form-control" id="dep6viaja1" name="dep6viaja[]" placeholder="ESPECIALIDAD 6">
                                                            <input type="text" class="form-control" id="dep7viaja1" name="dep7viaja[]" placeholder="ESPECIALIDAD 7">
                                                            <input type="text" class="form-control" id="dep8viaja1" name="dep8viaja[]" placeholder="ESPECIALIDAD 8">
                                                        </div>
                                                        <div class="col-md-6 text-right">
                                                            <div class="text-center">
                                                                <strong>FECHA DE ESPECIALIDAD</strong>
                                                            </div>
                                                            <input type="date" class="form-control" id="fechadep1viaja1" name="fechadep1viaja[]">
                                                            <input type="date" class="form-control" id="fechadep2viaja1" name="fechadep2viaja[]">
                                                            <input type="date" class="form-control" id="fechadep3viaja1" name="fechadep3viaja[]">
                                                            <input type="date" class="form-control" id="fechadep4viaja1" name="fechadep4viaja[]">
                                                            <input type="date" class="form-control" id="fechadep5viaja1" name="fechadep5viaja[]">
                                                            <input type="date" class="form-control" id="fechadep6viaja1" name="fechadep6viaja[]">
                                                            <input type="date" class="form-control" id="fechadep7viaja1" name="fechadep7viaja[]">
                                                            <input type="date" class="form-control" id="fechadep8viaja1" name="fechadep8viaja[]">
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                                <script>
                                                    function toggleVisibility() {
                                                        var viajaSelect = document.getElementById('viaja1');
                                                        var especialidadesDiv = document.getElementById('especialidades');
                                                        var departamentoSelect = document.getElementById('departamentoviaja1');
                                                        var notificacion1Div = document.getElementById('notificacion1');
                                                        var notificacion2Div = document.getElementById('notificacion2');
                                                    
                                                        if (viajaSelect.value === 'SI') {
                                                            especialidadesDiv.classList.remove('d-none');
                                                            notificacion1Div.classList.remove('d-none');
                                                            notificacion2Div.classList.remove('d-none');
                                                            departamentoSelect.disabled = false;
                                                        } else {
                                                            especialidadesDiv.classList.add('d-none');
                                                            notificacion1Div.classList.add('d-none');
                                                            notificacion2Div.classList.add('d-none');
                                                            departamentoSelect.disabled = true;
                                                        }
                                                    }
                                                    
                                                    // Inicialmente ocultar las especialidades
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        var viajaSelect = document.getElementById('viaja1');
                                                        if (viajaSelect && viajaSelect.value !== 'SI') {
                                                            document.getElementById('especialidades').classList.add('d-none');
                                                            document.getElementById('notificacion1').classList.add('d-none');
                                                            document.getElementById('notificacion2').classList.add('d-none');
                                                        }
                                                    });
                                                </script>

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
                                            </div>

                                            @php
                                                $documento101 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'AVISO DE VIAJE DEL ASEGURADO')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            <div id="notificacion1" class="row mb-3 d-none align-items-center {{ !$documento101 ? 'no-documento' : '' }}">
                                                <div class="col-md-4 text-center">
                                                    <p class="mb-0">AVISO DE VIAJE DEL ASEGURADO</p>
                                                    @if (!$documento101)
                                                    <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="AVISO DE VIAJE DEL ASEGURADO" hidden>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @php
                                                        $documento101 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'AVISO DE VIAJE DEL ASEGURADO')->where('tramite', 'INVALIDEZ')->first();
                                                    @endphp
                                                    @if ($documento101)
                                                        <p class="mb-0">{{ $documento101->fechasubida }}</p>
                                                    @else
                                                        <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @if ($documento101)
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento101->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                    @else
                                                        <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block">
                                                    @endif
                                                </div>
                                            </div>

                                            @php
                                                $documento102 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'AVISO DE VIAJE DEL ASEGURADO')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            <div id="notificacion2" class="row mb-3 d-none align-items-center {{ !$documento102 ? 'no-documento' : '' }}">
                                                <div class="col-md-4 text-center">
                                                    <p class="mb-0">ORDEN DE TRASLADO</p>
                                                    @if (!$documento102)
                                                    <input type="text" class="form-control" id="tramite3" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento3" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento3" name="subprocedimiento[]" value="ORDEN DE TRASLADO" hidden>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @php
                                                        $documento102 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'ORDEN DE TRASLADO')->where('tramite', 'INVALIDEZ')->first();
                                                    @endphp
                                                    @if ($documento102)
                                                        <p class="mb-0">{{ $documento102->fechasubida }}</p>
                                                    @else
                                                        <input type="date" class="form-control" id="fechasubida3" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @if ($documento102)
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento102->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                    @else
                                                        <input type="file" name="archivo[]" id="archivo3" class="dropify mx-auto d-block">
                                                    @endif
                                                </div>
                                            </div>

                                            @php
                                                $documento10 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->where('viaja', 'SI')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento10)
                                            <div class="row mb-3 align-items-center {{ !$documento101 ? 'no-documento' : '' }}">
                                                <div class="col-md-4 text-center">
                                                    <p class="mb-0">AVISO DE VIAJE DEL ASEGURADO</p>
                                                    @if (!$documento101)
                                                    <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="AVISO DE VIAJE DEL ASEGURADO" hidden>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @php
                                                        $documento101 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'AVISO DE VIAJE DEL ASEGURADO')->where('tramite', 'INVALIDEZ')->first();
                                                    @endphp
                                                    @if ($documento101)
                                                        <p class="mb-0">{{ $documento101->fechasubida }}</p>
                                                    @else
                                                        <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @if ($documento101)
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento101->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                    @else
                                                        <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block">
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                            @php
                                                $documento10 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->where('viaja', 'SI')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento10)
                                            <div class="row mb-3 align-items-center {{ !$documento102 ? 'no-documento' : '' }}">
                                                <div class="col-md-4 text-center">
                                                    <p class="mb-0">ORDEN DE TRASLADO</p>
                                                    @if (!$documento102)
                                                    <input type="text" class="form-control" id="tramite3" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento3" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento3" name="subprocedimiento[]" value="ORDEN DE TRASLADO" hidden>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @php
                                                        $documento102 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'ORDEN DE TRASLADO')->where('tramite', 'INVALIDEZ')->first();
                                                    @endphp
                                                    @if ($documento102)
                                                        <p class="mb-0">{{ $documento102->fechasubida }}</p>
                                                    @else
                                                        <input type="date" class="form-control" id="fechasubida3" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @if ($documento102)
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento102->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                    @else
                                                        <input type="file" name="archivo[]" id="archivo3" class="dropify mx-auto d-block">
                                                    @endif
                                                </div>
                                            </div>
                                            @endif

                                            @php
                                                $documento103 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'AGENDAMIENTO')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            <div class="row mb-3 align-items-center {{ !$documento103 ? 'no-documento' : '' }}">
                                                <div class="col-md-4 text-center">
                                                    <p class="mb-0">AGENDAMIENTO</p>
                                                    @if (!$documento103)
                                                    <input type="text" class="form-control" id="tramite4" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento4" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento4" name="subprocedimiento[]" value="AGENDAMIENTO" hidden>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @php
                                                        $documento103 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'AGENDAMIENTO')->where('tramite', 'INVALIDEZ')->first();
                                                    @endphp
                                                    @if ($documento103)
                                                        <p class="mb-0">{{ $documento103->fechasubida }}</p>
                                                    @else
                                                        <input type="date" class="form-control" id="fechasubida4" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @if ($documento103)
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento103->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                    @else
                                                        <input type="file" name="archivo[]" id="archivo4" class="dropify mx-auto d-block">
                                                    @endif
                                                </div>
                                            </div>

                                            @php
                                                $documento104 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'ORDENES DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            <div class="row mb-3 align-items-center {{ !$documento104 ? 'no-documento' : '' }}">
                                                <div class="col-md-4 text-center">
                                                    <p class="mb-0">ORDENES DE COMPRA DE SERVICIOS</p>
                                                    @if (!$documento104)
                                                    <input type="text" class="form-control" id="tramite5" name="tramite[]" value="INVALIDEZ" hidden>
                                                    <input type="text" class="form-control" id="nivelprocedimiento5" name="nivelprocedimiento[]" value="COMPRA DE SERVICIOS" hidden>
                                                    <input type="text" class="form-control" id="subprocedimiento5" name="subprocedimiento[]" value="ORDENES DE COMPRA DE SERVICIOS" hidden>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @php
                                                        $documento104 = $cliente->tramites()->where('nivelprocedimiento', 'COMPRA DE SERVICIOS')->where('subprocedimiento', 'ORDENES DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                                    @endphp
                                                    @if ($documento104)
                                                        <p class="mb-0">{{ $documento104->fechasubida }}</p>
                                                    @else
                                                        <input type="date" class="form-control" id="fechasubida5" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    @if ($documento104)
                                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento104->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                    @else
                                                        <input type="file" name="archivo[]" id="archivo5" class="dropify mx-auto d-block">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $documento10 = $cliente->tramites()->where('subprocedimiento', 'RECOJO DE NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento11 = $cliente->tramites()->where('subprocedimiento', 'SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO')->where('tramite', 'INVALIDEZ')->first();
                                            $documento12 = $cliente->tramites()->where('subprocedimiento', 'RESPUESTA A NOTA DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            $documento13 = $cliente->tramites()->where('subprocedimiento', 'ADJUNTO DE DECLARACIÓN DE HEREDEROS')->where('tramite', 'INVALIDEZ')->first();
                                        @endphp
                                        @if (!$documento10 || !$documento11 || !$documento12 || !$documento13)
                                            <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button> <!-- Añadido 'btn-block', 'mx-auto' y 'd-block' para centrar -->
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
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
                        
                            $documento270 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                            $documento280 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ RESPUESTA A NOTIFICACIÓN TMR')->where('tramite', 'INVALIDEZ')->first();
                        
                            $documento290 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                            $documento300 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                            $documento310 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                            $documento320 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ REMISIÓN DE RESPUESTA')->where('tramite', 'INVALIDEZ')->first();
                        @endphp
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item @if($documento210 || $documento990 || $documento220 || $documento230/*  || $documento240 */) text-success @endif" href="#" data-toggle="modal" data-target="#modalEnteGestorSalud2">ENTE GESTOR DE SALUD</a>
                            <a class="dropdown-item @if($documento250 || $documento260) text-success @endif" href="#" data-toggle="modal" data-target="#modalNotificacionTMC2">NOTIFICACIÓN TMC</a>
                            <a class="dropdown-item @if($documento270 || $documento280) text-success @endif" href="#" data-toggle="modal" data-target="#modalNotificacionTMR2">NOTIFICACIÓN TMR</a>
                            <a class="dropdown-item @if($documento290 || $documento300 || $documento310 || $documento320) text-success @endif" href="#" data-toggle="modal" data-target="#modalEmpleador2">EMPLEADOR</a>
                        </div>
                    </div>
                </div>
            </div>
                         
            {{-- SOLICITUD DE INFORMACION TECNICO MEDICO --}}
            <!-- Modal ENTE GESTOR DE SALUD -->
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
                                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                @csrf
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
                                    </div>
                                    
                                    <div class="row mb-3 align-items-center {{ !$documento21 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                            @if (!$documento21)
                                            <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-center">
                                            @php
                                                $documento21 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento21)
                                                <p class="mb-0">{{ $documento21->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-center">
                                            @if ($documento21)
                                                <p class="mb-0">{{ $documento21->seguro }} TIENE SEGURO</p>
                                            @else
                                                <select class="form-control" id="seguro1" name="seguro[]">
                                                    <option value="" disabled selected>¿Tiene Seguro?</option>
                                                    <option value="SI">SI</option>
                                                    <option value="NO">NO</option>
                                                </select>                                            
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento21)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento21->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    @php
                                        $documento21 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('seguro', 'NO')->where('tramite', 'INVALIDEZ')->first();
                                        $documento99 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                    @endphp
                                    @if ($documento21)
                                    <div class="row mb-3 align-items-center {{ !$documento99 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">SOLICITUD DE COMPRA DE SERVICIOS</p>
                                            <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento99 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento99)
                                                <p class="mb-0">{{ $documento99->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento99)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento99->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    @php
                                       
                                        $documento21 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('seguro', 'SI')->where('tramite', 'INVALIDEZ')->first();
                                    @endphp
                                    @if ($documento21)
                                    <div class="row mb-3 align-items-center {{ !$documento22 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS</p>
                                            <input type="text" class="form-control" id="tramite3" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento3" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento3" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento22 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento22)
                                                <p class="mb-0">{{ $documento22->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida3" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento22)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento22->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo3" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center {{ !$documento23 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">SOLICITUD DE HISTORIA CLÍNICA</p>
                                            <input type="text" class="form-control" id="tramite4" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento4" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento4" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento23 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento23)
                                                <p class="mb-0">{{ $documento23->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida4" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento23)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento23->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo4" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center {{ !$documento24 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">RESPUESTA TÉCNICO MÉDICO</p>
                                            <input type="text" class="form-control" id="tramite5" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento5" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento5" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ RESPUESTA TÉCNICO MÉDICO" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento24 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ RESPUESTA TÉCNICO MÉDICO')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento24)
                                                <p class="mb-0">{{ $documento24->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida5" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento24)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento24->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo5" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @php
                                    $documento21 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento99 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                    $documento22 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS')->where('tramite', 'INVALIDEZ')->first();
                                    $documento23 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento24 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ RESPUESTA TÉCNICO MÉDICO')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if (!$documento21 || !$documento22 || !$documento23 || !$documento24 || !$documento99)
                                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal NOTIFICACION TMC -->
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
                                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                @csrf
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
                                    </div>
                                    <div class="row mb-3 align-items-center {{ !$documento25 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                            @if (!$documento25)
                                            <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento25 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento25)
                                                <p class="mb-0">{{ $documento25->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento25)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento25->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento26 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">RESPUESTA A NOTIFICACIÓN TMC</p>
                                            <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento26 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento26)
                                                <p class="mb-0">{{ $documento26->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento26)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento26->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $documento25 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento26 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if (!$documento25 || !$documento26)
                                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal NOTIFICACION TMR -->
            <div class="modal fade" id="modalNotificacionTMR" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionTMRLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title titulomodal" id="modalNotificacionTMRLabel">NOTIFICACIÓN TMR</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                @csrf
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
                                    </div>
                                    <div class="row mb-3 align-items-center {{ !$documento27 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                            @if (!$documento27)
                                            <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento27 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento27)
                                                <p class="mb-0">{{ $documento27->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento27)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento27->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento28 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">RESPUESTA A NOTIFICACIÓN TMR</p>
                                            <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="NOTIFICACIÓN TMR _ RESPUESTA A NOTIFICACIÓN TMR" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento28 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ RESPUESTA A NOTIFICACIÓN TMR')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento28)
                                                <p class="mb-0">{{ $documento28->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento28)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento28->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $documento27 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento28 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ RESPUESTA A NOTIFICACIÓN TMR')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if (!$documento27 || !$documento28)
                                    <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button> <!-- Añadido 'btn-block', 'mx-auto' y 'd-block' para centrar -->
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal EMPLEADOR -->
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
                                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                @csrf
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
                                    </div>
                                    <div class="row mb-3 align-items-center {{ !$documento29 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                            @if (!$documento29)
                                            <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="EMPLEADOR _ NOTIFICACIÓN DE GESTORA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento29 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento29)
                                                <p class="mb-0">{{ $documento29->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento29)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento29->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento30 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">SOLICITUD DE INFORME AL EMPLEADOR</p>
                                            @if (!$documento30)
                                            <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento30 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento30)
                                                <p class="mb-0">{{ $documento30->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento30)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento30->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento31 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR</p>
                                            @if (!$documento31)
                                            <input type="text" class="form-control" id="tramite3" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento3" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento3" name="subprocedimiento[]" value="EMPLEADOR _ REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento31)
                                                <p class="mb-0">{{ $documento31->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida3" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento31)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento31->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo3" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento32 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">REMISIÓN DE RESPUESTA</p>
                                            @if (!$documento32)
                                            <input type="text" class="form-control" id="tramite4" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento4" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento4" name="subprocedimiento[]" value="EMPLEADOR _ REMISIÓN DE RESPUESTA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento32 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'EMPLEADOR _ REMISIÓN DE RESPUESTA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento32)
                                                <p class="mb-0">{{ $documento32->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida4" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento32)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento32->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo4" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $documento29 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento30 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'SOLICITUD DE INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                    $documento31 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                    $documento32 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'REMISIÓN DE RESPUESTA')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if (!$documento29 || !$documento30 || !$documento31|| !$documento32)
                                    <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button>
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


            {{-- SOLICITUD DE INFORMACION TECNICO COMPLEMENTARIA --}}
            <!-- Modal ENTE GESTOR DE SALUD -->
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
                                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                @csrf
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
                                    </div>
                                    
                                    <div class="row mb-3 align-items-center {{ !$documento210 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                            @if (!$documento210)
                                            <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-center">
                                            @php
                                                $documento210 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento210)
                                                <p class="mb-0">{{ $documento210->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-center">
                                            @if ($documento210)
                                                <p class="mb-0">{{ $documento210->seguro }} TIENE SEGURO</p>
                                            @else
                                                <select class="form-control" id="seguro1" name="seguro[]">
                                                    <option value="" disabled selected>¿Tiene Seguro?</option>
                                                    <option value="SI">SI</option>
                                                    <option value="NO">NO</option>
                                                </select>                                            
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento210)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento210->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    @php
                                        $documento210 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('seguro', 'NO')->where('tramite', 'INVALIDEZ')->first();
                                        $documento990 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                    @endphp
                                    @if ($documento210)
                                    <div class="row mb-3 align-items-center {{ !$documento990 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">SOLICITUD DE COMPRA DE SERVICIOS</p>
                                            <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento990 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento990)
                                                <p class="mb-0">{{ $documento990->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento990)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento990->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    @php
                                       
                                        $documento210 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('seguro', 'SI')->where('tramite', 'INVALIDEZ')->first();
                                    @endphp
                                    @if ($documento210)
                                    <div class="row mb-3 align-items-center {{ !$documento220 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS</p>
                                            <input type="text" class="form-control" id="tramite3" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento3" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento3" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento220 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento220)
                                                <p class="mb-0">{{ $documento220->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida3" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento220)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento220->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo3" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-center {{ !$documento230 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">SOLICITUD DE HISTORIA CLÍNICA</p>
                                            <input type="text" class="form-control" id="tramite4" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento4" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento4" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento230 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento230)
                                                <p class="mb-0">{{ $documento230->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida4" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento230)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento230->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo4" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                    {{-- <div class="row mb-3 align-items-center {{ !$documento24 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">RESPUESTA TÉCNICO MÉDICO</p>
                                            <input type="text" class="form-control" id="tramite5" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento5" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento5" name="subprocedimiento[]" value="ENTE GESTOR DE SALUD _ RESPUESTA TÉCNICO MÉDICO" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento24 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ RESPUESTA TÉCNICO MÉDICO')->first();
                                            @endphp
                                            @if ($documento24)
                                                <p class="mb-0">{{ $documento24->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida5" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento24)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento24->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo5" class="dropify mx-auto d-block">
                                            @endif
                                        </div>
                                    </div>--}}
                                    @endif 
                                </div>
                                @php
                                    $documento210 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento990 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE COMPRA DE SERVICIOS')->where('tramite', 'INVALIDEZ')->first();
                                    $documento220 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE EVALUACIÓN POR MEDICINA DEL TRABAJO EGS')->where('tramite', 'INVALIDEZ')->first();
                                    $documento230 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ SOLICITUD DE HISTORIA CLÍNICA')->where('tramite', 'INVALIDEZ')->first();
                                    /* $documento24 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO')->where('subprocedimiento', 'ENTE GESTOR DE SALUD _ RESPUESTA COMPLEMENTARIA')->first(); */
                                @endphp
                                @if (!$documento210 || !$documento220 || !$documento230 || !$documento240 || !$documento990)
                                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal NOTIFICACION TMC -->
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
                                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                @csrf
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
                                    </div>
                                    <div class="row mb-3 align-items-center {{ !$documento250 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                            @if (!$documento250)
                                            <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento250 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento250)
                                                <p class="mb-0">{{ $documento250->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento250)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento250->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento260 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">RESPUESTA A NOTIFICACIÓN TMC</p>
                                            <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento260 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento260)
                                                <p class="mb-0">{{ $documento260->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento260)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento260->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $documento250 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento260 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMC _ RESPUESTA A NOTIFICACIÓN TMC')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if (!$documento250 || !$documento260)
                                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal NOTIFICACION TMR -->
            <div class="modal fade" id="modalNotificacionTMR2" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionTMR2Label" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title titulomodal" id="modalNotificacionTMR2Label">NOTIFICACIÓN TMR</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                @csrf
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
                                    </div>
                                    <div class="row mb-3 align-items-center {{ !$documento270 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                            @if (!$documento270)
                                            <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento270 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento270)
                                                <p class="mb-0">{{ $documento270->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento270)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento270->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento280 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">RESPUESTA A NOTIFICACIÓN TMR</p>
                                            <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="NOTIFICACIÓN TMR _ RESPUESTA A NOTIFICACIÓN TMR" hidden>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento280 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ RESPUESTA A NOTIFICACIÓN TMR')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento280)
                                                <p class="mb-0">{{ $documento280->fechasubida }}</p>
                                            @else
                                            <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento280)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento280->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                            <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $documento270 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento280 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN TMR _ RESPUESTA A NOTIFICACIÓN TMR')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if (!$documento270 || !$documento280)
                                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button> <!-- Añadido 'btn-block', 'mx-auto' y 'd-block' para centrar -->
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal EMPLEADOR -->
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
                                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                                {!! Form::hidden('apoderado', auth()->user()->name) !!}
                                @csrf
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
                                    </div>
                                    <div class="row mb-3 align-items-center {{ !$documento29 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                            @if (!$documento290)
                                            <input type="text" class="form-control" id="tramite1" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="EMPLEADOR _ NOTIFICACIÓN DE GESTORA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento290 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento290)
                                                <p class="mb-0">{{ $documento290->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento290)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento290->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento300 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">SOLICITUD DE INFORME AL EMPLEADOR</p>
                                            @if (!$documento300)
                                            <input type="text" class="form-control" id="tramite2" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento300 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ SOLICITUD DE INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento300)
                                                <p class="mb-0">{{ $documento300->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento300)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento300->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento310 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR</p>
                                            @if (!$documento310)
                                            <input type="text" class="form-control" id="tramite3" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento3" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento3" name="subprocedimiento[]" value="EMPLEADOR _ REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento310 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento310)
                                                <p class="mb-0">{{ $documento310->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida3" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento310)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento310->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo3" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3 align-items-center {{ !$documento320 ? 'no-documento' : '' }}">
                                        <div class="col-md-4 text-center">
                                            <p class="mb-0">REMISIÓN DE RESPUESTA</p>
                                            @if (!$documento320)
                                            <input type="text" class="form-control" id="tramite4" name="tramite[]" value="INVALIDEZ" hidden>
                                            <input type="text" class="form-control" id="nivelprocedimiento4" name="nivelprocedimiento[]" value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA" hidden>
                                            <input type="text" class="form-control" id="subprocedimiento4" name="subprocedimiento[]" value="EMPLEADOR _ REMISIÓN DE RESPUESTA" hidden>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @php
                                                $documento320 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'EMPLEADOR _ REMISIÓN DE RESPUESTA')->where('tramite', 'INVALIDEZ')->first();
                                            @endphp
                                            @if ($documento320)
                                                <p class="mb-0">{{ $documento320->fechasubida }}</p>
                                            @else
                                                <input type="date" class="form-control" id="fechasubida4" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" disabled>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if ($documento320)
                                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento320->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                            @else
                                                <input type="file" name="archivo[]" id="archivo4" class="dropify mx-auto d-block" accept=".pdf">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $documento290 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->where('tramite', 'INVALIDEZ')->first();
                                    $documento300 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'SOLICITUD DE INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                    $documento310 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'REITERACIÓN DE SOLICITUD DEL INFORME AL EMPLEADOR')->where('tramite', 'INVALIDEZ')->first();
                                    $documento320 = $cliente->tramites()->where('nivelprocedimiento', 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA')->where('subprocedimiento', 'REMISIÓN DE RESPUESTA')->where('tramite', 'INVALIDEZ')->first();
                                @endphp
                                @if (!$documento290 || !$documento300 || !$documento310|| !$documento320)
                                    <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button>
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
            </div>

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
                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
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
    <div class="modal-dialog modal-xl" role="document">
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
                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', auth()->user()->name) !!}
                    <input type="text" class="form-control" id="tramite" name="tramite" value="INVALIDEZ" hidden>
                    <input type="date" class="form-control" id="fechasubida" name="fechasubida" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group col-lg-12">
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
                            <div class="form-group  col-lg-12">
                                {!! Form::label('apoderado', 'Apoderado:') !!}
                                {!! Form::select('apoderado', $personal->pluck('nombrecompleto', 'id'), null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'personalSelect', 'required' => 'required']) !!}
                                @error('apoderado')
                                    <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-lg-12">
                                {!! Form::label('notatecnicomedico', 'Nota:') !!}
                                {!! Form::text('notatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            </div>
                            <div class="form-group col-lg-12">
                                {!! Form::label('fechanotatecnicomedico', 'Fecha de Nota:') !!}
                                {!! Form::date('fechanotatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <table class="table" style="margin-top: -5px;">
                                <thead>
                                    <tr>
                                        <th class="col-lg-5" style="text-align: center">Especialistas</th>
                                        <th class="col-lg-5" style="text-align: center">Detalle</th>
                                        <th class="col-lg-2" style="text-align: center">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <tr>
                                            <td class="col-lg-5"><input type="text" name="especialista{{ $i }}" class="form-control" /></td>
                                            <td class="col-lg-5"><input type="text" name="detalle{{ $i }}" class="form-control" /></td>
                                            <td class="col-lg-2"><input type="text" name="cantidad{{ $i }}" class="form-control" /></td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                            <input type="text" class="form-control" id="tipocartareclamo" name="tipocartareclamo" value="PENSIÓN DE INVALIDEZ" hidden>
                            <input type="date" class="form-control" id="fechaactual" name="fechaactual" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">GENERAR ADJUNTO Y RESPUESTA</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SOLICITUDES --}}
<div class="modal fade" id="modalsolicitudes" tabindex="-1" role="dialog" aria-labelledby="modalsolicitudesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalsolicitudesLabel">SOLICITUDES</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.tramites.generarsolicitud', $cliente) }}" method="GET" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                    {!! Form::hidden('apoderado', auth()->user()->name) !!}
                    <input type="text" class="form-control" id="tramite" name="tramite" value="INVALIDEZ" hidden>
                    <input type="date" class="form-control" id="fechasubida" name="fechasubida" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                    @csrf
                    <div class="row">
                        <!-- Asegúrate de incluir jQuery -->
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    {!! Form::label('tipo_pdf', 'Tipo de Solicitud:') !!}
                                    {!! Form::select('tipo_pdf', [
                                        'ACTUALIZACIÓN DE DATOS' => 'ACTUALIZACIÓN DE DATOS',
                                        'EVALUACIÓN POR MEDICINA DEL TRABAJO' => 'EVALUACIÓN POR MEDICINA DEL TRABAJO',
                                        'INCLUSIÓN DE INFORMES MÉDICOS' => 'INCLUSIÓN DE INFORMES MÉDICOS',
                                        'SITM COMPRA DE SERVICIOS' => 'SITM COMPRA DE SERVICIOS',
                                        'SITM EVALUACIÓN POR MEDICINA DEL TRABAJO EGS' => 'SITM EVALUACIÓN POR MEDICINA DEL TRABAJO EGS',
                                        'SITM HISTORIA CLÍNICA' => 'SITM HISTORIA CLÍNICA',
                                        'SITM INFORME AL EMPLEADOR' => 'SITM INFORME AL EMPLEADOR',
                                        'SIC COMPRA DE SERVICIOS' => 'SIC COMPRA DE SERVICIOS',
                                        'SIC EVALUACIÓN POR MEDICINA DEL TRABAJO EGS' => 'SIC EVALUACIÓN POR MEDICINA DEL TRABAJO EGS',
                                        'SIC HISTORIA CLÍNICA' => 'SIC HISTORIA CLÍNICA',
                                        'SIC INFORME AL EMPLEADOR' => 'SIC INFORME AL EMPLEADOR',
                                    ], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoPdfSelect2', 'required' => 'required']) !!}
                                    @error('tipo_pdf')
                                        <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-lg-6">
                                    {!! Form::label('apoderado', 'Apoderado:') !!}
                                    {!! Form::select('apoderado', $personal->pluck('nombrecompleto', 'id'), null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'personalSelect', 'required' => 'required']) !!}
                                    @error('apoderado')
                                        <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                    @enderror
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
                                    
                                    // Set initial width to fit placeholder text
                                    input.css('width', `${placeholderText.length * 0.95}ch`);
                                    
                                    // Update width on input event
                                    input.on('input', function() {
                                        const inputLength = $(this).val().length;
                                        const newWidth = Math.max(inputLength * 0.95, placeholderText.length * 0.95);
                                        $(this).css('width', `${newWidth}ch`);
                                    });
                                });
                            </script>
                        </div>

                        {{-- <script>
                            $(document).ready(function() {
                                $('#tipoPdfSelect2').on('change', function() {
                                    if ($(this).val() === 'ACTUALIZACIÓN DE DATOS') {
                                        $('#cambioactualizacionContainer').show();
                                    } else {;
                                        $('#cambioactualizacionContainer').hide();
                                        $('#apoderadoContainer').hide();
                                    }
                                    if ($(this).val() === 'COMPRA DE SERVICIOS') {
                                        $('#notatecnicomedicoContainer').show();
                                        $('#fechanotatecnicomedicoContainer').show();
                                        $('#tablaadjuntosContainer').show();
                                    } else {;
                                        $('#notatecnicomedicoContainer').hide();
                                        $('#fechanotatecnicomedicoContainer').hide();
                                        $('#tablaadjuntosContainer').hide();
                                    }
                                    if ($(this).val() === 'HISTORIA CLÍNICA') {            
                                        $('#tablaadjuntosContainer').show();
                                        $('#matriculaContainer').show();
                                        $('#fechainformeestudioContainer').show();
                                    } else {;
                                        $('#tablaadjuntosContainer').hide();
                                        $('#matriculaContainer').hide();
                                        $('#fechainformeestudioContainer').hide();
                                    }
                                    if ($(this).val() === 'INCLUSIÓN DE INFORMES MÉDICOS') {            
                                        $('#tablaadjuntos222Container').show();
                                    } else {;
                                        $('#tablaadjuntos222Container').hide();
                                    }
                                    if ($(this).val() === 'INFORME AL EMPLEADOR') {            
                                        $('#tablaadjuntosContainer222').show();
                                        $('#tablaadjuntosContainer').show();
                                    } else {;
                                        $('#tablaadjuntosContainer222').hide();
                                        $('#tablaadjuntosContainer').hide();
                                    }
                                    if ($(this).val() === 'HISTORIA CLÍNICA') {            
                                        $('#tablaadjuntosContainer').show();
                                        $('#matriculaContainer').show();
                                        $('#fechainformeestudioContainer').show();
                                    } else {;
                                        $('#tablaadjuntosContainer').hide();
                                        $('#matriculaContainer').hide();
                                        $('#fechainformeestudioContainer').hide();
                                    }
                                    if ($(this).val() === 'EVALUACIÓN POR MEDICINA DEL TRABAJO EGS') {            
                                        $('#notatecnicomedicoContainer').show();
                                        $('#fechanotatecnicomedicoContainer').show();
                                        $('#matriculaContainer').show();
                                        $('#tablaadjuntosContainer').show();
                                    } else {;
                                        $('#notatecnicomedicoContainer').hide();
                                        $('#fechanotatecnicomedicoContainer').hide();
                                        $('#matriculaContainer').hide();
                                        $('#tablaadjuntosContainer').hide();
                                    }
                                    if ($(this).val() === 'EVALUACIÓN POR MEDICINA DEL TRABAJO') {            
                                        $('#notatecnicomedicoContainer').show();
                                        $('#fechanotatecnicomedicoContainer').show();
                                        $('#matriculaContainer').show();
                                        $('#tablaadjuntosContainer').show();
                                    } else {;
                                        $('#notatecnicomedicoContainer').hide();
                                        $('#fechanotatecnicomedicoContainer').hide();
                                        $('#matriculaContainer').hide();
                                        $('#tablaadjuntosContainer').hide();
                                    }
                                });
                            });
                        </script> --}}
                        <script>
                            $(document).ready(function() {
                                $('#tipoPdfSelect2').on('change', function() {
                                    // Ocultar todos los contenedores al inicio
                                    $('#cambioactualizacionContainer').hide();
                                    $('#apoderadoContainer').hide();
                                    $('#notatecnicomedicoContainer').hide();
                                    $('#fechanotatecnicomedicoContainer').hide();
                                    $('#tablaadjuntosContainer').hide();
                                    $('#tablaadjuntos222Container').hide();
                                    $('#tablaadjuntosContainer222').hide();
                                    $('#matriculaContainer').hide();
                                    $('#fechainformeestudioContainer').hide();
                        
                                    // Obtener el valor seleccionado
                                    var selectedValue = $(this).val();
                        
                                    // Mostrar contenedores según el valor seleccionado
                                    if (selectedValue === 'ACTUALIZACIÓN DE DATOS') {
                                        $('#cambioactualizacionContainer').show();
                                    } else if (selectedValue === 'SITM COMPRA DE SERVICIOS' ||
                                        selectedValue === 'SIC COMPRA DE SERVICIOS') {
                                        $('#notatecnicomedicoContainer').show();
                                        $('#fechanotatecnicomedicoContainer').show();
                                        $('#tablaadjuntosContainer').show();
                                    } else if (selectedValue === 'SITM HISTORIA CLÍNICA'||
                                    selectedValue === 'SIC HISTORIA CLÍNICA') {            
                                        $('#tablaadjuntosContainer').show();
                                        $('#matriculaContainer').show();
                                        $('#fechainformeestudioContainer').show();
                                    } else if (selectedValue === 'INCLUSIÓN DE INFORMES MÉDICOS') {            
                                        $('#tablaadjuntos222Container').show();
                                    } else if (selectedValue === 'SITM INFORME AL EMPLEADOR'||
                                    selectedValue === 'SIC INFORME AL EMPLEADOR') {            
                                        $('#tablaadjuntosContainer222').show();
                                        $('#tablaadjuntosContainer').show();
                                    } else if (selectedValue === 'SITM EVALUACIÓN POR MEDICINA DEL TRABAJO EGS' ||
                                        selectedValue === 'SIC EVALUACIÓN POR MEDICINA DEL TRABAJO EGS'||
                                        selectedValue === 'EVALUACIÓN POR MEDICINA DEL TRABAJO') {            
                                        $('#notatecnicomedicoContainer').show();
                                        $('#fechanotatecnicomedicoContainer').show();
                                        $('#matriculaContainer').show();
                                        $('#tablaadjuntosContainer').show();
                                    }
                                });
                            });
                        </script>
                        
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="form-group col-lg-6" id="notatecnicomedicoContainer" style="display: none;">
                                    {!! Form::label('notatecnicomedico', 'Nota:') !!}
                                    {!! Form::text('notatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <div class="form-group col-lg-6" id="fechanotatecnicomedicoContainer" style="display: none;">
                                    {!! Form::label('fechanotatecnicomedico', 'Fecha de Nota:') !!}
                                    {!! Form::date('fechanotatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                </div> 
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="form-group col-lg-6" id="matriculaContainer" style="display: none;">
                                    {!! Form::label('matricula', 'Matricula:') !!}
                                    {!! Form::text('matricula', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                </div>
                                <div class="form-group col-lg-6" id="fechainformeestudioContainer" style="display: none;">
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
                    <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">GENERAR ADJUNTO Y RESPUESTA</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- DICTAMEN -->
<div class="modal fade" id="modalDictamen" tabindex="-1" role="dialog" aria-labelledby="modalDictamenLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalDictamenLabel">DICTAMEN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.tramites.guardartramitesclienteita', $cliente) }}" method="POST" enctype="multipart/form-data">
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
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
            </script>
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
                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
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
                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
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
                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                    {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nivel procedimiento</th>
                                <th>Sub procedimiento</th>
                                <th>Documento</th>
                                <th>Comunicar</th>
                                <th>Captura</th>
                                @if ($procedimientotramites->whereNull('capturacomunicacion')->count() > 0)
                                    <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($procedimientotramites as $procedimientotramite)
                                <tr>
                                    <td>{{ $procedimientotramite->nivelprocedimiento }}</td>
                                    <td>{{ $procedimientotramite->subprocedimiento }}</td>
                                    <td>
                                        <abbr title="Ver documento">
                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$procedimientotramite->document}") }}" class="btn btn-verdocumento fas fa-eye" target="_blank" ></a>
                                        </abbr>
                                    </td>
                                    <td>
                                        @if ($procedimientotramite->estadocomunicado !== 'COMUNICADO')
                                            <a href="{{ route('tramites.actualizarEstado', ['id' => $procedimientotramite->id, 'clienteId' => $cliente->id]) }}" class="btn btn-comunicar" target="_blank" style="width: 130px;">COMUNICAR</a>
                                        @else
                                            <button class="btn btn-whatsapp" style="width: 120px; background-color: #ccc; color: #666;" disabled>COMUNICADO</button>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($procedimientotramite->capturacomunicacion)
                                            <abbr title="Ver captura">
                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$procedimientotramite->capturacomunicacion}") }}" class="btn btn-verdocumento fas fa-eye" target="_blank"></a>
                                            </abbr>
                                            @else
                                            <form action="{{ route('tramites.subirArchivo', ['id' => $procedimientotramite->id, 'clienteId' => $cliente->id]) }}" method="POST" enctype="multipart/form-data" class="d-inline">
                                                @csrf
                                                <div class="input-group">
                                                    <input type="file" name="documento" class="dropify esdropify" accept=".jpg" required>      
                                    </td>
                                    <td>
                                                <div class="input-group-append">
                                                    <abbr title="Subir captura">
                                                    <button type="submit" class="btn btn-subircaptura fas fa-share-square"></button>
                                                    </abbr>
                                                </div>
                                            </div>
                                            <style>
                                                .btn-subircaptura {
                                                    background-color:  #ffffff;
                                                    color: #94c93b;
                                                    border-color: #94c93b;
                                                    border-radius: 5px;
                                                    padding: 7px 10px;
                                                }
                                                .btn-subircaptura:hover {
                                                    background-color: #94c93b;
                                                    color: #ffffff;
                                                }
                                            </style>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionar todos los inputs de archivo y las etiquetas asociadas
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
    
    //CANCELAR FUNCION DE LA TECLA ENTER
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
@endsection

