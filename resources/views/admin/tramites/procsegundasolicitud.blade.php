@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.tramites.index') }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-cartareclamo" data-toggle="modal" data-target="#modalcartayreclamo">CARTA / RECLAMO</a>
<h5>PROCEDIMIENTO DE SEGUNDA SOLICITUD DE:</h5>
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
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="modal-body">
                    <button type="button" class="btn btn-primary btn-block mb-3 text-left" data-toggle="modal" data-target="#modalIngresoTramite">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-folder-plus fa-2x mr-3"></i>
                                <span class="h5 mb-0">Ingreso de Trámite</span>
                            </div>
                        </div>
                    </button>

                    @php
                        $documento1 = $cliente->tramites()->where('subprocedimiento', 'RECEPCION DE DOCUMENTACIÓN')->first();
                        $documento2 = $cliente->tramites()->where('subprocedimiento', 'INCLUSION DE PODER')->first();
                    @endphp
                    @if ($documento1 && $documento2)
                        <button type="button" class="btn btn-primary btn-block mb-3 text-left" data-toggle="modal" data-target="#modalNotificacionPoder">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope-open-text fa-2x mr-3"></i>
                                    <span class="h5 mb-0">Notificación del Poder</span>
                                </div>
                            </div>
                        </button>
                    @else
                        <button type="button" class="btn btn-primary btn-block mb-3 text-left" data-toggle="modal" data-target="#modalNotificacionPoder" disabled>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope-open-text fa-2x mr-3"></i>
                                    <span class="h5 mb-0">Notificación del Poder</span>
                                </div>
                            </div>
                        </button>
                    @endif

                    @php
                        $documento3 = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE PODER')->first();
                    @endphp
                    @if ($documento3)
                        @php
                            $fechaSubidaEAP = \Carbon\Carbon::parse($documento3->fechasubida);
                            $diasRestantesEAP = max(0, 10 - $fechaSubidaEAP->diffInDays(\Carbon\Carbon::now()));
                            $mensajeDias = $diasRestantesEAP == 1 ? '1 día para subir' : "$diasRestantesEAP días para subir";
                        @endphp
                        <button type="button" class="btn btn-primary btn-block mb-3 text-left" data-toggle="modal" data-target="#modalFirmaEAP">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-signature fa-2x mr-3"></i>
                                    <span class="h5 mb-0">Firma EAP</span>
                                </div>
                                @php
                                    $documento1 = $cliente->tramites()->where('subprocedimiento', 'RECEPCION DE DOCUMENTACIÓN')->first();
                                    if ($documento1) {
                                        $fechaSubida = \Carbon\Carbon::parse($documento1->fechasubida);
                                        $diasRestantes = max(0, 10 - $fechaSubida->diffInDays(\Carbon\Carbon::now()));
                                        echo "<span class='badge badge-secondary'>QUEDAN $diasRestantes DIA(S)</span>";
                                    }
                                @endphp
                            </div>
                        </button>
                    @else
                        <button type="button" class="btn btn-primary btn-block mb-3 text-left" data-toggle="modal" data-target="#modalFirmaEAP" disabled>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-signature fa-2x mr-3"></i>
                                    <span class="h5 mb-0">Firma EAP</span>
                                </div>
                                @php
                                $documento1 = $cliente->tramites()->where('subprocedimiento', 'RECEPCION DE DOCUMENTACIÓN')->first();
                                if ($documento1) {
                                    $fechaSubida = \Carbon\Carbon::parse($documento1->fechasubida);
                                    $diasRestantes = max(0, 10 - $fechaSubida->diffInDays(\Carbon\Carbon::now()));
                                    echo "<span class='badge badge-secondary'>QUEDAN $diasRestantes DIA(S)</span>";
                                }
                            @endphp
                            </div>
                        </button>
                    @endif
                    <button type="button" class="btn btn-primary btn-block mb-3 text-left" data-toggle="modal" data-target="#modalNotificaciones">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-paperclip fa-2x mr-3"></i>
                            <span class="h5 mb-0">Notificaciones y Adjuntos</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalcartayreclamo" tabindex="-1" role="dialog" aria-labelledby="modalcartayreclamoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalcartayreclamoLabel">CARTA / RECLAMO</h5>
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
                    @csrf
                    <div class="row">
                        <div class="form-group col-lg-12">
                            {!! Form::label('tipo_pdf', 'Selecciona el tipo de Carta / Reclamo:') !!}
                            {!! Form::select('tipo_pdf', [
                                'PRIMERA CARTA SIT' => 'PRIMERA CARTA SIT',
                                'SEGUNDA CARTA SIT' => 'SEGUNDA CARTA SIT',
                                'TERCERA CARTA SIT' => 'TERCERA CARTA SIT',
                                'PRIMERA CARTA DE RECLAMO' => 'PRIMERA CARTA DE RECLAMO',
                                'SEGUNDA CARTA DE RECLAMO' => 'SEGUNDA CARTA DE RECLAMO',
                                'TERCERA CARTA DE RECLAMO' => 'TERCERA CARTA DE RECLAMO',
                                'CARTA DE RECLAMO APS' => 'CARTA DE RECLAMO APS',
                               
                            ], null, ['class' => 'form-control', 'placeholder' => 'Selecciona una opción', 'id' => 'tipoPdfSelect']) !!}
                            @error('tipo_pdf')
                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group  col-lg-12">
                            {!! Form::label('apoderado', 'Apoderado:') !!}
                            {!! Form::select('apoderado', $personal->pluck('nombrecompleto', 'id'), null, ['class' => 'form-control', 'placeholder' => 'Selecciona una opción', 'id' => 'personalSelect']) !!}
                            @error('apoderado')
                                <small class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                            @enderror
                        </div>
                        <input type="text" class="form-control" id="tipocartareclamo" name="tipocartareclamo" value="MASA HEREDITARIA" hidden>
                        <input type="date" class="form-control" id="fechaactual" name="fechaactual" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                    </div>
                    <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">GENERAR CARTA / RECLAMO</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ingreso de Trámite -->
<div class="modal fade" id="modalIngresoTramite" tabindex="-1" role="dialog" aria-labelledby="modalIngresoTramiteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalIngresoTramiteLabel">INGRESO DE TRÁMITE</h5>
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
                    <div class="row">
                        <div class="form-group col-lg-6 text-center">
                            <label for="pdf1">RECEPCION DE DOCUMENTACIÓN</label>
                            @php
                                $documento1 = $cliente->tramites()->where('subprocedimiento', 'RECEPCION DE DOCUMENTACIÓN')->first();
                            @endphp
                            @if ($documento1)
                            <p class="text-center mb-3" style="margin-top: 30px;">Fecha de Subida: {{ $documento1->fechasubida }}</p>
                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento1->document}") }}" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Ver Documento</a>
                            @else
                                <div class="form-group">
                                    <input type="text" class="form-control" id="tramite1" name="tramite[]" value="MASA HEREDITARIA" hidden>
                                    <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="INGRESO DE TRAMITE" hidden>
                                    <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="RECEPCION DE DOCUMENTACIÓN" hidden>
                                    {!! Form::label('file1', 'Documento:', ['class' => 'd-block text-center']) !!}
                                    <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block">
                                </div>
                                <label for="fechasubida1" class="text-center">Fecha:</label>
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            @endif
                        </div>
                        <div class="form-group col-lg-6 text-center">
                            <label for="pdf2">INCLUSIÓN DE PODER</label>
                            @php
                                $documento2 = $cliente->tramites()->where('subprocedimiento', 'INCLUSION DE PODER')->first();
                            @endphp
                            @if ($documento2)
                                <p class="text-center mb-3" style="margin-top: 30px;">Fecha de Subida: {{ $documento2->fechasubida }}</p>
                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento2->document}") }}" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Ver Documento</a> <!-- Añadido 'btn-block' para ocupar todo el ancho -->
                            @else
                                <div class="form-group">
                                    <input type="text" class="form-control" id="tramite2" name="tramite[]" value="MASA HEREDITARIA" hidden>
                                    <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="INGRESO DE TRAMITE" hidden>
                                    <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="INCLUSION DE PODER" hidden>
                                    {!! Form::label('file2', 'Documento:', ['class' => 'd-block text-center']) !!} <!-- Añadido 'd-block' para que el label ocupe todo el ancho y 'text-center' para centrar -->
                                    <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block"> <!-- Añadido 'mx-auto' y 'd-block' para centrar -->
                                </div>
                                <label for="fechasubida2" class="text-center">Fecha:</label> <!-- Añadido 'text-center' para centrar -->
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            @endif
                        </div>
                    </div>
                    @php
                        $documento1 = $cliente->tramites()->where('subprocedimiento', 'RECEPCION DE DOCUMENTACIÓN')->first();
                        $documento2 = $cliente->tramites()->where('subprocedimiento', 'INCLUSION DE PODER')->first();
                    @endphp
                    @if (!$documento1 || !$documento2)
                        <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button> <!-- Añadido 'btn-block', 'mx-auto' y 'd-block' para centrar -->
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
                <h5 class="modal-title" id="modalNotificacionPoderLabel">NOTIFICACIÓN DEL PODER</h5>
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
                    <div class="row">
                        <div class="form-group col-lg-6 text-center">
                            <label for="pdf1">VALIDACIÓN DE PODER</label>
                            @php
                                $documento3 = $cliente->tramites()->where('subprocedimiento', 'VALIDACIÓN DE PODER')->first();
                            @endphp
                            @if ($documento3)
                            <p class="text-center mb-3" style="margin-top: 30px;">Fecha de Subida: {{ $documento3->fechasubida }}</p>
                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento3->document}") }}" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Ver Documento</a>
                            @else
                                <div class="form-group">
                                    <input type="text" class="form-control" id="tramite1" name="tramite[]" value="MASA HEREDITARIA" hidden>
                                    <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="NOTIFICACIÓN DEL PODER" hidden>
                                    <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="VALIDACIÓN DE PODER" hidden>
                                    {!! Form::label('file1', 'Documento:', ['class' => 'd-block text-center']) !!}
                                    <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block">
                                </div>
                                <label for="fechasubida1" class="text-center">Fecha:</label>
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            @endif
                        </div>
                        <div class="form-group col-lg-6 text-center">
                            <label for="pdf2">RECHAZO DE PODER</label>
                            @php
                                $documento4 = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE PODER')->first();
                            @endphp
                            @if ($documento4)
                            <p class="text-center mb-3" style="margin-top: 30px;">Fecha de Subida: {{ $documento4->fechasubida }}</p>
                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento4->document}") }}" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Ver Documento</a> <!-- Añadido 'btn-block' para ocupar todo el ancho -->
                            @else
                                <div class="form-group">
                                    <input type="text" class="form-control" id="tramite2" name="tramite[]" value="MASA HEREDITARIA" hidden>
                                    <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="NOTIFICACIÓN DEL PODER" hidden>
                                    <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="RECHAZO DE PODER" hidden>
                                    {!! Form::label('file2', 'Documento:', ['class' => 'd-block text-center']) !!} <!-- Añadido 'd-block' para que el label ocupe todo el ancho y 'text-center' para centrar -->
                                    <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block"> <!-- Añadido 'mx-auto' y 'd-block' para centrar -->
                                </div>
                                <label for="fechasubida2" class="text-center">Fecha:</label> <!-- Añadido 'text-center' para centrar -->
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            @endif
                        </div>
                    </div>
                    @php
                        $documento3 = $cliente->tramites()->where('subprocedimiento', 'VALIDACION DE PODER')->first();
                        $documento4 = $cliente->tramites()->where('subprocedimiento', 'RECHAZO DE PODER')->first();
                    @endphp
                    @if (!$documento3 || !$documento4)
                        <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button> <!-- Añadido 'btn-block', 'mx-auto' y 'd-block' para centrar -->
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
                <h5 class="modal-title" id="modalFirmaEAPLabel">FIRMA EAP</h5>
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
                    <div class="row">
                        <div class="form-group col-lg-12 text-center">
                            <label for="pdf1">ESTADO DE AHORRO PREVISIONAL</label>
                            @php
                                $documento5 = $cliente->tramites()->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                            @endphp
                            @if ($documento5)
                            <p class="text-center mb-3" style="margin-top: 30px;">Fecha de Subida: {{ $documento5->fechasubida }}</p>
                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento5->document}") }}" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Ver Documento</a>
                            @else
                                <div class="form-group">
                                    <input type="text" class="form-control" id="tramite1" name="tramite[]" value="MASA HEREDITARIA" hidden>
                                    <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="FIRMA EAP" hidden>
                                    <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="ESTADO DE AHORRO PREVISIONAL" hidden>
                                    {!! Form::label('file1', 'Documento:', ['class' => 'd-block text-center']) !!}
                                    <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block">
                                </div>
                                <label for="fechasubida1" class="text-center">Fecha:</label>
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            @endif
                        </div>
                    </div>
                    @php
                        $documento5 = $cliente->tramites()->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                    @endphp
                    @if (!$documento5)
                        <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button> <!-- Añadido 'btn-block', 'mx-auto' y 'd-block' para centrar -->
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Notificaciones -->
<div class="modal fade" id="modalNotificaciones" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNotificacionesLabel">NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS</h5>
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
                    <div class="row">
                        <div class="form-group col-lg-3 text-center">
                            <label for="pdf1">RECOJO DE NOTIFICACIÓN DE GESTORA (Declaración  de herederos)</label>
                            @php
                                $documento6 = $cliente->tramites()->where('subprocedimiento', 'RECOJO DE NOTIFICACIÓN DE GESTORA')->first();
                            @endphp
                            @if ($documento6)
                            <p class="text-center mb-3" style="margin-top: 30px;">Fecha de Subida: {{ $documento6->fechasubida }}</p>
                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento6->document}") }}" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Ver Documento</a>
                            @else
                                <div class="form-group">
                                    <input type="text" class="form-control" id="tramite1" name="tramite[]" value="MASA HEREDITARIA" hidden>
                                    <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS" hidden>
                                    <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="RECOJO DE NOTIFICACIÓN DE GESTORA" hidden>
                                    {!! Form::label('file1', 'Documento:', ['class' => 'd-block text-center']) !!}
                                    <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block">
                                </div>
                                <label for="fechasubida1" class="text-center">Fecha:</label>
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            @endif
                        </div>
                        <div class="form-group col-lg-3 text-center">
                            <label for="pdf2">SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO</label>
                            @php
                                $documento7 = $cliente->tramites()->where('subprocedimiento', 'SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO')->first();
                            @endphp
                            @if ($documento7)
                            <p class="text-center mb-3" style="margin-top: 30px;">Fecha de Subida: {{ $documento7->fechasubida }}</p>
                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento7->document}") }}" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Ver Documento</a> <!-- Añadido 'btn-block' para ocupar todo el ancho -->
                            @else
                                <div class="form-group">
                                    <input type="text" class="form-control" id="tramite2" name="tramite[]" value="MASA HEREDITARIA" hidden>
                                    <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS" hidden>
                                    <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO" hidden>
                                    {!! Form::label('file2', 'Documento:', ['class' => 'd-block text-center']) !!} <!-- Añadido 'd-block' para que el label ocupe todo el ancho y 'text-center' para centrar -->
                                    <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block"> <!-- Añadido 'mx-auto' y 'd-block' para centrar -->
                                </div>
                                <label for="fechasubida2" class="text-center">Fecha:</label> <!-- Añadido 'text-center' para centrar -->
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            @endif
                        </div>
                        <div class="form-group col-lg-3 text-center">
                            <label for="pdf1">RESPUESTA A NOTA DE GESTORA</label>
                            @php
                                $documento8 = $cliente->tramites()->where('subprocedimiento', 'RESPUESTA A NOTA DE GESTORA')->first();
                            @endphp
                            @if ($documento8)
                            <p class="text-center mb-3" style="margin-top: 30px;">Fecha de Subida: {{ $documento8->fechasubida }}</p>
                            <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento8->document}") }}" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Ver Documento</a>
                            @else
                                <div class="form-group">
                                    <input type="text" class="form-control" id="tramite1" name="tramite[]" value="MASA HEREDITARIA" hidden>
                                    <input type="text" class="form-control" id="nivelprocedimiento1" name="nivelprocedimiento[]" value="NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS" hidden>
                                    <input type="text" class="form-control" id="subprocedimiento1" name="subprocedimiento[]" value="RESPUESTA A NOTA DE GESTORA" hidden>
                                    {!! Form::label('file1', 'Documento:', ['class' => 'd-block text-center']) !!}
                                    <input type="file" name="archivo[]" id="archivo1" class="dropify mx-auto d-block">
                                </div>
                                <label for="fechasubida1" class="text-center">Fecha:</label>
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            @endif
                        </div>
                        <div class="form-group col-lg-3 text-center">
                            <label for="pdf2">ADJUNTO DE DECLARACIÓN DE HEREDEROS</label>
                            @php
                                $documento9 = $cliente->tramites()->where('subprocedimiento', 'ADJUNTO DE DECLARACIÓN DE HEREDEROS')->first();
                            @endphp
                            @if ($documento9)
                            <p class="text-center mb-3" style="margin-top: 30px;">Fecha de Subida: {{ $documento9->fechasubida }}</p>
                                <a href="{{ url("/tramitesclientesita/{$cliente->id}/{$documento9->document}") }}" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Ver Documento</a> <!-- Añadido 'btn-block' para ocupar todo el ancho -->
                            @else
                                <div class="form-group">
                                    <input type="text" class="form-control" id="tramite2" name="tramite[]" value="MASA HEREDITARIA" hidden>
                                    <input type="text" class="form-control" id="nivelprocedimiento2" name="nivelprocedimiento[]" value="NOTIFICACIONES, SOLICITUDES, ADJUNTOS Y RESPUESTAS" hidden>
                                    <input type="text" class="form-control" id="subprocedimiento2" name="subprocedimiento[]" value="ADJUNTO DE DECLARACIÓN DE HEREDEROS" hidden>
                                    {!! Form::label('file2', 'Documento:', ['class' => 'd-block text-center']) !!} <!-- Añadido 'd-block' para que el label ocupe todo el ancho y 'text-center' para centrar -->
                                    <input type="file" name="archivo[]" id="archivo2" class="dropify mx-auto d-block"> <!-- Añadido 'mx-auto' y 'd-block' para centrar -->
                                </div>
                                <label for="fechasubida2" class="text-center">Fecha:</label> <!-- Añadido 'text-center' para centrar -->
                                <input type="date" class="form-control" id="fechasubida2" name="fechasubida[]" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            @endif
                        </div>
                        
                    </div>
                    @php
                        $documento6 = $cliente->tramites()->where('subprocedimiento', 'RECOJO DE NOTIFICACIÓN DE GESTORA')->first();
                        $documento7 = $cliente->tramites()->where('subprocedimiento', 'SOLICITUD DE CERTIFICADO DE OBITO LEGALIZADO')->first();
                        $documento8 = $cliente->tramites()->where('subprocedimiento', 'RESPUESTA A NOTA DE GESTORA')->first();
                        $documento9 = $cliente->tramites()->where('subprocedimiento', 'ADJUNTO DE DECLARACIÓN DE HEREDEROS')->first();
                    @endphp
                    @if (!$documento6 || !$documento7 || !$documento8 || !$documento9)
                        <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank" style="width: fit-content;">Subir Archivos</button> <!-- Añadido 'btn-block', 'mx-auto' y 'd-block' para centrar -->
                    @endif
                </form>
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
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .dropify-wrapper {
        height: 125px !important;
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
</style>
@stop