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
        }, 3000);
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
            <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    CXP OTROS PROVEEDORES
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
                                                                        <div class="card-header text-center border-0 position-relative" style="background: #fffbf3; padding: 0.3rem;">
                                                                            <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 3000189269</h6>
                                                                        </div>
                                                                        <div class="card-body" style="text-align: left;">
                                                                            <div class="d-flex justify-content-between" style="margin-top: -15px; margin-bottom: -15px;">
                                                                                <h6 class="font-weight-bold">SALDO:</h6>
                                                                                <p class="h5 fw-bold text-success">{{ number_format($saldoanteriorcuenta1 + $totalCuenta1Ingreso - $totalCuenta1Egreso, 2, '.', '') }} <span class="text-muted">Bs.</span></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- Tarjeta Cuenta 2 -->
                                                                <div class="col-md-6 mb-4">
                                                                    <div class="card shadow-lg border-0" style="color: #495057; border-radius: 15px; overflow: hidden; position: relative;">
                                                                        <div class="card-header text-center border-0 position-relative" style="background: #fffbf3; padding: 0.3rem;">
                                                                            <h6 class="mt-2 text-uppercase font-weight-bold">BNB: N° Cuenta: 2505314878</h6>
                                                                        </div>
                                                                        <div class="card-body" style="text-align: left;">
                                                                            <div class="d-flex justify-content-between" style="margin-top: -15px; margin-bottom: -15px;">
                                                                                <h6 class="font-weight-bold">SALDO:</h6>
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
                                                            <h5 style="font-weight:900;">CUENTAS POR PAGAR</h5>
                                                            <input type="hidden" id="fechaSeleccionada{{ Str::slug($fechaasignada) }}" value="{{ $fechaasignada }}">
                                                            @php
                                                            $cuentasFiltradas = $cuentas->whereIn('estado', ['PENDIENTE', 'SALDO PENDIENTE'])
                                                                                        ->where('estadoaprobacion', '!=', 'RECHAZADO');
                                                            
                                                            $cuentasAgrupadas = $cuentasFiltradas->groupBy(function ($item) {
                                                                $proveedorNombre = strtoupper(trim($item->proveedornombre));
                                                            
                                                                if ($proveedorNombre === 'TELEFONICA CELULAR DE BOLIVIA S.A.') {
                                                                // Buscar código
                                                                    if (preg_match('/NRO\. CODIGO (\d+)/i', $item->detalleproducto, $matches)) {
                                                                        return $proveedorNombre . ' - CODIGO ' . $matches[1];
                                                                    }
                                                                    // Buscar contrato
                                                                    if (preg_match('/NRO\. CONTRATO (\d+)/i', $item->detalleproducto, $matches)) {
                                                                        return $proveedorNombre . ' - CONTRATO ' . $matches[1];
                                                                    }
                                                            
                                                                    // Si no hay contrato ni código
                                                                    return $proveedorNombre;
                                                                }

                                                                if ($proveedorNombre === 'BANCO NACIONAL DE BOLIVIA S.A.') {
                                                                    if (preg_match('/GARANTIA\ HIPOTECARIA (\d+)/i', $item->detalleproducto, $matches)) {
                                                                        return $proveedorNombre . ' - CREDITO ' . $matches[1];
                                                                    }
                                                                    if (preg_match('/VIVIENDA\ PYME (\d+)/i', $item->detalleproducto, $matches)) {
                                                                        return $proveedorNombre . ' - CREDITO ' . $matches[1];
                                                                    }
                                                                    if (preg_match('/CONSUMO\ ASALARIADO (\d+)/i', $item->detalleproducto, $matches)) {
                                                                        return $proveedorNombre . ' - CREDITO ' . $matches[1];
                                                                    }
                                                            
                                                                    // Si no hay contrato ni código
                                                                    return $proveedorNombre;
                                                                }
                                                            
                                                                return $item->proveedornombre;
                                                            });
                                                            @endphp

                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="padding: 5px 10px; width: 5%; background-color:#f6faee"><input type="checkbox" class="select-all-cuentas" data-fecha1="{{ Str::slug($fechaasignada) }}">Aprob</th>
                                                                            <th style="padding: 5px 10px; width: 5%; background-color:#f6faee">Ver</th>
                                                                            <th style="padding: 5px 10px; width: 50%; background-color:#f6faee">Proveedor</th>
                                                                            <th style="padding: 5px 10px; width: 15%; background-color:#f6faee">Tipo Planilla</th>
                                                                            <th style="padding: 5px 10px; width: 15%; background-color:#f6faee">Banco Destino</th>
                                                                            <th style="padding: 5px 10px; width: 10%; background-color:#f6faee" class="text-right">Monto Total</th>
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
                                                                            $bancodestino = optional($primerRegistro->proveedorServicio)->numcuenta ?? 'NO DEFINIDO';
                                                                            $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $proveedor)->where('fechapago', $fechaasignada)->first();
                                                                            $archivoExistentefijo = \App\Models\ProveedoresServicios::where('razonsocial', $proveedor)->first();
                                                                        @endphp
                                                                            <tr class="font-weight-bold" style="background-color: #f7f7f7;">
                                                                                <td>
                                                                                    @if ($todosAprobados)
                                                                                        <span class="badge badge-success">APROBADO</span>
                                                                                    @else
                                                                                        <input type="checkbox" class="select-group-cuentas" data-group="{{ Str::slug($proveedor, '-') }}" data-fecha1="{{ Str::slug($fechaasignada) }}">

                                                                                        @php
                                                                                            $anteriores = \App\Models\CuentasPagar::where('proveedornombre', $proveedor)
                                                                                                ->where('fechaasignada', '<', $fechaasignada)
                                                                                                ->where('estadoaprobacion', 'RECHAZADO')
                                                                                                ->pluck('fechaasignada')
                                                                                                ->map(function ($fecha) {
                                                                                                    return \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                                                                                })
                                                                                                ->unique()
                                                                                                ->implode(', ');
                                                                                        @endphp

                                                                                        @if (!empty($anteriores))
                                                                                            <i class="fas fa-exclamation-triangle text-warning float-end"
                                                                                            title="Existen cuentas por pagar en mora de fecha(s): {{ $anteriores }}"></i>
                                                                                        @endif
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
                                                                                <td>{{ $bancodestino }}</td>
                                                                                <td class="text-right">
                                                                                    {{ number_format($registros->sum('montototal'), 2) }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="collapse" id="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                <td colspan="6" class="p-0">
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
                                                                                                                <input type="checkbox" class="select-item-cuentas select-c-{{ Str::slug($proveedor, '-') }}" data-id="{{ $pendiente->id }}" data-total="{{ $pendiente->montototal }}" data-fecha1="{{ $pendiente->fechaasignada }}" data-fecha1-modal="{{ Str::slug($fechaasignada) }}" data-nrocuenta="{{ $pendiente->nrobancoorigen }}">
                                                                                                                @if ($pendiente->estadoaprobacion === 'SUGERIDO')
                                                                                                                    <span class="badge badge-primary">SUGERIDO</span>
                                                                                                                @endif
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
                                                            <h5 style="font-weight:900;">PROVEEDORES MEDICOS</h5>
                                                            <div class="table-responsive" style="overflow-x: auto;">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="padding: 5px 10px; width: 5%; background-color:#f6faee"><input type="checkbox" class="select-all-programaciones" data-fecha1="{{ Str::slug($fechaasignada) }}">Aprob</th>
                                                                            <th style="padding: 5px 10px; width: 5%; background-color:#f6faee">Ver</th>
                                                                            <th style="padding: 5px 10px; width: 50%; background-color:#f6faee">Proveedor</th>
                                                                            <th style="padding: 5px 10px; width: 15%; background-color:#f6faee">Tipo Planilla</th>
                                                                            <th style="padding: 5px 10px; width: 15%; background-color:#f6faee">Banco Destino</th>
                                                                            <th style="padding: 5px 10px; width: 10%; background-color:#f6faee" class="text-right">Monto Total</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($registrosbateria->where('fechapago', $fechaasignada)->where('prioridad', 'CUENTA POR PAGAR')->where('estadoaprobacion','!=','RECHAZADO')->groupBy('proveedorasignado') as $proveedor => $registros)
                                                                            @php
                                                                                $todosAprobados = $registros->every(function($r) {
                                                                                    return in_array($r->estadoaprobacion, ['APROBADO', 'CARGADO', 'SUBIDO']);
                                                                                });
                                                                                $tipoplanilla = $proveedoresServicios[$proveedor] ?? 'NO DEFINIDO';
                                                                                $bancodestino = $proveedoresServicioscuenta[$proveedor] ?? 'NO DEFINIDO';
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
                                                                                        <input type="checkbox" class="select-group-programaciones" data-group="{{ Str::slug($proveedor, '-') }}" data-fecha1="{{ Str::slug($fechaasignada) }}">

                                                                                        @php
                                                                                            $anteriores2 = \App\Models\Bateriasubcliente::where('proveedorasignado', $proveedor)
                                                                                                ->where('fechapago', '<', $fechaasignada)
                                                                                                ->where('estadoaprobacion', 'RECHAZADO')
                                                                                                ->pluck('fechapago')
                                                                                                ->map(function ($fecha) {
                                                                                                    return \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                                                                                })
                                                                                                ->unique()
                                                                                                ->implode(', ');
                                                                                        @endphp

                                                                                        @if (!empty($anteriores2))
                                                                                            <i class="fas fa-exclamation-triangle text-warning float-end"
                                                                                            title="Existen cuentas por pagar en mora de fecha(s): {{ $anteriores2 }}"></i>
                                                                                        @endif
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
                                                                                <td>{{ $bancodestino }}</td>
                                                                                <td class="text-right">
                                                                                    {{ number_format($registros->sum('preciocompra'), 2) }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="collapse" id="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                <td colspan="6" class="p-0">
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
                                                                                                                    data-fecha1="{{ $registro->fechapago }}" 
                                                                                                                    data-fecha1-modal="{{ Str::slug($fechaasignada) }}" 
                                                                                                                    data-nrocuenta="{{ $registro->nrobancoorigen }}">
                                                                                                                    @if ($registro->estadoaprobacion === 'SUGERIDO')
                                                                                                                        <span class="badge badge-primary">SUGERIDO</span>
                                                                                                                    @endif
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
                                                        @can('admin.cuentaspagar.sugerirpagos')
                                                            <button class="btn btn-sm btn-outline-primary mb-2" onclick="sugerirpagosSeleccionados2('{{ Str::slug($fechaasignada) }}')">SUGERIR PAGOS</button>
                                                        @endcan
                                                    </div>
                                                    <div class="table-responsive">
                                                        <p style="width: 100%; text-align: center; color: #059232; pargb(20, 79, 26)10px; border-radius: 5px; font-style: italic; font-weight: bold;">
                                                            AL PRESIONAR "APROBAR CXP", LOS PAGOS QUE NO ESTÉN SELECCIONADOS AUTOMÁTICAMENTE SE MARCARÁN COMO "RECHAZADO"
                                                        </p>
                                                    </div>
                                                </div>
                                                <script>
                                                    function updateTotalByCuenta(fechaModal) {
                                                        // Reiniciar todos los totales visibles a 0.00
                                                        document.querySelectorAll(`span[id^="total-cuentas-${fechaModal}-"]`).forEach(span => {
                                                            span.textContent = "0.00";
                                                        });

                                                        let totales = {};

                                                        document.querySelectorAll(`.select-item-cuentas[data-fecha1-modal="${fechaModal}"]`).forEach(item => {
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
                                                            const fechaModal = this.getAttribute('data-fecha1');
                                                            let checked = this.checked;
                                                            document.querySelectorAll(`.select-item-cuentas[data-fecha1-modal="${fechaModal}"], .select-group-cuentas[data-fecha1="${fechaModal}"]`).forEach(cb => cb.checked = checked);
                                                            updateTotalByCuenta(fechaModal);
                                                        });
                                                    });

                                                    // Grupo por proveedor
                                                    document.querySelectorAll('.select-group-cuentas').forEach(groupCheckbox => {
                                                        groupCheckbox.addEventListener('change', function() {
                                                            const fechaModal = this.getAttribute('data-fecha1');
                                                            let group = this.dataset.group;
                                                            document.querySelectorAll(`.select-c-${group}[data-fecha1-modal="${fechaModal}"]`).forEach(cb => cb.checked = this.checked);
                                                            updateTotalByCuenta(fechaModal);
                                                        });
                                                    });

                                                    // Ítem individual
                                                    document.querySelectorAll('.select-item-cuentas').forEach(item => {
                                                        item.addEventListener('change', function() {
                                                            const fechaModal = this.getAttribute('data-fecha1-modal');
                                                            updateTotalByCuenta(fechaModal);
                                                        });
                                                    });
                                                    
                                                    function updateTotalByCuenta2(fechaModal) {
                                                        // Reiniciar todos los totales visibles a 0.00
                                                        document.querySelectorAll(`span[id^="total-programaciones-${fechaModal}-"]`).forEach(span => {
                                                            span.textContent = "0.00";
                                                        });

                                                        let totales = {};

                                                        document.querySelectorAll(`.select-item-programaciones[data-fecha1-modal="${fechaModal}"]`).forEach(item => {
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
                                                            const fechaModal = this.getAttribute('data-fecha1');
                                                            const checked = this.checked;

                                                            document.querySelectorAll(`.select-item-programaciones[data-fecha1="${fechaModal}"], .select-group-programaciones[data-fecha1="${fechaModal}"]`).forEach(cb => cb.checked = checked);
                                                            updateTotalByCuenta2(fechaModal);
                                                        });
                                                    });

                                                    document.querySelectorAll('.select-group-programaciones').forEach(groupCheckbox => {
                                                        groupCheckbox.addEventListener('change', function() {
                                                            const fechaModal = this.getAttribute('data-fecha1');
                                                            const group = this.dataset.group;

                                                            document.querySelectorAll(`.select-p-${group}-${fechaModal}[data-fecha1="${fechaModal}"]`).forEach(cb => cb.checked = this.checked);
                                                            updateTotalByCuenta2(fechaModal);
                                                        });
                                                    });

                                                    document.querySelectorAll('.select-item-programaciones').forEach(item => {
                                                        item.addEventListener('change', function() {
                                                            const fechaModal = this.getAttribute('data-fecha1');
                                                            updateTotalByCuenta2(fechaModal);
                                                        });
                                                    });

                                                </script>

                                                <script>
                                                    function sugerirpagosSeleccionados2(slug) {
                                                        const cuentasIds = Array.from(document.querySelectorAll('.select-item-cuentas:checked'))
                                                            .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                        const programacionesIds = Array.from(document.querySelectorAll('.select-item-programaciones:checked'))
                                                            .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                        // Crear formulario dinámico
                                                        const form = document.createElement('form');
                                                        form.method = 'POST';
                                                        form.action = '{{ route('sugerir.pagos.nomora') }}';

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

                                                        document.body.appendChild(form);
                                                        form.submit();
                                                    }
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

            {{-- CUENTAS POR PAGAR EN MORA --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
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
                                        <button type="button" class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modalProveedor2{{ Str::slug($fechaasignada) }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </abbr>
                                    <div class="modal fade" id="modalProveedor2{{ Str::slug($fechaasignada) }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-xl" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header d-block text-center py-4" style="background: #efefef">
                                                    <div class="mb-3">
                                                        <h4 class="modal-title font-weight-bold text-wrap" style="font-size: 1.5rem; margin-bottom:10px;">
                                                            <strong>CUENTAS POR PAGAR EN MORA DEL: {{ $fechaasignada }}</strong>
                                                        </h4>
                                                        @php
                                                            $documentos = $documentosPorFecha[$fechaasignada] ?? collect();
                                                            $pagoTercero = $documentos->firstWhere('tipo', 'PAGO A TERCERO');
                                                            $pagoInterbancario = $documentos->firstWhere('tipo', 'PAGO INTERBANCARIO');
                                                            $documentosPagoTercero = $documentos->where('tipo', 'PAGO A TERCERO')->values();
                                                            $documentosPagoInterbancario = $documentos->where('tipo', 'PAGO INTERBANCARIO')->values();
                                                        @endphp
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
                                                            <h5 style="font-weight:900;">CUENTAS POR PAGAR</h5>
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
                                                                            <th style="padding: 5px 10px; width: 5%; background-color:#f6faee"><input type="checkbox" class="select-all-cuentas2" data-fecha="{{ Str::slug($fechaasignada) }}">Sel.</th>
                                                                            <th style="padding: 5px 10px; width: 5%; background-color:#f6faee">Ver</th>
                                                                            <th style="padding: 5px 10px; width: 50%; background-color:#f6faee">Proveedor</th>
                                                                            <th style="padding: 5px 10px; width: 15%; background-color:#f6faee">Tipo Planilla</th>
                                                                            <th style="padding: 5px 10px; width: 15%; background-color:#f6faee">Banco Destino</th>
                                                                            <th style="padding: 5px 10px; width: 10%; background-color:#f6faee" class="text-right">Monto Total</th>
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
                                                                            $bancodestino = optional($primerRegistro->proveedorServicio)->numcuenta ?? 'NO DEFINIDO';
                                                                            $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $proveedor)->where('fechapago', $fechaasignada)->first();
                                                                        @endphp
                                                                            <tr class="font-weight-bold" style="background-color: #f7f7f7;">
                                                                                <td>
                                                                                    @if ($todosAprobados)
                                                                                        <span class="badge badge-success">APROBADO</span>
                                                                                    @else
                                                                                        <input type="checkbox" class="select-group-cuentas2" data-group="{{ Str::slug($proveedor, '-') }}" data-fecha="{{ Str::slug($fechaasignada) }}">
                                                                                        @php
                                                                                            $anteriores3 = \App\Models\CuentasPagar::where('proveedornombre', $proveedor)
                                                                                                ->where('fechaasignada', '<', $fechaasignada)
                                                                                                ->where('estadoaprobacion', 'RECHAZADO')
                                                                                                ->pluck('fechaasignada')
                                                                                                ->map(function ($fecha) {
                                                                                                    return \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                                                                                })
                                                                                                ->unique()
                                                                                                ->implode(', ');
                                                                                        @endphp

                                                                                        @if (!empty($anteriores3))
                                                                                            <i class="fas fa-exclamation-triangle text-warning float-end"
                                                                                            title="Existen cuentas por pagar en mora de fecha(s): {{ $anteriores3 }}"></i>
                                                                                        @endif
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
                                                                                <td>{{ $bancodestino }}</td>
                                                                                <td class="text-right">
                                                                                    {{ number_format($registros->sum('montototal'), 2) }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="collapse" id="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                <td colspan="6" class="p-0">
                                                                                    <div class="table-responsive">
                                                                                        <table class="table table-sm">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Sel.</th>
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
                                                                                                    {{-- <th>Edit.</th> --}}
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
                                                                                                                <input type="checkbox" class="select-item-cuentas2 select-c-{{ Str::slug($proveedor, '-') }}" data-id="{{ $pendiente->id }}" data-total="{{ $pendiente->montototal }}" data-fecha="{{ $pendiente->fechaasignada }}" data-fecha-modal="{{ Str::slug($fechaasignada) }}" data-nrocuenta="{{ $pendiente->nrobancoorigen }}">
                                                                                                            @endif
                                                                                                        </td>
                                                                                                        <td hidden>{{ $pendiente->id }}</td>
                                                                                                        <td>{{ $pendiente->ordenid ?? 0 }}</td>
                                                                                                        <td>{{ $pendiente->tipoorden }}</td>
                                                                                                        <td>{{ $pendiente->detalleproducto }}</td>
                                                                                                        <td>{{ $pendiente->fechaasignada }}</td>
                                                                                                        <td>{{ $pendiente->nrobancoorigen ?? 0 }}</td>
                                                                                                        <td>{{ $pendiente->cantidad ?? 0 }}</td>
                                                                                                        {{-- <td>{{ $pendiente->subtotal }}</td> --}}
                                                                                                        <td>
                                                                                                            <input type="number" class="form-control form-control-sm input-subtotal" style="width: 100px;" 
                                                                                                                data-id="{{ $pendiente->id }}"
                                                                                                                value="{{ $pendiente->subtotal }}" readonly>
                                                                                                        </td>
                                                                                                        <td id="descuento-{{ $pendiente->id }}">{{ $pendiente->descuento }}</td>

                                                                                                        {{-- <td>{{ $pendiente->montototal }}</td> --}}
                                                                                                        <td>
                                                                                                            <div class="d-flex align-items-center gap-1">
                                                                                                                <input type="number" class="form-control form-control-sm input-total" style="width: 100px;"
                                                                                                                    data-id="{{ $pendiente->id }}"
                                                                                                                    data-total="{{ $pendiente->montototal }}"
                                                                                                                    value="{{ $pendiente->montototal }}">

                                                                                                                @can('admin.caja.cuentaspagar.dividirmontoscxp')
                                                                                                                    <button class="btn btn-sm btn-outline-success d-none btn-guardar"
                                                                                                                        data-id="{{ $pendiente->id }}">
                                                                                                                        <i class="fas fa-print"></i>
                                                                                                                    </button>
                                                                                                                @endcan
                                                                                                            </div>
                                                                                                        </td>
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
                                                                        <script>
                                                                            document.addEventListener('DOMContentLoaded', function () {
                                                                                document.querySelectorAll('.input-total').forEach(input => {
                                                                                    input.addEventListener('input', function () {
                                                                                        const id = this.dataset.id;
                                                                                        const nuevoTotal = parseFloat(this.value || 0);
                                                                                        const inputSubtotal = document.querySelector(`.input-subtotal[data-id="${id}"]`);
                                                                                        const btnGuardar = document.querySelector(`.btn-guardar[data-id="${id}"]`);

                                                                                        // Leer el valor del descuento desde la celda
                                                                                        const descuento = parseFloat(document.querySelector(`#descuento-${id}`)?.innerText || 0);
                                                                                        const nuevoSubtotal = nuevoTotal + descuento;

                                                                                        if (inputSubtotal) {
                                                                                            inputSubtotal.value = nuevoSubtotal.toFixed(2);
                                                                                        }

                                                                                        const totalOriginal = parseFloat(this.dataset.total || 0);
                                                                                        if (btnGuardar) {
                                                                                            const totalRedondeado = parseFloat(nuevoTotal.toFixed(2));
                                                                                            const originalRedondeado = parseFloat(totalOriginal.toFixed(2));

                                                                                            if (totalRedondeado !== originalRedondeado) {
                                                                                                btnGuardar.classList.remove('d-none');
                                                                                            } else {
                                                                                                btnGuardar.classList.add('d-none');
                                                                                            }
                                                                                        }
                                                                                    });
                                                                                });

                                                                                document.querySelectorAll('.btn-guardar').forEach(btn => {
                                                                                    btn.addEventListener('click', function () {
                                                                                        const id = this.dataset.id;
                                                                                        const btnGuardar = this;
                                                                                        btnGuardar.disabled = true;

                                                                                        const inputTotal = document.querySelector(`.input-total[data-id="${id}"]`);
                                                                                        const descuento = parseFloat(document.querySelector(`#descuento-${id}`)?.innerText || 0);
                                                                                        const nuevoTotal = parseFloat(inputTotal.value || 0);
                                                                                        const nuevoSubtotal = nuevoTotal + descuento;

                                                                                        if (isNaN(nuevoTotal) || nuevoTotal < 0) {
                                                                                            alert("El total debe ser un número válido y mayor o igual a cero.");
                                                                                            btnGuardar.disabled = false;
                                                                                            return;
                                                                                        }

                                                                                        const url = `{{ route('cuentasporpagar.actualizar.monto', ['id' => 'ID_TEMP']) }}`.replace('ID_TEMP', id);

                                                                                        fetch(url, {
                                                                                            method: 'POST',
                                                                                            headers: {
                                                                                                'Content-Type': 'application/json',
                                                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                                            },
                                                                                            body: JSON.stringify({
                                                                                                nuevo_subtotal: nuevoSubtotal,
                                                                                                nuevo_total: nuevoTotal
                                                                                            })
                                                                                        })
                                                                                        .then(res => res.json())
                                                                                        .then(data => {
                                                                                            alert(data.message || data.info || 'Actualización realizada.');
                                                                                            location.reload();
                                                                                        })
                                                                                        .catch(err => {
                                                                                            console.error(err);
                                                                                            alert('Error al guardar.');
                                                                                            btnGuardar.disabled = false;
                                                                                        });
                                                                                    });
                                                                                });
                                                                            });
                                                                        </script>
                                                                        <div class="row justify-content-center" hidden> 
                                                                            @foreach ($cuentasAgrupadas->flatten()->groupBy('nrobancoorigen') as $cuenta => $items)
                                                                                <div class="col-md-4 mb-4">
                                                                                    <strong>TOTAL CxP ({{ $cuenta }}):</strong> 
                                                                                    Bs.<span id="total-cuentas2-{{ Str::slug($fechaasignada) }}-{{ $cuenta }}">0.00</span>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <br>
                                                            <h5 style="font-weight:900;">PROVEEDORES MEDICOS</h5>
                                                            <div class="table-responsive" style="overflow-x: auto;">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="padding: 5px 10px; width: 5%; background-color:#f6faee"><input type="checkbox" class="select-all-programaciones2" data-fecha="{{ Str::slug($fechaasignada) }}">Sel.</th>
                                                                            <th style="padding: 5px 10px; width: 5%; background-color:#f6faee">Ver</th>
                                                                            <th style="padding: 5px 10px; width: 50%; background-color:#f6faee">Proveedor</th>
                                                                            <th style="padding: 5px 10px; width: 15%; background-color:#f6faee">Tipo Planilla</th>
                                                                            <th style="padding: 5px 10px; width: 15%; background-color:#f6faee">Banco Destino</th>
                                                                            <th style="padding: 5px 10px; width: 10%; background-color:#f6faee" class="text-right">Monto Total</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($registrosbateria->where('fechapago', $fechaasignada)->where('prioridad', 'CUENTA POR PAGAR')->where('estadoaprobacion','=','RECHAZADO')->groupBy('proveedorasignado') as $proveedor => $registros)
                                                                            @php
                                                                                $todosAprobados = $registros->every(function($r) {
                                                                                    return in_array($r->estadoaprobacion, ['APROBADO', 'CARGADO']);
                                                                                });
                                                                                $tipoplanilla = $proveedoresServicios[$proveedor] ?? 'NO DEFINIDO';
                                                                                $bancodestino = $proveedoresServicioscuenta[$proveedor] ?? 'NO DEFINIDO';
                                                                                $archivoExistente = \App\Models\PlanillasPagosGeneradas::where('proveedor', $proveedor)
                                                                                    ->where('fechapago', $fechaasignada)
                                                                                    ->first();
                                                                            @endphp
                                                                            <tr class="font-weight-bold" style="background-color: #f7f7f7;">
                                                                                <td>
                                                                                    @if ($todosAprobados)
                                                                                        <span class="badge badge-success">APROBADO</span>
                                                                                    @else
                                                                                        <input type="checkbox" class="select-group-programaciones2" data-group="{{ Str::slug($proveedor, '-') }}" data-fecha="{{ Str::slug($fechaasignada) }}">
                                                                                        @php
                                                                                            $anteriores4 = \App\Models\Bateriasubcliente::where('proveedorasignado', $proveedor)
                                                                                                ->where('fechapago', '<', $fechaasignada)
                                                                                                ->where('estadoaprobacion', 'RECHAZADO')
                                                                                                ->pluck('fechapago')
                                                                                                ->map(function ($fecha) {
                                                                                                    return \Carbon\Carbon::parse($fecha)->format('d/m/Y');
                                                                                                })
                                                                                                ->unique()
                                                                                                ->implode(', ');
                                                                                        @endphp

                                                                                        @if (!empty($anteriores4))
                                                                                            <i class="fas fa-exclamation-triangle text-warning float-end"
                                                                                            title="Existen cuentas por pagar en mora de fecha(s): {{ $anteriores4 }}"></i>
                                                                                        @endif
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
                                                                                <td>{{ $bancodestino }}</td>
                                                                                <td class="text-right">
                                                                                    {{ number_format($registros->sum('preciocompra'), 2) }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr class="collapse" id="detalle-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}">
                                                                                <td colspan="6" class="p-0">
                                                                                    <div class="table-responsive" style="overflow-x: auto;">
                                                                                        <table class="table table-sm">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th>Sel.</th>
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
                                                                                                                    class="select-item-programaciones2 select-p-{{ Str::slug($proveedor, '-') }}-{{ Str::slug($fechaasignada) }}" 
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
                                                                                    Bs.<span id="total-programaciones2-{{ Str::slug($fechaasignada) }}-{{ $programacion }}">0.00</span>
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
                                                                                Bs.<span id="total-general2-{{ Str::slug($fechaasignada) }}-{{ $cuenta }}">0.00</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <div class="d-flex flex-column align-items-end">
                                                                    @can('admin.cuentaspagar.sugerirpagos')
                                                                            @php $slugFecha = Str::slug($fechaasignada); @endphp
                                                                            <input type="date" name="fechapagocambio" id="fechapagocambio-{{ $slugFecha }}" class="form-control mb-2">
                                                                            <button class="btn btn-sm btn-outline-primary mb-2" onclick="sugerirpagosSeleccionados('{{ $slugFecha }}')">SUGERIR PAGOS</button>
                                                                        @can('admin.cuentaspagar.aprobarcxp')
                                                                            {{-- <input type="date" name="fechapagocambio" id="fechapagocambio" class="form-control">
                                                                            <button class="btn btn-sm btn-outline-primary mb-2" onclick="cambiarfechaSeleccionados()">CAMBIAR FECHA</button> --}}
                                                                            
                                                                            <button class="btn btn-sm btn-outline-success mb-2" onclick="cambiarfechaSeleccionados('{{ $slugFecha }}')">CAMBIAR FECHA</button>
                                                                        @endcan
                                                                    @endcan
                                                                    {{-- <a type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">CERRAR</a> --}}
                                                                </div>
                                                            </div>
                                                            <script>
                                                                function cambiarfechaSeleccionados(slug) {
                                                                    const cuentasIds = Array.from(document.querySelectorAll('.select-item-cuentas2:checked'))
                                                                        .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                    const programacionesIds = Array.from(document.querySelectorAll('.select-item-programaciones2:checked'))
                                                                        .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                    const fechaInput = document.getElementById(`fechapagocambio-${slug}`);
                                                                    const fechaSeleccionada = fechaInput ? fechaInput.value : '';

                                                                    if (!fechaSeleccionada) {
                                                                        alert("Debe seleccionar una fecha para continuar.");
                                                                        return;
                                                                    }

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
                                                                    const fechaHidden = document.createElement('input');
                                                                    fechaHidden.type = 'hidden';
                                                                    fechaHidden.name = 'fechapagocambio';
                                                                    fechaHidden.value = fechaSeleccionada;
                                                                    form.appendChild(fechaHidden);

                                                                    document.body.appendChild(form);
                                                                    form.submit();
                                                                }
                                                            </script>
                                                            <script>
                                                                function sugerirpagosSeleccionados(slug) {
                                                                    const cuentasIds = Array.from(document.querySelectorAll('.select-item-cuentas2:checked'))
                                                                        .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                    const programacionesIds = Array.from(document.querySelectorAll('.select-item-programaciones2:checked'))
                                                                        .map(cb => cb.closest('tr').querySelector('td:nth-child(2)').textContent.trim());

                                                                    const fechaInput = document.getElementById(`fechapagocambio-${slug}`);
                                                                    const fechaSeleccionada = fechaInput ? fechaInput.value : '';

                                                                    if (!fechaSeleccionada) {
                                                                        alert("Debe seleccionar una fecha para continuar.");
                                                                        return;
                                                                    }

                                                                    // Crear formulario dinámico
                                                                    const form = document.createElement('form');
                                                                    form.method = 'POST';
                                                                    form.action = '{{ route('sugerir.pagos') }}';

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
                                                                    const fechaHidden = document.createElement('input');
                                                                    fechaHidden.type = 'hidden';
                                                                    fechaHidden.name = 'fechapagocambio';
                                                                    fechaHidden.value = fechaSeleccionada;
                                                                    form.appendChild(fechaHidden);

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
                                                        document.querySelectorAll(`span[id^="total-cuentas2-${fechaModal}-"]`).forEach(span => {
                                                            span.textContent = "0.00";
                                                        });

                                                        let totales = {};

                                                        document.querySelectorAll(`.select-item-cuentas2[data-fecha-modal="${fechaModal}"]`).forEach(item => {
                                                            if (item.checked) {
                                                                let cuenta = item.getAttribute('data-nrocuenta') || '0';
                                                                let total = parseFloat(item.getAttribute('data-total')) || 0;
                                                                totales[cuenta] = (totales[cuenta] || 0) + total;
                                                            }
                                                        });

                                                        for (let cuenta in totales) {
                                                            let spanId = `total-cuentas2-${fechaModal}-${cuenta}`;
                                                            let span = document.getElementById(spanId);
                                                            if (span) {
                                                                span.textContent = totales[cuenta].toFixed(2);
                                                            }
                                                        }
                                                        updateTotalGeneral3(fechaModal); // 👈 al final
                                                    }
                                                    function updateTotalGeneral3(fechaModal) {
                                                        document.querySelectorAll(`span[id^="total-general2-${fechaModal}-"]`).forEach(span => {
                                                            const idCuenta = span.id.replace(`total-general2-${fechaModal}-`, '');
                                                            const totalCuentas = parseFloat(document.getElementById(`total-cuentas2-${fechaModal}-${idCuenta}`)?.textContent || '0') || 0;
                                                            const totalProgramaciones = parseFloat(document.getElementById(`total-programaciones2-${fechaModal}-${idCuenta}`)?.textContent || '0') || 0;
                                                            const totalGeneral = totalCuentas + totalProgramaciones;
                                                            span.textContent = totalGeneral.toFixed(2);
                                                        });
                                                    }


                                                    // Seleccionar todos
                                                    document.querySelectorAll('.select-all-cuentas2').forEach(selectAll => {
                                                        selectAll.addEventListener('change', function() {
                                                            const fechaModal = this.getAttribute('data-fecha');
                                                            let checked = this.checked;
                                                            document.querySelectorAll(`.select-item-cuentas2[data-fecha-modal="${fechaModal}"], .select-group-cuentas2[data-fecha="${fechaModal}"]`).forEach(cb => cb.checked = checked);
                                                            updateTotalByCuenta3(fechaModal);
                                                        });
                                                    });

                                                    // Grupo por proveedor
                                                    document.querySelectorAll('.select-group-cuentas2').forEach(groupCheckbox => {
                                                        groupCheckbox.addEventListener('change', function() {
                                                            const fechaModal = this.getAttribute('data-fecha');
                                                            let group = this.dataset.group;
                                                            document.querySelectorAll(`.select-c-${group}[data-fecha-modal="${fechaModal}"]`).forEach(cb => cb.checked = this.checked);
                                                            updateTotalByCuenta3(fechaModal);
                                                        });
                                                    });

                                                    // Ítem individual
                                                    document.querySelectorAll('.select-item-cuentas2').forEach(item => {
                                                        item.addEventListener('change', function() {
                                                            const fechaModal = this.getAttribute('data-fecha-modal');
                                                            updateTotalByCuenta3(fechaModal);
                                                        });
                                                    });
                                                    
                                                    function updateTotalByCuenta23(fechaModal) {
                                                        // Reiniciar todos los totales visibles a 0.00
                                                        document.querySelectorAll(`span[id^="total-programaciones2-${fechaModal}-"]`).forEach(span => {
                                                            span.textContent = "0.00";
                                                        });

                                                        let totales = {};

                                                        document.querySelectorAll(`.select-item-programaciones2[data-fecha-modal="${fechaModal}"]`).forEach(item => {
                                                            if (item.checked) {
                                                                let programacion = item.getAttribute('data-nrocuenta') || '0';
                                                                let total = parseFloat(item.getAttribute('data-total')) || 0;
                                                                totales[programacion] = (totales[programacion] || 0) + total;
                                                            }
                                                        });

                                                        for (let programacion in totales) {
                                                            let spanId = `total-programaciones2-${fechaModal}-${programacion}`;
                                                            let span = document.getElementById(spanId);
                                                            if (span) {
                                                                span.textContent = totales[programacion].toFixed(2);
                                                            }
                                                        }
                                                        updateTotalGeneral3(fechaModal);
                                                    }

                                                    document.querySelectorAll('.select-all-programaciones2').forEach(selectAll => {
                                                        selectAll.addEventListener('change', function() {
                                                            const fechaModal = this.getAttribute('data-fecha');
                                                            const checked = this.checked;

                                                            document.querySelectorAll(`.select-item-programaciones2[data-fecha="${fechaModal}"], .select-group-programaciones2[data-fecha="${fechaModal}"]`).forEach(cb => cb.checked = checked);
                                                            updateTotalByCuenta23(fechaModal);
                                                        });
                                                    });

                                                    document.querySelectorAll('.select-group-programaciones2').forEach(groupCheckbox => {
                                                        groupCheckbox.addEventListener('change', function() {
                                                            const fechaModal = this.getAttribute('data-fecha');
                                                            const group = this.dataset.group;

                                                            document.querySelectorAll(`.select-p-${group}-${fechaModal}[data-fecha="${fechaModal}"]`).forEach(cb => cb.checked = this.checked);
                                                            updateTotalByCuenta23(fechaModal);
                                                        });
                                                    });

                                                    document.querySelectorAll('.select-item-programaciones2').forEach(item => {
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

            {{-- PROVEEDORES MEDICOS --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
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

            {{-- CUENTAS POR PAGAR OTROS PROVEEDORES --}}
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <table class="table table-striped">
                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #ffffff;">
                        <tr>
                            <th style="width: 5%;"><i class="fas fa-check"></i></th>
                            <th style="width: 85%;">Proveedor</th>
                            <th style="width: 10%;">C.Pagar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $cuentasPorProveedor = $cuentaspagar->groupBy('proveedornombre');
                            $bateriasPorProveedor = $registrosbateria->groupBy('proveedornombre');

                            use App\Models\Proveedoresservicios;
                            $proveedoresConFactura = Proveedoresservicios::where('emision', 'FACTURA')->pluck('razonsocial');

                            /* $proveedoresUnicos = $cuentasPorProveedor->keys()
                                ->merge($bateriasPorProveedor->keys())
                                ->unique()
                                ->sort(); */
                            $proveedoresUnicos = $cuentasPorProveedor->keys()
                                ->merge($bateriasPorProveedor->keys())
                                ->unique()
                                ->filter(function ($proveedor) use ($proveedoresConFactura) {
                                    return $proveedoresConFactura->contains($proveedor);
                                })
                                ->sort();

                            $proveedoresFuturas = collect();
                            $proveedoresPasadas = collect();

                            $hoy = Carbon::today();

                            foreach ($proveedoresUnicos as $proveedor) {
                                $cuentas = $cuentasPorProveedor->get($proveedor, collect());
                                $baterias = $bateriasPorProveedor->get($proveedor, collect());

                                $hayPendienteCuentas = $cuentas->contains(fn($item) => $item->estado !== 'PAGO PROCESADO' && $item->estadoaprobacion !== 'RECHAZADO');
                                $hayPendienteBaterias = $baterias->contains(fn($item) => $item->prioridad === 'CUENTA POR PAGAR' && $item->estadoaprobacion !== 'RECHAZADO');
                                $tieneFechaPasada = $cuentas->concat($baterias)->contains(function ($item) use ($hoy) {
                                    $fecha = $item->fechaasignada ?? $item->fechapago ?? null;
                                    return $fecha && Carbon::parse($fecha)->lessThan($hoy);
                                });

                                if ($hayPendienteCuentas || $hayPendienteBaterias) {
                                    if ($tieneFechaPasada) {
                                        $proveedoresPasadas->put($proveedor, $cuentas);
                                    } else {
                                        $proveedoresFuturas->put($proveedor, $cuentas);
                                    }
                                }
                            }

                            $proveedoresConPendientes = $proveedoresFuturas->merge($proveedoresPasadas);
                        @endphp
                        @foreach ($proveedoresConPendientes as $proveedornombre => $cuentas)
                                <tr>
                                <td><i class="fas fa-check"></i></td>
                                <td>{{ $proveedornombre }}</td>
                                <td>
                                    <abbr title="VER REGISTROS">
                                        <button type="button" class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modalProveedor7{{ Str::slug($proveedornombre) }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </abbr>
                                    <div class="modal fade" id="modalProveedor7{{ Str::slug($proveedornombre) }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-xxl" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header d-block text-center py-4" style="background: #efefef">
                                                    <div class="mb-3">
                                                        <h4 class="modal-title font-weight-bold text-wrap" style="font-size: 1.5rem; margin-bottom:10px;">
                                                            <strong>{{ $proveedornombre }}</strong>
                                                        </h4>
                                                        <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="modal-body">
                                                    <ul class="nav nav-tabs" id="tabsProveedor7{{ Str::slug($proveedornombre) }}">
                                                        <li class="nav-item">
                                                            <a class="nav-link active" data-toggle="tab" href="#pendientes7{{ Str::slug($proveedornombre) }}">PAGOS PENDIENTES</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link" data-toggle="tab" href="#finalizados7{{ Str::slug($proveedornombre) }}">PAGOS PROCESADOS</a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content mt-3">
                                                        <div id="pendientes7{{ Str::slug($proveedornombre) }}" class="tab-pane fade show active">
                                                            <input type="hidden" id="fechaSeleccionada7{{ Str::slug($proveedornombre) }}" value="{{ $proveedornombre }}">
                                                            @php
                                                                $cuentasAgrupadas = $cuentas->whereIn('estado', ['PENDIENTE', 'SALDO PENDIENTE'])
                                                                                            ->where('estadoaprobacion', '!=', 'RECHAZADO')
                                                                                            ->groupBy('proveedornombre');
                                                            @endphp
                                                            <form action="{{ route('cuentasporpagar.facturas.otrosprov') }}" method="POST" enctype="multipart/form-data" data-modal-id="{{ Str::slug($proveedornombre) }}">
                                                                @csrf
                                                                <input type="hidden" name="ids_seleccionados7" id="ids_seleccionados7_{{ Str::slug($proveedornombre) }}">

                                                                <div class="table-responsive">
                                                                    <table class="table table-striped">
                                                                        <tbody>
                                                                            @foreach ($cuentasAgrupadas as $proveedor => $registros)
                                                                                <tr>
                                                                                    <td colspan="5" class="p-0">
                                                                                        <div class="table-responsive">
                                                                                            <table class="table table-striped">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th style="background-color: #f8f9fa;">ID Reg.</th>
                                                                                                        <th style="background-color: #f8f9fa;">Orden.ID</th>
                                                                                                        <th style="background-color: #f8f9fa;">Tipo Orden</th>
                                                                                                        <th style="background-color: #f8f9fa;">Detalle</th>
                                                                                                        <th style="background-color: #f8f9fa;">Fecha Pago</th>
                                                                                                        <th style="background-color: #f8f9fa;">N.Cta Origen</th>
                                                                                                        <th style="background-color: #f8f9fa;">Cant.</th>
                                                                                                        <th style="background-color: #f8f9fa;">Subto.</th>
                                                                                                        <th style="background-color: #f8f9fa;">Desc.</th>
                                                                                                        <th style="background-color: #f8f9fa;">Total</th>
                                                                                                        <th style="background-color: #f8f9fa;">Cod.Aut.</th>
                                                                                                        <th style="background-color: #f8f9fa;">N.Factura</th>
                                                                                                        <th style="background-color: #f8f9fa;">Sel.</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    @foreach ($registros as $pendiente)
                                                                                                        <tr>
                                                                                                            <td>{{ $pendiente->id }}</td>
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
                                                                                                                @if (empty($pendiente->codautorizacion))
                                                                                                                    <span class="badge badge-danger">PENDIENTE</span>
                                                                                                                @else
                                                                                                                    {{ $pendiente->codautorizacion }}
                                                                                                                @endif
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                @if (empty($pendiente->factura) || empty($pendiente->nrofactura))
                                                                                                                    <span class="badge badge-danger">PENDIENTE</span>
                                                                                                                @else
                                                                                                                    <a href="{{ asset('comprobantescuentaspagar/' . $pendiente->factura) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                                                                        <i class="fas fa-file-alt"></i>
                                                                                                                    </a>
                                                                                                                    {{ $pendiente->nrofactura }}
                                                                                                                @endif
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <input type="checkbox" class="select-item-cuentas-prov select-c-prov-{{ Str::slug($proveedor, '-') }}" data-id="{{ $pendiente->id }}" data-total="{{ $pendiente->montototal }}" data-fecha1="{{ $pendiente->fechaasignada }}" data-fecha1-modal="{{ Str::slug($proveedornombre) }}" data-nrocuenta="{{ $pendiente->nrobancoorigen }}">
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="card">
                                                                    <div class="card-body" style="background-color: #f8f8f8;">
                                                                        <div class="row mb-3">
                                                                            <div class="col-12 d-flex justify-content-end">
                                                                                <div class="d-flex align-items-end flex-wrap gap-3" style=" margin-top: -15px; margin-bottom: -20px;">
                                                                                    <div>
                                                                                        <label for="archivo_comprobante" class="form-label">Archivo Factura</label>
                                                                                        <input type="file" name="archivo_comprobante" id="archivo_comprobante" class="form-control form-control-sm" accept="application/pdf" required>
                                                                                    </div>

                                                                                    <div>
                                                                                        <label for="nro_factura" class="form-label">Nro. Factura</label>
                                                                                        <input type="text" name="nro_factura" id="nro_factura" class="form-control form-control-sm" placeholder="Nro. Factura" required>
                                                                                    </div>

                                                                                    <div style="min-width: 300px;">
                                                                                        <label for="codigo_autorizacion" class="form-label">Cod. Autorización</label>
                                                                                        <input type="text" name="codigo_autorizacion" id="codigo_autorizacion" class="form-control form-control-sm" placeholder="Cod. Autorización" required>
                                                                                    </div>

                                                                                    <div>
                                                                                        <button type="submit" name="action" value="guardar" class="btn btn-outline-secondary btn-sm">
                                                                                            <i class="fas fa-print"></i> GUARDAR
                                                                                        </button>
                                                                                    </div>
                                                                                    <div>
                                                                                        @can('admin.cuentaspagar.anularfacturas')
                                                                                            <button type="submit" name="action" value="anular" class="btn btn-outline-danger btn-sm">
                                                                                                <i class="fas fa-times-circle"></i> ANULAR
                                                                                            </button>
                                                                                        @endcan
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <script>
                                                                    document.addEventListener('DOMContentLoaded', function () {
                                                                        document.querySelectorAll('form[data-modal-id]').forEach(form => {
                                                                            form.addEventListener('submit', function (e) {
                                                                                const modalId = this.getAttribute('data-modal-id');
                                                                                const checkboxes = document.querySelectorAll(`.select-item-cuentas-prov[data-fecha1-modal="${modalId}"]:checked`);
                                                                                const ids = Array.from(checkboxes).map(cb => cb.getAttribute('data-id'));
                                                                                const inputHidden = this.querySelector(`#ids_seleccionados7_${modalId}`);
                                                                                if (inputHidden) {
                                                                                    inputHidden.value = ids.join(',');
                                                                                }
                                                                            });
                                                                        });
                                                                    });
                                                                </script>
                                                            </form>
                                                        </div>
                                                            
                                                        <div id="finalizados7{{ Str::slug($proveedornombre) }}" class="tab-pane fade">
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
                                <div class="table-responsive" style="max-height: 65vh; margin-top: -20px;">
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
                                                <th>Cod.Autorización</th>
                                                <th>N.Factura</th>
                                                <th>
                                                    Sel.{{-- <input type="checkbox" id="seleccionarTodos{{ $loop->index }}" class="seleccionarTodos" data-modal="{{ $loop->index }}"> --}}
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
                                                            @if ($accion['codautorizacion'])
                                                                {{ $accion['codautorizacion'] }}
                                                            @else
                                                                <span class="badge bg-danger">PENDIENTE</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (!empty($accion['documentofactura']))
                                                                <a href="{{ asset('comprobantescuentaspagar/' . $accion['documentofactura']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                            @if (!empty($accion['nrofacturaprog']))
                                                                {{ $accion['nrofacturaprog'] }}
                                                            @else
                                                                <span class="badge bg-danger">PENDIENTE</span>
                                                            @endif
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
                                                            @if ($accion['codautorizacioninfofinal'])
                                                                {{ $accion['codautorizacioninfofinal'] }}
                                                            @else
                                                                <span class="badge bg-danger">PENDIENTE</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (!empty($accion['facturainformefinal']))
                                                                <a href="{{ asset('comprobantescuentaspagar/' . $accion['facturainformefinal']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                            @if (!empty($accion['nrofacturainformefinal']))
                                                                {{ $accion['nrofacturainformefinal'] }}
                                                            @else
                                                                <span class="badge bg-danger">PENDIENTE</span>
                                                            @endif
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
                                <div class="card">
                                    <div class="card-body" style="background-color: #f8f8f8;">
                                        <div class="row mb-3">
                                            <div class="col-12 d-flex justify-content-end">
                                                <div class="d-flex align-items-end flex-wrap gap-3" style=" margin-top: -15px; margin-bottom: -20px;">
                                                    <div>
                                                        <label for="archivo_comprobante" class="form-label">Archivo Factura</label>
                                                        <input type="file" name="documentofactura" id="documentofactura" accept=".pdf" class="form-control form-control-sm" style="max-width: 300px;" required/>
                                                    </div>

                                                    <div>
                                                        <label for="nro_factura" class="form-label">Nro. Factura</label>
                                                        <input type="text" class="form-control me-2 form-control-sm" id="nroFactura" name="nroFactura" placeholder="Nro. Factura" style="max-width: 150px;" required>
                                                    </div>

                                                    <div style="min-width: 300px;">
                                                        <label for="codigo_autorizacion" class="form-label">Cod. Autorización</label>
                                                        <input type="text" class="form-control me-2 form-control-sm" id="codautorizacion" name="codautorizacion" placeholder="Cod. Autorización" style="max-width: 350px;" required>
                                                    </div>

                                                    <div>
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm me-2" onclick="document.getElementById('action_type').value='guardar'" title="GUARDAR FACTURA">
                                                            <i class="fas fa-save"></i> GUARDAR
                                                        </button>
                                                    </div>
                                                    <div>
                                                        @can('admin.cuentaspagar.anularfacturas')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('action_type').value='anular'" title="ANULAR FACTURA">
                                                            <i class="fas fa-times-circle"></i> ANULAR
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
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
                                                <th>Cod.Autorizacion</th>
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
                                                            @if ($accion['codautorizacioninfofinal'])
                                                                {{ $accion['codautorizacioninfofinal'] }}
                                                            @else
                                                                <span class="badge bg-danger">PENDIENTE</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (!empty($accion['facturainformefinal']))
                                                                <a href="{{ asset('comprobantescuentaspagar/' . $accion['facturainformefinal']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                    <i class="fas fa-file-alt"></i>
                                                                </a>
                                                            @endif
                                                            @if (!empty($accion['nrofacturainformefinal']))
                                                                {{ $accion['nrofacturainformefinal'] }}
                                                            @else
                                                                <span class="badge bg-danger">PENDIENTE</span>
                                                            @endif
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
                                                                @if ($accion['codautorizacion'])
                                                                    {{ $accion['codautorizacion'] }}
                                                                @else
                                                                    <span class="badge bg-danger">PENDIENTE</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if (!empty($accion['documentofactura']))
                                                                    <a href="{{ asset('comprobantescuentaspagar/' . $accion['documentofactura']) }}" target="_blank" class="btn btn-sm btn-botongris" title="VER FACTURA">
                                                                        <i class="fas fa-file-alt"></i>
                                                                    </a>
                                                                @endif
                                                                @if (!empty($accion['nrofacturaprog']))
                                                                    {{ $accion['nrofacturaprog'] }}
                                                                @else
                                                                    <span class="badge bg-danger">PENDIENTE</span>
                                                                @endif
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

                                <div class="card">
                                    <div class="card-body" style="background-color: #f8f8f8;">
                                        <div class="row mb-3">
                                            <div class="col-12 d-flex justify-content-end">
                                                <div class="d-flex align-items-end flex-wrap gap-3" style=" margin-top: -15px; margin-bottom: -20px;">
                                                    <div>
                                                        <label for="archivo_comprobante" class="form-label">Archivo Factura</label>
                                                        <input type="file" name="documentofactura" id="documentofactura" accept=".pdf" class="form-control form-control-sm" style="max-width: 300px;" required/>
                                                    </div>

                                                    <div>
                                                        <label for="nro_factura" class="form-label">Nro. Factura</label>
                                                        <input type="text" class="form-control me-2 form-control-sm" id="nroFactura" name="nroFactura" placeholder="Nro. Factura" style="max-width: 150px;" required>
                                                    </div>

                                                    <div style="min-width: 300px;">
                                                        <label for="codigo_autorizacion" class="form-label">Cod. Autorización</label>
                                                        <input type="text" class="form-control me-2 form-control-sm" id="codautorizacion" name="codautorizacion" placeholder="Cod. Autorización" style="max-width: 350px;" required>
                                                    </div>

                                                    <div>
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm me-2" onclick="document.getElementById('action_type2').value='guardar'" title="GUARDAR FACTURA">
                                                            <i class="fas fa-save"></i> GUARDAR
                                                        </button>
                                                    </div>
                                                    <div>
                                                        @can('admin.cuentaspagar.anularfacturas')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('action_type2').value='anular'" title="ANULAR FACTURA">
                                                            <i class="fas fa-times-circle"></i> ANULAR
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
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
                    const nroFacturaCol = tab === 'completos' ? 14 : 12;
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

