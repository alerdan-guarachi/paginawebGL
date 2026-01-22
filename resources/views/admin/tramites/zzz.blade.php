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
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/TERCERA SOLICITUD/COMUNICACIONES/{$procedimientotramite->capturacomunicacion}") }}"
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
                                                                    <input type="hidden" name="tramitenombre" value="TERCERA SOLICITUD">
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
                                                                    <a href="{{ url("/tramitesclientesita/{$cliente->id}/TERCERA SOLICITUD/COMUNICACIONES/{$seguimiento->capturacomunicacion}") }}"
                                                                        class="btn btn-sm btn-verdocumento"
                                                                        target="_blank">VER CAPTURA</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td>
                                                            <input type="hidden" name="comtramitenombre" value="TERCERA SOLICITUD">
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
                                                                @foreach ($apoderadosList as $apoderado)
                                                                    <option value="{{ $apoderado }}">
                                                                @endforeach
                                                                <option value="{{ $cliente->nombrecompleto }}">
                                                            </datalist>
                                                        </td>
                                                        <td>
                                                            <input list="lista_receptores" 
                                                                name="comusureceptor2" 
                                                                class="form-control form-control-sm" 
                                                                placeholder="Escriba o seleccione..." requerid>

                                                            <datalist id="lista_receptores">
                                                                @foreach ($contactos as $nombrecontacto)
                                                                    <option value="{{ $nombrecontacto }}">
                                                                @endforeach
                                                                <option value="{{ $cliente->nombrecompleto }}">
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
                <button type="submit"
                                                                        class="btn btn-sm btn-subircaptura"
                                                                        title="GUARDAR">
                                                                    <i class="fas fa-print"></i>
                                                                </button>