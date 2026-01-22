@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
@if ($hayPendientes)
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#requisitosModal">VER REQUISITOS</a>
@endif
<h5>SUBIR REQUISITOS DE APELACIÓN SEGUNDA SOLICITUD DE:</h5>
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
        }, 3000);
    </script>
@endif

@if ($hayPendientes)
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                {!! Form::model($cliente, ['route' => ['admin.asociados.guardardocrequisitosapelsegsolicitud', $cliente], 'method' => 'PUT', 'files' => true]) !!}
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
                    @foreach($requisitos as $req)
                        @if($req['pendiente'])
                            <div class="form-group col-lg-3">
                                {!! Form::label($req['campo'], $req['label'] . ':') !!}
                                {!! Form::file($req['campo'], ['class' => 'form-control-file dropify']) !!}
                                
                                @if($req['campo'] === 'poder')
                                    {!! Form::text('numeropoder', null, [
                                        'class' => 'form-control',
                                        'placeholder' => 'NRO. DE PODER'
                                    ]) !!}
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
                
                {!! Form::submit('SUBIR REQUISITOS', ['class' => 'btn btn-crear btn-sm']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endif

@if ($hayPendientes)
<div class="modal fade" id="requisitosModal" tabindex="-1" aria-labelledby="requisitosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="requisitosModalLabel">LISTA DE REQUISITOS</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
@else
            <div class="card">
                <div class="card-body">
@endif
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-7">
                                <label>REQUISITOS DE APELACIÓN SEGUNDA SOLICITUD</label>
                                <div class="card"> 
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table tabla-ajustada table-striped table-bordered">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th>REQUISITO</th>
                                                        <th class="text-center">ESTADO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($requisitosList as $req)
                                                        @if (!is_null($req['file']))
                                                            <tr>
                                                                <td>
                                                                    {{ $req['label'] }}
                                                                    @if(!empty($req['extra']))
                                                                        : {{ $req['extra'] }}
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($req['uploaded'])
                                                                        @if (!empty($req['restricted']) && !in_array($userRole, ['MAESTRO','ADMINISTRADOR']))
                                                                            <p class="verdoc2" disabled>VER DOCUMENTO</p>
                                                                        @else
                                                                            <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$req['file']}") }}" 
                                                                            target="_blank" class="verdoc">VISUALIZAR</a>
                                                                        @endif
                                                                    @else
                                                                        <div class="pendiente">PENDIENTE</div>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <label>REQUISITOS PARA NUEVO TRÁMITE</label>
                                <div class="card"> 
                                    <div class="card-body">
                                        <form id="pdfForm" action="{{ route('admin.asociados.descargarchecklistclienteita2', $cliente) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="documentosSeleccionados" id="documentosSeleccionadosInput">
                                            <input type="hidden" name="documentosSeleccionados2" id="documentosSeleccionados2Input">
                                            <input type="hidden" name="tramitecliente" id="tramitecliente" value="APELACIÓN SEGUNDA SOLICITUD">
                                            <div class="form-group">
                                                <label for="tipo_solicitud">Trámite:</label>
                                                <select id="tipo_solicitud" name="tipo_solicitud" class="form-control">
                                                    <option value="">Seleccione...</option>
                                                    <option value="INVALIDEZ">INVALIDEZ</option>
                                                    <option value="APELACIÓN">APELACIÓN</option>
                                                    <option value="SEGUNDA SOLICITUD">SEGUNDA SOLICITUD</option>
                                                    {{-- <option value="APELACIÓN SEGUNDA SOLICITUD">APELACIÓN SEGUNDA SOLICITUD</option> --}}
                                                    <option value="TERCERA SOLICITUD">TERCERA SOLICITUD</option>
                                                    <option value="APELACIÓN TERCERA SOLICITUD">APELACIÓN TERCERA SOLICITUD</option>
                                                    <option value="RECALIFICACIÓN">RECALIFICACIÓN</option>
                                                    <option value="APELACIÓN DE RECALIFICACIÓN">APELACIÓN DE RECALIFICACIÓN</option>
                                                    <option value="RECALIFICACIÓN SEGUNDA SOLICITUD">RECALIFICACIÓN SEGUNDA SOLICITUD</option>
                                                    <option value="APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD">APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD</option>
                                                    <option value="JUBILACIÓN">JUBILACIÓN</option>
                                                    <option value="RETIRO DE APORTES TOTAL">RETIRO DE APORTES TOTAL</option>
                                                    <option value="RETIRO DE APORTES PARCIAL">RETIRO DE APORTES PARCIAL</option>
                                                    <option value="PENSIÓN POR MUERTE">PENSIÓN POR MUERTE</option>
                                                    <option value="MASA HEREDITARIA">MASA HEREDITARIA</option>
                                                    <option value="COMPENSACIÓN DE COTIZACIONES (SENASIR)">COMPENSACIÓN DE COTIZACIONES (SENASIR)</option>
                                                </select>
                                            </div>
                                            <script>
                                                document.addEventListener("DOMContentLoaded", function () {
                                                    const checkboxes = document.querySelectorAll('.form-group.opcion input[type="checkbox"]');
                                                    checkboxes.forEach(chk => {
                                                        chk.addEventListener('change', function () {
                                                            if (this.checked) {
                                                                const grupo = this.closest('.form-group.opcion');
                                                                const otros = grupo.querySelectorAll('input[type="checkbox"]');
                                                                otros.forEach(cb => {
                                                                    if (cb !== this) cb.checked = false;
                                                                });
                                                            }
                                                        });
                                                    });
                                                    const selectSolicitud = document.getElementById("tipo_solicitud");
                                                    selectSolicitud.addEventListener("change", function () {
                                                        checkboxes.forEach(cb => cb.checked = false);
                                                    });
                                                });
                                            </script>
                                        </form>
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                const select = document.getElementById("tipo_solicitud");
                                                const checkboxes = document.querySelectorAll("#checkboxes-container .opcion");
                                                function mostrarChecks(valor) {
                                                    checkboxes.forEach(cb => {
                                                        cb.style.display = "none";
                                                        const valorClase = valor.replace(/\s+/g, "_"); 
                                                        if (valor && cb.classList.contains(valorClase)) {
                                                            cb.style.display = "block";
                                                        }
                                                    });
                                                }
                                                select.addEventListener("change", function () {
                                                    mostrarChecks(this.value);
                                                });
                                                mostrarChecks("");
                                            });
                                        </script>
                                        <style>
                                            .form-group.opcion {
                                                margin-bottom: 8px;
                                                margin-top: -10px;
                                                border-bottom: 1px solid #ccc;
                                            }
                                        </style>
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                const relaciones = {
                                                    "poder": "numeropoder",
                                                    "poder2": "numeropoder2",
                                                };
                                                Object.keys(relaciones).forEach(principalId => {
                                                    const dependienteId = relaciones[principalId];
                                                    const principal = document.getElementById(principalId);
                                                    const dependiente = document.getElementById(dependienteId);

                                                    if (principal && dependiente) {
                                                        principal.addEventListener("change", function () {
                                                            dependiente.checked = principal.checked;
                                                            const opuestoId = principalId.endsWith("2")
                                                                ? principalId.slice(0, -1)
                                                                : principalId + "2";
                                                            const opuesto = document.getElementById(opuestoId);
                                                            const opuestoDependiente = document.getElementById(relaciones[opuestoId]);
                                                            if (this.checked) {
                                                                if (opuesto) opuesto.checked = false;
                                                                if (opuestoDependiente) opuestoDependiente.checked = false;
                                                            }
                                                        });
                                                    }
                                                });
                                            });
                                        </script>

                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                const checkboxes = document.querySelectorAll('.form-group.opcion input[type="checkbox"]');
                                                checkboxes.forEach(chk => {
                                                    chk.addEventListener('change', function () {
                                                        if (this.checked) {
                                                            const grupo = this.closest('.form-group.opcion');
                                                            const otros = grupo.querySelectorAll('input[type="checkbox"]');
                                                            otros.forEach(cb => {
                                                                if (cb !== this) cb.checked = false;
                                                            });
                                                        }
                                                    });
                                                });
                                            });
                                        </script>

                                        <div class="row" id="checkboxes-container">
                                            <div class="col-lg-12">
                                                <div class="form-group" style="background-color: #f0f0f0">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8">Requisito</label>
                                                        <label class="col-2 text-center">Nuevo</label>
                                                        <label class="col-2 text-center">Actual</label>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="poder" style="font-weight: 500;">PODER</label>
                                                        <input class="col-2" type="checkbox" name="poder" value="poder" id="poder">
                                                        <input class="col-2" type="checkbox" name="poder" value="poder2" id="poder2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)" hidden>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="numeropoder" style="font-weight: 500;">NRO. PODER</label>
                                                        <input class="col-2" type="checkbox" name="numeropoder" value="numeropoder" id="numeropoder">
                                                        <input class="col-2" type="checkbox" name="numeropoder" value="numeropoder2" id="numeropoder2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="avcci" style="font-weight: 500;">AVC/CARNET ASEGURADO</label> 
                                                        <input class="col-2" type="checkbox" name="avcci" value="avcci" id="avcci">
                                                        <input class="col-2" type="checkbox" name="avcci" value="avcci2" id="avcci2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="cnacasegurado" style="font-weight: 500;">CERTIFICADO NACIMIENTO ASEGURADO</label>
                                                        <input class="col-2" type="checkbox" name="cnacasegurado" value="cnacasegurado" id="cnacasegurado">
                                                        <input class="col-2" type="checkbox" name="cnacasegurado" value="cnacasegurado2" id="cnacasegurado2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN JUBILACIÓN RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="ciasegurado" style="font-weight: 500;">CARNET IDENTIDAD ASEGURADO</label>
                                                        <input class="col-2" type="checkbox" name="ciasegurado" value="ciasegurado" id="ciasegurado">
                                                        <input class="col-2" type="checkbox" name="ciasegurado" value="ciasegurado2" id="ciasegurado2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE MASA_HEREDITARIA RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="crodomicilio" style="font-weight: 500;">CROQUIS DE DOMICILIO</label>
                                                        <input class="col-2" type="checkbox" name="crodomicilio" value="crodomicilio" id="crodomicilio">
                                                        <input class="col-2" type="checkbox" name="crodomicilio" value="crodomicilio2" id="crodomicilio2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE MASA_HEREDITARIA RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="contrato" style="font-weight: 500;">CONTRATO</label>
                                                        <input class="col-2" type="checkbox" name="contrato" value="contrato" id="contrato">
                                                        <input class="col-2" type="checkbox" name="contrato" value="contrato2" id="contrato2">
                                                    </div>
                                                </div>
                                                @if (strtolower($estadoCivil) === 'casad@')
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cmatrimonio" style="font-weight: 500;">CERTIFICADO DE MATRIMONIO</label>
                                                            <input class="col-2" type="checkbox" name="cmatrimonio" value="cmatrimonio" id="cmatrimonio">
                                                            <input class="col-2" type="checkbox" name="cmatrimonio" value="cmatrimonio2" id="cmatrimonio2">
                                                        </div>
                                                    </div>
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cnacconyuge" style="font-weight: 500;">CERTIFICADO NACIMIENTO CONYUGE</label>
                                                            <input class="col-2" type="checkbox" name="cnacconyuge" value="cnacconyuge" id="cnacconyuge">
                                                            <input class="col-2" type="checkbox" name="cnacconyuge" value="cnacconyuge2" id="cnacconyuge2">
                                                        </div>
                                                    </div>
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="ciconyuge" style="font-weight: 500;">CARNET IDENTIDAD CONYUGE</label>
                                                            <input class="col-2" type="checkbox" name="ciconyuge" value="ciconyuge" id="ciconyuge">
                                                            <input class="col-2" type="checkbox" name="ciconyuge" value="ciconyuge2" id="ciconyuge2">
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (strtolower($estadoCivil) === 'union libre')
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cunionlibre" style="font-weight: 500;">CERTIFICADO DE UNIÓN LIBRE</label>
                                                            <input class="col-2" type="checkbox" name="cunionlibre" value="cunionlibre" id="cunionlibre">
                                                            <input class="col-2" type="checkbox" name="cunionlibre" value="cunionlibre" id="cunionlibre">
                                                        </div>
                                                    </div>
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cnacimientounionlibre" style="font-weight: 500;">CERTIFICADO NACIMIENTO DE UNIÓN LIBRE</label>
                                                            <input class="col-2" type="checkbox" name="cnacimientounionlibre" value="cnacimientounionlibre" id="cnacimientounionlibre">
                                                            <input class="col-2" type="checkbox" name="cnacimientounionlibre" value="cnacimientounionlibre2" id="cnacimientounionlibre2">
                                                        </div>
                                                    </div>
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="ciunionlibre" style="font-weight: 500;">CARNET IDENTIDAD DE UNIÓN LIBRE</label>
                                                            <input class="col-2" type="checkbox" name="ciunionlibre" value="ciunionlibre" id="ciunionlibre">
                                                            <input class="col-2" type="checkbox" name="ciunionlibre" value="ciunionlibre2" id="ciunionlibre2">
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (strtolower($estadoCivil) === 'divorciad@')
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cdivorcio" style="font-weight: 500;">CERTIFICADO DE DIVORCIO</label>
                                                            <input class="col-2" type="checkbox" name="cdivorcio" value="cdivorcio" id="cdivorcio">
                                                            <input class="col-2" type="checkbox" name="cdivorcio" value="cdivorcio2" id="cdivorcio2">
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (strtolower($estadoCivil) === 'viud@')
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE MASA_HEREDITARIA">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cdefuncion" style="font-weight: 500;">CERTIFICADO DE DIFUNCIÓN</label>
                                                            <input class="col-2" type="checkbox" name="cdefuncion" value="cdefuncion" id="cdefuncion">
                                                            <input class="col-2" type="checkbox" name="cdefuncion" value="cdefuncion2" id="cdefuncion2">
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($numHijosMenores > 0 || $numHijosMenores !== null)
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cnacjihos" style="font-weight: 500;">CERTIFICADO NACIMIENTO HIJOS < 25</label>
                                                            <input class="col-2" type="checkbox" name="cnacjihos" value="cnacjihos" id="cnacjihos">
                                                            <input class="col-2" type="checkbox" name="cnacjihos" value="cnacjihos2" id="cnacjihos2">
                                                        </div>
                                                    </div>
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD PENSIÓN_POR_MUERTE RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cihijos" style="font-weight: 500;">CARNET IDENTIDAD HIJOS < 25</label>
                                                            <input class="col-2" type="checkbox" name="cihijos" value="cihijos" id="cihijos">
                                                            <input class="col-2" type="checkbox" name="cihijos" value="cihijos2" id="cihijos2">
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (strtolower($estadoLaboral) === 'activo')
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="ctrabajo" style="font-weight: 500;">CERTIFICADO DE TRABAJO</label>
                                                            <input class="col-2" type="checkbox" name="ctrabajo" value="ctrabajo" id="ctrabajo">
                                                            <input class="col-2" type="checkbox" name="ctrabajo" value="ctrabajo2" id="ctrabajo2">
                                                        </div>
                                                    </div>
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="boletapago" style="font-weight: 500;">BOLETA DE PAGO</label>
                                                            <input class="col-2" type="checkbox" name="boletapago" value="boletapago" id="boletapago">
                                                            <input class="col-2" type="checkbox" name="boletapago" value="boletapago2" id="boletapago2">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="egestora" style="font-weight: 500;">EXTRACTO DE GESTORA</label>
                                                        <input class="col-2" type="checkbox" name="egestora" value="egestora" id="egestora">
                                                        <input class="col-2" type="checkbox" name="egestora" value="egestora2" id="egestora2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="recordservicios" style="font-weight: 500;">RECORD SERVICIOS</label>
                                                        <input class="col-2" type="checkbox" name="recordservicios" value="recordservicios" id="recordservicios">
                                                        <input class="col-2" type="checkbox" name="recordservicios" value="recordservicios2" id="recordservicios2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="infomedicasalud" style="font-weight: 500;">INFORMACIÓN MÉDICA</label>
                                                        <input class="col-2" type="checkbox" name="infomedicasalud" value="infomedicasalud" id="infomedicasalud">
                                                        <input class="col-2" type="checkbox" name="infomedicasalud" value="infomedicasalud2" id="infomedicasalud2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ APELACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD PENSIÓN_POR_MUERTE">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="denfaccidente" style="font-weight: 500;">DENUNCIA ENFERMEDAD ACCIDENTE</label>
                                                        <input class="col-2" type="checkbox" name="denfaccidente" value="denfaccidente" id="denfaccidente">
                                                        <input class="col-2" type="checkbox" name="denfaccidente" value="denfaccidente2" id="denfaccidente2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ JUBILACIÓN PENSIÓN_POR_MUERTE">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="actdatos" style="font-weight: 500;">ACTUALIZACIÓN DE DATOS</label>
                                                        <input class="col-2" type="checkbox" name="actdatos" value="actdatos" id="actdatos">
                                                        <input class="col-2" type="checkbox" name="actdatos" value="actdatos2" id="actdatos2">
                                                    </div>
                                                </div>
                                                @if ($numHijosMenores > 0 || $numHijosMenores !== null)
                                                    <div class="form-group opcion APELACIÓN_SEGUNDA_SOLICITUD APELACIÓN_TERCERA_SOLICITUD INVALIDEZ SEGUNDA_SOLICITUD TERCERA_SOLICITUD JUBILACIÓN PENSIÓN_POR_MUERTE">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="resolinvhijos" style="font-weight: 500;">RESOLUCIÓN INVALIDEZ DE HIJOS < 25</label>
                                                            <input class="col-2" type="checkbox" name="resolinvhijos" value="resolinvhijos" id="resolinvhijos">
                                                            <input class="col-2" type="checkbox" name="resolinvhijos" value="resolinvhijos2" id="resolinvhijos2">
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="form-group opcion APELACIÓN">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="dictamencalentenc" style="font-weight: 500;">DICTAMEN CALIFICACIÓN ENTIDAD ENCARGADA</label>
                                                        <input class="col-2" type="checkbox" name="dictamencalentenc" value="dictamencalentenc" id="dictamencalentenc">
                                                        <input class="col-2" type="checkbox" name="dictamencalentenc" value="dictamencalentenc2" id="dictamencalentenc2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN SEGUNDA_SOLICITUD TERCERA_SOLICITUD">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="anteriordictamen" style="font-weight: 500;">ANTERIOR DICTAMEN O RESOLUCIÓN</label>
                                                        <input class="col-2" type="checkbox" name="anteriordictamen" value="anteriordictamen" id="anteriordictamen">
                                                        <input class="col-2" type="checkbox" name="anteriordictamen" value="anteriordictamen2" id="anteriordictamen2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion JUBILACIÓN">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="ccompcotsenasir" style="font-weight: 500;">CERTIFICADO COMPENZACIÓN COTIZACIONES SENASIR</label>
                                                        <input class="col-2" type="checkbox" name="ccompcotsenasir" value="ccompcotsenasir" id="ccompcotsenasir">
                                                        <input class="col-2" type="checkbox" name="ccompcotsenasir" value="ccompcotsenasir2" id="ccompcotsenasir2">
                                                    </div>
                                                </div>
                                                @if (strtolower($genero) === 'femenino')
                                                    <div class="form-group opcion JUBILACIÓN">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cnactreshijos" style="font-weight: 500;">CERTIFICADO NACIMIENTO DE 3 HIJOS</label>
                                                            <input class="col-2" type="checkbox" name="cnactreshijos" value="cnactreshijos" id="cnactreshijos">
                                                            <input class="col-2" type="checkbox" name="cnactreshijos" value="cnactreshijos2" id="cnactreshijos2">
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (strtolower($ocupacion) === 'minero')
                                                    <div class="form-group opcion JUBILACIÓN">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="ctrabajoinsalubre" style="font-weight: 500;">CERTIFICADO TRABAJO INSALUBRE</label>
                                                            <input class="col-2" type="checkbox" name="ctrabajoinsalubre" value="ctrabajoinsalubre" id="ctrabajoinsalubre">
                                                            <input class="col-2" type="checkbox" name="ctrabajoinsalubre" value="ctrabajoinsalubre2" id="ctrabajoinsalubre2">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="form-group opcion JUBILACIÓN MASA_HEREDITARIA PENSIÓN_POR_MUERTE">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="poderciapoderado" style="font-weight: 500;">PODER Y CARNET IDENTIDAD APODERADO</label>
                                                        <input class="col-2" type="checkbox" name="poderciapoderado" value="poderciapoderado" id="poderciapoderado">
                                                        <input class="col-2" type="checkbox" name="poderciapoderado" value="poderciapoderado2" id="poderciapoderado2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion PENSIÓN_POR_MUERTE">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="cmeddifuncion" style="font-weight: 500;">CERTIFICADO MÉDICO DIFUNCIÓN</label>
                                                        <input class="col-2" type="checkbox" name="cmeddifuncion" value="cmeddifuncion" id="cmeddifuncion">
                                                        <input class="col-2" type="checkbox" name="cmeddifuncion" value="cmeddifuncion2" id="cmeddifuncion2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion MASA_HEREDITARIA PENSIÓN_POR_MUERTE">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="cnactitular" style="font-weight: 500;">CERTIFICADO NACIMIENTO TITULAR</label>
                                                        <input class="col-2" type="checkbox" name="cnactitular" value="cnactitular" id="cnactitular">
                                                        <input class="col-2" type="checkbox" name="cnactitular" value="cnactitular2" id="cnactitular2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion MASA_HEREDITARIA PENSIÓN_POR_MUERTE">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="cititular" style="font-weight: 500;">CARNET IDENTIDAD TITULAR</label>
                                                        <input class="col-2" type="checkbox" name="cititular" value="cititular" id="cititular">
                                                        <input class="col-2" type="checkbox" name="cititular" value="cititular2" id="cititular2">
                                                    </div>
                                                </div>
                                                @if ($numHijosMenores > 0 || $numHijosMenores !== null)
                                                    <div class="form-group opcion PENSIÓN_POR_MUERTE">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cestudioshijos" style="font-weight: 500;">CERTIFICADO ESTUDIOS HIJOS < 25</label>
                                                            <input class="col-2" type="checkbox" name="cestudioshijos" value="cestudioshijos" id="cestudioshijos">
                                                            <input class="col-2" type="checkbox" name="cestudioshijos" value="cestudioshijos2" id="cestudioshijos2">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="form-group opcion MASA_HEREDITARIA">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="tdeclaherederos" style="font-weight: 500;">TESTIMONIO DE DECLARATORIA DE HEREDEROS</label>
                                                        <input class="col-2" type="checkbox" name="tdeclaherederos" value="tdeclaherederos" id="tdeclaherederos">
                                                        <input class="col-2" type="checkbox" name="tdeclaherederos" value="tdeclaherederos2" id="tdeclaherederos2">
                                                    </div>
                                                </div>
                                                @if ($numHijosMenores > 0 || $numHijosMenores !== null)
                                                    <div class="form-group opcion MASA_HEREDITARIA">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <label class="col-8" for="cnacherederos" style="font-weight: 500;">CERTIFICADO NACIMIENTO DECLARADOS HEREDEROS</label>
                                                            <input class="col-2" type="checkbox" name="cnacherederos" value="cnacherederos" id="cnacherederos">
                                                            <input class="col-2" type="checkbox" name="cnacherederos" value="cnacherederos2" id="cnacherederos2">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="form-group opcion MASA_HEREDITARIA">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="cideclaherederos" style="font-weight: 500;">CARNET IDENTIDAD DECLARADOS HEREDEROS</label>
                                                        <input class="col-2" type="checkbox" name="cideclaherederos" value="cideclaherederos" id="cideclaherederos">
                                                        <input class="col-2" type="checkbox" name="cideclaherederos" value="cideclaherederos2" id="cideclaherederos2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="compenzacioncotizacion" style="font-weight: 500;">CERTIFICADO DE COMPENZACIÓN DE COTIZACIONES</label>
                                                        <input class="col-2" type="checkbox" name="compenzacioncotizacion" value="compenzacioncotizacion" id="compenzacioncotizacion">
                                                        <input class="col-2" type="checkbox" name="compenzacioncotizacion" value="compenzacioncotizacion2" id="compenzacioncotizacion2">
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group opcion RETIRO_DE_APORTES_TOTAL RETIRO_DE_APORTES_PARCIAL">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="contratojubilacion" style="font-weight: 500;">CONTRATO DE JUBILACIÓN</label>
                                                        <input type="checkbox" name="contratojubilacion" value="contratojubilacion" id="contratojubilacion">
                                                        <input type="checkbox" name="contratojubilacion" value="contratojubilacion2" id="contratojubilacion2">
                                                    </div>
                                                </div> --}}
                                                <div class="form-group opcion COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="csalarioaportes" style="font-weight: 500;">CERTIFICADO DE SALARIOS Y APORTES</label>
                                                        <input class="col-2" type="checkbox" name="csalarioaportes" value="csalarioaportes" id="csalarioaportes">
                                                        <input class="col-2" type="checkbox" name="csalarioaportes" value="csalarioaportes2" id="csalarioaportes2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="fotofrojoasegurado" style="font-weight: 500;">ASEGURADO FOTO 4X4 FONDO ROJO</label>
                                                        <input class="col-2" type="checkbox" name="fotofrojoasegurado" value="fotofrojoasegurado" id="fotofrojoasegurado">
                                                        <input class="col-2" type="checkbox" name="fotofrojoasegurado" value="fotofrojoasegurado2" id="fotofrojoasegurado2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="fotofrojoapoderadocroquis" style="font-weight: 500;">APOD. FOTO 4X4 FONDO ROJO + CROQUIS DOM.</label>
                                                        <input class="col-2" type="checkbox" name="fotofrojoapoderadocroquis" value="fotofrojoapoderadocroquis" id="fotofrojoapoderadocroquis">
                                                        <input class="col-2" type="checkbox" name="fotofrojoapoderadocroquis" value="fotofrojoapoderadocroquis2" id="fotofrojoapoderadocroquis2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="csalarioaporteslegalizada" style="font-weight: 500;">CERTIFICADO DE SALARIOS Y APORTES (LEGALIZADO)</label>
                                                        <input class="col-2" type="checkbox" name="csalarioaporteslegalizada" value="csalarioaporteslegalizada" id="csalarioaporteslegalizada">
                                                        <input class="col-2" type="checkbox" name="csalarioaporteslegalizada" value="csalarioaporteslegalizada2" id="csalarioaporteslegalizada2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion COMPENSACIÓN_DE_COTIZACIONES_(SENASIR)">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="finiquito" style="font-weight: 500;">FINIQUITO</label>
                                                        <input class="col-2" type="checkbox" name="finiquito" value="finiquito" id="finiquito">
                                                        <input class="col-2" type="checkbox" name="finiquito" value="finiquito2" id="finiquito2">
                                                    </div>
                                                </div>
                                                <div class="form-group opcion RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN_SEGUNDA_SOLICITUD APELACIÓN_DE_RECALIFICACIÓN RECALIFICACIÓN">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="col-8" for="solrecaldictamen" style="font-weight: 500;">CARTA DE SOLICITUD DE RECALIFICACION DE DICTAMEN</label>
                                                        <input class="col-2" type="checkbox" name="solrecaldictamen" value="solrecaldictamen" id="solrecaldictamen">
                                                        <input class="col-2" type="checkbox" name="solrecaldictamen" value="solrecaldictamen2" id="solrecaldictamen2">
                                                    </div>
                                                </div>
                                                <script>
                                                    document.querySelectorAll('.color-toggle').forEach(label => {
                                                        label.addEventListener('dblclick', () => {
                                                            label.classList.toggle('black');
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                        <div style="text-align: center; margin-top: 10px;">
                                            <button id="btnGenerar" onclick="generatePDF(), generatePDF2()" class="btn-crear btn-sm" disabled>
                                                GENERAR CHECK LIST
                                            </button>
                                        </div>
                                        <script>
                                            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                                            const btnGenerar = document.getElementById('btnGenerar');

                                            function toggleButton() {
                                                btnGenerar.disabled = !Array.from(checkboxes).some(chk => chk.checked);
                                            }

                                            checkboxes.forEach(chk => chk.addEventListener('change', toggleButton));
                                        </script>

                                        <script>
                                            function generatePDF() {
                                                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                                                var documentosSeleccionados = [];
                                                checkboxes.forEach(function(checkbox) {
                                                    if (checkbox.checked) {
                                                        documentosSeleccionados.push(checkbox.value);
                                                    }
                                                });
                                                document.getElementById('documentosSeleccionadosInput').value = JSON.stringify(documentosSeleccionados);
                                                document.getElementById('pdfForm').submit();
                                            }

                                            function generatePDF2() {
                                                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                                                var documentosSeleccionados2 = [];
                                                checkboxes.forEach(function(checkbox) {
                                                    if (checkbox.checked) {
                                                        documentosSeleccionados2.push(checkbox.value);
                                                    }
                                                });
                                                document.getElementById('documentosSeleccionados2Input').value = JSON.stringify(documentosSeleccionados2);
                                                document.getElementById('pdfForm').submit();
                                            }
                                        </script>
                                        <script>
                                            function generatePDFOnly() {
                                                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                                                var documentosSeleccionados = [];
                                                checkboxes.forEach(function(checkbox) {
                                                    if (checkbox.checked) {
                                                        documentosSeleccionados.push(checkbox.value);
                                                    }
                                                });
                                                document.getElementById('documentosSeleccionadosInputOnly').value = JSON.stringify(documentosSeleccionados);

                                                var documentosSeleccionados2 = [];
                                                checkboxes.forEach(function(checkbox) {
                                                    if (checkbox.checked) {
                                                        documentosSeleccionados2.push(checkbox.value);
                                                    }
                                                });
                                                document.getElementById('documentosSeleccionados2InputOnly').value = JSON.stringify(documentosSeleccionados2);

                                                document.getElementById('pdfOnlyForm').submit();}
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
@if (!$hayPendientes)
                </div>
            </div>
@else
        </div>
    </div>
</div>
@endif

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
    .tabla-ajustada td, 
    .tabla-ajustada th {
        padding: 2px 6px;
        line-height: 1.5;
    }
    th {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .pendiente {
        color:#ff0000; 
        font-family: "Segoe UI";
        font-weight: 500;
    }
    .verdoc {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
    }
    .verdoc:hover {
        color:#faa625; 
        font-family: "Segoe UI";
        font-weight: 500;
    }
    .verdoc2 {
        color:#b5b5b5; 
        font-family: "Segoe UI";
        font-weight: 500;
    }
    .dropify-wrapper {
        height: 100px !important;
    }
    .dropify-message p {
        font-size: 14px;
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
        font-weight: 800;
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
    .opcion {
        margin-top: -20px;
    }
</style>
@stop