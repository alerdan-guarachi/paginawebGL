@extends('adminlte::page')

@section('content_header')
<h1>RESERVAS MÉDICAS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/reservasmedicas.css') }}">
<style>
    .table td {
        padding: 5px 10px;;
    }
</style>
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
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid justify-content-end">
            <div class="d-flex flex-wrap align-items-center">
                {{-- {{ $programacionclientes->links() }} --}}
                <form id="search-form" action="{{ route('buscarclientereservamedica') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        {{-- <input type="date" name="buscarporfecha" class="form-control mr-sm-2" placeholder="Fecha"> --}}
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="NOMBRE DEL CLIENTE">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar" type="submit">BUSCAR</button>
                    <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" name="buscartodo" type="submit" value="1">MOSTRAR TODO</button>
                </form>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarclientereservamedica') }}";
            });
        });
    </script>

    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    ATENCIÓN PENDIENTE
                    <?php 
                    $totalatencion = $atencionpendienteCount + $atencionpendienteauditoriaCount;
                    if ($totalatencion > 0): ?>
                        <span class="circle"><?= $totalatencion ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    INFORMES PENDIENTES
                    <?php 
                    $totalIncompletos = $informependienteCount + $informependienteauditoriaCount;
                    if ($totalIncompletos > 0): ?>
                        <span class="circle"><?= $totalIncompletos ?></span>
                    <?php endif; ?>
                </a>
            </li> 
            <li class="nav-item"> 
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    COMPLETOS
                    <?php 
                    $totalCompletos = $informecompletoCount + $informecompletoauditoriaCount;
                    if ($totalCompletos > 0): ?>
                        <span class="circle"><?= $totalCompletos ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            {{-- <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    PAGOS MENSUALES
                </a>
            </li> --}}
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
        {{-- ATENCION PENDIENTE --}}
        <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tipo_Cli.</th>
                            <th>ID_Cli.</th>
                            <th>Cliente</th>
                            <th hidden>Fecha Bateria</th>
                            @if ($rolusuario !== 'PROVEEDOR')
                            <th>Proveedor_Asignado</th>
                            @endif
                            <th>Estudio/Especialidad</th>
                            <th>Fecha_Asignada</th>
                            <th>Hora_Asignada</th>
                            <th>Atención</th>
                            @can('admin.informesfinales.ordenesproveedores')
                            <th>Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservasmedicas as $reservasmedica)
                            @if(!$reservasmedica->documentacionDisponible && !$reservasmedica->informeDisponible)
                                <tr>
                                    <td>ITA</td>
                                    <td>{{$reservasmedica->clienteitaid}}</td>
                                    <td>{{$reservasmedica->clienteitanombre}}</td>
                                    <td hidden>{{$reservasmedica->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedica->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedica->accionnombre}}</td>
                                    <td>{{$reservasmedica->fechaasignada}}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservasmedica->horadesde)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasmedica->horahasta)->format('H:i') }}</td>
                                    <td>
                                        <p><span class="badge bg-danger">PENDIENTE</span></p>
                                    </td>
                                    @can('admin.informesfinales.ordenesproveedores')
                                    <td>
                                        <a href="{{ route('generar.pdf.orden', [
                                            'clienteitaid' => $reservasmedica->clienteitaid,
                                            'fechabateria' => $reservasmedica->fechabateria,
                                            'proveedornombre' => $reservasmedica->proveedornombre ?? 'N/A',
                                            'clienteitanombre' => urlencode($reservasmedica->clienteitanombre)
                                        ]) }}" class="btn btn-sm btn-crearproveedor" title="GENERAR ORDEN">
                                           <i class="fas fa-file"></i>
                                        </a>

                                        @if($reservasmedica->ciasegurado && $reservasmedica->ciasegurado !== 'PENDIENTE')
                                            <a href="{{ asset('/requisitosclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->ciasegurado) }}" 
                                                class="btn btn-sm btn-subirinformeproveedor" target="_blank" title="VER CARNET DE IDENTIDAD">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        @else
                                            <a href="#" 
                                                class="btn btn-sm btn-subirinformeproveedor disabled" 
                                                title="VER CARNET DE IDENTIDAD"
                                                onclick="return false;">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        @endif
                                    </td>
                                    @endcan                                    
                                </tr>
                            @endif
                        @endforeach
                        @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                            @if(!$reservasmedicaauditoria->documentacionDisponibleauditoria && !$reservasmedicaauditoria->informeDisponibleauditoria)
                                <tr>
                                    <td>AUDITORIA</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                    <td hidden>{{$reservasmedicaauditoria->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechaasignada}}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservasmedicaauditoria->horadesde)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasmedicaauditoria->horahasta)->format('H:i') }}</td>
                                    <td>
                                        <p><span class="badge bg-danger">PENDIENTE</span></p>
                                    </td>
                                    @can('admin.informesfinales.ordenesproveedores')
                                    <td>
                                        <a href="{{ route('generar.pdf.ordenauditoria', [
                                            'clienteauditoriaid' => $reservasmedicaauditoria->clienteauditoriaid,
                                            'fechabateria' => $reservasmedicaauditoria->fechabateria,
                                            'proveedornombre' => $reservasmedicaauditoria->proveedornombre ?? 'N/A',
                                            'clienteauditorianombre' => urlencode($reservasmedicaauditoria->clienteauditorianombre)
                                        ]) }}" class="btn btn-sm btn-crearproveedor" title="GENERAR ORDEN">
                                           <i class="fas fa-file"></i>
                                        </a>

                                        @if($reservasmedicaauditoria->ciasegurado && $reservasmedicaauditoria->ciasegurado !== 'PENDIENTE')
                                            <a href="{{ asset('/requisitosclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->ciasegurado) }}" 
                                                class="btn btn-sm btn-subirinformeproveedor" target="_blank" title="VER CARNET DE IDENTIDAD">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        @else
                                            <a href="#" 
                                                class="btn btn-sm btn-subirinformeproveedor disabled" 
                                                title="VER CARNET DE IDENTIDAD"
                                                onclick="return false;">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        @endif
                                    </td>
                                    @endcan
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- INFORME PENDIENTES --}}
        <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID_Prog.</th>
                            <th>Tipo_Cli.</th>
                            <th>ID_Cli.</th>
                            <th>Cliente</th>
                            <th hidden>Fecha_Bateria</th>
                            @if ($rolusuario !== 'PROVEEDOR')
                            <th>Proveedor_Asignado</th>
                            @endif
                            <th>Estudio/Especialidad</th>
                            <th>Fecha_Asignada</th>
                            <th>Hora_Asignada</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservasmedicas as $reservasmedica)
                            @if(!$reservasmedica->documentacionDisponible && $reservasmedica->informeDisponible)
                                <tr>
                                    <td>{{$reservasmedica->id}}</td>
                                    <td>ITA</td>
                                    <td>{{$reservasmedica->clienteitaid}}</td>
                                    <td>{{$reservasmedica->clienteitanombre}}</td>
                                    <td hidden>{{$reservasmedica->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedica->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedica->accionnombre}}</td>
                                    <td>{{$reservasmedica->fechaasignada}}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservasmedica->horadesde)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasmedica->horahasta)->format('H:i') }}</td>
                                    {{-- FICHA MEDICA --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'YELKA MORALES VELARDE')
                                        <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                            @if($reservasmedica->fichamedicaita)
                                                <a href="{{ asset('/fichamedicaclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->fichamedicaita) }}" class="btn btn-sm btn-subirinf" target="_blank" title="VER FICHA MEDICA">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            @else
                                                <a class="btn btn-sm btn-fichamedica" href="{{route('admin.asociados.crearformularioclienteita', $reservasmedica->clienteitaid)}}" title="CREAR FICHA MÉDICA">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            @endif
                                        </td>
                                    @endif

                                    {{-- DIAGNOSTICO MEDICO --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'MARICELA COLQUE SANDOVAL' || $nombreusuario === 'MONICA MACOÑO FLORES' || $nombreusuario === 'YELKA MORALES VELARDE')
                                        <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                            @if($reservasmedica->diagnosticomedicoita)
                                                <a href="{{ asset('/diagnosticos/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->diagnosticomedicoita) }}" class="btn btn-sm btn-subirdiagnostico" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-laptop-medical"></i>
                                                </a>
                                            @else
                                            <a href="{{ asset('/diagnosticos/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->diagnosticomedicoita) }}" 
                                                class="btn btn-sm btn-subirdiagnostico disabled" target="_blank" title="VER DIAGNÓSTICO">
                                                 <i class="fas fa-laptop-medical"></i>
                                             </a>
                                            <style>
                                                .btn.disabled {
                                                    pointer-events: none;
                                                    background-color: #d6d6d6;
                                                    color: #a5a5a5;
                                                    border-color: #d6d6d6;
                                                }
                                                .btn.disabled i {
                                                    color: #a5a5a5;
                                                }
                                            </style>                                             

                                            @endif
                                        </td>
                                        <!-- MODAL DIAGNOSTICO -->
                                        <div class="modal fade" id="subirdiagnosticoModal" tabindex="-1" role="dialog" aria-labelledby="subirdiagnosticoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="subirdiagnosticoModalLabel" style="color: #94c93b; font-weight: bold;">DIAGNÓSTICO</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    {!! Form::open(['route' => 'procesar.diagnostico', 'method' => 'POST', 'files' => true]) !!}
                                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                    {!! Form::hidden('clienteitaid', null, ['id' => 'modal-clienteitaid']) !!}
                                                    {!! Form::hidden('clienteitanombre', null, ['id' => 'modal-clienteitanombre']) !!}
                                                    {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                    {!! Form::hidden('accion', 'DIAGNÓSTICO MÉDICO') !!}
    
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-12"> 
                                                                <div class="file-upload">
                                                                    <label for="archivodiagnostico">DIAGNÓSTICO:</label>
                                                                    <input type="file" name="archivo" id="archivodiagnostico" accept=".pdf"/>
                                                                    <div class="file-preview" id="preview-archivo2"></div>
                                                                    @error('archivo')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('#subirdiagnosticoModal').on('show.bs.modal', function (event) {
                                                                var button = $(event.relatedTarget); // Botón que activó el modal
                                                                var clienteitaid = button.data('clienteitaid');
                                                                var clienteitanombre = button.data('clienteitanombre');
                                                                var fechabateria = button.data('fechabateria');
                                                                var accion = button.data('accion');
    
                                                                var modal = $(this);
                                                                modal.find('#modal-clienteitaid').val(clienteitaid);
                                                                modal.find('#modal-clienteitanombre').val(clienteitanombre);
                                                                modal.find('#modal-fechabateria').val(fechabateria);
                                                                modal.find('#modal-accion').val(accion);
                                                            });
                                                        });
                                                    </script>
                                                    <div class="modal-footer">
                                                        <div class="text-center w-100">
                                                            {!! Form::submit('SUBIR DIAGNÓSTICO', ['class' => 'btn btn-crear']) !!}
                                                        </div>
                                                    </div>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- CREAR BATERIA --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'YELKA MORALES VELARDE')
                                        <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crearproveedor" href="{{route('admin.asociados.crearbateriaclienteita', $reservasmedica->clienteitaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif

                                    {{-- SUBIR INFORME ITA --}}
                                    <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                        @if($reservasmedica->informeDisponible)
                                            <abbr title="SUBIR INFORME">
                                                <button type="button" class="btn btn-sm btn-subirinformeproveedor" 
                                                        data-toggle="modal" 
                                                        data-target="#subirinformeitaModal"
                                                        data-id="{{ $reservasmedica->id }}"
                                                        data-clienteitaid="{{ $reservasmedica->clienteitaid }}"
                                                        data-clienteitanombre="{{ $reservasmedica->clienteitanombre }}"
                                                        data-fechabateria="{{ $reservasmedica->fechabateria }}"
                                                        data-accion="{{ $reservasmedica->accionnombre }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </abbr>
                                        @else
                                            <p class="text-incompleto">FECHA DE ATENCIÓN PENDIENTE</p>
                                        @endif
                                    </td>

                                    {{-- MODAL SUBIR INFORMES ITA --}}
                                    <div class="modal fade" id="subirinformeitaModal" tabindex="-1" role="dialog" aria-labelledby="subirinformeitaModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <div class="container text-center">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h3 class="modal-title" id="subirinformeitaModalLabel" style="color: #94c93b; font-weight: bold;">
                                                                    SUBIR INFORME
                                                                </h3>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <p><strong>CLIENTE:</strong> <span id="clienteitanombre-texto"></span></p>
                                                            </div>
                                                        </div>
                                                        <div class="row" style="margin-top: -10px; margin-bottom:-10px;">
                                                            <div class="col-12">
                                                                <p><strong>EST. / ESP.:</strong> <span id="accionnombre-texto"></span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        $(document).on('click', '.btn-subirinformeproveedor', function () {
                                                            let clienteitanombre = $(this).data('clienteitanombre');
                                                            let accionnombre = $(this).data('accion');

                                                            $('#clienteitanombre-texto').text(clienteitanombre);
                                                            $('#accionnombre-texto').text(accionnombre);
                                                        });
                                                    </script>

                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                {!! Form::open(['route' => 'procesar.informe', 'method' => 'POST', 'files' => true, 'id' => 'formuno']) !!}
                                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                    {!! Form::hidden('clienteitaid', null, ['id' => 'modal-clienteitaid']) !!}
                                                    {!! Form::hidden('clienteitanombre', null, ['id' => 'modal-clienteitanombre']) !!}
                                                    {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                    {!! Form::hidden('accion', null, ['id' => 'modal-accion']) !!}
                                                    {!! Form::hidden('programacionid', null, ['id' => 'modal-id']) !!}

                                                    
                                                    <div class="modal-body">
                                                        <div class="row"> 
                                                            <div class="col-lg-6">
                                                                <div class="file-upload">
                                                                    <label for="archivoita">INFORME PDF (Obligatorio):</label>
                                                                    <input type="file" name="archivo" id="archivoita" class="file-input" accept=".pdf" onchange="handleFileSelectita(this, 'preview-archivoita')" />
                                                                    <label for="archivoita" class="file-custom-label">Elige un PDF</label>
                                                                    <div class="file-preview" id="preview-archivoita"></div>
                                                                    @error('archivo')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-lg-6">
                                                                <div class="file-upload">
                                                                    <label for="archivoita">INFORME WORD (Se cargará sin firma):</label>
                                                                    <input type="file" name="archivo3" id="archivoitaword" class="file-input" accept=".docx" onchange="handleFileSelectitaword(this, 'preview-archivoitaword')" />
                                                                    <label for="archivoitaword" class="file-custom-label">Elige un WORD</label>
                                                                    <div class="file-preview" id="preview-archivoitaword"></div>
                                                                    @error('archivo3')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            
                                                        
                                                            @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'PROMED S.R.L.' || $nombreusuario === 'SERRANO PORSTENDOERFER VIVIAN YANETH')
                                                                <div class="col-lg-6">
                                                                    <div class="file-upload">
                                                                        <label for="picture">IMAGEN 1:</label>
                                                                        <input type="file" name="picture" id="picture" accept="image/*" onchange="handleFileSelectita(this, 'preview-picture')" />
                                                                        <div class="file-preview" id="preview-picture"></div>
                                                                        @error('picture')
                                                                        <small class="text-danger fas fa-exclamation-circle">
                                                                            {{$message}}
                                                                        </small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="file-upload">
                                                                        <label for="picture2">IMAGEN 2:</label>
                                                                        <input type="file" name="picture2" id="picture2" accept="image/*" onchange="handleFileSelectita(this, 'preview-picture2')" />
                                                                        <div class="file-preview" id="preview-picture2"></div>
                                                                        @error('picture2')
                                                                        <small class="text-danger fas fa-exclamation-circle">
                                                                            {{$message}}
                                                                        </small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <script>
                                                            function handleFileSelectita(input, previewId) {
                                                                const preview = document.getElementById(previewId);
                                                                preview.innerHTML = '';
                                                        
                                                                if (input.files && input.files[0]) {
                                                                    const file = input.files[0];
                                                        
                                                                    if (file.type === 'application/pdf') {
                                                                        // Muestra solo el nombre del archivo PDF
                                                                        const fileName = document.createElement('span');
                                                                        fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                        fileName.className = 'file-name';
                                                                        preview.appendChild(fileName);
                                                                    } else if (file.type.startsWith('image/')) {
                                                                        // Muestra la vista previa de la imagen
                                                                        const fileURL = URL.createObjectURL(file);
                                                                        const img = document.createElement('img');
                                                                        img.src = fileURL;
                                                                        img.style.maxWidth = '100%';
                                                                        img.style.maxHeight = '300px';
                                                                        img.alt = 'Vista previa de la imagen';
                                                                        preview.appendChild(img);
                                                                    } else {
                                                                        const fileName = document.createElement('span');
                                                                        fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                        fileName.className = 'file-name';
                                                                        preview.appendChild(fileName);
                                                                    }
                                                                } else {
                                                                    preview.textContent = 'No se ha seleccionado ningún archivo';
                                                                }
                                                            }

                                                            function handleFileSelectitaword(input, previewId) {
                                                                const preview = document.getElementById(previewId);
                                                                preview.innerHTML = '';

                                                                if (input.files && input.files[0]) {
                                                                    const file = input.files[0];

                                                                    if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                                        // Muestra solo el nombre del archivo Word
                                                                        const fileName = document.createElement('span');
                                                                        fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                        fileName.className = 'file-name';
                                                                        preview.appendChild(fileName);
                                                                    } else if (file.type.startsWith('image/')) {
                                                                        // Muestra la vista previa de la imagen
                                                                        const fileURL = URL.createObjectURL(file);
                                                                        const img = document.createElement('img');
                                                                        img.src = fileURL;
                                                                        img.style.maxWidth = '100%';
                                                                        img.style.maxHeight = '300px';
                                                                        img.alt = 'Vista previa de la imagen';
                                                                        preview.appendChild(img);
                                                                    } else {
                                                                        const fileName = document.createElement('span');
                                                                        fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                        fileName.className = 'file-name';
                                                                        preview.appendChild(fileName);
                                                                    }
                                                                } else {
                                                                    preview.textContent = 'No se ha seleccionado ningún archivo';
                                                                }
                                                            }
                                                        </script>
                                                        
                                                        <div class="row" hidden>
                                                            <div class="col-lg-6 text-center">
                                                                <label for="firma">Firma Digital:</label>
                                                                <div>
                                                                    @if ($usuario->firmadigital)
                                                                        <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->firmadigital) }}" 
                                                                            alt="Firma Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                    @else
                                                                        <p>No se ha cargado ninguna firma.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 text-center">
                                                                <label for="sello">Sello Digital:</label>
                                                                <div>
                                                                    @if ($usuario->sellodigital)
                                                                        <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->sellodigital) }}" 
                                                                            alt="Sello Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                    @else
                                                                        <p>No se ha cargado ningún sello.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="modal-footer">
                                                        <div class="text-center w-100">
                                                            {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear']) !!}
                                                        </div>
                                                    </div> --}}
                                                    <div class="modal-footer">
                                                        <div class="text-center w-100">
                                                            {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear', 'id' => 'btnSubirInforme']) !!}
                                                        </div>
                                                    </div>

                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function () {
                                                            const form = document.querySelector('#formuno'); // <- CORREGIDO
                                                            const btn = document.getElementById('btnSubirInforme');

                                                            form.addEventListener('submit', function(e) {
                                                                // Cambiar texto del botón
                                                                btn.value = 'GUARDANDO...';
                                                                // Deshabilitar el botón
                                                                btn.disabled = true;
                                                            });
                                                        });
                                                    </script>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- SUBIR INFORMES MULTIPLES ITA --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'PROMED S.R.L.' || $nombreusuario === 'SERRANO PORSTENDOERFER VIVIAN YANETH')
                                        <td width="10px" style="padding-left: 2px; padding-right: 4px; justify-content: center;">
                                            @if($reservasmedica->informeDisponible)
                                                <abbr title="SUBIR INFORME MULTIPLE">
                                                    <button type="button" class="btn btn-sm btn-subirinformeproveedor" 
                                                            data-toggle="modal" 
                                                            data-target="#subirinformeitaModal2"
                                                            data-id="{{ $reservasmedica->id }}"
                                                            data-clienteitaid2="{{ $reservasmedica->clienteitaid }}"
                                                            data-clienteitanombre2="{{ $reservasmedica->clienteitanombre }}"
                                                            data-fechabateria2="{{ $reservasmedica->fechabateria }}"
                                                            data-accion2="{{ $reservasmedica->accionnombre }}">
                                                            <i class="fas fa-share-square"></i>
                                                    </button>
                                                </abbr>
                                            @else
                                                <p class="text-incompleto">FECHA DE ATENCIÓN PENDIENTE</p>
                                            @endif
                                        </td>
                                        <!-- MODAL SUBIR INFORME MULTIPLE ITA -->
                                        <div class="modal fade" id="subirinformeitaModal2" tabindex="-1" role="dialog" aria-labelledby="subirinformeitaModal2Label" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <div class="container text-center">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h3 class="modal-title" id="subirinformeitaModalLabel" style="color: #94c93b; font-weight: bold;">
                                                                        SUBIR INFORME
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <p><strong>CLIENTE:</strong> <span id="clienteitanombre2-texto"></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <script>
                                                            $(document).on('click', '.btn-subirinformeproveedor', function () {
                                                                let clienteitanombre2 = $(this).data('clienteitanombre2');
                                                                let accionnombre2 = $(this).data('accion2');
    
                                                                $('#clienteitanombre2-texto').text(clienteitanombre2);
                                                                $('#accionnombre2-texto').text(accionnombre2);
                                                            });
                                                        </script>
    
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    {!! Form::open(['route' => 'procesar.informe.multiple', 'method' => 'POST', 'files' => true, 'id' => 'formdos']) !!}
                                                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                        {!! Form::hidden('clienteitaid', null, ['id' => 'modal-clienteitaid']) !!}
                                                        {!! Form::hidden('clienteitanombre', null, ['id' => 'modal-clienteitanombre']) !!}
                                                        {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                        {!! Form::hidden('acciones_seleccionadas', null, ['id' => 'acciones_seleccionadas']) !!}
                                                        <script>
                                                            $(document).ready(function() {
                                                                $('#subirinformeitaModal2').on('show.bs.modal', function (event) {
                                                                    var button = $(event.relatedTarget);
                                                                    var clienteitaid = button.data('clienteitaid2');
                                                                    var clienteitanombre = button.data('clienteitanombre2');
                                                                    var fechabateria = button.data('fechabateria2');
                                                                    var id = button.data('id');
                    
                                                                    var modal = $(this);
                                                                    modal.find('#modal-clienteitaid').val(clienteitaid);
                                                                    modal.find('#modal-clienteitanombre').val(clienteitanombre);
                                                                    modal.find('#modal-fechabateria').val(fechabateria);
                                                                    modal.find('#modal-id').val(id);
                                                                    var formAction = '{{ route("admin.informesfinales.reservasmedicas", ":cliente") }}';
                                                                    formAction = formAction.replace(':cliente', clienteitaid);
                                                                    $('#subirinformeFormita2').attr('action', formAction);
                                                                });
                                                            });
                                                        </script>
                                                        <div class="modal-body">
                                                            <div class="row"> 
                                                                <div class="col-lg-6">

                                                                    <div id="acciones-checkboxes">
                                                                    </div>
                                                                    
                                                                </div>
                                                                <script>
                                                                    $(document).on('submit', 'form', function () {
                                                                        // Obtener los checkboxes seleccionados
                                                                        let seleccionadas = $('input[name="acciones[]"]:checked').map(function () {
                                                                            return $(this).val();
                                                                        }).get();
                                                        
                                                                        // Asignar el array de acciones seleccionadas al campo oculto
                                                                        $('#acciones_seleccionadas').val(JSON.stringify(seleccionadas));
                                                        
                                                                        console.log('Acciones seleccionadas:', seleccionadas);
                                                                    });
                                                                </script>
                                                                
                                                                <script>
                                                                    $(document).on('click', '.btn-subirinformeproveedor', function () {
                                                                        let clienteitaid = $(this).data('clienteitaid2'); 
                                                                        let clienteitanombre = $(this).data('clienteitanombre2'); 
                                                                        let fechabateria = $(this).data('fechabateria2'); 

                                                                        $('#clienteitaid2-texto').text(clienteitaid);
                                                                        $('#clienteitanombre2-texto').text(clienteitanombre);
                                                                        $('#fechabateria2-texto').text(fechabateria);
                                                                        $('#acciones-checkboxes').html('<strong>ESTUDIOS / ESPECIALIDADES</strong>');

                                                                        let selectAllCheckbox = 
                                                                            '<div>' +
                                                                                '<input type="checkbox" id="select-all" />' +
                                                                                '<label for="select-all" class="normal-label">Seleccionar todo</label>' +
                                                                            '</div>';
                                                                        $('#acciones-checkboxes').append(selectAllCheckbox);

                                                                        // Verifica si el checkbox de seleccionar todo existe antes de agregar el listener
                                                                        $('#select-all').on('change', function () {
                                                                            let isChecked = $(this).prop('checked');
                                                                            $('#acciones-checkboxes input[type="checkbox"]').not(this).prop('checked', isChecked);
                                                                        });

                                                                        @foreach($reservasmedicas as $reserva)
                                                                            @if(!$reserva->documentacionDisponible)
                                                                                if ({{ $reserva->clienteitaid }} == clienteitaid && '{{ $reserva->fechabateria }}' == fechabateria) {
                                                                                    let checkbox = '<div>' +
                                                                                        '<input type="checkbox" name="acciones[]" value="{{ $reserva->accionnombre }}" id="accion-{{ $reserva->id }}">' +
                                                                                        '<label for="accion-{{ $reserva->id }}">{{ $reserva->accionnombre }}</label>' +
                                                                                    '</div>';
                                                                                    $('#acciones-checkboxes').append(checkbox);
                                                                                }
                                                                                @endif
                                                                        @endforeach
                                                                    });

                                                                    
                                                                </script>
                                                                
                                                                <style>
                                                                    #acciones-checkboxes label {
                                                                        font-weight: normal !important;
                                                                    }
                                                                    .normal-label {
                                                                        font-weight: normal !important;
                                                                    }
                                                                    #acciones-checkboxes h4 {
                                                                        font-weight: bold;
                                                                        margin-bottom: 10px;
                                                                    }
                                                                </style>
                                                                
                                                                <div class="col-lg-6">
                                                                    <div class="row"> 
                                                                        <div class="col-lg-12">
                                                                            <div class="file-upload">
                                                                                <label for="archivoita5">INFORME PDF (Obligatorio):</label>
                                                                                <input type="file" name="archivo" id="archivoita5" class="file-input" accept=".pdf" onchange="handleFileSelectita5(this, 'preview-archivoita5')" />
                                                                                <label for="archivoita5" class="file-custom-label">Elige un PDF</label>
                                                                                <div class="file-preview" id="preview-archivoita5"></div>
                                                                                @error('archivo')
                                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                                    {{$message}}
                                                                                </small>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'PROMED S.R.L.' || $nombreusuario === 'SERRANO PORSTENDOERFER VIVIAN YANETH')
                                                                            <div class="col-lg-12" style="margin-top: 10px; margin-bottom: 10px;">
                                                                                <div class="file-upload">
                                                                                    <label for="picture">IMAGEN 1:</label>
                                                                                    <input type="file" name="picture" id="picture" accept="image/*, .pdf" onchange="handleFileSelectita5(this, 'preview-picture5')" />
                                                                                    <div class="file-preview" id="preview-picture5"></div>
                                                                                    @error('picture')
                                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                                        {{$message}}
                                                                                    </small>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-12">
                                                                                <div class="file-upload">
                                                                                    <label for="picture2">IMAGEN 2:</label>
                                                                                    <input type="file" name="picture2" id="picture52" accept="image/*, .pdf" onchange="handleFileSelectita5(this, 'preview-picture52')" />
                                                                                    <div class="file-preview" id="preview-picture52"></div>
                                                                                    @error('picture2')
                                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                                        {{$message}}
                                                                                    </small>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <script>
                                                                function handleFileSelectita5(input, previewId) {
                                                                    const preview = document.getElementById(previewId);
                                                                    preview.innerHTML = '';
                                                            
                                                                    if (input.files && input.files[0]) {
                                                                        const file = input.files[0];
                                                            
                                                                        if (file.type === 'application/pdf') {
                                                                            const fileName = document.createElement('span');
                                                                            fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                            fileName.className = 'file-name';
                                                                            preview.appendChild(fileName);
                                                                        } else if (file.type.startsWith('image/')) {
                                                                            const fileURL = URL.createObjectURL(file);
                                                                            const img = document.createElement('img');
                                                                            img.src = fileURL;
                                                                            img.style.maxWidth = '100%';
                                                                            img.style.maxHeight = '300px';
                                                                            img.alt = 'Vista previa de la imagen';
                                                                            preview.appendChild(img);
                                                                        } else {
                                                                            const fileName = document.createElement('span');
                                                                            fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                            fileName.className = 'file-name';
                                                                            preview.appendChild(fileName);
                                                                        }
                                                                    } else {
                                                                        preview.textContent = 'No se ha seleccionado ningún archivo';
                                                                    }
                                                                }
                                                            </script>
                                                            
                                                            <div class="row" hidden>
                                                                <div class="col-lg-6 text-center">
                                                                    <label for="firma">Firma Digital:</label>
                                                                    <div>
                                                                        @if ($usuario->firmadigital)
                                                                            <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->firmadigital) }}" 
                                                                                alt="Firma Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                        @else
                                                                            <p>No se ha cargado ninguna firma.</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 text-center">
                                                                    <label for="sello">Sello Digital:</label>
                                                                    <div>
                                                                        @if ($usuario->sellodigital)
                                                                            <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->sellodigital) }}" 
                                                                                alt="Sello Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                        @else
                                                                            <p>No se ha cargado ningún sello.</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="modal-footer">
                                                            <div class="text-center w-100">
                                                                {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear']) !!}
                                                            </div>
                                                        </div> --}}
                                                        <div class="modal-footer">
                                                            <div class="text-center w-100">
                                                                {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear', 'id' => 'btnSubirInforme2']) !!}
                                                            </div>
                                                        </div>

                                                        <script>
                                                            document.addEventListener('DOMContentLoaded', function () {
                                                                const form = document.querySelector('#formdos'); // <- CORREGIDO
                                                                const btn = document.getElementById('btnSubirInforme2');

                                                                form.addEventListener('submit', function(e) {
                                                                    // Cambiar texto del botón
                                                                    btn.value = 'GUARDANDO...';
                                                                    // Deshabilitar el botón
                                                                    btn.disabled = true;
                                                                });
                                                            });
                                                        </script>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $('#subirinformeitaModal').on('show.bs.modal', function (event) {
                                                var button = $(event.relatedTarget);
                                                var clienteitaid = button.data('clienteitaid');
                                                var clienteitanombre = button.data('clienteitanombre');
                                                var fechabateria = button.data('fechabateria');
                                                var accion = button.data('accion');
                                                var id = button.data('id');

                                                var modal = $(this);
                                                modal.find('#modal-clienteitaid').val(clienteitaid);
                                                modal.find('#modal-clienteitanombre').val(clienteitanombre);
                                                modal.find('#modal-fechabateria').val(fechabateria);
                                                modal.find('#modal-accion').val(accion);
                                                modal.find('#modal-id').val(id);
                                                var formAction = '{{ route("admin.asociados.guardardocumentacionclienteitadeproveedor", ":cliente") }}';
                                                formAction = formAction.replace(':cliente', clienteitaid);
                                                $('#subirinformeFormita').attr('action', formAction);
                                            });
                                        });
                                    </script>
                                </tr>
                            @endif
                        @endforeach
                        @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                            @if(!$reservasmedicaauditoria->documentacionDisponibleauditoria && $reservasmedicaauditoria->informeDisponibleauditoria)
                                <tr>
                                    <td>{{$reservasmedicaauditoria->id}}</td>
                                    <td>AUDITORIA</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                    <td hidden>{{$reservasmedicaauditoria->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechaasignada}}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservasmedicaauditoria->horadesde)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasmedicaauditoria->horahasta)->format('H:i') }}</td>

                                    {{-- FICHA MEDICA AUDITORIA --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'YELKA MORALES VELARDE')
                                        <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                            @if($reservasmedicaauditoria->fichamedicaauditoria)
                                                <a href="{{ asset('/fichamedicaclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->fichamedicaauditoria) }}" class="btn btn-sm btn-subirinf" target="_blank" title="VER FICHA MEDICA">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            @else
                                                <a class="btn btn-sm btn-fichamedica" href="{{ route('admin.asociados.crearformularioclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid) }}" title="CREAR FICHA MÉDICA">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            @endif
                                        </td>
                                    @endif
                                    
                                    {{-- DIAGNOSTICO AUDITORIA --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'MARICELA COLQUE SANDOVAL' || $nombreusuario === 'MONICA MACOÑO FLORES' || $nombreusuario === 'YELKA MORALES VELARDE')
                                        <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                            @if($reservasmedicaauditoria->diagnosticomedicoauditoria)
                                                <a href="{{ asset('/diagnosticosauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->diagnosticomedicoauditoria) }}" class="btn btn-sm btn-subirdiagnostico" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-laptop-medical"></i>
                                                </a>
                                            @else
                                            <a href="{{ asset('/diagnosticosauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->diagnosticomedicoauditoria) }}" 
                                                class="btn btn-sm btn-subirdiagnostico disabled" target="_blank" title="VER DIAGNÓSTICO">
                                                 <i class="fas fa-laptop-medical"></i>
                                             </a>
                                            <style>
                                                .btn.disabled {
                                                    pointer-events: none;
                                                    background-color: #d6d6d6;
                                                    color: #a5a5a5;
                                                    border-color: #d6d6d6;
                                                }
                                                .btn.disabled i {
                                                    color: #a5a5a5;
                                                }
                                            </style>

                                            @endif
                                        </td>
                                        <!-- MODAL SUBIR DIAGNOSTICO -->
                                        <div class="modal fade" id="subirdiagnosticoModal2" tabindex="-1" role="dialog" aria-labelledby="subirdiagnosticoModal2Label" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="subirdiagnosticoModal2Label" style="color: #94c93b; font-weight: bold;">DIAGNÓSTICO</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    {!! Form::open(['route' => 'procesar.diagnosticoauditoria', 'method' => 'POST', 'files' => true]) !!}
                                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                    {!! Form::hidden('clienteauditoriaid', null, ['id' => 'modal-clienteauditoriaid']) !!}
                                                    {!! Form::hidden('clienteauditorianombre', null, ['id' => 'modal-clienteauditorianombre']) !!}
                                                    {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                    {!! Form::hidden('accion', 'DIAGNÓSTICO MÉDICO') !!}
    
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-12"> 
                                                                <div class="file-upload">
                                                                    <label for="archivo">DIAGNÓSTICO:</label>
                                                                    <input type="file" name="archivo" id="archivo" accept=".pdf"/>
                                                                    <div class="file-preview" id="preview-archivo2"></div>
                                                                    @error('archivo')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('#subirdiagnosticoModal2').on('show.bs.modal', function (event) {
                                                                var button = $(event.relatedTarget);
                                                                var clienteauditoriaid = button.data('clienteauditoriaid');
                                                                var clienteauditorianombre = button.data('clienteauditorianombre');
                                                                var fechabateria = button.data('fechabateria');
                                                                var accion = button.data('accion');
    
                                                                var modal = $(this);
                                                                modal.find('#modal-clienteauditoriaid').val(clienteauditoriaid);
                                                                modal.find('#modal-clienteauditorianombre').val(clienteauditorianombre);
                                                                modal.find('#modal-fechabateria').val(fechabateria);
                                                                modal.find('#modal-accion').val(accion);
                                                            });
                                                        });
                                                    </script>
                                                    <div class="modal-footer">
                                                        <div class="text-center w-100">
                                                            {!! Form::submit('SUBIR DIAGNÓSTICO', ['class' => 'btn btn-crear']) !!}
                                                        </div>
                                                    </div>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    {{-- CREAR BATERIA AUDITORIA --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'YELKA MORALES VELARDE')
                                        <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crearproveedor" href="{{route('admin.asociados.crearbateriaclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif

                                    {{-- SUBIR INFORME AUDITORIA --}}
                                    <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                        @if($reservasmedicaauditoria->informeDisponibleauditoria)
                                            <abbr title="SUBIR INFORME">
                                                <button type="button" class="btn btn-sm btn-subirinformeproveedor" 
                                                        data-toggle="modal" 
                                                        data-target="#subirinformeModal5"
                                                        data-id="{{ $reservasmedicaauditoria->id }}"
                                                        data-clienteauditoriaid="{{ $reservasmedicaauditoria->clienteauditoriaid }}"
                                                        data-clienteauditorianombre="{{ $reservasmedicaauditoria->clienteauditorianombre }}"
                                                        data-fechabateria="{{ $reservasmedicaauditoria->fechabateria }}"
                                                        data-accion="{{ $reservasmedicaauditoria->accionnombre }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </abbr>
                                        @else
                                            <p class="text-incompleto">FECHA DE ATENCIÓN PENDIENTE</p>
                                        @endif
                                    </td>

                                    <!-- MODAL SUBIR INFORME -->
                                    <div class="modal fade" id="subirinformeModal5" tabindex="-1" role="dialog" aria-labelledby="subirinformeModal5Label" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="subirinformeModal5Label" style="color: #94c93b; font-weight: bold;">SUBIR INFORME</h3>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                {!! Form::open(['route' => 'procesar.informeauditoria', 'method' => 'POST', 'files' => true]) !!}
                                                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                {!! Form::hidden('clienteauditoriaid', null, ['id' => 'modal-clienteauditoriaid']) !!}
                                                {!! Form::hidden('clienteauditorianombre', null, ['id' => 'modal-clienteauditorianombre']) !!}
                                                {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                {!! Form::hidden('accion', null, ['id' => 'modal-accion']) !!}
                                                {!! Form::hidden('programacionid', null, ['id' => 'modal-id']) !!}

                                                <div class="modal-body">
                                                    <div class="row"> 
                                                        <!-- PDF -->
                                                        <div class="col-lg-6">
                                                            <div class="file-upload">
                                                                <label for="archivoauditoria">INFORME PDF (Obligatorio):</label>
                                                                <input type="file" name="archivo" id="archivoauditoria" class="file-input" accept=".pdf" onchange="handleFileSelect(this, 'preview-archivoauditoria')" />
                                                                <label for="archivoauditoria" class="file-custom-label">Elige un PDF</label>
                                                                <div class="file-preview" id="preview-archivoauditoria"></div>
                                                                @error('archivo')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="col-lg-6">
                                                            <div class="file-upload">
                                                                <label for="archivoauditoria">INFORME WORD (Se cargará sin firma):</label>
                                                                <input type="file" name="archivo3" id="archivoauditoriaword" class="file-input" accept=".docx" onchange="handleFileSelectauditoriaword(this, 'preview-archivoauditoriaword')" />
                                                                <label for="archivoauditoriaword" class="file-custom-label">Elige un WORD</label>
                                                                <div class="file-preview" id="preview-archivoauditoriaword"></div>
                                                                @error('archivo3')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'PROMED S.R.L.' || $nombreusuario === 'SERRANO PORSTENDOERFER VIVIAN YANETH')
                                                            <!-- Imagen 1 -->
                                                            <div class="col-lg-6">
                                                                <div class="file-upload">
                                                                    <label for="picture">IMAGEN 1:</label>
                                                                    <input type="file" name="picture" id="picture" accept="image/*" onchange="handleFileSelect(this, 'preview-pictureauditoria1')" />
                                                                    <div class="file-preview" id="preview-pictureauditoria1"></div>
                                                                    @error('picture')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        
                                                            <!-- Imagen 2 -->
                                                            <div class="col-lg-6">
                                                                <div class="file-upload">
                                                                    <label for="picture2">IMAGEN 2:</label>
                                                                    <input type="file" name="picture2" id="picture2" accept="image/*" onchange="handleFileSelect(this, 'preview-pictureauditoria2')" />
                                                                    <div class="file-preview" id="preview-pictureauditoria2"></div>
                                                                    @error('picture2')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <script>
                                                        function handleFileSelect(input, previewId) {
                                                            const preview = document.getElementById(previewId);
                                                            preview.innerHTML = '';
                                                    
                                                            if (input.files && input.files[0]) {
                                                                const file = input.files[0];
                                                    
                                                                if (file.type === 'application/pdf') {
                                                                    const fileName = document.createElement('span');
                                                                    fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                    fileName.className = 'file-name';
                                                                    preview.appendChild(fileName);
                                                                } else if (file.type.startsWith('image/')) {
                                                                    const fileURL = URL.createObjectURL(file);
                                                                    const img = document.createElement('img');
                                                                    img.src = fileURL;
                                                                    img.style.maxWidth = '100%';
                                                                    img.style.maxHeight = '300px';
                                                                    img.alt = 'Vista previa de la imagen';
                                                                    preview.appendChild(img);
                                                                } else {
                                                                    const fileName = document.createElement('span');
                                                                    fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                    fileName.className = 'file-name';
                                                                    preview.appendChild(fileName);
                                                                }
                                                            } else {
                                                                preview.textContent = 'No se ha seleccionado ningún archivo';
                                                            }
                                                        }

                                                        function handleFileSelectauditoriaword(input, previewId) {
                                                            const preview = document.getElementById(previewId);
                                                            preview.innerHTML = '';

                                                            if (input.files && input.files[0]) {
                                                                const file = input.files[0];

                                                                if (file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                                                                    // Muestra solo el nombre del archivo Word
                                                                    const fileName = document.createElement('span');
                                                                    fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                    fileName.className = 'file-name';
                                                                    preview.appendChild(fileName);
                                                                } else if (file.type.startsWith('image/')) {
                                                                    // Muestra la vista previa de la imagen
                                                                    const fileURL = URL.createObjectURL(file);
                                                                    const img = document.createElement('img');
                                                                    img.src = fileURL;
                                                                    img.style.maxWidth = '100%';
                                                                    img.style.maxHeight = '300px';
                                                                    img.alt = 'Vista previa de la imagen';
                                                                    preview.appendChild(img);
                                                                } else {
                                                                    const fileName = document.createElement('span');
                                                                    fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                    fileName.className = 'file-name';
                                                                    preview.appendChild(fileName);
                                                                }
                                                            } else {
                                                                preview.textContent = 'No se ha seleccionado ningún archivo';
                                                            }
                                                        }
                                                    </script>
                                                    <div class="row" hidden>
                                                        <div class="col-lg-6 text-center">
                                                            <label for="firma">Firma Digital:</label>
                                                            <div>
                                                                @if ($usuario->firmadigital)
                                                                    <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->firmadigital) }}" 
                                                                         alt="Firma Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                @else
                                                                    <p>No se ha cargado ninguna firma.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 text-center">
                                                            <label for="sello">Sello Digital:</label>
                                                            <div>
                                                                @if ($usuario->sellodigital)
                                                                    <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->sellodigital) }}" 
                                                                         alt="Sello Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                @else
                                                                    <p>No se ha cargado ningún sello.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="text-center w-100">
                                                        {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear']) !!}
                                                    </div>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- SUBIR INFORME MULTIPLE AUDITORIA --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'PROMED S.R.L.' || $nombreusuario === 'SERRANO PORSTENDOERFER VIVIAN YANETH')
                                        <td width="10px" style="padding-left: padding-right: 4px; justify-content: center;">
                                            @if($reservasmedicaauditoria->informeDisponibleauditoria)
                                                <abbr title="SUBIR INFORME MULTIPLE">
                                                    <button type="button" class="btn btn-sm btn-subirinformeproveedor3" 
                                                            data-toggle="modal" 
                                                            data-target="#subirinformeauditoriaModal2"
                                                            data-id="{{ $reservasmedicaauditoria->id }}"
                                                            data-clienteauditoriaid5="{{ $reservasmedicaauditoria->clienteauditoriaid }}"
                                                            data-clienteauditorianombre5="{{ $reservasmedicaauditoria->clienteauditorianombre }}"
                                                            data-fechabateria5="{{ $reservasmedicaauditoria->fechabateria }}"
                                                            data-accion5="{{ $reservasmedicaauditoria->accionnombre }}">
                                                            <i class="fas fa-share-square"></i>
                                                    </button>
                                                </abbr>
                                            @else
                                                <p class="text-incompleto">FECHA DE ATENCIÓN PENDIENTE</p>
                                            @endif
                                        </td>
                                        <!-- MODAL SUBIR INFORME MULTIPLE AUDITORIA -->
                                        <div class="modal fade" id="subirinformeauditoriaModal2" tabindex="-1" role="dialog" aria-labelledby="subirinformeauditoriaModal2Label" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <div class="container text-center">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <h3 class="modal-title" id="subirinformeauditoriaModal2Label" style="color: #94c93b; font-weight: bold;">
                                                                        SUBIR INFORME
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <p><strong>CLIENTE:</strong> <span id="clienteauditorianombre5-texto"></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <script>
                                                            $(document).on('click', '.btn-subirinformeproveedor3', function () {
                                                                let clienteauditorianombre5 = $(this).data('clienteauditorianombre5');
                                                                let accionnombre5 = $(this).data('accion5');
    
                                                                $('#clienteauditorianombre5-texto').text(clienteauditorianombre5);
                                                                $('#accionnombre5-texto').text(accionnombre5);
                                                            });
                                                        </script>
    
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>

                                                    {!! Form::open(['route' => 'procesar.informe.multiple.auditoria', 'method' => 'POST', 'files' => true]) !!}
                                                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                        {!! Form::hidden('clienteauditoriaid', null, ['id' => 'modal-clienteauditoriaid']) !!}
                                                        {!! Form::hidden('clienteauditorianombre', null, ['id' => 'modal-clienteauditorianombre']) !!}
                                                        {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                        {!! Form::hidden('acciones_seleccionadas2', null, ['id' => 'acciones_seleccionadas2']) !!}
                                                        <script>
                                                            $(document).ready(function() {
                                                                $('#subirinformeauditoriaModal2').on('show.bs.modal', function (event) {
                                                                    var button = $(event.relatedTarget);
                                                                    var clienteauditoriaid = button.data('clienteauditoriaid5');
                                                                    var clienteauditorianombre = button.data('clienteauditorianombre5');
                                                                    var fechabateria = button.data('fechabateria5');
                                                                    var id = button.data('id');
                    
                                                                    var modal = $(this);
                                                                    modal.find('#modal-clienteauditoriaid').val(clienteauditoriaid);
                                                                    modal.find('#modal-clienteauditorianombre').val(clienteauditorianombre);
                                                                    modal.find('#modal-fechabateria').val(fechabateria);
                                                                    modal.find('#modal-id').val(id);
                                                                    var formAction = '{{ route("admin.informesfinales.reservasmedicas", ":cliente") }}';
                                                                    formAction = formAction.replace(':cliente', clienteauditoriaid);
                                                                    $('#subirinformeFormauditoria2').attr('action', formAction);
                                                                });
                                                            });
                                                        </script>
                                                        <div class="modal-body">
                                                            <div class="row"> 
                                                                <div class="col-lg-6">

                                                                    <div id="acciones-checkboxes3">
                                                                    </div>
                                                                    
                                                                </div>
                                                                <script>
                                                                    $(document).on('submit', 'form', function () {
                                                                        let seleccionadas = $('input[name="acciones[]"]:checked').map(function () {
                                                                            return $(this).val();
                                                                        }).get();

                                                                        $('#acciones_seleccionadas2').val(JSON.stringify(seleccionadas));
                                                        
                                                                        console.log('Acciones seleccionadas:', seleccionadas);
                                                                    });
                                                                </script>
                                                                
                                                                <script>
                                                                    $(document).on('click', '.btn-subirinformeproveedor3', function () {
                                                                        let clienteauditoriaid = $(this).data('clienteauditoriaid5'); 
                                                                        let clienteauditorianombre = $(this).data('clienteauditorianombre5'); 
                                                                        let fechabateria = $(this).data('fechabateria5'); 

                                                                        $('#clienteauditoriaid5-texto').text(clienteauditoriaid);
                                                                        $('#clienteauditorianombre5-texto').text(clienteauditorianombre);
                                                                        $('#fechabateria5-texto').text(fechabateria);
                                                                        $('#acciones-checkboxes3').html('<strong>ESTUDIOS / ESPECIALIDADES</strong>');

                                                                        let selectAllCheckbox = 
                                                                            '<div>' +
                                                                                '<input type="checkbox" id="select-all" />' +
                                                                                '<label for="select-all" class="normal-label">Seleccionar todo</label>' +
                                                                            '</div>';
                                                                        $('#acciones-checkboxes3').append(selectAllCheckbox);

                                                                        // Verifica si el checkbox de seleccionar todo existe antes de agregar el listener
                                                                        $('#select-all').on('change', function () {
                                                                            let isChecked = $(this).prop('checked');
                                                                            $('#acciones-checkboxes3 input[type="checkbox"]').not(this).prop('checked', isChecked);
                                                                        });

                                                                        @foreach($reservasmedicasauditorias as $reservaauditoria)
                                                                            @if(!$reservaauditoria->documentacionDisponibleauditoria)
                                                                            if ('{{ $reservaauditoria->clienteauditoriaid }}' == clienteauditoriaid && '{{ $reservaauditoria->fechabateria }}' == fechabateria) {
                                                                                let checkbox = '<div>' +
                                                                                    '<input type="checkbox" name="acciones[]" value="{{ $reservaauditoria->accionnombre }}" id="accion-{{ $reservaauditoria->id }}">' +
                                                                                    '<label for="accion-{{ $reservaauditoria->id }}">{{ $reservaauditoria->accionnombre }}</label>' +
                                                                                '</div>';
                                                                                $('#acciones-checkboxes3').append(checkbox);
                                                                            }
                                                                            @endif
                                                                        @endforeach

                                                                    });

                                                                    
                                                                </script>
                                                                
                                                                <style>
                                                                    #acciones-checkboxes3 label {
                                                                        font-weight: normal !important;
                                                                    }
                                                                    .normal-label {
                                                                        font-weight: normal !important;
                                                                    }
                                                                    #acciones-checkboxes3 h4 {
                                                                        font-weight: bold;
                                                                        margin-bottom: 10px;
                                                                    }
                                                                </style>
                                                                
                                                                <div class="col-lg-6">
                                                                    <div class="row"> 
                                                                        <div class="col-lg-12">
                                                                            <div class="file-upload">
                                                                                <label for="archivoauditoria5">INFORME PDF (Obligatorio):</label>
                                                                                <input type="file" name="archivo" id="archivoauditoria5" class="file-input" accept=".pdf" onchange="handleFileSelectauditoria5(this, 'preview-archivoauditoria5')" />
                                                                                <label for="archivoauditoria5" class="file-custom-label">Elige un PDF</label>
                                                                                <div class="file-preview" id="preview-archivoauditoria5"></div>
                                                                                @error('archivo')
                                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                                    {{$message}}
                                                                                </small>
                                                                                @enderror
                                                                            </div>
                                                                        </div>

                                                                        @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'PROMED S.R.L.' || $nombreusuario === 'SERRANO PORSTENDOERFER VIVIAN YANETH')
                                                                            <div class="col-lg-12" style="margin-top: 10px; margin-bottom: 10px;">
                                                                                <div class="file-upload">
                                                                                    <label for="picture">IMAGEN 1:</label>
                                                                                    <input type="file" name="picture" id="picture" accept="image/*, .pdf" onchange="handleFileSelectauditoria5(this, 'preview-pictureauditoria5')" />
                                                                                    <div class="file-preview" id="preview-pictureauditoria5"></div>
                                                                                    @error('picture')
                                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                                        {{$message}}
                                                                                    </small>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-12">
                                                                                <div class="file-upload">
                                                                                    <label for="picture2">IMAGEN 2:</label>
                                                                                    <input type="file" name="picture2" id="pictureauditoria52" accept="image/*, .pdf" onchange="handleFileSelectauditoria5(this, 'preview-pictureauditoria52')" />
                                                                                    <div class="file-preview" id="preview-pictureauditoria52"></div>
                                                                                    @error('picture2')
                                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                                        {{$message}}
                                                                                    </small>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <script>
                                                                function handleFileSelectauditoria5(input, previewId) {
                                                                    const preview = document.getElementById(previewId);
                                                                    preview.innerHTML = '';
                                                            
                                                                    if (input.files && input.files[0]) {
                                                                        const file = input.files[0];
                                                            
                                                                        if (file.type === 'application/pdf') {
                                                                            const fileName = document.createElement('span');
                                                                            fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                            fileName.className = 'file-name';
                                                                            preview.appendChild(fileName);
                                                                        } else if (file.type.startsWith('image/')) {
                                                                            const fileURL = URL.createObjectURL(file);
                                                                            const img = document.createElement('img');
                                                                            img.src = fileURL;
                                                                            img.style.maxWidth = '100%';
                                                                            img.style.maxHeight = '300px';
                                                                            img.alt = 'Vista previa de la imagen';
                                                                            preview.appendChild(img);
                                                                        } else {
                                                                            const fileName = document.createElement('span');
                                                                            fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                            fileName.className = 'file-name';
                                                                            preview.appendChild(fileName);
                                                                        }
                                                                    } else {
                                                                        preview.textContent = 'No se ha seleccionado ningún archivo';
                                                                    }
                                                                }
                                                            </script>
                                                            
                                                            <div class="row" hidden>
                                                                <div class="col-lg-6 text-center">
                                                                    <label for="firma">Firma Digital:</label>
                                                                    <div>
                                                                        @if ($usuario->firmadigital)
                                                                            <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->firmadigital) }}" 
                                                                                alt="Firma Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                        @else
                                                                            <p>No se ha cargado ninguna firma.</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 text-center">
                                                                    <label for="sello">Sello Digital:</label>
                                                                    <div>
                                                                        @if ($usuario->sellodigital)
                                                                            <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->sellodigital) }}" 
                                                                                alt="Sello Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                        @else
                                                                            <p>No se ha cargado ningún sello.</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <div class="text-center w-100">
                                                                {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear']) !!}
                                                            </div>
                                                        </div>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $('#subirinformeModal5').on('show.bs.modal', function (event) {
                                                var button = $(event.relatedTarget);
                                                var clienteauditoriaid = button.data('clienteauditoriaid');
                                                var clienteauditorianombre = button.data('clienteauditorianombre');
                                                var fechabateria = button.data('fechabateria');
                                                var accion = button.data('accion');
                                                var id = button.data('id');

                                                var modal = $(this);
                                                modal.find('#modal-clienteauditoriaid').val(clienteauditoriaid);
                                                modal.find('#modal-clienteauditorianombre').val(clienteauditorianombre);
                                                modal.find('#modal-fechabateria').val(fechabateria);
                                                modal.find('#modal-accion').val(accion);
                                                modal.find('#modal-id').val(id);
                                                var formAction = '{{ route("admin.asociados.guardardocumentacionclienteauditoriadeproveedor", ":clienteauditoria") }}';
                                                formAction = formAction.replace(':cliente', clienteauditoriaid);
                                                $('#subirinformeFormauditoria').attr('action', formAction);
                                            });
                                        });
                                    </script>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- INFORMES COMPLETOS --}}
        <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tipo_Cli.</th>
                            <th>ID_Cli.</th>
                            <th>Cliente</th>
                            <th hidden>Fecha_Bateria</th>
                            @if ($rolusuario !== 'PROVEEDOR')
                            <th>Proveedor_Asignado</th>
                            @endif
                            <th>Estudio/Especialidad</th>
                            <th>Fecha_Asignada</th>
                            <th>Hora_Asignada</th>
                            <th>Fecha_Registro</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservasmedicas as $reservasmedica)
                            @if($reservasmedica->documentacionDisponible)
                                <tr>
                                    <td>ITA</td>
                                    <td>{{$reservasmedica->clienteitaid}}</td>
                                    <td>{{$reservasmedica->clienteitanombre}}</td>
                                    <td hidden>{{$reservasmedica->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedica->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedica->accionnombre}}</td>
                                    <td>{{$reservasmedica->fechaasignada}}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservasmedica->horadesde)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasmedica->horahasta)->format('H:i') }}</td>
                                    <td>{{$reservasmedica->fechainforme}}</td>
                                    
                                    <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                        <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->documentacionDisponible) }}" class="btn btn-sm btn-verdocumentacion2" target="_blank" title="VER INFORME MÉDICO">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        @if($reservasmedica->imagen1Disponible)
                                            <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->imagen1Disponible) }}" class="btn btn-verdocumentacion2" target="_blank" title="VER IMAGEN 1">
                                                <i class="fas fa-images"></i>
                                            </a>
                                        @endif
                                        @if($reservasmedica->imagen2Disponible)
                                            <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->imagen2Disponible) }}" class="btn btn-verdocumentacion2" target="_blank" title="VER IMAGEN 2">
                                                <i class="far fa-images"></i>
                                            </a>
                                        @endif
                                    </td>  
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $nombreusuario === 'YELKA MORALES VELARDE')
                                        <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crear2" href="{{route('admin.asociados.crearbateriaclienteita', $reservasmedica->clienteitaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif

                                    <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                        @if ($reservasmedica->documentacionfirmadaDisponible)
                                            <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->documentacionfirmadaDisponible) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME MÉDICO FIRMADO">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @else
                                        @endif
                                    </td>

                                    <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                        @if($reservasmedica->documentacionworditaDisponible)
                                            <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->documentacionworditaDisponible) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME MÉDICO WORD">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @else
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                            @if($reservasmedicaauditoria->documentacionDisponibleauditoria)
                                <tr>
                                    <td>AUDITORIA</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                    <td hidden>{{$reservasmedicaauditoria->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechaasignada}}</td>
                                    <td>{{ \Carbon\Carbon::parse($reservasmedicaauditoria->horadesde)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasmedicaauditoria->horahasta)->format('H:i') }}</td>
                                    <td>{{$reservasmedicaauditoria->fechainformeauditoria}}</td>
                                    
                                    <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                        <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->documentacionDisponibleauditoria) }}" class="btn btn-sm btn-verdocumentacion2" target="_blank" title="VER INFORME MÉDICO">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        @if($reservasmedicaauditoria->imagen1Disponibleauditoria)
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->imagen1Disponibleauditoria) }}" class="btn btn-verdocumentacion2" target="_blank" title="VER IMAGEN 1">
                                                <i class="fas fa-images"></i>
                                            </a>
                                        @endif
                                        @if($reservasmedicaauditoria->imagen2Disponibleauditoria)
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->imagen2Disponibleauditoria) }}" class="btn btn-verdocumentacion2" target="_blank" title="VER IMAGEN 2">
                                                <i class="far fa-images"></i>
                                            </a>
                                        @endif 
                                    </td> 
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $nombreusuario === 'YELKA MORALES VELARDE')
                                    <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crear2" href="{{route('admin.asociados.crearbateriaclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif  

                                    <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                        @if ($reservasmedicaauditoria->documentacionfirmadaauditoriaDisponible)
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->documentacionfirmadaauditoriaDisponible) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME MÉDICO FIRMADO">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @else
                                        @endif
                                    </td>

                                    <td width="10px" style="padding-left: 2px; padding-right: 2px; justify-content: center;">
                                        @if($reservasmedicaauditoria->documentacionwordauditoriaDisponible)
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->documentacionwordauditoriaDisponible) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME MÉDICO WORD">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @else
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGOS MENSUALES --}}
        {{-- <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">

            @if ($rolusuario !== 'PROVEEDOR')
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid justify-content-end">
                    <div class="d-flex flex-wrap align-items-center">
                        <form id="search-form" action="{{ route('buscarporproveedor') }}" method="get" class="form-inline">
                            <div class="flex-grow-1">
                                <select name="proveedor" class="form-control mr-sm-2">
                                    <option value="">Seleccionar Proveedor</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->proveedornombre }}" {{ request('proveedor') == $proveedor->proveedornombre ? 'selected' : '' }}>
                                            {{ $proveedor->proveedornombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                            <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" type="button">Mostrar Todo</button>
                        </form>
                    </div>
                </div>
            </nav>
            @endif
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                        window.location.href = "{{ route('buscarporproveedor') }}";
                    });
            
                    const activeTabId = localStorage.getItem('activeTab') || 'tab-1';
                    const tabLink = document.querySelector(`a[href="#${activeTabId}"]`);
                    if (tabLink) {
                        tabLink.click();
                    }
                    document.querySelectorAll('#myTabs .nav-link').forEach(function(link) {
                        link.addEventListener('click', function() {
                            const href = this.getAttribute('href');
                            const tabId = href.substring(1);
                            localStorage.setItem('activeTab', tabId);
                        });
                    });
                });
            </script>
            <script>
                $(document).ready(function() {
                    $('input[name="buscarporproveedor"]').on('keyup change', function() {
                        var proveedorSeleccionado = $('input[name="buscarporproveedor"]').val();
                        var botonBuscar = $('#btn-buscar');
                        
                        if (proveedorSeleccionado.trim() === '') {
                            botonBuscar.prop('disabled', true);
                        } else {
                            botonBuscar.prop('disabled', false);
                        }
                    });
                });
            </script>
            <!-- Pestañas de Navegación -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @php
                    use Carbon\Carbon;
                    Carbon::setLocale('es');
                    $currentMonth = Carbon::now()->month;
                    $currentYear = Carbon::now()->year;
                    $currentMonthName = Carbon::now()->translatedFormat('F Y');
                    $currentMonthName = strtoupper($currentMonthName);

                    $previousMonth = Carbon::now()->subMonth()->month;
                    $previousYear = Carbon::now()->subMonth()->year;
                    $previousMonthName = Carbon::now()->subMonth()->translatedFormat('F Y');
                    $previousMonthName = strtoupper($previousMonthName);
                @endphp

                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="previous-month-tab" data-toggle="tab" href="#previous-month" role="tab" aria-controls="previous-month" aria-selected="true">{{ $previousMonthName }}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="current-month-tab" data-toggle="tab" href="#current-month" role="tab" aria-controls="current-month" aria-selected="true">{{ $currentMonthName }}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="pagosaprobadostab" data-toggle="tab" href="#pagosaprobados" role="tab" aria-controls="pagosaprobados" aria-selected="true">PAGOS APROBADOS</a>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="myTabContent">

                <!-- Pestaña Mes Pasado -->
                <div class="tab-pane fade show active" id="previous-month" role="tabpanel" aria-labelledby="previous-month-tab">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Cliente</th>
                                    <th>Cliente</th>
                                    <th>Fecha Bateria</th>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <th>Proveedor Asignado</th>
                                    @endif
                                    <th>Acción</th>
                                    <th>Fecha Informe</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalPreviousMonth = 0;
                                    $previousMonth = Carbon::now()->subMonth()->month;
                                    $previousYear = Carbon::now()->subMonth()->year;
                                @endphp

                                @foreach ($reservasmedicas as $reservasmedica)
                                    @if($reservasmedica->documentacionDisponible)
                                        @php
                                            $fechaInforme = Carbon::parse($reservasmedica->fechainforme);
                                        @endphp

                                        @if($fechaInforme->month == $previousMonth && $fechaInforme->year == $previousYear)
                                            @php
                                                $totalPreviousMonth += $reservasmedica->precio;
                                            @endphp
                                            <tr>
                                                <td>{{$reservasmedica->clienteitaid}}</td>
                                                <td>{{$reservasmedica->clienteitanombre}}</td>
                                                <td>{{$reservasmedica->fechabateria}}</td>
                                                @if ($rolusuario !== 'PROVEEDOR')
                                                <td>{{$reservasmedica->proveedornombre}}</td>
                                                @endif
                                                <td>{{$reservasmedica->accionnombre}}</td>
                                                <td>{{ $fechaInforme->format('Y-m-d') }}</td>
                                                <td>{{ number_format($reservasmedica->precio, 2) }}</td>                              
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                                @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                                    @if($reservasmedicaauditoria->documentacionDisponible)
                                        @php
                                            $fechaInforme = Carbon::parse($reservasmedicaauditoria->fechainforme);
                                        @endphp

                                        @if($fechaInforme->month == $previousMonth && $fechaInforme->year == $previousYear)
                                            @php
                                                $totalPreviousMonth += $reservasmedicaauditoria->precio;
                                            @endphp
                                            <tr>
                                                <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                                <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                                <td>{{$reservasmedicaauditoria->fechabateria}}</td>
                                                @if ($rolusuario !== 'PROVEEDOR')
                                                <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                                @endif
                                                <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                                <td>{{ $fechaInforme->format('Y-m-d') }}</td>
                                                <td>{{ number_format($reservasmedicaauditoria->precio, 2) }}</td>                              
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            </tbody>
                            @if ($rolusuario === 'PROVEEDOR')
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right" style="background: #fff4e4"><strong>Total:</strong></td>
                                    <td style="background: #fff4e4"><strong>{{ number_format($totalPreviousMonth, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                            @endif
                            @if ($rolusuario !== 'PROVEEDOR')
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-right" style="background: #fff4e4"><strong>Total:</strong></td>
                                    <td style="background: #fff4e4"><strong>{{ number_format($totalPreviousMonth, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Pestaña Mes Actual -->
                <div class="tab-pane fade" id="current-month" role="tabpanel" aria-labelledby="current-month-tab">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Cliente</th>
                                    <th>Cliente</th>
                                    <th>Fecha Bateria</th>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <th>Proveedor Asignado</th>
                                    @endif
                                    <th>Acción</th>
                                    <th>Fecha Informe</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalCurrentMonth = 0;
                                    $currentMonth = Carbon::now()->month;
                                    $currentYear = Carbon::now()->year;
                                @endphp

                                @foreach ($reservasmedicas as $reservasmedica)
                                    @if($reservasmedica->documentacionDisponible)
                                        @php
                                            $fechaInforme = Carbon::parse($reservasmedica->fechainforme);
                                        @endphp

                                        @if($fechaInforme->month == $currentMonth && $fechaInforme->year == $currentYear)
                                            @php
                                                $totalCurrentMonth += $reservasmedica->precio;
                                            @endphp
                                            <tr>
                                                <td>{{$reservasmedica->clienteitaid}}</td>
                                                <td>{{$reservasmedica->clienteitanombre}}</td>
                                                <td>{{$reservasmedica->fechabateria}}</td>
                                                @if ($rolusuario !== 'PROVEEDOR')
                                                <td>{{$reservasmedica->proveedornombre}}</td>
                                                @endif
                                                <td>{{$reservasmedica->accionnombre}}</td>
                                                <td>{{ $fechaInforme->format('Y-m-d') }}</td>
                                                <td>{{ number_format($reservasmedica->precio, 2) }}</td>                              
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                                @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                                    @if($reservasmedicaauditoria->documentacionDisponible)
                                        @php
                                            $fechaInforme = Carbon::parse($reservasmedicaauditoria->fechainforme);
                                        @endphp

                                        @if($fechaInforme->month == $currentMonth && $fechaInforme->year == $currentYear)
                                            @php
                                                $totalCurrentMonth += $reservasmedicaauditoria->precio;
                                            @endphp
                                            <tr>
                                                <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                                <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                                <td>{{$reservasmedicaauditoria->fechabateria}}</td>
                                                @if ($rolusuario !== 'PROVEEDOR')
                                                <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                                @endif
                                                <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                                <td>{{ $fechaInforme->format('Y-m-d') }}</td>
                                                <td>{{ number_format($reservasmedicaauditoria->precio, 2) }}</td>                              
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            </tbody>
                            @if ($rolusuario === 'PROVEEDOR')
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right" style="background: #fff4e4"><strong>Total:</strong></td>
                                    <td style="background: #fff4e4"><strong>{{ number_format($totalCurrentMonth, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                            @endif
                            @if ($rolusuario !== 'PROVEEDOR')
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-right" style="background: #fff4e4"><strong>Total:</strong></td>
                                    <td style="background: #fff4e4"><strong>{{ number_format($totalCurrentMonth, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Pestaña Pagos aprobados -->
                <div class="tab-pane fade" id="pagosaprobados" role="tabpanel" aria-labelledby="pagosaprobadostab">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Cliente</th>
                                    <th>Cliente</th>
                                    <th>Fecha Bateria</th>
                                    <th>Proveedor Asignado</th>
                                    <th>Acción</th>
                                    <th>Fecha Informe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservasmedicas as $reservasmedica)
                                <tr>
                                    <td>{{$reservasmedica->clienteitaid}}</td>
                                    <td>{{$reservasmedica->clienteitanombre}}</td>
                                    <td>{{$reservasmedica->fechabateria}}</td>
                                    <td>{{$reservasmedica->proveedornombre}}</td>
                                    <td>{{$reservasmedica->accionnombre}}</td>
                                    <td>{{ $fechaInforme->format('Y-m-d') }}</td>                           
                                </tr>
                                @endforeach
                                @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                                <tr>
                                    <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechabateria}}</td>
                                    <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                    <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                    <td>{{ $fechaInforme->format('Y-m-d') }}</td>                           
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>
@stop

@section('js')
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

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

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
      'success')
    </script>
    @endif

    <script>
        $('.formulario-eliminar').submit(function(e){
            e.preventDefault();
    
            Swal.fire({
            title: '¿Estás seguro?',
            text: "Este perfil se eliminará definitivamente",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '¡Si, eliminar!',
            cancelButtonText: 'Cancelar'
            }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
            }) 
        });
        $(document).ready(function() {
            $('input[name="buscarpor"]').on('keyup', function() {
                var query = $(this).val();
                var botonBuscar = $('#btn-buscar');
                if (query.trim() === '') {
                    botonBuscar.prop('disabled', true);
                } else {
                    botonBuscar.prop('disabled', false);
                }
            });
        });
    </script>
    
@endsection
