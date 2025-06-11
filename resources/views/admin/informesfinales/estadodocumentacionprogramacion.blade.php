@extends('adminlte::page')

@section('content_header')
<h1>RESULTADOS MÉDICOS CLIENTES ITA</h1>
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
                <form id="search-form" action="{{ route('buscarprogramacionescomclienteita') }}" method="get" class="form-inline">
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
                window.location.href = "{{ route('buscarprogramacionescomclienteita') }}";
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
                            @if ($item['estado'] === 'COMPLETO' && $item['estadoGeneral'] === 'COMPLETO')
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteitaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteitanombre'] }}</td>

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

                                    {{-- DOCUMENTACION REQUISITOS --}}
                                    <td>
                                        @if ($item['tramite'])
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            {{ $item['estadoGeneral'] }}
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        </p>
                                        @else
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnostico'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticos/' . $item['clienteitaid'] . '/' .$item['diagnostico']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                            @if ($item['estado'] === 'COMPLETO' && $item['estadoGeneral'] === 'PENDIENTE')
                            <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteitaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteitanombre'] }}</td>

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
                                        @if ($item['tramite'])
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            {{ $item['estadoGeneral'] }}
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        </p>
                                        @else
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>
                                    
                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnostico'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticos/' . $item['clienteitaid'] . '/' .$item['diagnostico']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                            </div>
                                        @else
                                            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $nombreusuario === 'YELKA MORALES VELARDE')
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <p class="text-incompleto mb-0">PENDIENTE </p>
                                                    <a type="button" class="btn btn-sm btn-incompleto" 
                                                    data-toggle="modal" 
                                                    data-target="#subirdiagnosticoModal"
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                            @if ($item['estadoGeneral'] === 'COMPLETO' && $item['estado'] === 'INCOMPLETO')
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteitaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteitanombre'] }}</td>

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
                                            <a class="btn btn-veracciones {{ $item['estado'] === 'INCOMPLETO' ? 'btn-danger' : '' }}" data-toggle="modal" data-target="#modal{{ $loop->index }}">
                                                <i class="fas fa-file-medical-alt"></i>
                                            </a>
                                        </abbr>
                                    </td>

                                    {{-- DOCUMENTACION REQUISITOS --}}
                                    <td>
                                        @if ($item['tramite'])
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            {{ $item['estadoGeneral'] }}
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        </p>
                                        @else
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnostico'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticos/' . $item['clienteitaid'] . '/' .$item['diagnostico']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                            @if ($item['estado'] === 'INCOMPLETO' && $item['estadoGeneral'] === 'PENDIENTE')
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteitaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteitanombre'] }}</td>

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
                                    
                                    {{-- ESTADO DE DOCUMENTACION --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones2" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    <td>
                                        @if ($item['tramite'])
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            {{ $item['estadoGeneral'] }}
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        </p>
                                        @else
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnostico'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticos/' . $item['clienteitaid'] . '/' .$item['diagnostico']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                                <th style="width: 15%;">Result_médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Diagnóstico</th>
                                <th style="width: 10%;">Hist_médica</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'COMPLETO' && $item['estadoGeneral'] === 'COMPLETO' && !is_null($item['diagnostico']))
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteitaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteitanombre'] }}</td>

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

                                    {{-- DOCUMENTACION REQUISITOS --}}
                                    <td>
                                        @if ($item['tramite'])
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            {{ $item['estadoGeneral'] }}
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        </p>
                                        @else
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnostico'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticos/' . $item['clienteitaid'] . '/' .$item['diagnostico']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                            @if ($item['estado'] === 'COMPLETO' && $item['estadoGeneral'] === 'PENDIENTE' && !is_null($item['diagnostico']))
                            <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteitaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteitanombre'] }}</td>

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
                                        @if ($item['tramite'])
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            {{ $item['estadoGeneral'] }}
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        </p>
                                        @else
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>
                                    
                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnostico'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticos/' . $item['clienteitaid'] . '/' .$item['diagnostico']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                            @if ($item['estadoGeneral'] === 'COMPLETO' && $item['estado'] === 'INCOMPLETO' && !is_null($item['diagnostico']))
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteitaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteitanombre'] }}</td>

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
                                            <a class="btn btn-veracciones {{ $item['estado'] === 'INCOMPLETO' ? 'btn-danger' : '' }}" data-toggle="modal" data-target="#modal{{ $loop->index }}">
                                                <i class="fas fa-file-medical-alt"></i>
                                            </a>
                                        </abbr>
                                    </td>

                                    {{-- DOCUMENTACION REQUISITOS --}}
                                    <td>
                                        @if ($item['tramite'])
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            {{ $item['estadoGeneral'] }}
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        </p>
                                        @else
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnostico'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticos/' . $item['clienteitaid'] . '/' .$item['diagnostico']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                            @if ($item['estado'] === 'INCOMPLETO' && $item['estadoGeneral'] === 'PENDIENTE' && !is_null($item['diagnostico']))
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteitaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteitanombre'] }}</td>

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
                                    
                                    {{-- ESTADO DE DOCUMENTACION --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones2" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    <td>
                                        @if ($item['tramite'])
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            {{ $item['estadoGeneral'] }}
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        </p>
                                        @else
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- DIAGNOSTICO --}}
                                    <td width="10px"> 
                                        @if($item['diagnostico'])
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="text-completo mb-0">COMPLETO </p>
                                                <a href="{{ asset('/diagnosticos/' . $item['clienteitaid'] . '/' .$item['diagnostico']) }}" class="btn btn-completo" target="_blank" title="VER DIAGNÓSTICO">
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
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
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/extracted/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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

    <!-- MODAL DIAGNÓSTICO -->
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
                <script>
                    $('#subirdiagnosticoModal').on('show.bs.modal', function (event) {
                        var button = $(event.relatedTarget);
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
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}"><strong>{{ $item['clienteitanombre'] }}</strong> - Fecha Bateria: {{ \Carbon\Carbon::parse($item['fechabateria'])->format('Y-m-d') }}</h4>
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
                                    <th>Result_Médico</th>
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
                                                    <a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['document']->document) }}" class="btn btn-verdocumentacion" target="_blank" title="VER RESULTADO MÉDICO">
                                                        <i class="fas fa-folder-open"></i>
                                                    </a>
                                                @endif
            
                                                @if (isset($accion['image']) && $accion['image']->image)
                                                    <a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['image']->image) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 1">
                                                        <i class="fas fa-images"></i>
                                                    </a>
                                                @endif
            
                                                @if (isset($accion['image2']) && $accion['image2']->image2)
                                                    <a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['image2']->image2) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 2">
                                                        <i class="far fa-images"></i>
                                                    </a>
                                                @endif
                                                @if (isset($accion['documentfirmado']) && $accion['documentfirmado']->documentfirmado)
                                                    <a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['documentfirmado']->documentfirmado) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME MÉDICO FIRMADO">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @endif

                                                @if (isset($accion['documentword']) && $accion['documentword']->documentword)
                                                    <a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['documentword']->documentword) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME MÉDICO WORD">
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
    {{-- <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}"><strong>{{ $item['clienteitanombre'] }}</strong> - Fecha Bateria: {{ \Carbon\Carbon::parse($item['fechabateria'])->format('Y-m-d') }}</h4>
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
                                    <th>Result_Médico</th>
                                </tr>
                            </thead>
                            <tbody id="accionList{{ $loop->index }}">
                                <!-- Aquí se cargarán dinámicamente los datos de los archivos -->
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
    <script>
        $(document).ready(function() {
        // Evento para abrir el modal
        $('#modal{{ $loop->index }}').on('shown.bs.modal', function() {
            var modalId = $(this).attr('id');
            var index = modalId.replace('modal', '');
            var accionList = $('#accionList' + index);
    
            // Limpiar contenido anterior
            accionList.empty();
    
            // Cargar dinámicamente los archivos de este modal
            @foreach ($result as $index => $item)
                if (index == {{ $loop->index }}) {
                    @foreach ($item['acciones'] as $accion)
                        var row = '<tr>';
                        row += '<td>{{ $accion['accion'] }}</td>';
                        row += '<td>{{ $accion['proveedornombre'] }}</td>';
                        row += '<td>{{ $accion['fechaasignada'] }}</td>';
                        row += '<td>{{ $accion['fechaatencionprogramacion'] ? $accion['fechaatencionprogramacion'] : 'PENDIENTE' }}</td>';
                        row += '<td>{{ $accion['estado'] === 'COMPLETO' ? $accion['fechadocumento']->format('Y-m-d') : 'PENDIENTE' }}</td>';
                        row += '<td>';
    
                        // Agregar enlaces de archivos según disponibilidad
                        @if (isset($accion['document']))
                            row += '<a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['document']->document) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-folder-open"></i></a>';
                        @endif
                        @if (isset($accion['image']) && $accion['image']->image)
                            row += '<a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['image']->image) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-images"></i></a>';
                        @endif
                        @if (isset($accion['image2']) && $accion['image2']->image2)
                            row += '<a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['image2']->image2) }}" class="btn btn-verdocumentacion" target="_blank"><i class="far fa-images"></i></a>';
                        @endif
                        @if (isset($accion['documentfirmado']) && $accion['documentfirmado']->documentfirmado)
                            row += '<a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['documentfirmado']->documentfirmado) }}" class="btn btn-sm btn-verinformefirmado" target="_blank"><i class="fas fa-file"></i></a>';
                        @endif
                        @if (isset($accion['documentword']) && $accion['documentword']->documentword)
                            row += '<a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['documentword']->documentword) }}" class="btn btn-sm btn-verinformeword" target="_blank"><i class="fas fa-file"></i></a>';
                        @endif
                        row += '</td>';
                        row += '</tr>';
                        accionList.append(row);
                    @endforeach
                }
            @endforeach
        });
        });
    
    </script> --}}
    <!-- DOCUMENTACIÓN -->
    <div class="modal fade" id="modalDocumentacion{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelDocumentacion{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabelDocumentacion{{ $loop->index }}">
                        <strong>DOCUMENTACION</strong>
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
                                <th>Servicio</th>
                                <th>Ver Doc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $mostrarpoder = !empty($accion['poder']);
                                $mostrarpoderpendiente = $accion['poder'] === 'PENDIENTE';
                                $mostraravcci = !empty($accion['avcci']);
                                $mostraravccipendiente = $accion['avcci'] === 'PENDIENTE';
                                $mostrarcnacasegurado = !empty($accion['cnacasegurado']);
                                $mostrarcnacaseguradopendiente = $accion['cnacasegurado'] === 'PENDIENTE';
                                $mostrarciasegurado = !empty($accion['ciasegurado']);
                                $mostrarciaseguradopendiente = $accion['ciasegurado'] === 'PENDIENTE';
                                $mostrarcimatrimonio = !empty($accion['cmatrimonio']);
                                $mostrarcimatrimoniopendiente = $accion['cmatrimonio'] === 'PENDIENTE';
                                $mostrarcnacconyuge = !empty($accion['cnacconyuge']);
                                $mostrarcnacconyugependiente = $accion['cnacconyuge'] === 'PENDIENTE';
                                $mostrarciconyuge = !empty($accion['ciconyuge']);
                                $mostrarciconyugependiente = $accion['ciconyuge'] === 'PENDIENTE';
                                $mostrarcnacjihos = !empty($accion['cnacjihos']);
                                $mostrarcnacjihospendiente = $accion['cnacjihos'] === 'PENDIENTE';
                                $mostrarcihijos = !empty($accion['cihijos']);
                                $mostrarcihijospendiente = $accion['cihijos'] === 'PENDIENTE';
                                $mostrardenfaccidente = !empty($accion['denfaccidente']);
                                $mostrardenfaccidentependiente = $accion['denfaccidente'] === 'PENDIENTE';
                                $mostrarcrodomicilio = !empty($accion['crodomicilio']);
                                $mostrarcrodomiciliopendiente = $accion['crodomicilio'] === 'PENDIENTE';
                                $mostrarcontrato = !empty($accion['contrato']);
                                $mostrarcontratopendiente = $accion['contrato'] === 'PENDIENTE';

                                $mostraregestora = !empty($accion['egestora']);
                                $mostraregestorapendiente = $accion['egestora'] === 'PENDIENTE';
                                $mostrardictamencalentenc = !empty($accion['dictamencalentenc']);
                                $mostrardictamencalentencpendiente = $accion['dictamencalentenc'] === 'PENDIENTE';
                                $mostrarinfomedicasalud = !empty($accion['infomedicasalud']);
                                $mostrarinfomedicasaludpendiente = $accion['infomedicasalud'] === 'PENDIENTE';
                                $mostrarctrabajo = !empty($accion['ctrabajo']);
                                $mostrarctrabajopendiente = $accion['ctrabajo'] === 'PENDIENTE';
                                $mostrarboletapago = !empty($accion['boletapago']);
                                $mostrarboletapagopendiente = $accion['boletapago'] === 'PENDIENTE';
                                $mostraractdatos = !empty($accion['actdatos']);
                                $mostraractdatospendiente = $accion['actdatos'] === 'PENDIENTE';
                                $mostrarresolinvhijos = !empty($accion['resolinvhijos']);
                                $mostrarresolinvhijospendiente = $accion['resolinvhijos'] === 'PENDIENTE';
                                $mostrarcunionlibre = !empty($accion['cunionlibre']);
                                $mostrarcunionlibrependiente = $accion['cunionlibre'] === 'PENDIENTE';
                                $mostrarcnacimientounionlibre = !empty($accion['cnacimientounionlibre']);
                                $mostrarcnacimientounionlibrependiente = $accion['cnacimientounionlibre'] === 'PENDIENTE';
                                $mostrarciunionlibre = !empty($accion['ciunionlibre']);
                                $mostrarciunionlibrependiente = $accion['ciunionlibre'] === 'PENDIENTE';
                                $mostrarcdivorcio = !empty($accion['cdivorcio']);
                                $mostrarcdivorciopendiente = $accion['cdivorcio'] === 'PENDIENTE';
                                $mostrarcdefuncion = !empty($accion['cdefuncion']);
                                $mostrarcdefuncionpendiente = $accion['cdefuncion'] === 'PENDIENTE';
                                $mostrarpolizasgen = !empty($accion['polizasgen']);
                                $mostrarpolizasgenpendiente = $accion['polizasgen'] === 'PENDIENTE';
                                $mostrardeclasalud = !empty($accion['declasalud']);
                                $mostrardeclasaludpendiente = $accion['declasalud'] === 'PENDIENTE';
                                $mostrarpolizaseguro = !empty($accion['polizaseguro']);
                                $mostrarpolizaseguropendiente = $accion['polizaseguro'] === 'PENDIENTE';

                                $mostraranteriordictamen = !empty($accion['anteriordictamen']);
                                $mostraranteriordictamenpendiente = $accion['anteriordictamen'] === 'PENDIENTE';
                                $mostrarpoderciapoderado = !empty($accion['poderciapoderado']);
                                $mostrarpoderciapoderadopendiente = $accion['poderciapoderado'] === 'PENDIENTE';
                            @endphp


                            @if ($nombreusuario != 'MARICELA COLQUE SANDOVAL' && $nombreusuario != 'MONICA MACOÑO FLORES' && $nombreusuario != 'SERRANO PORSTENDOERFER VIVIAN YANETH' && $nombreusuario != 'PROMED S.R.L.')
                                @if ($mostrarpoder || $mostrarpoderpendiente)
                                    <tr>
                                        <td>PODER: {{ $accion['numeropoder'] }}</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarpoderpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarpoder)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['poder']) }}" class="btn btn-verdocumentacion" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostraravcci || $mostraravccipendiente)
                                    <tr>
                                        <td>AVC / CARNET DE ASEGURADO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostraravccipendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostraravcci)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['avcci']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcnacasegurado || $mostrarcnacaseguradopendiente)
                                    <tr>
                                        <td>CERTIFICADO NACIMIENTO DE ASEGURADO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcnacaseguradopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcnacasegurado)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacasegurado']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endif

                            @if ($mostrarciasegurado || $mostrarciaseguradopendiente)
                                <tr>
                                    <td>CARNET IDENTIDAD DE ASEGURADO</td>
                                    <td>{{ $accion['servicio'] }}</td>
                                    <td>
                                        @if ($mostrarciaseguradopendiente)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciasegurado)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciasegurado']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @if ($nombreusuario != 'MARICELA COLQUE SANDOVAL' && $nombreusuario != 'MONICA MACOÑO FLORES' && $nombreusuario != 'SERRANO PORSTENDOERFER VIVIAN YANETH' && $nombreusuario != 'PROMED S.R.L.')
                                @if ($mostrarcimatrimonio || $mostrarcimatrimoniopendiente)
                                    <tr>
                                        <td>CERTIFICADO DE MATRIMONIO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcimatrimoniopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcimatrimonio)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cmatrimonio']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>   
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcnacconyuge || $mostrarcnacconyugependiente)
                                    <tr>
                                        <td>CERTIFICADO NACIMIENTO DE CONYUGE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcnacconyugependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcnacconyuge)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacconyuge']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarciconyuge || $mostrarciconyugependiente)
                                    <tr>
                                        <td>CARNET IDENTIDAD DE CONYUGE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarciconyugependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarciconyuge)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciconyuge']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcnacjihos || $mostrarcnacjihospendiente)
                                    <tr>
                                        <td>CERTIFICADO NACIMIENTO DE HIJOS < 25</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcnacjihospendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcnacjihos)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacjihos']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcihijos || $mostrarcihijospendiente)
                                    <tr>
                                        <td>CARNET IDENTIDAD DE HIJOS < 25</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcihijospendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcihijos)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cihijos']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrardenfaccidente || $mostrardenfaccidentependiente)
                                    <tr>
                                        <td>DENUNCIA ENFERMEDAD ACCIDENTE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrardenfaccidentependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrardenfaccidente)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['denfaccidente']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcrodomicilio || $mostrarcrodomiciliopendiente)
                                    <tr>
                                        <td>CROQUIS DE DOMICILIO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcrodomiciliopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcrodomicilio)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['crodomicilio']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcontrato || $mostrarcontratopendiente)
                                    <tr>
                                        <td>CONTRATO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcontratopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcontrato)
                                                @if ($userRole === 'MAESTRO' || $userRole === 'ADMINISTRADOR')
                                                    <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['contrato']) }}" class="btn btn-verdocumentacion" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a> 
                                                @else
                                                    <a class="btn btn-verdocumentacion" disabled>
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostraregestora || $mostraregestorapendiente)
                                    <tr>
                                        <td>EXTRACTO DE GESTORA</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostraregestorapendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostraregestora)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['egestora']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrardictamencalentenc || $mostrardictamencalentencpendiente)
                                    <tr>
                                        <td>DICTAMEN CALIFICACION ENTIDAD ENCARGADA</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrardictamencalentencpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrardictamencalentenc)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['dictamencalentenc']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarinfomedicasalud || $mostrarinfomedicasaludpendiente)
                                    <tr>
                                        <td>INFORMACION MEDICA</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarinfomedicasaludpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarinfomedicasalud)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['infomedicasalud']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarctrabajo || $mostrarctrabajopendiente)
                                    <tr>
                                        <td>CERTIFICADO DE TRABAJO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarctrabajopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarctrabajo)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ctrabajo']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarboletapago || $mostrarboletapagopendiente)
                                    <tr>
                                        <td>BOLETA DE PAGO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarboletapagopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarboletapago)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['boletapago']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostraractdatos || $mostraractdatospendiente)
                                    <tr>
                                        <td>ACTUALIZACION DE DATOS</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostraractdatospendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostraractdatos)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['actdatos']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarresolinvhijos || $mostrarresolinvhijospendiente)
                                    <tr>
                                        <td>RESOLUCION INVALIDEZ DE HIJOS < 25</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarresolinvhijospendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarresolinvhijos)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['resolinvhijos']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcunionlibre || $mostrarcunionlibrependiente)
                                    <tr>
                                        <td>CERTIFICADO DE UNION LIBRE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcunionlibrependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcunionlibre)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cunionlibre']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcnacimientounionlibre || $mostrarcnacimientounionlibrependiente)
                                    <tr>
                                        <td>CERTIFICADO NACIMIENTO DE UNION LIBRE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcnacimientounionlibrependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcnacimientounionlibre)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacimientounionlibre']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarciunionlibre || $mostrarciunionlibrependiente)
                                    <tr>
                                        <td>CARNET IDENTIDAD DE UNION LIBRE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarciunionlibrependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarciunionlibre)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciunionlibre']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcdivorcio || $mostrarcdivorciopendiente)
                                    <tr>
                                        <td>CERTIFICADO DE DIVORCIO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcdivorciopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcdivorcio)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cdivorcio']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcdefuncion || $mostrarcdefuncionpendiente)
                                    <tr>
                                        <td>CERTIFICADO DE DIFUNCION</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcdefuncionpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcdefuncion)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cdefuncion']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarpolizasgen || $mostrarpolizasgenpendiente)
                                    <tr>
                                        <td>POLIZAS GENERALES</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarpolizasgenpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarpolizasgen)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['polizasgen']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrardeclasalud || $mostrardeclasaludpendiente)
                                    <tr>
                                        <td>DECLARACION SALUD</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrardeclasaludpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrardeclasalud)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['declasalud']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarpolizaseguro || $mostrarpolizaseguropendiente)
                                    <tr>
                                        <td>POLIZA SEGURO DESGRAVAMEN</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarpolizaseguropendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarpolizaseguro)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['polizaseguro']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostraranteriordictamen || $mostraranteriordictamenpendiente)
                                    <tr>
                                        <td>ANTERIOR DICTAMEN O RESOLUCION</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostraranteriordictamenpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostraranteriordictamen)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['anteriordictamen']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarpoderciapoderado || $mostrarpoderciapoderadopendiente)
                                    <tr>
                                        <td>PODER Y CARNET IDENTIDAD APODERADO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarpoderciapoderadopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarpoderciapoderado)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['poderciapoderado']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endif

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

