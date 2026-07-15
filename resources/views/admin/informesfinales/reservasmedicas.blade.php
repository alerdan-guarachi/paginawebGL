@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-secondary" href="{{ route('admin.informesfinales.historialreservasmedicas') }}">VER HISTORIAL DE RESERVAS</a>
<h1>RESERVAS MÉDICAS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/reservasmedicas.css') }}">
<style>
    .table td {
        padding: 5px 10px;;
    }
    .btn.disabled {
        pointer-events: none;
        background-color: #d6d6d6;
        color: #a5a5a5;
        border-color: #d6d6d6;
    }
    .btn.disabled i {
        color: #a5a5a5;
    }
    .dropdown-menu {
        min-width: 260px;
    }

    .dropdown-item {
        white-space: nowrap;
    }
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
    #acciones-checkboxes2 label {
        font-weight: normal !important;
    }
    #acciones-checkboxes2 h4 {
        font-weight: bold;
        margin-bottom: 10px;
    }
    #acciones-checkboxes4 label {
        font-weight: normal !important;
    }
    #acciones-checkboxes4 h4 {
        font-weight: bold;
        margin-bottom: 10px;
    }
    .info-box-modal {
        background: #f1f3f5;
        border: 1px solid #dcdcdc;
        border-radius: 8px;
        padding: 10px 15px;
        margin-top: 10px;
    }
    .table-responsive {
        overflow: visible !important;
    }
</style>

@php
    $usuariosFicha = [
        'CARLOS ALEJANDRO GUARACHI SANDOVAL',
        'DENISSE MAUREN LOPEZ FLORES',
        'AGUIRRE VASQUEZ MARIA RENEE',
        'YELKA MORALES VELARDE'
    ];

    $usuariosDiagnostico = [
        'CARLOS ALEJANDRO GUARACHI SANDOVAL',
        'DENISSE MAUREN LOPEZ FLORES',
        'AGUIRRE VASQUEZ MARIA RENEE',
        'MARICELA COLQUE SANDOVAL',
        'MONICA MACOÑO FLORES',
        'YELKA MORALES VELARDE'
    ];

    $usuariosMultiple = [
        'CARLOS ALEJANDRO GUARACHI SANDOVAL',
        'DENISSE MAUREN LOPEZ FLORES',
        'PROMED S.R.L.',
        'SERRANO PORSTENDOERFER VIVIAN YANETH',
        'MARIA RENEE MONTENEGRO ORELLANA'
    ];

    $usuariosAdjuntarImagenes = [
        'CARLOS ALEJANDRO GUARACHI SANDOVAL',
        'DENISSE MAUREN LOPEZ FLORES',
        'PROMED S.R.L.',
        'SERRANO PORSTENDOERFER VIVIAN YANETH',
        'MARIA RENEE MONTENEGRO ORELLANA'
    ];

@endphp
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
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid justify-content-end">
            <div class="d-flex flex-wrap align-items-center">
                <form id="search-form" action="{{ route('buscarclientereservamedica') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
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
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
        {{-- ATENCION PENDIENTE --}}
        <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm">
                    <thead class="table-secondary">
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
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" 
                                                        href="{{ route('generar.pdf.orden', [
                                                                'clienteitaid' => $reservasmedica->clienteitaid,
                                                                'fechabateria' => $reservasmedica->fechabateria,
                                                                'proveedornombre' => $reservasmedica->proveedornombre ?? 'N/A',
                                                                'clienteitanombre' => urlencode($reservasmedica->clienteitanombre)
                                                        ]) }}">
                                                        <i class="fas fa-file mr-2"></i> Generar Orden
                                                    </a>
                                                    @if($reservasmedica->ciasegurado && $reservasmedica->ciasegurado !== 'PENDIENTE')
                                                        <a class="dropdown-item" 
                                                        href="{{ asset('/requisitosclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->ciasegurado) }}" 
                                                        target="_blank">
                                                            <i class="fas fa-address-book mr-2"></i> Ver CI
                                                        </a>
                                                    @else
                                                        <span class="dropdown-item text-muted">
                                                            <i class="fas fa-address-book mr-2"></i> Ver CI (No disponible)
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
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
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" 
                                                        href="{{ route('generar.pdf.ordenauditoria', [
                                                                'clienteauditoriaid' => $reservasmedicaauditoria->clienteauditoriaid,
                                                                'fechabateria' => $reservasmedicaauditoria->fechabateria,
                                                                'proveedornombre' => $reservasmedicaauditoria->proveedornombre ?? 'N/A',
                                                                'clienteauditorianombre' => urlencode($reservasmedicaauditoria->clienteauditorianombre)
                                                        ]) }}">
                                                        <i class="fas fa-file mr-2"></i> Generar Orden
                                                    </a>
                                                    @if($reservasmedicaauditoria->ciasegurado && $reservasmedicaauditoria->ciasegurado !== 'PENDIENTE')
                                                        <a class="dropdown-item" 
                                                        href="{{ asset('/requisitosclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->ciasegurado) }}" 
                                                        target="_blank">
                                                            <i class="fas fa-address-book mr-2"></i> Ver CI
                                                        </a>
                                                    @else
                                                        <span class="dropdown-item text-muted">
                                                            <i class="fas fa-address-book mr-2"></i> Ver CI (No disponible)
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
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
                <table class="table table-striped table-bordered table-sm">
                    <thead class="table-secondary">
                        <tr>
                            <th>ID_Prog.</th>
                            <th>Tipo_Cli.</th>
                            <th>ID_Cli.</th>
                            <th>Cliente</th>
                            <th hidden>Fecha_Bateria</th>
                            @if ($rolusuario !== 'PROVEEDOR')
                            <th>Proveedor_Atención</th>
                            @endif
                            <th>Estudio/Especialidad</th>
                            <th>Fecha_Atención</th>
                            <th>Hora_Atención</th>
                            <th>Acciones</th>
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
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                {{-- FICHA MEDICA --}}
                                                @if(in_array($nombreusuario, $usuariosFicha))
                                                    @if($reservasmedica->fichamedicaita)
                                                        <a class="dropdown-item" target="_blank"
                                                        href="{{ asset('/fichamedicaclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->fichamedicaita) }}">
                                                            <i class="fas fa-file-signature mr-2"></i> Ver Ficha Médica
                                                        </a>
                                                    @else
                                                        <a class="dropdown-item"
                                                        href="{{ route('admin.asociados.crearformularioclienteita', $reservasmedica->clienteitaid) }}">
                                                            <i class="fas fa-file-signature mr-2"></i> Crear Ficha Médica
                                                        </a>
                                                    @endif
                                                @endif

                                                {{-- DIAGNOSTICO --}}
                                                @if(in_array($nombreusuario, $usuariosDiagnostico))
                                                    @if($reservasmedica->diagnosticomedicoita)
                                                        <a class="dropdown-item" target="_blank"
                                                        href="{{ asset('/diagnosticos/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->diagnosticomedicoita) }}">
                                                            <i class="fas fa-laptop-medical mr-2"></i> Ver Diagnóstico
                                                        </a>
                                                    @else
                                                        <span class="dropdown-item text-muted">
                                                            <i class="fas fa-laptop-medical mr-2"></i> Diagnóstico (No Disponible)
                                                        </span>
                                                    @endif
                                                @endif

                                                {{-- CREAR BATERIA --}}
                                                @if(in_array($nombreusuario, $usuariosFicha))
                                                    <a class="dropdown-item"
                                                    href="{{ route('admin.asociados.crearbateriaclienteita', $reservasmedica->clienteitaid) }}">
                                                        <i class="fas fa-charging-station mr-2"></i> Crear Batería
                                                    </a>
                                                @endif

                                                {{-- SUBIR INFORMES ITA --}}
                                                @if($reservasmedica->informeDisponible)
                                                    <button type="button"
                                                    class="dropdown-item btn-subir-informe"
                                                    data-toggle="modal"
                                                    data-target="#subirinformeitaModal"
                                                    data-id="{{ $reservasmedica->id }}"
                                                    data-clienteitaid="{{ $reservasmedica->clienteitaid }}"
                                                    data-clienteitanombre="{{ $reservasmedica->clienteitanombre }}"
                                                    data-fechabateria="{{ $reservasmedica->fechabateria }}"
                                                    data-accion="{{ $reservasmedica->accionnombre }}">
                                                        <i class="fas fa-upload mr-2"></i> Subir Informe
                                                    </button>
                                                @else
                                                    <span class="dropdown-item text-muted">
                                                        <i class="fas fa-upload mr-2"></i> Fecha pendiente
                                                    </span>
                                                @endif

                                                {{-- SUBIR INFORME MULTIPLE --}}
                                                @if(in_array($nombreusuario, $usuariosMultiple))
                                                    @if($reservasmedica->informeDisponible)
                                                        <button type="button"
                                                        class="dropdown-item btn-subirinforme-multiple"
                                                        data-toggle="modal"
                                                        data-target="#subirinformemultipleitaModal"
                                                        data-id="{{ $reservasmedica->id }}"
                                                        data-clienteitaid2="{{ $reservasmedica->clienteitaid }}"
                                                        data-clienteitanombre2="{{ $reservasmedica->clienteitanombre }}"
                                                        data-fechabateria2="{{ $reservasmedica->fechabateria }}"
                                                        data-accion2="{{ $reservasmedica->accionnombre }}">
                                                            <i class="fas fa-share-square mr-2"></i> Subir Informe Múltiple
                                                        </button>
                                                    @else
                                                        <span class="dropdown-item text-muted">
                                                            <i class="fas fa-share-square mr-2"></i> Fecha pendiente
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        
                        {{-- MODAL SUBIR INFORMES ITA --}}
                        <div class="modal fade" id="subirinformeitaModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title"
                                            style="color:#94c93b;font-weight:bold;">
                                            SUBIR INFORME
                                        </h3>
                                        <button type="button"
                                            class="close"
                                            data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="info-box-modal">
                                        <p>
                                            <strong>CLIENTE:</strong>
                                            <span id="clienteitanombre-texto"></span>
                                        </p>
                                        <p>
                                            <strong>EST. / ESP.:</strong>
                                            <span id="accionnombre-texto"></span>
                                        </p>
                                    </div>

                                    {!! Form::open(['route' => 'procesar.informe', 'method' => 'POST', 'files' => true, 'id' => 'formuno']) !!}
                                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                        {!! Form::hidden('clienteitaid', '', ['id' => 'modal-clienteitaid']) !!}
                                        {!! Form::hidden('clienteitanombre', '', ['id' => 'modal-clienteitanombre']) !!}
                                        {!! Form::hidden('fechabateria', '', ['id' => 'modal-fechabateria']) !!}
                                        {!! Form::hidden('accion', '', ['id' => 'modal-accion']) !!}
                                        {!! Form::hidden('programacionid', '', ['id' => 'modal-id']) !!}

                                        

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label><strong>INFORME PDF (Obligatorio)</strong></label>
                                                    <input type="file" name="archivo" id="archivoita" class="form-control" accept=".pdf" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label><strong>INFORME WORD (Se guardará sin firma)</strong></label>
                                                    <input type="file" name="archivo3" id="archivoitaword" class="form-control" accept=".doc,.docx">
                                                </div>

                                                @if(in_array($nombreusuario, $usuariosAdjuntarImagenes))
                                                    <div class="col-12 mb-3">
                                                        <label><strong>IMAGEN 1</strong></label>
                                                        <input type="file" name="picture" id="picture" class="form-control" accept="image/*">
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <label><strong>IMAGEN 2</strong></label>
                                                        <input type="file" name="picture2" id="picture2" class="form-control" accept="image/*">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="text-center w-100">
                                                {!! Form::submit('SUBIR INFORME', ['class'=>'btn btn-sm btn-crear', 'id'=>'btnSubirInforme']) !!}
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).on('click', '.btn-subir-informe', function () {
                                let clienteitaid = $(this).attr('data-clienteitaid');
                                let clienteitanombre = $(this).attr('data-clienteitanombre');
                                let fechabateria = $(this).attr('data-fechabateria');
                                let accion = $(this).attr('data-accion');
                                let id = $(this).attr('data-id');

                                console.log("DATOS EN BOTON", {
                                    clienteitaid,
                                    clienteitanombre,
                                    fechabateria,
                                    accion,
                                    id
                                });

                                $('#modal-clienteitaid').val(clienteitaid);
                                $('#modal-clienteitanombre').val(clienteitanombre);
                                $('#modal-fechabateria').val(fechabateria);
                                $('#modal-accion').val(accion);
                                $('#modal-id').val(id);
                                $('#clienteitanombre-texto').text(clienteitanombre);
                                $('#accionnombre-texto').text(accion);
                            });

                            $('#subirinformeitaModal').on('hidden.bs.modal', function () {
                                if($('#formuno').length){
                                    $('#formuno')[0].reset();
                                }

                                $('#clienteitanombre-texto').text('');
                                $('#accionnombre-texto').text('');

                                $('#modal-clienteitaid').val('');
                                $('#modal-clienteitanombre').val('');
                                $('#modal-fechabateria').val('');
                                $('#modal-accion').val('');
                                $('#modal-id').val('');
                            });

                            document.addEventListener('DOMContentLoaded', function () {
                                const form = document.querySelector('#formuno');
                                const btn = document.getElementById('btnSubirInforme');

                                if(form){
                                    form.addEventListener('submit', function(e){
                                        const archivo = document.getElementById('archivoita').files.length;
                                        if(archivo === 0){
                                            e.preventDefault();
                                            alert('Debes subir el INFORME PDF');
                                            return;
                                        }
                                        btn.value = 'GUARDANDO...';
                                        btn.disabled = true;
                                    });
                                }
                            });
                        </script>

                        <!-- MODAL SUBIR INFORME MULTIPLE ITA -->
                        <div class="modal fade" id="subirinformemultipleitaModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="container text-center">
                                            <div class="row">
                                                <div class="col-12">
                                                    <h3 class="modal-title" id="subirinformemultipleitaModalLabel" style="color: #94c93b; font-weight: bold;">
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
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    {!! Form::open(['route' => 'procesar.informe.multiple', 'method' => 'POST', 'files' => true, 'id' => 'formdos']) !!}
                                        {!! Form::hidden('usuarioid2', auth()->user()->id) !!}
                                        {!! Form::hidden('usuarioregistro2', auth()->user()->name) !!}
                                        {!! Form::hidden('clienteitaid2', null, ['id' => 'modal2-clienteitaid']) !!}
                                        {!! Form::hidden('clienteitanombre2', null, ['id' => 'modal2-clienteitanombre']) !!}
                                        {!! Form::hidden('fechabateria2', null, ['id' => 'modal2-fechabateria']) !!}
                                        {!! Form::hidden('acciones_seleccionadas2', null, ['id' => 'acciones_seleccionadas2']) !!}
                                        
                                        

                                        <div class="modal-body">
                                            <div class="row"> 
                                                <div class="col-lg-6">
                                                    <div id="acciones-checkboxes2">
                                                    </div>
                                                </div>
                                                <script>
                                                    $(document).on('submit', '#formdos', function () {
                                                        let seleccionadas = $('input[name="acciones[]"]:checked').map(function () {
                                                            return $(this).val();
                                                        }).get();

                                                        $('#acciones_seleccionadas2').val(JSON.stringify(seleccionadas));

                                                        console.log('Acciones seleccionadas:', seleccionadas);
                                                    });
                                                </script>
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row"> 
                                                                <div class="col-lg-12 mb-3">
                                                                    <label><strong>INFORME PDF (Obligatorio)</strong></label>
                                                                    <input type="file" name="archivo" id="archivoita" class="form-control" accept=".pdf" required/>
                                                                </div>
                                                                @if(in_array($nombreusuario, $usuariosAdjuntarImagenes))
                                                                    <div class="col-12 mb-3">
                                                                        <label><strong>IMAGEN 1</strong></label>
                                                                        <input type="file" name="picture" class="form-control" accept="image/*">
                                                                    </div>
                                                                    <div class="col-12 mb-3">
                                                                        <label><strong>IMAGEN 2</strong></label>
                                                                        <input type="file" name="picture2" class="form-control" accept="image/*">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="text-center w-100">
                                                {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear btn-sm', 'id' => 'btnSubirInforme2']) !!}
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).on('click', '.btn-subirinforme-multiple', function () {

                                let clienteitaid = $(this).data('clienteitaid2');
                                let clienteitanombre = $(this).data('clienteitanombre2');
                                let fechabateria = $(this).data('fechabateria2');
                                let id = $(this).data('id');

                                $('#clienteitanombre2-texto').text(clienteitanombre);
                                $('#modal2-clienteitaid').val(clienteitaid);
                                $('#modal2-clienteitanombre').val(clienteitanombre);
                                $('#modal2-fechabateria').val(fechabateria);
                                $('#acciones-checkboxes2').html('<strong>ESTUDIOS / ESPECIALIDADES</strong>');

                                let selectAll = `
                                    <div>
                                        <input type="checkbox" id="select-all2">
                                        <label for="select-all2">Seleccionar todo</label>
                                    </div>
                                `;
                                $('#acciones-checkboxes2').append(selectAll);

                                @foreach($reservasmedicas as $reserva)
                                    @if(!$reserva->documentacionDisponible && $reserva->informeDisponible)
                                        if ({{ $reserva->clienteitaid }} == clienteitaid && '{{ $reserva->fechabateria }}' == fechabateria) {

                                            let checkbox = `
                                                <div>
                                                    <input type="checkbox" name="acciones[]" value="{{ $reserva->accionnombre }}">
                                                    <label>{{ $reserva->accionnombre }}</label>
                                                </div>
                                            `;
                                            $('#acciones-checkboxes2').append(checkbox);
                                        }
                                    @endif
                                @endforeach

                                $('#select-all2').on('change', function () {
                                    let checked = $(this).prop('checked');
                                    $('#acciones-checkboxes2 input[type="checkbox"]').not(this).prop('checked', checked);
                                });

                            });

                            document.addEventListener('DOMContentLoaded', function () {
                                const form = document.querySelector('#formdos');
                                const btn = document.getElementById('btnSubirInforme2');
                                form.addEventListener('submit', function(e) {
                                    btn.value = 'GUARDANDO...';
                                    btn.disabled = true;
                                });
                            });

                            $('#subirinformemultipleitaModal').on('hidden.bs.modal', function () {
                                $('#formdos')[0].reset();
                                $('#acciones-checkboxes2').html('');
                                $('#clienteitanombre2-texto').text('');
                                $('#modal2-clienteitaid').val('');
                                $('#modal2-clienteitanombre').val('');
                                $('#modal2-fechabateria').val('');
                            });
                        </script>

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

                                    

                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" style="min-width: 280px;">
                                                {{-- FICHA MEDICA --}}
                                                @if(in_array($nombreusuario, $usuariosFicha))
                                                    @if($reservasmedicaauditoria->fichamedicaauditoria)
                                                        <a class="dropdown-item" target="_blank"
                                                        href="{{ asset('/fichamedicaclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->fichamedicaauditoria) }}">
                                                            <i class="fas fa-file-signature mr-2"></i> Ver Ficha Médica
                                                        </a>
                                                    @else
                                                        <a class="dropdown-item"
                                                        href="{{ route('admin.asociados.crearformularioclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid) }}">
                                                            <i class="fas fa-file-signature mr-2"></i> Crear Ficha Médica
                                                        </a>
                                                    @endif
                                                @endif

                                                {{-- DIAGNOSTICO --}}
                                                @if(in_array($nombreusuario, $usuariosDiagnostico))
                                                    @if($reservasmedicaauditoria->diagnosticomedicoauditoria)
                                                        <a class="dropdown-item" target="_blank"
                                                        href="{{ asset('/diagnosticosauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->diagnosticomedicoauditoria) }}">
                                                            <i class="fas fa-laptop-medical mr-2"></i> Ver Diagnóstico
                                                        </a>
                                                    @else
                                                        <span class="dropdown-item text-muted">
                                                            <i class="fas fa-laptop-medical mr-2"></i> Diagnóstico (No Disponible)
                                                        </span>
                                                    @endif
                                                @endif

                                                {{-- CREAR BATERIA --}}
                                                @if(in_array($nombreusuario, $usuariosFicha))
                                                    <a class="dropdown-item"
                                                    href="{{ route('admin.asociados.crearbateriaclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid) }}">
                                                        <i class="fas fa-charging-station mr-2"></i> Crear Batería
                                                    </a>
                                                @endif

                                                {{-- SUBIR INFORME --}}
                                                @if($reservasmedicaauditoria->informeDisponibleauditoria)
                                                    <button type="button"
                                                    class="dropdown-item btn-subir-informeauditoria"
                                                    data-toggle="modal"
                                                    data-target="#subirinformeauditoriaModal"
                                                    data-id="{{ $reservasmedicaauditoria->id }}"
                                                    data-clienteauditoriaid="{{ $reservasmedicaauditoria->clienteauditoriaid }}"
                                                    data-clienteauditorianombre="{{ $reservasmedicaauditoria->clienteauditorianombre }}"
                                                    data-fechabateria="{{ $reservasmedicaauditoria->fechabateria }}"
                                                    data-accion="{{ $reservasmedicaauditoria->accionnombre }}">
                                                        <i class="fas fa-upload mr-2"></i> Subir Informe
                                                    </button>
                                                @else
                                                    <span class="dropdown-item text-muted">
                                                        <i class="fas fa-upload mr-2"></i> Fecha pendiente
                                                    </span>
                                                @endif

                                                {{-- SUBIR INFORME MULTIPLE --}}
                                                @if(in_array($nombreusuario, $usuariosMultiple))
                                                    @if($reservasmedicaauditoria->informeDisponibleauditoria)
                                                        <button type="button"
                                                        class="dropdown-item btn-subirinforme-multipleauditoria"
                                                        data-toggle="modal"
                                                        data-target="#subirinformemultipleauditoriaModal"
                                                        data-id="{{ $reservasmedicaauditoria->id }}"
                                                        data-clienteauditoriaid2="{{ $reservasmedicaauditoria->clienteauditoriaid }}"
                                                        data-clienteauditorianombre2="{{ $reservasmedicaauditoria->clienteauditorianombre }}"
                                                        data-fechabateria2="{{ $reservasmedicaauditoria->fechabateria }}"
                                                        data-accion2="{{ $reservasmedicaauditoria->accionnombre }}">
                                                            <i class="fas fa-share-square mr-2"></i> Subir Informe Múltiple
                                                        </button>
                                                    @else
                                                        <span class="dropdown-item text-muted">
                                                            <i class="fas fa-share-square mr-2"></i> Fecha pendiente
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        {{-- MODAL SUBIR INFORMES auditoria --}}
                        <div class="modal fade" id="subirinformeauditoriaModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title"
                                            style="color:#94c93b;font-weight:bold;">
                                            SUBIR INFORME
                                        </h3>
                                        <button type="button"
                                            class="close"
                                            data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="info-box-modal">
                                        <p>
                                            <strong>CLIENTE:</strong>
                                            <span id="clienteauditorianombre-texto"></span>
                                        </p>
                                        <p>
                                            <strong>EST. / ESP.:</strong>
                                            <span id="accionnombre-texto2"></span>
                                        </p>
                                    </div>

                                    {!! Form::open(['route' => 'procesar.informeauditoria', 'method' => 'POST', 'files' => true, 'id' => 'formtres']) !!}
                                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                        {!! Form::hidden('clienteauditoriaid', '', ['id' => 'modal-clienteauditoriaid']) !!}
                                        {!! Form::hidden('clienteauditorianombre', '', ['id' => 'modal-clienteauditorianombre']) !!}
                                        {!! Form::hidden('fechabateria', '', ['id' => 'modal-fechabateria3']) !!}
                                        {!! Form::hidden('accion', '', ['id' => 'modal-accion3']) !!}
                                        {!! Form::hidden('programacionid', '', ['id' => 'modal-id3']) !!}

                                        

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label><strong>INFORME PDF (Obligatorio)</strong></label>
                                                    <input type="file" name="archivo" id="archivoauditoria" class="form-control" accept=".pdf" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label><strong>INFORME WORD (Se guardará sin firma)</strong></label>
                                                    <input type="file" name="archivo3" id="archivoauditoriaword" class="form-control" accept=".doc,.docx">
                                                </div>

                                                @if(in_array($nombreusuario, $usuariosAdjuntarImagenes))
                                                    <div class="col-12 mb-3">
                                                        <label><strong>IMAGEN 1</strong></label>
                                                        <input type="file" name="picture" id="picture" class="form-control" accept="image/*">
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <label><strong>IMAGEN 2</strong></label>
                                                        <input type="file" name="picture2" id="picture2" class="form-control" accept="image/*">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="text-center w-100">
                                                {!! Form::submit('SUBIR INFORME', ['class'=>'btn btn-sm btn-crear', 'id'=>'btnSubirInforme3']) !!}
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).on('click', '.btn-subir-informeauditoria', function () {
                                let clienteauditoriaid = $(this).attr('data-clienteauditoriaid');
                                let clienteauditorianombre = $(this).attr('data-clienteauditorianombre');
                                let fechabateria = $(this).attr('data-fechabateria');
                                let accion = $(this).attr('data-accion');
                                let id = $(this).attr('data-id');

                                console.log("DATOS EN BOTON", {
                                    clienteauditoriaid,
                                    clienteauditorianombre,
                                    fechabateria,
                                    accion,
                                    id
                                });

                                $('#modal-clienteauditoriaid').val(clienteauditoriaid);
                                $('#modal-clienteauditorianombre').val(clienteauditorianombre);
                                $('#modal-fechabateria3').val(fechabateria);
                                $('#modal-accion3').val(accion);
                                $('#modal-id3').val(id);
                                $('#clienteauditorianombre-texto').text(clienteauditorianombre);
                                $('#accionnombre-texto2').text(accion);
                            });

                            $('#subirinformeauditoriaModal').on('hidden.bs.modal', function () {
                                if($('#formtres').length){
                                    $('#formtres')[0].reset();
                                }

                                $('#clienteauditorianombre-texto').text('');
                                $('#accionnombre-texto2').text('');

                                $('#modal-clienteauditoriaid').val('');
                                $('#modal-clienteauditorianombre').val('');
                                $('#modal-fechabateria3').val('');
                                $('#modal-accion3').val('');
                                $('#modal-id3').val('');
                            });

                            document.addEventListener('DOMContentLoaded', function () {
                                const form = document.querySelector('#formtres');
                                const btn = document.getElementById('btnSubirInforme3');

                                if(form){
                                    form.addEventListener('submit', function(e){
                                        const archivo = document.getElementById('archivoauditoria').files.length;
                                        if(archivo === 0){
                                            e.preventDefault();
                                            alert('Debes subir el INFORME PDF');
                                            return;
                                        }
                                        btn.value = 'GUARDANDO...';
                                        btn.disabled = true;
                                    });
                                }
                            });
                        </script>

                        <!-- MODAL SUBIR INFORME MULTIPLE auditoria -->
                        <div class="modal fade" id="subirinformemultipleauditoriaModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="container text-center">
                                            <h3 class="modal-title" style="color:#94c93b;font-weight:bold;">
                                                SUBIR INFORME
                                            </h3>
                                            <p>
                                                <strong>CLIENTE:</strong>
                                                <span id="clienteauditorianombre2-texto"></span>
                                            </p>
                                        </div>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    {!! Form::open(['route'=>'procesar.informe.multiple.auditoria', 'method'=>'POST', 'files'=>true, 'id'=>'formcuatro']) !!}
                                        {!! Form::hidden('usuarioid2', auth()->user()->id) !!}
                                        {!! Form::hidden('usuarioregistro2', auth()->user()->name) !!}
                                        {!! Form::hidden('clienteauditoriaid2', null, ['id'=>'modal4-clienteauditoriaid']) !!}
                                        {!! Form::hidden('clienteauditorianombre2', null, ['id'=>'modal4-clienteauditorianombre']) !!}
                                        {!! Form::hidden('fechabateria2', null, ['id'=>'modal4-fechabateria']) !!}
                                        {!! Form::hidden('acciones_seleccionadas4', null, ['id'=>'acciones_seleccionadas4']) !!}

                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <h5><strong>ESTUDIOS / ESPECIALIDADES</strong></h5>
                                                    <div id="acciones-checkboxes4">
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <label><strong>INFORME PDF (Obligatorio)</strong></label>
                                                    <input type="file" name="archivo" class="form-control" accept=".pdf" required>

                                                    

                                                    @if(in_array($nombreusuario,$usuariosAdjuntarImagenes))
                                                        <label class="mt-3"><strong>IMAGEN 1</strong></label>
                                                        <input type="file" name="picture" class="form-control" accept="image/*">

                                                        <label class="mt-3"><strong>IMAGEN 2</strong></label>
                                                        <input type="file" name="picture2" class="form-control" accept="image/*">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="text-center w-100">
                                                <button type="submit" class="btn btn-crear btn-sm" id="btnSubirInforme4">SUBIR INFORME</button>
                                            </div>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).on('click','.btn-subirinforme-multipleauditoria',function(){

                                let clienteauditoriaid = $(this).attr('data-clienteauditoriaid2');
                                let clienteauditorianombre = $(this).attr('data-clienteauditorianombre2');
                                let fechabateria = $(this).attr('data-fechabateria2');
                                console.log({
                                    clienteauditoriaid,
                                    clienteauditorianombre,
                                    fechabateria
                                });

                                $('#clienteauditorianombre2-texto')
                                    .text(clienteauditorianombre);

                                $('#modal4-clienteauditoriaid')
                                    .val(clienteauditoriaid);

                                $('#modal4-clienteauditorianombre')
                                    .val(clienteauditorianombre);

                                $('#modal4-fechabateria')
                                    .val(fechabateria);

                                $('#acciones-checkboxes4').html('');

                                let html = `
                                    <div>
                                        <input type="checkbox" id="select-all4">
                                        <label>
                                            Seleccionar todo
                                        </label>
                                    </div>
                                `;

                                $('#acciones-checkboxes4').append(html);

                                @foreach($reservasmedicasauditorias as $reserva)
                                    @if(!$reserva->documentacionDisponibleauditoria && $reserva->informeDisponibleauditoria)
                                        if(
                                            "{{ $reserva->clienteauditoriaid }}" == clienteauditoriaid &&
                                            "{{ $reserva->fechabateria }}" == fechabateria
                                        ){
                                            $('#acciones-checkboxes4').append(`
                                                <div>
                                                    <input 
                                                        type="checkbox"
                                                        name="acciones[]"
                                                        value="{{ $reserva->accionnombre }}"
                                                    >
                                                    <label>
                                                        {{ $reserva->accionnombre }}
                                                    </label>
                                                </div>
                                            `);
                                        }
                                    @endif
                                @endforeach
                                $('#select-all4')
                                .off('change')
                                .on('change',function(){
                                    $('#acciones-checkboxes4 input[type=checkbox]')
                                    .not(this)
                                    .prop('checked',this.checked);
                                });
                            });

                            $('#formcuatro').on('submit',function(){
                                let seleccionadas=[];
                                $('input[name="acciones[]"]:checked')
                                .each(function(){
                                    seleccionadas.push($(this).val());

                                });
                                $('#acciones_seleccionadas4')
                                .val(JSON.stringify(seleccionadas));

                                console.log(
                                    "ACCIONES:",
                                    seleccionadas
                                );
                            });

                            $('#formcuatro').on('submit',function(){
                                $('#btnSubirInforme4')
                                .text('GUARDANDO...')
                                .prop('disabled',true);
                            });

                            $('#subirinformemultipleauditoriaModal')
                            .on('hidden.bs.modal',function(){
                                $('#formcuatro')[0].reset();
                                $('#acciones-checkboxes4').html('');

                                $('#clienteauditorianombre2-texto')
                                .text('');

                                $('#modal4-clienteauditoriaid').val('');
                                $('#modal4-clienteauditorianombre').val('');
                                $('#modal4-fechabateria').val('');
                            });
                        </script>
                    </tbody>
                </table>
            </div>
        </div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
