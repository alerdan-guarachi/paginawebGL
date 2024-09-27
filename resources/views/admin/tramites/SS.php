<button type="button" class="btn btn-primary btn-block mb-3 text-left" data-toggle="modal" data-target="#modalNotificaciones">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-paperclip fa-2x mr-3"></i>
                            <span class="h5 mb-0">Notificaciones y Adjuntos</span>
                        </div>
                    </button>

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