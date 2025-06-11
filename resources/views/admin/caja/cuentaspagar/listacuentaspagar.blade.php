@extends('adminlte::page')

@section('content_header')
{{-- <a href="{{ route('reporte.cuentaspagar') }}" class="btn btn-danger">
    <i class="fas fa-file-pdf"></i> Generar PDF
</a> --}}
{{-- <a href="{{ route('reporte.cuentaspagar') }}" class="btn float-right btn-outline-secondary" style="margin-right: 10px;">REPORTE GENERAL</a> --}}
<a href="{{ route('reporte.cuentaspagar') }}" target="_blank" class="btn float-right btn-outline-secondary btn-sm">REPORTE GENERAL PROV. MEDICOS</a>
<h1>CUENTAS POR PAGAR</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/cuentascobrarpagar.css') }}">
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
                <form id="search-form" action="{{ route('buscarlistacuentaspagar') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="Nombre del Proveedor">
                    </div>
                    <button id="btn-buscar" class="btn btn-outline-secondary my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                    <button id="btn-mostrar-todo" class="btn btn-outline-secondary my-2 my-sm-0 ml-2" type="button">MOSTRAR TODO</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">  
            <li class="nav-item">
                <a class="nav-link active" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    CXP GENERAL
                </a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    CXP EN MORA
                </a>
            </li>  
            <li class="nav-item">
                <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    CXP PROVEEDORES MÉDICOS
                </a>
            </li>        
        </ul>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('a[data-toggle="tab"]').forEach(tab => {
                tab.addEventListener('click', function () {
                    localStorage.setItem('pestana_activa', this.getAttribute('href'));
                });
            });
            let pestana = localStorage.getItem('pestana_activa');
            if (pestana) {
                const tabElement = document.querySelector(`a[href="${pestana}"]`);
                if (tabElement) {
                    new bootstrap.Tab(tabElement).show();
                }
            }
        });
    </script>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            
            {{-- CUENTAS POR PAGAR PENDIENTES --}}
            <div class="tab-pane fade show active" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive" style="max-height: 70vh;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead style="position: sticky; top: 0; z-index: 1010; background-color: #ffffff;">
                                <tr>
                                    <th style="width: 5%;"><i class="fas fa-check"></i></th>
                                    <th style="width: 85%;">Fecha de C. Pagar</th>
                                    <th style="width: 10%;">C.Pagar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    use Illuminate\Support\Carbon;
                                    $cuentasPorFecha = $cuentaspagar->groupBy('fechaasignada');
                                    $bateriasPorFecha = $registrosbateria->groupBy('fechapago');
                                    $fechasUnicas = $cuentasPorFecha->keys()
                                        ->merge($bateriasPorFecha->keys())
                                        ->unique()
                                        ->sort();
                                    $proveedoresFuturas = collect();
                                    $proveedoresPasadas = collect();
                                    $hoy = Carbon::today();
                                    foreach ($fechasUnicas as $fecha) {
                                        $fechaCarbon = Carbon::parse($fecha);
                                        $cuentas = $cuentasPorFecha->get($fecha, collect());
                                        $baterias = $bateriasPorFecha->get($fecha, collect());
                                        $hayPendienteCuentas = $cuentas->contains(fn($item) => $item->estado !== 'PAGO PROCESADO' && $item->estadoaprobacion !== 'RECHAZADO');
                                        $hayPendienteBaterias = $baterias->contains(fn($item) => $item->prioridad === 'CUENTA POR PAGAR' && $item->estadoaprobacion !== 'RECHAZADO');
                                        if ($hayPendienteCuentas || $hayPendienteBaterias) {
                                            if ($fechaCarbon->lessThan($hoy)) {
                                                $proveedoresPasadas->put($fecha, $cuentas);
                                            } else {
                                                $proveedoresFuturas->put($fecha, $cuentas);
                                            }
                                        }
                                    }
                                    /* $proveedoresConPendientes = $proveedoresFuturas->merge($proveedoresPasadas); */
                                    $proveedoresConPendientes = $proveedoresFuturas;
                                @endphp
                                <style>
                                    .fondo-rojo-solo {
                                        background-color: #f8d7da !important;
                                    }
                                </style>
                                @foreach ($proveedoresConPendientes as $fechaasignada => $cuentas)
                                        @php
                                            $esPasada = \Illuminate\Support\Carbon::parse($fechaasignada)->lessThan(\Illuminate\Support\Carbon::today());
                                        @endphp
                                        <tr class="{{ $esPasada ? 'fondo-rojo-solo' : '' }}">
                                        <td><i class="fas fa-check"></i></td>
                                        <td>{{ $fechaasignada }}</td>
                                        <td>
                                            <abbr title="VER REGISTROS">
                                                <button type="button" class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modalProveedor{{ Str::slug($fechaasignada) }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </abbr>
                                            <div class="modal fade" id="modalProveedor{{ Str::slug($fechaasignada) }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog modal-xl" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header d-block text-center py-4" style="background: #efefef">
                                                            <div class="mb-3">
                                                                <h4 class="modal-title font-weight-bold text-wrap" style="font-size: 1.5rem; margin-bottom:10px;">
                                                                    <strong>CUENTAS POR PAGAR DEL: {{ $fechaasignada }}</strong>
                                                                </h4>
                                                                @php
                                                                    $documentos = $documentosPorFecha[$fechaasignada] ?? collect();
                                                                    $pagoTercero = $documentos->firstWhere('tipo', 'PAGO A TERCERO');
                                                                    $pagoInterbancario = $documentos->firstWhere('tipo', 'PAGO INTERBANCARIO');
                                                                    $documentosPagoTercero = $documentos->where('tipo', 'PAGO A TERCERO')->values();
                                                                    $documentosPagoInterbancario = $documentos->where('tipo', 'PAGO INTERBANCARIO')->values();
                                                                @endphp
                                                                @foreach ($documentosPagoTercero as $index => $pagoTercero)
                                                                    <a href="{{ asset('planillaspagosgeneradas/' . \Carbon\Carbon::parse($pagoTercero->fechapago)->format('Ymd') . '/' . $pagoTercero->documento) }}"
                                                                    target="_blank" class="btn btn-outline-secondary btn-sm">
                                                                        TXT PAGO A TERCERO {{ $index + 1 }}
                                                                    </a>
                                                                @endforeach
                                                                @foreach ($documentosPagoInterbancario as $index => $pagoInterbancario)
                                                                    <a href="{{ asset('planillaspagosgeneradas/' . \Carbon\Carbon::parse($pagoInterbancario->fechapago)->format('Ymd') . '/' . $pagoInterbancario->documento) }}"
                                                                    target="_blank" class="btn btn-outline-secondary btn-sm">
                                                                        TXT PAGO INTERBANCARIO {{ $index + 1 }}
                                                                    </a>
                                                                @endforeach
                                                                @if($pagoTercero || $pagoInterbancario)
                                                                    <form method="POST" action="{{ route('marcar.cargado') }}" class="d-inline">
                                                                        @csrf
                                                                        <input type="hidden" name="fecha" value="{{ $fechaasignada }}">
                                                                        {{-- <input type="hidden" name="nrobancoorigen" value="2505314878"> --}}
                                                                        <button type="submit" class="btn btn-sm btn-outline-success">MARCAR COMO CARGADO</button>
                                                                    </form>
                                                                @endif
                                                                <script>
                                                                    function enviarTelegram() {
                                                                        fetch("{{ route('informar.subida') }}", {
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
                                                                <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <ul class="nav nav-tabs" id="tabsProveedor{{ Str::slug($fechaasignada) }}">
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" data-toggle="tab" href="#pendientes{{ Str::slug($fechaasignada) }}">PAGOS PENDIENTES</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" data-toggle="tab" href="#finalizados{{ Str::slug($fechaasignada) }}">PAGOS PROCESADOS</a>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content mt-3">
                                                                <div id="pendientes{{ Str::slug($fechaasignada) }}" class="tab-pane fade show active">
                                                                    @can('admin.banco.index')
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
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @endcan
                                                                    <h4 style="font-weight:900;">CUENTAS POR PAGAR</h4>
                                                                    <input type="hidden" id="fechaSeleccionada{{ Str::slug($fechaasignada) }}" value="{{ $fechaasignada }}">
                                                                    @php
                                                                        $cuentasAgrupadas = $cuentas->whereIn('estado', ['PENDIENTE', 'SALDO PENDIENTE'])
                                                                                                    ->where('estadoaprobacion', '!=', 'RECHAZADO')
                                                                                                    ->groupBy('proveedornombre');
                                                                    @endphp
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered table-striped">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="width: 5%; background-color:#f6faee"><input type="checkbox" class="select-all-cuentas" data-fecha="{{ Str::slug($fechaasignada) }}">Aprob</th>
                                                                                    <th style="width: 5%; background-color:#f6faee">Ver</th>
                                                                                    <th style="width: 60%; background-color:#f6faee">Proveedor</th>
                                                                                    <th style="width: 20%; background-color:#f6faee">Tipo Planilla</th>
                                                                                    <th style="width: 10%; background-color:#f6faee" class="text-right">Monto Total</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($cuentasAgrupadas as $proveedor => $registros)
                                                                                @php
                                                                                    $todosAprobados = $registros->every(function($r) {
                                                                                        return in_array($r->estadoaprobacion, ['APROBADO', 'CARGADO', 'SUBIDO']);
                                                                                    });
                                                                                @endphp

                                                                                @php
                                                                                    $primerRegistro = $registros->first();
                                                                                    $tipoplanilla = optional($primerRegistro->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                                                    $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $proveedor)->where('fechapago', $fechaasignada)->first();
                                                                                    $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $proveedor)->first();
                                                                                @endphp
                                                                                    <tr class="font-weight-bold" style="background-color: #f7f7f7;">
                                                                                        <td>
                                                                                            @if ($todosAprobados)
                                                                                                <span class="badge badge-success">APROBADO</span>
                                                                                            @else
                                                                                                <input type="checkbox" class="select-group-cuentas" data-group="{{ Str::slug($proveedor, '-') }}" data-fecha="{{ Str::slug($fechaasignada) }}">
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>
                                                                                            <button class="btn btn-sm btn-botongris" type="button" 
                                                                                                    data-toggle="collapse" 
                                                                                                    data-target="#detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}"
                                                                                                    aria-expanded="false"
                                                                                                    aria-controls="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                                <i class="fas fa-eye"></i>
                                                                                            </button>
                                                                                        </td>
                                                                                        <td>{{ $proveedor }}</td>
                                                                                        <td>
                                                                                            {{ $tipoplanilla }}
                                                                                            @if ($tipoplanilla === 'PAGO QR')
                                                                                                @if ($archivoExistente)
                                                                                                    <a href="{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}" class="btn btn-botongris" target="_blank">
                                                                                                        <i class="fas fa-file"></i>
                                                                                                    </a>
                                                                                                @endif 
                                                                                                @if ($archivoExistentefijo && $archivoExistentefijo->imagenqr)
                                                                                                <a href="{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id .'/'. $archivoExistentefijo->imagenqr) }}" class="btn btn-botongris" target="_blank">
                                                                                                        <i class="fas fa-file"></i>
                                                                                                    </a>
                                                                                                @endif
                                                                                                @if (!$archivoExistente && !($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                                                    @php
                                                                                                        $inputId = 'imagen_qr_' . Str::slug($proveedor) . '_' . Str::slug($fechaasignada);
                                                                                                    @endphp
                                                                                                    <form action="{{ route('guardar.qr') }}" method="POST" enctype="multipart/form-data" style="display:inline;">
                                                                                                        @csrf
                                                                                                        <input type="hidden" name="proveedor" value="{{ $proveedor }}">
                                                                                                        <input type="hidden" name="fechapago" value="{{ $fechaasignada }}">
                                                                                                        <input type="file" id="{{ $inputId }}" name="imagen_qr" accept="image/*" style="display:none;" onchange="this.form.submit();">
                                                                                                        <label for="{{ $inputId }}" class="qr-upload-label" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #ccc; border-radius: 4px; text-align: center; cursor: pointer;">
                                                                                                            <i class="fas fa-upload" id="icon_{{ $inputId }}" style="font-size: 14px; line-height: 28px; color: #555;"></i>
                                                                                                        </label>
                                                                                                    </form>
                                                                                                @endif
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            {{ number_format($registros->sum('montototal'), 2) }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr class="collapse" id="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                        <td colspan="5" class="p-0">
                                                                                            <div class="table-responsive">
                                                                                                <table class="table table-sm">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th>Aprob.</th>
                                                                                                            <th hidden>ID Reg.</th>
                                                                                                            <th>Orden.ID</th>
                                                                                                            <th>Tipo Orden</th>
                                                                                                            <th>Detalle</th>
                                                                                                            <th>Fecha Pago</th>
                                                                                                            <th>N.Cta Origen</th>
                                                                                                            <th>Cant.</th>
                                                                                                            <th>Subto.</th>
                                                                                                            <th>Desc.</th>
                                                                                                            <th>Total</th>
                                                                                                            <th>Estado</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @foreach ($registros as $pendiente)
                                                                                                            <tr>
                                                                                                                <td>
                                                                                                                    @if (in_array($pendiente->estadoaprobacion, ['APROBADO', 'CARGADO']))
                                                                                                                        <span class="badge badge-success">APROBADO</span>
                                                                                                                    @else
                                                                                                                        <input type="checkbox" class="select-item-cuentas select-c-{{ Str::slug($proveedor, '-') }}" data-id="{{ $pendiente->id }}" data-total="{{ $pendiente->montototal }}" data-fecha="{{ $pendiente->fechaasignada }}" data-fecha-modal="{{ Str::slug($fechaasignada) }}" data-nrocuenta="{{ $pendiente->nrobancoorigen }}">
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                                <td hidden>{{ $pendiente->id }}</td>
                                                                                                                <td>{{ $pendiente->ordenid ?? 0 }}</td>
                                                                                                                <td>{{ $pendiente->tipoorden }}</td>
                                                                                                                <td>{{ $pendiente->detalleproducto }}</td>
                                                                                                                <td>{{ $pendiente->fechaasignada }}</td>
                                                                                                                <td>{{ $pendiente->nrobancoorigen ?? 0 }}</td>
                                                                                                                <td>{{ $pendiente->cantidad ?? 0 }}</td>
                                                                                                                <td>{{ $pendiente->subtotal }}</td>
                                                                                                                <td>{{ $pendiente->descuento }}</td>
                                                                                                                <td>{{ $pendiente->montototal }}</td>
                                                                                                                <td>
                                                                                                                    @if ($pendiente->estado == 'PENDIENTE')
                                                                                                                        <span class="badge badge-danger">{{ $pendiente->estado }}</span>
                                                                                                                    @else
                                                                                                                        <span class="badge badge-light">{{ $pendiente->estado }}</span>
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endforeach
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                                <div class="row justify-content-center" hidden> 
                                                                                    @foreach ($cuentasAgrupadas->flatten()->groupBy('nrobancoorigen') as $cuenta => $items)
                                                                                        <div class="col-md-4 mb-4">
                                                                                            <strong>TOTAL CxP APROBAR ({{ $cuenta }}):</strong> 
                                                                                            Bs.<span id="total-cuentas-{{ Str::slug($fechaasignada) }}-{{ $cuenta }}">0.00</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <br>
                                                                    <h4 style="font-weight:900;">PROVEEDORES MEDICOS</h4>
                                                                    <div class="table-responsive" style="overflow-x: auto;">
                                                                        <table class="table table-bordered table-striped">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="width: 5%; background-color:#f6faee"><input type="checkbox" class="select-all-programaciones" data-fecha="{{ Str::slug($fechaasignada) }}">Aprob</th>
                                                                                    <th style="width: 5%; background-color:#f6faee">Ver</th>
                                                                                    <th style="width: 60%; background-color:#f6faee">Proveedor</th>
                                                                                    <th style="width: 20%; background-color:#f6faee">Tipo Planilla</th>
                                                                                    <th style="width: 10%; background-color:#f6faee" class="text-right">Monto Total</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($registrosbateria->where('fechapago', $fechaasignada)->where('prioridad', 'CUENTA POR PAGAR')->where('estadoaprobacion','!=','RECHAZADO')->groupBy('proveedorasignado') as $proveedor => $registros)
                                                                                    @php
                                                                                        $todosAprobados = $registros->every(function($r) {
                                                                                            return in_array($r->estadoaprobacion, ['APROBADO', 'CARGADO', 'SUBIDO']);
                                                                                        });
                                                                                        $tipoplanilla = $proveedoresServicios[$proveedor] ?? 'NO DEFINIDO';
                                                                                        $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $proveedor)
                                                                                            ->where('fechapago', $fechaasignada)
                                                                                            ->first();
                                                                                        $archivoExistentefijo = \App\Models\Proveedor::where('proveedor', $proveedor)->first();
                                                                                    @endphp
                                                                                    <tr class="font-weight-bold" style="background-color: #f7f7f7;">
                                                                                        <td>
                                                                                            @if ($todosAprobados)
                                                                                                <span class="badge badge-success">APROBADO</span>
                                                                                            @else
                                                                                                <input type="checkbox" class="select-group-programaciones" data-group="{{ Str::slug($proveedor, '-') }}" data-fecha="{{ Str::slug($fechaasignada) }}">
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>
                                                                                            <button class="btn btn-sm btn-botongris" type="button" 
                                                                                                    data-toggle="collapse" 
                                                                                                    data-target="#detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}"
                                                                                                    aria-expanded="false"
                                                                                                    aria-controls="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                                <i class="fas fa-eye"></i>
                                                                                            </button>
                                                                                        </td>
                                                                                        <td>{{ $proveedor }}</td>
                                                                                        <td>
                                                                                            {{ $tipoplanilla }}
                                                                                            @if ($tipoplanilla === 'PAGO QR')
                                                                                                @if ($archivoExistente)
                                                                                                    <a href="{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}" class="btn btn-botongris" target="_blank">
                                                                                                        <i class="fas fa-file"></i>
                                                                                                    </a>
                                                                                                @endif 
                                                                                                @if ($archivoExistentefijo && $archivoExistentefijo->imagenqr)
                                                                                                <a href="{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id .'/'. $archivoExistentefijo->imagenqr) }}" class="btn btn-botongris" target="_blank">
                                                                                                        <i class="fas fa-file"></i>
                                                                                                    </a>
                                                                                                @endif
                                                                                                @if (!$archivoExistente && !($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                                                    @php
                                                                                                        $inputId = 'imagen_qr_' . Str::slug($proveedor) . '_' . Str::slug($fechaasignada);
                                                                                                    @endphp
                                                                                                    <form action="{{ route('guardar.qr') }}" method="POST" enctype="multipart/form-data" style="display:inline;">
                                                                                                        @csrf
                                                                                                        <input type="hidden" name="proveedor" value="{{ $proveedor }}">
                                                                                                        <input type="hidden" name="fechapago" value="{{ $fechaasignada }}">
                                                                                                        <input type="file" id="{{ $inputId }}" name="imagen_qr" accept="image/*" style="display:none;" onchange="this.form.submit();">
                                                                                                        <label for="{{ $inputId }}" class="qr-upload-label" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #ccc; border-radius: 4px; text-align: center; cursor: pointer;">
                                                                                                            <i class="fas fa-upload" id="icon_{{ $inputId }}" style="font-size: 14px; line-height: 28px; color: #555;"></i>
                                                                                                        </label>
                                                                                                    </form>
                                                                                                @endif
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            {{ number_format($registros->sum('preciocompra'), 2) }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr class="collapse" id="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                        <td colspan="5" class="p-0">
                                                                                            <div class="table-responsive" style="overflow-x: auto;">
                                                                                                <table class="table table-sm">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th>Aprob.</th>
                                                                                                            <th style="width: 10%;" hidden>ID Reg.</th>
                                                                                                            <th>Orden.ID</th>
                                                                                                            <th hidden>Cli_ID</th>
                                                                                                            <th>Cliente_Nombre</th>
                                                                                                            <th>Est./Esp.</th>
                                                                                                            <th>Fecha_Bateria</th>
                                                                                                            <th>Prog.</th>
                                                                                                            <th>Informe</th>
                                                                                                            <th>Fecha_Pago</th>
                                                                                                            <th>N.Cta_Origen</th>
                                                                                                            <th class="text-right" style="width: 10%;">Total</th>
                                                                                                            <th>Estado</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @foreach ($registros as $registro)
                                                                                                            <tr>
                                                                                                                <td>
                                                                                                                    @if (in_array($registro->estadoaprobacion, ['APROBADO', 'CARGADO']))
                                                                                                                        <span class="badge badge-success">APROBADO</span>
                                                                                                                    @else
                                                                                                                        <input type="checkbox" 
                                                                                                                            class="select-item-programaciones select-p-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}" 
                                                                                                                            data-id="{{ $registro->id }}" 
                                                                                                                            data-total="{{ $registro->preciocompra }}" 
                                                                                                                            data-fecha="{{ $registro->fechapago }}" 
                                                                                                                            data-fecha-modal="{{ Str::slug($fechaasignada) }}" 
                                                                                                                            data-nrocuenta="{{ $registro->nrobancoorigen }}">
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                                <td hidden>{{ $registro->id }}</td>
                                                                                                                <td>{{ $registro->ordenid }}</td>
                                                                                                                <td hidden>{{ $registro->clienteitaid }}{{ $registro->clienteauditoriaid }}{{ $registro->clientecomunid }}</td>
                                                                                                                <td>{{ $registro->clienteitanombre }}{{ $registro->clienteauditorianombre }}{{ $registro->clientecomunnombre }}</td>
                                                                                                                <td>{{ $registro->accionnombre }}</td>
                                                                                                                <td>{{ $registro->fechabateria }}</td>
                                                                                                                <td>
                                                                                                                    @if ($registro->accionnombre === 'INFORME FINAL')
                                                                                                                        {{ $registro->informe_created_at ? \Carbon\Carbon::parse($registro->informe_created_at)->format('Y-m-d') : 'PENDIENTE' }}
                                                                                                                    @else
                                                                                                                        {{ optional($registro->programacion)->fechaasignada ?? 'PENDIENTE' }}
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    @if ($registro->accionnombre === 'INFORME FINAL')
                                                                                                                        {{ $registro->informe_created_at ? \Carbon\Carbon::parse($registro->informe_created_at)->format('Y-m-d') : '' }}
                                                                                                                    @else
                                                                                                                        {{ optional(optional($registro->programacion)->documentacion)->created_at ? optional($registro->programacion)->documentacion->created_at->format('Y-m-d') : '' }}
                                                                                                                    @endif

                                                                                                                    @if ($registro->accionnombre === 'PSICOLOGIA' && $registro->clientecomunid !== null)
                                                                                                                        {{ optional($registro->programacion)->fechaasignada ?? '' }}
                                                                                                                    @endif

                                                                                                                    @if (
                                                                                                                        $registro->accionnombre === 'LAVADO DE OIDO DERECHO' || 
                                                                                                                        $registro->accionnombre === 'LAVADO DE OIDO IZQUIERDO'
                                                                                                                    )
                                                                                                                        {{ optional($registro->programacion)->fechaasignada ?? '' }}
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                                <td>{{ $registro->fechapago }}</td>
                                                                                                                <td>{{ $registro->nrobancoorigen }}</td>
                                                                                                                <td class="text-right" style="max-width: 120px; overflow-x: hidden;">{{ $registro->preciocompra }}</td>
                                                                                                                <td>
                                                                                                                    @if ($registro->prioridad == 'CUENTA POR PAGAR')
                                                                                                                        <span class="badge badge-danger">PENDIENTE</span>
                                                                                                                    @else
                                                                                                                        
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endforeach
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                                <div class="row justify-content-center" hidden> 
                                                                                    @foreach ($registrosbateria->flatten()->groupBy('nrobancoorigen') as $programacion => $items)
                                                                                        <div class="col-md-4 mb-4">
                                                                                            <strong>TOTAL CxP APROBAR ({{ $programacion }}):</strong> 
                                                                                            Bs.<span id="total-programaciones-{{ Str::slug($fechaasignada) }}-{{ $programacion }}">0.00</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    {{-- APROBAR SELECCIONADOS --}}
                                                                    <script>
                                                                        function aprobarSeleccionados() {
                                                                            const cuentasIds = Array.from(document.querySelectorAll('.select-item-cuentas:checked'))
                                                                                .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                            const programacionesIds = Array.from(document.querySelectorAll('.select-item-programaciones:checked'))
                                                                                .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                            const fechaSeleccionada = document.getElementById('fechaSeleccionada{{ Str::slug($fechaasignada) }}').value;

                                                                            // Crear formulario dinámico
                                                                            const form = document.createElement('form');
                                                                            form.method = 'POST';
                                                                            form.action = '{{ route('aprobar.registros') }}';

                                                                            // Agregar token CSRF
                                                                            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                                                                            const csrfInput = document.createElement('input');
                                                                            csrfInput.type = 'hidden';
                                                                            csrfInput.name = '_token';
                                                                            csrfInput.value = csrfToken;
                                                                            form.appendChild(csrfInput);

                                                                            // Agregar cuentas
                                                                            cuentasIds.forEach(id => {
                                                                                const input = document.createElement('input');
                                                                                input.type = 'hidden';
                                                                                input.name = 'cuentas[]';
                                                                                input.value = id;
                                                                                form.appendChild(input);
                                                                            });

                                                                            // Agregar programaciones
                                                                            programacionesIds.forEach(id => {
                                                                                const input = document.createElement('input');
                                                                                input.type = 'hidden';
                                                                                input.name = 'programaciones[]';
                                                                                input.value = id;
                                                                                form.appendChild(input);
                                                                            });

                                                                            // Agregar fecha
                                                                            const fechaInput = document.createElement('input');
                                                                            fechaInput.type = 'hidden';
                                                                            fechaInput.name = 'fecha';
                                                                            fechaInput.value = fechaSeleccionada;
                                                                            form.appendChild(fechaInput);

                                                                            document.body.appendChild(form);
                                                                            form.submit();
                                                                        }
                                                                    </script>

                                                                    {{-- RECHAZAR SELECCIONADOS --}}
                                                                    <script>
                                                                        function rechazarSeleccionados() {
                                                                            const cuentasIds = Array.from(document.querySelectorAll('.select-item-cuentas:checked'))
                                                                                .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                            const programacionesIds = Array.from(document.querySelectorAll('.select-item-programaciones:checked'))
                                                                                .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                            const fechaSeleccionada = document.getElementById('fechaSeleccionada{{ Str::slug($fechaasignada) }}').value;

                                                                            // Crear formulario dinámico
                                                                            const form = document.createElement('form');
                                                                            form.method = 'POST';
                                                                            form.action = '{{ route('rechazar.registros') }}';

                                                                            // Agregar token CSRF
                                                                            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                                                                            const csrfInput = document.createElement('input');
                                                                            csrfInput.type = 'hidden';
                                                                            csrfInput.name = '_token';
                                                                            csrfInput.value = csrfToken;
                                                                            form.appendChild(csrfInput);

                                                                            // Agregar cuentas
                                                                            cuentasIds.forEach(id => {
                                                                                const input = document.createElement('input');
                                                                                input.type = 'hidden';
                                                                                input.name = 'cuentas[]';
                                                                                input.value = id;
                                                                                form.appendChild(input);
                                                                            });

                                                                            // Agregar programaciones
                                                                            programacionesIds.forEach(id => {
                                                                                const input = document.createElement('input');
                                                                                input.type = 'hidden';
                                                                                input.name = 'programaciones[]';
                                                                                input.value = id;
                                                                                form.appendChild(input);
                                                                            });

                                                                            // Agregar fecha
                                                                            const fechaInput = document.createElement('input');
                                                                            fechaInput.type = 'hidden';
                                                                            fechaInput.name = 'fecha';
                                                                            fechaInput.value = fechaSeleccionada;
                                                                            form.appendChild(fechaInput);

                                                                            document.body.appendChild(form);
                                                                            form.submit();
                                                                        }
                                                                    </script>
                                                                </div>
                                                                <div id="finalizados{{ Str::slug($fechaasignada) }}" class="tab-pane fade">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-striped">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="background-color: white">ID Reg.</th>
                                                                                    <th style="background-color: white">Proveedor</th>
                                                                                    <th style="background-color: white">Tipo_Orden</th>
                                                                                    <th style="background-color: white">Orden ID</th>
                                                                                    <th style="background-color: white">Detalle</th>
                                                                                    <th style="background-color: white">Fecha_Pagar</th>
                                                                                    <th style="background-color: white">N.Cuenta_Origen</th>
                                                                                    <th style="background-color: white">Cant.</th>
                                                                                    <th style="background-color: white">Subto.</th>
                                                                                    <th style="background-color: white">Desc.</th>
                                                                                    <th style="background-color: white">Total</th>
                                                                                    <th style="background-color: white">Estado</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($cuentas->where('estado', 'PAGO PROCESADO') as $finalizado)
                                                                                <tr>
                                                                                    <td>{{ $finalizado->id }}</td>
                                                                                    <td>{{ $finalizado->proveedornombre }}</td>
                                                                                    <td>{{ $finalizado->tipoorden }}</td>
                                                                                    <td>{{ $finalizado->ordenid ?? 0 }}</td>
                                                                                    <td>{{ $finalizado->detalleproducto }}</td>
                                                                                    <td>{{ $finalizado->fechaasignada }}</td>
                                                                                    <td>{{ $finalizado->nrobancoorigen  ?? 0 }}</td>
                                                                                    <td>{{ $finalizado->cantidad ?? 0 }}</td>
                                                                                    <td>{{ $finalizado->subtotal }}</td>
                                                                                    <td>{{ $finalizado->descuento }}</td>
                                                                                    <td>{{ $finalizado->montototal }}</td>
                                                                                    <td>
                                                                                        @if ($finalizado->estado == 'PAGO PROCESADO')
                                                                                            <span class="badge badge-success">{{ $finalizado->estado }}</span>
                                                                                        @else
                                                                                            <span class="badge badge-danger">{{ $finalizado->estado }}</span>
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
                                                        <div class="modal-footer d-flex justify-content-between align-items-start w-100">
                                                            <div class="d-flex flex-column">
                                                                @foreach ($cuentasAgrupadas->flatten()->merge($registrosbateria->flatten())->groupBy('nrobancoorigen') as $cuenta => $items)
                                                                    <div class="mb-2">
                                                                        <strong>TOTAL APROBAR ({{ $cuenta }}):</strong>
                                                                            Bs.<span id="total-general-{{ Str::slug($fechaasignada) }}-{{ $cuenta }}">0.00</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <div class="d-flex flex-column align-items-end">
                                                                @can('admin.cuentaspagar.aprobarcxp')
                                                                    <button class="btn btn-sm btn-outline-success mb-2" onclick="aprobarSeleccionados()">APROBAR CXP</button>
                                                                    <button class="btn btn-sm btn-outline-danger mb-2" onclick="rechazarSeleccionados()">RECHAZAR CXP</button>
                                                                @endcan
                                                                <a type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">CERRAR</a>
                                                            </div>
                                                        </div>
                                                        <script>
                                                            function updateTotalByCuenta(fechaModal) {
                                                                // Reiniciar todos los totales visibles a 0.00
                                                                document.querySelectorAll(`span[id^="total-cuentas-${fechaModal}-"]`).forEach(span => {
                                                                    span.textContent = "0.00";
                                                                });

                                                                let totales = {};

                                                                document.querySelectorAll(`.select-item-cuentas[data-fecha-modal="${fechaModal}"]`).forEach(item => {
                                                                    if (item.checked) {
                                                                        let cuenta = item.getAttribute('data-nrocuenta') || '0';
                                                                        let total = parseFloat(item.getAttribute('data-total')) || 0;
                                                                        totales[cuenta] = (totales[cuenta] || 0) + total;
                                                                    }
                                                                });

                                                                for (let cuenta in totales) {
                                                                    let spanId = `total-cuentas-${fechaModal}-${cuenta}`;
                                                                    let span = document.getElementById(spanId);
                                                                    if (span) {
                                                                        span.textContent = totales[cuenta].toFixed(2);
                                                                    }
                                                                }
                                                                updateTotalGeneral(fechaModal); // 👈 al final
                                                            }
                                                            function updateTotalGeneral(fechaModal) {
                                                                document.querySelectorAll(`span[id^="total-general-${fechaModal}-"]`).forEach(span => {
                                                                    const idCuenta = span.id.replace(`total-general-${fechaModal}-`, '');
                                                                    const totalCuentas = parseFloat(document.getElementById(`total-cuentas-${fechaModal}-${idCuenta}`)?.textContent || '0') || 0;
                                                                    const totalProgramaciones = parseFloat(document.getElementById(`total-programaciones-${fechaModal}-${idCuenta}`)?.textContent || '0') || 0;
                                                                    const totalGeneral = totalCuentas + totalProgramaciones;
                                                                    span.textContent = totalGeneral.toFixed(2);
                                                                });
                                                            }


                                                            // Seleccionar todos
                                                            document.querySelectorAll('.select-all-cuentas').forEach(selectAll => {
                                                                selectAll.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    let checked = this.checked;
                                                                    document.querySelectorAll(`.select-item-cuentas[data-fecha-modal="${fechaModal}"], .select-group-cuentas[data-fecha="${fechaModal}"]`).forEach(cb => cb.checked = checked);
                                                                    updateTotalByCuenta(fechaModal);
                                                                });
                                                            });

                                                            // Grupo por proveedor
                                                            document.querySelectorAll('.select-group-cuentas').forEach(groupCheckbox => {
                                                                groupCheckbox.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    let group = this.dataset.group;
                                                                    document.querySelectorAll(`.select-c-${group}[data-fecha-modal="${fechaModal}"]`).forEach(cb => cb.checked = this.checked);
                                                                    updateTotalByCuenta(fechaModal);
                                                                });
                                                            });

                                                            // Ítem individual
                                                            document.querySelectorAll('.select-item-cuentas').forEach(item => {
                                                                item.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha-modal');
                                                                    updateTotalByCuenta(fechaModal);
                                                                });
                                                            });
                                                            
                                                            function updateTotalByCuenta2(fechaModal) {
                                                                // Reiniciar todos los totales visibles a 0.00
                                                                document.querySelectorAll(`span[id^="total-programaciones-${fechaModal}-"]`).forEach(span => {
                                                                    span.textContent = "0.00";
                                                                });

                                                                let totales = {};

                                                                document.querySelectorAll(`.select-item-programaciones[data-fecha-modal="${fechaModal}"]`).forEach(item => {
                                                                    if (item.checked) {
                                                                        let programacion = item.getAttribute('data-nrocuenta') || '0';
                                                                        let total = parseFloat(item.getAttribute('data-total')) || 0;
                                                                        totales[programacion] = (totales[programacion] || 0) + total;
                                                                    }
                                                                });

                                                                for (let programacion in totales) {
                                                                    let spanId = `total-programaciones-${fechaModal}-${programacion}`;
                                                                    let span = document.getElementById(spanId);
                                                                    if (span) {
                                                                        span.textContent = totales[programacion].toFixed(2);
                                                                    }
                                                                }
                                                                updateTotalGeneral(fechaModal);
                                                            }

                                                            document.querySelectorAll('.select-all-programaciones').forEach(selectAll => {
                                                                selectAll.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    const checked = this.checked;

                                                                    document.querySelectorAll(`.select-item-programaciones[data-fecha="${fechaModal}"], .select-group-programaciones[data-fecha="${fechaModal}"]`).forEach(cb => cb.checked = checked);
                                                                    updateTotalByCuenta2(fechaModal);
                                                                });
                                                            });

                                                            document.querySelectorAll('.select-group-programaciones').forEach(groupCheckbox => {
                                                                groupCheckbox.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    const group = this.dataset.group;

                                                                    document.querySelectorAll(`.select-p-${group}-${fechaModal}[data-fecha="${fechaModal}"]`).forEach(cb => cb.checked = this.checked);
                                                                    updateTotalByCuenta2(fechaModal);
                                                                });
                                                            });

                                                            document.querySelectorAll('.select-item-programaciones').forEach(item => {
                                                                item.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    updateTotalByCuenta2(fechaModal);
                                                                });
                                                            });

                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> 
                        </table>
                    </div>
                </div>
            </div>

            {{-- CUENTAS POR PAGAR EN MORA --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive" style="max-height: 70vh;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead style="position: sticky; top: 0; z-index: 1010; background-color: #ffffff;">
                                <tr>
                                    <th style="width: 5%;"><i class="fas fa-check"></i></th>
                                    <th style="width: 85%;">Fecha de C. Pagar</th>
                                    <th style="width: 10%;">C.Pagar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cuentasPorFecha = $cuentaspagar->groupBy('fechaasignada');
                                    $bateriasPorFecha = $registrosbateria->groupBy('fechapago');
                                    $fechasUnicas = $cuentasPorFecha->keys()
                                        ->merge($bateriasPorFecha->keys())
                                        ->unique()
                                        ->sort();

                                    $proveedoresFuturas = collect();
                                    $proveedoresPasadas = collect();
                                    $hoy = Carbon::today();

                                    foreach ($fechasUnicas as $fecha) {
                                        $fechaCarbon = Carbon::parse($fecha);
                                        $cuentas = $cuentasPorFecha->get($fecha, collect());
                                        $baterias = $bateriasPorFecha->get($fecha, collect());
                                        $hayPendienteCuentas = $cuentas->contains(fn($item) => $item->estado !== 'PAGO PROCESADO' && $item->estadoaprobacion === 'RECHAZADO');
                                        $hayPendienteBaterias = $baterias->contains(fn($item) => $item->prioridad === 'CUENTA POR PAGAR' && $item->estadoaprobacion === 'RECHAZADO');

                                        if ($hayPendienteCuentas || $hayPendienteBaterias) {
                                            if ($fechaCarbon->lessThanOrEqualTo($hoy)) {
                                                $proveedoresPasadas->put($fecha, $cuentas);
                                            } else {
                                                $proveedoresFuturas->put($fecha, $cuentas);
                                            }
                                        }
                                    }
                                    $proveedoresConPendientes = $proveedoresPasadas;
                                @endphp
                                <style>
                                    .fondo-rojo-solo {
                                        background-color: #f8d7da !important;
                                    }
                                </style>
                                @foreach ($proveedoresConPendientes as $fechaasignada => $cuentas)
                                        @php
                                            $esPasada = \Illuminate\Support\Carbon::parse($fechaasignada)->lessThan(\Illuminate\Support\Carbon::today());
                                        @endphp
                                        <tr class="{{ $esPasada ? 'fondo-rojo-solo' : '' }}">
                                        <td><i class="fas fa-check"></i></td>
                                        <td>{{ $fechaasignada }}</td>
                                        <td>
                                            <abbr title="VER REGISTROS">
                                                <button type="button" class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modalProveedor{{ Str::slug($fechaasignada) }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </abbr>
                                            <div class="modal fade" id="modalProveedor{{ Str::slug($fechaasignada) }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog modal-xl" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header d-block text-center py-4" style="background: #efefef">
                                                            <div class="mb-3">
                                                                <h4 class="modal-title font-weight-bold text-wrap" style="font-size: 1.5rem; margin-bottom:10px;">
                                                                    <strong>CUENTAS POR PAGAR DEL: {{ $fechaasignada }}</strong>
                                                                </h4>
                                                                @php
                                                                    $documentos = $documentosPorFecha[$fechaasignada] ?? collect();
                                                                    $pagoTercero = $documentos->firstWhere('tipo', 'PAGO A TERCERO');
                                                                    $pagoInterbancario = $documentos->firstWhere('tipo', 'PAGO INTERBANCARIO');
                                                                    $documentosPagoTercero = $documentos->where('tipo', 'PAGO A TERCERO')->values();
                                                                    $documentosPagoInterbancario = $documentos->where('tipo', 'PAGO INTERBANCARIO')->values();
                                                                @endphp
                                                                {{-- @foreach ($documentosPagoTercero as $index => $pagoTercero)
                                                                    <a href="{{ asset('planillaspagosgeneradas/' . \Carbon\Carbon::parse($pagoTercero->fechapago)->format('Ymd') . '/' . $pagoTercero->documento) }}"
                                                                    target="_blank" class="btn btn-outline-secondary btn-sm">
                                                                        TXT PAGO A TERCERO {{ $index + 1 }}
                                                                    </a>
                                                                @endforeach
                                                                @foreach ($documentosPagoInterbancario as $index => $pagoInterbancario)
                                                                    <a href="{{ asset('planillaspagosgeneradas/' . \Carbon\Carbon::parse($pagoInterbancario->fechapago)->format('Ymd') . '/' . $pagoInterbancario->documento) }}"
                                                                    target="_blank" class="btn btn-outline-secondary btn-sm">
                                                                        TXT PAGO INTERBANCARIO {{ $index + 1 }}
                                                                    </a>
                                                                @endforeach
                                                                <script>
                                                                    function enviarTelegram() {
                                                                        fetch("{{ route('informar.subida') }}", {
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
                                                                </script> --}}
                                                                <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <ul class="nav nav-tabs" id="tabsProveedor{{ Str::slug($fechaasignada) }}">
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" data-toggle="tab" href="#pendientes{{ Str::slug($fechaasignada) }}">PAGOS PENDIENTES</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" data-toggle="tab" href="#finalizados{{ Str::slug($fechaasignada) }}">PAGOS PROCESADOS</a>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content mt-3">
                                                                <div id="pendientes{{ Str::slug($fechaasignada) }}" class="tab-pane fade show active">
                                                                    <h4 style="font-weight:900;">CUENTAS POR PAGAR</h4>
                                                                    <input type="hidden" id="fechaSeleccionada{{ Str::slug($fechaasignada) }}" value="{{ $fechaasignada }}">
                                                                    @php
                                                                        $cuentasAgrupadas = $cuentas->whereIn('estado', ['PENDIENTE', 'SALDO PENDIENTE'])
                                                                                                    ->where('estadoaprobacion', '=', 'RECHAZADO')
                                                                                                    ->groupBy('proveedornombre');
                                                                    @endphp
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered table-striped">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="width: 5%; background-color:#f6faee"><input type="checkbox" class="select-all-cuentas" data-fecha="{{ Str::slug($fechaasignada) }}">Aprob</th>
                                                                                    <th style="width: 5%; background-color:#f6faee">Ver</th>
                                                                                    <th style="width: 60%; background-color:#f6faee">Proveedor</th>
                                                                                    <th style="width: 20%; background-color:#f6faee">Tipo Planilla</th>
                                                                                    <th style="width: 10%; background-color:#f6faee" class="text-right">Monto Total</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($cuentasAgrupadas as $proveedor => $registros)
                                                                                @php
                                                                                    $todosAprobados = $registros->every(function($r) {
                                                                                        return in_array($r->estadoaprobacion, ['APROBADO', 'CARGADO']);
                                                                                    });
                                                                                @endphp

                                                                                @php
                                                                                    $primerRegistro = $registros->first();
                                                                                    $tipoplanilla = optional($primerRegistro->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                                                    $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $proveedor)->where('fechapago', $fechaasignada)->first();
                                                                                @endphp
                                                                                    <tr class="font-weight-bold" style="background-color: #f7f7f7;">
                                                                                        <td>
                                                                                            @if ($todosAprobados)
                                                                                                <span class="badge badge-success">APROBADO</span>
                                                                                            @else
                                                                                                <input type="checkbox" class="select-group-cuentas" data-group="{{ Str::slug($proveedor, '-') }}" data-fecha="{{ Str::slug($fechaasignada) }}">
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>
                                                                                            <button class="btn btn-sm btn-botongris" type="button" 
                                                                                                    data-toggle="collapse" 
                                                                                                    data-target="#detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}"
                                                                                                    aria-expanded="false"
                                                                                                    aria-controls="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                                <i class="fas fa-eye"></i>
                                                                                            </button>
                                                                                        </td>
                                                                                        <td>{{ $proveedor }}</td>
                                                                                        <td>
                                                                                            {{ $tipoplanilla }}
                                                                                            @if ($tipoplanilla === 'PAGO QR')
                                                                                                @if ($archivoExistente)
                                                                                                    <a href="{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}" class="btn btn-botongris" target="_blank">
                                                                                                        <i class="fas fa-file"></i>
                                                                                                    </a>
                                                                                                @else
                                                                                                    @php
                                                                                                        $inputId = 'imagen_qr_' . Str::slug($proveedor) . '_' . Str::slug($fechaasignada);
                                                                                                    @endphp
                                                                                                    <form action="{{ route('guardar.qr') }}" method="POST" enctype="multipart/form-data" style="display:inline;">
                                                                                                        @csrf
                                                                                                        <input type="hidden" name="proveedor" value="{{ $proveedor }}">
                                                                                                        <input type="hidden" name="fechapago" value="{{ $fechaasignada }}">
                                                                                                        <input type="file" id="{{ $inputId }}" name="imagen_qr" accept="image/*" style="display:none;" onchange="this.form.submit();">
                                                                                                        <label for="{{ $inputId }}" class="qr-upload-label" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #ccc; border-radius: 4px; text-align: center; cursor: pointer;">
                                                                                                            <i class="fas fa-upload" id="icon_{{ $inputId }}" style="font-size: 14px; line-height: 28px; color: #555;"></i>
                                                                                                        </label>
                                                                                                    </form>
                                                                                                @endif
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            {{ number_format($registros->sum('montototal'), 2) }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr class="collapse" id="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                        <td colspan="5" class="p-0">
                                                                                            <div class="table-responsive">
                                                                                                <table class="table table-sm">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th>Aprob.</th>
                                                                                                            <th hidden>ID Reg.</th>
                                                                                                            <th>Orden.ID</th>
                                                                                                            <th>Tipo Orden</th>
                                                                                                            <th>Detalle</th>
                                                                                                            <th>Fecha Pago</th>
                                                                                                            <th>N.Cta Origen</th>
                                                                                                            <th>Cant.</th>
                                                                                                            <th>Subto.</th>
                                                                                                            <th>Desc.</th>
                                                                                                            <th>Total</th>
                                                                                                            <th>Estado</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @foreach ($registros as $pendiente)
                                                                                                            <tr>
                                                                                                                <td>
                                                                                                                    @if (in_array($pendiente->estadoaprobacion, ['APROBADO', 'CARGADO']))
                                                                                                                        <span class="badge badge-success">APROBADO</span>
                                                                                                                    @else
                                                                                                                        <input type="checkbox" class="select-item-cuentas select-c-{{ Str::slug($proveedor, '-') }}" data-id="{{ $pendiente->id }}" data-total="{{ $pendiente->montototal }}" data-fecha="{{ $pendiente->fechaasignada }}" data-fecha-modal="{{ Str::slug($fechaasignada) }}" data-nrocuenta="{{ $pendiente->nrobancoorigen }}">
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                                <td hidden>{{ $pendiente->id }}</td>
                                                                                                                <td>{{ $pendiente->ordenid ?? 0 }}</td>
                                                                                                                <td>{{ $pendiente->tipoorden }}</td>
                                                                                                                <td>{{ $pendiente->detalleproducto }}</td>
                                                                                                                <td>{{ $pendiente->fechaasignada }}</td>
                                                                                                                <td>{{ $pendiente->nrobancoorigen ?? 0 }}</td>
                                                                                                                <td>{{ $pendiente->cantidad ?? 0 }}</td>
                                                                                                                <td>{{ $pendiente->subtotal }}</td>
                                                                                                                <td>{{ $pendiente->descuento }}</td>
                                                                                                                <td>{{ $pendiente->montototal }}</td>
                                                                                                                <td>
                                                                                                                    @if ($pendiente->estado == 'PENDIENTE')
                                                                                                                        <span class="badge badge-danger">{{ $pendiente->estado }}</span>
                                                                                                                    @else
                                                                                                                        <span class="badge badge-light">{{ $pendiente->estado }}</span>
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endforeach
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                                <div class="row justify-content-center" hidden> 
                                                                                    @foreach ($cuentasAgrupadas->flatten()->groupBy('nrobancoorigen') as $cuenta => $items)
                                                                                        <div class="col-md-4 mb-4">
                                                                                            <strong>TOTAL CxP ({{ $cuenta }}):</strong> 
                                                                                            Bs.<span id="total-cuentas-{{ Str::slug($fechaasignada) }}-{{ $cuenta }}">0.00</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <br>
                                                                    <h4 style="font-weight:900;">PROVEEDORES MEDICOS</h4>
                                                                    <div class="table-responsive" style="overflow-x: auto;">
                                                                        <table class="table table-bordered table-striped">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="width: 5%; background-color:#f6faee"><input type="checkbox" class="select-all-programaciones" data-fecha="{{ Str::slug($fechaasignada) }}">Aprob</th>
                                                                                    <th style="width: 5%; background-color:#f6faee">Ver</th>
                                                                                    <th style="width: 60%; background-color:#f6faee">Proveedor</th>
                                                                                    <th style="width: 20%; background-color:#f6faee">Tipo Planilla</th>
                                                                                    <th style="width: 10%; background-color:#f6faee" class="text-right">Monto Total</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($registrosbateria->where('fechapago', $fechaasignada)->where('prioridad', 'CUENTA POR PAGAR')->where('estadoaprobacion','=','RECHAZADO')->groupBy('proveedorasignado') as $proveedor => $registros)
                                                                                    @php
                                                                                        $todosAprobados = $registros->every(function($r) {
                                                                                            return in_array($r->estadoaprobacion, ['APROBADO', 'CARGADO']);
                                                                                        });
                                                                                        $tipoplanilla = $proveedoresServicios[$proveedor] ?? 'NO DEFINIDO';
                                                                                        $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $proveedor)
                                                                                            ->where('fechapago', $fechaasignada)
                                                                                            ->first();
                                                                                    @endphp
                                                                                    <tr class="font-weight-bold" style="background-color: #f7f7f7;">
                                                                                        <td>
                                                                                            @if ($todosAprobados)
                                                                                                <span class="badge badge-success">APROBADO</span>
                                                                                            @else
                                                                                                <input type="checkbox" class="select-group-programaciones" data-group="{{ Str::slug($proveedor, '-') }}" data-fecha="{{ Str::slug($fechaasignada) }}">
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>
                                                                                            <button class="btn btn-sm btn-botongris" type="button" 
                                                                                                    data-toggle="collapse" 
                                                                                                    data-target="#detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}"
                                                                                                    aria-expanded="false"
                                                                                                    aria-controls="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                                <i class="fas fa-eye"></i>
                                                                                            </button>
                                                                                        </td>
                                                                                        <td>{{ $proveedor }}</td>
                                                                                        <td>
                                                                                            {{ $tipoplanilla }}
                                                                                            @if ($tipoplanilla === 'PAGO QR')
                                                                                                @if ($archivoExistente)
                                                                                                <a href="{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}" class="btn btn-botongris" target="_blank">
                                                                                                    <i class="fas fa-file"></i>
                                                                                                </a>
                                                                                                @else
                                                                                                    @php
                                                                                                        $inputId = 'imagen_qr_' . Str::slug($proveedor) . '_' . Str::slug($fechaasignada);
                                                                                                    @endphp
                                                                                                    <form action="{{ route('guardar.qr') }}" method="POST" enctype="multipart/form-data" style="display:inline;">
                                                                                                        @csrf
                                                                                                        <input type="hidden" name="proveedor" value="{{ $proveedor }}">
                                                                                                        <input type="hidden" name="fechapago" value="{{ $fechaasignada }}">
                                                                                                        <input type="file" id="{{ $inputId }}" name="imagen_qr" accept="image/*" style="display:none;" onchange="this.form.submit();">
                                                                                                        <label for="{{ $inputId }}" class="qr-upload-label" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #ccc; border-radius: 4px; text-align: center; cursor: pointer;">
                                                                                                            <i class="fas fa-upload" id="icon_{{ $inputId }}" style="font-size: 14px; line-height: 28px; color: #555;"></i>
                                                                                                        </label>
                                                                                                    </form>
                                                                                                @endif
                                                                                            @endif
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            {{ number_format($registros->sum('preciocompra'), 2) }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr class="collapse" id="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                        <td colspan="5" class="p-0">
                                                                                            <div class="table-responsive" style="overflow-x: auto;">
                                                                                                <table class="table table-sm">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th>Aprob.</th>
                                                                                                            <th style="width: 10%;" hidden>ID Reg.</th>
                                                                                                            <th>Orden.ID</th>
                                                                                                            <th hidden>Cli_ID</th>
                                                                                                            <th>Cliente_Nombre</th>
                                                                                                            <th>Est./Esp.</th>
                                                                                                            <th>Fecha_Bateria</th>
                                                                                                            <th>Prog.</th>
                                                                                                            <th>Informe</th>
                                                                                                            <th>Fecha_Pago</th>
                                                                                                            <th>N.Cta_Origen</th>
                                                                                                            <th class="text-right" style="width: 10%;">Total</th>
                                                                                                            <th>Estado</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @foreach ($registros as $registro)
                                                                                                            <tr>
                                                                                                                <td>
                                                                                                                    @if (in_array($registro->estadoaprobacion, ['APROBADO', 'CARGADO']))
                                                                                                                        <span class="badge badge-success">APROBADO</span>
                                                                                                                    @else
                                                                                                                        <input type="checkbox" 
                                                                                                                            class="select-item-programaciones select-p-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}" 
                                                                                                                            data-id="{{ $registro->id }}" 
                                                                                                                            data-total="{{ $registro->preciocompra }}" 
                                                                                                                            data-fecha="{{ $registro->fechapago }}" 
                                                                                                                            data-fecha-modal="{{ Str::slug($fechaasignada) }}" 
                                                                                                                            data-nrocuenta="{{ $registro->nrobancoorigen }}">
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                                <td hidden>{{ $registro->id }}</td>
                                                                                                                <td>{{ $registro->ordenid }}</td>
                                                                                                                <td hidden>{{ $registro->clienteitaid }}{{ $registro->clienteauditoriaid }}{{ $registro->clientecomunid }}</td>
                                                                                                                <td>{{ $registro->clienteitanombre }}{{ $registro->clienteauditorianombre }}{{ $registro->clientecomunnombre }}</td>
                                                                                                                <td>{{ $registro->accionnombre }}</td>
                                                                                                                <td>{{ $registro->fechabateria }}</td>
                                                                                                                <td>
                                                                                                                    @if ($registro->accionnombre === 'INFORME FINAL')
                                                                                                                        {{ $registro->informe_created_at ? \Carbon\Carbon::parse($registro->informe_created_at)->format('Y-m-d') : '' }}
                                                                                                                    @else
                                                                                                                        {{ optional($registro->programacion)->fechaasignada ?? '' }}
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    @if ($registro->accionnombre === 'INFORME FINAL')
                                                                                                                        {{ $registro->informe_created_at ? \Carbon\Carbon::parse($registro->informe_created_at)->format('Y-m-d') : '' }}
                                                                                                                    @else
                                                                                                                        {{ optional(optional($registro->programacion)->documentacion)->created_at ? optional($registro->programacion)->documentacion->created_at->format('Y-m-d') : '' }}
                                                                                                                    @endif

                                                                                                                    @if ($registro->accionnombre === 'PSICOLOGIA' && $registro->clientecomunid !== null)
                                                                                                                        {{ optional($registro->programacion)->fechaasignada ?? '' }}
                                                                                                                    @endif

                                                                                                                    @if (
                                                                                                                        $registro->accionnombre === 'LAVADO DE OIDO DERECHO' || 
                                                                                                                        $registro->accionnombre === 'LAVADO DE OIDO IZQUIERDO'
                                                                                                                    )
                                                                                                                        {{ optional($registro->programacion)->fechaasignada ?? '' }}
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                                <td>{{ $registro->fechapago }}</td>
                                                                                                                <td>{{ $registro->nrobancoorigen }}</td>
                                                                                                                <td class="text-right" style="max-width: 120px; overflow-x: hidden;">{{ $registro->preciocompra }}</td>
                                                                                                                <td>
                                                                                                                    @if ($registro->prioridad == 'CUENTA POR PAGAR')
                                                                                                                        <span class="badge badge-danger">PENDIENTE</span>
                                                                                                                    @else
                                                                                                                        
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endforeach
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                                <div class="row justify-content-center" hidden> 
                                                                                    @foreach ($registrosbateria->flatten()->groupBy('nrobancoorigen') as $programacion => $items)
                                                                                        <div class="col-md-4 mb-4">
                                                                                            <strong>TOTAL CxP ({{ $programacion }}):</strong> 
                                                                                            Bs.<span id="total-programaciones-{{ Str::slug($fechaasignada) }}-{{ $programacion }}">0.00</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div class="modal-footer d-flex justify-content-between align-items-start w-100">
                                                                        <div class="d-flex flex-column">
                                                                            @foreach ($cuentasAgrupadas->flatten()->merge($registrosbateria->flatten())->groupBy('nrobancoorigen') as $cuenta => $items)
                                                                                <div class="mb-2">
                                                                                    <strong>TOTAL ({{ $cuenta }}):</strong>
                                                                                        Bs.<span id="total-general-{{ Str::slug($fechaasignada) }}-{{ $cuenta }}">0.00</span>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <div class="d-flex flex-column align-items-end">
                                                                            @can('admin.cuentaspagar.aprobarcxp')
                                                                                <input type="date" name="fechapago" id="fechapago" class="form-control">
                                                                                <button class="btn btn-sm btn-outline-primary mb-2" onclick="cambiarfechaSeleccionados()">CAMBIAR FECHA</button>
                                                                            @endcan
                                                                            <a type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">CERRAR</a>
                                                                        </div>
                                                                    </div>
                                                                    <script>
                                                                        function cambiarfechaSeleccionados() {
                                                                            const cuentasIds = Array.from(document.querySelectorAll('.select-item-cuentas:checked'))
                                                                                .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                            const programacionesIds = Array.from(document.querySelectorAll('.select-item-programaciones:checked'))
                                                                                .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                            const fechaSeleccionada = document.getElementById('fechapago').value;

                                                                            // Crear formulario dinámico
                                                                            const form = document.createElement('form');
                                                                            form.method = 'POST';
                                                                            form.action = '{{ route('cambiarfecha.registros') }}';

                                                                            // Agregar token CSRF
                                                                            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                                                                            const csrfInput = document.createElement('input');
                                                                            csrfInput.type = 'hidden';
                                                                            csrfInput.name = '_token';
                                                                            csrfInput.value = csrfToken;
                                                                            form.appendChild(csrfInput);

                                                                            // Agregar cuentas
                                                                            cuentasIds.forEach(id => {
                                                                                const input = document.createElement('input');
                                                                                input.type = 'hidden';
                                                                                input.name = 'cuentas[]';
                                                                                input.value = id;
                                                                                form.appendChild(input);
                                                                            });

                                                                            // Agregar programaciones
                                                                            programacionesIds.forEach(id => {
                                                                                const input = document.createElement('input');
                                                                                input.type = 'hidden';
                                                                                input.name = 'programaciones[]';
                                                                                input.value = id;
                                                                                form.appendChild(input);
                                                                            });

                                                                            // Agregar fecha
                                                                            const fechaInput = document.createElement('input');
                                                                            fechaInput.type = 'hidden';
                                                                            fechaInput.name = 'fechapago';

                                                                            fechaInput.value = fechaSeleccionada;
                                                                            form.appendChild(fechaInput);

                                                                            document.body.appendChild(form);
                                                                            form.submit();
                                                                        }
                                                                    </script>
                                                                </div>
                                                                <div id="finalizados{{ Str::slug($fechaasignada) }}" class="tab-pane fade">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-striped">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="background-color: white">ID Reg.</th>
                                                                                    <th style="background-color: white">Proveedor</th>
                                                                                    <th style="background-color: white">Tipo_Orden</th>
                                                                                    <th style="background-color: white">Orden ID</th>
                                                                                    <th style="background-color: white">Detalle</th>
                                                                                    <th style="background-color: white">Fecha_Pagar</th>
                                                                                    <th style="background-color: white">N.Cuenta_Origen</th>
                                                                                    <th style="background-color: white">Cant.</th>
                                                                                    <th style="background-color: white">Subto.</th>
                                                                                    <th style="background-color: white">Desc.</th>
                                                                                    <th style="background-color: white">Total</th>
                                                                                    <th style="background-color: white">Estado</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($cuentas->where('estado', 'PAGO PROCESADO') as $finalizado)
                                                                                <tr>
                                                                                    <td>{{ $finalizado->id }}</td>
                                                                                    <td>{{ $finalizado->proveedornombre }}</td>
                                                                                    <td>{{ $finalizado->tipoorden }}</td>
                                                                                    <td>{{ $finalizado->ordenid ?? 0 }}</td>
                                                                                    <td>{{ $finalizado->detalleproducto }}</td>
                                                                                    <td>{{ $finalizado->fechaasignada }}</td>
                                                                                    <td>{{ $finalizado->nrobancoorigen  ?? 0 }}</td>
                                                                                    <td>{{ $finalizado->cantidad ?? 0 }}</td>
                                                                                    <td>{{ $finalizado->subtotal }}</td>
                                                                                    <td>{{ $finalizado->descuento }}</td>
                                                                                    <td>{{ $finalizado->montototal }}</td>
                                                                                    <td>
                                                                                        @if ($finalizado->estado == 'PAGO PROCESADO')
                                                                                            <span class="badge badge-success">{{ $finalizado->estado }}</span>
                                                                                        @else
                                                                                            <span class="badge badge-danger">{{ $finalizado->estado }}</span>
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
                                                        <script>
                                                            function updateTotalByCuenta3(fechaModal) {
                                                                // Reiniciar todos los totales visibles a 0.00
                                                                document.querySelectorAll(`span[id^="total-cuentas-${fechaModal}-"]`).forEach(span => {
                                                                    span.textContent = "0.00";
                                                                });

                                                                let totales = {};

                                                                document.querySelectorAll(`.select-item-cuentas[data-fecha-modal="${fechaModal}"]`).forEach(item => {
                                                                    if (item.checked) {
                                                                        let cuenta = item.getAttribute('data-nrocuenta') || '0';
                                                                        let total = parseFloat(item.getAttribute('data-total')) || 0;
                                                                        totales[cuenta] = (totales[cuenta] || 0) + total;
                                                                    }
                                                                });

                                                                for (let cuenta in totales) {
                                                                    let spanId = `total-cuentas-${fechaModal}-${cuenta}`;
                                                                    let span = document.getElementById(spanId);
                                                                    if (span) {
                                                                        span.textContent = totales[cuenta].toFixed(2);
                                                                    }
                                                                }
                                                                updateTotalGeneral3(fechaModal); // 👈 al final
                                                            }
                                                            function updateTotalGeneral3(fechaModal) {
                                                                document.querySelectorAll(`span[id^="total-general-${fechaModal}-"]`).forEach(span => {
                                                                    const idCuenta = span.id.replace(`total-general-${fechaModal}-`, '');
                                                                    const totalCuentas = parseFloat(document.getElementById(`total-cuentas-${fechaModal}-${idCuenta}`)?.textContent || '0') || 0;
                                                                    const totalProgramaciones = parseFloat(document.getElementById(`total-programaciones-${fechaModal}-${idCuenta}`)?.textContent || '0') || 0;
                                                                    const totalGeneral = totalCuentas + totalProgramaciones;
                                                                    span.textContent = totalGeneral.toFixed(2);
                                                                });
                                                            }


                                                            // Seleccionar todos
                                                            document.querySelectorAll('.select-all-cuentas').forEach(selectAll => {
                                                                selectAll.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    let checked = this.checked;
                                                                    document.querySelectorAll(`.select-item-cuentas[data-fecha-modal="${fechaModal}"], .select-group-cuentas[data-fecha="${fechaModal}"]`).forEach(cb => cb.checked = checked);
                                                                    updateTotalByCuenta3(fechaModal);
                                                                });
                                                            });

                                                            // Grupo por proveedor
                                                            document.querySelectorAll('.select-group-cuentas').forEach(groupCheckbox => {
                                                                groupCheckbox.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    let group = this.dataset.group;
                                                                    document.querySelectorAll(`.select-c-${group}[data-fecha-modal="${fechaModal}"]`).forEach(cb => cb.checked = this.checked);
                                                                    updateTotalByCuenta3(fechaModal);
                                                                });
                                                            });

                                                            // Ítem individual
                                                            document.querySelectorAll('.select-item-cuentas').forEach(item => {
                                                                item.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha-modal');
                                                                    updateTotalByCuenta3(fechaModal);
                                                                });
                                                            });
                                                            
                                                            function updateTotalByCuenta23(fechaModal) {
                                                                // Reiniciar todos los totales visibles a 0.00
                                                                document.querySelectorAll(`span[id^="total-programaciones-${fechaModal}-"]`).forEach(span => {
                                                                    span.textContent = "0.00";
                                                                });

                                                                let totales = {};

                                                                document.querySelectorAll(`.select-item-programaciones[data-fecha-modal="${fechaModal}"]`).forEach(item => {
                                                                    if (item.checked) {
                                                                        let programacion = item.getAttribute('data-nrocuenta') || '0';
                                                                        let total = parseFloat(item.getAttribute('data-total')) || 0;
                                                                        totales[programacion] = (totales[programacion] || 0) + total;
                                                                    }
                                                                });

                                                                for (let programacion in totales) {
                                                                    let spanId = `total-programaciones-${fechaModal}-${programacion}`;
                                                                    let span = document.getElementById(spanId);
                                                                    if (span) {
                                                                        span.textContent = totales[programacion].toFixed(2);
                                                                    }
                                                                }
                                                                updateTotalGeneral3(fechaModal);
                                                            }

                                                            document.querySelectorAll('.select-all-programaciones').forEach(selectAll => {
                                                                selectAll.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    const checked = this.checked;

                                                                    document.querySelectorAll(`.select-item-programaciones[data-fecha="${fechaModal}"], .select-group-programaciones[data-fecha="${fechaModal}"]`).forEach(cb => cb.checked = checked);
                                                                    updateTotalByCuenta23(fechaModal);
                                                                });
                                                            });

                                                            document.querySelectorAll('.select-group-programaciones').forEach(groupCheckbox => {
                                                                groupCheckbox.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    const group = this.dataset.group;

                                                                    document.querySelectorAll(`.select-p-${group}-${fechaModal}[data-fecha="${fechaModal}"]`).forEach(cb => cb.checked = this.checked);
                                                                    updateTotalByCuenta23(fechaModal);
                                                                });
                                                            });

                                                            document.querySelectorAll('.select-item-programaciones').forEach(item => {
                                                                item.addEventListener('change', function() {
                                                                    const fechaModal = this.getAttribute('data-fecha');
                                                                    updateTotalByCuenta23(fechaModal);
                                                                });
                                                            });

                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> 
                        </table>
                    </div>
                </div>
            </div>

            {{-- PROVEEDORES MEDICOS --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="table table-striped">
                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #ffffff;">
                            <tr>
                                <th style="width: 5%;"><i class="fas fa-check"></i></th>
                                <th style="width: 85%;">Proveedor Médico</th>
                                <th style="width: 10%;">C.Pagar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                                <tr>
                                    <td><i class="fas fa-check"></i></td>
                                    <td>{{ $item['proveedorasignado'] }}</td>
                                    <td>
                                        <abbr title="VER REGISTROS">
                                            <a class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
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

@foreach ($result as $item)
    <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true"> 
        <div class="modal-dialog modal-xxl" role="document"> 
            <div class="modal-content">
                @php 
                    $sumaPrecios = collect($item['acciones'])->sum(function ($accion) use ($item) {
                        return (!is_null($accion['informedocumentacion']) || !is_null($accion['informedocumentacionfinal'])) 
                            && is_numeric($accion['precio']) 
                            ? $accion['precio'] : 0;
                    });
                @endphp
                <div class="modal-header d-block text-center py-4" style="background: #efefef">
                    <div class="mb-3">
                        <h4 class="modal-title font-weight-bold" id="modalLabel{{ $loop->index }}">
                            <strong>{{ $item['proveedorasignado'] }}</strong>
                        </h4>
                        <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="tabs-{{ $loop->index }}">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-completos-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completos-{{ $loop->index }}" role="tab" aria-controls="tab-content-completos-{{ $loop->index }}" aria-selected="true">
                                INFORMES COMPLETOS Y PAGOS PENDIENTES
                            </a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link" id="tab-pendientes-{{ $loop->index }}" data-toggle="tab" href="#tab-content-pendientes-{{ $loop->index }}" role="tab" aria-controls="tab-content-pendientes-{{ $loop->index }}" aria-selected="false">
                                ATENDIDOS E INFORMES PENDIENTES
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-completosprocesados-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completosprocesados-{{ $loop->index }}" role="tab" aria-controls="tab-content-completosprocesados-{{ $loop->index }}" aria-selected="false">
                                INFORMES COMPLETOS Y PAGOS PROCESADOS
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="tabsContent-{{ $loop->index }}">
                        {{-- INFORMES COMPLETOS Y PAGOS PENDIENTES --}}
                        <div class="tab-pane fade show active" id="tab-content-completos-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completos-{{ $loop->index }}">
                            <form action="{{ route('actualizarFactura') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="action_type" id="action_type" value="guardar">
                                <div class="d-flex justify-content-end align-items-center mb-3">
                                    {{-- <div class="fw-bold me-3" style="white-space: nowrap; margin-right:10px;">
                                        Total: Bs. <span id="totalSeleccionados{{ $loop->index }}">0.00</span><span class="totalSeleccionados" data-modal-index="{{ $loop->index }}">0.00</span>
                                    </div> --}}
                                    {{-- <input type="text" class="form-control form-control-sm" id="filtroFactura{{ $loop->index }}" placeholder="Buscar Factura..." style="max-width: 180px; margin-right: 0.5rem;"> --}}
                                    {{-- <input type="text" class="form-control form-control-sm filtroFactura" data-modal-index="{{ $loop->index }}" placeholder="Buscar Factura..." style="max-width: 180px; margin-right: 0.5rem;"> --}}
                                    {{-- <button type="button" class="btn btn-sm btn-outline-primary" onclick="filtrarYCalcular({{ $loop->index }})">
                                        <i class="fas fa-search"></i>
                                    </button> --}}
                                    <div class="d-flex justify-content-end align-items-center mb-3">
                                        <div class="fw-bold me-3" style="white-space: nowrap; margin-right:10px;">
                                            Total: Bs. <span class="totalSeleccionados" data-tab="completos" data-index="{{ $loop->index }}">0.00</span>
                                        </div>
                                        <input type="text" class="form-control form-control-sm filtroFactura"
                                            data-tab="completos"
                                            data-index="{{ $loop->index }}"
                                            placeholder="Buscar Factura..."
                                            style="max-width: 180px; margin-right: 0.5rem;">
                                    </div>

                                </div>
                                {{-- <script>
                                    function filtrarYCalcular(modalIndex) {
                                        const filtro = document.getElementById('filtroFactura' + modalIndex);
                                        const tabla = document.getElementById('tab-content-completos-' + modalIndex);
                                        const totalSpan = document.getElementById('totalSeleccionados' + modalIndex);
                                        const texto = filtro.value.trim().toLowerCase();
                                        const filas = tabla.querySelectorAll('tbody tr');
                                        let total = 0;
                                        filas.forEach(fila => {
                                            const nroFactura = fila.children[13]?.innerText?.trim().toLowerCase() || '';

                                            if (texto === '') {
                                                fila.style.display = '';
                                            } else {
                                                const coincide = nroFactura === texto;
                                                fila.style.display = coincide ? '' : 'none';
                                                if (coincide) {
                                                    const precio = parseFloat(fila.children[8]?.innerText.replace(',', '.')) || 0;
                                                    total += precio;
                                                }
                                            }
                                        });
                                        totalSpan.innerText = texto === '' ? '0.00' : total.toFixed(2);
                                    }
                                    document.getElementById('filtroFactura{{ $loop->index }}').addEventListener('input', function () {
                                        if (this.value.trim() === '') {
                                            filtrarYCalcular({{ $loop->index }});
                                        }
                                    });
                                </script> --}}
                                <div class="table-responsive" style="max-height: 65vh;">
                                    <table class="table table-striped">
                                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                            <tr>
                                                <th class="text-center"><span style="color: black; font-size: 20px;">★</span></th>
                                                <th>ID</th>
                                                <th>Est./Esp.</th>
                                                <th>Tipo_Cli.</th>
                                                <th>Cliente_ID</th>
                                                <th>Cliente_Nombre</th>
                                                <th>Fecha_Bateria</th>
                                                <th>Servicio</th>
                                                <th>Pago</th>
                                                <th>Prog.</th>
                                                <th>Informe</th>
                                                <th>Fecha_Pago</th>
                                                <th hidden>ID Prog</th>
                                                <th>N.Factura</th>
                                                <th>
                                                    Fac{{-- <input type="checkbox" id="seleccionarTodos{{ $loop->index }}" class="seleccionarTodos" data-modal="{{ $loop->index }}"> --}}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $accionesOrdenadas = collect($item['acciones'])->sortBy(function($accion) {
                                                    if (!empty($accion['fechaprogramacion'])) {
                                                        return \Carbon\Carbon::parse($accion['fechaprogramacion']);
                                                    }
                                                    if (!empty($accion['informedocumentacionfinal'])) {
                                                        return \Carbon\Carbon::parse($accion['informedocumentacionfinal']);
                                                    }
                                                    return \Carbon\Carbon::now()->addYears(100);
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
                                                        <td class="text-center align-middle">
                                                            <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                                                @if ($accion['prioridad'] === 'CUENTA POR PAGAR' && $accion['estadoaprobacion'] !== 'RECHAZADO')
                                                                    <span style="color: #faa625; font-size: 20px;">★</span>
                                                                @elseif ($accion['prioridad'] === 'PRIORITARIO')
                                                                    <span style="color: #faa625; font-size: 20px;">★</span>
                                                                @else
                                                                    <input type="checkbox" class="check-prioridad" data-bateriaid="{{ $accion['id'] }}" style="transform: scale(1.0); margin: 0;">
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                        <td>
                                                            @if($accion['clienteitaid'] !== null)
                                                                ITA
                                                            @elseif($accion['clienteauditoriaid'] !== null)
                                                                AUDITORIA
                                                            @elseif($accion['clientecomunid'] !== null)
                                                                COMUN
                                                            @else
                                                                NINGUNO
                                                            @endif
                                                        </td>
                                                        <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                        <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
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
                                                        <td>
                                                            @if ($accion['pagoservicioinforme'] === 'SALDO PENDIENTE')
                                                                <div class="badge badge-warning">SALDO PENDIENTE</div>
                                                            @elseif ($accion['pagoservicioinforme'] && $accion['pagoservicioinforme'] !== 'PENDIENTE' && $accion['pagoservicioinforme'] !== 'PROCESADO')
                                                                <div class="badge badge-success">{{ $accion['pagoservicioinforme'] }}</div>
                                                            @else
                                                                <div class="badge badge-danger">PENDIENTE</div>
                                                            @endif
                                                        </td>

                                                        <td hidden>{{ $accion['idprogramacion'] }}</td>
                                                        <td>
                                                            @if (!empty($accion['documentofactura']))
                                                                <a href="{{ asset('comprobantescuentaspagar/' . $accion['documentofactura']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                            {{ $accion['nrofacturaprog'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" name="seleccionados[]" value="{{ $accion['idprogramacion'] }}" class="seleccionarFila" data-modal="{{ $loop->parent->index }}">
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endif

                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    @if (!is_null($accion['informedocumentacionfinal']) && 
                                                            !in_array($accion['pagoservicioinformefinal'], ['PROCESADO', 'PAGO PROCESADO']) 
                                                        && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $accion['pagoservicioinformefinal'] ?? ''))
                                                    <tr>
                                                        <td class="text-center align-middle">
                                                            <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                                                @if ($accion['prioridad'] === 'CUENTA POR PAGAR' && $accion['estadoaprobacion'] !== 'RECHAZADO')
                                                                    <span style="color: #faa625; font-size: 20px;">★</span>
                                                                @elseif ($accion['prioridad'] === 'PRIORITARIO')
                                                                    <span style="color: #faa625; font-size: 20px;">★</span>
                                                                @else
                                                                    <input type="checkbox" class="check-prioridad" data-bateriaid="{{ $accion['id'] }}" style="transform: scale(1.0); margin: 0;">
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                        <td>
                                                            @if($accion['clienteitaid'] !== null)
                                                                ITA
                                                            @elseif($accion['clienteauditoriaid'] !== null)
                                                                AUDITORIA
                                                            @elseif($accion['clientecomunid'] !== null)
                                                                COMUN
                                                            @else
                                                                NINGUNO
                                                            @endif
                                                        </td>
                                                        <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                        <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                                                        <td>{{ $accion['fechabateria'] }}</td>
                                                        <td>{{ $accion['tramiteinformefinal'] }}</td>
                                                        <td>{{ $accion['preciocompra'] }}</td>
                                                        <td>
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td>
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td>
                                                            @if ($accion['pagoservicioinformefinal'] === 'SALDO PENDIENTE')
                                                                <div class="badge badge-warning">SALDO PENDIENTE</div>
                                                            @elseif ($accion['pagoservicioinformefinal'] && $accion['pagoservicioinformefinal'] !== 'PENDIENTE' && $accion['pagoservicioinformefinal'] !== 'PROCESADO')
                                                                <div class="badge badge-success">{{ $accion['pagoservicioinformefinal'] }}</div>
                                                            @else
                                                                <div class="badge badge-danger">PENDIENTE</div>
                                                            @endif
                                                        </td>
                                                        <td hidden>{{ $accion['provinfofinalid'] }}</td>
                                                        <td>
                                                            @if (!empty($accion['facturainformefinal']))
                                                                <a href="{{ asset('comprobantescuentaspagar/' . $accion['facturainformefinal']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                            {{ $accion['nrofacturainformefinal'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" name="seleccionados[]" value="{{ $accion['provinfofinalid'] }}" class="seleccionarFila" data-modal="{{ $loop->parent->index }}">
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <div class="d-flex">
                                            <input type="file" name="documentofactura" id="documentofactura" accept=".pdf" style="max-width: 300px;"/>
                                            <input type="text" class="form-control me-2 form-control-sm" id="nroFactura" name="nroFactura" placeholder="Nro. Factura" style="max-width: 150px;">
                                            {{-- <button type="submit" class="btn btn-outline-secondary btn-sm" title="GUARDAR NRO. DE FACTURA"><i class="fas fa-save"></i></button> --}}
                                            <button type="submit" class="btn btn-outline-secondary btn-sm me-2" onclick="document.getElementById('action_type').value='guardar'" title="GUARDAR FACTURA">
                                                <i class="fas fa-save"></i> Guardar
                                            </button>

                                            @can('admin.cuentaspagar.anularfacturas')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('action_type').value='anular'" title="ANULAR FACTURA">
                                                <i class="fas fa-times-circle"></i> ANULAR
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- INFORMES PENDIENTES --}}
                        <div class="tab-pane fade" id="tab-content-pendientes-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-pendientes-{{ $loop->index }}">
                            <form action="{{ route('actualizarFactura') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="action_type" id="action_type2" value="guardar">
                                {{-- <div class="d-flex justify-content-end align-items-center mb-3">
                                    <div class="fw-bold me-3" style="white-space: nowrap; margin-right:10px;">
                                        Total: Bs. <span id="totalSeleccionados2{{ $loop->index }}">0.00</span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" id="filtroFactura2{{ $loop->index }}" placeholder="Buscar Factura..." style="max-width: 180px; margin-right: 0.5rem;">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="filtrarYCalcular2({{ $loop->index }})">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div> --}}
                                <div class="d-flex justify-content-end align-items-center mb-3">
                                    <div class="fw-bold me-3" style="white-space: nowrap; margin-right:10px;">
                                        Total: Bs. <span class="totalSeleccionados" data-tab="pendientes" data-index="{{ $loop->index }}">0.00</span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm filtroFactura"
                                        data-tab="pendientes"
                                        data-index="{{ $loop->index }}"
                                        placeholder="Buscar Factura..."
                                        style="max-width: 180px; margin-right: 0.5rem;">
                                </div>


                                {{-- <script>
                                    function filtrarYCalcular2(modalIndex) {
                                        const filtro = document.getElementById('filtroFactura2' + modalIndex);
                                        const tabla = document.getElementById('tab-content-pendientes-' + modalIndex);
                                        const totalSpan = document.getElementById('totalSeleccionados2' + modalIndex);

                                        const texto = filtro.value.trim().toLowerCase();
                                        const filas = tabla.querySelectorAll('tbody tr');
                                        let total = 0;

                                        filas.forEach(fila => {
                                            const nroFactura = fila.children[11]?.innerText?.trim().toLowerCase() || ''; // columna 11 = N.Factura

                                            if (texto === '') {
                                                fila.style.display = '';
                                            } else {
                                                const coincide = nroFactura === texto;
                                                fila.style.display = coincide ? '' : 'none';
                                                if (coincide) {
                                                    const precio = parseFloat(fila.children[7]?.innerText.replace(',', '.')) || 0; // columna 7 = Pago
                                                    total += precio;
                                                }
                                            }
                                        });
                                        totalSpan.innerText = texto === '' ? '0.00' : total.toFixed(2);
                                    }
                                    document.getElementById('filtroFactura2{{ $loop->index }}').addEventListener('input', function () {
                                        if (this.value.trim() === '') {
                                            filtrarYCalcular2({{ $loop->index }});
                                        }
                                    });
                                </script> --}}

                                <div class="table-responsive" style="max-height: 65vh;">
                                    <table class="table table-striped" id="tablaPendientes{{ $loop->index }}">
                                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                            <tr>
                                                <th>ID</th>
                                                <th>Est./Esp.</th>
                                                <th>Tipo_Cli.</th>
                                                <th>Cliente_ID</th>
                                                <th>Cliente_Nombre</th>
                                                <th>Fecha_Bateria</th>
                                                <th>Servicio</th>
                                                <th>Pago</th>
                                                <th>Prog.</th>
                                                <th>Informe</th>
                                                <th>Fecha_Pago</th>
                                                <th>N.Factura</th>
                                                <th>
                                                    Fac{{-- <input type="checkbox" id="seleccionarTodos2{{ $loop->index }}" class="seleccionarTodos" data-modal="{{ $loop->index }}"> --}}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $accionesOrdenadas = collect($item['acciones'])->sortBy(function($accion) {
                                                    if (!empty($accion['fechaprogramacion'])) {
                                                        return \Carbon\Carbon::parse($accion['fechaprogramacion']);
                                                    }
                                                    if (!empty($accion['informedocumentacionfinal'])) {
                                                        return \Carbon\Carbon::parse($accion['informedocumentacionfinal']);
                                                    }
                                                    return \Carbon\Carbon::now()->addYears(100);
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

                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    @if (is_null($accion['informedocumentacionfinal']) && is_null($accion['pagoservicioinformefinal'])) 
                                                    <tr>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                        <td>
                                                            @if($accion['clienteitaid'] !== null)
                                                                ITA
                                                            @elseif($accion['clienteauditoriaid'] !== null)
                                                                AUDITORIA
                                                            @elseif($accion['clientecomunid'] !== null)
                                                                COMUN
                                                            @else
                                                                NINGUNO
                                                            @endif
                                                        </td>
                                                        <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                        <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                                                        <td>{{ $accion['fechabateria'] }}</td>
                                                        <td>{{ $accion['tramiteinformefinal'] }}</td>
                                                        <td>{{ $accion['preciocompra'] }}</td>
                                                        <td>
                                                            {{ $accion['informedocumentacionfinal'] ?? '--------------' }}
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-danger">
                                                                {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if ($accion['pagoservicioinformefinal'])
                                                            <div class="badge badge-success">
                                                                {{ $accion['pagoservicioinformefinal'] }}
                                                            </div>
                                                            @else
                                                                <div class="badge badge-danger" >
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (!empty($accion['facturainformefinal']))
                                                                <a href="{{ asset('comprobantescuentaspagar/' . $accion['facturainformefinal']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                            {{ $accion['nrofacturainformefinal'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" name="seleccionados[]" value="{{ $accion['provinfofinalid'] }}" class="seleccionarFila2" data-modal="{{ $loop->parent->index }}">
                                                        </td>
                                                    </tr>
                                                    @endif
                                                @endif

                                                @if ($accion['accion'] !== 'INFORME FINAL')
                                                    @if (is_null($accion['informedocumentacion']) && !is_null($accion['fechaatencionprogramacion']) ) 
                                                        <tr>
                                                            <td>{{ $accion['id'] }}</td>
                                                            <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                            <td>
                                                                @if($accion['clienteitaid'] !== null)
                                                                    ITA
                                                                @elseif($accion['clienteauditoriaid'] !== null)
                                                                    AUDITORIA
                                                                @elseif($accion['clientecomunid'] !== null)
                                                                    COMUN
                                                                @else
                                                                    NINGUNO
                                                                @endif
                                                            </td>
                                                            <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                            <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
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
                                                                @if ($accion['informedocumentacion'])
                                                                    {{ $accion['informedocumentacion'] }}
                                                                @else
                                                                    <div class="badge 
                                                                        {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-danger' }}">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($accion['pagoservicioinforme'])
                                                                <div class="badge badge-danger">
                                                                    {{ $accion['pagoservicioinforme'] }}
                                                                </div>
                                                                @else
                                                                    <div class="badge badge-danger">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if (!empty($accion['documentofactura']))
                                                                    <a href="{{ asset('comprobantescuentaspagar/' . $accion['documentofactura']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                        <i class="fas fa-file-alt"></i>
                                                                    </a>
                                                                @endif
                                                                {{ $accion['nrofacturaprog'] ?? 'PENDIENTE' }}
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="seleccionados[]" value="{{ $accion['idprogramacion'] }}" class="seleccionarFila2" data-modal="{{ $loop->parent->index }}">
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row" style="margin-bottom: 10px;">
                                    <div class="col-12 d-flex justify-content-end">
                                        <div class="d-flex">
                                            <input type="file" name="documentofactura" id="documentofactura" accept=".pdf" style="max-width: 300px;"/>
                                            <input type="text" class="form-control me-2 form-control-sm" id="nroFactura" name="nroFactura" placeholder="Nro. Factura" style="max-width: 150px;">
                                            {{-- <button type="submit" class="btn btn-outline-secondary btn-sm" title="GUARDAR NRO. DE FACTURA"><i class="fas fa-save"></i></button> --}}
                                            <button type="submit" class="btn btn-outline-secondary btn-sm me-2" onclick="document.getElementById('action_type2').value='guardar'" title="GUARDAR FACTURA">
                                                <i class="fas fa-save"></i> Guardar
                                            </button>
                                            @can('admin.cuentaspagar.anularfacturas')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('action_type2').value='anular'" title="ANULAR FACTURA">
                                                <i class="fas fa-times-circle"></i> ANULAR
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- INFORMES COMPLETOS Y PAGOS PROCESADOS --}}
                        <div class="tab-pane fade" id="tab-content-completosprocesados-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completosprocesados-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est./Esp.</th>
                                            <th>Tipo Cli.</th>
                                            <th>Cliente_ID</th>
                                            <th>Cliente_Nombre</th>
                                            <th>Fecha_Bateria</th>
                                            <th>Servicio</th>
                                            <th>Pago</th>
                                            <th>Prog.</th>
                                            <th>Informe</th>
                                            <th>Fecha_Pago</th>
                                            <th>N.Factura</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $accionesOrdenadas = collect($item['acciones'])->sortBy(function($accion) {
                                                if (!empty($accion['fechaprogramacion'])) {
                                                    return \Carbon\Carbon::parse($accion['fechaprogramacion']);
                                                }
                                                if (!empty($accion['informedocumentacionfinal'])) {
                                                    return \Carbon\Carbon::parse($accion['informedocumentacionfinal']);
                                                }
                                                return \Carbon\Carbon::now()->addYears(100);
                                            });
                                        @endphp
                                        @foreach ($accionesOrdenadas as $accion)
                                        {{-- @foreach ($item['acciones'] as $accion)  --}}
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
                                                            ($accion['accion'] === 'PSICOLOGIA' && $accion['clientecomunid'] !== null)
                                                            || (!is_null($accion['informedocumentacion']))
                                                        ) &&
                                                        (
                                                            preg_match('/^\d{4}-\d{2}-\d{2}$/', $accion['pagoservicioinforme'] ?? '') ||
                                                            in_array($accion['pagoservicioinforme'], ['PROCESADO', 'PAGO PROCESADO'])
                                                        )
                                                    )
                                                    <tr>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                        <td>
                                                            @if($accion['clienteitaid'] !== null)
                                                                ITA
                                                            @elseif($accion['clienteauditoriaid'] !== null)
                                                                AUDITORIA
                                                            @elseif($accion['clientecomunid'] !== null)
                                                                COMUN
                                                            @else
                                                                NINGUNO
                                                            @endif
                                                        </td>
                                                        <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                        <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
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
                                                        <td>
                                                            @if ($accion['pagoservicioinforme'])
                                                            <div class="badge badge-success">
                                                                {{ $accion['pagoservicioinforme'] }}
                                                            </div>
                                                            @else
                                                                <div class="badge badge-danger">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $accion['nrofacturaprog'] ?? 'PENDIENTE' }}
                                                            @if (!empty($accion['documentofactura']))
                                                                <a href="{{ asset('comprobantescuentaspagar/' . $accion['documentofactura']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                            @if ($accion['accion'] === 'INFORME FINAL')
                                                @if (
                                                        !is_null($accion['informedocumentacionfinal']) &&
                                                        (
                                                            preg_match('/^\d{4}-\d{2}-\d{2}$/', $accion['pagoservicioinformefinal'] ?? '') ||
                                                            in_array(strtoupper($accion['pagoservicioinformefinal']), ['PROCESADO', 'PAGO PROCESADO'])
                                                        )
                                                    )
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td title="{{ $accion['accion'] }}" class="truncar">{{ $accion['accion'] }}</td>
                                                    <td>
                                                        @if($accion['clienteitaid'] !== null)
                                                            ITA
                                                        @elseif($accion['clienteauditoriaid'] !== null)
                                                            AUDITORIA
                                                        @elseif($accion['clientecomunid'] !== null)
                                                            COMUN
                                                        @else
                                                            NINGUNO
                                                        @endif
                                                    </td>
                                                    <td>{{ $accion['clienteitaid'] }}{{ $accion['clienteauditoriaid'] }}{{ $accion['clientecomunid'] }}</td>
                                                    <td title="{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}" class="truncar">{{ $accion['clienteitanombre'] }}{{ $accion['clienteauditorianombre'] }}{{ $accion['clientecomunnombre'] }}</td>
                                                    <td>{{ $accion['fechabateria'] }}</td>
                                                    <td>{{ $accion['tramiteinformefinal'] }}</td>
                                                    <td>{{ $accion['preciocompra'] }}</td>
                                                    <td>
                                                        {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                    </td>
                                                    <td>
                                                        {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinformefinal'] === 'SALDO PENDIENTE')
                                                            <div class="badge badge-warning">SALDO PENDIENTE</div>
                                                        @elseif ($accion['pagoservicioinformefinal'] && $accion['pagoservicioinformefinal'] !== 'PENDIENTE' && $accion['pagoservicioinformefinal'] !== 'PROCESADO')
                                                            <div class="badge badge-success">{{ $accion['pagoservicioinformefinal'] }}</div>
                                                        @else
                                                            <div class="badge badge-danger">PENDIENTE</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $accion['nrofacturainformefinal'] ?? 'PENDIENTE' }}
                                                        @if (!empty($accion['facturainformefinal']))
                                                            <a href="{{ asset('comprobantescuentaspagar/' . $accion['facturainformefinal']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                <i class="fas fa-file-alt"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button class="btn-priorizar btn btn-outline-success">PRIORIZAR</button>
                    {{-- <a type="button" class="btn btn-outline-danger" data-dismiss="modal">CERRAR</a> --}}
                </div>
            </div>
        </div>
    </div>
@endforeach
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.filtroFactura').forEach(input => {
            input.addEventListener('input', function () {
                const index = this.dataset.index;
                const tab = this.dataset.tab;

                const tabla = document.getElementById(`tab-content-${tab}-${index}`);
                const totalSpan = document.querySelector(`.totalSeleccionados[data-tab="${tab}"][data-index="${index}"]`);
                const texto = this.value.trim().toLowerCase();

                let total = 0;
                tabla.querySelectorAll('tbody tr').forEach(fila => {
                    // Diferente columna para cada tipo de pestaña
                    const nroFacturaCol = tab === 'completos' ? 13 : 11;
                    const montoCol = tab === 'completos' ? 8 : 7;

                    const nroFactura = fila.children[nroFacturaCol]?.innerText?.trim().toLowerCase() || '';

                    if (texto === '') {
                        fila.style.display = '';
                    } else {
                        const coincide = nroFactura === texto;
                        fila.style.display = coincide ? '' : 'none';
                        if (coincide) {
                            const precio = parseFloat(fila.children[montoCol]?.innerText.replace(',', '.')) || 0;
                            total += precio;
                        }
                    }
                });

                totalSpan.innerText = texto === '' ? '0.00' : total.toFixed(2);
            });
        });
    });
</script>


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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarlistacuentaspagar') }}";
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".seleccionarTodos").forEach(checkbox => {
                checkbox.addEventListener("change", function () {
                    let modalId = this.getAttribute("data-modal");
                    let checkboxes = document.querySelectorAll(`.seleccionarFila[data-modal="${modalId}"]`);
        
                    checkboxes.forEach(chk => {
                        chk.checked = checkbox.checked;
                    });
                });
            });
        
            document.querySelectorAll(".seleccionarFila").forEach(checkbox => {
                checkbox.addEventListener("change", function () {
                    let modalId = this.getAttribute("data-modal");
                    let allCheckboxes = document.querySelectorAll(`.seleccionarFila[data-modal="${modalId}"]`);
                    let selectAllCheckbox = document.querySelector(`#seleccionarTodos${modalId}`);
        
                    if (Array.from(allCheckboxes).every(chk => chk.checked)) {
                        selectAllCheckbox.checked = true;
                    } else {
                        selectAllCheckbox.checked = false;
                    }
                });
            });
        });
    </script>
    <script>
        document.querySelectorAll('.btn-priorizar').forEach(btn => {
        btn.addEventListener('click', function () {
            const modal = this.closest('.modal');
            const preordenesSeleccionadas = Array.from(
            modal.querySelectorAll('.check-prioridad:checked')
            ).map(cb => cb.dataset.bateriaid);

            if (preordenesSeleccionadas.length === 0) {
            alert('Seleccione al menos un registro para actualizar.');
            return;
            }

            fetch("{{ route('actualizar.prioridad.programacion') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ preordenes: preordenesSeleccionadas })
            })
            .then(r => r.json())
            .then(data => {
            alert(data.message);
            location.reload();
            })
            .catch(e => {
            console.error("Error al actualizar:", e);
            alert("Error al actualizar prioridad.");
            });
        });
        });
    </script>
    <script>
        function handleQRUpload(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon_' + inputId);

            if (input.files.length > 0) {
                icon.classList.remove('fa-upload');
                icon.classList.add('fa-check');
                icon.style.color = 'green';
            } else {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-upload');
                icon.style.color = '#555';
            }
        }
    </script>
@endsection

