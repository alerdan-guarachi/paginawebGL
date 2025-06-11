@extends('adminlte::page')

@section('content_header')
<h1>RESULTADOS MÉDICOS CLIENTES AUDITORIA</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/resultadosmedicos.css') }}">
<style>
    .table td {
        padding: 2px 10px;
        }
        .td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                <form id="search-form" action="{{ route('buscarresultadosmedicosclientesauditoria') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="NOMBRE DEL CLIENTE">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar" type="submit">BUSCAR</button>
                    {{-- <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" type="button">Mostrar Todo</button> --}}
                    <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" name="buscartodo" type="submit" value="1">MOSTRAR TODO</button>
                </form>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarresultadosmedicosclientesauditoria') }}";
            });
        });
    </script>
    
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">

            <li class="nav-item">
                <a class="nav-link active" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    100% COMPLETOS
                    <?php if ($completosCount > 0): ?>
                        <span class="circle"><?= $completosCount ?></span>
                    <?php endif; ?>
                </a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    RESULT. MÉDICOS COMPLETOS
                    <?php if ($resultadosCount > 0): ?>
                        <span class="circle"><?= $resultadosCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    DOCUMENTACIONES COMPLETAS
                    <?php if ($documentosCount > 0): ?>
                        <span class="circle"><?= $documentosCount ?></span>
                    <?php endif; ?>
                </a>
            </li>    
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    INCOMPLETOS
                    <?php if ($incompletosCount > 0): ?>
                        <span class="circle"><?= $incompletosCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">

            @if ($nombreusuario != 'MARICELA COLQUE SANDOVAL')
            {{-- 100% COMPLETOS --}}
            <div class="tab-pane fade show active" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 20%;">Fecha_Batería-Servicio</th>
                                <th style="width: 15%;">Result_médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Diagnóstico</th>
                                <th style="width: 10%;">Hist_médica</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'COMPLETO' && $item['estadoGeneralauditoria'] === 'COMPLETO')
                                <tr>
                                    <td>{{ $item['clienteauditoriaid'] }}</td>
                                    <td>{{ $item['clienteauditorianombre'] }}</td>
                                    <td>{{ $item['usuarioregistro'] }}</td>
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>
                                    <td>
                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif
                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>
                                    <td width="10px"> 
                                        @if($item['diagnosticoauditoria'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticosauditoria/' . $item['clienteauditoriaid'] . '/' .$item['diagnosticoauditoria']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </div>
                                        @else
                                            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $nombreusuario === 'YELKA MORALES VELARDE')
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0 disabled">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto disabled" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
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
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr></p>
                                        @else
                                        <p class="text-notiene">NO TIENE</p>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>

            {{-- RESULTADOS MEDICOS COMPLETOS --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr> 
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 20%;">Fecha_Batería-Servicio</th>
                                <th style="width: 15%;">Result_Médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Diagnóstico</th>
                                <th style="width: 10%;">Hist_médica</th>
                            </tr>                            
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'COMPLETO' && ($item['estadoGeneralauditoria'] === 'PENDIENTE'))
                            <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- USUARIO REGISTRO --}}
                                    <td>{{ $item['usuarioregistro'] }}</td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA Y SERVICIO--}}
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>

                                    {{-- RESULTADOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones {{ $item['estado'] === 'INCOMPLETO' ? 'btn-danger' : '' }}" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>
                                    <style>
                                        .btn-danger {
                                                background-color:  #ffffff;
                                                color: red;
                                                border-color: red;
                                                border-radius: 5px;
                                                padding: 2px 10px;
                                                }
                                        .btn-danger:hover {
                                                background-color: red;
                                                color: #ffffff;
                                                }
                                    </style>

                                    {{-- DOCUMENTACION --}}
                                    <td>
                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif
                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>
                                    
                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnosticoauditoria'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticosauditoria/' . $item['clienteauditoriaid'] . '/' .$item['diagnosticoauditoria']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </div>
                                        @else
                                            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $nombreusuario === 'YELKA MORALES VELARDE')
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0 disabled">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto disabled" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
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
                                        @endif
                                    </td>

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr></p>
                                        @else
                                        <p class="text-notiene">NO TIENE</p>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>

            {{-- DOCUMENTACIONES COMPLETAS --}}
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 20%;">Fecha_Batería-Servicio</th>
                                <th style="width: 15%;">Result_médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Diagnóstico</th>
                                <th style="width: 10%;">Hist_médica</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'INCOMPLETO' && ($item['estadoGeneralauditoria'] === 'COMPLETO'))
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- USUARIO REGISTRO --}}
                                    <td>{{ $item['usuarioregistro'] }}</td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    {{-- <td>{{ $item['fechabateria'] }} - {{ $item['tramite'] }}</td> --}}
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>

                                    {{-- RESULTADOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones {{ $item['estado'] === 'INCOMPLETO' ? 'btn-danger' : '' }}" data-toggle="modal" data-target="#modal{{ $loop->index }}">
                                                <i class="fas fa-file-medical-alt"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                    <style>
                                        .btn-danger {
                                                background-color:  #ffffff;
                                                color: red;
                                                border-color: red;
                                                border-radius: 5px;
                                                padding: 2px 10px;
                                                }
                                        .btn-danger:hover {
                                                background-color: red;
                                                color: #ffffff;
                                                }
                                    </style>
                                
                                    {{-- DOCUMENTACION --}}
                                    <td>
                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>
                                    
                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnosticoauditoria'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticosauditoria/' . $item['clienteauditoriaid'] . '/' .$item['diagnosticoauditoria']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </div>
                                        @else
                                            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $nombreusuario === 'YELKA MORALES VELARDE')
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0 disabled">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto disabled" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
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
                                        @endif
                                    </td>

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr></p>
                                        @else
                                        <p class="text-notiene">NO TIENE</p>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>

            {{-- INCOMPLETOS --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 20%;">Fecha_Batería-Servicio</th>
                                <th style="width: 15%;">Result_médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Diagnóstico</th>
                                <th style="width: 10%;">Hist_médica</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'INCOMPLETO' && $item['estadoGeneralauditoria'] === 'PENDIENTE')
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- USUARIO REGISTRO --}}
                                    <td>{{ $item['usuarioregistro'] }}</td>
                                    
                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    {{-- <td>{{ $item['fechabateria'] }} - {{ $item['tramite'] }}</td>--}}
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>
                                    
                                    {{-- ESTADO DE DOCUMENTACION --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones2" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- DOCUMENTACION --}}
                                    <td>
                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnosticoauditoria'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticosauditoria/' . $item['clienteauditoriaid'] . '/' .$item['diagnosticoauditoria']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </div>
                                        @else
                                            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $nombreusuario === 'YELKA MORALES VELARDE')
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0 disabled">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto disabled" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
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
                                        @endif
                                    </td>

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr></p>
                                        @else
                                        <p class="text-notiene">NO TIENE</p>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ABANDONARON --}}
            {{-- <div class="tab-pane fade" id="tab-content-6" role="tabpanel" aria-labelledby="tab-6">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Cliente</th>
                                <th>Cliente</th>
                                <th>Fecha Batería</th>
                                <th>Result. Médicos</th>
                                <th>Otros doc.</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['motivoabandono'])
                                <tr>
                                    <td>{{ $item['clienteitaid'] }}</td>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>
                                    <td>{{ $item['fechabateria'] }}</td>
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <a class="btn btn-veracciones2" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>

                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>
                                    <td>{{ $item['motivoabandono'] }}</td>
                                </tr>
                                
                                @endif
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
            </div> --}}
            @endif


            @if ($nombreusuario === 'MARICELA COLQUE SANDOVAL')
            {{-- 100% COMPLETOS --}}
            <div class="tab-pane fade show active" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 20%;">Fecha_Batería-Servicio</th>
                                <th style="width: 15%;">Result. médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Diagnóstico</th>
                                <th style="width: 10%;">Hist_médica</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'COMPLETO' && $item['estadoGeneralauditoria'] === 'COMPLETO' && !is_null($item['diagnosticoauditoria']))
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- USUARIO REGISTRO --}}
                                    <td>{{ $item['usuarioregistro'] }}</td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>

                                    {{-- RESULTADOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- DOCUMENTACION --}}
                                    <td>
                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif
                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnosticoauditoria'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticosauditoria/' . $item['clienteauditoriaid'] . '/' .$item['diagnosticoauditoria']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </div>
                                        @else
                                            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0 disabled">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto disabled" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
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
                                        @endif
                                    </td>

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr></p>
                                        @else
                                        <p class="text-notiene">NO TIENE</p>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>

            {{-- RESULTADOS MEDICOS COMPLETOS --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr> 
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 20%;">Fecha_Batería-Servicio</th>
                                <th style="width: 15%;">Result_médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Diagnóstico</th>
                                <th style="width: 10%;">Hist_médica</th>
                            </tr>                            
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'COMPLETO' && $item['estadoGeneralauditoria'] === 'PENDIENTE' && !is_null($item['diagnosticoauditoria']))
                            <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- USUARIO REGISTRO --}}
                                    <td>{{ $item['usuarioregistro'] }}</td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA Y SERVICIO--}}
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>

                                    {{-- RESULTADOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones {{ $item['estado'] === 'INCOMPLETO' ? 'btn-danger' : '' }}" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>
                                    <style>
                                        .btn-danger {
                                                background-color:  #ffffff;
                                                color: red;
                                                border-color: red;
                                                border-radius: 5px;
                                                padding: 2px 10px;
                                                }
                                        .btn-danger:hover {
                                                background-color: red;
                                                color: #ffffff;
                                                }
                                    </style>

                                    {{-- DOCUMENTACION --}}
                                    <td>
                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif
                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>
                                    
                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnosticoauditoria'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticosauditoria/' . $item['clienteauditoriaid'] . '/' .$item['diagnosticoauditoria']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </div>
                                        @else
                                            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0 disabled">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto disabled" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
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
                                        @endif
                                    </td>

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr></p>
                                        @else
                                        <p class="text-notiene">NO TIENE</p>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>

            {{-- DOCUMENTACIONES COMPLETAS --}}
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 20%;">Fecha_Batería-Servicio</th>
                                <th style="width: 15%;">Result_médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Diagnóstico</th>
                                <th style="width: 10%;">Hist_médica</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'INCOMPLETO' && $item['estadoGeneralauditoria'] === 'COMPLETO' && !is_null($item['diagnosticoauditoria']))
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- USUARIO REGISTRO --}}
                                    <td>{{ $item['usuarioregistro'] }}</td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    {{-- <td>{{ $item['fechabateria'] }} - {{ $item['tramite'] }}</td> --}}
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>

                                    {{-- RESULTADOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones {{ $item['estado'] === 'INCOMPLETO' ? 'btn-danger' : '' }}" data-toggle="modal" data-target="#modal{{ $loop->index }}">
                                                <i class="fas fa-file-medical-alt"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                    <style>
                                        .btn-danger {
                                                background-color:  #ffffff;
                                                color: red;
                                                border-color: red;
                                                border-radius: 5px;
                                                padding: 2px 10px;
                                                }
                                        .btn-danger:hover {
                                                background-color: red;
                                                color: #ffffff;
                                                }
                                    </style>
                                
                                    {{-- DOCUMENTACION --}}
                                    <td>
                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>
                                    
                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnosticoauditoria'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticosauditoria/' . $item['clienteauditoriaid'] . '/' .$item['diagnosticoauditoria']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </div>
                                        @else
                                            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0 disabled">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto disabled" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
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
                                        @endif
                                    </td>

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr></p>
                                        @else
                                        <p class="text-notiene">NO TIENE</p>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>

            {{-- INCOMPLETOS --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 15%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 20%;">Fecha_Batería-Servicio</th>
                                <th style="width: 15%;">Result_médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Diagnóstico</th>
                                <th style="width: 10%;">Hist_médica</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'INCOMPLETO' && $item['estadoGeneralauditoria'] === 'PENDIENTE' && !is_null($item['diagnosticoauditoria']))
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- USUARIO REGISTRO --}}
                                    <td>{{ $item['usuarioregistro'] }}</td>
                                    
                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    {{-- <td>{{ $item['fechabateria'] }} - {{ $item['tramite'] }}</td>--}}
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>
                                    
                                    {{-- ESTADO DE DOCUMENTACION --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones2" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- DOCUMENTACION --}}
                                    <td>
                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnosticoauditoria'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticosauditoria/' . $item['clienteauditoriaid'] . '/' .$item['diagnosticoauditoria']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </div>
                                        @else
                                            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0 disabled">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto disabled" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}"
                                                    data-accion="DIAGNÓSTICO MÉDICO">
                                                        <i class="fas fa-paste"></i>
                                                    </a>
                                                </div>
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
                                        @endif
                                    </td>

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr></p>
                                        @else
                                        <p class="text-notiene">NO TIENE</p>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.btn-aprobar').on('click', function() {
            var cliente = $(this).data('cliente');
            var fecha = $(this).data('fecha');

            // Asignar los valores al modal
            var modal2 = $(this).data('target');
            $(modal2).find('#cliente').val(cliente);
            $(modal2).find('#fechabateria').val(fecha);
        });
    });
</script>
@foreach ($result as $item)

    {{-- DIAGNOSTICO MEDICO --}}
    <div class="modal fade" id="subirdiagnosticoModal" tabindex="-1" role="dialog" aria-labelledby="subirdiagnosticoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="subirdiagnosticoModalLabel" style="color: #94c93b; font-weight: bold;">DIAGNÓSTICO</h3>
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
                                <label for="archivo">ELIJE UN PDF:</label>
                                <input type="file" name="archivo" class="file-input" id="archivo" accept=".pdf, .docx"/>
                                <label for="archivo" class="file-custom-label" id="file-name">SELECCIONAR ARCHIVO</label>
                                <div class="file-preview" id="preview-archivo"></div>
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
                    document.getElementById('archivo').addEventListener('change', function(event) {
                        var fileName = event.target.files[0] ? event.target.files[0].name : 'SELECCIONAR ARCHIVO';
                        document.getElementById('file-name').textContent = fileName;
                    });
                </script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
                <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <!-- Script para pasar los datos al modal -->
                <script>
                    $('#subirdiagnosticoModal').on('show.bs.modal', function (event) {
                        var button = $(event.relatedTarget); // El botón que activó el modal
                        var clienteauditoriaid = button.data('clienteauditoriaid');
                        var clienteauditorianombre = button.data('clienteauditorianombre');
                        var fechabateria = button.data('fechabateria');
                        var accion = button.data('accion');

                        var modal = $(this);
                        modal.find('#modal-clienteauditoriaid').val(clienteauditoriaid);
                        modal.find('#modal-clienteauditorianombre').val(clienteauditorianombre);
                        modal.find('#modal-fechabateria').val(fechabateria);
                        modal.find('#modal-accion').val(accion); // También se pasa al campo 'accion'
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

    {{-- RESULTADOS MEDICOS --}}
    <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}"><strong>{{ $item['clienteauditorianombre'] }}</strong> - Fecha Bateria: {{ \Carbon\Carbon::parse($item['fechabateria'])->format('Y-m-d') }}</h4>
                    <a type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body table-responsive">
                    <div class="d-none d-md-block">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Estudio/Especialidad</th>
                                    <th>Proveedor</th>
                                    <th>Prog.</th>
                                    <th>Atención</th>
                                    <th>Fecha_Sub.</th>
                                    <th>Result_Médicos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item['acciones'] as $accion)
                                    <tr>
                                        <td>{{ $accion['accion'] }}</td>
                                        <td>{{ $accion['proveedornombre'] }}</td>
                                        <td>{{ $accion['fechaasignada'] }}</td>   
                                        <td>
                                            @if ($accion['fechaatencionprogramacion'])
                                            {{ $accion['fechaatencionprogramacion'] }}
                                            @else
                                            <div class="pendiente">PENDIENTE</div>
                                            @endif
                                        </td>

                                        @if ($accion['estado'] === 'COMPLETO')
                                            <td>{{ $accion['fechadocumento']->format('Y-m-d') }}</td>
                                        @else
                                            <td class="pendiente">PENDIENTE</td>
                                        @endif   

                                        @if ($accion['estado'] === 'COMPLETO')
                                            <td>
                                                @if (isset($accion['document']))
                                                    <a href="{{ asset('/documentacionclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['document']->document) }}" class="btn btn-verdocumentacion" target="_blank" title="VER RESULTADO MÉDICO">
                                                        <i class="fas fa-folder-open"></i>
                                                    </a>
                                                @endif
                                                @if (isset($accion['image']) && $accion['image']->image)
                                                    <a href="{{ asset('/documentacionclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['image']->image) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 1">
                                                        <i class="fas fa-images"></i>
                                                    </a>
                                                @endif
            
                                                @if (isset($accion['image2']) && $accion['image2']->image2)
                                                    <a href="{{ asset('/documentacionclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['image2']->image2) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 2">
                                                        <i class="far fa-images"></i>
                                                    </a>
                                                @endif
                                                @if (isset($accion['documentfirmado']) && $accion['documentfirmado']->documentfirmado)
                                                    <a href="{{ asset('/documentacionclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['documentfirmado']->documentfirmado) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME MÉDICO FIRMADO">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @endif
                                                @if (isset($accion['documentword']) && $accion['documentword']->documentword)
                                                    <a href="{{ asset('/documentacionclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['documentword']->documentword) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME MÉDICO WORD">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        @else
                                            <td class="pendiente">PENDIENTE</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- DOCUMENTACIÓN AUDITORIA-->
    <div class="modal fade" id="modalDocumentacionauditoria{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelDocumentacion{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabelDocumentacionauditoria{{ $loop->index }}">
                        <strong>DOCUMENTACIÓN AUDITORIA MEDICA</strong>
                    </h4>
                    <a type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Ver Doc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $mostrarcnacaseguradoau = !empty($accion['cnacaseguradoau']);
                                $mostrarcnacaseguradopendienteau = $accion['cnacaseguradoau'] === 'PENDIENTE';
                                $mostrarciaseguradoau = !empty($accion['ciaseguradoau']);
                                $mostrarciaseguradopendienteau = $accion['ciaseguradoau'] === 'PENDIENTE';
                            @endphp
                            @if ($nombreusuario != 'MARICELA COLQUE SANDOVAL' && $nombreusuario != 'MONICA MACOÑO FLORES' && $nombreusuario != 'SERRANO PORSTENDOERFER VIVIAN YANETH' && $nombreusuario != 'PROMED S.R.L.')
                                @if ($mostrarcnacaseguradoau || $mostrarcnacaseguradopendienteau)
                                    <tr>
                                        <td>CERTIFICADO NACIMIENTO DE ASEGURADO</td>
                                        <td>
                                            @if ($mostrarcnacaseguradopendienteau)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcnacaseguradoau)
                                                <a href="{{ asset('/requisitosclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['cnacaseguradoau']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endif
                            @if ($mostrarciaseguradoau || $mostrarciaseguradopendienteau)
                                <tr>
                                    <td>CARNET IDENTIDAD DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarciaseguradopendienteau)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciaseguradoau)
                                            <a href="{{ asset('/requisitosclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['ciaseguradoau']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($nombreusuario != 'MARICELA COLQUE SANDOVAL' && $nombreusuario != 'MONICA MACOÑO FLORES' && $nombreusuario != 'SERRANO PORSTENDOERFER VIVIAN YANETH' && $nombreusuario != 'PROMED S.R.L.')
                            <table class="table">
                                <thead>
                                    <tr> 
                                        <th>Banco</th>
                                        <th>Nro. Póliza General</th>
                                        <th>Póliza General</th>
                                        <th>Declaración Salud</th>
                                        <th>Nro. Póliza Desgravamen</th>
                                        <th>Poliza seguro desgravamen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requisitosClientepolizas as $requisito)
                                    <tr>
                                        <td>{{ $requisito->banco }}</td>
                                        <td>{{ $requisito->nropolizageneral ?? '' }}</td>
                                        <td>
                                            @if ($requisito->polizageneral === 'PENDIENTE')
                                                <div class="pendiente">PENDIENTE</div>
                                            @else
                                                <a href="{{ asset("/requisitosclientesauditoria/{$clienteauditoria->id}/{$requisito->polizageneral}") }}" target="_blank" class="verdoc">VER DOC.</a>
                                            @endif
                                        </td>
                                        <td> 
                                            @if ($requisito->declasalud === 'PENDIENTE')
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($requisito->declasalud === 'NO APLICA')
                                                <div class="noaplica">NO APLICA</div>
                                            @else
                                                <a href="{{ asset("/requisitosclientesauditoria/{$clienteauditoria->id}/{$requisito->declasalud}") }}" target="_blank" class="verdoc">VER DOC.</a>
                                            @endif
                                        </td>
                                        
                                        <td>{{ $requisito->nropolizadesgravamen ?? '' }}</td>
                                        <td>
                                            @if ($requisito->polizasegurodesgravamen === 'PENDIENTE')
                                                <div class="pendiente">PENDIENTE</div>
                                            @else
                                                <a href="{{ asset("/requisitosclientesauditoria/{$clienteauditoria->id}/{$requisito->polizasegurodesgravamen}") }}" target="_blank" class="verdoc">VER DOC.</a> 
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </tbody>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</a>
                </div>
            </div>
        </div>
    </div>
@endforeach

@stop

@section('js')
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
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
        $(document).ready(function() {
            $('input[name="buscarporfecha"], input[name="buscarporarea"]').on('keyup change', function() {
                var fechaSeleccionada = $('input[name="buscarporfecha"]').val();
                var areaSeleccionada = $('input[name="buscarporarea"]').val();
                var botonBuscar = $('#btn-buscar');
                
                if (fechaSeleccionada.trim() === '' && areaSeleccionada.trim() === '') {
                    botonBuscar.prop('disabled', true);
                } else {
                    botonBuscar.prop('disabled', false);
                }
            });
        });
    </script>
    <script>
        function cargarVistaPrevia() {
          var document = document.getElementById('document').files[0];
          if (document) {
            var reader = new FileReader();
            reader.onload = function(e) {
              var previewIframe = document.getElementById('document-preview');
              previewIframe.src = e.target.result;
            };
            reader.readAsDataURL(document);
          }
        }
      
        document.getElementById('document').addEventListener('change', function() {
          cargarVistaPrevia();
        });
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

        document.getElementById('document').addEventListener('change', function(event) {
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

    </script>
    <script>

        $('.formulario-eliminar').submit(function(e){
            e.preventDefault();

            Swal.fire({
            title: '¿Estás seguro?',
            text: "El rol se eliminará definitivamente",
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
    </script>
@endsection
