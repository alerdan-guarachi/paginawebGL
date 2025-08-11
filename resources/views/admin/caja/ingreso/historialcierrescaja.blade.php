@extends('adminlte::page')

@section('content_header')
<a class="btn float-right btn-outline-secondary btn-sm" data-toggle="modal" data-target="#cierresModal">
    CIERRES DE CAJA
</a>
<div class="modal fade" id="cierresModal" tabindex="-1" aria-labelledby="cierresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header w-100 text-center">
                <h4 class="modal-title w-100 fw-bold" id="cierresModalLabel" style="font-weight: 900">CIERRES DE CAJA</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    @php
                        $authUser = auth()->user();
                        $esContable = $authUser->hasRole('CONTABLE');
                        $fechaFiltro = \Carbon\Carbon::create(2025, 7, 8); // 8 de julio de 2025
                    @endphp
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2" style="background-color: #ececec; text-align: center; vertical-align: middle; padding: 4px;">Usuario_Cierre</th>
                                <th rowspan="2" style="background-color: #ececec; text-align: center; vertical-align: middle; padding: 4px;">Fecha_Cierre</th>
                                <th colspan="6" style="background-color: #eaffde; text-align: center; vertical-align: middle; padding: 4px;">Ingresos</th>
                                <th colspan="3" style="background-color: #feebdb; text-align: center; vertical-align: middle; padding: 4px;">Egresos</th>
                                <th rowspan="2" style="background-color: #e3f1fd; text-align: center; vertical-align: middle; padding: 4px;">Neto</th>
                            </tr>
                            <tr>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Efectivo</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">ATC</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Depósito</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Transf.</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Cheque</th>
                                <th style="background-color: #eaffde; text-align: center; padding: 4px;">Total</th>
                                <th style="background-color: #feebdb; text-align: center; padding: 4px;">Transf.</th>
                                <th style="background-color: #feebdb; text-align: center; padding: 4px;">Cheque</th>
                                <th style="background-color: #feebdb; text-align: center; padding: 4px;">Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($cierrecajas as $cierrecaja)
                                @php
                                    $fechaCierre = \Carbon\Carbon::parse($cierrecaja->fechacierre ?? $cierrecaja->created_at);
                                @endphp

                                @if($fechaCierre >= $fechaFiltro && (!$esContable || ($esContable && $cierrecaja->usuariocierrenombre == $authUser->name)))
                                    <tr>
                                        <td style="background-color: #ffffff">{{ $cierrecaja->usuariocierrenombre }}</td>
                                        <td style="background-color: #ffffff; text-align: center;">{{\Carbon\Carbon::parse($cierrecaja->fechacierre ?? $cierrecaja->created_at)->format('Y-m-d')}}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierreefectivo }}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierreatc }}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierredeposito }}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierretransferencia }}</td>
                                        <td style="background-color: #fcfff5; text-align: right;">{{ $cierrecaja->cierrecheque }}</td>
                                        <td style="background-color: #fafafa; text-align: right;">
                                            {{
                                                number_format(
                                                    ($cierrecaja->cierreefectivo ?? 0)
                                                    + ($cierrecaja->cierreatc ?? 0)
                                                    + ($cierrecaja->cierredeposito ?? 0)
                                                    + ($cierrecaja->cierretransferencia ?? 0)
                                                    + ($cierrecaja->cierrecheque ?? 0),
                                                    2, '.', ','
                                                )
                                            }}
                                        </td>
                                        <td style="background-color: #fff9f5; text-align: right;">{{ $cierrecaja->egresotransferencia }}</td>
                                        <td style="background-color: #fff9f5; text-align: right;">{{ $cierrecaja->egresocheque }}</td>
                                        <td style="background-color: #fafafa; text-align: right;">
                                            {{
                                                number_format(
                                                    ($cierrecaja->egresotransferencia ?? 0)
                                                    + ($cierrecaja->egresocheque ?? 0),
                                                    2, '.', ','
                                                )
                                            }}
                                        </td>
                                        <td style="background-color: #f3f9ff; text-align: right;">
                                            {{
                                                number_format(
                                                    ($cierrecaja->cierreefectivo ?? 0)
                                                    + ($cierrecaja->cierreatc ?? 0)
                                                    + ($cierrecaja->cierredeposito ?? 0)
                                                    + ($cierrecaja->cierretransferencia ?? 0)
                                                    + ($cierrecaja->cierrecheque ?? 0)
                                                    - ($cierrecaja->egresotransferencia ?? 0)
                                                    - ($cierrecaja->egresocheque ?? 0),
                                                    2, '.', ','
                                                )
                                            }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-danger" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>

<h1 style="font-weight: 700;">HISTORIAL DE CIERRES DE CAJA</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
        .table td {
        padding: 5px 10px;
    }
    .btn-veringresos {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-veringresos:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
</style>
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
</style>
<style>
    .bg-light-green {
        background-color: #ebfff0 !important;
    }
    .bg-light-yellow {
        background-color: #fafadb !important;
    }
    .bg-light-purple {
        background-color: #fceaff !important;
    }
    h1, th, h5 {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
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
    <div class="card-body">
        <form method="GET" action="{{ route('admin.caja.ingreso.historialcierrescaja') }}" class="p-3 bg-light rounded shadow-sm">
            <div class="row align-items-center">
                <!-- Card Por Fecha -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="fecha_desde" class="form-label">Fecha Desde:</label>
                                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                                </div>
        
                                <div class="col-lg-6">
                                    <label for="fecha_hasta" class="form-label">Fecha Hasta:</label>
                                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- BUSCAR --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body d-flex justify-content-center align-items-center p-2">
                            <div class="card-header d-flex justify-content-center align-items-center p-2">
                                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i> BUSCAR</button>
                            </div>
                            @php
                                $idsMostrados = [];
                                $totalMonto = 0;
                                foreach($detalles as $detalle) {
                                    if(!in_array($detalle->id, $idsMostrados)) {
                                        $idsMostrados[] = $detalle->id;
                                        $totalMonto += $detalle->montototal - ($detalle->descuentoatc ?? 0);
                                    }
                                }
                            @endphp

                            <div class="bg-light border p-2 rounded text-center w-100">
                                <strong>Total Mov.:</strong>
                                <p class="m-0 total-mov">{{ number_format($totalMonto, 2) }}</p>
                                <input type="hidden" id="totalMontoHidden" name="totalMonto" value="{{ number_format($totalMonto, 2, '.', ',') }}">
                            </div>
                            <script>
                                document.getElementById('buscarBtn').addEventListener('click', function() {
                                    var totalMonto = document.querySelector('.total-mov').textContent.trim();
                                    totalMonto = totalMonto.replace(',', '').replace('.', ',');
                                    totalMonto = parseFloat(totalMonto).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    document.getElementById('totalMontoHidden').value = totalMonto;
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @php           
            $total = 0;
        @endphp
        <input type="hidden" id="totalMontoHidden" name="totalMonto" value="{{ number_format($total, 2, '.', ',') }}">

        <div id="estadoConsolidado" class="text-center w-100 p-2 rounded" style="display: none;">
            <strong id="estadoTexto"></strong>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        {{-- @if($detalles->isEmpty())
            <div class="alert alert-danger text-center" role="alert">
                <strong>No hay resultados para la búsqueda</strong>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Proveedor</th>
                            <th>Detalle</th>
                            <th>Area</th>
                            <th>Tipo_Mov.</th>
                            <th>Suc.Gasto</th>
                            <th>Transac.</th>
                            <th>Subto.</th>
                            <th>Desc.</th>
                            <th>Monto_Total</th>
                            <th>Dsc.ATC</th>
                            <th>Saldo</th>
                            <th>Recibo</th>
                            <th>Factura</th>
                            <th>Cuenta</th>
                            <th>Bancariz.</th>
                            <th>Nro.Cheque</th>
                            <th>Ciudad_Reg.</th>
                            <th>Usuario_Reg.</th>
                            <th>Fecha_Reg.</th>
                            <th>Fecha_Dep.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tbody> 
                            @php
                                $idsMostrados = [];
                                $totalMonto = 0;
                            @endphp
                            @foreach($detalles as $detalle)
                                @if(!in_array($detalle->id, $idsMostrados))
                                    @php
                                        $idsMostrados[] = $detalle->id;
                                        $montoFinal = $detalle->montototal - ($detalle->descuentoatc ?? 0);
                                        $totalMonto += $montoFinal;
                                    @endphp
                                <tr>
                                    <td>{{ $detalle->id }}</td>
                                    <td title="{{ $detalle->clientenombre }}" class="truncar">{{ $detalle->clientenombre ?? 0}}</td>
                                    <td title="{{ $detalle->proveedoratencion }}" class="truncar">{{ $detalle->proveedoratencion }}</td>
                                    <td title="{{ $detalle->detalle }}" class="truncar">{{ $detalle->detalle }}</td>
                                    <td>{{ $detalle->area }}</td>
                                    <td>{{ $detalle->tipomovimiento }}</td>
                                    <td>
                                        @if ($detalle->tipomovimiento === 'INGRESO')
                                            0
                                        @elseif ($detalle->tipomovimiento === 'EGRESO')
                                            @if ($detalle->sucursalgasto)
                                                {{ $detalle->sucursalgasto }}
                                            @else
                                                {{ $detalle->cajacentral_ciudadregistro }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td title="{{ $detalle->tipotransaccion }}" class="truncar">{{ $detalle->tipotransaccion }}</td>
                                    <td>{{ $detalle->subtotal }}</td>
                                    <td>{{ $detalle->descuento }}</td>
                                    <td>{{ $detalle->montototal }}</td>
                                    <td>{{ $detalle->descuentoatc ?? '0.00' }}</td>
                                    <td>{{ $detalle->saldo }}</td>
                                    <td>{{ $detalle->reciboid }}</td>
                                    <td>{{ $detalle->cajacentral_nrofactura ?? '---' }}</td>
                                    @if($detalle->tipotransaccion === 'EFECTIVO')
                                        <td>{{$detalle->cajacentral_nrobancodestinoefectivo ?? 'PENDIENTE' }}</td>
                                    @elseif($detalle->tipotransaccion === 'TRANSFERENCIA BANCARIA' || $detalle->tipotransaccion === 'RETIRO BANCARIO')
                                        <td>{{$detalle->cajacentral_nrocuentadestinotransferencia}}</td>
                                    @elseif($detalle->tipotransaccion === 'DEPOSITO BANCARIO')
                                        <td>{{$detalle->cajacentral_nrocuentadestinodeposito}}</td>
                                    @elseif($detalle->tipotransaccion === 'ATC')
                                        <td>{{$detalle->cajacentral_nrocuentadestinoatc}}</td>
                                    @elseif($detalle->tipotransaccion === 'CHEQUE')
                                        <td>3000189269</td>
                                    @else
                                        <td>-----------------</td>
                                    @endif
                                    @if($detalle->tipotransaccion === 'EFECTIVO')
                                        <td>{{$detalle->cajacentral_nrobancarizacionefectivo ?? '-----------------' }}</td>
                                    @elseif($detalle->tipotransaccion === 'TRANSFERENCIA BANCARIA' || $detalle->tipotransaccion === 'RETIRO BANCARIO')
                                        <td>{{$detalle->cajacentral_nrobancarizaciontransferencia ?? '-----------------' }}</td>
                                    @elseif($detalle->tipotransaccion === 'DEPOSITO BANCARIO')
                                        <td>{{$detalle->cajacentral_nrobancarizaciondeposito ?? '--------------' }}</td>
                                    @elseif($detalle->tipotransaccion === 'ATC')
                                        <td>{{$detalle->cajacentral_nrobancarizacionatc ?? '-----------------' }}</td>
                                    @elseif($detalle->tipotransaccion === 'CHEQUE')
                                        <td>{{$detalle->cajacentral_nrobancarizacioncheque ?? '-----------------' }}</td>
                                    @else
                                        <td>-----------------</td>
                                    @endif

                                    @if($detalle->tipotransaccion === 'CHEQUE')
                                        <td>{{$detalle->cajacentral_nrocheque ?? '-----------------' }}</td>
                                    @else
                                        <td>0</td>
                                    @endif

                                    <td>{{ $detalle->cajacentral_ciudadregistro }}</td>
                                    <td title="{{ $detalle->usuarioregistronombre }}" class="truncar">{{ $detalle->usuarioregistronombre }}</td>
                                    <td>{{ $detalle->created_at }}</td>
                                    @if($detalle->tipotransaccion === 'EFECTIVO')
                                        <td>{{ \Carbon\Carbon::parse($detalle->depositosbancarios_created_at)->format('Y-m-d') }}</td>
                                    @elseif($detalle->tipotransaccion === 'TRANSFERENCIA BANCARIA' || $detalle->tipotransaccion === 'DEPOSITO BANCARIO' || $detalle->tipotransaccion === 'RETIRO BANCARIO')
                                        <td>{{ \Carbon\Carbon::parse($detalle->created_at)->format('Y-m-d') }}</td>
                                    @elseif($detalle->tipotransaccion === 'ATC' && !empty($detalle->cajacentral_nrobancarizacionatc))
                                        <td>{{ \Carbon\Carbon::parse($detalle->cajacentral_fechabancarizacionatc)->format('Y-m-d') }}</td>
                                    @elseif($detalle->tipotransaccion === 'CHEQUE')
                                        <td>{{ \Carbon\Carbon::parse($detalle->cajacentral_updated_at)->format('Y-m-d') }}</td>
                                    @else
                                        <td>---------------</td>
                                    @endif
                                    <td>
                                        @if ($detalle->estado === 'PAGO PROCESADO')
                                            <span class="badge bg-success">{{ $detalle->estado }}</span>
                                        @elseif ($detalle->estado === 'SALDO PENDIENTE')
                                            <span class="badge bg-warning text-dark">{{ $detalle->estado }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $detalle->estado }}</span>
                                        @endif
                                    </td> 
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif    --}}
        
        @if($detalles->isEmpty())
            <div class="alert alert-danger text-center" role="alert">
                <strong>NO HAY RESULTADOS PARA LA BÚSQUEDA</strong>
            </div>
        @else

            @php
                $detallesAgrupados = $detalles->groupBy('usuarioregistronombre')->map(function ($porUsuario) {
                    return $porUsuario->groupBy('tipomovimiento')->map(function ($porTipoMov) {
                        return $porTipoMov->groupBy('tipotransaccion');
                    });
                });
            @endphp
            <style>
                .btn-toggle {
                    padding: 0.1rem 0.5rem;
                    font-size: 0.75rem;
                }
            </style>
            @php
                $authUser = auth()->user();
                $esContable = $authUser->hasRole('CONTABLE');
            @endphp
            <div class="row {{ $esContable ? '' : 'row-cols-1 row-cols-md-2' }} g-3">
                @foreach($detallesAgrupados as $usuario => $movimientos)
                    @if(!$esContable || ($esContable && $usuario == $authUser->name))
                    @php
                        $totalIngreso = isset($movimientos['INGRESO']) 
                            ? $movimientos['INGRESO']->reduce(function ($carry, $registros) {
                                return $carry + $registros->sum(function ($d) {
                                    $monto = $d->montototal;
                                    if (strtoupper($d->tipotransaccion) === 'ATC') {
                                        $monto -= ($d->descuentoatc ?? 0);
                                    }
                                    return $monto;
                                });
                            }, 0)
                            : 0;
                        $totalEgreso = isset($movimientos['EGRESO']) 
                            ? $movimientos['EGRESO']->reduce(function ($carry, $registros) {
                                return $carry + $registros->sum(function ($d) {
                                    $monto = $d->montototal;
                                    if (strtoupper($d->tipotransaccion) === 'ATC') {
                                        $monto -= ($d->descuentoatc ?? 0);
                                    }
                                    return $monto;
                                });
                            }, 0)
                            : 0;
                        $totalNeto = $totalIngreso - $totalEgreso;
                    @endphp

                    @php $totalUsuario = 0; @endphp
                    <div class="{{ $esContable ? 'col-12' : 'col' }}">

                        <div class="card border-dark shadow-sm h-100">
                            <div class="card-header bg-secondary text-white">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <strong>{{ $usuario }}</strong>
                                    <div class="ms-auto">
                                        <span class="badge bg-light text-dark fs-5 px-3 py-2" style="font-size: 13px;">Monto Neto: {{ number_format($totalNeto, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    @foreach(['INGRESO', 'EGRESO'] as $tipo)
                                        @if(isset($movimientos[$tipo]))
                                            @php $totalMovimiento = 0; @endphp
                                            <div class="col-12">
                                                @php 
                                                    $totalMovimiento = $movimientos[$tipo]->reduce(function($carry, $registros) {
                                                        return $carry + $registros->sum(function($d) {
                                                            $monto = $d->montototal;
                                                            if (strtoupper($d->tipotransaccion) === 'ATC') {
                                                                $monto -= ($d->descuentoatc ?? 0);
                                                            }
                                                            return $monto;
                                                        });
                                                    }, 0);
                                                @endphp

                                                <div class="card border border-secondary shadow-sm">
                                                    <div class="card-header bg-light d-flex align-items-center">
                                                        <div class="flex-grow-1">
                                                           <i class="fas fa-cash-register"></i> TIPO MOVIMIENTO: <strong>{{ $tipo }}</strong>
                                                        </div>
                                                        <div class="text-end" style="min-width: 120px; text-align: right;">
                                                            <strong>Total: {{ number_format($totalMovimiento, 2) }}</strong>
                                                        </div>
                                                    </div>
                                                    @php
                                                        // Agrupar por número de cuenta
                                                        $cuentasAgrupadas = collect();
                                                        
                                                        foreach ($movimientos[$tipo] as $transaccion => $regs) {
                                                            foreach ($regs as $d) {
                                                                // Obtener número de cuenta según tipo
                                                                $nroCuenta = match (strtoupper($d->tipotransaccion)) {
                                                                    'EFECTIVO' => $d->cajacentral_nrobancodestinoefectivo ?? 'PENDIENTE',
                                                                    'TRANSFERENCIA BANCARIA', 'RETIRO BANCARIO' => $d->cajacentral_nrocuentadestinotransferencia ?? '---',
                                                                    'DEPOSITO BANCARIO' => $d->cajacentral_nrocuentadestinodeposito ?? '---',
                                                                    'ATC' => $d->cajacentral_nrocuentadestinoatc ?? '---',
                                                                    'CHEQUE' => '3000189269',
                                                                    default => '-----------------',
                                                                };

                                                                $monto = $d->montototal - ($d->descuentoatc ?? 0);

                                                                // Agrupar y acumular
                                                                $cuentasAgrupadas[$nroCuenta] = ($cuentasAgrupadas[$nroCuenta] ?? 0) + $monto;
                                                            }
                                                        }
                                                    @endphp

                                                    @if($cuentasAgrupadas->count())
                                                        <div class="table-responsive mb-2">
                                                            <table class="table table-sm table-bordered mb-0" style="font-size: 13px;">
                                                                <tbody>
                                                                    @php 
                                                                        $rowspan = $cuentasAgrupadas->count(); 
                                                                        $first = true; 
                                                                        $colWidth = '160px'; // ancho uniforme
                                                                    @endphp
                                                                    @foreach($cuentasAgrupadas as $nroCuenta => $total)
                                                                        <tr>
                                                                            @if($first)
                                                                                <td class="py-1 px-2 text-center align-middle fw-bold bg-light" rowspan="{{ $rowspan }}" style="width: {{ $colWidth }};">
                                                                                    POR CUENTA BANCARIA
                                                                                </td>
                                                                                @php $first = false; @endphp
                                                                            @endif
                                                                            <td class="py-1 px-2" style="width: {{ $colWidth }}; text-align: right;">{{ $nroCuenta }}</td>
                                                                            <td class="py-1 px-2 text-end" style="width: {{ $colWidth }}; text-align: right;">{{ number_format($total, 2) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endif

                                                    <div class="card-body p-2">
                                                        @foreach($movimientos[$tipo] as $tipotransaccion => $registros)
                                                            @php
                                                                $idCollapse = \Str::slug($usuario . '-' . $tipo . '-' . $tipotransaccion);
                                                                $totalTransaccion = $registros->sum(fn($d) => $d->montototal - ($d->descuentoatc ?? 0));
                                                                $totalMovimiento += $totalTransaccion;
                                                            @endphp

                                                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-1">
                                                                <div>
                                                                   <i class="fas fa-check"></i> {{ $tipotransaccion }} — Total: <strong>{{ number_format($totalTransaccion, 2) }}</strong>
                                                                </div>
                                                                <a class="btn btn-outline-secondary btn-sm btn-toggle"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapse-{{ $idCollapse }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </div>
                                                            <div class="collapse mb-3" id="collapse-{{ $idCollapse }}">
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-striped table-bordered">
                                                                        <thead class="table-secondary">
                                                                            <tr>
                                                                                <th>Cierre_Caja</th>
                                                                                <th>ID</th>
                                                                                <th>Cliente</th>
                                                                                <th>Proveedor</th>
                                                                                <th>Detalle</th>
                                                                                <th>Area</th>
                                                                                <th>Tipo_Mov.</th>
                                                                                @if($registros->first()?->tipomovimiento === 'EGRESO')
                                                                                <th>Suc.Gasto</th>
                                                                                @endif
                                                                                <th>Transac.</th>
                                                                                <th>Subto.</th>
                                                                                <th>Desc.</th>
                                                                                <th>Monto_Total</th>
                                                                                @if($registros->first()?->tipotransaccion === 'ATC')
                                                                                    <th>Dsc.ATC</th>
                                                                                @endif
                                                                                <th>Saldo</th>
                                                                                <th>Recibo</th>
                                                                                <th>Factura</th>
                                                                                <th>Cuenta</th>
                                                                                <th>Bancariz.</th>
                                                                                @if($registros->first()?->tipotransaccion === 'CHEQUE')
                                                                                    <th>Nro.Cheque</th>
                                                                                @endif
                                                                                <th>Ciudad_Reg.</th>
                                                                                <th>Usuario_Reg.</th>
                                                                                <th>Fecha_Reg.</th>
                                                                                <th>Fecha_Dep.</th>
                                                                                <th>Estado</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($registros as $detalle)
                                                                                @php $neto = $detalle->montototal - ($detalle->descuentoatc ?? 0); @endphp
                                                                                <tr>
                                                                                    <td>
                                                                                        @if ($detalle->cajacentral_estadorevisioncierre !== 'FINALIZADO')
                                                                                            <span class="badge bg-danger">PENDIENTE</span>
                                                                                        @else
                                                                                            <span class="badge bg-success">CAJA CERRADA</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>{{ $detalle->id }}</td>
                                                                                    <td title="{{ $detalle->clientenombre }}" class="truncar">{{ $detalle->clientenombre ?? 0}}</td>
                                                                                    <td title="{{ $detalle->proveedoratencion }}" class="truncar">{{ $detalle->proveedoratencion }}</td>
                                                                                    <td title="{{ $detalle->detalle }}" class="truncar">{{ $detalle->detalle }}</td>
                                                                                    <td>{{ $detalle->area }}</td>
                                                                                    <td>{{ $detalle->tipomovimiento }}</td>
                                                                                    @if ($detalle->tipomovimiento === 'EGRESO')
                                                                                        <td>
                                                                                            @if ($detalle->sucursalgasto)
                                                                                                {{ $detalle->sucursalgasto }}
                                                                                            @else
                                                                                                {{ $detalle->cajacentral_ciudadregistro }}
                                                                                            @endif
                                                                                        </td>
                                                                                    @endif
                                                                                    <td title="{{ $detalle->tipotransaccion }}" class="truncar">{{ $detalle->tipotransaccion }}</td>
                                                                                    <td>{{ $detalle->subtotal }}</td>
                                                                                    <td>{{ $detalle->descuento }}</td>
                                                                                    <td>{{ $detalle->montototal }}</td>
                                                                                    @if($detalle->tipotransaccion === 'ATC')
                                                                                        <td>{{ $detalle->descuentoatc ?? '0.00' }}</td>
                                                                                    @endif
                                                                                    <td>{{ $detalle->saldo }}</td>
                                                                                    <td>{{ $detalle->reciboid }}</td>
                                                                                    <td>{{ $detalle->cajacentral_nrofactura ?? '---' }}</td>
                                                                                    @if($detalle->tipotransaccion === 'EFECTIVO')
                                                                                        <td>{{$detalle->cajacentral_nrobancodestinoefectivo ?? 'PENDIENTE' }}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'TRANSFERENCIA BANCARIA' || $detalle->tipotransaccion === 'RETIRO BANCARIO')
                                                                                        <td>{{$detalle->cajacentral_nrocuentadestinotransferencia}}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'DEPOSITO BANCARIO')
                                                                                        <td>{{$detalle->cajacentral_nrocuentadestinodeposito}}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'ATC')
                                                                                        <td>{{$detalle->cajacentral_nrocuentadestinoatc}}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'CHEQUE')
                                                                                        <td>3000189269</td>
                                                                                    @else
                                                                                        <td>-----------------</td>
                                                                                    @endif
                                                                                    @if($detalle->tipotransaccion === 'EFECTIVO')
                                                                                        <td>{{$detalle->cajacentral_nrobancarizacionefectivo ?? '-----------------' }}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'TRANSFERENCIA BANCARIA' || $detalle->tipotransaccion === 'RETIRO BANCARIO')
                                                                                        <td>{{$detalle->cajacentral_nrobancarizaciontransferencia ?? '-----------------' }}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'DEPOSITO BANCARIO')
                                                                                        <td>{{$detalle->cajacentral_nrobancarizaciondeposito ?? '--------------' }}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'ATC')
                                                                                        <td>{{$detalle->cajacentral_nrobancarizacionatc ?? '-----------------' }}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'CHEQUE')
                                                                                        <td>{{$detalle->cajacentral_nrobancarizacioncheque ?? '-----------------' }}</td>
                                                                                    @else
                                                                                        <td>-----------------</td>
                                                                                    @endif

                                                                                    @if($detalle->tipotransaccion === 'CHEQUE')
                                                                                        <td>{{$detalle->cajacentral_nrocheque ?? '-----------------' }}</td>
                                                                                    @endif

                                                                                    <td>{{ $detalle->cajacentral_ciudadregistro }}</td>
                                                                                    <td title="{{ $detalle->usuarioregistronombre }}" class="truncar">{{ $detalle->usuarioregistronombre }}</td>
                                                                                    <td>{{ $detalle->created_at }}</td>
                                                                                    @if($detalle->tipotransaccion === 'EFECTIVO')
                                                                                        <td>{{ \Carbon\Carbon::parse($detalle->depositosbancarios_created_at)->format('Y-m-d') }}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'TRANSFERENCIA BANCARIA' || $detalle->tipotransaccion === 'DEPOSITO BANCARIO' || $detalle->tipotransaccion === 'RETIRO BANCARIO')
                                                                                        <td>{{ \Carbon\Carbon::parse($detalle->created_at)->format('Y-m-d') }}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'ATC' && !empty($detalle->cajacentral_nrobancarizacionatc))
                                                                                        <td>{{ \Carbon\Carbon::parse($detalle->cajacentral_fechabancarizacionatc)->format('Y-m-d') }}</td>
                                                                                    @elseif($detalle->tipotransaccion === 'CHEQUE')
                                                                                        <td>{{ \Carbon\Carbon::parse($detalle->cajacentral_updated_at)->format('Y-m-d') }}</td>
                                                                                    @else
                                                                                        <td>---------------</td>
                                                                                    @endif
                                                                                    <td>
                                                                                        @if ($detalle->estado === 'PAGO PROCESADO')
                                                                                            <span class="badge bg-success">{{ $detalle->estado }}</span>
                                                                                        @elseif ($detalle->estado === 'SALDO PENDIENTE')
                                                                                            <span class="badge bg-warning text-dark">{{ $detalle->estado }}</span>
                                                                                        @else
                                                                                            <span class="badge bg-secondary">{{ $detalle->estado }}</span>
                                                                                        @endif
                                                                                    </td> 
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        @endif                 
    </div>
</div>
@stop

<!-- Total general por usuario -->
{{-- <div class="text-end mb-5 bg-light border rounded p-2">
    <strong>Total General de {{ $usuario }}:</strong> {{ number_format($totalUsuario, 2) }}
</div> --}}
@section('js')
{{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
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
@endsection