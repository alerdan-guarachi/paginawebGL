@extends('adminlte::page')

@section('content_header')
<h1>CUENTAS POR PAGAR PENDIENTES</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/cuentascobrarpagar.css') }}">
<style>
    .table td {
        padding: 5px 10px;
    }
    .btn-botongris {
        background-color: #676767;
        color: #ffffff;
        border-color: #676767;
        border-radius: 5px;
        padding: 1px 3px;
    }
    .btn-botongris:hover {
        background-color: #676767;
        color: #ffffff;
        }
    .btn-botonverde {
        background-color: #67bb2a;
        color: #ffffff;
        border-color: #67bb2a;
        border-radius: 5px;
        padding: 1px 3px;
    }
    .btn-botonverde:hover {
        background-color: #676767;
        color: #ffffff;
        }
    .btn-botongrisgrande {
        background-color: #ffffff;
        color: #676767;
        border-color: #676767;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-botongrisgrande:hover {
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
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- PENDIENTES --}}
            <div class="tab-pane fade show active" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                {{-- GERENCIA --}}
                <div class="card">
                    <div class="card-body" style="background-color: #f8f8f8">
                        <h6 style="font-size: 20px;"><strong>PAGOS QR Y EN LINEA DE LA CUENTA 3000189269</strong></h6>
                        <div class="table-responsive" style="margin-bottom: -10px;">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr style="background-color: #f5f5f5">
                                        <th style="width: 10%">ID Orden</th>
                                        <th style="width: 25%">Proveedor</th>
                                        <th style="width: 10%">Sigla</th>
                                        <th style="width: 15%">Tipo Planilla</th>
                                        <th style="width: 10%">Banco Origen</th>
                                        <th style="width: 10%">Banco Destino</th>
                                        <th style="width: 10%" class="text-right">Total</th>
                                        <th style="width: 10%" class="text-right" style="width: 200px;">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        @php
                            $cuentasFiltradas = $cuentaspagar->where('estadoaprobacion', 'APROBADO');
                            $agrupados = [];
                            foreach ($cuentasFiltradas as $item) {
                                $clave = $item->ordenid . '|' . $item->proveedornombre;
                                $agrupados[$clave][] = $item;
                            }
                            uksort($agrupados, function ($a, $b) use ($agrupados) {
                                $tipoplanillaA = optional(optional($agrupados[$a][0])->proveedorServicio)->tipoplanilla ?? 'ZZZ';
                                $tipoplanillaB = optional(optional($agrupados[$b][0])->proveedorServicio)->tipoplanilla ?? 'ZZZ';
                                return strcmp($tipoplanillaA, $tipoplanillaB);
                            });
                        @endphp
                        @foreach ($agrupados as $clave => $items)
                            @php
                                $filtrados = collect($items)->filter(function ($item) {
                                    $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                    return $item->nrobancoorigen === '3000189269' &&
                                        in_array($tipoplanilla, ['PAGO QR', 'PAGO EN LINEA']);
                                });
                            @endphp
                            @if ($filtrados->count() > 0)
                                @php
                                    [$ordenId, $proveedor] = explode('|', $clave);
                                    $total = $filtrados->sum('montototal');
                                    $idGrupo = 'grupo_' . md5($clave);

                                    //LOGICA PARA BANCO NACIONAL DE BOLIVIA
                                    $proveedorMostrar = $proveedor;
                                    $detallePrimerItem = $filtrados->first()->detalleproducto ?? '';

                                    if ($proveedor === 'BANCO NACIONAL DE BOLIVIA S.A.') {
                                        if (preg_match('/GARANTIA\ HIPOTECARIA (\d+)/i', $detallePrimerItem, $matches)) {
                                            $proveedorMostrar = $proveedor . ' - CREDITO ' . $matches[1];
                                        } elseif (preg_match('/VIVIENDA\ PYME (\d+)/i', $detallePrimerItem, $matches)) {
                                            $proveedorMostrar = $proveedor . ' - CREDITO ' . $matches[1];
                                        } elseif (preg_match('/CONSUMO\ ASALARIADO (\d+)/i', $detallePrimerItem, $matches)) {
                                            $proveedorMostrar = $proveedor . ' - CREDITO ' . $matches[1];
                                        }
                                    }

                                    $registroQR = $filtrados->first(function ($item) use ($proveedorMostrar) {
                                        $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? '';
                                        return $tipoplanilla === 'PAGO QR' &&
                                            \App\Models\PlanillasPagosGeneradas::where('proveedor', $proveedorMostrar)
                                                ->where('fechapago', $item->fechaasignada)
                                                ->exists();
                                    });

                                    $rutaQR = null;
                                    if ($registroQR) {
                                        $archivo = \App\Models\PlanillasPagosGeneradas::where('proveedor', $proveedorMostrar)
                                            ->where('fechapago', $registroQR->fechaasignada)
                                            ->first();
                                        if ($archivo) {
                                            $rutaQR = asset('planillaspagosgeneradas/' . str_replace('-', '', $archivo->fechapago) . '/' . $archivo->documento);
                                        }
                                    }

                                    //RUTA QR FIJA DEL PROVEEDOR
                                    $rutaQR2 = null;
                                    $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $proveedor)->first();
                                    if ($archivoExistentefijo && $archivoExistentefijo->imagenqr) {
                                        $rutaQR2 = asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr);
                                    }
                                @endphp

                                @php
                                    $primerItem = $items[0];
                                    $proveedorServicio = \App\Models\ProveedoresServicios::where('razonsocial', $primerItem->proveedornombre)->first();
                                    $tipoplanilla = $proveedorServicio->tipoplanilla ?? 'NO DEFINIDO';
                                    $numcuenta = $proveedorServicio->numcuenta ?? 'NO DEFINIDO';
                                    $nrobancoorigen = $primerItem->nrobancoorigen ?? 'NO DEFINIDO';

                                    $detalleLimpio = strtoupper(preg_replace('/\s+/', '', $filtrados->first()->detalleproducto ?? ''));

                                    $siglasItem = \App\Models\PlanesServiciosProv::where('razonsocial', $proveedor)
                                        ->get()
                                        ->filter(function ($registro) use ($detalleLimpio) {
                                            $codigoValido = $registro->codigo == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->codigo));
                                            $contratoValido = $registro->contrato == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->contrato));
                                            $lineaValida = $registro->linea == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->linea));
                                            $cuentaValida = $registro->cuenta == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->cuenta));
                                            $servicioValido = $registro->servicio == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->servicio));
                                            return $codigoValido && $contratoValido && $lineaValida && $cuentaValida && $servicioValido;
                                        })
                                        ->pluck('sigla')
                                        ->unique()
                                        ->values()
                                        ->all();

                                    $provsigla = count($siglasItem) > 0 ? implode(', ', $siglasItem) : '0';

                                @endphp
                                
                                <div class="mb-1 border rounded">
                                    <div class="table-responsive">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <table class="table table-sm table-bordered mb-0 w-100">
                                                <thead>
                                                    <tr style="background-color: #fffdf0">
                                                        <th style="width: 10%; font-size: 14px;">{{ $ordenId }}</th>
                                                        <th style="width: 25%; font-size: 14px;">{{ $proveedorMostrar }}</th>

                                                        <th style="width: 10%; font-size: 14px;">{{ $provsigla }}</th>
                                                        <th style="width: 15%; font-size: 14px;">{{ $tipoplanilla }}</th>
                                                        <th style="width: 10%; font-size: 14px;">{{ $nrobancoorigen }}</th>
                                                        <th style="width: 10%; font-size: 14px;">{{ $numcuenta }}</th>
                                                        <th style="width: 10%; font-size: 14px;" class="text-right">{{ number_format($total, 2) }}</th>
                                                        <th style="width: 10%; font-size: 14px;" class="text-right" style="width: 200px;">
                                                            @if ($rutaQR)
                                                                <a class="btn btn-sm btn-botonverde" onclick="mostrarQR('{{ $rutaQR }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            @if ($rutaQR2)
                                                                <a class="btn btn-sm btn-botonverde" onclick="mostrarQR('{{ $rutaQR2 }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            <a class="btn btn-sm btn-botongris" title="VER DETALLES" onclick="document.getElementById('{{ $idGrupo }}').classList.toggle('d-none')">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mt-2 d-none" id="{{ $idGrupo }}">
                                            <thead>
                                                <tr>
                                                    <th>Selec.</th>
                                                    <th>Tipo_Orden</th>
                                                    <th>Sigla</th>
                                                    <th>Detalle</th>
                                                    <th>Fecha_Pago</th>
                                                    <th>Tipo_Planilla</th>
                                                    <th>Banco_Origen</th>
                                                    <th>Banco_Destino</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($filtrados as $item)
                                                    @php
                                                        $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                        $numcuenta = $proveedorServicio->numcuenta ?? 'NO DEFINIDO';

                                                        $detalleLimpio = strtoupper(preg_replace('/\s+/', '', $item->detalleproducto));
                                                        $siglasItem = \App\Models\PlanesServiciosProv::where('razonsocial', $item->proveedornombre)
                                                            ->get()
                                                            ->filter(function ($registro) use ($detalleLimpio) {
                                                                $codigoValido = $registro->codigo == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->codigo));
                                                                $contratoValido = $registro->contrato == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->contrato));
                                                                $lineaValida = $registro->linea == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->linea));
                                                                $cuentaValida = $registro->cuenta == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->cuenta));
                                                                $servicioValido = $registro->servicio == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->servicio));
                                                                return $codigoValido && $contratoValido && $lineaValida && $cuentaValida && $servicioValido;
                                                            })
                                                            ->pluck('sigla')
                                                            ->unique()
                                                            ->values()
                                                            ->all();
                                                        $provsigla = count($siglasItem) > 0 ? implode(', ', $siglasItem) : '0';
                                                    @endphp
                                                    <tr>
                                                        <td><input type="checkbox" class="checkbox-cuentas" value="{{ $item->id }}"></td>
                                                        <td>{{ $item->tipoorden }}</td>
                                                        <td>{{ $provsigla }}</td>
                                                        <td>{{ $item->detalleproducto }}</td>
                                                        <td>{{ $item->fechaasignada }}</td>
                                                        <td>{{ $tipoplanilla }}</td>
                                                        <td>{{ $item->nrobancoorigen }}</td>
                                                        <td>{{ $numcuenta }}</td>
                                                        <td class="text-right">{{ number_format($item->montototal, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @php
                            $bateriaFiltrada = $registrosbateria->where('estadoaprobacion', 'APROBADO');
                            $gruposBateria = [];
                            foreach ($bateriaFiltrada as $item) {
                                $clave = $item->ordenid . '|' . $item->proveedorasignado;
                                $gruposBateria[$clave][] = $item;
                            }
                        @endphp
                        @foreach ($gruposBateria as $clave => $items)
                            @php
                                $filtrados = collect($items)->filter(function ($item) use ($proveedoresServicios) {
                                    $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                    return $item->nrobancoorigen === '3000189269' &&
                                        in_array($tipoplanilla, ['PAGO QR', 'PAGO EN LINEA']);
                                });
                            @endphp
                            @if ($filtrados->count() > 0)
                                @php
                                    [$ordenId, $proveedor] = explode('|', $clave);
                                    $total = $filtrados->sum('preciocompra');
                                    $idGrupo = 'grupo_bat_' . md5($clave);
                                    $registroQR = $filtrados->first(function ($item) use ($proveedoresServicios) {
                                        $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                        return $tipoplanilla === 'PAGO QR' &&
                                            \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedorasignado)
                                                ->where('fechapago', $item->fechapago)
                                                ->exists();
                                    });
                                    $rutaQR = null;
                                    if ($registroQR) {
                                        $archivo = \App\Models\PlanillasPagosGeneradas::where('proveedor', $registroQR->proveedornombre)
                                            ->where('fechapago', $registroQR->fechaasignada)
                                            ->first();
                                        if ($archivo) {
                                            $rutaQR = asset('planillaspagosgeneradas/' . str_replace('-', '', $archivo->fechapago) . '/' . $archivo->documento);
                                        } 
                                    }

                                    $rutaQR2 = null;
                                    $archivoExistentefijo = \App\Models\Proveedor::where('proveedor', $proveedor)->first();
                                    if ($archivoExistentefijo && $archivoExistentefijo->imagenqr) {
                                        $rutaQR2 = asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr);
                                    }
                                @endphp
                                @php
                                    $primerItem = $items[0];
                                    $tipoplanilla = $proveedoresServicios[$proveedor] ?? 'NO DEFINIDO';
                                    $nrobancoorigen = $primerItem->nrobancoorigen ?? 'NO DEFINIDO';
                                    
                                    $proveedorServicio = \App\Models\Proveedor::where('proveedor', $primerItem->proveedorasignado)->first();
                                    $numcuenta = $proveedorServicio->cuenta ?? 'NO DEFINIDO';
                                @endphp
                                <div class="mb-1 border rounded">
                                    <div class="table-responsive">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <table class="table table-sm table-bordered mb-0 w-100">
                                                <thead>
                                                    <tr style="background-color: #fffdf0">
                                                        <th style="width: 10%; font-size: 14px;">{{ $ordenId }}</th>
                                                        <th style="width: 25%; font-size: 14px;">{{ $proveedor }}</th>
                                                        <th style="width: 10%; font-size: 14px;">0</th>
                                                        <th style="width: 15%; font-size: 14px;">{{ $tipoplanilla }}</th>
                                                        <th style="width: 10%; font-size: 14px;">{{ $nrobancoorigen }}</th>
                                                        <th style="width: 10%; font-size: 14px;">{{ $numcuenta }}</th>
                                                        <th style="width: 10%; font-size: 14px;" class="text-right">{{ number_format($total, 2) }}</th>
                                                        <th style="width: 10%; font-size: 14px;" class="text-right" style="width: 200px;">
                                                            @if ($rutaQR)
                                                                <a class="btn btn-sm btn-botonverde" onclick="mostrarQR('{{ $rutaQR }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            @if ($rutaQR2)
                                                                <a class="btn btn-sm btn-botonverde" onclick="mostrarQR('{{ $rutaQR2 }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            <a class="btn btn-sm btn-botongris" title="VER DETALLES" onclick="document.getElementById('{{ $idGrupo }}').classList.toggle('d-none')">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mt-2 d-none" id="{{ $idGrupo }}">
                                            <thead>
                                                <tr>
                                                    <th>Selec.</th>
                                                    <th>Cliente</th>
                                                    <th>Detalle</th>
                                                    <th>Fecha_Pago</th>
                                                    <th>Tipo_Planilla</th>
                                                    <th>Banco_Origen</th>
                                                    <th>Banco_Destino</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($filtrados as $item)
                                                    @php
                                                        $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                                        $proveedorServicio = \App\Models\Proveedor::where('proveedor', $primerItem->proveedorasignado)->first();
                                                        $numcuenta = $proveedorServicio->cuenta ?? 'NO DEFINIDO';
                                                    @endphp
                                                    <tr>
                                                        <td><input type="checkbox" class="checkbox-bateria" value="{{ $item->id }}"></td>
                                                        <td>{{ $item->clientenombre }}</td>
                                                        <td>{{ $item->accionnombre }}</td>
                                                        <td>{{ $item->fechapago }}</td>
                                                        <td>{{ $tipoplanilla }}</td>
                                                        <td>{{ $item->nrobancoorigen }}</td>
                                                        <td>{{ $numcuenta }}</td>
                                                        <td class="text-right">{{ number_format($item->preciocompra, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- ADMINISTRACION --}}
                <div class="card">
                    <div class="card-body">
                        <h6 style="font-size: 20px;"><strong>PAGOS A TERCERO, INTERBANCARIO Y CHEQUE DE LA CUENTA 3000189269</strong></h6>
                        <div class="table-responsive" style="margin-bottom: -10px;">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr style="background-color: #f5f5f5">
                                        <th style="width: 10%">ID Orden</th>
                                        <th style="width: 25%">Proveedor</th>
                                        <th style="width: 10%">Sigla</th>
                                        <th style="width: 15%">Tipo Planilla</th>
                                        <th style="width: 10%">Banco Origen</th>
                                        <th style="width: 10%">Banco Destino</th>
                                        <th style="width: 10%" class="text-right">Total</th>
                                        <th style="width: 10%" class="text-right" style="width: 200px;">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        @php
                            $cuentasFiltradas = $cuentaspagar->whereIn('estadoaprobacion', ['SUBIDO', 'APROBADO']);
                            $agrupados = [];
                            foreach ($cuentasFiltradas as $item) {
                                $clave = $item->ordenid . '|' . $item->proveedornombre;
                                $agrupados[$clave][] = $item;
                            }
                            uksort($agrupados, function ($a, $b) use ($agrupados) {
                                $tipoplanillaA = optional(optional($agrupados[$a][0])->proveedorServicio)->tipoplanilla ?? 'ZZZ';
                                $tipoplanillaB = optional(optional($agrupados[$b][0])->proveedorServicio)->tipoplanilla ?? 'ZZZ';
                                return strcmp($tipoplanillaA, $tipoplanillaB);
                            });
                        @endphp
                        @foreach ($agrupados as $clave => $items)
                            @php
                                $filtrados = collect($items)->filter(function ($item) {
                                    $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                    return $item->nrobancoorigen === '3000189269' &&
                                        in_array($tipoplanilla, ['PAGO A TERCERO', 'PAGO INTERBANCARIO', 'PAGO CHEQUE']);
                                });
                                $estadoGrupo = $filtrados->every(fn($item) => $item->estadoaprobacion === 'SUBIDO') ? 'SUBIDO' : 'PENDIENTE';
                                $estadosGrupos[] = $estadoGrupo;
                            @endphp
                            @if ($filtrados->count() > 0)
                                @php
                                    [$ordenId, $proveedor] = explode('|', $clave);
                                    $total = $filtrados->sum('montototal');
                                    $idGrupo = 'grupo_' . md5($clave);
                                    $registroQR = $filtrados->first(function ($item) {
                                        $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? '';
                                        return $tipoplanilla === 'PAGO QR' &&
                                            \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedornombre)
                                                ->where('fechapago', $item->fechaasignada)
                                                ->exists();
                                    });
                                    $rutaQR = null;
                                        if ($registroQR) {
                                            $archivo = \App\Models\PlanillasPagosGeneradas::where('proveedor', $registroQR->proveedornombre)
                                                ->where('fechapago', $registroQR->fechaasignada)
                                                ->first();
                                            if ($archivo) {
                                                $rutaQR = asset('planillaspagosgeneradas/' . str_replace('-', '', $archivo->fechapago) . '/' . $archivo->documento);
                                                } 
                                            }
                                    $rutaQR2 = null;
                                    $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $proveedor)->first();
                                    if ($archivoExistentefijo && $archivoExistentefijo->imagenqr) {
                                        $rutaQR2 = asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr);
                                    }
                                @endphp
                                @php
                                    $primerItem = $items[0];
                                    $proveedorServicio = \App\Models\ProveedoresServicios::where('razonsocial', $primerItem->proveedornombre)->first();
                                    $tipoplanilla = $proveedorServicio->tipoplanilla ?? 'NO DEFINIDO';
                                    $numcuenta = $proveedorServicio->numcuenta ?? 'NO DEFINIDO';
                                    $nrobancoorigen = $primerItem->nrobancoorigen ?? 'NO DEFINIDO';
                                    $detalleLimpio = strtoupper(preg_replace('/\s+/', '', $filtrados->first()->detalleproducto ?? ''));
                                    $siglasItem = \App\Models\PlanesServiciosProv::where('razonsocial', $proveedor)
                                        ->get()
                                        ->filter(function ($registro) use ($detalleLimpio) {
                                            $codigoValido = $registro->codigo == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->codigo));
                                            $contratoValido = $registro->contrato == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->contrato));
                                            $lineaValida = $registro->linea == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->linea));
                                            $cuentaValida = $registro->cuenta == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->cuenta));
                                            $servicioValido = $registro->servicio == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->servicio));
                                            return $codigoValido && $contratoValido && $lineaValida && $cuentaValida && $servicioValido;
                                        })
                                        ->pluck('sigla')
                                        ->unique()
                                        ->values()
                                        ->all();
                                    $provsigla = count($siglasItem) > 0 ? implode(', ', $siglasItem) : '0';
                                @endphp
                                <div class="mb-1 border rounded">
                                    <div class="table-responsive">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <table class="table table-sm table-bordered mb-0 w-100">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 10%; font-size: 14px;">{{ $ordenId }}<span class="badge {{ $estadoGrupo === 'SUBIDO' ? 'badge-success' : 'badge-warning' }}">{{ $estadoGrupo }}</span></th>
                                                        <th style="width: 25%; font-size: 14px;">{{ $proveedor }}</th>
                                                        <th style="width: 10%; font-size: 14px;">{{ $provsigla }}</th>
                                                        <th style="width: 15%; font-size: 14px;">{{ $tipoplanilla }}</th>
                                                        <th style="width: 10%; font-size: 14px;">{{ $nrobancoorigen }}</th>
                                                        <th style="width: 10%; font-size: 14px;">{{ $numcuenta }}</th>
                                                        <th style="width: 10%; font-size: 14px;" class="text-right">{{ number_format($total, 2) }}</th>
                                                        <th style="width: 10%; font-size: 14px;" class="text-right" style="width: 200px;">
                                                            @if ($rutaQR)
                                                                <a class="btn btn-sm btn-botonverde" onclick="mostrarQR('{{ $rutaQR }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            @if ($rutaQR2)
                                                                <a class="btn btn-sm btn-botonverde" onclick="mostrarQR('{{ $rutaQR2 }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            <a class="btn btn-sm btn-botongris" title="VER DETALLES" onclick="document.getElementById('{{ $idGrupo }}').classList.toggle('d-none')">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mt-2 d-none" id="{{ $idGrupo }}">
                                            <thead>
                                                <tr>
                                                    <th>Selec.</th>
                                                    <th>Tipo_Orden</th>
                                                    <th>Sigla</th>
                                                    <th>Detalle</th>
                                                    <th>Fecha_Pago</th>
                                                    <th>Tipo_Planilla</th>
                                                    <th>Banco_Origen</th>
                                                    <th>Banco_Destino</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($filtrados as $item)
                                                    @php
                                                        $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                        $numcuenta = $proveedorServicio->numcuenta ?? 'NO DEFINIDO';

                                                        $detalleLimpio = strtoupper(preg_replace('/\s+/', '', $item->detalleproducto));
                                                        $siglasItem = \App\Models\PlanesServiciosProv::where('razonsocial', $item->proveedornombre)
                                                            ->get()
                                                            ->filter(function ($registro) use ($detalleLimpio) {
                                                                $codigoValido = $registro->codigo == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->codigo));
                                                                $contratoValido = $registro->contrato == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->contrato));
                                                                $lineaValida = $registro->linea == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->linea));
                                                                $cuentaValida = $registro->cuenta == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->cuenta));
                                                                $servicioValido = $registro->servicio == 0 || str_contains($detalleLimpio, strtoupper((string)$registro->servicio));
                                                                return $codigoValido && $contratoValido && $lineaValida && $cuentaValida && $servicioValido;
                                                            })
                                                            ->pluck('sigla')
                                                            ->unique()
                                                            ->values()
                                                            ->all();
                                                        $provsigla = count($siglasItem) > 0 ? implode(', ', $siglasItem) : '0';
                                                    @endphp
                                                    <tr>
                                                        {{-- <td><input type="checkbox" class="checkbox-cuentas" value="{{ $item->id }}"></td> --}}
                                                        <td>
                                                            <input 
                                                                type="checkbox" 
                                                                class="checkbox-cuentas" 
                                                                value="{{ $item->id }}" 
                                                                {{ $item->estadoaprobacion !== 'SUBIDO' ? 'disabled' : '' }}>
                                                            {{-- {{ $item->estadoaprobacion }} --}}
                                                        </td>
                                                        <td>{{ $item->tipoorden }}</td>
                                                        <td>{{ $provsigla }}</td>
                                                        
                                                        <td>{{ $item->detalleproducto }}</td>
                                                        <td>{{ $item->fechaasignada }}</td>
                                                        <td>{{ $tipoplanilla }}</td>
                                                        <td>{{ $item->nrobancoorigen }}</td>
                                                        <td>{{ $numcuenta }}</td>
                                                        <td class="text-right">{{ number_format($item->montototal, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @php
                            $bateriaFiltrada = $registrosbateria->whereIn('estadoaprobacion', ['SUBIDO', 'APROBADO']);
                            $gruposBateria = [];
                            foreach ($bateriaFiltrada as $item) {
                                $clave = $item->ordenid . '|' . $item->proveedorasignado;
                                $gruposBateria[$clave][] = $item;
                            }
                        @endphp
                        @foreach ($gruposBateria as $clave => $items)
                            @php
                                $filtrados = collect($items)->filter(function ($item) use ($proveedoresServicios) {
                                    $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                    return $item->nrobancoorigen === '3000189269' &&
                                        in_array($tipoplanilla, ['PAGO A TERCERO', 'PAGO INTERBANCARIO', 'PAGO CHEQUE']);
                                });
                                $estadoGrupo = $filtrados->every(fn($item) => $item->estadoaprobacion === 'SUBIDO') ? 'SUBIDO' : 'PENDIENTE';
                                $estadosGrupos[] = $estadoGrupo;
                            @endphp
                            @if ($filtrados->count() > 0)
                                @php
                                    [$ordenId, $proveedor] = explode('|', $clave);
                                    $total = $filtrados->sum('preciocompra');
                                    $idGrupo = 'grupo_bat_' . md5($clave);
                                    $registroQR = $filtrados->first(function ($item) use ($proveedoresServicios) {
                                        $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                        return $tipoplanilla === 'PAGO QR' &&
                                            \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedorasignado)
                                                ->where('fechapago', $item->fechapago)
                                                ->exists();
                                    });
                                    $rutaQR = null;
                                    if ($registroQR) {
                                        $archivo = \App\Models\PlanillasPagosGeneradas::where('proveedor', $registroQR->proveedornombre)
                                            ->where('fechapago', $registroQR->fechaasignada)
                                            ->first();
                                        if ($archivo) {
                                            $rutaQR = asset('planillaspagosgeneradas/' . str_replace('-', '', $archivo->fechapago) . '/' . $archivo->documento);
                                        } 
                                    }

                                    $rutaQR2 = null;
                                    $archivoExistentefijo = \App\Models\Proveedor::where('proveedor', $proveedor)->first();
                                    if ($archivoExistentefijo && $archivoExistentefijo->imagenqr) {
                                        $rutaQR2 = asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr);
                                    }
                                @endphp
                                @php
                                    $primerItem = $items[0];
                                    $tipoplanilla = $proveedoresServicios[$proveedor] ?? 'NO DEFINIDO';
                                    $nrobancoorigen = $primerItem->nrobancoorigen ?? 'NO DEFINIDO';
                                    
                                    $proveedorServicio = \App\Models\Proveedor::where('proveedor', $primerItem->proveedorasignado)->first();
                                    $numcuenta = $proveedorServicio->cuenta ?? 'NO DEFINIDO';
                                @endphp
                                <div class="mb-1 border rounded">
                                    <div class="table-responsive">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <table class="table table-sm table-bordered mb-0 w-100">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 10%; font-size: 14px;">{{ $ordenId }}<span class="badge {{ $estadoGrupo === 'SUBIDO' ? 'badge-success' : 'badge-warning' }}">{{ $estadoGrupo }}</span></th>
                                                        <th style="width: 25%; font-size: 14px;">{{ $proveedor }}</th>
                                                        <th style="width: 10%; font-size: 14px;">0</th>
                                                        <th style="width: 15%; font-size: 14px;">{{ $tipoplanilla }}</th>
                                                        <th style="width: 10%; font-size: 14px;">{{ $nrobancoorigen }}</th>
                                                        <th style="width: 10%; font-size: 14px;">{{ $numcuenta }}</th>
                                                        <th style="width: 10%; font-size: 14px;" class="text-right">{{ number_format($total, 2) }}</th>
                                                        <th style="width: 10%; font-size: 14px;" class="text-right" style="width: 200px;">
                                                            @if ($rutaQR)
                                                                <a class="btn btn-sm btn-botonverde" onclick="mostrarQR('{{ $rutaQR }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            @if ($rutaQR2)
                                                                <a class="btn btn-sm btn-botonverde" onclick="mostrarQR('{{ $rutaQR2 }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            <a class="btn btn-sm btn-botongris" title="VER DETALLES" onclick="document.getElementById('{{ $idGrupo }}').classList.toggle('d-none')">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mt-2 d-none" id="{{ $idGrupo }}">
                                            <thead>
                                                <tr>
                                                    <th>Selec.</th>
                                                    <th>Cliente</th>
                                                    <th>Detalle</th>
                                                    <th>Fecha_Pago</th>
                                                    <th>Tipo_Planilla</th>
                                                    <th>Banco_Origen</th>
                                                    <th>Banco_Destino</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($filtrados as $item)
                                                    @php
                                                        $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                                        $proveedorServicio = \App\Models\Proveedor::where('proveedor', $primerItem->proveedorasignado)->first();
                                                        $numcuenta = $proveedorServicio->cuenta ?? 'NO DEFINIDO';
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <input 
                                                                type="checkbox" 
                                                                class="checkbox-bateria" 
                                                                value="{{ $item->id }}" 
                                                                {{ $item->estadoaprobacion !== 'SUBIDO' ? 'disabled' : '' }}>
                                                        </td>
                                                        <td>{{ $item->clientenombre }}</td>
                                                        <td>{{ $item->accionnombre }}</td>
                                                        <td>{{ $item->fechapago }}</td>
                                                        <td>{{ $tipoplanilla }}</td>
                                                        <td>{{ $item->nrobancoorigen }}</td>
                                                        <td>{{ $numcuenta }}</td>
                                                        <td class="text-right">{{ number_format($item->preciocompra, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- ADMINISTRACION --}}
                <div class="card">
                    <div class="card-body">
                        @php
                            $bloquearAcciones = in_array('PENDIENTE', $estadosGrupos ?? []);
                        @endphp
                        <div class="d-flex align-items-center p-3 mb-3" style="background-color: #f5f5f5; border-radius: 5px;">  
                            <div>
                                <input 
                                    type="checkbox" 
                                    id="select-todos-global" 
                                    {{ $bloquearAcciones ? 'disabled' : '' }}>
                                <strong>SELECCIONAR TODO</strong>
                            </div>
                            <button 
                                class="btn btn-botongrisgrande" 
                                onclick="enviarSeleccionados()" 
                                id="btn-confirmar-pagos"
                                style="margin-left: 10px;" 
                                disabled>
                                CONFIRMAR PAGOS
                            </button>
                        </div>
                        <script>
                            const checkboxGlobal = document.getElementById('select-todos-global');
                            const btnConfirmar = document.getElementById('btn-confirmar-pagos');

                            checkboxGlobal.addEventListener('change', function () {
                                const isChecked = this.checked;
                                
                                document.querySelectorAll('.checkbox-cuentas, .checkbox-bateria').forEach(cb => cb.checked = isChecked);
                                
                                btnConfirmar.disabled = !isChecked;
                            });

                            function enviarSeleccionados() {
                                const seleccionadosCuentas = Array.from(document.querySelectorAll('.checkbox-cuentas:checked')).map(cb => cb.value);
                                const seleccionadosBateria = Array.from(document.querySelectorAll('.checkbox-bateria:checked')).map(cb => cb.value);

                                fetch("{{ route('actualizar.estado.aprobacion') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        cuentas: seleccionadosCuentas,
                                        bateria: seleccionadosBateria
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    alert(data.message);
                                    location.reload();
                                })
                                .catch(error => {
                                    console.error(error);
                                    alert("Error al actualizar.");
                                });
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalQR" tabindex="-1" aria-labelledby="modalQRLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalQRLabel"><strong>CODIGO QR</strong></h5>
            <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body text-center">
            <img id="imagenQR" src="" class="img-fluid" alt="QR">
        </div>
        </div>
    </div>
</div>
<script>
    function mostrarQR(ruta) {
        document.getElementById('imagenQR').src = ruta;
        const modal = new bootstrap.Modal(document.getElementById('modalQR'));
        modal.show();
    }
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
            document.getElementById('actualizarPrioridadBtn').addEventListener('click', function () {
            let preordenesSeleccionadas = [];

            document.querySelectorAll('.check-prioridad:checked').forEach(cb => {
                preordenesSeleccionadas.push(cb.dataset.bateriaid);
            });

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
                body: JSON.stringify({
                    preordenes: preordenesSeleccionadas
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

