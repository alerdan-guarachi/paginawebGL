@extends('adminlte::page')

@section('content_header')
@can('admin.inventario.notificarcxplistas')
<a class="btn float-right btn-outline-primary btn-sm" onclick="enviarTelegram()" title="NOTIFICAR CUENTAS POR PAGAR LISTAS">NOTIFICAR <i class="fab fa-telegram-plane"></i></a>
@endcan
<script>
    function enviarTelegram() {
        fetch("{{ route('informar.subida.cxplistas') }}", {
            method: 'POST',
            headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
            },
        body: JSON.stringify({})
            })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Notificación enviada correctamente.');
            })
            .catch(error => {
            console.error(error);
            alert('Ocurrió un error al enviar la notificación.');
            });
        }
</script>
<h1>LISTA DE ÓRDENES</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .truncar2 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100px;
    }
    .table td {
        padding: 5px 10px;
    }
    .btn-verpreorden {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-verpreorden:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-priorizar {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-priorizar:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-cerrar {
        background-color:  #ffffff;
        color: #d91616;
        border-color: #d91616;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-cerrar:hover {
        background-color: #d91616;
        color: #ffffff;
        }
</style>
<style>
    .table td {
        padding: 5px 10px;
    }
    .btn-botongris {
        background-color: #ffffff;
        color: #676767;
        border-color: #676767;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-botongris:hover {
        background-color: #676767;
        color: #ffffff;
        }
    .label-estrella {
        cursor: pointer;
        font-size: 20px;
        color: #ccc;
        transition: color 0.2s;
        margin-bottom: -5px;
        margin-top: -5px;
    }

    .check-prioridad:checked + .label-estrella {
        color: #faa625;
    }

    .label-estrella::before {
        content: '☆'; /* estrella vacía */
    }

    .check-prioridad:checked + .label-estrella::before {
        content: '★'; /* estrella llena */
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
        }, 3000);
    </script>
@endif
<div class="card">
    <div class="card-header"> 
        <ul class="nav nav-tabs card-header-tabs" id="mainTabs">
            <li class="nav-item">
                <a class="nav-link active tab-rounded" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    <i class="fas fa-shopping-cart"></i> ÓRDENES DE COMPRA
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link tab-rounded" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    <i class="fas fa-concierge-bell"></i> ÓRDENES DE SERVICIO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link tab-rounded" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    <i class="fas fa-cogs"></i> ÓRDENES DE PERSONAL
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link tab-rounded" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    <i class="fas fa-lungs-virus"></i> ATENCIÓN MÉDICA
                </a>
            </li> --}}
        </ul>
    </div>
    
    <style>
        .nav-link.active.tab-rounded {
            background-color: #94c93b !important;
            color: white !important;
        }
        .nav-link.tab-rounded {
            border-radius: 100px !important;
        }
    </style>    

    <div class="card-body">
        <div class="tab-content">
            {{-- ORDENES DE COMPRA --}}
            <div class="tab-pane fade show active" id="tab-content-1">
                <ul class="nav nav-tabs">
                    {{-- <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#sub-tab-1">PRE - ÓRDENES DE COMPRA</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#sub-tab-2">ÓRDENES DE COMPRA PENDIENTES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#sub-tab-3">ÓRDENES DE COMPRA PROCESADAS</a>
                    </li>
                </ul><br>
                <div class="tab-content">
                    {{-- PRE - ÓRDENES DE COMPRA --}}
                    {{-- <div class="tab-pane fade show active" id="sub-tab-1">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><span style="color: black; font-size: 20px;">★</span></th>
                                        <th>PreOrden</th>
                                        <th>Proveedor</th>
                                        <th>Suc_Gasto</th>
                                        <th>Usuario_Reg.</th>
                                        <th>Fecha_Reg.</th>
                                        <th>Fecha_Pago</th>
                                        <th>Estado</th>
                                        <th>Ver</th>
                                        <th>Unif.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenes as $preordenid => $ordenesGrupo)
                                        @if ($ordenesGrupo[0]->estado == 'PENDIENTE') 
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                                        <input type="checkbox"
                                                            class="check-prioridad estrella"
                                                            data-preordenid="{{ $preordenid }}"
                                                            style="display: none;"
                                                            id="estrella-{{ $preordenid }}"
                                                            {{ $ordenesGrupo[0]->prioridad === 'PRIORITARIO' ? 'checked' : '' }}>
                                                        <label for="estrella-{{ $preordenid }}" class="label-estrella"></label>
                                                    </div>
                                                </td>
                                                <td>{{ $preordenid }}</td>
                                                <td>{{ $ordenesGrupo[0]->proveedornombre }}</td>
                                                <td>{{ $ordenesGrupo[0]->sucursalgasto }}</td>
                                                <td>{{ $ordenesGrupo[0]->usuarioregistronombre }}</td>
                                                <td>{{ \Carbon\Carbon::parse($ordenesGrupo[0]->created_at)->format('Y-m-d') }}</td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="date" name="fechapagar"
                                                               class="form-control form-control-sm"
                                                               style="max-width: 130px;"
                                                               value="{{ $ordenesGrupo[0]->fechapagar }}"
                                                               data-preordenid="{{ $preordenid }}"
                                                               @cannot('admin.inventario.actualizarfechapreorden') disabled @endcannot>
                                                        
                                                        @can('admin.inventario.actualizarfechapreorden')
                                                        <button class="btn btn-success btn-sm actualizar-fecha-btn"
                                                                data-preordenid="{{ $preordenid }}" title="Actualizar fecha" style="display: none;">
                                                            <i class="fas fa-save"></i>
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $ordenesGrupo[0]->estado == 'PENDIENTE' ? 'bg-danger' : 'bg-success' }}">
                                                        {{ strtoupper($ordenesGrupo[0]->estado) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @can('admin.inventario.aprobarpreordenes')
                                                        <a type="button" class="btn btn-botongris" data-toggle="modal" data-target="#modal{{ $preordenid }}" title="VER PRE-ORDEN DE COMPRA">
                                                            <i class="fas fa-clipboard-list"></i>
                                                        </a>
                                                    @else
                                                        <button type="button" class="btn btn-botongris" title="Sin permiso" disabled>
                                                            <i class="fas fa-clipboard-list"></i>
                                                        </button>
                                                    @endcan
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="check-unificar" data-preordenid="{{ $preordenid }}" data-proveedornombre="{{ $ordenesGrupo[0]->proveedornombre }}" data-fechapagar="{{ $ordenesGrupo[0]->fechapagar }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            

                            <div class="d-flex justify-content-between mt-2">
                                <button id="actualizarPrioridadBtn" class="btn btn-priorizar">
                                    ACTUALIZAR PRIORIDAD
                                </button>
                                @can('admin.inventario.aprobarpreordenes')
                                <button id="unificarPreordenBtn" class="btn btn-outline-secondary btn-sm">
                                    UNIFICAR ID
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div> --}}
                    {{-- ÓRDENES DE COMPRA APROBADAS --}}
                    <div class="tab-pane fade show active" id="sub-tab-2">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Unif.</th>
                                        <th>ID</th>
                                        <th>Proveedor</th>
                                        <th>Suc_Gasto</th>
                                        <th>Detalles</th>
                                        <th>Subtotal</th>
                                        <th>Desc.</th>
                                        <th>Total</th>
                                        <th>Fecha_Com.</th>
                                        <th>Fecha_Pago</th>
                                        <th>Transac.</th>
                                        <th>Forma_Pago</th>
                                        <th>Usuario_Reg.</th>
                                        <th>Orden</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenesaprobadas as $orden)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="check-unificar" data-preordenid="{{$orden->id}}" data-proveedornombre="{{ $orden->proveedornombre }}" data-fechapagar="{{$orden->fechapagar}}">
                                            </td>
                                            <td>
                                                {{ $orden->id }}
                                                @if(!empty($orden->ordenunificado))
                                                    (UNIF.: {{ $orden->ordenunificado }})
                                                @endif
                                            </td>
                                            <td title="{{ $orden->proveedornombre }}" class="truncar">{{$orden->proveedornombre}}</td>
                                            <td>{{$orden->sucursalgasto}}</td>
                                            <td title="{{ $orden->detalles->pluck('detalle')->implode(', ') }}" class="truncar">{{ $orden->detalles->pluck('detalle')->implode(', ') }}</td>
                                            <td>{{$orden->subtotal}}</td>
                                            <td>{{$orden->descuento}}</td>
                                            <td>{{$orden->montototal}}</td>
                                            <td>{{$orden->fechacomprar}}</td>
                                            <td>{{$orden->fechapagar}}</td>
                                            <td title="{{ $orden->tipotransaccion }}" class="truncar2">{{$orden->tipotransaccion}}</td>
                                            <td title="{{ $orden->formapago }}" class="truncar">{{$orden->formapago}}</td>
                                            <td title="{{ $orden->usuarioregistronombre }}" class="truncar">{{$orden->usuarioregistronombre}}</td>
                                            <td>
                                                @if($orden->documentoorden)
                                                    <a href="{{ asset('ordenesaprobadas/' . $orden->usuarioregistroid . '/' . $orden->documentoorden) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER ORDEN DE COMPRA">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span>No disponible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($orden->estado === 'APROBADO')
                                                    <span class="badge bg-success">APROBADO</span>
                                                @elseif($orden->estado === 'RECHAZADO')
                                                    <span class="badge bg-danger">RECHAZADO</span>
                                                @else
                                                    <form action="{{ route('ordenes.aprobar', $orden->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i></button>
                                                    </form>
                                            
                                                    <form action="{{ route('ordenes.rechazar', $orden->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody> 
                            </table>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            @can('admin.inventario.aprobarpreordenes')
                            <button id="unificarPreordenBtn" class="btn btn-outline-secondary btn-sm">
                                UNIFICAR ID
                            </button>
                            @endcan
                        </div>
                    </div>
                    {{-- ÓRDENES DE COMPRA PROCESADAS --}}
                    <div class="tab-pane fade" id="sub-tab-3">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Proveedor</th>
                                        <th>Suc_Gasto</th>
                                        <th>Detalles</th>
                                        <th>Subtotal</th>
                                        <th>Desc.</th>
                                        <th>Total</th>
                                        <th>Fecha_Com.</th>
                                        <th>Fecha_Pago</th>
                                        <th>Transac.</th>
                                        <th>Forma_Pago</th>
                                        <th>Usuario_Reg.</th>
                                        <th>Orden</th>
                                        <th>Fecha_Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenesaprobadasprocesadas as $orden)
                                        <tr>
                                            <td>{{$orden->id}}</td>
                                            <td title="{{ $orden->proveedornombre }}" class="truncar">{{$orden->proveedornombre}}</td>
                                            <td>{{$orden->sucursalgasto}}</td>
                                            <td title="{{ $orden->detalles->pluck('detalle')->implode(', ') }}" class="truncar">{{ $orden->detalles->pluck('detalle')->implode(', ') }}</td>
                                            <td>{{$orden->subtotal}}</td>
                                            <td>{{$orden->descuento}}</td>
                                            <td>{{$orden->montototal}}</td>
                                            <td>{{$orden->fechacomprar}}</td>
                                            <td>{{$orden->fechapagar}}</td>
                                            <td title="{{ $orden->tipotransaccion }}" class="truncar2">{{$orden->tipotransaccion}}</td>
                                            <td title="{{ $orden->formapago }}" class="truncar">{{$orden->formapago}}</td>
                                            <td title="{{ $orden->usuarioregistronombre }}" class="truncar">{{$orden->usuarioregistronombre}}</td>
                                            <td>
                                                @if($orden->documentoorden)
                                                    <a href="{{ asset('ordenesaprobadas/' . $orden->usuarioregistroid . '/' . $orden->documentoorden) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER ORDEN DE COMPRA">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span>No disponible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($orden->estado === 'APROBADO')
                                                    <span class="badge bg-success">
                                                        {{ optional($orden->detalles->first())->created_at?->format('Y-m-d') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody> 
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ORDENES DE SERVICIO --}}
            <div class="tab-pane fade" id="tab-content-2">
                <ul class="nav nav-tabs">
                    {{-- <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#sub-tab-4">PRE - ÓRDENES DE SERVICIO</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#sub-tab-5">ÓRDENES DE SERVICIO PENDIENTES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#sub-tab-6">ÓRDENES DE SERVICIO PROCESADAS</a>
                    </li>
                </ul><br>
                <div class="tab-content">
                    {{-- PRE - ÓRDENES DE SERVICIO --}}
                    {{-- <div class="tab-pane fade show active" id="sub-tab-4">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><span style="color: black; font-size: 20px;">★</span></th>
                                        <th>PreOrden</th>
                                        <th>Proveedor</th>
                                        <th>Suc_Gasto</th>
                                        <th>Usuario_Reg.</th>
                                        <th>Fecha_Reg.</th>
                                        <th>Fecha_Pago</th>
                                        <th>Estado</th>
                                        <th>Ver</th>
                                        <th>Unif.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenesservicio as $preordenid => $ordenesGrupo)
                                        @if ($ordenesGrupo[0]->estado == 'PENDIENTE') 
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                                        <input type="checkbox"
                                                            class="check-prioridad estrella"
                                                            data-preordenid="{{ $preordenid }}"
                                                            style="display: none;"
                                                            id="estrella-{{ $preordenid }}"
                                                            {{ $ordenesGrupo[0]->prioridad === 'PRIORITARIO' ? 'checked' : '' }}>
                                                        <label for="estrella-{{ $preordenid }}" class="label-estrella"></label>
                                                    </div>
                                                </td>
                                                <td>{{ $preordenid }}</td>
                                                <td>{{ $ordenesGrupo[0]->proveedornombre }}</td>
                                                <td>{{ $ordenesGrupo[0]->sucursalgasto }}</td>
                                                <td>{{ $ordenesGrupo[0]->usuarioregistronombre }}</td>
                                                <td>{{ \Carbon\Carbon::parse($ordenesGrupo[0]->created_at)->format('Y-m-d') }}</td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="date" name="fechapagar"
                                                               class="form-control form-control-sm"
                                                               style="max-width: 130px;"
                                                               value="{{ $ordenesGrupo[0]->fechapagar }}"
                                                               data-preordenid="{{ $preordenid }}"
                                                               @cannot('admin.inventario.actualizarfechapreorden') disabled @endcannot>
                                                        
                                                        @can('admin.inventario.actualizarfechapreorden')
                                                        <button class="btn btn-success btn-sm actualizar-fecha-btn"
                                                                data-preordenid="{{ $preordenid }}" title="Actualizar fecha" style="display: none;">
                                                            <i class="fas fa-save"></i>
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $ordenesGrupo[0]->estado == 'PENDIENTE' ? 'bg-danger' : 'bg-success' }}">
                                                        {{ strtoupper($ordenesGrupo[0]->estado) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-botongris" data-toggle="modal" data-target="#modal2{{ $preordenid }}" title="VER PRE-ORDEN DE SERVICIO">
                                                        <i class="fas fa-clipboard-list"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="check-unificar" data-preordenid="{{ $preordenid }}" data-proveedornombre="{{ $ordenesGrupo[0]->proveedornombre }}" data-fechapagar="{{ $ordenesGrupo[0]->fechapagar }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between mt-2">
                                <button id="actualizarPrioridadBtn2" class="btn btn-priorizar">
                                    ACTUALIZAR PRIORIDAD
                                </button>
                                @can('admin.inventario.aprobarpreordenes')
                                <button id="unificarPreordenBtn2" class="btn btn-outline-secondary btn-sm">
                                    UNIFICAR ID
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div> --}}
                    {{-- ÓRDENES DE SERVICIO APROBADAS --}}
                    <div class="tab-pane fade show active" id="sub-tab-5">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Unif.</th>
                                        <th>ID</th>
                                        <th>Proveedor</th>
                                        <th>Suc_Gasto</th>
                                        <th>Detalles</th>
                                        <th>Subtotal</th>
                                        <th>Desc.</th>
                                        <th>Total</th>
                                        <th hidden>Fecha_Com.</th>
                                        <th>Fecha_Pago</th>
                                        <th>Transac.</th>
                                        <th>Forma_Pago</th>
                                        <th>Usuario_Reg.</th>
                                        <th>Orden</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenesaprobadasservicio as $orden)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="check-unificar" data-preordenid="{{$orden->id}}" data-proveedornombre="{{ $orden->proveedornombre }}" data-fechapagar="{{$orden->fechapagar}}">
                                            </td>
                                            <td>
                                                {{ $orden->id }}
                                                @if(!empty($orden->ordenunificado))
                                                    (UNIF.: {{ $orden->ordenunificado }})
                                                @endif
                                            </td>
                                            <td title="{{ $orden->proveedornombre }}" class="truncar">{{$orden->proveedornombre}}</td>
                                            <td>{{$orden->sucursalgasto}}</td>
                                            <td title="{{ $orden->detalles->pluck('detalle')->implode(', ') }}" class="truncar">{{ $orden->detalles->pluck('detalle')->implode(', ') }}</td>
                                            <td>{{$orden->subtotal}}</td>
                                            <td>{{$orden->descuento}}</td>
                                            <td>{{$orden->montototal}}</td>
                                            <td hidden>{{$orden->fechacomprar}}</td>
                                            <td>{{$orden->fechapagar}}</td>
                                            <td title="{{ $orden->tipotransaccion }}" class="truncar2">{{$orden->tipotransaccion}}</td>
                                            <td title="{{ $orden->formapago }}" class="truncar">{{$orden->formapago}}</td>
                                            <td title="{{ $orden->usuarioregistronombre }}" class="truncar">{{$orden->usuarioregistronombre}}</td>
                                            <td>
                                                @if($orden->documentoorden)
                                                    <a href="{{ asset('ordenesaprobadas/' . $orden->usuarioregistroid . '/' . $orden->documentoorden) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER ORDEN DE COMPRA">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span>No disponible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($orden->estado === 'APROBADO')
                                                    <span class="badge bg-success">APROBADO</span>
                                                @elseif($orden->estado === 'RECHAZADO')
                                                    <span class="badge bg-danger">RECHAZADO</span>
                                                @else
                                                    <form action="{{ route('ordenes.aprobar', $orden->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i></button>
                                                    </form>
                                            
                                                    <form action="{{ route('ordenes.rechazar', $orden->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody> 
                            </table>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            @can('admin.inventario.aprobarpreordenes')
                            <button id="unificarPreordenBtn2" class="btn btn-outline-secondary btn-sm">
                                UNIFICAR ID
                            </button>
                            @endcan
                        </div>
                    </div>
                    {{-- ÓRDENES DE SERVICIO PROCESADAS --}}
                    <div class="tab-pane fade" id="sub-tab-6">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Proveedor</th>
                                        <th>Suc_Gasto</th>
                                        <th>Detalles</th>
                                        <th>Subtotal</th>
                                        <th>Desc.</th>
                                        <th>Total</th>
                                        <th hidden>Fecha_Com.</th>
                                        <th>Fecha_Pago</th>
                                        <th>Transac.</th>
                                        <th>Forma_Pago</th>
                                        <th>Usuario_Reg.</th>
                                        <th>Orden</th>
                                        <th>Fecha_Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenesaprobadasprocesadasservicio as $orden)
                                        <tr>
                                            <td>{{$orden->id}}</td>
                                            <td title="{{ $orden->proveedornombre }}" class="truncar">{{$orden->proveedornombre}}</td>
                                            <td>{{$orden->sucursalgasto}}</td>
                                            <td title="{{ $orden->detalles->pluck('detalle')->implode(', ') }}" class="truncar">{{ $orden->detalles->pluck('detalle')->implode(', ') }}</td>
                                            <td>{{$orden->subtotal}}</td>
                                            <td>{{$orden->descuento}}</td>
                                            <td>{{$orden->montototal}}</td>
                                            <td hidden>{{$orden->fechacomprar}}</td>
                                            <td>{{$orden->fechapagar}}</td>
                                            <td title="{{ $orden->tipotransaccion }}" class="truncar2">{{$orden->tipotransaccion}}</td>
                                            <td title="{{ $orden->formapago }}" class="truncar">{{$orden->formapago}}</td>
                                            <td title="{{ $orden->usuarioregistronombre }}" class="truncar">{{$orden->usuarioregistronombre}}</td>
                                            <td>
                                                @if($orden->documentoorden)
                                                    <a href="{{ asset('ordenesaprobadas/' . $orden->usuarioregistroid . '/' . $orden->documentoorden) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER ORDEN DE COMPRA">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span>No disponible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($orden->estado === 'APROBADO')
                                                    <span class="badge bg-success">
                                                        {{ optional($orden->detalles->first())->created_at?->format('Y-m-d') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody> 
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        
            {{-- ORDENES DE PERSONAL --}}
            <div class="tab-pane fade" id="tab-content-4">
                <ul class="nav nav-tabs">
                    {{-- <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#sub-tab-10">PRE - ÓRDENES DE PERSONAL</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#sub-tab-11">ÓRDENES DE PERSONAL PENDIENTES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#sub-tab-12">ÓRDENES DE PERSONAL PROCESADAS</a>
                    </li>
                </ul><br>
                <div class="tab-content">
                    {{-- PRE - ÓRDENES DE PERSONAL --}}
                    {{-- <div class="tab-pane fade show active" id="sub-tab-10">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center"><span style="color: black; font-size: 20px;">★</span></th>
                                        <th>PreOrden</th>
                                        <th>Proveedor</th>
                                        <th>Suc_Gasto</th>
                                        <th>Usuario_Reg.</th>
                                        <th>Fecha_Reg.</th>
                                        <th>Fecha_Pago</th>
                                        <th>Estado</th>
                                        <th>Ver</th>
                                        <th>Unif.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenespersonal as $preordenid => $ordenesGrupo)
                                        @if ($ordenesGrupo[0]->estado == 'PENDIENTE') 
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                                        <input type="checkbox"
                                                            class="check-prioridad estrella"
                                                            data-preordenid="{{ $preordenid }}"
                                                            style="display: none;"
                                                            id="estrella-{{ $preordenid }}"
                                                            {{ $ordenesGrupo[0]->prioridad === 'PRIORITARIO' ? 'checked' : '' }}>
                                                        <label for="estrella-{{ $preordenid }}" class="label-estrella"></label>
                                                    </div>
                                                </td>
                                                <td>{{ $preordenid }}</td>
                                                <td>{{ $ordenesGrupo[0]->proveedornombre }}</td>
                                                <td>{{ $ordenesGrupo[0]->sucursalgasto }}</td>
                                                <td>{{ $ordenesGrupo[0]->usuarioregistronombre }}</td>
                                                <td>{{ \Carbon\Carbon::parse($ordenesGrupo[0]->created_at)->format('Y-m-d') }}</td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="date" name="fechapagar"
                                                               class="form-control form-control-sm"
                                                               style="max-width: 130px;"
                                                               value="{{ $ordenesGrupo[0]->fechapagar }}"
                                                               data-preordenid="{{ $preordenid }}"
                                                               @cannot('admin.inventario.actualizarfechapreorden') disabled @endcannot>
                                                        
                                                        @can('admin.inventario.actualizarfechapreorden')
                                                        <button class="btn btn-success btn-sm actualizar-fecha-btn"
                                                                data-preordenid="{{ $preordenid }}" title="Actualizar fecha" style="display: none;">
                                                            <i class="fas fa-save"></i>
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $ordenesGrupo[0]->estado == 'PENDIENTE' ? 'bg-danger' : 'bg-success' }}">
                                                        {{ strtoupper($ordenesGrupo[0]->estado) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @can('admin.inventario.aprobarpreordenes')
                                                        <a type="button" class="btn btn-botongris" data-toggle="modal" data-target="#modal3{{ $preordenid }}" title="VER PRE-ORDEN DE PERSONAL">
                                                            <i class="fas fa-clipboard-list"></i>
                                                        </a>
                                                    @else
                                                        <button type="button" class="btn btn-botongris" title="Sin permiso" disabled>
                                                            <i class="fas fa-clipboard-list"></i>
                                                        </button>
                                                    @endcan
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="check-unificar" data-preordenid="{{ $preordenid }}" data-proveedornombre="{{ $ordenesGrupo[0]->proveedornombre }}" data-fechapagar="{{ $ordenesGrupo[0]->fechapagar }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between mt-2">
                                <button id="actualizarPrioridadBtn3" class="btn btn-priorizar">
                                    ACTUALIZAR PRIORIDAD
                                </button>
                                @can('admin.inventario.aprobarpreordenes')
                                <button id="unificarPreordenBtn3" class="btn btn-outline-secondary btn-sm">
                                    UNIFICAR ID
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div> --}}
                    {{-- ÓRDENES DE PERSONAL APROBADAS --}}
                    <div class="tab-pane fade show active" id="sub-tab-11">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Unif.</th>
                                        <th>ID</th>
                                        <th>Proveedor</th>
                                        <th>Suc_Gasto</th>
                                        <th>Detalles</th>
                                        <th>Subtotal</th>
                                        <th>Desc.</th>
                                        <th>Total</th>
                                        <th hidden>Fecha_Com.</th>
                                        <th>Fecha_Pago</th>
                                        <th>Transac.</th>
                                        <th>Forma_Pago</th>
                                        <th>Usuario_Reg.</th>
                                        <th>Orden</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenesaprobadaspersonal as $orden)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="check-unificar" data-preordenid="{{$orden->id}}" data-proveedornombre="{{ $orden->proveedornombre }}" data-fechapagar="{{$orden->fechapagar}}">
                                            </td>
                                            <td>
                                                {{ $orden->id }}
                                                @if(!empty($orden->ordenunificado))
                                                    (UNIF.: {{ $orden->ordenunificado }})
                                                @endif
                                            </td>
                                            <td title="{{ $orden->proveedornombre }}" class="truncar">{{$orden->proveedornombre}}</td>
                                            <td>{{$orden->sucursalgasto}}</td>
                                            <td title="{{ $orden->detalles->pluck('detalle')->implode(', ') }}" class="truncar">{{ $orden->detalles->pluck('detalle')->implode(', ') }}</td>
                                            <td>{{$orden->subtotal}}</td>
                                            <td>{{$orden->descuento}}</td>
                                            <td>{{$orden->montototal}}</td>
                                            <td hidden>{{$orden->fechacomprar}}</td>
                                            <td>{{$orden->fechapagar}}</td>
                                            <td title="{{ $orden->tipotransaccion }}" class="truncar2">{{$orden->tipotransaccion}}</td>
                                            <td title="{{ $orden->formapago }}" class="truncar">{{$orden->formapago}}</td>
                                            <td title="{{ $orden->usuarioregistronombre }}" class="truncar">{{$orden->usuarioregistronombre}}</td>
                                            <td>
                                                @if($orden->documentoorden)
                                                    <a href="{{ asset('ordenesaprobadas/' . $orden->usuarioregistroid . '/' . $orden->documentoorden) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER ORDEN DE PERSONAL">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span>No disponible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($orden->estado === 'APROBADO')
                                                    <span class="badge bg-success">APROBADO</span>
                                                @elseif($orden->estado === 'RECHAZADO')
                                                    <span class="badge bg-danger">RECHAZADO</span>
                                                @else
                                                    <form action="{{ route('ordenes.aprobar', $orden->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i></button>
                                                    </form>
                                            
                                                    <form action="{{ route('ordenes.rechazar', $orden->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody> 
                            </table>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            @can('admin.inventario.aprobarpreordenes')
                            <button id="unificarPreordenBtn3" class="btn btn-outline-secondary btn-sm">
                                UNIFICAR ID
                            </button>
                            @endcan
                        </div>
                    </div>
                    {{-- ÓRDENES DE PERSONAL PROCESADAS --}}
                    <div class="tab-pane fade" id="sub-tab-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Proveedor</th>
                                        <th>Suc_Gasto</th>
                                        <th>Detalles</th>
                                        <th>Subtotal</th>
                                        <th>Desc.</th>
                                        <th>Total</th>
                                        <th hidden>Fecha_Com.</th>
                                        <th>Fecha_Pago</th>
                                        <th>Transac.</th>
                                        <th>Forma_Pago</th>
                                        <th>Usuario_Reg.</th>
                                        <th>Orden</th>
                                        <th>Fecha_Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ordenesaprobadasprocesadaspersonal as $orden)
                                        <tr>
                                            <td>{{$orden->id}}</td>
                                            <td title="{{ $orden->proveedornombre }}" class="truncar">{{$orden->proveedornombre}}</td>
                                            <td>{{$orden->sucursalgasto}}</td>
                                            <td title="{{ $orden->detalles->pluck('detalle')->implode(', ') }}" class="truncar">{{ $orden->detalles->pluck('detalle')->implode(', ') }}</td>
                                            <td>{{$orden->subtotal}}</td>
                                            <td>{{$orden->descuento}}</td>
                                            <td>{{$orden->montototal}}</td>
                                            <td hidden>{{$orden->fechacomprar}}</td>
                                            <td>{{$orden->fechapagar}}</td>
                                            <td title="{{ $orden->tipotransaccion }}" class="truncar2">{{$orden->tipotransaccion}}</td>
                                            <td title="{{ $orden->formapago }}" class="truncar">{{$orden->formapago}}</td>
                                            <td title="{{ $orden->usuarioregistronombre }}" class="truncar">{{$orden->usuarioregistronombre}}</td>
                                            <td>
                                                @if($orden->documentoorden)
                                                    <a href="{{ asset('ordenesaprobadas/' . $orden->usuarioregistroid . '/' . $orden->documentoorden) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER ORDEN DE COMPRA">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span>No disponible</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($orden->estado === 'APROBADO')
                                                    <span class="badge bg-success">
                                                        {{ optional($orden->detalles->first())->created_at?->format('Y-m-d') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody> 
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ATENCIÓN MÉDICA --}}
            {{-- <div class="tab-pane fade" id="tab-content-5">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="sub-tab-13">
                        <div class="row justify-content-center"> 
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 3000189269</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar1 + $programacioncuentaporpagar1, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 2505314878</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta2 + $totalCuenta2Ingreso - $totalCuenta2Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar2 + $programacioncuentaporpagar2, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4" hidden>
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BMSC: N° Cuenta: 4011113557</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta3 + $totalCuenta3Ingreso - $totalCuenta3Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar3 + $programacioncuentaporpagar1, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('guardar.pagos') }}" method="POST"> 
                            @csrf
                            <div class="card" style="background-color: #f8f8f8;">
                                <div class="card-body">
                                    <div class="row align-items-end" style=" margin-top: -15px; margin-bottom: -15px;">
                                        <div class="form-group col-lg-2">
                                            <label for="bancoorigenprogramacion">Nro. Cuenta Origen:</label>
                                            <select id="bancoorigenprogramacion" name="bancoorigenprogramacion" class="form-control">
                                                <option value="">Seleccione una cuenta</option>
                                                <option value="3000189269" data-saldo="{{ $cuentasConSaldo['3000189269'] ?? 0 }}">3000189269</option>
                                                <option value="2505314878" data-saldo="{{ $cuentasConSaldo['2505314878'] ?? 0 }}">2505314878</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="fechapago">Fecha Pago:</label>
                                            <input type="date" name="fechapago" class="form-control">
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <button type="submit" id="btn-guardar" name="accion" value="guardar" class="btn btn-outline-success mt-4">GUARDAR</button>
                                        </div>
                                        <div class="form-group col-lg-2">
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label for="busqueda">Buscar Factura:</label>
                                            <div class="input-group">
                                                <input type="text" id="busqueda" class="form-control" placeholder="Nro. de Factura...">
                                                <div class="input-group-append">
                                                    <button type="button" id="btn-buscar-factura" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <label>Total:</label>
                                            <input type="text" id="total-seleccionado" class="form-control" value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @foreach ($result as $item)
                                <div class="table-responsive" style="max-height: 65vh;">
                                    <table class="table table-striped">
                                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                            <tr>
                                                <th>Sel.</th>
                                                <th>ID</th>
                                                <th>Proveedor</th>
                                                <th>Est./Esp.</th>
                                                <th>ID.Cli.</th>
                                                <th>Cliente_Nombre</th>
                                                <th>Sucursal_Cli.</th>
                                                <th>Fecha_Bat.</th>
                                                <th>Servicio</th>
                                                <th>Pago</th>
                                                <th>Prog.</th>
                                                <th>Informe</th>
                                                <th hidden>ID Prog</th>
                                                <th>Factura</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $accionesOrdenadas = collect($item['acciones'])->sortBy(function($accion) {
                                                    return $accion['proveedorasignado'] ?? '';
                                                });

                                            @endphp
                                            @foreach ($accionesOrdenadas as $accion)
                                                @php
                                                    $hoy = \Carbon\Carbon::now();
                                                    $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                    $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                    $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                    $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                                @endphp
                                                
                                                @if ($accion['accion'] !== 'INFORME FINAL')
                                                    @if (
                                                            (
                                                                ($accion['accion'] === 'PSICOLOGIA'
                                                                    && $accion['clientecomunid'] !== null
                                                                    && is_null($accion['clienteitaid'])
                                                                    && is_null($accion['clienteauditoriaid'])
                                                                    && !is_null($accion['fechaprogramacion'])
                                                                )
                                                                || (!is_null($accion['informedocumentacion']) && !is_null($accion['fechaprogramacion']))
                                                            )
                                                            && !in_array($accion['pagoservicioinforme'], ['PROCESADO', 'PAGO PROCESADO']) 
                                                            && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $accion['pagoservicioinforme'] ?? '')
                                                        )
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="registros[]" value="{{ $accion['id'] }}" class="check-pago" data-precio="{{ $accion['preciocompra'] }}">
                                                        </td>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td>{{ $accion['proveedorasignado'] }}</td>
                                                        <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                        
                                                        <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                        <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                                                        <td>{{ $accion['sucursalcliente'] ?? 0 }}</td>
                                                        <td>{{ $accion['fechabateria'] }}</td>
                                                        <td>{{ $accion['tramitecliente'] ?? 0 }}</td>
                                                        <td>{{ $accion['preciocompra'] }}</td>
                                                        <td>
                                                            @if ($accion['fechaprogramacion'])
                                                                {{ $accion['fechaprogramacion'] }}
                                                            @else
                                                                <div class="badge 
                                                                    {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-danger' }}">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($accion['accion'] === 'PSICOLOGIA' && $accion['clientecomunid'] !== null)
                                                                {{ $accion['fechaprogramacion'] ?? 'PENDIENTE' }}
                                                            @elseif ($accion['informedocumentacion'])
                                                                {{ $accion['informedocumentacion'] }}
                                                            @else
                                                                <div class="badge badge-danger">PENDIENTE</div>
                                                            @endif
                                                        </td>
                                                        <td hidden>{{ $accion['idprogramacion'] }}</td>
                                                        <td class="td-nrofactura">
                                                            @if (!empty($accion['documentofactura']) || !empty($accion['facturainformefinal']))
                                                                <a 
                                                                    class="btn btn-sm btn-botongris btn-ver-factura" 
                                                                    data-archivo="{{ asset('comprobantescuentaspagar/' . ($accion['documentofactura'] ?? $accion['facturainformefinal'])) }}"
                                                                    title="VER FACTURA"
                                                                >
                                                                    <i class="fas fa-file-alt"></i>
                                                                                                                        </a>
                                                            @endif

                                                            {{ $accion['nrofacturaprog'] ?? 'PENDIENTE' }}
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endif

                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    @if (!is_null($accion['informedocumentacionfinal']) && 
                                                            !in_array($accion['pagoservicioinformefinal'], ['PROCESADO', 'PAGO PROCESADO']) 
                                                        && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $accion['pagoservicioinformefinal'] ?? ''))
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="registros[]" value="{{ $accion['id'] }}" class="check-pago" data-precio="{{ $accion['preciocompra'] }}">
                                                        </td>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td>{{ $accion['proveedorasignado'] }}</td>
                                                        <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                        
                                                        <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                        <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                                                        <td>{{ $accion['sucursalcliente'] ?? 0 }}</td>
                                                        <td>{{ $accion['fechabateria'] }}</td>
                                                        <td>{{ $accion['tramiteinformefinal'] }}</td>
                                                        <td>{{ $accion['preciocompra'] }}</td>
                                                        <td>
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td>
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td hidden>{{ $accion['provinfofinalid'] }}</td>
                                                        <td class="td-nrofactura">
                                                            @if (!empty($accion['documentofactura']) || !empty($accion['facturainformefinal']))
                                                                <a 
                                                                    class="btn btn-sm btn-botongris btn-ver-factura" 
                                                                    data-archivo="{{ asset('comprobantescuentaspagar/' . ($accion['documentofactura'] ?? $accion['facturainformefinal'])) }}"
                                                                    title="VER FACTURA"
                                                                >
                                                                    <i class="fas fa-file-alt"></i>
                                                                                                                        </a>
                                                            @endif

                                                            {{ $accion['nrofacturainformefinal'] ?? 'PENDIENTE' }}
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                            <div>
                                <button type="submit" id="btn-despriorizar" name="accion" value="despriorizar" class="btn btn-sm btn-outline-danger mt-4">DESPRIORIZAR</button>
                            </div>
                            <script>
                                function actualizarTotal() {
                                    let total = 0;
                                    document.querySelectorAll('.check-pago:checked').forEach(function(chk) {
                                        total += parseFloat(chk.dataset.precio || 0);
                                    });
                                    document.getElementById('total-seleccionado').value = total.toFixed(2);
                                }

                                document.querySelectorAll('.check-pago').forEach(function(checkbox) {
                                    checkbox.addEventListener('change', actualizarTotal);
                                });

                                document.getElementById('busqueda').addEventListener('keyup', function () {
                                    const texto = this.value.toLowerCase();

                                    document.querySelectorAll('table tbody tr').forEach(function (fila) {
                                        const visible = Array.from(fila.cells).some(td => td.textContent.toLowerCase().includes(texto));
                                        fila.style.display = visible ? '' : 'none';
                                    });
                                    document.querySelectorAll('table tbody tr').forEach(function (fila) {
                                    const tdFactura = fila.querySelector('.td-nrofactura');
                                    const contiene = tdFactura && tdFactura.textContent.toLowerCase().includes(texto);
                                    fila.style.display = contiene ? '' : 'none';
                                    });

                                    document.querySelectorAll('.check-pago').forEach(function(chk) {
                                        chk.checked = false;
                                    });

                                    document.getElementById('total-seleccionado').value = '0.00';
                                });
                            </script>
                            <script>
                                document.getElementById('btn-buscar-factura').addEventListener('click', function () {
                                    const valorBuscado = document.getElementById('busqueda').value.trim();

                                    let total = 0;

                                    document.querySelectorAll('table tbody tr').forEach(function (fila) {
                                        const tdFactura = fila.querySelector('.td-nrofactura');
                                        const checkbox = fila.querySelector('.check-pago');

                                        if (!tdFactura || !checkbox) return;

                                        const textoFactura = tdFactura.textContent.trim();

                                        if (textoFactura === valorBuscado) {
                                            fila.style.display = '';
                                            total += parseFloat(checkbox.dataset.precio || 0);
                                        } else {
                                            fila.style.display = 'none';
                                        }

                                        checkbox.checked = false;
                                    });

                                    document.getElementById('total-seleccionado').value = total.toFixed(2);
                                });
                            </script>

                        </form>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const cuentaSelect = document.getElementById('bancoorigenprogramacion');
                                const checkboxes = document.querySelectorAll('.check-pago');
                                const btnGuardar = document.getElementById('btn-guardar');
                                const btnDespriorizar = document.getElementById('btn-despriorizar');

                                let saldoDisponible = 0;
                                let modoGuardarActivo = false;
                                let cuentaActual = '';

                                btnGuardar.addEventListener('click', function () {
                                    modoGuardarActivo = true;
                                    actualizarChecks();
                                });

                                btnDespriorizar.addEventListener('click', function () {
                                    modoGuardarActivo = false;
                                    checkboxes.forEach(c => {
                                        c.disabled = false;
                                    });
                                });

                                function actualizarChecks() {
                                    if (!modoGuardarActivo) return;

                                    let totalSeleccionado = 0;
                                    checkboxes.forEach(c => {
                                        if (c.checked) {
                                            totalSeleccionado += parseFloat(c.getAttribute('data-precio'));
                                        }
                                    });

                                    const saldoRestante = saldoDisponible - totalSeleccionado;

                                    checkboxes.forEach(c => {
                                        const precio = parseFloat(c.getAttribute('data-precio'));

                                        if (cuentaActual === '3000189269') {
                                            if (!c.checked) {
                                                c.disabled = precio > saldoRestante;
                                            } else {
                                                c.disabled = false;
                                            }
                                        } else {
                                            c.disabled = false;
                                        }
                                    });
                                }

                                cuentaSelect.addEventListener('change', function () {
                                    const selectedOption = this.options[this.selectedIndex];
                                    cuentaActual = selectedOption.value;
                                    saldoDisponible = parseFloat(selectedOption.getAttribute('data-saldo')) || 0;
                                    modoGuardarActivo = true;

                                    checkboxes.forEach(chk => {
                                        chk.checked = false;
                                        chk.disabled = false;
                                    });

                                    actualizarChecks();
                                });

                                checkboxes.forEach(chk => {
                                    chk.addEventListener('change', function () {
                                        if (!modoGuardarActivo) return;

                                        let totalSeleccionado = 0;
                                        checkboxes.forEach(c => {
                                            if (c.checked) {
                                                totalSeleccionado += parseFloat(c.getAttribute('data-precio'));
                                            }
                                        });

                                        if (cuentaActual === '3000189269' && totalSeleccionado > saldoDisponible) {
                                            alert('No hay suficiente saldo para seleccionar este registro.');
                                            this.checked = false;
                                        }

                                        actualizarChecks();
                                    });
                                });
                            });
                        </script>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>

<div class="modal fade" id="modalFactura" tabindex="-1" role="dialog" aria-labelledby="modalFacturaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><strong>FACTURA</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="height: 80vh;">
        <iframe id="iframeFactura" src="" frameborder="0" style="width:100%; height:100%;"></iframe>
      </div>
    </div>
  </div>
</div>
<script>
    $(document).on('click', '.btn-ver-factura', function () {
        const archivoUrl = $(this).data('archivo');
        $('#iframeFactura').attr('src', archivoUrl);
        $('#modalFactura').modal('show');
    });

    $('#modalFactura').on('hidden.bs.modal', function () {
        $('#iframeFactura').attr('src', '');
    });
</script>

@foreach ($ordenes as $preordenid => $ordenesGrupo)
    <div class="modal fade" id="modal{{ $preordenid }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $preordenid }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel{{ $preordenid }}" style="font-weight: 900; font-size:25px;">DETALLE DE LA PREÓRDEN: {{ $preordenid }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="pdf-form" action="{{ route('generar.ordencompra') }}" method="POST">
                        @csrf
                        <div class="row justify-content-center"> 
                            <!-- Tarjeta Cuenta 1 -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 3000189269</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar1 + $programacioncuentaporpagar1, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                            {{-- <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar1, 2, '.', '') }} <span class="text-muted">Bs.</span></p> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tarjeta Cuenta 2 -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 2505314878</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta2 + $totalCuenta2Ingreso - $totalCuenta2Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar2 + $programacioncuentaporpagar2, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                            {{-- <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar2, 2, '.', '') }} <span class="text-muted">Bs.</span></p> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tarjeta Cuenta 3 -->
                            <div class="col-md-4 mb-4" hidden>
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BMSC: N° Cuenta: 4011113557</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta3 + $totalCuenta3Ingreso - $totalCuenta3Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar3, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card" style="background-color: #fafafa">
                            <div class="card-body">
                                <div class="modal-body">
                                    <div class="row mb-2" style="margin-top: -20px;">
                                        <input type="hidden" name="proveedorid" value="{{ $ordenesGrupo[0]->proveedorid }}">
                                        <input type="hidden" name="proveedornombre" value="{{ $ordenesGrupo[0]->proveedornombre }}">
                                        <input type="hidden" name="usuariopreorden" value="{{ $ordenesGrupo[0]->usuarioregistronombre }}">
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Tipo de Transacción:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->tipotransaccion }}</p>
                                            <input type="hidden" name="tipotransaccion" value="{{ $ordenesGrupo[0]->tipotransaccion }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Fecha de Compra:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->fechacomprar }}</p>
                                            <input type="hidden" name="fechacomprar" value="{{ $ordenesGrupo[0]->fechacomprar }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Subtotal:</strong></p>
                                            <input type="text" name="subtotal" value="{{ $subtotal }}" style="width: 100px; height: 25px;" readonly>
                                        </div>
                                    </div>
                
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Forma de Pago:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->formapago }}</p>
                                            <input type="hidden" name="formapago" value="{{ $ordenesGrupo[0]->formapago }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Fecha de Pago:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->fechapagar }}</p>
                                            <input type="hidden" name="fechapagar" value="{{ $ordenesGrupo[0]->fechapagar }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Descuento Total:</strong></p>
                                            <input type="text" name="descuento" value="{{ $descuentoTotal }}" style="width: 100px; height: 25px;" readonly>
                                        </div>
                                    </div>
                
                                    <div class="row mb-2" style="margin-bottom: -20px;"> 
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Banco de Origen:</strong></p>
                                            <p class="mb-0 text-muted">
                                                {{ $ordenesGrupo[0]->proveedorServicio->bancoorigen ?? 'No especificado' }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Fecha de Registro:</strong></p>
                                            <p class="mb-0 text-muted">{{ \Carbon\Carbon::parse($ordenesGrupo[0]->created_at)->format('Y-m-d') }}</p>
                                        </div>
                                        
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Monto Total:</strong></p>
                                            <input type="text" name="montototal" value="{{ $montoTotal }}" style="width: 100px; height: 25px;" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-2" style="margin-bottom: -20px;"> 
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Observación:</strong></p>
                                            <p class="mb-0 text-muted">
                                                {{ $ordenesGrupo[0]->observacion ? $ordenesGrupo[0]->observacion : 'SIN OBSERVACIONES' }}
                                            </p>
                                            <input type="hidden" name="observacion" value="{{ $ordenesGrupo[0]->observacion }}">
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Sucursal:</strong></p>
                                            {{-- <p class="mb-0 text-muted">{{ ($ordenesGrupo[0]->sucursalgasto) }}</p>
                                            <input type="hidden" name="sucursalgasto" value="{{ $ordenesGrupo[0]->sucursalgasto }}"> --}}
                                            @php
                                                $sucursales = collect($ordenesGrupo)->pluck('sucursalgasto')->unique()->implode(', ');
                                            @endphp
                                            <p class="mb-0 text-muted">{{ $sucursales }}</p>
                                            <input type="hidden" name="sucursalgasto" value="{{ $sucursales }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"> </th>
                                                <th>N.Cuenta_Origen</th>
                                                <th>ID</th>
                                                <th>Detalle</th>
                                                <th>Especif.</th>
                                                <th>Color</th>
                                                <th>Marca</th>
                                                <th>Cant.</th>
                                                <th>Subto.</th>
                                                <th>Desc.</th>
                                                <th>Total</th>
                                                <th>Stock_Actual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ordenesGrupo as $orden)
                                                <tr>
                                                    <td><input type="checkbox" name="ordenes[]" value="{{ $orden->id }}"></td>
                                                    <td>
                                                        @php
                                                            $bancoorigen = $orden->proveedorServicio->bancoorigen ?? null;
                                                        @endphp
                                                        <select name="bancodestino[{{ $orden->id }}]" id="bancodestino" class="form-control" style="height: 28px; padding: 2px 6px;">
                                                            <option value=""></option>
                                                    
                                                            @if ($bancoorigen === 'CUENTA FACTURADA')
                                                                <option value="3000189269">3000189269</option>
                                                                {{-- <option value="4011113557">4011113557</option> --}}
                                                            @elseif ($bancoorigen === 'CUENTA NO FACTURADA')
                                                                <option value="2505314878">2505314878</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td>{{ $orden->id }}</td>
                                                    <td>{{ $orden->detalle }}</td>
                                                    <td>{{ $orden->portafolio->especificacionmedida ?? 'N/A' }}</td>
                                                    <td>{{ $orden->portafolio->color ?? 'N/A' }}</td>
                                                    <td>{{ $orden->portafolio->marca ?? 'N/A' }}</td>
                                                    <td>{{ $orden->cantidad }}</td>
                                                    <td>{{ number_format($orden->preciounitario, 2) }}</td>
                                                    <td>{{ number_format($orden->descuentounitario, 2) }}</td>
                                                    <td>
                                                        <input type="text" 
                                                        class="form-control form-control-sm totalunitario-input" 
                                                        name="totalunitario[{{ $orden->id }}]"
                                                        value="{{ number_format($orden->totalunitario, 2) }}" 
                                                        style="width: 80px; height: 25px; padding: 2px 6px;">
                                                    </td>
                                                    <td class="text-center">
                                                        @if(empty($orden->inventario->stockactual) || $orden->inventario->stockactual == 0)
                                                            <span class="badge badge-danger p-2">Sin stock</span>
                                                        @else
                                                            <span class="badge badge-warning p-2">{{ $orden->inventario->stockactual }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <script>
                                                const saldosIniciales = @json($cuentasConSaldo);
                                                let saldosDisponibles = JSON.parse(JSON.stringify(saldosIniciales));
                                            
                                                document.addEventListener('DOMContentLoaded', () => {
                                                    const selects = document.querySelectorAll('select[id="bancodestino"]');
                                            
                                                    selects.forEach(select => {
                                                        select.addEventListener('change', function () {
                                                            const selectedCuenta = this.value;
                                                            const row = this.closest('tr');
                                                            const totalUnitario = parseFloat(row.children[9].innerText.replace(',', ''));
                                                            saldosDisponibles = JSON.parse(JSON.stringify(saldosIniciales));
                                                            document.querySelectorAll('select[id="bancodestino"]').forEach(sel => {
                                                                if (sel !== this && sel.value) {
                                                                    const fila = sel.closest('tr');
                                                                    const total = parseFloat(fila.children[10].innerText.replace(',', ''));
                                                                    saldosDisponibles[sel.value] -= total;
                                                                }
                                                            });
                                                            // BLOQUEO DE APROBAR SIN SALDO EN CUENTAS
                                                            //if (selectedCuenta && totalUnitario >= saldosDisponibles[selectedCuenta]) {
                                                            //    alert(`No hay saldo suficiente en la cuenta ${selectedCuenta}. Saldo disponible: ${saldosDisponibles[selectedCuenta].toFixed(2)}`);
                                                            //    this.value = '';
                                                            //}
                                                        });
                                                    });
                                                });
                                            </script>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-end">
                            @can('admin.inventario.aprobarpreordenes')
                            <button type="submit" class="btn btn-verpreorden">APROBAR PRE-ÓRDEN</button>
                            @endcan
                            <button type="button" class="btn btn-cerrar" data-dismiss="modal">CERRAR</button>
                        </div>
                    </form> 
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        document.querySelectorAll('.modal').forEach(modal => {
                            const selectAll = modal.querySelector('#selectAll');
                            if (selectAll) {
                                selectAll.addEventListener('change', function () {
                                    const checkboxes = modal.querySelectorAll('input[name="ordenes[]"]');
                                    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                                });
                            }
                        });
                    });
                </script>
                <script>
                    $(document).ready(function() {
                        function calcularTotales(modal) { 
                            let subtotal = 0;
                            let descuentoTotal = 0;
                            let montoTotal = 0;

                            modal.find('tbody tr').each(function() {
                                let checkbox = $(this).find('input[type="checkbox"]');
                                if (checkbox.prop('checked')) {
                                    let cantidad = parseFloat($(this).find('td:nth-child(8)').text().replace(',', '')) || 0;
                                    let precioUnitario = parseFloat($(this).find('td:nth-child(9)').text().replace(',', '')) || 0;
                                    let descuentoUnitario = parseFloat($(this).find('td:nth-child(10)').text().replace(',', '')) || 0;
                                    let input = $(this).find('.totalunitario-input');
                                    let totalUnitarioStr = input.val() || input.attr('value') || '0';
                                    let totalUnitario = parseFloat(totalUnitarioStr.replace(/,/g, '')) || 0;
                                    let subtotalItem = cantidad !== 0 ? cantidad * precioUnitario : precioUnitario;
                                    subtotal += precioUnitario;
                                    descuentoTotal += descuentoUnitario;
                                    montoTotal += totalUnitario;
                                }
                            });
                            modal.find('input[name="subtotal"]').val(subtotal.toFixed(2));  
                            modal.find('input[name="descuento"]').val(descuentoTotal.toFixed(2));
                            modal.find('input[name="montototal"]').val(montoTotal.toFixed(2));
                        }
                        $('.modal').on('change', '#selectAll', function() {
                            let modal = $(this).closest('.modal');
                            let isChecked = $(this).prop('checked');
                            modal.find('tbody input[type="checkbox"]').prop('checked', isChecked);
                            calcularTotales(modal);
                        });
                        $('.modal').on('change', 'tbody input[type="checkbox"]', function() {
                            let modal = $(this).closest('.modal');
                            calcularTotales(modal);
                        });
                        $('.modal').on('hidden.bs.modal', function() {
                            let modal = $(this);
                            modal.find('input[type="checkbox"]').prop('checked', false);
                            modal.find('input[name="subtotal"], input[name="descuento"], input[name="montototal"]').val('0.00');
                            modal.find('#selectAll').prop('checked', false);
                        });
                        $('.modal').on('input', '.totalunitario-input', function() {
                            let modal = $(this).closest('.modal');
                            calcularTotales(modal);
                        });
                    });
                </script>
            </div>
        </div>
    </div>
@endforeach

@foreach ($ordenesservicio as $preordenid => $ordenesGrupo)
    <div class="modal fade" id="modal2{{ $preordenid }}" tabindex="-1" role="dialog" aria-labelledby="modal2Label{{ $preordenid }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel{{ $preordenid }}" style="font-weight: 900; font-size:25px;">DETALLE DE LA PRE-ÓRDEN DE SERVICIO: {{ $preordenid }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="pdf-form" action="{{ route('generar.ordenservicio') }}" method="POST">
                        @csrf
                        @can('admin.inventario.aprobarpreordenes')
                        <div class="row justify-content-center"> 
                            <!-- Tarjeta Cuenta 1 -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 3000189269</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar1 + $programacioncuentaporpagar1, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                           {{--  <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar1, 2, '.', '') }} <span class="text-muted">Bs.</span></p> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tarjeta Cuenta 2 -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 2505314878</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta2 + $totalCuenta2Ingreso - $totalCuenta2Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar2 + $programacioncuentaporpagar2, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                            {{-- <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar2, 2, '.', '') }} <span class="text-muted">Bs.</span></p> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tarjeta Cuenta 3 -->
                            <div class="col-md-4 mb-4" hidden>
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BMSC: N° Cuenta: 4011113557</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta3 + $totalCuenta3Ingreso - $totalCuenta3Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar3, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcan
                        <div class="card" style="background-color: #fafafa">
                            <div class="card-body">
                                <div class="modal-body">
                                    <div class="row mb-2" style="margin-top: -20px;">
                                        <input type="hidden" name="proveedorid" value="{{ $ordenesGrupo[0]->proveedorid }}">
                                        <input type="hidden" name="proveedornombre" value="{{ $ordenesGrupo[0]->proveedornombre }}">
                                        <input type="hidden" name="usuariopreorden" value="{{ $ordenesGrupo[0]->usuarioregistronombre }}">
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Tipo de Transacción:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->tipotransaccion }}</p>
                                            <input type="hidden" name="tipotransaccion" value="{{ $ordenesGrupo[0]->tipotransaccion }}">
                                        </div>
                                        <div class="col-md-4 text-end" hidden>
                                            <p class="mb-0"><strong>Fecha de Compra:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->fechacomprar }}</p>
                                            <input type="hidden" name="fechacomprar" value="{{ $ordenesGrupo[0]->fechacomprar }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Fecha de Pago:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->fechapagar }}</p>
                                            <input type="hidden" name="fechapagar" value="{{ $ordenesGrupo[0]->fechapagar }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Subtotal:</strong></p>
                                            <input type="text" name="subtotal" value="{{ $subtotal }}" style="width: 100px; height: 25px;" readonly>
                                        </div>
                                    </div>
                
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Forma de Pago:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->formapago }}</p>
                                            <input type="hidden" name="formapago" value="{{ $ordenesGrupo[0]->formapago }}">
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Fecha de Registro:</strong></p>
                                            <p class="mb-0 text-muted">{{ \Carbon\Carbon::parse($ordenesGrupo[0]->created_at)->format('Y-m-d') }}</p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Descuento Total:</strong></p>
                                            <input type="text" name="descuento" value="{{ $descuentoTotal }}" style="width: 100px; height: 25px;" readonly>
                                        </div>
                                    </div>
                
                                    <div class="row mb-2" style="margin-bottom: -20px;"> 
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Banco de Origen:</strong></p>
                                            <p class="mb-0 text-muted">
                                                {{ $ordenesGrupo[0]->proveedorServicio->bancoorigen ?? 'No especificado' }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Sucursal:</strong></p>
                                            {{-- <p class="mb-0 text-muted">{{ ($ordenesGrupo[0]->sucursalgasto) }}</p>
                                            <input type="hidden" name="sucursalgasto2" value="{{ $ordenesGrupo[0]->sucursalgasto }}"> --}}
                                            @php
                                                $sucursales2 = collect($ordenesGrupo)->pluck('sucursalgasto')->unique()->implode(', ');
                                            @endphp
                                            <p class="mb-0 text-muted">{{ $sucursales2 }}</p>
                                            <input type="hidden" name="sucursalgasto2" value="{{ $sucursales2 }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Monto Total:</strong></p>
                                            <input type="text" name="montototal" value="{{ $montoTotal }}" style="width: 100px; height: 25px;" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-2" style="margin-bottom: -20px;"> 
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Observación:</strong></p>
                                            <p class="mb-0 text-muted">
                                                {{ $ordenesGrupo[0]->observacion ? $ordenesGrupo[0]->observacion : 'SIN OBSERVACIONES' }}
                                            </p>
                                            <input type="hidden" name="observacion" value="{{ $ordenesGrupo[0]->observacion }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                @can('admin.inventario.aprobarpreordenes')
                                                <th><input type="checkbox" id="selectAll2"> </th>
                                                <th>N.Cuenta_Origen</th>
                                                @endcan
                                                <th>ID</th>
                                                <th>Detalle</th>
                                                <th hidden>Cant.</th>
                                                <th hidden>Cant.</th>
                                                <th hidden>Cant.</th>
                                                <th hidden>Cant.</th>
                                                <th>Subto.</th>
                                                <th>Desc.</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ordenesGrupo as $orden)
                                                <tr>
                                                    @can('admin.inventario.aprobarpreordenes')
                                                    <td><input type="checkbox" name="ordenes2[]" value="{{ $orden->id }}"></td>
                                                    <td>
                                                        @php
                                                            $bancoorigen = $orden->proveedorServicio->bancoorigen ?? null;
                                                        @endphp
                                                        <select name="bancodestino2[{{ $orden->id }}]" id="bancodestino" class="form-control" style="height: 28px; padding: 2px 6px;">
                                                            <option value=""></option>
                                                    
                                                            @if ($bancoorigen === 'CUENTA FACTURADA')
                                                                <option value="3000189269">3000189269</option>
                                                                {{-- <option value="4011113557">4011113557</option> --}}
                                                            @elseif ($bancoorigen === 'CUENTA NO FACTURADA')
                                                                <option value="2505314878">2505314878</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    @endcan
                                                    <td>{{ $orden->id }}</td>
                                                    <td>{{ $orden->detalle }}</td>
                                                    <td hidden>{{ $orden->portafolio->especificacionmedida ?? 'N/A' }}</td>
                                                    <td hidden>{{ $orden->portafolio->color ?? 'N/A' }}</td>
                                                    <td hidden>{{ $orden->portafolio->marca ?? 'N/A' }}</td>
                                                    <td hidden>{{ $orden->cantidad }}</td>
                                                    <td>{{ number_format($orden->preciounitario, 2) }}</td>
                                                    <td>{{ number_format($orden->descuentounitario, 2) }}</td>
                                                    <td>
                                                        <input type="text" 
                                                        class="form-control form-control-sm totalunitario-input" 
                                                        name="totalunitario2[{{ $orden->id }}]"
                                                        value="{{ number_format($orden->totalunitario, 2) }}" 
                                                        style="width: 80px; height: 25px; padding: 2px 6px;">
                                                    </td>
                                                    
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-end">
                            @can('admin.inventario.aprobarpreordenes')
                                <button type="submit" class="btn btn-verpreorden">APROBAR PRE-ÓRDEN</button>
                            @endcan
                            <button type="button" class="btn btn-cerrar" data-dismiss="modal">CERRAR</button>
                        </div>
                    </form> 
                </div>
                
                <script>
                    document.addEventListener('DOMContentLoaded2', function () {
                        document.querySelectorAll('.modal').forEach(modal => {
                            const selectAll = modal.querySelector('#selectAll2');
                            if (selectAll) {
                                selectAll.addEventListener('change', function () {
                                    const checkboxes = modal.querySelectorAll('input[name="ordenes2[]"]');
                                    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                                });
                            }
                        });
                    });
                </script>
                <script>
                    $(document).ready(function() {
                        function calcularTotales2(modal) { 
                            let subtotal = 0;
                            let descuentoTotal = 0;
                            let montoTotal = 0;
                            modal.find('tbody tr').each(function() {
                                let checkbox = $(this).find('input[type="checkbox"]');
                                if (checkbox.prop('checked')) {
                                    let cantidad = parseFloat($(this).find('td:nth-child(8)').text().replace(',', '')) || 0;
                                    let precioUnitario = parseFloat($(this).find('td:nth-child(9)').text().replace(',', '')) || 0;
                                    let descuentoUnitario = parseFloat($(this).find('td:nth-child(10)').text().replace(',', '')) || 0;
                                    let input = $(this).find('.totalunitario-input');
                                    let totalUnitarioStr = input.val() || input.attr('value') || '0';
                                    let totalUnitario = parseFloat(totalUnitarioStr.replace(/,/g, '')) || 0;
                                    let subtotalItem = cantidad !== 0 ? cantidad * precioUnitario : precioUnitario;
                                    subtotal += precioUnitario;
                                    descuentoTotal += descuentoUnitario;
                                    montoTotal += totalUnitario;
                                }
                            });
                            modal.find('input[name="subtotal"]').val(subtotal.toFixed(2));  
                            modal.find('input[name="descuento"]').val(descuentoTotal.toFixed(2));
                            $('input[name="montototal"]').val(montoTotal.toFixed(2));
                        }
                        $('.modal').on('change', '#selectAll2', function() {
                            let modal = $(this).closest('.modal');
                            let isChecked = $(this).prop('checked');
                            modal.find('tbody input[type="checkbox"]').prop('checked', isChecked);
                            calcularTotales2(modal);
                        });
                        $('.modal').on('change', 'tbody input[type="checkbox"]', function() {
                            let modal = $(this).closest('.modal');
                            calcularTotales2(modal);
                        });
                        $('.modal').on('hidden.bs.modal', function() {
                            let modal = $(this);
                            modal.find('input[type="checkbox"]').prop('checked', false);
                            modal.find('input[name="subtotal"], input[name="descuento"], input[name="montototal"]').val('0.00');
                            modal.find('#selectAll2').prop('checked', false);
                        });
                        $('.modal').on('input', '.totalunitario-input', function() {
                            let modal = $(this).closest('.modal');
                            calcularTotales2(modal);
                        });

                    });
                </script>
            </div>
        </div>
    </div>
@endforeach

@foreach ($ordenespersonal as $preordenid => $ordenesGrupo)
    <div class="modal fade" id="modal3{{ $preordenid }}" tabindex="-1" role="dialog" aria-labelledby="modal3Label{{ $preordenid }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel{{ $preordenid }}" style="font-weight: 900; font-size:25px;">DETALLE DE LA PREÓRDEN: {{ $preordenid }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="pdf-form" action="{{ route('generar.ordenpersonal') }}" method="POST">
                        @csrf
                        <div class="row justify-content-center"> 
                            <!-- Tarjeta Cuenta 1 -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 3000189269</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar1 + $programacioncuentaporpagar1, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                            {{-- <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar1, 2, '.', '') }} <span class="text-muted">Bs.</span></p> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tarjeta Cuenta 2 -->
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 2505314878</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta2 + $totalCuenta2Ingreso - $totalCuenta2Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar2 + $programacioncuentaporpagar2, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                            {{-- <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar2, 2, '.', '') }} <span class="text-muted">Bs.</span></p> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tarjeta Cuenta 3 -->
                            <div class="col-md-4 mb-4" hidden>
                                <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                    <div class="card-header text-center border-0 position-relative" style="background: rgba(0, 0, 0, 0.03); padding: 0.5rem;">
                                        <h6 class="mt-2 text-uppercase font-weight-bold">BMSC: N° Cuenta: 4011113557</h6>
                                    </div>
                                    <div class="card-body" style="text-align: left;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">Saldo</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta3 + $totalCuenta3Ingreso - $totalCuenta3Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <h6 class="font-weight-bold">C.Pagar</h6>
                                            <p class="h5 fw-bold text-success">{{ number_format($cuentaporpagar3, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card" style="background-color: #fafafa">
                            <div class="card-body">
                                <div class="modal-body">
                                    <div class="row mb-2" style="margin-top: -20px;">
                                        <input type="hidden" name="proveedorid" value="{{ $ordenesGrupo[0]->proveedorid }}">
                                        <input type="hidden" name="proveedornombre" value="{{ $ordenesGrupo[0]->proveedornombre }}">
                                        <input type="hidden" name="usuariopreorden" value="{{ $ordenesGrupo[0]->usuarioregistronombre }}">
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Tipo de Transacción:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->tipotransaccion }}</p>
                                            <input type="hidden" name="tipotransaccion" value="{{ $ordenesGrupo[0]->tipotransaccion }}">
                                        </div>
                                        <div class="col-md-4 text-end" hidden>
                                            <p class="mb-0"><strong>Fecha de Compra:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->fechacomprar }}</p>
                                            <input type="hidden" name="fechacomprar" value="{{ $ordenesGrupo[0]->fechacomprar }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Fecha de Pago:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->fechapagar }}</p>
                                            <input type="hidden" name="fechapagar" value="{{ $ordenesGrupo[0]->fechapagar }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Subtotal:</strong></p>
                                            <input type="text" name="subtotal" value="{{ $subtotal }}" style="width: 100px; height: 25px;" readonly>
                                        </div>
                                    </div>
                
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Forma de Pago:</strong></p>
                                            <p class="mb-0 text-muted">{{ $ordenesGrupo[0]->formapago }}</p>
                                            <input type="hidden" name="formapago" value="{{ $ordenesGrupo[0]->formapago }}">
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Fecha de Registro:</strong></p>
                                            <p class="mb-0 text-muted">{{ \Carbon\Carbon::parse($ordenesGrupo[0]->created_at)->format('Y-m-d') }}</p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Descuento Total:</strong></p>
                                            <input type="text" name="descuento" value="{{ $descuentoTotal }}" style="width: 100px; height: 25px;" readonly>
                                        </div>
                                    </div>
                
                                    <div class="row mb-2" style="margin-bottom: -20px;"> 
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Banco de Origen:</strong></p>
                                            <p class="mb-0 text-muted">
                                                {{ $ordenesGrupo[0]->proveedorServicio->bancoorigen ?? 'No especificado' }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Sucursal:</strong></p>
                                            {{-- <p class="mb-0 text-muted">{{ ($ordenesGrupo[0]->sucursalgasto) }}</p>
                                            <input type="hidden" name="sucursalgasto3" value="{{ $ordenesGrupo[0]->sucursalgasto }}"> --}}
                                            @php
                                                $sucursales3 = collect($ordenesGrupo)->pluck('sucursalgasto')->unique()->implode(', ');
                                            @endphp
                                            <p class="mb-0 text-muted">{{ $sucursales3 }}</p>
                                            <input type="hidden" name="sucursalgasto3" value="{{ $sucursales3 }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <p class="mb-0"><strong>Monto Total:</strong></p>
                                            <input type="text" name="montototal" value="{{ $montoTotal }}" style="width: 100px; height: 25px;" readonly>
                                        </div>
                                    </div>

                                    <div class="row mb-2" style="margin-bottom: -20px;"> 
                                        <div class="col-md-4">
                                            <p class="mb-0"><strong>Observación:</strong></p>
                                            <p class="mb-0 text-muted">
                                                {{ $ordenesGrupo[0]->observacion ? $ordenesGrupo[0]->observacion : 'SIN OBSERVACIONES' }}
                                            </p>
                                            <input type="hidden" name="observacion" value="{{ $ordenesGrupo[0]->observacion }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll3"> </th>
                                                <th>N.Cuenta_Origen</th>
                                                <th>ID</th>
                                                <th>Detalle</th>
                                                <th hidden>Cant.</th>
                                                <th hidden>Cant.</th>
                                                <th hidden>Cant.</th>
                                                <th hidden>Cant.</th>
                                                <th>Prec.Uni.</th>
                                                <th>Desc.</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ordenesGrupo as $orden)
                                                <tr>
                                                    <td><input type="checkbox" name="ordenes3[]" value="{{ $orden->id }}"></td>
                                                    <td>
                                                        @php
                                                            $bancoorigen = $orden->proveedorServicio->bancoorigen ?? null;
                                                        @endphp
                                                        <select name="bancodestino3[{{ $orden->id }}]" id="bancodestino" class="form-control" style="height: 28px; padding: 2px 6px;">
                                                            <option value=""></option>
                                                    
                                                            @if ($bancoorigen === 'CUENTA FACTURADA')
                                                                <option value="3000189269">3000189269</option>
                                                                {{-- <option value="4011113557">4011113557</option> --}}
                                                            @elseif ($bancoorigen === 'CUENTA NO FACTURADA')
                                                                <option value="2505314878">2505314878</option>
                                                            @endif
                                                        </select>
                                                    </td>
                                                    <td>{{ $orden->id }}</td>
                                                    <td>{{ $orden->detalle }}</td>
                                                    <td hidden>{{ $orden->portafolio->especificacionmedida ?? 'N/A' }}</td>
                                                    <td hidden>{{ $orden->portafolio->color ?? 'N/A' }}</td>
                                                    <td hidden>{{ $orden->portafolio->marca ?? 'N/A' }}</td>
                                                    <td hidden>{{ $orden->cantidad }}</td>
                                                    <td>{{ number_format($orden->preciounitario, 2) }}</td>
                                                    <td>{{ number_format($orden->descuentounitario, 2) }}</td>
                                                    <td>
                                                        <input type="text" 
                                                        class="form-control form-control-sm totalunitario-input" 
                                                        name="totalunitario3[{{ $orden->id }}]"
                                                        value="{{ number_format($orden->totalunitario, 2) }}" 
                                                        style="width: 80px; height: 25px; padding: 2px 6px;">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-end">
                            @can('admin.inventario.aprobarpreordenes')
                            <button type="submit" class="btn btn-verpreorden">APROBAR PRE-ÓRDEN</button>
                            @endcan
                            <button type="button" class="btn btn-cerrar" data-dismiss="modal">CERRAR</button>
                        </div>
                    </form> 
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        document.querySelectorAll('.modal').forEach(modal => {
                            const selectAll = modal.querySelector('#selectAll3');
                            if (selectAll) {
                                selectAll.addEventListener('change', function () {
                                    const checkboxes = modal.querySelectorAll('input[name="ordenes3[]"]');
                                    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                                });
                            }
                        });
                    });
                </script>
                
                <script>
                    $(document).ready(function() {
                        function calcularTotales3(modal) { 
                            let subtotal = 0;
                            let descuentoTotal = 0;
                            let montoTotal = 0;

                            modal.find('tbody tr').each(function() {
                                let checkbox = $(this).find('input[type="checkbox"]');
                                if (checkbox.prop('checked')) {
                                    let cantidad = parseFloat($(this).find('td:nth-child(8)').text().replace(',', '')) || 0;
                                    let precioUnitario = parseFloat($(this).find('td:nth-child(9)').text().replace(',', '')) || 0;
                                    let descuentoUnitario = parseFloat($(this).find('td:nth-child(10)').text().replace(',', '')) || 0;
                                    let input = $(this).find('.totalunitario-input');
                                    let totalUnitarioStr = input.val() || input.attr('value') || '0';
                                    let totalUnitario = parseFloat(totalUnitarioStr.replace(/,/g, '')) || 0;
                                    let subtotalItem = cantidad !== 0 ? cantidad * precioUnitario : precioUnitario;
                                    subtotal += precioUnitario;
                                    descuentoTotal += descuentoUnitario;
                                    montoTotal += totalUnitario;
                                }
                            });
                            modal.find('input[name="subtotal"]').val(subtotal.toFixed(2));  
                            modal.find('input[name="descuento"]').val(descuentoTotal.toFixed(2));
                            modal.find('input[name="montototal"]').val(montoTotal.toFixed(2));
                        }
                        $('.modal').on('change', '#selectAll3', function() {
                            let modal = $(this).closest('.modal');
                            let isChecked = $(this).prop('checked');
                    
                            modal.find('tbody input[type="checkbox"]').prop('checked', isChecked);
                            calcularTotales3(modal);
                        });
                        $('.modal').on('change', 'tbody input[type="checkbox"]', function() {
                            let modal = $(this).closest('.modal');
                            calcularTotales3(modal);
                        });
                        $('.modal').on('hidden.bs.modal', function() {
                            let modal = $(this);
                            modal.find('input[type="checkbox"]').prop('checked', false);
                            modal.find('input[name="subtotal"], input[name="descuento"], input[name="montototal"]').val('0.00');
                            modal.find('#selectAll3').prop('checked', false);
                        });
                        $('.modal').on('input', '.totalunitario-input', function() {
                            let modal = $(this).closest('.modal');
                            calcularTotales3(modal);
                        });
                    });
                </script>   
            </div>
        </div>
    </div>
@endforeach
@stop

@section('js')
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.actualizar-fecha-btn').forEach(button => {
            button.addEventListener('click', function () {
                const preordenid = this.getAttribute('data-preordenid');
                const inputFecha = this.closest('.input-group').querySelector('input[type="date"]');
                const nuevaFecha = inputFecha.value;

                if (!nuevaFecha) {
                    alert("Debe seleccionar una fecha válida.");
                    return;
                }

                fetch("{{ route('actualizar.fechapagar') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        preordenid: preordenid,
                        fechapagar: nuevaFecha
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Fecha actualizada correctamente");
                        location.reload();
                    } else {
                        alert("Error al actualizar la fecha");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error al actualizar la fecha");
                });
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.actualizar-fecha-btn').forEach(button => {
            const inputFecha = button.closest('.input-group').querySelector('input[type="date"]');
            inputFecha.addEventListener('change', function () {
                const nuevaFecha = inputFecha.value;
                const preordenid = inputFecha.getAttribute('data-preordenid');
                if (nuevaFecha !== inputFecha.getAttribute('value')) {
                    button.style.display = 'inline-block';
                } else {
                    button.style.display = 'none';
                }
            });
        });
    });
</script>
<script>
    document.getElementById('actualizarPrioridadBtn').addEventListener('click', function () {
        let prioridades = [];

        document.querySelectorAll('.check-prioridad').forEach(cb => {
            prioridades.push({
                preordenid: cb.dataset.preordenid,
                priorizado: cb.checked ? 'PRIORITARIO' : null
            });
        });

        if (prioridades.length === 0) {
            alert('No hay registros visibles.');
            return;
        }

        fetch("{{ route('actualizar.prioridad.preorden') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                prioridades: prioridades
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => {
            console.error("Error al actualizar:", error);
            alert("Error al actualizar prioridad.");
        });
    });

    document.getElementById('actualizarPrioridadBtn2').addEventListener('click', function () {
        let prioridades = [];

        document.querySelectorAll('.check-prioridad').forEach(cb => {
            prioridades.push({
                preordenid: cb.dataset.preordenid,
                priorizado: cb.checked ? 'PRIORITARIO' : null
            });
        });

        if (prioridades.length === 0) {
            alert('No hay registros visibles.');
            return;
        }

        fetch("{{ route('actualizar.prioridad.preorden') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                prioridades: prioridades
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => {
            console.error("Error al actualizar:", error);
            alert("Error al actualizar prioridad.");
        });
    });

    document.getElementById('actualizarPrioridadBtn3').addEventListener('click', function () {
        let prioridades = [];

        document.querySelectorAll('.check-prioridad').forEach(cb => {
            prioridades.push({
                preordenid: cb.dataset.preordenid,
                priorizado: cb.checked ? 'PRIORITARIO' : null
            });
        });

        if (prioridades.length === 0) {
            alert('No hay registros visibles.');
            return;
        }

        fetch("{{ route('actualizar.prioridad.preorden') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                prioridades: prioridades
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => {
            console.error("Error al actualizar:", error);
            alert("Error al actualizar prioridad.");
        });
    });
</script>
<script>
    document.getElementById('unificarPreordenBtn').addEventListener('click', function () {
        const seleccionados = Array.from(document.querySelectorAll('.check-unificar:checked'));

        if (seleccionados.length < 2) {
            alert('Seleccione al menos dos órdenes para unificar.');
            return;
        }

        const proveedores = new Set();
        const fechas = new Set();
        const ids = [];

        seleccionados.forEach(cb => {
            const proveedor = (cb.dataset.proveedornombre || '').trim().toLowerCase();
            const fecha = (cb.dataset.fechapagar || '').trim();
            ids.push(cb.dataset.preordenid);
            proveedores.add(proveedor);
            fechas.add(fecha);
        });

        if (proveedores.size > 1 || fechas.size > 1) {
            alert('Las órdenes seleccionadas deben tener el MISMO PROVEEDOR y la MISMA FECHA DE PAGO.');
            return;
        }

        // Elegir la preorden más antigua alfabéticamente
        const preordenBase = ids.reduce((a, b) => a < b ? a : b);

        if (!confirm(`¿Desea unificar las órdenes seleccionadas bajo la preorden "${preordenBase}"?`)) {
            return;
        }

        fetch("{{ route('unificar.preordenes') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                preordenes: ids,
                preorden_destino: preordenBase
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Actualización exitosa.');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al actualizar.');
        });
    });


    document.getElementById('unificarPreordenBtn2').addEventListener('click', function () {
        const seleccionados = Array.from(document.querySelectorAll('.check-unificar:checked'));

        if (seleccionados.length < 2) {
            alert('Seleccione al menos dos órdenes para unificar.');
            return;
        }

        const proveedores = new Set();
        const fechas = new Set();
        const ids = [];

        seleccionados.forEach(cb => {
            const proveedor = (cb.dataset.proveedornombre || '').trim().toLowerCase();
            const fecha = (cb.dataset.fechapagar || '').trim();
            ids.push(cb.dataset.preordenid);
            proveedores.add(proveedor);
            fechas.add(fecha);
        });

        if (proveedores.size > 1 || fechas.size > 1) {
            alert('Las órdenes seleccionadas deben tener el MISMO PROVEEDOR y la MISMA FECHA DE PAGO.');
            return;
        }

        // Elegir la preorden más antigua alfabéticamente
        const preordenBase = ids.reduce((a, b) => a < b ? a : b);

        if (!confirm(`¿Desea unificar las órdenes seleccionadas bajo la preorden "${preordenBase}"?`)) {
            return;
        }

        fetch("{{ route('unificar.preordenes') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                preordenes: ids,
                preorden_destino: preordenBase
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Actualización exitosa.');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al actualizar.');
        });
    });


    document.getElementById('unificarPreordenBtn3').addEventListener('click', function () {
        const seleccionados = Array.from(document.querySelectorAll('.check-unificar:checked'));

        if (seleccionados.length < 2) {
            alert('Seleccione al menos dos órdenes para unificar.');
            return;
        }

        const proveedores = new Set();
        const fechas = new Set();
        const ids = [];

        seleccionados.forEach(cb => {
            const proveedor = (cb.dataset.proveedornombre || '').trim().toLowerCase();
            const fecha = (cb.dataset.fechapagar || '').trim();
            ids.push(cb.dataset.preordenid);
            proveedores.add(proveedor);
            fechas.add(fecha);
        });

        if (proveedores.size > 1 || fechas.size > 1) {
            alert('Las órdenes seleccionadas deben tener el MISMO PROVEEDOR y la MISMA FECHA DE PAGO.');
            return;
        }

        // Elegir la preorden más antigua alfabéticamente
        const preordenBase = ids.reduce((a, b) => a < b ? a : b);

        if (!confirm(`¿Desea unificar las órdenes seleccionadas bajo la preorden "${preordenBase}"?`)) {
            return;
        }

        fetch("{{ route('unificar.preordenes') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                preordenes: ids,
                preorden_destino: preordenBase
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Actualización exitosa.');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al actualizar.');
        });
    });
</script>
@endsection