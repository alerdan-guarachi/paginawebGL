@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-botongris" href="{{ route('admin.caja.cuentaspagar.listacuentaspagar') }}">CUENTAS POR PAGAR PENDIENTES</a>
<h1>CUENTAS POR PAGAR POR PROVEEDOR</h1>
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
    .btn-botonrojo {
        background-color: #ffffff;
        color: #c51616;
        border-color: #c51616;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-botonrojo:hover {
        background-color: #c51616;
        color: #ffffff;
        }
    .btn-guardarconfactura {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        margin-right: 5px;
    }
    .btn-guardarconfactura:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-guardarsinfactura {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
        margin-right: 5px;
    }
    .btn-guardarsinfactura:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-anularfactura {
        background-color: #ffffff;
        color: #df1a1a;
        border-color: #df1a1a;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-anularfactura:hover {
        background-color: #df1a1a;
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
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">  
            <li class="nav-item">
                <a class="nav-link active" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    CXP POR PROVEEDOR
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    CXP PENDIENTES ÁREA MÉDICA
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    CXP PENDIENTES PROVEEDORES
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- CXP POR PROVEEDOR --}}
            <div class="tab-pane fade show active" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                <div class="table-responsive">
                    <label for="">BUSCAR POR PROVEEDOR O FECHA:</label>
                    {{-- <div class="row mb-2">
                        <div class="col-lg-5">
                            <input type="text" id="buscarProveedor" class="form-control" placeholder="Buscar proveedor...">
                        </div>
                        <div class="col-lg-5">
                            <input type="date" id="buscarFecha" class="form-control" placeholder="Buscar por fecha">
                        </div>
                        <div class="col-lg-2">
                            <a href="#" id="limpiarBusqueda" class="btn btn-outline-secondary mb-3 w-100">LIMPIAR CAMPOS</a>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            function filtrarTabla() {
                                var proveedorFiltro = $('#buscarProveedor').val().toLowerCase();
                                var fechaFiltro = $('#buscarFecha').val();

                                $('table.table > tbody > tr').each(function() {
                                    var filaProveedor = $(this);

                                    if (filaProveedor.find('td').length === 3) {
                                        var nombreProveedor = filaProveedor.find('td:nth-child(2)').text().toLowerCase();
                                        var coincideProveedor = nombreProveedor.includes(proveedorFiltro);
                                        var collapseId = filaProveedor.find('button[data-target]').data('target');
                                        var registros = $(collapseId + ' table tbody tr');
                                        var tieneRegistrosFecha = false;

                                        registros.each(function() {
                                            var filaRegistro = $(this);
                                            var fechaTexto = '';

                                            filaRegistro.find('td').each(function() {
                                                var texto = $(this).text().trim();
                                                if (/^\d{4}-\d{2}-\d{2}$/.test(texto)) {
                                                    fechaTexto = texto;
                                                }
                                            });

                                            if (!fechaFiltro || fechaTexto === fechaFiltro) {
                                                filaRegistro.show();
                                                tieneRegistrosFecha = true;
                                            } else {
                                                filaRegistro.hide();
                                            }
                                        });

                                        if (coincideProveedor && tieneRegistrosFecha) {
                                            filaProveedor.show();
                                        } else {
                                            filaProveedor.hide();
                                            $(collapseId).removeClass('show');
                                        }
                                    }
                                });
                            }

                            $('#buscarProveedor, #buscarFecha').on('input change', filtrarTabla);

                            $('#limpiarBusqueda').click(function(e) {
                                e.preventDefault();
                                $('#buscarProveedor').val('');
                                $('#buscarFecha').val('');
                                filtrarTabla();
                            });
                        });
                    </script> --}}
                    {{-- <div class="row mb-2">
                        <div class="col-lg-5">
                            <input type="text" id="buscarProveedor" placeholder="Buscar proveedor..." class="form-control mb-2">
                        </div>
                        <div class="col-lg-5">
                            <input type="date" id="buscarFecha" class="form-control mb-2">
                        </div>
                        <div class="col-lg-2">
                            <a href="#" id="limpiarBusqueda" class="btn btn-outline-secondary mb-3 w-100">LIMPIAR CAMPOS</a>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const inputProveedor = document.getElementById('buscarProveedor');
                            const inputFecha = document.getElementById('buscarFecha');

                            function filtrar() {
                                const filtroProveedor = inputProveedor.value.toLowerCase();
                                const filtroFecha = inputFecha.value; // formato YYYY-MM-DD

                                document.querySelectorAll('#tab-content-5 > .table-responsive > table > tbody > tr').forEach(tr => {
                                    if (!tr.classList.contains('collapse')) { // fila principal
                                        const proveedorTd = tr.querySelector('td:nth-child(2)');
                                        const proveedorTexto = proveedorTd ? proveedorTd.textContent.toLowerCase() : '';

                                        let fechaCoincide = true;
                                        if (filtroFecha) {
                                            const collapseTr = tr.nextElementSibling;
                                            fechaCoincide = false;
                                            if (collapseTr && collapseTr.classList.contains('collapse')) {
                                                // Recorremos todas las filas de detalle de cuentas y baterías
                                                collapseTr.querySelectorAll('tbody tr').forEach(detalleTr => {
                                                    const columnasFecha = [
                                                        detalleTr.children[3], // fecha de cuenta o bateria
                                                        detalleTr.children[4],
                                                        detalleTr.children[5],
                                                        detalleTr.children[6]
                                                    ];
                                                    columnasFecha.forEach(td => {
                                                        if (td && td.textContent.includes(filtroFecha)) {
                                                            fechaCoincide = true;
                                                        }
                                                    });
                                                });
                                            }
                                        }

                                        const mostrar = proveedorTexto.includes(filtroProveedor) && fechaCoincide;
                                        tr.style.display = mostrar ? '' : 'none';

                                        const collapseTr = tr.nextElementSibling;
                                        if (collapseTr && collapseTr.classList.contains('collapse')) {
                                            collapseTr.style.display = mostrar ? '' : 'none';
                                        }
                                    }
                                });
                            }

                            inputProveedor.addEventListener('input', filtrar);
                            inputFecha.addEventListener('input', filtrar);

                            $('#buscarProveedor, #buscarFecha').on('input change', filtrarTabla);

                            $('#limpiarBusqueda').click(function(e) {
                                e.preventDefault();
                                $('#buscarProveedor').val('');
                                $('#buscarFecha').val('');
                                filtrar();
                            });
                        });
                    </script> --}}

                    <div class="row mb-2">
                        <div class="col-lg-5">
                            <input type="text" id="buscarProveedor" placeholder="Buscar proveedor..." class="form-control mb-2">
                        </div>
                        <div class="col-lg-5">
                            <input type="date" id="buscarFecha" class="form-control mb-2">
                        </div>
                        <div class="col-lg-2">
                            <a href="#" id="limpiarBusqueda" class="btn btn-outline-secondary mb-3 w-100">LIMPIAR CAMPOS</a>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const inputProveedor = document.getElementById('buscarProveedor');
                            const inputFecha = document.getElementById('buscarFecha');
                            const limpiarBtn = document.getElementById('limpiarBusqueda');

                            function filtrar() {
                                const filtroProveedor = inputProveedor.value.toLowerCase();
                                const filtroFecha = inputFecha.value;

                                document.querySelectorAll('#tab-content-5 > .table-responsive > table > tbody > tr').forEach(tr => {
                                    if (!tr.classList.contains('collapse')) {
                                        const proveedorTd = tr.querySelector('td:nth-child(2)');
                                        const proveedorTexto = proveedorTd ? proveedorTd.textContent.toLowerCase() : '';
                                        let fechaCoincide = !filtroFecha;

                                        if (filtroFecha) {
                                            const collapseTr = tr.nextElementSibling;
                                            fechaCoincide = false;
                                            if (collapseTr && collapseTr.classList.contains('collapse')) {
                                                collapseTr.querySelectorAll('tbody tr').forEach(detalleTr => {
                                                    Array.from(detalleTr.children).forEach(td => {
                                                        if (td && td.textContent.includes(filtroFecha)) {
                                                            fechaCoincide = true;
                                                        }
                                                    });
                                                });
                                            }
                                        }
                                        const mostrar = proveedorTexto.includes(filtroProveedor) && fechaCoincide;
                                        tr.style.display = mostrar ? '' : 'none';
                                        const collapseTr = tr.nextElementSibling;
                                        if (collapseTr && collapseTr.classList.contains('collapse')) {
                                            collapseTr.style.display = mostrar ? '' : 'none';
                                        }
                                    }
                                });
                            }

                            inputProveedor.addEventListener('input', filtrar);
                            inputFecha.addEventListener('input', filtrar);
                            limpiarBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                inputProveedor.value = '';
                                inputFecha.value = '';
                                filtrar();
                            });
                        });
                    </script>

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
                                $proveedoresCuentas = $cuentaspagar->groupBy('proveedornombre');
                                $proveedoresBaterias = $registrosbateria->groupBy('proveedorasignado');

                                $todosProveedores = $proveedoresCuentas->keys()
                                    ->merge($proveedoresBaterias->keys())
                                    ->unique()
                                    ->sort();
                            @endphp

                            @foreach($todosProveedores as $proveedor)
                                @php
                                    $cuentas = $proveedoresCuentas->get($proveedor, collect());
                                    $baterias = $proveedoresBaterias->get($proveedor, collect());
                                    $fechaActual = now();

                                    /* $tienePendientes = $cuentas->contains(fn($item) => $item->estado !== 'PAGO PROCESADO') 
                                                    || $baterias->contains(fn($item) => $item->prioridad === 'CUENTA POR PAGAR'); */

                                    $tienePendientes = $cuentas->contains(function($item) {
                                            return !in_array($item->estado, ['PAGO PROCESADO', 'FINALIZADO']);
                                        }) || $baterias->contains(fn($item) => $item->prioridad === 'CUENTA POR PAGAR');

                                    $hoy = \Carbon\Carbon::today();

                                    /* $proveedorVencido = $cuentas->contains(fn($c) => $c->fechaasignada && \Carbon\Carbon::parse($c->fechaasignada)->lt($hoy))
                                        || $baterias->contains(function($b) use ($hoy) {
                                            $fechaPago = $b->fechapago ? \Carbon\Carbon::parse($b->fechapago) : null;
                                            $fechaAsignada = optional($b->programacion)->fechaasignada ? \Carbon\Carbon::parse(optional($b->programacion)->fechaasignada) : null;

                                            return $fechaPago
                                                ? $fechaPago->lt($hoy)
                                                : ($fechaAsignada && $fechaAsignada->lt($hoy));
                                        }); */

                                    $proveedorVencido = $cuentas->contains(function($c) use ($hoy) {
                                        return !in_array($c->estado, ['PAGO PROCESADO', 'FINALIZADO'])
                                            && $c->fechaasignada
                                            && \Carbon\Carbon::parse($c->fechaasignada)->lt($hoy);
                                    }) || $baterias->contains(function($b) use ($hoy) {
                                        $fechaPago = $b->fechapago ? \Carbon\Carbon::parse($b->fechapago) : null;
                                        $fechaAsignada = optional($b->programacion)->fechaasignada ? \Carbon\Carbon::parse(optional($b->programacion)->fechaasignada) : null;
                                        return $b->prioridad === 'CUENTA POR PAGAR'
                                            && ($fechaPago ? $fechaPago->lt($hoy) : ($fechaAsignada && $fechaAsignada->lt($hoy)));
                                    });
                                @endphp

                                @if($tienePendientes)
                                    <tr @if($proveedorVencido) class="table-danger" @endif>
                                        <td><i class="fas fa-check"></i></td>
                                        <td>{{ $proveedor }}</td>
                                        <td>
                                            <button class="btn btn-botongris btn-sm" type="button" data-toggle="collapse" data-target="#proveedor-{{ Str::slug($proveedor) }}" title="VER REGISTROS">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- <tr class="collapse" id="proveedor-{{ Str::slug($proveedor) }}">
                                        <td colspan="3">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm">
                                                    @if($cuentas->isNotEmpty())
                                                        <thead>
                                                            <tr>
                                                                <th>Orden.ID</th>
                                                                <th>Tipo_Orden</th>
                                                                <th>Detalle</th>
                                                                <th>Fecha_Pago</th>
                                                                <th>N.Cta_Origen</th>
                                                                <th>Nro.Factura</th>
                                                                <th>Cant.</th>
                                                                <th>SubTotal</th>
                                                                <th>Desc.</th>
                                                                <th>Total</th>
                                                                <th>Estado</th>
                                                            </tr>
                                                        </thead>
                                                        @php
                                                            $fechaActual = now();
                                                            $cuentasOrdenadas = $cuentas->sortBy(function($item) {
                                                                return $item->fechaasignada ? \Carbon\Carbon::parse($item->fechaasignada) : now();
                                                            });
                                                        @endphp

                                                        <tbody>
                                                            @foreach($cuentasOrdenadas as $c)
                                                                @php
                                                                    $esVencido = $c->fechaasignada && \Carbon\Carbon::parse($c->fechaasignada)->lt(\Carbon\Carbon::today());
                                                                @endphp
                                                                <tr @if($esVencido) class="table-danger" @endif>
                                                                    <td>{{ $c->ordenid }}</td>
                                                                    <td>{{ $c->tipoorden }}</td>
                                                                    <td>{{ $c->detalleproducto }}</td>
                                                                    <td>{{ $c->fechaasignada }}</td>
                                                                    <td>{{ $c->nrobancoorigen }}</td>
                                                                    <td>{{ $c->nrofactura ?? 'PENDIENTE'}}</td>
                                                                    <td>{{ $c->cantidad }}</td>
                                                                    <td>{{ $c->subtotal }}</td>
                                                                    <td>{{ $c->descuento }}</td>
                                                                    <td>{{ $c->montototal }}</td>
                                                                    <td>{{ $c->estado }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    @endif

                                                    @if($baterias->isNotEmpty())
                                                        <thead>
                                                            <tr>
                                                                <th>Orden.ID</th>
                                                                <th>Cliente</th>
                                                                <th>Estudio/Especialidad</th>
                                                                <th>Fecha_Bateria</th>
                                                                <th>Fecha_Prog.</th>
                                                                <th>Fecha_Informe</th>
                                                                <th>Fecha_Pago</th>
                                                                <th>N.Cta_Origen</th>
                                                                <th>Nro.Factura</th>
                                                                <th>Total</th>
                                                                <th>Estado</th>
                                                            </tr>
                                                        </thead>
                                                        @php
                                                            $fechaActual = now();
                                                            $bateriasOrdenadas = $baterias->sortBy(function($item) {
                                                                $fechaPago = $item->fechapago ? \Carbon\Carbon::parse($item->fechapago) : null;
                                                                $fechaAsignada = optional($item->programacion)->fechaasignada ? \Carbon\Carbon::parse(optional($item->programacion)->fechaasignada) : null;

                                                                return $fechaPago ?? $fechaAsignada ?? now();
                                                            });
                                                        @endphp

                                                        <tbody>
                                                            @foreach($bateriasOrdenadas as $b)
                                                                @php
                                                                    $fechaPago = $b->fechapago ? \Carbon\Carbon::parse($b->fechapago) : null;
                                                                    $fechaAsignada = optional($b->programacion)->fechaasignada ? \Carbon\Carbon::parse(optional($b->programacion)->fechaasignada) : null;
                                                                    $esVencido = $fechaPago
                                                                        ? $fechaPago->lt($hoy)
                                                                        : ($fechaAsignada && $fechaAsignada->lt($hoy));
                                                                @endphp
                                                                <tr @if($esVencido) class="table-danger" @endif>
                                                                    <td>{{ $b->ordenid }}</td>
                                                                    <td>{{ $b->clienteitanombre }}{{ $b->clienteauditorianombre }}{{ $b->clientecomunnombre }}</td>
                                                                    <td>{{ $b->accionnombre }}</td>
                                                                    <td>{{ $b->fechabateria }}</td>
                                                                    <td>
                                                                        @if ($b->accionnombre === 'INFORME FINAL')
                                                                            {{ $b->informe_created_at ? \Carbon\Carbon::parse($b->informe_created_at)->format('Y-m-d') : 'PENDIENTE' }}
                                                                        @else
                                                                            {{ optional($b->programacion)->fechaasignada ?? 'PENDIENTE' }}
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($b->accionnombre === 'INFORME FINAL')
                                                                            {{ $b->informe_created_at ? \Carbon\Carbon::parse($b->informe_created_at)->format('Y-m-d') : '' }}
                                                                        @else
                                                                            {{ optional(optional($b->programacion)->documentacion)->created_at ? optional($b->programacion)->documentacion->created_at->format('Y-m-d') : '' }}
                                                                        @endif

                                                                        @if ($b->accionnombre === 'PSICOLOGIA' && $b->clientecomunid !== null)
                                                                            {{ optional($b->programacion)->fechaasignada ?? '' }}
                                                                        @endif

                                                                        @if (
                                                                            $b->accionnombre === 'LAVADO DE OIDO DERECHO' || 
                                                                            $b->accionnombre === 'LAVADO DE OIDO IZQUIERDO'
                                                                        )
                                                                            {{ optional($b->programacion)->fechaasignada ?? '' }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $b->fechapago }}</td>
                                                                    <td>{{ $b->nrobancoorigen }}</td>
                                                                    <td>
                                                                        @if ($b->accionnombre === 'INFORME FINAL')
                                                                            {{ optional($b->proveedorinformefinal)->nrofactura ?? 'PENDIENTE' }}
                                                                        @else
                                                                            {{ optional($b->programacion)->nrofactura ?? 'PENDIENTE' }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $b->preciocompra }}</td>
                                                                    <td>{{ $b->prioridad == 'CUENTA POR PAGAR' ? 'PENDIENTE' : $b->prioridad }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    @endif
                                                </table>
                                            </div>
                                        </td>
                                    </tr> --}}
                                    <tr class="collapse" id="proveedor-{{ Str::slug($proveedor) }}">
                                        <td colspan="3">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm">
                                                    @if($cuentas->isNotEmpty())
                                                        {{-- @php
                                                            $cuentasPorFactura = $cuentas
                                                                ->sortBy('fechaasignada')
                                                                ->groupBy('nrofactura')
                                                                ->sortKeys();

                                                        @endphp --}}
                                                        @php
                                                            // Primero agrupamos por nro de factura
                                                            $cuentasPorFactura = $cuentas
                                                                ->groupBy('nrofactura')
                                                                ->sortBy(function ($grupo) {
                                                                    // fecha más antigua de los registros pendientes
                                                                    return $grupo
                                                                        ->whereNotIn('estado', ['PAGO PROCESADO', 'FINALIZADO'])
                                                                        ->min('fechaasignada');
                                                                });
                                                        @endphp

                                                        <thead class="thead-white">
                                                            <tr>
                                                                <th style="background-color: white;">Nro.Factura</th>
                                                                <th style="background-color: white;">Total_Factura</th>
                                                                <th style="background-color: white;">Ver</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {{-- @foreach($cuentasPorFactura as $factura => $registrosFactura)
                                                                @php
                                                                    $totalFactura = $registrosFactura
                                                                        ->filter(fn($r) => !in_array($r->estado, ['PAGO PROCESADO', 'FINALIZADO']))
                                                                        ->sum('montototal');
                                                                    $facturaTexto = $factura ?: 'PENDIENTE';
                                                                    $facturaId = 'factura-' . Str::slug($proveedor) . '-' . Str::slug($facturaTexto);
                                                                @endphp
                                                                @if(!($facturaTexto === 'PENDIENTE' && $totalFactura == 0))
                                                                    <tr>
                                                                        <td>{{ $facturaTexto }}</td>
                                                                        <td>{{ number_format($totalFactura, 2) }}</td>
                                                                        <td>
                                                                            <a class="btn btn-botongris btn-sm" data-toggle="collapse" data-target="#{{ $facturaId }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                <tr class="collapse" id="{{ $facturaId }}">
                                                                    <td colspan="5">
                                                                        <table class="table table-sm">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Orden.ID</th>
                                                                                    <th>Tipo_Orden</th>
                                                                                    <th>Detalle</th>
                                                                                    <th>Fecha_Pago</th>
                                                                                    <th>N.Cta_Origen</th>
                                                                                    <th>Nro.Factura</th>
                                                                                    <th>Cant.</th>
                                                                                    <th>SubTotal</th>
                                                                                    <th>Desc.</th>
                                                                                    <th>Total</th>
                                                                                    <th>Estado</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @php
                                                                                    $registrosPendientes = $registrosFactura->whereNotIn('estado', ['PAGO PROCESADO', 'FINALIZADO']);
                                                                                @endphp

                                                                                @foreach($registrosPendientes->sortBy('fechaasignada') as $c)
                                                                                    @php
                                                                                        $esVencido = $c->fechaasignada && \Carbon\Carbon::parse($c->fechaasignada)->lt(\Carbon\Carbon::today());
                                                                                    @endphp
                                                                                    <tr @if($esVencido) class="table-danger" @endif>
                                                                                        <td>{{ $c->ordenid }}</td>
                                                                                        <td>{{ $c->tipoorden }}</td>
                                                                                        <td>{{ $c->detalleproducto }}</td>
                                                                                        <td>{{ $c->fechaasignada }}</td>
                                                                                        <td>{{ $c->nrobancoorigen }}</td>
                                                                                        <td>{{ $c->nrofactura ?? 'PENDIENTE'}}</td>
                                                                                        <td>{{ $c->cantidad ?? '0' }}</td>
                                                                                        <td>{{ $c->subtotal }}</td>
                                                                                        <td>{{ $c->descuento }}</td>
                                                                                        <td>{{ $c->montototal }}</td>
                                                                                        <td>{{ $c->estado }}</td>
                                                                                    </tr>
                                                                                @endforeach

                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            @endforeach --}}
                                                            @foreach($cuentasPorFactura as $factura => $registrosFactura)
                                                                @php
                                                                    // Filtrar solo registros que no están procesados
                                                                    $registrosPendientes = $registrosFactura->whereNotIn('estado', ['PAGO PROCESADO', 'FINALIZADO']);
                                                                    $totalFactura = $registrosPendientes->sum('montototal');
                                                                    $facturaTexto = $factura ?: 'PENDIENTE';
                                                                    $facturaId = 'factura-' . Str::slug($proveedor ?? 'prov') . '-' . Str::slug($facturaTexto);
                                                                @endphp

                                                                {{-- Mostrar solo si hay registros pendientes --}}
                                                                @if($registrosPendientes->isNotEmpty())
                                                                    <tr>
                                                                        <td>{{ $facturaTexto }}</td>
                                                                        <td>{{ number_format($totalFactura, 2) }}</td>
                                                                        <td>
                                                                            <a class="btn btn-botongris btn-sm" data-toggle="collapse" data-target="#{{ $facturaId }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>

                                                                    <tr class="collapse" id="{{ $facturaId }}">
                                                                        <td colspan="11">
                                                                            <table class="table table-sm">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Orden.ID</th>
                                                                                        <th>Tipo_Orden</th>
                                                                                        <th>Detalle</th>
                                                                                        <th>Fecha_Pago</th>
                                                                                        <th>N.Cta_Origen</th>
                                                                                        <th>Nro.Factura</th>
                                                                                        <th>Cant.</th>
                                                                                        <th>SubTotal</th>
                                                                                        <th>Desc.</th>
                                                                                        <th>Total</th>
                                                                                        <th>Estado</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($registrosPendientes->sortBy('fechaasignada') as $c)
                                                                                        @php
                                                                                            $esVencido = $c->fechaasignada && \Carbon\Carbon::parse($c->fechaasignada)->lt(\Carbon\Carbon::today());
                                                                                        @endphp
                                                                                        <tr @if($esVencido) class="table-danger" @endif>
                                                                                            <td>{{ $c->ordenid }}</td>
                                                                                            <td>{{ $c->tipoorden }}</td>
                                                                                            <td>{{ $c->detalleproducto }}</td>
                                                                                            <td>{{ $c->fechaasignada }}</td>
                                                                                            <td>{{ $c->nrobancoorigen }}</td>
                                                                                            <td>{{ $c->nrofactura ?? 'PENDIENTE'}}</td>
                                                                                            <td>{{ $c->cantidad ?? '0' }}</td>
                                                                                            <td>{{ $c->subtotal }}</td>
                                                                                            <td>{{ $c->descuento }}</td>
                                                                                            <td>{{ $c->montototal }}</td>
                                                                                            <td>{{ $c->estado }}</td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach

                                                        </tbody>
                                                    @endif

                                                    @if($baterias->isNotEmpty())
                                                        @php
                                                            $bateriasPorFactura = $baterias->groupBy(function($b) {
                                                                return $b->accionnombre === 'INFORME FINAL'
                                                                    ? optional($b->proveedorinformefinal)->nrofactura
                                                                    : optional($b->programacion)->nrofactura;
                                                            })->sortKeys();
                                                        @endphp

                                                        <thead class="thead-white">
                                                            <tr>
                                                                <th style="background-color: white;">Nro.Factura</th>
                                                                <th style="background-color: white;">Total_Factura</th>
                                                                <th style="background-color: white;">Ver</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($bateriasPorFactura as $factura => $registrosFactura)
                                                                @php
                                                                    $totalFactura = $registrosFactura->sum('preciocompra');
                                                                    $facturaTexto = $factura ?: 'PENDIENTE';
                                                                    $facturaId = 'bateria-' . Str::slug($proveedor) . '-' . Str::slug($facturaTexto);
                                                                @endphp
                                                                @if(!($facturaTexto === 'PENDIENTE' && $totalFactura == 0))
                                                                    <tr>
                                                                        <td>{{ $facturaTexto }}</td>
                                                                        <td>{{ number_format($totalFactura, 2) }}</td>
                                                                        <td>
                                                                            <a class="btn btn-botongris btn-sm" data-toggle="collapse" data-target="#{{ $facturaId }}">
                                                                                <i class="fas fa-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                <tr class="collapse" id="{{ $facturaId }}">
                                                                    <td colspan="5">
                                                                        <table class="table table-sm">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Orden.ID</th>
                                                                                    <th>Cliente</th>
                                                                                    <th>Estudio/Especialidad</th>
                                                                                    <th>Fecha_Bateria</th>
                                                                                    <th>Fecha_Prog.</th>
                                                                                    <th>Fecha_Informe</th>
                                                                                    <th>Fecha_Pago</th>
                                                                                    <th>N.Cta_Origen</th>
                                                                                    <th>Nro.Factura</th>
                                                                                    <th>Total</th>
                                                                                    <th>Estado</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($registrosFactura->sortBy('fechapago') as $b)
                                                                                    @php
                                                                                        $fechaPago = $b->fechapago ? \Carbon\Carbon::parse($b->fechapago) : null;
                                                                                        $fechaAsignada = optional($b->programacion)->fechaasignada ? \Carbon\Carbon::parse(optional($b->programacion)->fechaasignada) : null;
                                                                                        $esVencido = $fechaPago
                                                                                            ? $fechaPago->lt(\Carbon\Carbon::today())
                                                                                            : ($fechaAsignada && $fechaAsignada->lt(\Carbon\Carbon::today()));
                                                                                    @endphp
                                                                                    <tr @if($esVencido) class="table-danger" @endif>
                                                                                        <td>{{ $b->ordenid }}</td>
                                                                                        <td>{{ $b->clienteitanombre }}{{ $b->clienteauditorianombre }}{{ $b->clientecomunnombre }}</td>
                                                                                        <td>{{ $b->accionnombre }}</td>
                                                                                        <td>{{ $b->fechabateria }}</td>
                                                                                        <td>
                                                                                            @if ($b->accionnombre === 'INFORME FINAL')
                                                                                                {{ $b->informe_created_at ? \Carbon\Carbon::parse($b->informe_created_at)->format('Y-m-d') : 'PENDIENTE' }}
                                                                                            @else
                                                                                                {{ optional($b->programacion)->fechaasignada ?? 'PENDIENTE' }}
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>
                                                                                            @if ($b->accionnombre === 'INFORME FINAL')
                                                                                                {{ $b->informe_created_at ? \Carbon\Carbon::parse($b->informe_created_at)->format('Y-m-d') : '' }}
                                                                                            @else
                                                                                                {{ optional(optional($b->programacion)->documentacion)->created_at ? optional($b->programacion)->documentacion->created_at->format('Y-m-d') : '' }}
                                                                                            @endif

                                                                                            @if ($b->accionnombre === 'PSICOLOGIA' && $b->clientecomunid !== null)
                                                                                                {{ optional($b->programacion)->fechaasignada ?? '' }}
                                                                                            @endif

                                                                                            @if (
                                                                                                $b->accionnombre === 'LAVADO DE OIDO DERECHO' || 
                                                                                                $b->accionnombre === 'LAVADO DE OIDO IZQUIERDO'
                                                                                            )
                                                                                                {{ optional($b->programacion)->fechaasignada ?? '' }}
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>{{ $b->fechapago }}</td>
                                                                                        <td>{{ $b->nrobancoorigen }}</td>
                                                                                        <td>
                                                                                            @if ($b->accionnombre === 'INFORME FINAL')
                                                                                                {{ optional($b->proveedorinformefinal)->nrofactura ?? 'PENDIENTE' }}
                                                                                            @else
                                                                                                {{ optional($b->programacion)->nrofactura ?? 'PENDIENTE' }}
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>{{ $b->preciocompra }}</td>
                                                                                        <td>{{ $b->prioridad == 'CUENTA POR PAGAR' ? 'PENDIENTE' : $b->prioridad }}</td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    @endif
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- CXP PENDIENTES ÁREA MÉDICA --}}
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
            {{-- CXP PENDIENTES PROVEEDORES --}}
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
                            use Illuminate\Support\Carbon;
                            $cuentasPorProveedor = $cuentaspagar->groupBy('proveedornombre');
                            $bateriasPorProveedor = $registrosbateria->groupBy('proveedornombre');

                            use App\Models\Proveedoresservicios;
                            $proveedoresConFactura = Proveedoresservicios::where('emision', 'FACTURA')->pluck('razonsocial');

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

                                /* $hayPendienteCuentas = $cuentas->contains(fn($item) => $item->estado !== 'PAGO PROCESADO' && $item->estadoaprobacion !== 'RECHAZADO'); */
                                $hayPendienteCuentas = $cuentas->contains(fn($item) => $item->estado !== 'PAGO PROCESADO' && $item->estado !== 'FINALIZADO');
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
                                                                                            /* ->where('estadoaprobacion', '!=', 'RECHAZADO') */
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
                                                                        @foreach ($cuentas->whereIn('estado', ['PAGO PROCESADO', 'FINALIZADO']) as $finalizado)
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
                                <input type="hidden" name="action_type" id="action_type_{{ $loop->index }}" value="">
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
                                                    Sel.
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
                                                            (
                                                                in_array($accion['accion'], ['PSICOLOGIA', 'MEDICINA LABORAL'])
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
                                                            @elseif ($accion['accion'] === 'MEDICINA LABORAL' && $accion['clientecomunid'] !== null)
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
                                            <div class="col-12">
                                                <div class="row g-2 align-items-end">
                                                    <div class="col-lg-3">
                                                        <label for="archivo_comprobante" class="form-label" style="margin-bottom: -10px;">Archivo Factura</label>
                                                        <input type="file" name="documentofactura" id="documentofactura" accept=".pdf" class="form-control form-control-sm"/>
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label for="nroFactura" class="form-label" style="margin-bottom: -10px;">Nro. Factura</label>
                                                        <input type="text" class="form-control form-control-sm" id="nroFactura" name="nroFactura" placeholder="Nro. Factura">
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label for="codautorizacion" class="form-label" style="margin-bottom: -10px;">Cod. Autorización</label>
                                                        <input type="text" class="form-control form-control-sm" id="codautorizacion" name="codautorizacion" placeholder="Cod. Autorización">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label for="fechaPago" class="form-label" style="margin-bottom: -10px;">Fecha de Pago</label>
                                                        <input type="date" class="form-control form-control-sm" id="fechaPago" name="fechaPagoProv">
                                                    </div>

                                                    <div class="col-lg-2">
                                                        <label for="nrocuentabanco" class="form-label" style="margin-bottom: -10px;">Nro. Cuenta Origen:</label>
                                                        <select class="form-control form-control-sm" name="nrocuentabanco" required>
                                                            @foreach ($cuentasbancos as $cuenta)
                                                                <option value="{{ $cuenta->numerocuenta }}">
                                                                    {{ $cuenta->numerocuenta }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- BOTONES -->
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-guardarconfactura btn-sm"
                                                    onclick="document.getElementById('action_type_{{ $loop->index }}').value='guardar'">
                                                 GUARDAR CON FACTURA
                                            </button>

                                            @can('admin.cuentaspagar.anularfacturas')
                                            <button type="submit" class="btn btn-guardarsinfactura btn-sm"
                                                    onclick="document.getElementById('action_type_{{ $loop->index }}').value='guardarsinfactura'">
                                                 GUARDAR SIN FACTURA
                                            </button>

                                            <button type="submit" class="btn btn-anularfactura btn-sm"
                                                    onclick="document.getElementById('action_type_{{ $loop->index }}').value='anular'">
                                                 ANULAR FACTURA
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
                                <input type="hidden" name="action_type" id="action_type2_{{ $loop->index }}" value="">
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
                                            <div class="col-12">
                                                <div class="row g-2 align-items-end">
                                                    <div class="col-lg-3">
                                                        <label for="archivo_comprobante" class="form-label" style="margin-bottom: -10px;">Archivo Factura</label>
                                                        <input type="file" name="documentofactura" id="documentofactura" accept=".pdf" class="form-control form-control-sm"/>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <label for="nro_factura" class="form-label" style="margin-bottom: -10px;">Nro. Factura</label>
                                                        <input type="text" class="form-control me-2 form-control-sm" id="nroFactura" name="nroFactura" placeholder="Nro. Factura">
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label for="codigo_autorizacion" class="form-label" style="margin-bottom: -10px;">Cod. Autorización</label>
                                                        <input type="text" class="form-control me-2 form-control-sm" id="codautorizacion" name="codautorizacion" placeholder="Cod. Autorización">
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <label for="nro_factura" class="form-label" style="margin-bottom: -10px;">Fecha de Pago</label>
                                                        <input type="date" class="form-control me-2 form-control-sm" id="fechaPago" name="fechaPagoProv">
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <label for="nrocuentabanco" class="form-label" style="margin-bottom: -10px;">Nro. Cuenta Origen:</label>
                                                        <select class="form-control form-control-sm" name="nrocuentabanco" required>
                                                            @foreach ($cuentasbancos as $cuenta)
                                                                <option value="{{ $cuenta->numerocuenta }}">
                                                                    {{ $cuenta->numerocuenta }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-guardarconfactura btn-sm"
                                                    onclick="document.getElementById('action_type2_{{ $loop->index }}').value='guardar'">
                                                 GUARDAR CON FACTURA
                                            </button>
                                            <div>
                                                @can('admin.cuentaspagar.anularfacturas')
                                                <button type="submit" class="btn btn-guardarsinfactura btn-sm"
                                                        onclick="document.getElementById('action_type2_{{ $loop->index }}').value='guardarsinfactura'">
                                                     GUARDAR SIN FACTURA
                                                </button>
                                                @endcan
                                            </div>
                                            <div>
                                                @can('admin.cuentaspagar.anularfacturas')
                                                <button type="submit" class="btn btn-anularfactura btn-sm"
                                                        onclick="document.getElementById('action_type2_{{ $loop->index }}').value='anular'">
                                                     ANULAR FACTURA
                                                </button>
                                                @endcan
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
                    const nroFacturaCol = tab === 'completos' ? 13 : 12;
                    const montoCol = tab === 'completos' ? 7 : 7;

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

