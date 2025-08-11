@extends('adminlte::page')

@section('content_header')
<h1>CUENTAS POR PAGAR COMPROBANTES</h1>
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
    .btn-botongris2 {
        background-color: #ffffff;
        color: #676767;
        border-color: #676767;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-botongris2:hover {
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
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">  
            <li class="nav-item">
                <a class="nav-link active" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    PENDIENTES
                </a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    COMPLETOS
                </a>
            </li>         
        </ul>
    </div>

        <div class="card-body">
            <div class="tab-content" id="myTabContent">
            
                {{-- PENDIENTES --}}
                <div class="tab-pane fade show active" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 70vh;">
                                <h4 style="font-weight:900;">CUENTAS POR PAGAR</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Selec.</th>
                                                <th hidden>ID</th>
                                                <th>Orden.ID</th>
                                                <th>Proveedor</th>
                                                <th>Detalle</th>
                                                <th>Fecha_Pago</th>
                                                <th>Tipo_Planilla</th>
                                                <th>Banco_Origen</th>
                                                <th>Banco_Destino</th>
                                                <th>N.Cuenta</th>
                                                <th>Monto_Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cuentaspagar->where('comprobante', null)->whereIn('estadoaprobacion', ['APROBADO', 'SUBIDO', 'CARGADO'])->sortByDesc('nrobancoorigen') as $item)
                                                @php
                                                    $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                @endphp

                                                @if (in_array($tipoplanilla, ['PAGO QR', 'PAGO EN LINEA','PAGO A TERCERO', 'PAGO INTERBANCARIO', 'PAGO CHEQUE']))
                                                    @php
                                                        $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedornombre)
                                                            ->where('fechapago', $item->fechaasignada)
                                                            ->first();
                                                        $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $item->proveedornombre)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            @if ($item->comprobante)
                                                                <a class="btn btn-sm btn-botongris mt-1" 
                                                                href="{{ asset('comprobantescuentaspagar/' . $item->comprobante) }}" 
                                                                target="_blank" title="Ver comprobante">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @else
                                                            <input type="checkbox" class="checkbox-cuentas" value="{{ $item->id }}">
                                                            @endif
                                                        </td>
                                                        <td hidden>{{ $item->id }}</td>
                                                        <td>{{ $item->ordenid }}</td>
                                                        <td>{{ $item->proveedornombre }}</td>
                                                        <td>{{ $item->detalleproducto }}</td>
                                                        <td>{{ $item->fechaasignada }}</td>
                                                        <td>
                                                            {{ $tipoplanilla }}
                                                            @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            
                                                            @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->nrobancoorigen }}</td>
                                                        <td>{{ $item->proveedorServicio->banco ?? '0' }}</td>
                                                        <td>{{ $item->proveedorServicio->numcuenta ?? '0' }}</td>
                                                        <td class="text-right">{{ number_format($item->montototal, 2) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <h4 style="font-weight:900;">PROVEEDORES MÉDICOS</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Selec.</th>
                                                <th hidden>ID</th>
                                                <th>Orden.ID</th>
                                                <th>Proveedor</th>
                                                <th>Cliente</th>
                                                <th>Detalle</th>
                                                <th>Fecha_Pago</th>
                                                <th>Tipo_Planilla</th>
                                                <th>Banco_Origen</th>
                                                <th>Banco_Destino</th>
                                                <th>N.Cuenta</th>
                                                <th>Monto_Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($registrosbateria->where('comprobante', null)->whereIn('estadoaprobacion', ['APROBADO', 'SUBIDO', 'CARGADO'])->sortByDesc('nrobancoorigen') as $item)
                                                @php
                                                    $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                                @endphp

                                                @if (in_array($tipoplanilla, ['PAGO QR', 'PAGO EN LINEA','PAGO A TERCERO', 'PAGO INTERBANCARIO', 'PAGO CHEQUE']))
                                                    @php
                                                        $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedorasignado)
                                                            ->where('fechapago', $item->fechapago)
                                                            ->first();
                                                            $archivoExistentefijo = \App\Models\Proveedor::where('proveedor', $item->proveedorasignado)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            @if ($item->comprobante)
                                                                <a class="btn btn-sm btn-primary mt-1" 
                                                                href="{{ asset('comprobantescuentaspagar/' . $item->comprobante) }}" 
                                                                target="_blank" title="Ver comprobante">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @else
                                                                <input type="checkbox" class="checkbox-bateria" value="{{ $item->id }}">
                                                            @endif
                                                        </td>
                                                        <td hidden>{{ $item->id }}</td>
                                                        <td>{{ $item->ordenid }}</td>
                                                        <td>{{ $item->proveedorasignado }}</td>
                                                        <td>{{ $item->clienteitanombre }}{{ $item->clienteauditorianombre }}{{ $item->clientecomunnombre }}</td>
                                                        <td>{{ $item->accionnombre }}</td>
                                                        <td>{{ $item->fechapago }}</td>
                                                        <td>
                                                            {{ $tipoplanilla }}
                                                            @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif

                                                            @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                <a class="btn btn-botongris" 
                                                                    onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->nrobancoorigen }}</td>
                                                        <td>{{ $item->proveedoresmedicos->banco ?? '0' }}</td>
                                                        <td>{{ $item->proveedoresmedicos->cuenta ?? '0' }}</td>
                                                        <td class="text-right">{{ number_format($item->preciocompra, 2) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @can('admin.cuentaspagar.guardarcomprobantescxp')
                            <div class="d-flex flex-wrap align-items-end" style="border-radius: 5px; margin-top:10px;">
                                <div>
                                    <label for="archivoComprobante" class="form-label"><strong>Comprobante (Obligatorio):</strong></label>
                                    <input type="file" id="archivoComprobante" accept=".pdf,.jpg,.png" class="form-control" required>
                                </div>
                                
                                <div class="ms-2">
                                    <label for="usuarioNotificado" class="form-label"><strong>Notificar a (Obligatorio):</strong></label>
                                    <select id="usuarioNotificado" class="form-control" required>
                                        <option value="">-- Seleccione --</option>
                                        <option value="SERGIO ARMANDO MICHEL MAITA">SERGIO ARMANDO MICHEL MAITA</option>
                                        <option value="CRISTHIAN ALAIN DURAN SULLCA">CRISTHIAN ALAIN DURAN SULLCA</option>
                                        <option value="JHOSELINE EVA VELASQUEZ ESCOBAR">JHOSELINE EVA VELASQUEZ ESCOBAR</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="archivoCheque" class="form-label"><strong>Cheque (Opcional):</strong></label>
                                    <input type="file" id="archivoCheque" accept=".pdf,.jpg,.png" class="form-control">
                                </div>
                                <div class="d-flex align-items-end">
                                    <button class="btn btn-botongrisgrande" onclick="enviarSeleccionados()">GUARDAR</button>
                                </div>
                            </div>
                            @endcan

                            <script>
                                function enviarSeleccionados() {
                                    const archivo = document.getElementById('archivoComprobante').files[0];
                                    const archivo2 = document.getElementById('archivoCheque').files[0];
                                    const usuarioNotificado = document.getElementById('usuarioNotificado').value;
                                    if (!archivo || !usuarioNotificado) {
                                        alert("Debe seleccionar un archivo y un usuario para notificar.");
                                        return;
                                    }
                                    const seleccionadosCuentas = Array.from(document.querySelectorAll('.checkbox-cuentas:checked')).map(cb => cb.value);
                                    const seleccionadosBateria = Array.from(document.querySelectorAll('.checkbox-bateria:checked')).map(cb => cb.value);
                                    const formData = new FormData();
                                    formData.append('archivo', archivo);
                                    formData.append('archivo2', archivo2);
                                    formData.append('usuarioNotificado', usuarioNotificado);
                                    seleccionadosCuentas.forEach(id => formData.append('cuentas[]', id));
                                    seleccionadosBateria.forEach(id => formData.append('bateria[]', id));

                                    fetch("{{ route('actualizar.comprobante') }}", {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                        },
                                        body: formData
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
                {{-- COMPLETOS --}}
                <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body" style="background-color: #f7f7f7">
                                    <div class="row align-items-end g-2">
                                        <div class="col-md-4 col-lg-4">
                                            <label class="form-label">Proveedor</label>
                                            <input type="text" id="filtroProveedor" class="form-control" placeholder="Buscar por proveedor..." onkeyup="filtrarTabla()">
                                        </div>

                                        <div class="col-md-4 col-lg-3">
                                            <label class="form-label">Fecha</label>
                                            <input type="date" id="filtroFecha" class="form-control" onchange="filtrarTabla()">
                                        </div>

                                        <div class="col-md-4 col-lg-3">
                                            <label class="form-label">Tipo de Transacción</label>
                                            <select class="form-control" id="filtroTipoTransaccion" onchange="filtrarTabla()">
                                                <option value="" disabled selected>Buscar por tipo transacción...</option>
                                                <option value="TRANSFERENCIA BANCARIA">TRANSFERENCIA BANCARIA</option>
                                                <option value="CHEQUE">CHEQUE</option>
                                            </select>
                                        </div>

                                        <div class="col-auto mt-4">
                                            <a class="btn btn-botongrisgrande" onclick="limpiarFiltros()" title="Limpiar filtros">
                                                <i class="fas fa-broom"> Limpiar</i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 style="font-weight:900;">CUENTAS POR PAGAR</h4>
                                    <div class="table-responsive">
                                        <table id="tabla-cuentas-pagar" class="table table-hover table-bordered">
                                            @php
                                                $filtrados = $cuentaspagar->whereNotNull('comprobante')
                                                    ->whereIn('estadoaprobacion', ['APROBADO', 'CARGADO']);
                                                $registrosIndividuales = $filtrados->filter(function($item) {
                                                    return in_array($item->proveedornombre, ['LAURA GONZALES MONTENEGRO', 'PAMELA ROSALY SANTOS VASQUEZ']) &&
                                                        \Carbon\Carbon::parse($item->fechaasignada)->format('Y-m-d') === '2025-06-13';
                                                });
                                                $resto = $filtrados->diff($registrosIndividuales);
                                                $agrupados1SQL = $resto->where('ordenid', '1SQL')->groupBy('proveedornombre');
                                                $agrupadosOtros = $resto->where('ordenid', '!=', '1SQL')->groupBy('ordenid');

                                                $detalle = \App\Models\Detallerecibo::where('cuentapagarid', $item->id)->first();
                                                $tipoTransaccion = $detalle->tipotransaccion ?? 'NO DEFINIDO';
                                            @endphp
                                            <thead class="thead-secondary">
                                                <tr>
                                                    <th style="width: 5%;">Sel.</th>
                                                    <th style="width: 5%;">Ver</th>
                                                    <th style="width: 5%;">Comp.</th>
                                                    <th style="width: 10%;">Orden.ID</th>
                                                    <th>Proveedor</th>
                                                    <th style="width: 15%;">Fecha_Pago</th>
                                                    <th style="width: 15%;">Transacción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($registrosIndividuales as $item)
                                                    @php
                                                        $ordenId = $item->ordenid;
                                                        $collapseId = 'individual_' . \Str::slug($item->proveedornombre) . '_' . $item->id;
                                                        $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                        $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedornombre)
                                                            ->where('fechapago', $item->fechaasignada)
                                                            ->first();
                                                        $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $item->proveedornombre)->first();

                                                        $detalle = \App\Models\Detallerecibo::where('cuentapagarid', $item->id)->first();
                                                        $tipoTransacciongeneral = $detalle->tipotransaccion ?? 'NO DEFINIDO';
                                                    @endphp

                                                    <tr class="font-weight-bold align-middle" style="background-color: #f7f7f7">
                                                        <td class="text-center">
                                                            {{-- <input type="checkbox" class="checkbox-grupo" data-orden="{{ $ordenId }}"> --}}
                                                            <i class="fas fa-check text-success"></i>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-botongris2" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}">
                                                                <i class="fas fa-arrow-down"></i>
                                                            </button>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($item->comprobante)
                                                                <a class="btn btn-sm btn-botongris2" href="{{ asset('comprobantescuentaspagar/' . $item->comprobante) }}" target="_blank" title="VER COMPROBANTE">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                            @if ($item->cheque)
                                                                <a class="btn btn-sm btn-botongris2" href="{{ asset('comprobantescuentaspagar/' . $item->cheque) }}" target="_blank" title="VER CHEQUE">
                                                                    <i class="fas fa-file"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $ordenId }}</td>
                                                        <td>{{ $item->proveedornombre }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->fechaasignada)->format('d/m/Y') }}</td>
                                                        <td>{{ $tipoTransacciongeneral }}</td>
                                                    </tr>

                                                    <tr class="collapse bg-light" id="{{ $collapseId }}">
                                                        <th>Sel.</th>
                                                        <th>ID</th>
                                                        <th>Orden.ID</th>
                                                        <th>Proveedor</th>
                                                        <th>Detalle</th>
                                                        <th>Tipo_Planilla</th>
                                                        <th>Banco_Origen</th>
                                                        <th>Banco_Destino</th>
                                                        <th>N°_Cuenta</th>
                                                        <th>Tipo Transaccion</th>
                                                        <th class="text-right">Monto</th>
                                                    </tr>
                                                    <tr class="collapse" id="{{ $collapseId }}">
                                                        <td>
                                                            <input type="checkbox" class="checkbox-cuentas cuenta-{{ $ordenId }}" value="{{ $item->id }}">
                                                        </td>
                                                        <td>{{ $item->id }}</td>
                                                        <td>{{ $item->ordenid }}</td>
                                                        <td>{{ $item->proveedornombre }}</td>
                                                        <td>{{ $item->detalleproducto }}</td>
                                                        <td>
                                                            {{ $tipoplanilla }}
                                                            @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                <a class="btn btn-sm btn-outline-secondary"
                                                                    onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                            @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                <a class="btn btn-sm btn-outline-secondary"
                                                                    onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                    <i class="fas fa-qrcode"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->nrobancoorigen }}</td>
                                                        <td>{{ $item->proveedorServicio->banco ?? '0' }}</td>
                                                        <td>{{ $item->proveedorServicio->numcuenta ?? '0' }}</td>
                                                        <td>{{ $tipoTransaccion }}</td>
                                                        <td class="text-right">{{ number_format($item->montototal, 2) }}</td>
                                                    </tr>
                                                @endforeach

                                                @foreach ($agrupados1SQL as $proveedorNombre => $items)
                                                    @php
                                                        $primer = $items->first();
                                                        $ordenId = $primer->ordenid;
                                                        $fechaPago = $primer->fechaasignada;
                                                        $archivo = $primer->comprobante;
                                                        $archivo2 = $primer->cheque;
                                                        $proveedor = $primer->proveedornombre;
                                                        $collapseId = 'grupo_1SQL_' . \Str::slug($proveedorNombre);
                                                        $detalle = \App\Models\Detallerecibo::where('cuentapagarid', $primer->id)->first();
                                                        $tipoTransacciongeneral = $detalle->tipotransaccion ?? 'NO DEFINIDO';
                                                    @endphp

                                                    <tr class="font-weight-bold align-middle" style="background-color: #f7f7f7">
                                                        <td class="text-center">
                                                            {{-- <input type="checkbox" class="checkbox-grupo" data-orden="{{ $ordenId }}"> --}}
                                                            <i class="fas fa-check text-success"></i>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-botongris2" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}">
                                                                <i class="fas fa-arrow-down"></i>
                                                            </button>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($archivo)
                                                                <a class="btn btn-sm btn-botongris2" href="{{ asset('comprobantescuentaspagar/' . $archivo) }}" target="_blank" title="VER COMPROBANTE">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif

                                                            @if ($archivo2)
                                                                <a class="btn btn-sm btn-botongris2" href="{{ asset('comprobantescuentaspagar/' . $archivo2) }}" target="_blank" title="VER CHEQUE">
                                                                    <i class="fas fa-file"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $ordenId }}</td>
                                                        <td>{{ $proveedor }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($fechaPago)->format('d/m/Y') }}</td>
                                                        <td>{{ $tipoTransacciongeneral }}</td>
                                                    </tr>
                                                    <tr class="collapse bg-light" id="{{ $collapseId }}">
                                                        <th>Sel.</th>
                                                        <th>ID</th>
                                                        <th>Orden.ID</th>
                                                        <th>Proveedor</th>
                                                        <th>Detalle</th>
                                                        <th>Tipo_Planilla</th>
                                                        <th>Banco_Origen</th>
                                                        <th>Banco_Destino</th>
                                                        <th>N°_Cuenta</th>
                                                        <th>Transacción</th>
                                                        <th class="text-right">Monto</th>
                                                    </tr>

                                                    @foreach ($items as $item)
                                                        @php
                                                            $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                            $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedornombre)
                                                                ->where('fechapago', $item->fechaasignada)
                                                                ->first();
                                                            $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $item->proveedornombre)->first();

                                                            $detalle = \App\Models\Detallerecibo::where('cuentapagarid', $item->id)->first();
                                                            $tipoTransaccion = $detalle->tipotransaccion ?? 'NO DEFINIDO';
                                                        @endphp
                                                        <tr class="collapse" id="{{ $collapseId }}">
                                                            <td>
                                                                <input type="checkbox" class="checkbox-cuentas cuenta-{{ $ordenId }}" value="{{ $item->id }}">
                                                            </td>
                                                            <td>{{ $item->id }}</td>
                                                            <td>{{ $item->ordenid }}</td>
                                                            <td>{{ $item->proveedornombre }}</td>
                                                            <td>{{ $item->detalleproducto }}</td>
                                                            <td>
                                                                {{ $tipoplanilla }}
                                                                @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                    <a class="btn btn-sm btn-outline-secondary"
                                                                        onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                        <i class="fas fa-qrcode"></i>
                                                                    </a>
                                                                @endif
                                                                @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                    <a class="btn btn-sm btn-outline-secondary"
                                                                        onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                        <i class="fas fa-qrcode"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->nrobancoorigen }}</td>
                                                            <td>{{ $item->proveedorServicio->banco ?? '0' }}</td>
                                                            <td>{{ $item->proveedorServicio->numcuenta ?? '0' }}</td>
                                                            <td>{{ $tipoTransaccion }}</td>
                                                            <td class="text-right">{{ number_format($item->montototal, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach

                                                @foreach ($agrupadosOtros as $ordenId => $items)
                                                    @php
                                                        $primer = $items->first();
                                                        $proveedor = $primer->proveedornombre;
                                                        $fechaPago = $primer->fechaasignada;
                                                        $archivo = $primer->comprobante;
                                                        $archivo2 = $primer->cheque;
                                                        $collapseId = 'grupo_' . $ordenId;
                                                        $detalle = \App\Models\Detallerecibo::where('cuentapagarid', $primer->id)->first();
                                                        $tipoTransacciongeneral = $detalle->tipotransaccion ?? 'NO DEFINIDO';
                                                    @endphp

                                                    <tr class="font-weight-bold align-middle" style="background-color: #f7f7f7">
                                                        <td class="text-center">
                                                            {{-- <input type="checkbox" class="checkbox-grupo" data-orden="{{ $ordenId }}"> --}}
                                                            <i class="fas fa-check text-success"></i>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-botongris2" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}">
                                                                <i class="fas fa-arrow-down"></i>
                                                            </button>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($archivo)
                                                                <a class="btn btn-sm btn-botongris2" href="{{ asset('comprobantescuentaspagar/' . $archivo) }}" target="_blank" title="VER COMPROBANTE">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                            @if ($archivo2)
                                                                <a class="btn btn-sm btn-botongris2" href="{{ asset('comprobantescuentaspagar/' . $archivo2) }}" target="_blank" title="VER CHEQUE">
                                                                    <i class="fas fa-file"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $ordenId }}</td>
                                                        <td>{{ $proveedor }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($fechaPago)->format('d/m/Y') }}</td>
                                                        <td>{{ $tipoTransacciongeneral }}</td>
                                                    </tr>
                                                    <tr class="collapse bg-light" id="{{ $collapseId }}">
                                                        <th>Sel.</th>
                                                        <th>ID</th>
                                                        <th>Orden.ID</th>
                                                        <th>Detalle</th>
                                                        <th>Tipo_Planilla</th>
                                                        <th>Banco_Origen</th>
                                                        <th>Banco_Destino</th>
                                                        <th>N°_Cuenta</th>
                                                        <th>Transacción</th>
                                                        <th class="text-right">Monto</th>
                                                    </tr>
                                                    @foreach ($items as $item)
                                                        @php
                                                            $tipoplanilla = optional($item->proveedorServicio)->tipoplanilla ?? 'NO DEFINIDO';
                                                            $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedornombre)
                                                                ->where('fechapago', $item->fechaasignada)
                                                                ->first();
                                                            $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $item->proveedornombre)->first();

                                                            $detalle = \App\Models\Detallerecibo::where('cuentapagarid', $item->id)->first();
                                                            $tipoTransaccion = $detalle->tipotransaccion ?? 'NO DEFINIDO';
                                                        @endphp
                                                        <tr class="collapse" id="{{ $collapseId }}">
                                                            <td>
                                                                <input type="checkbox" class="checkbox-cuentas cuenta-{{ $ordenId }}" value="{{ $item->id }}">
                                                            </td>
                                                            <td>{{ $item->id }}</td>
                                                            <td>{{ $item->ordenid }}</td>
                                                            <td>{{ $item->detalleproducto }}</td>
                                                            <td>
                                                                {{ $tipoplanilla }}
                                                                @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                    <a class="btn btn-sm btn-outline-secondary"
                                                                        onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                        <i class="fas fa-qrcode"></i>
                                                                    </a>
                                                                @endif
                                                                @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                    <a class="btn btn-sm btn-outline-secondary"
                                                                        onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                        <i class="fas fa-qrcode"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->nrobancoorigen }}</td>
                                                            <td>{{ $item->proveedorServicio->banco ?? '0' }}</td>
                                                            <td>{{ $item->proveedorServicio->numcuenta ?? '0' }}</td>
                                                            <td>{{ $tipoTransaccion }}</td>
                                                            <td class="text-right">{{ number_format($item->montototal, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                            <script>
                                                document.querySelectorAll('.checkbox-grupo').forEach(grupoCheckbox => {
                                                    grupoCheckbox.addEventListener('change', function () {
                                                        const ordenId = this.getAttribute('data-orden');
                                                        const checked = this.checked;
                                                        const checkboxes = document.querySelectorAll(`.cuenta-${ordenId}`);
                                                        checkboxes.forEach(cb => cb.checked = checked);
                                                    });
                                                });
                                            </script>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $agrupadosMedicos = $registrosbateria->whereNotNull('comprobante')
                                ->whereIn('estadoaprobacion', ['APROBADO', 'CARGADO', 'SUBIDO'])
                                ->groupBy('ordenid');
                        @endphp
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 style="font-weight:900;">PROVEEDORES MÉDICOS</h4>
                                    <div class="table-responsive">
                                        <table id="tabla-proveedores-medicos" class="table table-hover table-bordered">
                                            <thead class="thead-secondary">
                                                <tr>
                                                    <th style="width: 5%;">Sel.</th>
                                                    <th style="width: 5%;">Ver</th>
                                                    <th style="width: 5%;">Comp.</th>
                                                    <th style="width: 10%;">Orden.ID</th>
                                                    <th>Proveedor</th>
                                                    <th style="width: 15%;">Fecha_Pago</th>
                                                    <th style="width: 15%;">Transacción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($agrupadosMedicos as $ordenId => $items)
                                                    @php
                                                        $primer = $items->first();
                                                        $proveedor = $primer->proveedorasignado;
                                                        $fechaPago = $primer->fechapago;
                                                        $archivo = $primer->comprobante;
                                                        $archivo2 = $primer->cheque;
                                                        $collapseId = 'grupo_med_' . $ordenId;

                                                        $detalle = \App\Models\Detallerecibo::where(function ($query) use ($primer) {
                                                                    $query->where('clientenombre', $primer->clienteitanombre)
                                                                        ->orWhere('clientenombre', $primer->clienteauditorianombre)
                                                                        ->orWhere('clientenombre', $primer->clientecomunnombre);
                                                                })
                                                                ->where('fechabateria', $primer->fechabateria)
                                                                ->where('proveedoratencion', $primer->proveedorasignado)
                                                                ->where('detalle', $primer->accionnombre)
                                                                ->where('tipomovimiento', 'EGRESO')
                                                                ->first();
                                                        $tipoTransacciongeneral = $detalle->tipotransaccion ?? 'NO DEFINIDO';
                                                    @endphp

                                                    <tr class="font-weight-bold align-middle" style="background-color: #f7f7f7" data-fecha="{{ $fechaPago }}">
                                                        <td class="text-center">
                                                            {{-- <input type="checkbox" class="checkbox-grupo-med" data-orden="{{ $ordenId }}"> --}}
                                                            <i class="fas fa-check text-success"></i>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-sm btn-botongris2" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}">
                                                                <i class="fas fa-arrow-down"></i>
                                                            </button>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($archivo)
                                                                <a class="btn btn-sm btn-botongris2" href="{{ asset('comprobantescuentaspagar/' . $archivo) }}" target="_blank" title="VER COMPROBANTE">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                            @if ($archivo2)
                                                                <a class="btn btn-sm btn-botongris2" href="{{ asset('comprobantescuentaspagar/' . $archivo2) }}" target="_blank" title="VER CHEQUE">
                                                                    <i class="fas fa-file"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $ordenId }}</td>
                                                        <td>{{ $proveedor }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($fechaPago)->format('d/m/Y') }}</td>
                                                        <td>{{ $tipoTransacciongeneral }}</td>
                                                    </tr>

                                                    <tr class="collapse bg-light" id="{{ $collapseId }}">
                                                        <th>Sel.</th>
                                                        <th>ID</th>
                                                        <th>Cliente</th>
                                                        <th>Detalle</th>
                                                        <th>Tipo_Planilla</th>
                                                        <th>Banco_Origen</th>
                                                        <th>Banco_Destino</th>
                                                        <th>N.Cuenta</th>
                                                        <th>Transacción</th>
                                                        <th class="text-right">Monto</th>
                                                    </tr>

                                                    @foreach ($items as $item)
                                                        @php
                                                            $tipoplanilla = $proveedoresServicios[$item->proveedorasignado] ?? 'NO DEFINIDO';
                                                            $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $item->proveedorasignado)
                                                                ->where('fechapago', $item->fechapago)
                                                                ->first();
                                                            $archivoExistentefijo = \App\Models\Proveedor::where('proveedor', $item->proveedorasignado)->first();

                                                            $detalle = \App\Models\Detallerecibo::where(function ($query) use ($item) {
                                                                    $query->where('clientenombre', $item->clienteitanombre)
                                                                        ->orWhere('clientenombre', $item->clienteauditorianombre)
                                                                        ->orWhere('clientenombre', $item->clientecomunnombre);
                                                                })
                                                                ->where('fechabateria', $item->fechabateria)
                                                                ->where('proveedoratencion', $item->proveedorasignado)
                                                                ->where('detalle', $item->accionnombre)
                                                                ->where('tipomovimiento', 'EGRESO')
                                                                ->first();

                                                            $tipoTransaccion = $detalle->tipotransaccion ?? 'NO DEFINIDO';

                                                        @endphp
                                                        <tr class="collapse" id="{{ $collapseId }}">
                                                            <td><input type="checkbox" class="checkbox-med cuenta-med-{{ $ordenId }}" value="{{ $item->id }}"></td>
                                                            <td>{{ $item->id }}</td>
                                                            <td>{{ $item->clienteitanombre }}{{ $item->clienteauditorianombre }}{{ $item->clientecomunnombre }}</td>
                                                            <td>{{ $item->accionnombre }}</td>
                                                            <td>
                                                                {{ $tipoplanilla }}
                                                                @if ($tipoplanilla === 'PAGO QR' && $archivoExistente)
                                                                    <a class="btn btn-sm btn-outline-secondary" onclick="mostrarQR('{{ asset('planillaspagosgeneradas/' . str_replace('-', '', $archivoExistente->fechapago) . '/' . $archivoExistente->documento) }}')">
                                                                        <i class="fas fa-qrcode"></i>
                                                                    </a>
                                                                @endif
                                                                @if ($tipoplanilla === 'PAGO QR' && ($archivoExistentefijo && $archivoExistentefijo->imagenqr))
                                                                    <a class="btn btn-sm btn-outline-secondary" onclick="mostrarQR('{{ asset('proveedoresdocumentos/' . $archivoExistentefijo->id . '/' . $archivoExistentefijo->imagenqr) }}')">
                                                                        <i class="fas fa-qrcode"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->nrobancoorigen }}</td>
                                                            <td>{{ $item->proveedoresmedicos->banco ?? '0' }}</td>
                                                            <td>{{ $item->proveedoresmedicos->cuenta ?? '0' }}</td>
                                                            <td>{{ $tipoTransaccion }}</td>
                                                            <td class="text-right">{{ number_format($item->preciocompra, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @can('admin.cuentaspagar.guardarcomprobantescxp')
                        <div class="card">
                            <div class="card-body" style="background-color: #f7f7f7">
                                <div class="d-flex flex-wrap align-items-end" style="border-radius: 5px;">
                                    <div>
                                        <label for="archivoComprobante" class="form-label"><strong>Comprobante:</strong></label>
                                        <input type="file" id="archivoComprobante2" accept=".pdf,.jpg,.png" class="form-control" required>
                                    </div>
                                    <div class="d-flex align-items-end">
                                        <button class="btn btn-botongrisgrande" onclick="enviarSeleccionados2()">ACTUALIZAR</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                    <script>
                        function enviarSeleccionados2() {
                            const archivo = document.getElementById('archivoComprobante2').files[0];
                            if (!archivo) {
                                alert("Debe seleccionar un archivo comprobante.");
                                return;
                            }

                            const seleccionadosCuentas = Array.from(document.querySelectorAll('.checkbox-cuentas:checked')).map(cb => cb.value);
                            const seleccionadosBateria = Array.from(document.querySelectorAll('.checkbox-bateria:checked')).map(cb => cb.value);

                            const formData = new FormData();
                            formData.append('archivo', archivo);
                            seleccionadosCuentas.forEach(id => formData.append('cuentas[]', id));
                            seleccionadosBateria.forEach(id => formData.append('bateria[]', id));

                            fetch("{{ route('actualizar.comprobante') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                },
                                body: formData
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
                <script>
                    function filtrarTabla() {
                        const filtroProveedor = document.getElementById('filtroProveedor').value.toLowerCase();
                        const filtroFecha = document.getElementById('filtroFecha').value;
                        const filtroTipoTransaccion = document.getElementById('filtroTipoTransaccion').value;
                        const filasGrupo = document.querySelectorAll('tr.font-weight-bold.align-middle');

                        filasGrupo.forEach(grupoFila => {
                            const proveedor = grupoFila.cells[4]?.textContent.toLowerCase() || '';
                            const fechaTexto = grupoFila.cells[5]?.textContent.trim() || '';
                            const tipotransaccion = grupoFila.cells[6]?.textContent.trim() || '';

                            const partesFecha = fechaTexto.split('/');
                            const fechaFormateada = partesFecha.length === 3
                                ? `${partesFecha[2]}-${partesFecha[1]}-${partesFecha[0]}`
                                : '';

                            const coincideProveedor = proveedor.includes(filtroProveedor);
                            const coincideFecha = !filtroFecha || filtroFecha === fechaFormateada;
                            const coincideTipoTransaccion = !filtroTipoTransaccion || tipotransaccion.includes(filtroTipoTransaccion);

                            const mostrar = coincideProveedor && coincideFecha && coincideTipoTransaccion;
                            grupoFila.style.display = mostrar ? '' : 'none';

                            const collapseId = grupoFila.querySelector('[data-target]')?.getAttribute('data-target')?.replace('#', '');
                            if (collapseId) {
                                const filasDetalle = document.querySelectorAll(`tr#${collapseId}`);
                                filasDetalle.forEach(fila => {
                                    fila.style.display = mostrar ? '' : 'none';
                                });
                            }
                        });
                    }
                    function limpiarFiltros() {
                        document.getElementById('filtroProveedor').value = '';
                        document.getElementById('filtroFecha').value = '';
                        document.getElementById('filtroTipoTransaccion').value = '';
                        filtrarTabla();
                    }
                </script>
            </div>
        </div>
    </div>

    <!-- Modal -->
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
                location.reload(); // o actualiza la tabla dinámicamente
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

