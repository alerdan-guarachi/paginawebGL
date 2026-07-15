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
        content: '☆';
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
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#sub-tab-2">ÓRDENES DE COMPRA PENDIENTES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#sub-tab-3">ÓRDENES DE COMPRA PROCESADAS</a>
                    </li>
                </ul><br>
                <div class="tab-content">
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
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#sub-tab-5">ÓRDENES DE SERVICIO PENDIENTES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#sub-tab-6">ÓRDENES DE SERVICIO PROCESADAS</a>
                    </li>
                </ul><br>
                <div class="tab-content">
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
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#sub-tab-11">ÓRDENES DE PERSONAL PENDIENTES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#sub-tab-12">ÓRDENES DE PERSONAL PROCESADAS</a>
                    </li>
                </ul><br>
                <div class="tab-content">
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