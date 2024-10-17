@extends('adminlte::page')

@section('content_header')
<h1>RESULTADOS MÉDICOS CLIENTES ITA</h1>
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
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="Nombre del Cliente">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                    <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" type="button">Mostrar Todo</button>
                </form>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarprogramacionescomclienteita') }}";
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

            {{-- 100% COMPLETOS --}}
            <div class="tab-pane fade show active" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 20%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 25%;">Fecha Batería - Servicio</th>
                                <th style="width: 15%;">Result. médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Hist. médica</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'COMPLETO' && $item['estadoGeneral'] === 'COMPLETO' || $item['estadoGeneralss'] === 'COMPLETO' || $item['estadoGeneralap'] === 'COMPLETO' || $item['estadoGeneralauditoria'] === 'COMPLETO')
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
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }} {{-- Muestra la fecha seguida de los trámites --}}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO {{-- Muestra la fecha con un mensaje si no hay trámites --}}
                                        @endif
                                    </td>

                                    {{-- RESULTADOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <button class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></button>
                                        </abbr>
                                    </td>

                                    {{-- DOCUMENTACION --}}
                                    {{-- <td class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                        {{ $item['estadoGeneral'] }}
                                        @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                            <abbr title="VER DOCUMENTACIÓN">
                                                <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                    <i class="fas fa-address-book"></i>
                                                </button>
                                            </abbr>
                                        @endif
                                    </td> --}}
                                    {{-- DOCUMENTACION --}}
                                    <td>
                                        @if (in_array('INVALIDEZ', $item['tramite']))
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            INVALIDEZ
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('APELACION', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralap'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralap'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            APELACION
                                            @if ($item['estadoGeneralap'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralap'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralap'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionapelacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SEGUNDA SOLICITUD', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralss'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralss'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            SEG. SOLICITUD
                                            @if ($item['estadoGeneralss'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralss'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralss'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionsegundasolicitud{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                                <th style="width: 20%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 25%;">Fecha Batería - Servicio</th>
                                <th style="width: 15%;">Result. médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Hist. médica</th>
                            </tr>                            
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            {{-- @if ($item['estado'] === 'COMPLETO' && $item['estadoGeneral'] === 'PENDIENTE' || $item['estadoGeneralauditoria'] === 'PENDIENTE') --}}
                            @if ($item['estado'] === 'COMPLETO' && ($item['estadoGeneral'] === 'PENDIENTE' || $item['estadoGeneralauditoria'] === 'PENDIENTE' || $item['estadoGeneralss'] === 'PENDIENTE' || $item['estadoGeneralap'] === 'PENDIENTE'))

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
                                            <button class="btn btn-veracciones {{ $item['estado'] === 'INCOMPLETO' ? 'btn-danger' : '' }}" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></button>
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
                                        @if (in_array('INVALIDEZ', $item['tramite']))
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            INVALIDEZ
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('APELACION', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralap'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralap'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            APELACION
                                            @if ($item['estadoGeneralap'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralap'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralap'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionapelacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SEGUNDA SOLICITUD', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralss'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralss'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            SEG. SOLICITUD
                                            @if ($item['estadoGeneralss'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralss'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralss'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionsegundasolicitud{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>
                                    
                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                                <th style="width: 20%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 25%;">Fecha Batería - Servicio</th>
                                <th style="width: 15%;">Result. médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Hist. médica</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'INCOMPLETO' && ($item['estadoGeneral'] === 'COMPLETO' || $item['estadoGeneralauditoria'] === 'COMPLETO' || $item['estadoGeneralss'] === 'COMPLETO' || $item['estadoGeneralap'] === 'COMPLETO'))
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
                                    {{-- <td>{{ $item['fechabateria'] }} - {{ $item['tramite'] }}</td> --}}
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }} {{-- Muestra la fecha seguida de los trámites --}}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO {{-- Muestra la fecha con un mensaje si no hay trámites --}}
                                        @endif
                                    </td>


                                    {{-- RESULTADOS MEDICOS --}}
                                    {{-- <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <button class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></button>
                                        </abbr>
                                    </td> --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <button class="btn btn-veracciones {{ $item['estado'] === 'INCOMPLETO' ? 'btn-danger' : '' }}" data-toggle="modal" data-target="#modal{{ $loop->index }}">
                                                <i class="fas fa-file-medical-alt"></i>
                                            </button>
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
                                    {{-- <td class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                        {{ $item['estadoGeneral'] }}
                                        @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                            <abbr title="VER DOCUMENTACIÓN">
                                                <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                    <i class="fas fa-address-book"></i>
                                                </button>
                                            </abbr>
                                        @endif
                                    </td> --}}
                                    <td>
                                        @if (in_array('INVALIDEZ', $item['tramite']))
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            INVALIDEZ
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('APELACION', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralap'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralap'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            APELACION
                                            @if ($item['estadoGeneralap'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralap'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralap'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionapelacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SEGUNDA SOLICITUD', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralss'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralss'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            SEG. SOLICITUD
                                            @if ($item['estadoGeneralss'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralss'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralss'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionsegundasolicitud{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>
                                    

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
                                <th style="width: 20%;">Cliente</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 25%;">Fecha Batería - Servicio</th>
                                <th style="width: 15%;">Result. médicos</th>
                                <th style="width: 15%;">Documentación</th>
                                <th style="width: 10%;">Hist. médica</th>
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
                                    {{-- <td>{{ $item['fechabateria'] }} - {{ $item['tramite'] }}</td>--}}
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }} {{-- Muestra la fecha seguida de los trámites --}}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO {{-- Muestra la fecha con un mensaje si no hay trámites --}}
                                        @endif
                                    </td>
                                    
                                    {{-- ESTADO DE DOCUMENTACION --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <button class="btn btn-veracciones2" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></button>
                                        </abbr>
                                    </td>

                                    {{-- DOCUMENTACION --}}
                                    {{-- <td class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                        {{ $item['estadoGeneral'] }}
                                        @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                            <abbr title="VER DOCUMENTACIÓN">
                                                <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                    <i class="fas fa-address-book"></i>
                                                </button>
                                            </abbr>
                                        @endif
                                    </td> --}}
                                    <td>
                                        @if (in_array('INVALIDEZ', $item['tramite']))
                                        <p class="{{ $item['estadoGeneral'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            INVALIDEZ
                                            @if ($item['estadoGeneral'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneral'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('AUDITORIA MEDICA', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralauditoria'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            AUD. MEDICA
                                            @if ($item['estadoGeneralauditoria'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralauditoria'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneral'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionauditoria{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('APELACION', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralap'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralap'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            APELACION
                                            @if ($item['estadoGeneralap'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralap'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralap'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionapelacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SEGUNDA SOLICITUD', $item['tramite']))
                                        <p class="{{ $item['estadoGeneralss'] === 'COMPLETO' ? 'text-completo' : ($item['estadoGeneralss'] === 'PENDIENTE' ? 'text-incompleto' : 'text-noregistrado') }}">
                                            SEG. SOLICITUD
                                            @if ($item['estadoGeneralss'] !== 'NO REGISTRADO')
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <button class="btn btn-requisitosdocumentos {{ $item['estadoGeneralss'] === 'COMPLETO' ? 'btn-completo' : ($item['estadoGeneralss'] === 'PENDIENTE' ? 'btn-incompleto' : 'btn-noregistrado') }}" data-toggle="modal" data-target="#modalDocumentacionsegundasolicitud{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </button>
                                                </abbr>
                                            @endif
                                            </p>
                                        @endif

                                        @if (in_array('SIN SERVICIO', $item['tramite']))
                                            <p class="text-noregistrado">NO REGISTRADO</p>
                                        @endif
                                    </td>

                                    {{-- HISTORIA MEDICA --}}
                                    <td>
                                        @if ($item['historiamedica'])
                                        <p class="text-completo">VER
                                        <abbr title="VER HISTORIA MÉDICA">
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] . '/' . $item['historiamedica']) }}" class="btn btn-completo" target="_blank">
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
            <div class="tab-pane fade" id="tab-content-6" role="tabpanel" aria-labelledby="tab-6">
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
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteitaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteitanombre'] }}</td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>

                                    {{-- ESTADO DE DOCUMENTACION --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <button class="btn btn-veracciones2" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></button>
                                        </abbr>
                                    </td>

                                    {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <button class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </button>
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
            </div>
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

    {{-- RESULTADOS MEDICOS --}}
    <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}"><strong>{{ $item['clienteitanombre'] }}</strong> - Fecha Bateria: {{ \Carbon\Carbon::parse($item['fechabateria'])->format('Y-m-d') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive d-block d-md-none">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    {{-- <th>Fecha Bateria</th> --}}
                                    <th>Acción</th>
                                    <th>Proveedor</th>
                                    <th>Programación</th>
                                    <th>Atención</th>
                                    <th>Result. Médico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item['acciones'] as $accion)
                                    <tr>
                                        {{-- <td>{{ $accion['creacionbateria']->format('Y-m-d') }}</td> --}}
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
                                        <td>
                                            @if ($accion['estado'] === 'COMPLETO')
                                                {{ $accion['fechadocumento']->format('Y-m-d') }}
                                                
                                                <!-- Contenedor para el botón y los documentos -->
                                                <div class="dropdown-container">
                                                    <button class="btn btn-dropdown" type="button">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
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
                                                    </div>
                                                </div>
                                                
                                            @else
                                                <div class="pendiente">PENDIENTE</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-none d-md-block">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    {{-- <th>Fecha Bateria</th> --}}
                                    <th>Acción</th>
                                    <th>Proveedor</th>
                                    <th>Programación</th>
                                    <th>Atención</th>
                                    <th>Result. Médico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item['acciones'] as $accion)
                                    <tr>
                                        {{-- <td>{{ $accion['creacionbateria']->format('Y-m-d') }}</td> --}}
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
                                        <td>
                                            @if ($accion['estado'] === 'COMPLETO')
                                                {{ $accion['fechadocumento']->format('Y-m-d') }}
                                                
                                                <!-- Contenedor para el botón y los documentos -->
                                                <div class="dropdown-container">
                                                    <button class="btn btn-dropdown" type="button">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
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
                                                    </div>
                                                </div>
                                                
                                            @else
                                                <div class="pendiente">PENDIENTE</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- DOCUMENTACIÓN -->
    <div class="modal fade" id="modalDocumentacion{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelDocumentacion{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabelDocumentacion{{ $loop->index }}">
                        <strong>DOCUMENTACIÓN INVALIDEZ</strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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

                            @if ($mostrarpoder || $mostrarpoderpendiente)
                                <tr>
                                    <td>PODER: {{ $accion['numeropoder'] }}</td>
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
                                    <td>
                                        @if ($mostrarcnacaseguradopendiente)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacasegurado)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacasegurado']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarciasegurado || $mostrarciaseguradopendiente)
                                <tr>
                                    <td>CARNET IDENTIDAD DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarciaseguradopendiente)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciasegurado)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciasegurado']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcimatrimonio || $mostrarcimatrimoniopendiente)
                                <tr>
                                    <td>CERTIFICADO DE MATRIMONIO</td>
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
                                    <td>
                                        @if ($mostrarcontratopendiente)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcontrato)
                                            @if ($userRole === 'MAESTRO' || $userRole === 'ADMINISTRADOR')
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['contrato']) }}" class="btn btn-verdocumentacion" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a> 
                                            @else
                                                <button class="btn btn-verdocumentacion" disabled>
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostraregestora || $mostraregestorapendiente)
                                <tr>
                                    <td>EXTRACTO DE GESTORA</td>
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
                                    <td>
                                        @if ($mostrarpoderciapoderadopendiente)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarpoderciapoderado)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['poderciapoderado']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- DOCUMENTACIÓN SEGUNDA SOLICITUD-->
    <div class="modal fade" id="modalDocumentacionsegundasolicitud{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelDocumentacion{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabelDocumentacionsegundasolicitud{{ $loop->index }}">
                        <strong>DOCUMENTACIÓN SEGUNDA SOLICITUD</strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                                $mostrarpoderss = !empty($accion['poderss']);
                                $mostrarpoderpendientess = $accion['poderss'] === 'PENDIENTE';
                                $mostraravcciss = !empty($accion['avcciss']);
                                $mostraravccipendientess = $accion['avcciss'] === 'PENDIENTE';
                                $mostrarcnacaseguradoss = !empty($accion['cnacaseguradoss']);
                                $mostrarcnacaseguradopendientess = $accion['cnacaseguradoss'] === 'PENDIENTE';
                                $mostrarciaseguradoss = !empty($accion['ciaseguradoss']);
                                $mostrarciaseguradopendientess = $accion['ciaseguradoss'] === 'PENDIENTE';
                                $mostrarcimatrimonioss = !empty($accion['cmatrimonioss']);
                                $mostrarcimatrimoniopendientess = $accion['cmatrimonioss'] === 'PENDIENTE';
                                $mostrarcnacconyugess = !empty($accion['cnacconyugess']);
                                $mostrarcnacconyugependientess = $accion['cnacconyugess'] === 'PENDIENTE';
                                $mostrarciconyugess = !empty($accion['ciconyugess']);
                                $mostrarciconyugependientess = $accion['ciconyugess'] === 'PENDIENTE';
                                $mostrarcnacjihosss = !empty($accion['cnacjihosss']);
                                $mostrarcnacjihospendientess = $accion['cnacjihosss'] === 'PENDIENTE';
                                $mostrarcihijosss = !empty($accion['cihijosss']);
                                $mostrarcihijospendientess = $accion['cihijosss'] === 'PENDIENTE';
                                $mostrardenfaccidentess = !empty($accion['denfaccidentess']);
                                $mostrardenfaccidentependientess = $accion['denfaccidentess'] === 'PENDIENTE';
                                $mostrarcrodomicilioss = !empty($accion['crodomicilioss']);
                                $mostrarcrodomiciliopendientess = $accion['crodomicilioss'] === 'PENDIENTE';
                                $mostrarcontratoss = !empty($accion['contratoss']);
                                $mostrarcontratopendientess = $accion['contratoss'] === 'PENDIENTE';
                                $mostraregestorass = !empty($accion['egestorass']);
                                $mostraregestorapendientess = $accion['egestorass'] === 'PENDIENTE';
                                $mostrardictamencalentencss = !empty($accion['dictamencalentencss']);
                                $mostrardictamencalentencpendientess = $accion['dictamencalentencss'] === 'PENDIENTE';
                                $mostrarinfomedicasaludss = !empty($accion['infomedicasaludss']);
                                $mostrarinfomedicasaludpendientess = $accion['infomedicasaludss'] === 'PENDIENTE';
                                $mostrarctrabajoss = !empty($accion['ctrabajoss']);
                                $mostrarctrabajopendientess = $accion['ctrabajoss'] === 'PENDIENTE';
                                $mostrarboletapagoss = !empty($accion['boletapagoss']);
                                $mostrarboletapagopendientess = $accion['boletapagoss'] === 'PENDIENTE';
                                $mostraractdatosss = !empty($accion['actdatosss']);
                                $mostraractdatospendientess = $accion['actdatosss'] === 'PENDIENTE';
                                $mostrarresolinvhijosss = !empty($accion['resolinvhijosss']);
                                $mostrarresolinvhijospendientess = $accion['resolinvhijosss'] === 'PENDIENTE';
                                $mostrarcunionlibress = !empty($accion['cunionlibress']);
                                $mostrarcunionlibrependientess = $accion['cunionlibress'] === 'PENDIENTE';
                                $mostrarcnacimientounionlibress = !empty($accion['cnacimientounionlibress']);
                                $mostrarcnacimientounionlibrependientess = $accion['cnacimientounionlibress'] === 'PENDIENTE';
                                $mostrarciunionlibress = !empty($accion['ciunionlibress']);
                                $mostrarciunionlibrependientess = $accion['ciunionlibress'] === 'PENDIENTE';
                                $mostrarcdivorcioss = !empty($accion['cdivorcioss']);
                                $mostrarcdivorciopendientess = $accion['cdivorcioss'] === 'PENDIENTE';
                                $mostrarcdefuncionss = !empty($accion['cdefuncionss']);
                                $mostrarcdefuncionpendientess = $accion['cdefuncionss'] === 'PENDIENTE';
                                $mostrarpolizasgenss = !empty($accion['polizasgenss']);
                                $mostrarpolizasgenpendientess = $accion['polizasgenss'] === 'PENDIENTE';
                                $mostrardeclasaludss = !empty($accion['declasaludss']);
                                $mostrardeclasaludpendientess = $accion['declasaludss'] === 'PENDIENTE';
                                $mostrarpolizaseguross = !empty($accion['polizaseguross']);
                                $mostrarpolizaseguropendientess = $accion['polizaseguross'] === 'PENDIENTE';
                                $mostraranteriordictamenss = !empty($accion['anteriordictamenss']);
                                $mostraranteriordictamenpendientess = $accion['anteriordictamenss'] === 'PENDIENTE';
                                $mostrarpoderciapoderadoss = !empty($accion['poderciapoderadoss']);
                                $mostrarpoderciapoderadopendientess = $accion['poderciapoderadoss'] === 'PENDIENTE';
                            @endphp

                            @if ($mostrarpoderss || $mostrarpoderpendientess)
                                <tr>
                                    <td>PODER: {{ $accion['numeropoderss'] }}</td>
                                    <td>
                                        @if ($mostrarpoderpendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarpoderss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['poderss']) }}" class="btn btn-verdocumentacion" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostraravcci || $mostraravccipendiente)
                                <tr>
                                    <td>AVC / CARNET DE ASEGURADO</td>
                                    <td>
                                        @if ($mostraravccipendiente)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostraravcci)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['avcci']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcnacaseguradoss || $mostrarcnacaseguradopendientess)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarcnacaseguradopendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacaseguradoss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacaseguradoss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarciaseguradoss || $mostrarciaseguradopendientess)
                                <tr>
                                    <td>CARNET IDENTIDAD DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarciaseguradopendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciaseguradoss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciaseguradoss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcimatrimonioss || $mostrarcimatrimoniopendientess)
                                <tr>
                                    <td>CERTIFICADO DE MATRIMONIO</td>
                                    <td>
                                        @if ($mostrarcimatrimoniopendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcimatrimonioss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cmatrimonioss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>   
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcnacconyugess || $mostrarcnacconyugependientess)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE CONYUGE</td>
                                    <td>
                                        @if ($mostrarcnacconyugependientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacconyugess)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacconyugess']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarciconyugess || $mostrarciconyugependientess)
                                <tr>
                                    <td>CARNET IDENTIDAD DE CONYUGE</td>
                                    <td>
                                        @if ($mostrarciconyugependientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciconyugess)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciconyugess']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcnacjihosss || $mostrarcnacjihospendientess)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE HIJOS < 25</td>
                                    <td>
                                        @if ($mostrarcnacjihospendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacjihosss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacjihosss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcihijosss || $mostrarcihijospendientess)
                                <tr>
                                    <td>CARNET IDENTIDAD DE HIJOS < 25</td>
                                    <td>
                                        @if ($mostrarcihijospendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcihijosss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cihijosss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrardenfaccidentess || $mostrardenfaccidentependientess)
                                <tr>
                                    <td>DENUNCIA ENFERMEDAD ACCIDENTE</td>
                                    <td>
                                        @if ($mostrardenfaccidentependientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrardenfaccidentess)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['denfaccidentess']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcrodomicilioss || $mostrarcrodomiciliopendientess)
                                <tr>
                                    <td>CROQUIS DE DOMICILIO</td>
                                    <td>
                                        @if ($mostrarcrodomiciliopendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcrodomicilioss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['crodomicilioss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcontratoss || $mostrarcontratopendientess)
                                <tr>
                                    <td>CONTRATO</td>
                                    <td>
                                        @if ($mostrarcontratopendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcontratoss)
                                            @if ($userRole === 'MAESTRO' || $userRole === 'ADMINISTRADOR')
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['contratoss']) }}" class="btn btn-verdocumentacion" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a> 
                                            @else
                                                <button class="btn btn-verdocumentacion" disabled>
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostraregestorass || $mostraregestorapendientess)
                                <tr>
                                    <td>EXTRACTO DE GESTORA</td>
                                    <td>
                                        @if ($mostraregestorapendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostraregestorass)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['egestorass']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrardictamencalentencss || $mostrardictamencalentencpendientess)
                                <tr>
                                    <td>DICTAMEN CALIFICACION ENTIDAD ENCARGADA</td>
                                    <td>
                                        @if ($mostrardictamencalentencpendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrardictamencalentencss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['dictamencalentencss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarinfomedicasaludss || $mostrarinfomedicasaludpendientess)
                                <tr>
                                    <td>INFORMACION MEDICA</td>
                                    <td>
                                        @if ($mostrarinfomedicasaludpendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarinfomedicasaludss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['infomedicasaludss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarctrabajoss || $mostrarctrabajopendientess)
                                <tr>
                                    <td>CERTIFICADO DE TRABAJO</td>
                                    <td>
                                        @if ($mostrarctrabajopendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarctrabajoss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ctrabajoss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarboletapagoss || $mostrarboletapagopendientess)
                                <tr>
                                    <td>BOLETA DE PAGO</td>
                                    <td>
                                        @if ($mostrarboletapagopendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarboletapagoss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['boletapagoss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostraractdatosss || $mostraractdatospendientess)
                                <tr>
                                    <td>ACTUALIZACION DE DATOS</td>
                                    <td>
                                        @if ($mostraractdatospendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostraractdatosss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['actdatosss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarresolinvhijosss || $mostrarresolinvhijospendientess)
                                <tr>
                                    <td>RESOLUCION INVALIDEZ DE HIJOS < 25</td>
                                    <td>
                                        @if ($mostrarresolinvhijospendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarresolinvhijosss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['resolinvhijosss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcunionlibress || $mostrarcunionlibrependientess)
                                <tr>
                                    <td>CERTIFICADO DE UNION LIBRE</td>
                                    <td>
                                        @if ($mostrarcunionlibrependientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcunionlibress)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cunionlibress']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcnacimientounionlibress || $mostrarcnacimientounionlibrependientess)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE UNION LIBRE</td>
                                    <td>
                                        @if ($mostrarcnacimientounionlibrependientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacimientounionlibress)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacimientounionlibress']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarciunionlibress || $mostrarciunionlibrependientess)
                                <tr>
                                    <td>CARNET IDENTIDAD DE UNION LIBRE</td>
                                    <td>
                                        @if ($mostrarciunionlibrependientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciunionlibress)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciunionlibress']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcdivorcioss || $mostrarcdivorciopendientess)
                                <tr>
                                    <td>CERTIFICADO DE DIVORCIO</td>
                                    <td>
                                        @if ($mostrarcdivorciopendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcdivorcioss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cdivorcioss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcdefuncionss || $mostrarcdefuncionpendientess)
                                <tr>
                                    <td>CERTIFICADO DE DIFUNCION</td>
                                    <td>
                                        @if ($mostrarcdefuncionpendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcdefuncionss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cdefuncionss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarpolizasgenss || $mostrarpolizasgenpendientess)
                                <tr>
                                    <td>POLIZAS GENERALES</td>
                                    <td>
                                        @if ($mostrarpolizasgenpendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarpolizasgenss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['polizasgenss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrardeclasaludss || $mostrardeclasaludpendientess)
                                <tr>
                                    <td>DECLARACION SALUD</td>
                                    <td>
                                        @if ($mostrardeclasaludpendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrardeclasaludss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['declasaludss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarpolizaseguross || $mostrarpolizaseguropendientess)
                                <tr>
                                    <td>POLIZA SEGURO DESGRAVAMEN</td>
                                    <td>
                                        @if ($mostrarpolizaseguropendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarpolizaseguross)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['polizaseguross']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostraranteriordictamenss || $mostraranteriordictamenpendientess)
                                <tr>
                                    <td>ANTERIOR DICTAMEN O RESOLUCION</td>
                                    <td>
                                        @if ($mostraranteriordictamenpendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostraranteriordictamenss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['anteriordictamenss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarpoderciapoderadoss || $mostrarpoderciapoderadopendientess)
                                <tr>
                                    <td>PODER Y CARNET IDENTIDAD APODERADO</td>
                                    <td>
                                        @if ($mostrarpoderciapoderadopendientess)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarpoderciapoderadoss)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['poderciapoderadoss']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            
                        </tbody>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- DOCUMENTACIÓN APELACIÓN-->
    <div class="modal fade" id="modalDocumentacionapelacion{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelDocumentacion{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabelDocumentacion{{ $loop->index }}">
                        <strong>DOCUMENTACIÓN APELACION</strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                                $mostrarpoderap = !empty($accion['poderap']);
                                $mostrarpoderpendienteap = $accion['poderap'] === 'PENDIENTE';
                                $mostraravcciap = !empty($accion['avcciap']);
                                $mostraravccipendienteap = $accion['avcciap'] === 'PENDIENTE';
                                $mostrarcnacaseguradoap = !empty($accion['cnacaseguradoap']);
                                $mostrarcnacaseguradopendienteap = $accion['cnacaseguradoap'] === 'PENDIENTE';
                                $mostrarciaseguradoap = !empty($accion['ciaseguradoap']);
                                $mostrarciaseguradopendienteap = $accion['ciaseguradoap'] === 'PENDIENTE';
                                $mostrarcimatrimonioap = !empty($accion['cmatrimonioap']);
                                $mostrarcimatrimoniopendienteap = $accion['cmatrimonioap'] === 'PENDIENTE';
                                $mostrarcnacconyugeap = !empty($accion['cnacconyugeap']);
                                $mostrarcnacconyugependienteap = $accion['cnacconyugeap'] === 'PENDIENTE';
                                $mostrarciconyugeap = !empty($accion['ciconyugeap']);
                                $mostrarciconyugependienteap = $accion['ciconyugeap'] === 'PENDIENTE';
                                $mostrarcnacjihosap = !empty($accion['cnacjihosap']);
                                $mostrarcnacjihospendienteap = $accion['cnacjihosap'] === 'PENDIENTE';
                                $mostrarcihijosap = !empty($accion['cihijosap']);
                                $mostrarcihijospendienteap = $accion['cihijosap'] === 'PENDIENTE';
                                $mostrardenfaccidenteap = !empty($accion['denfaccidenteap']);
                                $mostrardenfaccidentependienteap = $accion['denfaccidenteap'] === 'PENDIENTE';
                                $mostrarcrodomicilioap = !empty($accion['crodomicilioap']);
                                $mostrarcrodomiciliopendienteap = $accion['crodomicilioap'] === 'PENDIENTE';
                                $mostrarcontratoap = !empty($accion['contratoap']);
                                $mostrarcontratopendienteap = $accion['contratoap'] === 'PENDIENTE';
                                $mostraregestoraap = !empty($accion['egestoraap']);
                                $mostraregestorapendienteap = $accion['egestoraap'] === 'PENDIENTE';
                                $mostrardictamencalentencap = !empty($accion['dictamencalentencap']);
                                $mostrardictamencalentencpendienteap = $accion['dictamencalentencap'] === 'PENDIENTE';
                                $mostrarinfomedicasaludap = !empty($accion['infomedicasaludap']);
                                $mostrarinfomedicasaludpendienteap = $accion['infomedicasaludap'] === 'PENDIENTE';
                                $mostrarctrabajoap = !empty($accion['ctrabajoap']);
                                $mostrarctrabajopendienteap = $accion['ctrabajoap'] === 'PENDIENTE';
                                $mostrarboletapagoap = !empty($accion['boletapagoap']);
                                $mostrarboletapagopendienteap = $accion['boletapagoap'] === 'PENDIENTE';
                                $mostraractdatosap = !empty($accion['actdatosap']);
                                $mostraractdatospendienteap = $accion['actdatosap'] === 'PENDIENTE';
                                $mostrarresolinvhijosap = !empty($accion['resolinvhijosap']);
                                $mostrarresolinvhijospendienteap = $accion['resolinvhijosap'] === 'PENDIENTE';
                                $mostrarcunionlibreap = !empty($accion['cunionlibreap']);
                                $mostrarcunionlibrependienteap = $accion['cunionlibreap'] === 'PENDIENTE';
                                $mostrarcnacimientounionlibreap = !empty($accion['cnacimientounionlibreap']);
                                $mostrarcnacimientounionlibrependienteap = $accion['cnacimientounionlibreap'] === 'PENDIENTE';
                                $mostrarciunionlibreap = !empty($accion['ciunionlibreap']);
                                $mostrarciunionlibrependienteap = $accion['ciunionlibreap'] === 'PENDIENTE';
                                $mostrarcdivorcioap = !empty($accion['cdivorcioap']);
                                $mostrarcdivorciopendienteap = $accion['cdivorcioap'] === 'PENDIENTE';
                                $mostrarcdefuncionap = !empty($accion['cdefuncionap']);
                                $mostrarcdefuncionpendienteap = $accion['cdefuncionap'] === 'PENDIENTE';
                                $mostrarpolizasgenap = !empty($accion['polizasgenap']);
                                $mostrarpolizasgenpendienteap = $accion['polizasgenap'] === 'PENDIENTE';
                                $mostrardeclasaludap = !empty($accion['declasaludap']);
                                $mostrardeclasaludpendienteap = $accion['declasaludap'] === 'PENDIENTE';
                                $mostrarpolizaseguroap = !empty($accion['polizaseguroap']);
                                $mostrarpolizaseguropendienteap = $accion['polizaseguroap'] === 'PENDIENTE';
                                $mostraranteriordictamenap = !empty($accion['anteriordictamenap']);
                                $mostraranteriordictamenpendienteap = $accion['anteriordictamenap'] === 'PENDIENTE';
                                $mostrarpoderciapoderadoap = !empty($accion['poderciapoderadoap']);
                                $mostrarpoderciapoderadopendienteap = $accion['poderciapoderadoap'] === 'PENDIENTE';
                            @endphp

                            @if ($mostrarpoderap || $mostrarpoderpendienteap)
                                <tr>
                                    <td>PODER: {{ $accion['numeropoderap'] }}</td>
                                    <td>
                                        @if ($mostrarpoderpendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarpoderap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['poderap']) }}" class="btn btn-verdocumentacion" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostraravcciap || $mostraravccipendienteap)
                                <tr>
                                    <td>AVC / CARNET DE ASEGURADO</td>
                                    <td>
                                        @if ($mostraravccipendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostraravcciap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['avcciap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcnacaseguradoap || $mostrarcnacaseguradopendienteap)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarcnacaseguradopendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacaseguradoap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacaseguradoap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarciaseguradoap || $mostrarciaseguradopendienteap)
                                <tr>
                                    <td>CARNET IDENTIDAD DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarciaseguradopendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciaseguradoap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciaseguradoap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcimatrimonioap || $mostrarcimatrimoniopendienteap)
                                <tr>
                                    <td>CERTIFICADO DE MATRIMONIO</td>
                                    <td>
                                        @if ($mostrarcimatrimoniopendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcimatrimonioap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cmatrimonioap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>   
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcnacconyugeap || $mostrarcnacconyugependienteap)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE CONYUGE</td>
                                    <td>
                                        @if ($mostrarcnacconyugependienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacconyugeap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacconyugeap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarciconyugeap || $mostrarciconyugependienteap)
                                <tr>
                                    <td>CARNET IDENTIDAD DE CONYUGE</td>
                                    <td>
                                        @if ($mostrarciconyugependienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciconyugeap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciconyugeap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcnacjihosap || $mostrarcnacjihospendienteap)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE HIJOS < 25</td>
                                    <td>
                                        @if ($mostrarcnacjihospendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacjihosap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacjihosap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcihijosap || $mostrarcihijospendienteap)
                                <tr>
                                    <td>CARNET IDENTIDAD DE HIJOS < 25</td>
                                    <td>
                                        @if ($mostrarcihijospendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcihijosap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cihijosap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrardenfaccidenteap || $mostrardenfaccidentependienteap)
                                <tr>
                                    <td>DENUNCIA ENFERMEDAD ACCIDENTE</td>
                                    <td>
                                        @if ($mostrardenfaccidentependienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrardenfaccidenteap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['denfaccidenteap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcrodomicilioap || $mostrarcrodomiciliopendienteap)
                                <tr>
                                    <td>CROQUIS DE DOMICILIO</td>
                                    <td>
                                        @if ($mostrarcrodomiciliopendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcrodomicilioap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['crodomicilioap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcontratoap || $mostrarcontratopendienteap)
                                <tr>
                                    <td>CONTRATO</td>
                                    <td>
                                        @if ($mostrarcontratopendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcontratoap)
                                            @if ($userRole === 'MAESTRO' || $userRole === 'ADMINISTRADOR')
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['contratoap']) }}" class="btn btn-verdocumentacion" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a> 
                                            @else
                                                <button class="btn btn-verdocumentacion" disabled>
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostraregestoraap || $mostraregestorapendienteap)
                                <tr>
                                    <td>EXTRACTO DE GESTORA</td>
                                    <td>
                                        @if ($mostraregestorapendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostraregestoraap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['egestoraap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrardictamencalentencap || $mostrardictamencalentencpendienteap)
                                <tr>
                                    <td>DICTAMEN CALIFICACION ENTIDAD ENCARGADA</td>
                                    <td>
                                        @if ($mostrardictamencalentencpendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrardictamencalentencap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['dictamencalentencap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarinfomedicasaludap || $mostrarinfomedicasaludpendienteap)
                                <tr>
                                    <td>INFORMACION MEDICA</td>
                                    <td>
                                        @if ($mostrarinfomedicasaludpendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarinfomedicasaludap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['infomedicasaludap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarctrabajoap || $mostrarctrabajopendienteap)
                                <tr>
                                    <td>CERTIFICADO DE TRABAJO</td>
                                    <td>
                                        @if ($mostrarctrabajopendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarctrabajoap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ctrabajoap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarboletapagoap || $mostrarboletapagopendienteap)
                                <tr>
                                    <td>BOLETA DE PAGO</td>
                                    <td>
                                        @if ($mostrarboletapagopendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarboletapagoap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['boletapagoap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostraractdatosap || $mostraractdatospendienteap)
                                <tr>
                                    <td>ACTUALIZACION DE DATOS</td>
                                    <td>
                                        @if ($mostraractdatospendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostraractdatosap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['actdatosap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarresolinvhijosap || $mostrarresolinvhijospendienteap)
                                <tr>
                                    <td>RESOLUCION INVALIDEZ DE HIJOS < 25</td>
                                    <td>
                                        @if ($mostrarresolinvhijospendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarresolinvhijosap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['resolinvhijosap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcunionlibreap || $mostrarcunionlibrependienteap)
                                <tr>
                                    <td>CERTIFICADO DE UNION LIBRE</td>
                                    <td>
                                        @if ($mostrarcunionlibrependienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcunionlibreap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cunionlibreap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcnacimientounionlibreap || $mostrarcnacimientounionlibrependienteap)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE UNION LIBRE</td>
                                    <td>
                                        @if ($mostrarcnacimientounionlibrependienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacimientounionlibreap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacimientounionlibreap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarciunionlibreap || $mostrarciunionlibrependienteap)
                                <tr>
                                    <td>CARNET IDENTIDAD DE UNION LIBRE</td>
                                    <td>
                                        @if ($mostrarciunionlibrependienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciunionlibreap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciunionlibreap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcdivorcioap || $mostrarcdivorciopendienteap)
                                <tr>
                                    <td>CERTIFICADO DE DIVORCIO</td>
                                    <td>
                                        @if ($mostrarcdivorciopendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcdivorcioap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cdivorcioap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarcdefuncionap || $mostrarcdefuncionpendienteap)
                                <tr>
                                    <td>CERTIFICADO DE DIFUNCION</td>
                                    <td>
                                        @if ($mostrarcdefuncionpendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcdefuncionap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cdefuncionap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarpolizasgenap || $mostrarpolizasgenpendienteap)
                                <tr>
                                    <td>POLIZAS GENERALES</td>
                                    <td>
                                        @if ($mostrarpolizasgenpendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarpolizasgenap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['polizasgenap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrardeclasaludap || $mostrardeclasaludpendienteap)
                                <tr>
                                    <td>DECLARACION SALUD</td>
                                    <td>
                                        @if ($mostrardeclasaludpendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrardeclasaludap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['declasaludap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarpolizaseguroap || $mostrarpolizaseguropendienteap)
                                <tr>
                                    <td>POLIZA SEGURO DESGRAVAMEN</td>
                                    <td>
                                        @if ($mostrarpolizaseguropendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarpolizaseguroap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['polizaseguroap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostraranteriordictamenap || $mostraranteriordictamenpendienteap)
                                <tr>
                                    <td>ANTERIOR DICTAMEN O RESOLUCION</td>
                                    <td>
                                        @if ($mostraranteriordictamenpendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostraranteriordictamenap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['anteriordictamenap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarpoderciapoderadoap || $mostrarpoderciapoderadopendienteap)
                                <tr>
                                    <td>PODER Y CARNET IDENTIDAD APODERADO</td>
                                    <td>
                                        @if ($mostrarpoderciapoderadopendienteap)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarpoderciapoderadoap)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['poderciapoderadoap']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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

                            
                            @if ($mostrarcnacaseguradoau || $mostrarcnacaseguradopendienteau)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarcnacaseguradopendienteau)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacaseguradoau)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacaseguradoau']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarciaseguradoau || $mostrarciaseguradopendienteau)
                                <tr>
                                    <td>CARNET IDENTIDAD DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarciaseguradopendienteau)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciaseguradoau)
                                            <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciaseguradoau']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
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
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->polizageneral}") }}" target="_blank" class="verdoc">VER DOC.</a>
                                            @endif
                                        </td>
                                        <td> 
                                            @if ($requisito->declasalud === 'PENDIENTE')
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($requisito->declasalud === 'NO APLICA')
                                                <div class="noaplica">NO APLICA</div>
                                            @else
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->declasalud}") }}" target="_blank" class="verdoc">VER DOC.</a>
                                            @endif
                                        </td>
                                        
                                        <td>{{ $requisito->nropolizadesgravamen ?? '' }}</td>
                                        <td>
                                            @if ($requisito->polizasegurodesgravamen === 'PENDIENTE')
                                                <div class="pendiente">PENDIENTE</div>
                                            @else
                                                <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->polizasegurodesgravamen}") }}" target="_blank" class="verdoc">VER DOC.</a> 
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </tbody>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

@endforeach

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    /* Estilo para el contenedor del botón y menú desplegable */
.dropdown-container {
    position: relative;
    display: inline-block;
}

/* Estilo para el botón que abre el menú */
.btn-dropdown {
margin-top: -10px;
    color: #94c93b;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 16px;
    display: flex;
}


/* Estilo para el menú desplegable */
.dropdown-menu {
    display: none;
    position: absolute;
    border-radius: 5px;
    z-index: 1;
    top: 100%;
    left: 100%;
    margin-left: 5px; /* Espacio entre el botón y el menú */
    padding: 5px;
    opacity: 0;
    background: #f7ffea;
    display: flex;
    flex-direction: row; /* Alineación horizontal de los botones */
    white-space: nowrap; /* Evita que los botones se envuelvan */
    min-width: 50px; /* Ancho mínimo del menú */
    width: auto; /* Ancho automático según el contenido */
    max-width: 300px; /* Opcional: Ajusta el máximo ancho según sea necesario */
    transition: opacity 0.3s ease; /* Transición suave */
    margin-top: -38px;
}

/* Mostrar el menú desplegable al pasar el cursor sobre el contenedor */
.dropdown-container:hover .dropdown-menu {
    display: flex;
    opacity: 1;
    visibility: visible;
}

/* Estilo para los enlaces dentro del menú desplegable */
.dropdown-menu a {
    padding: 10px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px; /* Espaciado entre los botones */
    margin-left: 10px;
}


</style>
<style>
    .btn-verhistoriamedica {
        background-color:  #ffffff;
        color: #c9c971;
        border-color: #c9c971;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-verhistoriamedica:hover {
        background-color: #c9c971;
        color: #ffffff;
        }
    .btn-completo {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-completo:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-incompleto {
        background-color:  #ffffff;
        color: red;
        border-color: red;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-incompleto:hover {
        background-color: red;
        color: #ffffff;
        }
    .btn-noregistrado {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-noregistrado:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .text-completo {
        color: #94c93b;
        font-size: 15px;
        font-weight: 900;
        }
    .text-incompleto {
        color: red;
        font-size: 15px;
        font-weight: 900;
        }
    .text-noregistrado {
        color: red;
        font-size: 15px;
        font-weight: 900;
        }
    .text-notiene {
        color: #faa625;
        font-size: 15px;
        font-weight: 900;
        }
    .btn-buscar { 
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        }
    .btn-buscar:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-mostrartodo { 
        background-color:  #ffffff;
        color: #2f97e7;
        border-color: #2f97e7;
        border-radius: 5px;
        }
    .btn-mostrartodo:hover {
        background-color: #2f97e7;
        color: #ffffff;
        }
    .btn-container {
        display: flex;
        align-items: center;
        gap: 2px;
    }
    .btn-container .btn,
    .btn-container .btn-enviarobservacion {
        justify-content: center;
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
        font-size: 17px;
        color: #faa625;
        background-color: #fef4e7;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
        font-size: 17px;
        color: #94c93b;
        background-color: #ffffff;
    }
    .circle {
        display: inline-block;
        width: 20px;
        height: 20px;
        line-height: 20px;
        border-radius: 50%;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-left: 8px;
    }
    .nav-link.active .circle {
        background-color: #94c93b;
        color: #fff;
    }
    .nav-link .circle {
        background-color: #faa625;
        color: #fff;
    }
    .btn-upload {
        background-color: #28a745;
        color: white;
    }
    .btn-disabled {
        background-color: #d3d3d3;
        color: #6c757d;
        cursor: not-allowed;
    }
    .btn-upload:hover {
        background-color: #218838;
    }
    .btn-disabled:hover {
        background-color: #d3d3d3;
    }
    .modal-content {
        border-radius: 10px;
        }
    .modal-header {
        border-bottom: none;
        }
    .modal-footer {
        border-top: none;
        }
    h4 {
        font-weight: bold;
        color: #94c93b;
        }
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .btn-no, .btn-si {
        background-color: #ffffff;
        border-radius: 5px;
        padding: 5px 5px;
        min-width: 50px;
        border: 1px solid;
        }
    .btn-no {
        color: #fd1d1d;
        border-color: #fd1d1d;
        width: 100px;
        }
    .btn-no:hover {
        background-color: #fd1d1d;
        color: #ffffff;
        }
    .btn-si {
        color: #8bc02f;
        border-color: #8bc02f;
        width: 100px;
        }
    .btn-si:hover {
        background-color: #8bc02f;
        color: #ffffff;
        }
    .checkverde {
        color:#94c93b; 
        }
    
    .text-aprobado {
        color: #94c93b;
        font-size: 15px;
        font-weight: 900;
        }
    .text-enrevision {
        color: #41b5eb;
        font-size: 15px;
        font-weight: 900;
        }
    .text-solicitorevision {
        color: #faa625;
        font-size: 15px;
        font-weight: 900;
        }
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-verdocumentacion:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .pendiente {color:#fb2525; 
        font-weight: 900;
        font-size: 15px;
        }
    .btn-veracciones {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-veracciones:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-veracciones2 {
        background-color:  #ffffff;
        color: red;
        border-color: red;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-veracciones2:hover {
        background-color: red;
        color: #ffffff;
        }
    .btn-enviarobservacion {
        background-color:  #ffffff;
        color: #41b5eb;
        border-color: #41b5eb;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-enviarobservacion:hover {
        background-color: #41b5eb;
        color: #ffffff;
        }
    .btn-observaciones {
        background-color:  #ffffff;
        color: #876f4a;
        border-color: #876f4a;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-observaciones:hover {
        background-color: #876f4a;
        color: #ffffff;
        }
    .btn-solicitarrevision {
        background-color:  #ffffff;
        color: #41b5eb;
        border-color: #41b5eb;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-solicitarrevision:hover {
        background-color: #41b5eb;
        color: #ffffff;
        }
    .btn-aprobarinforme {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-aprobarinforme:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-subirinforme {
        background-color:  #ffffff;
        color: #cf44b8;
        border-color: #cf44b8;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-subirinforme:hover {
        background-color: #cf44b8;
        color: #ffffff;
        }
    .btn-subirinformeinicio {
        background-color:  #ffffff;
        color: #cf44b8;
        border-color: #cf44b8;
        border-radius: 5px;
        padding: 2px 40px;
        }
    .btn-subirinformeinicio:hover {
        background-color: #cf44b8;
        color: #ffffff;
        }
    .btn-asignarproveedor {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 40px;
        }
    .btn-asignarproveedor:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-aprobar {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 40px;
        }
    .btn-aprobar:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-disabled {
        background-color:  #ffffff;
        color: #737373;
        border-color: #737373;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-disabled:hover {
        background-color: #737373;
        color: #ffffff;
        }
    .btn-cerrar {
        background-color: #ffffff;
        color: #fb2525;
        border-color: #fb2525;
        border-radius: 5px;
        padding: 2px 5px;
        }
    .btn-cerrar:hover {
        background-color: #fb2525;
        color: #ffffff;
        }
</style>
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
