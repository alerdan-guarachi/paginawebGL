@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#requisitosModal">VER REQUISITOS</a>
<h5>SUBIR DOCUMENTACIÓN DE REQUISITOS DE TERCERA SOLICITUD DE:</h5>
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
            <div class="col-lg-12">
                {!! Form::model($cliente, ['route' => ['admin.asociados.guardardocrequisitostercerasolicitud', $cliente], 'method' => 'PUT', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                <div class="row">
                    <div class="col-lg-6" hidden>
                        <div class="form-group">
                            {!! Form::label('nombrecompleto', 'Nombre completo:') !!}
                            {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                            @error('nombrecompleto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if($poderPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('poder', 'PODER:') !!}
                        {!! Form::file('poder', ['class' => 'form-control-file dropify']) !!}
                        {!! Form::text('numeropoder', null, ['class' => 'form-control', 'placeholder' => 'NRO. DE PODER']) !!}
                    </div>
                    @endif
                    
                    @if($avcciPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('avcci', 'AVC/CARNET ASEGURADO:') !!}
                        {!! Form::file('avcci', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                
                    @if($cnacaseguradoPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('cnacasegurado', 'CERTIFICADO NACIMIENTO ASEGURADO:') !!}
                        {!! Form::file('cnacasegurado', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($ciaseguradoPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('ciasegurado', 'CARNET IDENTIDAD ASEGURADO:') !!}
                        {!! Form::file('ciasegurado', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($cmatrimonioPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('cmatrimonio', 'CERTIFICADO DE MATRIMONIO:') !!}
                        {!! Form::file('cmatrimonio', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($cnacconyugePendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('cnacconyuge', 'CERTIFICADO NACIMIENTO CONYUGE:') !!}
                        {!! Form::file('cnacconyuge', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($ciconyugePendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('ciconyuge', 'CARNET IDENTIDAD CONYUGE:') !!}
                        {!! Form::file('ciconyuge', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif

                    @if($cunionlibrePendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('cunionlibre', 'CERTIFICADO DE UNIÓN LIBRE:') !!}
                        {!! Form::file('cunionlibre', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($cnacimientounionlibrePendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('cnacimientounionlibre', 'CERTIFICADO DE NAC. DE UNIÓN LIBRE:') !!}
                        {!! Form::file('cnacimientounionlibre', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($ciunionlibrePendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('ciunionlibre', 'CARNET IDENTIDAD DE UNIÓN LIBRE:') !!}
                        {!! Form::file('ciunionlibre', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($cdivorcioPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('cdivorcio', 'CERTIFICADO DE DIVORCIO:') !!}
                        {!! Form::file('cdivorcio', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($cdefuncionPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('cdefuncion', 'CERTIFICADO DE DEFUNCIÓN:') !!}
                        {!! Form::file('cdefuncion', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif

                    @if($cnacjihosPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('cnacjihos', 'CERTIFICADO NACIMIENTO HIJOS < 25:') !!}
                        {!! Form::file('cnacjihos', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($cihijosPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('cihijos', 'CARNET IDENTIDAD HIJOS < 25:') !!}
                        {!! Form::file('cihijos', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($denfaccidentePendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('denfaccidente', 'DENUNCIA ENFERMEDAD ACCIDENTE:') !!}
                        {!! Form::file('denfaccidente', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($crodomicilioPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('crodomicilio', 'CROQUIS DE DOMICILIO:') !!}
                        {!! Form::file('crodomicilio', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($contratoPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('contrato', 'CONTRATO:') !!}
                        {!! Form::file('contrato', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($ctrabajoPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('ctrabajo', 'CERTIFICADO DE TRABAJO:') !!}
                        {!! Form::file('ctrabajo', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($boletapagoPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('boletapago', 'BOLETA DE PAGO:') !!}
                        {!! Form::file('boletapago', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($egestoraPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('egestora', 'EXTRACTO DE GESTORA:') !!}
                        {!! Form::file('egestora', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($actdatosPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('actdatos', 'ACTUALIZACIÓN DE DATOS:') !!}
                        {!! Form::file('actdatos', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($resolinvhijosPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('resolinvhijos', 'RESOLUCIÓN INVALIDEZ DE HIJOS < 25:') !!}
                        {!! Form::file('resolinvhijos', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($recordserviciosPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('recordservicios', 'RECORD SERVICIOS') !!}
                        {!! Form::file('recordservicios', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($dictamencalentencPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('dictamencalentenc', 'DICTAMEN CALIFICACION ENTIDAD ENCARGADA') !!}
                        {!! Form::file('dictamencalentenc', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($infomedicasaludPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('infomedicasalud', 'INFORMACION MEDICA') !!}
                        {!! Form::file('infomedicasalud', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($anteriordictamenPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('anteriordictamen', 'ANTERIOR DICTAMEN O RESOLUCION') !!}
                        {!! Form::file('anteriordictamen', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                    @if($poderciapoderadoPendiente)
                    <div class="form-group col-lg-3">
                        {!! Form::label('poderciapoderado', 'PODER Y CARNET IDENTIDAD APODERADO') !!}
                        {!! Form::file('poderciapoderado', ['class' => 'form-control-file dropify']) !!}
                    </div>
                    @endif
                </div>
                

                <div class="modal fade" id="requisitosModal" tabindex="-1" aria-labelledby="requisitosModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title" id="requisitosModalLabel">LISTA DE DOCUMENTOS DE REQUISITOS</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <thead>
                                        <tr> 
                                            <th>Requisito</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!is_null($requisito->poder))
                                            <tr>
                                                @if (is_null($requisito->numeropoder))
                                                <td>PODER:</td>
                                                @endif
                                                @if (!is_null($requisito->numeropoder))
                                                <td>PODER: {{ $requisito->numeropoder }}</td>
                                                @endif
                                                <td> 
                                                    @if ($poderSubido)
                                                        <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->poder}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                        <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisito->avcci))
                                        <tr>
                                            <td>AVC/CARNET ASEGURADO</td>
                                            <td>
                                                @if ($avcciSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->avcci}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->cnacasegurado))
                                        <tr>
                                            <td>CERTIFICADO NACIMIENTO ASEGURADO</td>
                                            <td>
                                                @if ($cnacaseguradoSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->cnacasegurado}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        
                                        @if (!is_null($requisito->ciasegurado))
                                        <tr>
                                            <td>CARNET IDENTIDAD ASEGURADO</td>
                                            <td>
                                                @if ($ciaseguradoSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->ciasegurado}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->cmatrimonio))
                                        <tr>
                                            <td>CERTIFICADO DE MATRIMONIO</td>
                                            <td>
                                                @if ($cmatrimonioSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->cmatrimonio}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->cnacconyuge))
                                        <tr>
                                            <td>CERTIFICADO NACIMIENTO CONYUGE</td>
                                            <td>
                                                @if ($cnacconyugeSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->cnacconyuge}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->ciconyuge))
                                        <tr>
                                            <td>CARNET IDENTIDAD CONYUGE</td>
                                            <td>
                                                @if ($ciconyugeSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->ciconyuge}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->cunionlibre))
                                        <tr>
                                            <td>CERTIFICADO DE UNIÓN LIBRE</td>
                                            <td>
                                                @if ($cunionlibreSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->cunionlibre}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->cnacimientounionlibre))
                                        <tr>
                                            <td>CERTIFICADO DE NACIMIENTO DE UNIÓN LIBRE</td>
                                            <td>
                                                @if ($cnacimientounionlibreSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->cnacimientounionlibre}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->ciunionlibre))
                                        <tr>
                                            <td>CARNET IDENTIDAD DE UNIÓN LIBRE</td>
                                            <td>
                                                @if ($ciunionlibreSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->ciunionlibre}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->cdivorcio))
                                        <tr>
                                            <td>CERTIFICADO DE DIVORCIO</td>
                                            <td>
                                                @if ($cdivorcioSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->cdivorcio}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        
                                        @if (!is_null($requisito->cdefuncion))
                                        <tr>
                                            <td>CERTIFICADO DE DEFUNCIÓN</td>
                                            <td>
                                                @if ($cdefuncionSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->cdefuncion}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->cnacjihos))
                                        <tr>
                                            <td>CERTIFICADO NACIMIENTO HIJOS &lt; 25</td>
                                            <td>
                                                @if ($cnacjihosSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->cnacjihos}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->cihijos))
                                        <tr>
                                            <td>CARNET IDENTIDAD HIJOS &lt; 25</td>
                                            <td>
                                                @if ($cihijosSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->cihijos}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->denfaccidente))
                                        <tr>
                                            <td>DENUNCIA ENFERMEDAD ACCIDENTE</td>
                                            <td>
                                                @if ($denfaccidenteSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->denfaccidente}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->crodomicilio))
                                        <tr>
                                            <td>CROQUIS DE DOMICILIO</td>
                                            <td>
                                                @if ($crodomicilioSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->crodomicilio}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->contrato))
                                        <tr>
                                            <td>CONTRATO</td>
                                            <td>
                                                @if ($contratoSubido)
                                                    @if ($userRole === 'MAESTRO' || $userRole === 'ADMINISTRADOR')
                                                        <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->contrato}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                        @else
                                                        <p class="verdoc2" disabled>
                                                            VER DOCUMENTO
                                                        </p>
                                                    @endif
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->recordservicios))
                                        <tr>
                                            <td>RECORD SERVICIOS</td>
                                            <td>
                                                @if ($recordserviciosSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->recordservicios}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif

                                        @if (!is_null($requisito->ctrabajo))
                                        <tr>
                                            <td>CERTIFICADO DE TRABAJO</td>
                                            <td>
                                                @if ($ctrabajoSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->ctrabajo}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if (!is_null($requisito->boletapago))
                                        <tr>
                                            <td>BOLETA DE PAGO</td>
                                            <td>
                                                @if ($boletapagoSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->boletapago}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if (!is_null($requisito->egestora))
                                        <tr>
                                            <td>EXTRACTO DE GESTORA</td>
                                            <td>
                                                @if ($egestoraSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->egestora}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if (!is_null($requisito->actdatos))
                                        <tr>
                                            <td>ACTUALIZACIÓN DE DATOS</td>
                                            <td>
                                                @if ($actdatosSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->actdatos}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if (!is_null($requisito->resolinvhijos))
                                        <tr>
                                            <td>RESOL. INVAL. HIJOS < 25</td>
                                            <td>
                                                @if ($resolinvhijosSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->resolinvhijos}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if (!is_null($requisito->dictamencalentenc))
                                        <tr>
                                            <td>DICTAMEN CALIFICACION ENTIDAD ENCARGADA</td>
                                            <td>
                                                @if ($dictamencalentencSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->dictamencalentenc}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if (!is_null($requisito->infomedicasalud))
                                        <tr>
                                            <td>INFORMACION MEDICA</td>
                                            <td>
                                                @if ($infomedicasaludSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->infomedicasalud}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if (!is_null($requisito->anteriordictamen))
                                        <tr>
                                            <td>ANTERIOR DICTAMEN O RESOLUCION</td>
                                            <td>
                                                @if ($anteriordictamenSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->anteriordictamen}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if (!is_null($requisito->poderciapoderado))
                                        <tr>
                                            <td>PODER Y CARNET IDENTIDAD APODERADO</td>
                                            <td>
                                                @if ($poderciapoderadoSubido)
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->poderciapoderado}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @else
                                                <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::submit('SUBIR DOCUMENTACION', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script>
    function cargarVistaPrevia() {
      var poder = document.getElementById('poder').files[0];
      if (poder) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var previewIframe = document.getElementById('document-preview');
          previewIframe.src = e.target.result;};
        reader.readAsDataURL(poder);}}
    document.getElementById('poder').addEventListener('change', function() {
      cargarVistaPrevia();});
</script>

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
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    th {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .pendiente {
        color:#ff0000; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .verdoc {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .verdoc:hover {
        color:#faa625; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .verdoc2 {
        color:#b5b5b5; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .dropify-wrapper {
        height: 200px !important;
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
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
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
    .custom-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 40px;
    }
    .custom-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .custom2-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .custom2-button:hover {
        background-color: #faa625;
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
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 10px;
        margin-left: 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
    .btn-multiple {
        background-color: #ffffff;
        color: #26b0e2;
        border-color: #26b0e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-multiple:hover {
        background-color: #26b0e2;
        color: #ffffff;
    }
</style>
@stop