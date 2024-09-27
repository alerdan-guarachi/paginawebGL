@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.tramites.index') }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-seguimiento" data-toggle="modal" data-target="#modalseguimientoproceso">SEGUIMIENTO</a>
<a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalcartayreclamo">CARTA / RECLAMO</a>
<a class="btn btn-sm float-right btn-comunicaciones" data-toggle="modal" data-target="#modalcomunicaciones">COMUNICACIONES</a>
<h5>PROCEDIMIENTO DE TRÁMITE DE JUBILACIÓN DE:</h5>
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

<div class="card">
    {{-- NIVELES DE PROCEDIMIENTO --}}
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">INICIO DE TRÁMITE</a>
            </li>
            @php
                $documento5 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->where('estadocomunicado', 'COMUNICADO')->first();
            @endphp
            @if (!$documento5)
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
            {{-- 1.- INICIO DE TRÁMITE --}}
            <br><br>
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
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
                            $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                            $documento2 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'INCLUSIÓN DE PODER')->first();
                        @endphp
                        <div class="text-center">
                            @if (!$documento1 || !$documento2)
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

                    {{-- NOTIFICACION DEL PODER --}}
                    @php
                        $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECEPCION DE TRÁMITE')->where('estadocomunicado', 'COMUNICADO')->first();
                        $documento2 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'INCLUSION DE PODER')->where('estadocomunicado', 'COMUNICADO')->first();
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
                            $documento3 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'VALIDACIÓN DE PODER')->first();
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
                        $documento3 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'VALIDACIÓN DE PODER')->where('estadocomunicado', 'COMUNICADO')->first();
                    @endphp
                    <div class="col-12 col-md-4 mb-3">
                        <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalFirmaEAP" @if (!$documento3) disabled @endif>
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-signature fa-5x mb-2"></i>
                                <span class="h6 mb-0">FIRMA EAP</span>
                                @php
                                    $documento5 = $cliente->tramites()->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                                @endphp
                                @if (!$documento5)
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
                            $documento5 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                        @endphp
                        <div class="text-center">
                            @if (!$documento5)
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
            </div>


            {{-- 2.- PROCESO EN CURSO --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="row">

                    <div class="col-12 col-md-12 mb-3">
                        <button class="btn btn-custom btn-block text-center" type="button" data-toggle="modal" data-target="#modalNotificaciones">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-paperclip fa-5x mb-2"></i>
                                <span class="h6 mb-0 btn-block text-center">NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS</span>
                            </div>
                        </button>
                        <br>
                        @php
                            $documento6 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->first();
                            $documento7 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO')->first();
                            $documento8 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RESPUESTA A NOTA DE GESTORA')->first();
                            $documento9 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'ADJUNTO DE DECLARACIÓN DE HEREDEROS')->first();
                        @endphp
                        <div class="text-center">
                            @if (!$documento6 || !$documento7 || !$documento8 || !$documento9)
                                <span class="mb-0 checkamarillo">
                                    <i class="fas fa-exclamation-triangle"></i> INCOMPLETO
                                </span>
                            @else
                                <span class="mb-0 checkverde">
                                    <i class="fas fa-check-circle"></i> COMPLETO
                                </span>
                            @endif
                        </div>

                        {{-- NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS --}}
                        <div class="modal fade" id="modalNotificaciones" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionesLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title titulomodal" id="modalNotificacionesLabel">NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS</h5>
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
                                                @php
                                                    $documento6 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->first();
                                                @endphp
                                                <div class="row mb-3 align-items-center {{ !$documento6 ? 'no-documento' : '' }}">
                                                    <div class="col-md-4 text-center">
                                                        <p class="mb-0">NOTIFICACIÓN DE GESTORA</p>
                                                        @if (!$documento6)
                                                        <input type="text" class="form-control" id="tramite1" name="tramite[]" value="JUBILACIÓN" hidden>
                                                        <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS" hidden>
                                                        <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="NOTIFICACIÓN DE GESTORA" hidden>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        @php
                                                            $documento6 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS')->where('subprocedimiento', 'NOTIFICACIÓN DE GESTORA')->first();
                                                        @endphp
                                                        @if ($documento6)
                                                            <p class="mb-0">{{ $documento6->fechasubida }}</p>
                                                        @else
                                                            <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        @if ($documento6)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento6->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                        @else
                                                            <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                                        @endif
                                                    </div>
                                                </div>

                                                @php
                                                    $documento7 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO')->first();
                                                @endphp
                                                <div class="row mb-3 align-items-center {{ !$documento7 ? 'no-documento' : '' }}">
                                                    <div class="col-md-4 text-center">
                                                        <p class="mb-0">SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO</p>
                                                        @if (!$documento7)
                                                        <input type="text" class="form-control" id="tramite1" name="tramite[]" value="JUBILACIÓN" hidden>
                                                        <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS" hidden>
                                                        <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO" hidden>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        @php
                                                            $documento7 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS')->where('subprocedimiento', 'SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO')->first();
                                                        @endphp
                                                        @if ($documento7)
                                                            <p class="mb-0">{{ $documento7->fechasubida }}</p>
                                                        @else
                                                            <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        @if ($documento7)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento7->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                        @else
                                                            <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                                        @endif
                                                    </div>
                                                </div>

                                                @php
                                                    $documento8 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RESPUESTA A NOTA DE GESTORA')->first();
                                                @endphp
                                                <div class="row mb-3 align-items-center {{ !$documento8 ? 'no-documento' : '' }}">
                                                    <div class="col-md-4 text-center">
                                                        <p class="mb-0">RESPUESTA A NOTA DE GESTORA</p>
                                                        @if (!$documento8)
                                                        <input type="text" class="form-control" id="tramite1" name="tramite[]" value="JUBILACIÓN" hidden>
                                                        <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS" hidden>
                                                        <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="RESPUESTA A NOTA DE GESTORA" hidden>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        @php
                                                            $documento8 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS')->where('subprocedimiento', 'RESPUESTA A NOTA DE GESTORA')->first();
                                                        @endphp
                                                        @if ($documento8)
                                                            <p class="mb-0">{{ $documento8->fechasubida }}</p>
                                                        @else
                                                            <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        @if ($documento8)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento8->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                        @else
                                                            <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                                        @endif
                                                    </div>
                                                </div>

                                                @php
                                                    $documento9 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'ADJUNTO DE DECLARACIÓN DE HEREDEROS')->first();
                                                @endphp
                                                <div class="row mb-3 align-items-center {{ !$documento9 ? 'no-documento' : '' }}">
                                                    <div class="col-md-4 text-center">
                                                        <p class="mb-0">ADJUNTO DE DECLARACIÓN DE HEREDEROS</p>
                                                        @if (!$documento9)
                                                        <input type="text" class="form-control" id="tramite1" name="tramite[]" value="JUBILACIÓN" hidden>
                                                        <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS" hidden>
                                                        <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="ADJUNTO DE DECLARACIÓN DE HEREDEROS" hidden>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        @php
                                                            $documento9 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS')->where('subprocedimiento', 'ADJUNTO DE DECLARACIÓN DE HEREDEROS')->first();
                                                        @endphp
                                                        @if ($documento9)
                                                            <p class="mb-0">{{ $documento9->fechasubida }}</p>
                                                        @else
                                                            <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        @if ($documento9)
                                                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento9->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                                        @else
                                                            <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $documento6 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECOJO DE NOTIFICACIÓN DE GESTORA')->first();
                                                $documento7 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO')->first();
                                                $documento8 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RESPUESTA A NOTA DE GESTORA')->first();
                                                $documento9 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'ADJUNTO DE DECLARACIÓN DE HEREDEROS')->first();
                                            @endphp
                                            @if (!$documento6 || !$documento7 || !$documento8 || !$documento9)
                                                <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                         
            {{-- 3.- RESULTADO DEL PROCESO --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="row">
                    <div class="col-12 col-md-12 mb-3">
                        <button type="button" class="btn btn-custom btn-block text-center" data-toggle="modal" data-target="#modalContrato">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-balance-scale fa-5x mb-2"></i>
                                <span class="h6 mb-0 btn-block text-center">CONTRATO</span>
                            </div>
                        </button>
                        <br>
                        @php
                            $documento11 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'FIRMA DE CONTRATO')->first();
                            $documento13 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->first();
                        @endphp
                        <div class="text-center">
                            @if (!$documento11 && !$documento13)
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
            </div>
        </div>
    </div>
</div>


<!-- Ingreso de Trámite -->
<div class="modal fade" id="modalIngresoTramite" tabindex="-1" role="dialog" aria-labelledby="modalIngresoTramiteLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
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
                        </div><br>

                        @php
                            $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento1 ? 'no-documento' : '' }}">
                            <div class="col-md-4 text-center">
                                <p class="mb-0">RECEPCIÓN DE TRÁMITE</p>
                                @if (!$documento1)
                                <input type="text" class="form-control" id="tramite1" name="tramite[]" value="JUBILACIÓN" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="INGRESO DE TRÁMITE" hidden>
                                <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="RECEPCIÓN DE TRÁMITE" hidden>
                                @endif
                            </div>
                            <div class="col-md-4 text-center">
                                @php
                                    $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
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
                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center {{ !$documento2 ? 'no-documento' : '' }}">
                            <div class="col-md-4 text-center">
                                <p class="mb-0">INCLUSIÓN DE PODER</p>
                                <input type="text" class="form-control" id="tramite2" name="tramite[]" value="JUBILACIÓN" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="INGRESO DE TRÁMITE" hidden>
                                <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="INCLUSIÓN DE PODER" hidden>
                            </div>
                            <div class="col-md-4 text-center">
                                @php
                                    $documento2 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'INCLUSIÓN DE PODER')->first();
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
                                <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>
                    </div>
                    @php
                        $documento1 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                        $documento2 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'INCLUSIÓN DE PODER')->first();
                    @endphp
                    @if (!$documento1 || !$documento2)
                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mt-3" style="width: 200px;">SUBIR ARCHIVOS</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Notificación del Poder -->
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
                        $documento3 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'VALIDACIÓN DE PODER')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento3 ? 'no-documento' : '' }}">
                            <div class="col-md-4 text-center">
                                <p class="mb-0">VALIDACIÓN DE PODER</p>
                                <input type="text" class="form-control" id="tramite1" name="tramite[]" value="JUBILACIÓN" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER" hidden>
                                <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="VALIDACIÓN DE PODER" hidden>
                            </div>
                            <div class="col-md-4 text-center">
                                @if ($documento3)
                                    <p class="mb-0">{{ $documento3->fechasubida }}</p>
                                @else
                                    <input type="date" class="form-control" id="fechasubida1" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                @endif
                            </div>
                            <div class="col-md-4 text-center">
                                @if ($documento3)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento3->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                    <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>

                        @php
                        $documento4 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECHAZO DE PODER')->first();
                        @endphp
                        @if (!$documento3 || $documento4 && (!$documento4 || $documento4))
                        <div class="row mb-3 align-items-center {{ !$documento4 ? 'no-documento' : '' }}">
                            <div class="col-md-4 text-center">
                                <p class="mb-0">RECHAZO DE PODER</p>
                                <input type="text" class="form-control" id="tramite2" name="tramite[]" value="JUBILACIÓN" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="NOTIFICACIÓN DE PODER" hidden>
                                <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="RECHAZO DE PODER" hidden>
                            </div>
                            <div class="col-md-4 text-center">
                                @if ($documento4)
                                    <p class="mb-0">{{ $documento4->fechasubida }}</p>
                                @else
                                    <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                @endif
                            </div>
                            <div class="col-md-4 text-center">
                                @if ($documento4)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento4->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                    <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @php
                        $documento3 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'VALIDACION DE PODER')->first();
                        $documento4 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'RECHAZO DE PODER')->first();
                    @endphp
                    @if (!$documento3)
                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">SUBIR ARCHIVOS</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Firma EAP -->
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
                        <div class="row mb-3 align-items-center {{ !$documento5 ? 'no-documento' : '' }}">
                            <div class="col-md-4 text-center">
                                <p class="mb-0">ESTADO DE AHORRO PREVISIONAL</p>
                                <input type="text" class="form-control" id="tramite1" name="tramite[]" value="JUBILACIÓN" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="FIRMA EAP" hidden>
                                <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="ESTADO DE AHORRO PREVISIONAL" hidden>
                            </div>
                            <div class="col-md-4 text-center">
                                @php
                                    $documento5 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
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
                                <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>
                    </div>
                    @php
                        $documento5 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                    @endphp
                    @if (!$documento5)
                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">SUBIR ARCHIVOS</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>      

{{-- CARTAS Y RECLAMOS --}}
<div class="modal fade" id="modalcartayreclamo" tabindex="-1" role="dialog" aria-labelledby="modalcartayreclamoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
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

                        <div class="form-group col-lg-12">
                            {!! Form::label('tipo_pdf', 'Tipo de Carta / Reclamo:') !!}
                            {!! Form::select('tipo_pdf', [
                                'PRIMERA CARTA SIT' => 'PRIMERA CARTA SIT',
                                'SEGUNDA CARTA SIT' => 'SEGUNDA CARTA SIT',
                                'TERCERA CARTA SIT' => 'TERCERA CARTA SIT',
                                'PRIMERA CARTA DE RECLAMO' => 'PRIMERA CARTA DE RECLAMO',
                                'SEGUNDA CARTA DE RECLAMO' => 'SEGUNDA CARTA DE RECLAMO',
                                'TERCERA CARTA DE RECLAMO' => 'TERCERA CARTA DE RECLAMO',
                                'CARTA DE RECLAMO APS' => 'CARTA DE RECLAMO APS',
                            ], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoPdfSelect', 'required' => 'required']) !!}
                            @error('tipo_pdf')
                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                            @enderror
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const selectElement = document.getElementById('tipoPdfSelect');
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
                                const primeracartasit = tipoPdfSelect.querySelector('option[value="PRIMERA CARTA SIT"]');
                                const segundacartasit = tipoPdfSelect.querySelector('option[value="SEGUNDA CARTA SIT"]');
                                const terceracartasit = tipoPdfSelect.querySelector('option[value="TERCERA CARTA SIT"]');
                                const primeracartareclamo = tipoPdfSelect.querySelector('option[value="PRIMERA CARTA DE RECLAMO"]');
                                const segundacartareclamo = tipoPdfSelect.querySelector('option[value="SEGUNDA CARTA DE RECLAMO"]');
                                const terceracartareclamo = tipoPdfSelect.querySelector('option[value="TERCERA CARTA DE RECLAMO"]');
                                const cartareclamoaps = tipoPdfSelect.querySelector('option[value="CARTA DE RECLAMO APS"]');

                                @if (!$fechaingresoyeap)
                                    primeracartasit.disabled = true;
                                @endif
                                @if (!$primeracartasit)
                                    segundacartasit.disabled = true;
                                @endif
                                @if (!$segundacartasit)
                                    terceracartasit.disabled = true;
                                @endif
                                @if (!$terceracartasit)
                                    primeracartareclamo.disabled = true;
                                @endif
                                @if (!$primeracartareclamo)
                                    segundacartareclamo.disabled = true;
                                @endif
                                @if (!$segundacartareclamo)
                                    terceracartareclamo.disabled = true;
                                @endif
                                @if (!$terceracartareclamo)
                                    cartareclamoaps.disabled = true;
                                @endif
                            });
                        </script>
                        <style>
                            .form-control option.registered {
                                color:#098c12;
                                font-weight: 900;
                            }
                            .form-control option.registered[disabled] {
                                color: #098c12;
                                cursor: not-allowed;
                                font-weight: 900;
                            }
                            .form-control option.not-registered {
                                color: #ccc017;
                                font-weight: 900;
                            }
                            .form-control option[disabled] {
                                color: #d9d9d9;
                                cursor: not-allowed;
                                font-weight: 900;
                            }
                        </style>

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
                                const tipoPdfSelect = document.getElementById('tipoPdfSelect');
                                const folioContainer = document.getElementById('folioContainer');
                                
                                tipoPdfSelect.addEventListener('change', function () {
                                    if (tipoPdfSelect.value === 'CARTA DE RECLAMO APS') {
                                        folioContainer.style.display = 'block';
                                    } else {
                                        folioContainer.style.display = 'none';
                                    }
                                });
                        
                                // Trigger change event on page load to set initial state
                                tipoPdfSelect.dispatchEvent(new Event('change'));
                            });
                        </script>
                        
                        <input type="text" class="form-control" id="tipocartareclamo" name="tipocartareclamo" value="JUBILACIÓN" hidden>
                        <input type="date" class="form-control" id="fechaactual" name="fechaactual" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                    </div>
                    <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">GENERAR CARTA / RECLAMO</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CONTRATO -->
<div class="modal fade" id="modalContrato" tabindex="-1" role="dialog" aria-labelledby="modalContratoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title titulomodal" id="modalContratoLabel">CONTRATO</h5>
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
                        @if (!$documento13)
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
                        </div><br>
                        @php
                            $documento11 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'CONTRATO')->where('subprocedimiento', 'FIRMA DE CONTRATO')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento11 ? 'no-documento' : '' }}" style="margin-top: -10px;">
                            <div class="col-md-3 text-center">
                                <p class="mb-0">FIRMA DE CONTRATO</p>
                                @if (!$documento11)
                                <input type="text" class="form-control" id="tramite2" name="tramite[]" value="JUBILACIÓN" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="CONTRATO" hidden>
                                <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="FIRMA DE CONTRATO" hidden>
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @php
                                    $documento11 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'FIRMA DE CONTRATO')->first();
                                @endphp
                                @if ($documento11)
                                    <p class="mb-0">{{ $documento11->fechasubida }}</p>
                                @else
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @php
                                    $documento11 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'FIRMA DE CONTRATO')->first();
                                @endphp
                                @if ($documento11)
                                    <p class="mb-0">{{ $documento11->fechacobrocontrato }}</p>
                                @else
                                <input type="date" class="form-control" id="fechacobrocontrato1" name="fechacobrocontrato[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-2 text-center">
                                @php
                                    $documento11 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'FIRMA DE CONTRATO')->first();
                                @endphp
                                @if ($documento11)
                                    <p class="mb-0">{{ $documento11->montocontrato }}</p>
                                @else
                                <input type="number" class="form-control" id="montocontrato1" name="montocontrato[]">
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @if ($documento11)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento11->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>
                        @endif
                        @if (!$documento11)
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
                            $documento13 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->first();
                        @endphp
                        <div class="row mb-3 align-items-center {{ !$documento13 ? 'no-documento' : '' }}">
                            <div class="col-md-4 text-center">
                                <p class="mb-0">NOTA DE RECHAZO DE TRÁMITE</p>
                                @if (!$documento13)
                                <input type="text" class="form-control" id="tramite3" name="tramite[]" value="JUBILACIÓN" hidden>
                                <input type="text" class="form-control" id="nivelprocedimiento3" name="nivelprocedimiento[]" value="CONTRATO" hidden>
                                <input type="text" class="form-control" id="subprocedimiento3" name="subprocedimiento[]" value="NOTA DE RECHAZO DE TRÁMITE" hidden>
                                @endif
                            </div>
                            <div class="col-md-4 text-center">
                                @php
                                    $documento13 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->first();
                                @endphp
                                @if ($documento13)
                                    <p class="mb-0">{{ $documento13->fechasubida }}</p>
                                @else
                                <input type="date" class="form-control" id="fechasubida3" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                                @endif
                            </div>
                            <div class="col-md-4 text-center">
                                @if ($documento13)
                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento13->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">Ver Doc.</a>
                                @else
                                <input type="file" name="archivo[]" id="archivo3" class="dropify mx-auto d-block" accept=".pdf">
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @php
                        $documento11 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'FIRMA DE CONTRATO')->first();
                        $documento13 = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('subprocedimiento', 'NOTA DE RECHAZO DE TRÁMITE')->first();
                    @endphp
                    @if (!$documento11 && !$documento13)
                        <button type="submit" class="btn btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button>
                    @endif
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
                            $seguimientos = $cliente->tramites()->where('tramite', 'JUBILACIÓN')->where('nivelprocedimiento', 'SEGUIMIENTO')->get();
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
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nivel procedimiento</th>
                                <th>Sub procedimiento</th>
                                <th>Documento</th>
                                <th>Comunicar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($procedimientotramites as $procedimientotramite)
                                <tr>
                                    <td>{{ $procedimientotramite->nivelprocedimiento }}</td>
                                    <td>{{ $procedimientotramite->subprocedimiento }}</td>
                                    <td>
                                        <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$procedimientotramite->document}") }}" class="btn btn-verdocumento" target="_blank" style="width: 150px;">VER DOC.</a>
                                    </td>
                                    <td>
                                        @if ($procedimientotramite->estadocomunicado !== 'COMUNICADO')
                                            <a href="{{ route('tramites.actualizarEstado', ['id' => $procedimientotramite->id, 'clienteId' => $cliente->id]) }}" class="btn btn-comunicar" target="_blank">
                                                COMUNICAR
                                            </a>
                                        @else
                                            <button class="btn btn-whatsapp" style="width: 130px; background-color: #ccc; color: #666;" disabled>
                                                COMUNICADO
                                            </button>
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

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .dropdown-menu {
        width: 105%;
        box-sizing: border-box;
    }
    .dropdown-item {
        text-align: center;
        padding: 10px 15px;
    }
    .dropdown-item {
        display: block;
    }
    .nav-link.disabled {
        pointer-events: none;
        color: #6c757d;
        background-color: #e9ecef;
        border-color: #ddd;
        cursor: not-allowed;
    }
    .badge-orange {
        color: #ffffff;
        background-color: #f5a124;
    }
    .checkamarillo {
        color: black;
        border: 2px solid black;
        background-color: #ffe9a3;
        padding: 5px 8px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        font-size: 15px;
        font-weight: bold;
        margin: 0 auto;
        width: fit-content;
    }
    .checkrojo {
        color: black;
        border: 2px solid black;
        background-color: #ffc4c4;
        padding: 5px 8px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        font-size: 15px;
        font-weight: bold;
        margin: 0 auto;
        width: fit-content;
    }
    .checkverde {
        color: black;
        border: 2px solid black;
        background-color: #e8ffc1;
        padding: 5px 8px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        font-size: 15px;
        font-weight: bold;
        margin: 0 auto; 
        width: fit-content;
    }
    .checkamarillo i, .checkverde i {
        margin-right: 8px;
        font-size: 1em;
    }
    .titulos {
        background-color: #f0f0f0;
        padding: 10px 0;
    }
    .no-documento {
        background-color: #fff7eb;
        padding: 30px 0;
    }
    .file-label {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        position: relative;
    }
    .file-label:hover::after {
        content: attr(data-original-title);
        position: absolute;
        left: 0;
        top: 100%;
        white-space: normal;
        background: #fff;
        border: 1px solid #ccc;
        padding: 5px;
        z-index: 1000;
    }
    .fa-3x {
        font-size: 2em;
    }
    .h6 {
        font-size: 17px;
        font-weight: 900;
    }
    .badge-info {
        background-color: #17a2b8;
        color: #ffffff;
    }
    .nav-tabs {
        display: flex;
        justify-content: space-between;
    }
    .nav-tabs .nav-item {
        flex: 1;
    }
    .nav-tabs .nav-link {
        display: block;
        text-align: center;
        width: 100%;
        font-weight: bold;
        font-size: 20px;
        color: #faa625;
        background-color: #fef4e7;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
        font-size: 20px;
        color: #94c93b;
    }
    .dropify-wrapper {
        height: 40px !important;
    }
    .dropify-message p {
        font-size: 14px;
    }
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    .titulomodal {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 700;
        margin-bottom: 0%;
        font-size: 30px;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
        }
    h6 {
        font-weight: 900;
        }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;
        }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-verdocumento {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        font-weight: bold;
        padding: 5px 20px;
        }
    .btn-verdocumento:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-subirdocumento {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 20px;
        }
    .btn-subirdocumento:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-subirarchivos {
        background-color:  #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 20px;
        }
    .btn-subirarchivos:hover {
        background-color: #2926e2;
        color: #ffffff;
        }
    .mensaje-error {
        color: #e1172b;
        font-family: "Times New Roman";
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        font-size: 12.5px;
        font-weight: bold;
        display: inline-block;
        margin-left: -10px;
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
    .btn-cartareclamo {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 10px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-cartareclamo:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-seguimiento {
        background-color: #ffffff;
        color: #962fb0;
        border-color: #962fb0;
        border-radius: 5px;
        padding: 10px 10px;
        margin-right: 10px;
    }
    .btn-seguimiento:hover {
        background-color: #962fb0;
        color: #ffffff;
    }
    .btn-adjuntosrespuestas {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-adjuntosrespuestas:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-comunicar {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        font-weight: bold;
        padding: 5px 20px;
        }
    .btn-comunicar:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
        .btn-comunicaciones {
        background-color: #ffffff;
        color: #78afe7;
        border-color: #78afe7;
        border-radius: 5px;
        padding: 10px 10px;
        margin-left: 10px;
    }
    .btn-comunicaciones:hover {
        background-color: #78afe7;
        color: #ffffff;
    }
    .btn-custom {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s; 
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-custom:hover {
        background-color: #94c93b;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: scale(1.05);
    }
    .btn-custom:disabled {
        background-color: #d6d6d6;
        color: #a0a0a0;
        cursor: not-allowed;
    }
</style>
@stop