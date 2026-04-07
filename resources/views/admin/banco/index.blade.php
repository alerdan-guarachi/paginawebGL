@extends('adminlte::page')

@section('content_header')
<h1>CONSOLIDADO GENERAL</h1>
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
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    CONSOLIDADO POR FECHA
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    CONSOLIDADO DIARIO
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1"> 
                {{-- <form method="GET" action="{{ route('admin.banco.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="fecha_desde">Fecha Desde:</label>
                            <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_hasta">Fecha Hasta:</label>
                            <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="proveedoratencion">Proveedor Atención:</label>
                            <input type="text" name="proveedoratencion" class="form-control" value="{{ request('proveedoratencion') }}" placeholder="Nombre del proveedor">
                        </div>

                        <div class="col-md-3">
                            <label for="clientenombre">Cliente:</label>
                            <input type="text" name="clientenombre" class="form-control" value="{{ request('clientenombre') }}" placeholder="Nombre del cliente">
                        </div>

                        <div class="col-md-3">
                            <label for="tipotransaccion">Tipo de Transacción:</label>
                            <select name="tipotransaccion" class="form-control">
                                <option value="">TODOS</option>
                                <option value="ATC" {{ request('tipotransaccion') == 'ATC' ? 'selected' : '' }}>ATC</option>
                                <option value="CHEQUE" {{ request('tipotransaccion') == 'CHEQUE' ? 'selected' : '' }}>CHEQUE</option>
                                <option value="DEPOSITO BANCARIO" {{ request('tipotransaccion') == 'DEPOSITO BANCARIO' ? 'selected' : '' }}>DEPOSITO BANCARIO</option>
                                <option value="EFECTIVO" {{ request('tipotransaccion') == 'EFECTIVO' ? 'selected' : '' }}>EFECTIVO</option>
                                <option value="TRANSFERENCIA BANCARIA" {{ request('tipotransaccion') == 'TRANSFERENCIA BANCARIA' ? 'selected' : '' }}>TRANSFERENCIA BANCARIA</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="tipomovimiento">Tipo de Movimiento:</label>
                            <select name="tipomovimiento" class="form-control">
                                <option value="">TODOS</option>
                                <option value="INGRESO" {{ request('tipomovimiento') == 'INGRESO' ? 'selected' : '' }}>INGRESO</option>
                                <option value="EGRESO" {{ request('tipomovimiento') == 'EGRESO' ? 'selected' : '' }}>EGRESO</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </div>
                    </div>
                </form> --}}
                <form method="GET" action="{{ route('admin.banco.index') }}" class="p-3 bg-light rounded shadow-sm">
                    <div class="row align-items-center">
                        <!-- Card Por Fecha -->
                        <div class="col-lg-4">
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
                
                        <!-- Card Por Persona -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="proveedoratencion" class="form-label">Proveedor:</label>
                                            <select name="proveedoratencion" class="form-control">
                                                <option value="">TODOS</option>
                                                @foreach($proveedores as $proveedor)
                                                    <option value="{{ $proveedor->proveedoratencion }}" 
                                                        {{ request('proveedoratencion') == $proveedor->proveedoratencion ? 'selected' : '' }}>
                                                        {{ $proveedor->proveedoratencion }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-lg-6">
                                            <label for="clientenombre" class="form-label">Cliente:</label>
                                            <input type="text" name="clientenombre" class="form-control" value="{{ request('clientenombre') }}" placeholder="TODOS">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <!-- Card Tipo Transacción -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="proveedoratencion" class="form-label">Transacción:</label>
                                            <select name="tipotransaccion" class="form-control">
                                                <option value="">TODOS</option>
                                                <option value="ATC" {{ request('tipotransaccion') == 'ATC' ? 'selected' : '' }}>ATC</option>
                                                <option value="CHEQUE" {{ request('tipotransaccion') == 'CHEQUE' ? 'selected' : '' }}>CHEQUE</option>
                                                <option value="DEPOSITO BANCARIO" {{ request('tipotransaccion') == 'DEPOSITO BANCARIO' ? 'selected' : '' }}>DEPÓSITO BANCARIO</option>
                                                <option value="EFECTIVO" {{ request('tipotransaccion') == 'EFECTIVO' ? 'selected' : '' }}>EFECTIVO</option>
                                                <option value="TRANSFERENCIA BANCARIA" {{ request('tipotransaccion') == 'TRANSFERENCIA BANCARIA' ? 'selected' : '' }}>TRANSFERENCIA BANCARIA</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6">
                                            <label for="proveedoratencion" class="form-label">Movimiento:</label>
                                            <select name="tipomovimiento" class="form-control">
                                                <option value="">TODOS</option>
                                                <option value="INGRESO" {{ request('tipomovimiento') == 'INGRESO' ? 'selected' : '' }}>INGRESO</option>
                                                <option value="EGRESO" {{ request('tipomovimiento') == 'EGRESO' ? 'selected' : '' }}>EGRESO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Otros -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label for="cuenta" class="form-label">Cuenta/Banco:</label>
                                            <select name="cuenta" class="form-control">
                                                <option value="">TODOS</option>
                                                @foreach ($cuentas as $cuenta)
                                                    <option value="{{ $cuenta->numerocuenta }}" {{ request('cuenta') == $cuenta->numerocuenta ? 'selected' : '' }}>
                                                        {{ $cuenta->numerocuenta }} - {{ $cuenta->sigla }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-lg-4">
                                            <label for="ciudad" class="form-label">Ciudad:</label>
                                            <select name="ciudad" class="form-control">
                                                <option value="">TODOS</option>
                                                <option value="SANTA CRUZ" {{ request('ciudad') == 'SANTA CRUZ' ? 'selected' : '' }}>SANTA CRUZ</option>
                                                <option value="COCHABAMBA" {{ request('ciudad') == 'COCHABAMBA' ? 'selected' : '' }}>COCHABAMBA</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="estado" class="form-label">Estado:</label>
                                            <select name="estado" class="form-control">
                                                <option value="">TODOS</option>
                                                <option value="PAGO PROCESADO" {{ request('estado') == 'PAGO PROCESADO' ? 'selected' : '' }}>PAGO PROCESADO</option>
                                                <option value="SALDO PENDIENTE" {{ request('estado') == 'SALDO PENDIENTE' ? 'selected' : '' }}>SALDO PENDIENTE</option>
                                            </select>
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
                
                {{-- <div class="card-body p-2">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <label for="cuenta" class="form-label">Comparativa con planilla de Banco:</label>
                        <input type="file" name="archivo" accept=".csv" id="archivo">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">COMPARAR</button>
                
                        @if (isset($datos) && count($datos) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        @foreach ($encabezados as $encabezado)
                                            <th>{{ $encabezado }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datos as $fila)
                                        <tr>
                                            @foreach ($fila as $dato)
                                                <td>{{ $dato }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8"><strong>Total:</strong></td>
                                        <td><strong class="total">{{ number_format($total, 2, '.', ',') }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @endif
                    </form>
                </div> --}}
                @php           
                    $total = 0;
                @endphp
                <input type="hidden" id="totalMontoHidden" name="totalMonto" value="{{ number_format($total, 2, '.', ',') }}">

                <div id="estadoConsolidado" class="text-center w-100 p-2 rounded" style="display: none;">
                    <strong id="estadoTexto"></strong>
                </div>
                
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    $(document).ready(function() {
                        $('#uploadForm').on('submit', function(e) {
                            e.preventDefault();
                            var formData = new FormData(this);
                
                            $.ajax({
                                url: "{{ route('upload.excel') }}",
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    var nuevoContenido = $(response).find('#uploadForm').closest('.card-body').html();
                                    $('#uploadForm').closest('.card-body').html(nuevoContenido);
                                    var nuevoTotalMonto = parseFloat($(response).find('.total-mov').text().replace(/,/g, '')) || 0;
                                    var nuevoTotal = parseFloat($(response).find('.total').text().replace(/,/g, '')) || 0;
                                    var totalMonto = parseFloat($('#totalMontoHidden').val().replace(/,/g, '')) || 0;
                                    var estadoConsolidado = (totalMonto === nuevoTotal) ? "CONSOLIDADO" : "DISCREPADO";
                                    var estadoColor = (totalMonto === nuevoTotal) ? "green" : "red";

                                    $('#estadoConsolidado')
                                        .css("background-color", estadoColor)
                                        .show();
                
                                    $('#estadoTexto').html(`<strong>${estadoConsolidado}</strong>`);
                                },
                                error: function(xhr, status, error) {
                                    alert("Hubo un error al cargar el archivo.");
                                }
                            });
                        });
                    });
                </script>
                
                @if($detalles->isEmpty())
                <div class="alert alert-danger text-center" role="alert">
                    <strong>No hay resultados para la búsqueda</strong>
                </div>
                
                @else

                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="table table-striped">
                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #dcdcdc;">
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
                                                {{ $detalle->sucursal_origen ?? '0' }}
                                            @elseif ($detalle->tipomovimiento === 'EGRESO')
                                                @if ($detalle->area === 'MEDICA')
                                                    @if (!empty($detalle->proveedores_ciudad2))
                                                        {{-- Si ciudad2 existe, usamos sucursalgasto --}}
                                                        {{ $detalle->sucursalgasto ?? $detalle->cajacentral_ciudadregistro }}
                                                    @else
                                                        {{-- Si ciudad2 no existe, usamos ciudad del proveedor --}}
                                                        {{ $detalle->sucursal_origen ?? $detalle->cajacentral_ciudadregistro }}
                                                    @endif
                                                @else
                                                    {{-- Para otros tipos de cliente usamos sucursalgasto o ciudad registro --}}
                                                    {{ $detalle->sucursalgasto ?? $detalle->cajacentral_ciudadregistro }}
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td title="{{ $detalle->tipotransaccion }}" class="truncar">{{ $detalle->tipotransaccion }}</td>
                                        <td>{{ $detalle->subtotal + $detalle->cajacentral_diferenciafavor - $detalle->cajacentral_diferenciacontra }}</td>
                                        <td>{{ $detalle->descuento }}</td>
                                        <td>{{ $detalle->montototal + $detalle->cajacentral_diferenciafavor - $detalle->cajacentral_diferenciacontra }}</td>
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
                <div style="margin-top: 20px;">
                    {{ $detalles->links() }}
                </div>
                @endif                
            </div>

            {{-- INGRESOS POR FECHA --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">  
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th class="text-center">Ingreso_Programado</th>
                                <th class="text-center">Ingreso_Fecha_Actual</th>
                                <th class="text-center">Ingreso_Otras_Fechas</th>
                                <th class="text-center">Ingreso_Info_Final</th>
                                <th class="text-center">Total_Ingreso</th>
                                <th class="text-center">Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                $totales = [];
                                foreach ($consolidacion as $prog) {
                                    $fecha_asignada = $prog->fechaasignada;
                                    if (!isset($totales[$fecha_asignada])) {
                                        $totales[$fecha_asignada] = [
                                            'total_programado'         => 0,
                                            'total_recibido'           => 0,
                                            'total_recibido_otras'     => 0,
                                            'total_recibido_infofinal' => 0
                                        ];
                                    }
                                    $totales[$fecha_asignada]['total_programado'] += $prog->total_programado;
                                }
                                foreach ($recibos as $pago) {
                                    $pago_fecha = $pago->fecha;
                                    $programacion_id = $pago->programacionid;
                                    $fecha_asignada = null;
                                    foreach ($consolidacion as $prog) {
                                        if ($prog->id == $programacion_id) {
                                            $fecha_asignada = $prog->fechaasignada;
                                            break;
                                        }
                                    }
                                    if (!$fecha_asignada) {
                                        $fecha_asignada = $pago_fecha;
                                    }
                                    if (!isset($totales[$pago_fecha])) {
                                        $totales[$pago_fecha] = [
                                            'total_programado'         => 0,
                                            'total_recibido'           => 0,
                                            'total_recibido_otras'     => 0,
                                            'total_recibido_infofinal' => 0
                                        ];
                                    }
                                    if ($pago_fecha == $fecha_asignada) {
                                        if (($pago->area ?? null) === 'MEDICA') {
                                            $totales[$pago_fecha]['total_recibido'] += $pago->total_recibido;
                                        }
                                    } else {
                                        $totales[$pago_fecha]['total_recibido_otras'] += $pago->total_recibido;
                                    }
                                }
                                foreach ($totales as $fecha => &$data) {
                                    $data['total_recibido_infofinal'] = DB::table('detallerecibos')
                                        ->where('area', 'INFORME FINAL')
                                        ->where('tipomovimiento', 'INGRESO')
                                        ->whereDate('created_at', $fecha)
                                        ->sum('montototal');
                                }
                                unset($data);
                                ksort($totales);
                                $fechas = array_reverse(array_keys($totales));
                            @endphp
                            
                            @foreach($fechas as $index => $fecha)
                                @php
                                    $data = $totales[$fecha];
                                @endphp
                                <tr class="{{ \Carbon\Carbon::parse($fecha)->isToday() ? 'bg-light-green' : '' }}">
                                    <td>{{ $fecha }}</td>
                                    <td class="text-center">
                                        <strong>{{ number_format($data['total_programado'], 2, '.', '') }}</strong>
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($data['total_recibido'], 2, '.', '') }}
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($data['total_recibido_otras'], 2, '.', '') }}
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($data['total_recibido_infofinal'], 2, '.', '') }}
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ number_format($data['total_recibido'] + $data['total_recibido_otras'] + $data['total_recibido_infofinal'], 2, '.', '') }}</strong>
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-veringresos" data-toggle="modal" data-target="#modalProgramacion_{{ $fecha }}_{{ $index }}" data-fecha="{{ $fecha }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="modal fade" id="modalProgramacion_{{ $fecha }}_{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="modalProgramacionLabel_{{ $fecha }}_{{ $index }}" aria-hidden="true">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" style="font-weight: 900" id="modalProgramacionLabel_{{ $fecha }}_{{ $index }}">
                                                            PROGRAMACIONES E INGRESOS DEL: {{ $fecha }}
                                                        </h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <strong>PROGRAMACIONES</strong>
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID_Prog.</th>
                                                                        <th>ID_Cli.</th>
                                                                        <th>Cliente</th>
                                                                        <th>Estudio/Especialidad</th>
                                                                        <th>Proveedor</th>
                                                                        <th>Fecha_Asig.</th>
                                                                        <th>Fecha_Pago</th>
                                                                        <th>Precio</th>
                                                                        <th>Usu_Reg.</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($programaciones->where('fechaasignada', $fecha) as $prog)
                                                                    <tr>
                                                                        <td>{{ $prog->id }}</td>
                                                                        <td>{{ $prog->clienteitaid }}{{ $prog->clienteauditoriaid }}{{ $prog->clientecomunid }}</td>
                                                                        <td title="{{ $prog->clienteitanombre }}{{ $prog->clienteauditorianombre }}{{ $prog->clientecomunnombre }}" class="truncar">
                                                                            {{ $prog->clienteitanombre }}{{ $prog->clienteauditorianombre }}{{ $prog->clientecomunnombre }}
                                                                        </td>
                                                                        <td title="{{ $prog->accionnombre }}" class="truncar">{{ $prog->accionnombre }}</td>
                                                                        <td title="{{ $prog->proveedornombre }}" class="truncar">{{ $prog->proveedornombre }}</td>
                                                                        <td>{{ $prog->fechaasignada }}</td>
                                                                        <td>{{ $prog->fechapago  ?? '0' }}</td>
                                                                        <td>{{ $prog->precio }}</td>
                                                                        <td title="{{ $prog->usuarioregistro }}" class="truncar">{{ $prog->usuarioregistro }}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <strong>INGRESOS</strong>
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID_Ing.</th>
                                                                        <th>ID_Reg.</th>
                                                                        <th>ID_Cli.</th>
                                                                        <th>Cliente</th>
                                                                        <th>Estudio/Especialidad</th>
                                                                        <th>Proveedor</th>
                                                                        <th>Fecha_Aten.</th>
                                                                        <th>Fecha_Pago</th>
                                                                        <th>Tipo_Transac.</th>
                                                                        <th>Subtotal</th>
                                                                        <th>Desc.</th>
                                                                        <th>Monto_Total</th>
                                                                        <th>ID_Rec.</th>
                                                                        <th>Usu_Reg.</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($recibos->where('fechaatencion', $fecha)->merge($recibos->where('fecha', $fecha))->unique('id') as $recibo)
                                                                    <tr class="{{ 
                                                                        ($recibo->detalle == 'INFORME FINAL') ? 'bg-light-purple' : 
                                                                        ($recibo->fechaatencion != $recibo->fecha ? 'bg-light-yellow' : '') 
                                                                    }}">
                                                                        <td>{{ $recibo->id }}</td>
                                                                        <td>{{ $recibo->programacionid }}{{ $recibo->provinfofinalid }}</td>
                                                                        <td>{{ $recibo->clienteid }}</td>
                                                                        <td title="{{ $recibo->clientenombre }}" class="truncar">{{ $recibo->clientenombre }}</td>
                                                                        <td title="{{ $recibo->detalle }}" class="truncar">{{ $recibo->detalle }}</td>
                                                                        <td title="{{ $recibo->proveedoratencion }}" class="truncar">{{ $recibo->proveedoratencion }}</td>
                                                                        {{-- <td>{{ $recibo->fechaatencion ?? '---------------' }}</td> --}}
                                                                        <td>
                                                                            @if ($recibo->detalle == 'INFORME FINAL')
                                                                                {{ $recibo->fecha_informe_final ?? '---------------' }}
                                                                            @else
                                                                                {{ $recibo->fechaatencion ?? '---------------' }}
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $recibo->fecha }}</td>
                                                                        <td title="{{ $recibo->tipotransaccion }}" class="truncar">{{ $recibo->tipotransaccion }}</td>
                                                                        <td>{{ $recibo->subtotal }}</td>
                                                                        <td>{{ $recibo->descuento }}</td>
                                                                        <td>
                                                                            {{ number_format($recibo->montototal, 2) }} 
                                                                            @if (!is_null($recibo->descuentoatc) && $recibo->descuentoatc > 0)
                                                                                - {{ number_format($recibo->descuentoatc, 2) }} = {{ number_format($recibo->montototal - $recibo->descuentoatc, 2) }}
                                                                            @endif
                                                                        </td>
                                                                                                                                                                                                                                                            
                                                                        <td>{{ $recibo->reciboid }}</td>
                                                                        <td title="{{ $recibo->usuarioregistronombre }}" class="truncar">{{ $recibo->usuarioregistronombre }}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
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
    </div>
</div>

@stop

                {{-- <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th class="text-center">Ingreso_Programado</th>
                                <th class="text-center">Ingreso_Mes_Actual</th>
                                <th class="text-center">Ingreso_Info_Final</th>
                                <th class="text-center">Total_Ingreso</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($meses as $index => $mes)
                                @php
                                    $data = $consolidadoMes[$mes];
                                    $totalIngreso = $data['total_ingresado'] + $data['total_informe_final'];
                                @endphp
                                <tr class="{{ \Carbon\Carbon::parse($mes.'-01')->isCurrentMonth() ? 'bg-light-green' : '' }}">
                                    <td>{{ strtoupper(\Carbon\Carbon::parse($mes . '-01')->translatedFormat('F Y')) }}</td>
                                    <td class="text-center"><strong>{{ number_format($data['total_programado'], 2, '.', '') }}</strong></td>
                                    <td class="text-center">{{ number_format($data['total_ingresado'], 2, '.', '') }}</td>
                                    <td class="text-center">{{ number_format($data['total_informe_final'], 2, '.', '') }}</td>
                                    <td class="text-center"><strong>{{ number_format($totalIngreso, 2, '.', '') }}</strong></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-veringresos" data-toggle="modal" data-target="#modalMes_{{ $mes }}_{{ $index }}" data-mes="{{ $mes }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="modal fade" id="modalMes_{{ $mes }}_{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="modalMesLabel_{{ $mes }}_{{ $index }}" aria-hidden="true">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title font-weight-bold" id="modalMesLabel_{{ $mes }}_{{ $index }}">
                                                            PROGRAMACIONES E INGRESOS DEL MES DE {{ strtoupper(\Carbon\Carbon::parse($mes . '-01')->translatedFormat('F Y')) }}
                                                        </h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <strong>PROGRAMACIONES DETALLADAS</strong>
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID_Prog.</th>
                                                                        <th>ID_Cli.</th>
                                                                        <th>Cliente</th>
                                                                        <th>Estudio/Especialidad</th>
                                                                        <th>Proveedor</th>
                                                                        <th>Fecha_Asig.</th>
                                                                        <th>Fecha_Pago</th>
                                                                        <th>Precio</th>
                                                                        <th>Usu_Reg.</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if(isset($detallesProgPorMes[$mes]))
                                                                        @foreach($detallesProgPorMes[$mes] as $prog)
                                                                        <tr>
                                                                            <td>{{ $prog->id }}</td>
                                                                            <td>{{ $prog->clienteitaid }}{{ $prog->clienteauditoriaid }}{{ $prog->clientecomunid }}</td>
                                                                            <td title="{{ $prog->clienteitanombre }}{{ $prog->clienteauditorianombre }}{{ $prog->clientecomunnombre }}" class="truncar">
                                                                                {{ $prog->clienteitanombre }}{{ $prog->clienteauditorianombre }}{{ $prog->clientecomunnombre }}
                                                                            </td>
                                                                            <td title="{{ $prog->accionnombre }}" class="truncar">{{ $prog->accionnombre }}</td>
                                                                            <td title="{{ $prog->proveedornombre }}" class="truncar">{{ $prog->proveedornombre }}</td>
                                                                            <td>{{ $prog->fechaasignada }}</td>
                                                                            <td>{{ $prog->fechapago ?? '0' }}</td>
                                                                            <td>{{ $prog->precio }}</td>
                                                                            <td title="{{ $prog->usuarioregistro }}" class="truncar">{{ $prog->usuarioregistro }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="8" class="text-center">No hay programaciones para este mes.</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <strong>INGRESOS DETALLADOS</strong>
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID_Ing.</th>
                                                                        <th>ID_Reg.</th>
                                                                        <th>ID_Cli.</th>
                                                                        <th>Cliente</th>
                                                                        <th>Estudio/Especialidad</th>
                                                                        <th>Proveedor</th>
                                                                        <th>Fecha_Aten.</th>
                                                                        <th>Fecha_Pago</th>
                                                                        <th>Tipo_Transac.</th>
                                                                        <th>Subtotal</th>
                                                                        <th>Desc.</th>
                                                                        <th>Monto_Total</th>
                                                                        <th>ID_Rec.</th>
                                                                        <th>Usu_Reg.</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if(isset($detallesIngPorMes[$mes]))
                                                                        @foreach($detallesIngPorMes[$mes] as $ing)
                                                                        <tr class="{{ 
                                                                            ($ing->detalle == 'INFORME FINAL') 
                                                                                ? 'bg-light-purple' 
                                                                                : (\Carbon\Carbon::parse($ing->fechaatencion)->format('Y-m') != $mes ? 'bg-light-yellow' : '')
                                                                        }}">
                                                                        
                                                                            <td>{{ $ing->id }}</td>
                                                                            <td>{{ $ing->programacionid }}{{ $ing->provinfofinalid }}</td>
                                                                            <td>{{ $ing->clienteid }}</td>
                                                                            <td title="{{ $ing->clientenombre }}" class="truncar">{{ $ing->clientenombre }}</td>
                                                                            <td title="{{ $ing->detalle }}" class="truncar">{{ $ing->detalle }}</td>
                                                                            <td title="{{ $ing->proveedoratencion }}" class="truncar">{{ $ing->proveedoratencion }}</td>
                                                                            <td>{{ $ing->fechaatencion ?? '---------------' }}</td>
                                                                            <td>{{ $ing->fecha }}</td>
                                                                            <td title="{{ $ing->tipotransaccion }}" class="truncar">{{ $ing->tipotransaccion }}</td>
                                                                            <td>{{ $ing->subtotal }}</td>
                                                                            <td>{{ $ing->descuento }}</td>
                                                                            <td>
                                                                                {{ number_format($ing->montototal, 2) }} 
                                                                                @if (!is_null($ing->descuentoatc))
                                                                                    - {{ number_format($ing->descuentoatc, 2) }} = {{ number_format($ing->montototal - $ing->descuentoatc, 2) }}
                                                                                @endif
                                                                            </td>                                                                                                                                                                                         
                                                                            <td>{{ $ing->reciboid }}</td>
                                                                            <td title="{{ $ing->usuarioregistronombre }}" class="truncar">{{ $ing->usuarioregistronombre }}</td>
                                                                        </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="14" class="text-center">No hay ingresos para este mes.</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div><!-- /.modal-body -->
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> --}}
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
@endsection