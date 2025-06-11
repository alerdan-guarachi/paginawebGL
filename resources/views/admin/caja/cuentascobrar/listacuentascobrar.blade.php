@extends('adminlte::page')

@section('content_header')
<a type="button" class="btn btn-outline-secondary float-right" data-toggle="modal" data-target="#calendarModal">TOTAL CXC CONGELADAS</a>

<div class="container">
    <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h4 class="modal-title" id="calendarModalLabel">
                        Total de pagos congelados {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </h4>
                    <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 80vh;">
                    <div id="customCalendar" class="my-3"></div>
                    <div class="d-flex justify-content-between">
                        <button id="prevMonth" class="btn btn-crear">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </button>
                        <button id="nextMonth" class="btn btn-crear">
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script> 
    document.addEventListener('DOMContentLoaded', function () {
        const calendarContainer = document.getElementById('customCalendar');
        let year = {{ $year }};
        let month = {{ $month }};
        const modalTitle = document.getElementById('calendarModalLabel');

        function fetchRecords(year, month) {
            return fetch(`{{ route('admin.caja.cuentascobrar.listacuentascobrar') }}?year=${year}&month=${month}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .catch(error => {
                console.error("Error al cargar los registros:", error);
                return [];
            });
        }

        function loadCalendar(year, month) {
            const daysInMonth = new Date(year, month, 0).getDate();
            const firstDay = new Date(year, month - 1, 1).getDay();
            modalTitle.innerHTML = `<strong style="text-transform: uppercase;">Total de pagos congelados de ${new Date(year, month - 1).toLocaleString('default', { month: 'long' })} ${year}</strong>`;
            fetchRecords(year, month).then(records => {
                const recordsByDate = {};
                records.forEach(record => {
                    recordsByDate[record.fechabateria] = {
                        total_ingresos: record.total_ingresos
                    };
                });

                renderCalendar(year, month, daysInMonth, firstDay, recordsByDate);
            });
        }

        function renderCalendar(year, month, daysInMonth, firstDay, recordsByDate) {
            const table = document.createElement('table');
            table.classList.add('table', 'table-bordered', 'text-center');

            const header = document.createElement('thead');
            header.innerHTML = `
                <tr style="background-color: #f0f0f0;">
                    <th style="text-transform: uppercase; font-weight: bold;">Lunes</th>
                    <th style="text-transform: uppercase; font-weight: bold;">Martes</th>
                    <th style="text-transform: uppercase; font-weight: bold;">Miércoles</th>
                    <th style="text-transform: uppercase; font-weight: bold;">Jueves</th>
                    <th style="text-transform: uppercase; font-weight: bold;">Viernes</th>
                    <th style="text-transform: uppercase; font-weight: bold;">Sábado</th>
                    <th style="text-transform: uppercase; font-weight: bold;">Domingo</th>
                </tr>
            `;
            table.appendChild(header);

            const body = document.createElement('tbody');
            let row = document.createElement('tr');
            let startDay = firstDay === 0 ? 6 : firstDay - 1;
            let totalMes = 0;
            let totalMesegreso = 0;
            const today = new Date();

            for (let i = 0; i < startDay; i++) {
                const cell = document.createElement('td');
                row.appendChild(cell);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const cell = document.createElement('td');
                const date = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                // Comprobar si es el día actual
                const isToday = today.toISOString().split('T')[0] === date;

                if (recordsByDate[date]) {
                    const { total_ingresos } = recordsByDate[date];
                    totalMes += total_ingresos;

                    let content = `<strong>${day}</strong>`;
                    content += `<div><span class="badge badge-success badge-zoom">Total: ${total_ingresos.toFixed(2)} Bs.</span></div>`;

                    cell.innerHTML = content;
                } else {
                    cell.innerHTML = `<strong>${day}</strong><div>Sin prog.</div>`;
                }

                if (isToday) {
                    cell.style.backgroundColor = '#eeeeee';
                }

                row.appendChild(cell);

                if ((startDay + day) % 7 === 0) {
                    body.appendChild(row);
                    row = document.createElement('tr');
                }
            }

            while (row.children.length < 7) {
                const cell = document.createElement('td');
                row.appendChild(cell);
            }

            body.appendChild(row);
            table.appendChild(body);
            calendarContainer.innerHTML = '';
            calendarContainer.appendChild(table);

            const totalsContainer = document.createElement('div');
            totalsContainer.classList.add('mt-3', 'text-center');
            totalsContainer.innerHTML = `
                <div><span class="badge text-success badge-zoom monto"><strong>Total Congelado: ${totalMes.toFixed(2)} Bs.</strong></span></div>
            `;
            
            calendarContainer.appendChild(totalsContainer);
        }

        loadCalendar(year, month);

        document.getElementById('prevMonth').addEventListener('click', function() {
            month--;
            if (month < 1) {
                month = 12;
                year--;
            }
            loadCalendar(year, month);
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            month++;
            if (month > 12) {
                month = 1;
                year++;
            }
            loadCalendar(year, month);
        });
    });
</script>

<h1>CUENTAS POR COBRAR</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/cuentascobrarpagar.css') }}">
<style>
    .table td {
        padding: 5px 10px;;
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
                <form id="search-form" action="{{ route('buscarlistacuentascobrar') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="Nombre del Cliente">
                    </div>
                    <button id="btn-buscar" class="btn btn-outline-secondary my-2 my-sm-0" type="submit">Buscar</button>
                    <button id="btn-mostrar-todo" class="btn btn-outline-secondary my-2 my-sm-0 ml-2" type="button">Mostrar Todo</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">  
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    CLIENTES
                </a>
            </li>     
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    PROVEEDORES MEDICOS
                </a>
            </li>    
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    PROVEEDORES DE SERVICIOS Y CLIENTES
                </a>
            </li> 
        </ul>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarlistacuentascobrar') }}";
            });
        });
    </script>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- CLIENTES --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive" style="max-height: 75vh;">
                    <table class="table table-striped">
                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #ffffff;">
                            <tr>
                                <th style="width: 10%;">Tipo Cliente</th>
                                <th style="width: 10%;">ID_Cli.</th>
                                <th style="width: 35%;">Cliente Nombre</th>
                                <th style="width: 15%;">Sucursal</th>
                                <th style="width: 15%;">Fecha Batería - Servicio</th>
                                <th style="width: 5%;">Baterias</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- CLIENTES ITA --}}
                            @foreach ($result as $item)
                                @php
                                    $pagoPendiente = false;
                                    foreach ($item['acciones'] as $accion) {
                                        if (is_null($accion['pagoservicioinforme']) && is_null($accion['pagoservicioinformefinal'])) {
                                            $pagoPendiente = true;
                                            break;
                                        }
                                    }
                                @endphp
                                @if ($pagoPendiente)
                                <tr>
                                    <td>CLIENTE ITA</td>
                                    <td>{{ $item['clienteitaid'] }}</td>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td>{{ $item['usuarioregistro'] }}</td>
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>
                                    <td>
                                        <abbr title="VER BATERIA">
                                            <a class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>
                                </tr>
                                @endif
                            @endforeach

                            {{-- CLIENTES AUDITORIA --}}
                            @foreach ($result2 as $item)
                            @php
                                    $pagoPendiente = false;
                                    foreach ($item['acciones'] as $accion) {
                                        if (is_null($accion['pagoservicioinforme']) && is_null($accion['pagoservicioinformefinal'])) {
                                            $pagoPendiente = true;
                                            break;
                                        }
                                    }
                                @endphp
                                @if ($pagoPendiente)
                                <tr>
                                    <td>CLIENTE AUDITORIA</td>
                                    <td>{{ $item['clienteauditoriaid'] }}</td>
                                    <td>{{ $item['clienteauditorianombre'] }}</td>
                                    <td>{{ $item['usuarioregistro'] }}</td>
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }} - {{ implode(', ', $item['tramite']) }}
                                        @else
                                            {{ $item['fechabateria'] }} - SIN SERVICIO
                                        @endif
                                    </td>
                                    <td>
                                        <abbr title="VER BATERIA">
                                            <a class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modalauditoria{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>
                                </tr>
                                @endif
                            @endforeach

                            {{-- CLIENTES COMUNES --}}
                            @foreach ($result3 as $item)
                            @php
                                    $pagoPendiente = false;
                                    foreach ($item['acciones'] as $accion) {
                                        if (is_null($accion['pagoservicioinforme']) && is_null($accion['pagoservicioinformefinal'])) {
                                            $pagoPendiente = true;
                                            break;
                                        }
                                    }
                                @endphp
                                @if ($pagoPendiente)
                                <tr>
                                    <td>CLIENTE COMUN</td>
                                    <td>{{ $item['clientecomunid'] }}</td>
                                    <td>{{ $item['clientecomunnombre'] }}</td>
                                    <td>{{ $item['usuarioregistro'] }}</td>
                                    <td>
                                        @if (is_array($item['tramite']) && count($item['tramite']) > 0)
                                            {{ $item['fechabateria'] }}
                                        @else
                                            {{ $item['fechabateria'] }}
                                        @endif
                                    </td>
                                    <td>
                                        <abbr title="VER BATERIA">
                                            <a class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modalcomun{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>

            {{-- PROVEEDORES MEDICOS --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="table table-striped">
                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #ffffff;">
                            <tr>
                                <th style="width: 5%;"><i class="fas fa-check"></i></th>
                                <th style="width: 85%;">Proveedor Médico</th>
                                <th style="width: 10%;">C. Pagar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result4 as $item)
                                <tr>
                                    <td><i class="fas fa-check"></i></td>
                                    <td>{{ $item['proveedorasignado'] }}</td>
                                    <td>
                                        <abbr title="VER REGISTROS">
                                            <a class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modalproveedores{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>

            {{-- PROVEEDORES DE SERVICIOS --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive" style="max-height: 70vh;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead style="position: sticky; top: 0; z-index: 1010; background-color: #ffffff;">
                                <tr>
                                    <th style="width: 5%;"><i class="fas fa-check"></i></th>
                                    <th style="width: 85%;">Fecha de C. Cobrar</th>
                                    <th style="width: 10%;">C. Cobrar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $estadosDeseados = ['PENDIENTE', 'PAGO PROCESADO', 'SALDO PENDIENTE'];

                                    $proveedoresConPendientes = $cuentaspagar
                                        ->groupBy('fechaasignada')
                                        ->filter(function ($items) {
                                            return $items->contains(function ($item) {
                                                return $item->estado !== 'PAGO PROCESADO';
                                            });
                                        })
                                        ->sortKeys();
                                @endphp

                                @foreach ($proveedoresConPendientes as $fechaasignada => $cuentas)
                                    <tr>
                                        <td><i class="fas fa-check"></i></td>
                                        <td>{{ $fechaasignada }}</td>
                                        <td>
                                            <abbr title="VER REGISTROS">
                                                <button type="button" class="btn btn-sm btn-botongris" data-toggle="modal" data-target="#modalProveedor{{ Str::slug($fechaasignada) }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </abbr>
                                            <!-- Modal -->
                                            <div class="modal fade" id="modalProveedor{{ Str::slug($fechaasignada) }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog modal-xl" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header d-block text-center py-4" style="background: #efefef">
                                                            <div class="mb-3">
                                                                <h4 class="modal-title font-weight-bold">
                                                                    <strong>CUENTAS POR COBRAR DEL: {{ $fechaasignada }}</strong>
                                                                </h4>
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

                                                            <!-- Contenido de las pestañas -->
                                                            <div class="tab-content mt-3">
                                                                <!-- Pestaña PENDIENTES -->
                                                                <div id="pendientes{{ Str::slug($fechaasignada) }}" class="tab-pane fade show active">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-striped">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="background-color: white">ID Reg.</th>
                                                                                    <th style="background-color: white">Proveedor/Cliente</th>
                                                                                    <th style="background-color: white">Tipo</th>
                                                                                    <th style="background-color: white">Detalle</th>
                                                                                    <th style="background-color: white">Fecha_Cobrar</th>
                                                                                    <th style="background-color: white">N.Cuenta_Destino</th>
                                                                                    <th style="background-color: white">Subto.</th>
                                                                                    <th style="background-color: white">Desc.</th>
                                                                                    <th style="background-color: white">Total</th>
                                                                                    <th style="background-color: white">Estado</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($cuentas->whereIn('estado', ['PENDIENTE', 'SALDO PENDIENTE']) as $pendiente)
                                                                                    <tr>
                                                                                        <td>{{ $pendiente->id }}</td>
                                                                                        <td>{{ $pendiente->proveedornombre }}</td>
                                                                                        <td>{{ $pendiente->tipoorden }}</td>
                                                                                        <td>{{ $pendiente->detalleproducto }}</td>
                                                                                        <td>{{ $pendiente->fechaasignada }}</td>
                                                                                        <td>{{ $pendiente->nrobancodestino  ?? 0 }}</td>
                                                                                        <td>{{ $pendiente->subtotal }}</td>
                                                                                        <td>{{ $pendiente->descuento }}</td>
                                                                                        <td>{{ $pendiente->montototal }}</td>
                                                                                        <td>
                                                                                            @if ($pendiente->estado == 'PENDIENTE')
                                                                                                <span class="badge badge-danger">{{ $pendiente->estado }}</span>
                                                                                            @else
                                                                                                <span class="badge badge-warning">{{ $pendiente->estado }}</span>
                                                                                            @endif
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                                <!-- Pestaña FINALIZADOS -->
                                                                <div id="finalizados{{ Str::slug($fechaasignada) }}" class="tab-pane fade">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-striped">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th style="background-color: white">ID Reg.</th>
                                                                                    <th style="background-color: white">Proveedor/Cliente</th>
                                                                                    <th style="background-color: white">Tipo</th>
                                                                                    <th style="background-color: white">Detalle</th>
                                                                                    <th style="background-color: white">Fecha_Cobrar</th>
                                                                                    <th style="background-color: white">N.Cuenta_Destino</th>
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
                                                                                    <td>{{ $finalizado->detalleproducto }}</td>
                                                                                    <td>{{ $finalizado->fechaasignada }}</td>
                                                                                    <td>{{ $finalizado->nrobancodestino  ?? 0 }}</td>
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
                                                        <div class="modal-footer">
                                                            <a type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</a>
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
</div>

@foreach ($result as $item)
    <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true"> 
        <div class="modal-dialog modal-xxl" role="document">
            <div class="modal-content">
                @php 
                    $sumaPrecios = collect($item['acciones'])->sum(function ($accion) use ($item) {
                        return (is_null($accion['pagoservicioinforme']) && is_null($accion['pagoservicioinformefinal']))
                            && is_numeric($accion['precio']) 
                            && (isset($item['clienteitanombre']) && $accion['clienteitanombre'] == $item['clienteitanombre']) 
                            && (isset($item['fechabateria']) && $accion['fechabateria'] == $item['fechabateria']) 
                            ? $accion['precio'] : 0;
                    });
                @endphp
                <div class="modal-header d-block text-center py-4" style="background: #efefef"> 
                    <div class="mb-3">
                        <h4 class="modal-title font-weight-bold" id="modalLabel{{ $loop->index }}">
                            <strong>{{ $item['clienteitanombre'] }}</strong>
                        </h4>
                        <h5 class="modal-title text-muted" id="modalLabel{{ $loop->index }}" style="margin-bottom: -15px;">
                            Fecha de Batería: {{ \Carbon\Carbon::parse($item['fechabateria'])->format('Y-m-d') }}
                        </h5>
                        <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="tabs-{{ $loop->index }}">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-completos-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completos-{{ $loop->index }}" role="tab" aria-controls="tab-content-completos-{{ $loop->index }}" aria-selected="true">
                                ATENCION PENDIENTE
                            </a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link" id="tab-pendientes-{{ $loop->index }}" data-toggle="tab" href="#tab-content-pendientes-{{ $loop->index }}" role="tab" aria-controls="tab-content-pendientes-{{ $loop->index }}" aria-selected="false">
                                ATENCION COMPLETA E INFORMES PENDIENTES
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-completosprocesados-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completosprocesados-{{ $loop->index }}" role="tab" aria-controls="tab-content-completosprocesados-{{ $loop->index }}" aria-selected="false">
                                ATENCION E INFORMES COMPLETOS
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="tabsContent-{{ $loop->index }}">
                        {{-- ATENCION PENDIENTE --}}
                        <div class="tab-pane fade show active" id="tab-content-completos-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completos-{{ $loop->index }}">
                            <form method="POST" action="{{ route('actualizar.cantidadcuotas') }}">
                                @csrf
                                <div class="table-responsive" style="max-height: 65vh;">
                                    <table class="table table-striped">
                                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                            <tr>
                                                <th>Cuot.</th>
                                                <th>ID</th>
                                                <th>Est. / Esp.</th>
                                                <th>Proveedor</th>
                                                <th>Atención</th>
                                                <th>Precio</th>
                                                <th>Prog.</th>
                                                <th>Informe</th>
                                                <th>Fecha_Pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item['acciones'] as $accion) 
                                                @php
                                                    $hoy = \Carbon\Carbon::now();
                                                    $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                    $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                    $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                    $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                                @endphp

                                                @if ($accion['accion'] !== 'INFORME FINAL')
                                                    @if (is_null($accion['fechaprogramacion']))
                                                        <tr>
                                                            <td>
                                                                @if(is_null($accion['cantidadcuotas']) || $accion['cantidadcuotas'] == '')
                                                                    <input type="checkbox" class="seleccionar-checkbox" name="seleccionados[]" value="{{ $accion['id'] }}">
                                                                @else
                                                                    <span class="badge badge-secondary fs-5 p-2">{{-- {{ $accion['cantidadcuotas'] }} --}}CON CRÉDITO</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $accion['id'] }}</td>
                                                            <td>{{ $accion['accion'] }}</td>
                                                            <td>{{ $accion['proveedorasignado'] }}</td>
                                                            <td>{{ $accion['servicio'] }}</td>
                                                            <td>{{ $accion['precio'] }}</td>
                                                            <td>
                                                                @if ($accion['fechaprogramacion'])
                                                                    {{ $accion['fechaprogramacion'] }}
                                                                @else
                                                                    <div class="badge 
                                                                        {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($accion['informedocumentacion'])
                                                                    {{ $accion['informedocumentacion'] }}
                                                                @else
                                                                    <div class="badge 
                                                                        {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($accion['pagoservicioinforme'])
                                                                <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                    {{ $accion['pagoservicioinforme'] }}
                                                                </div>
                                                                @else
                                                                    <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    @if (is_null($accion['informedocumentacionfinal']))
                                                        <tr>
                                                            <td>
                                                                @if(is_null($accion['cantidadcuotas']) || $accion['cantidadcuotas'] == '')
                                                                    <input type="checkbox" class="seleccionar-checkbox" name="seleccionados[]" value="{{ $accion['id'] }}">
                                                                @else
                                                                    <span class="badge badge-secondary fs-5 p-2">{{-- {{ $accion['cantidadcuotas'] }} --}}CON CRÉDITO</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $accion['id'] }}</td>
                                                            <td>{{ $accion['accion'] }}</td>
                                                            <td>{{ $accion['proveedorasignado'] }}</td>
                                                            <td>{{ $accion['servicio'] }}</td>
                                                            <td>{{ $accion['precio'] }}</td>
                                                            <td>-------------</td>
                                                            <td>
                                                                <span class="badge badge-warning">
                                                                    {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($accion['pagoservicioinformefinal'])
                                                                <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                    {{ $accion['pagoservicioinformefinal'] }}
                                                                </div>
                                                                @else
                                                                    <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mb-3 d-flex align-items-center">
                                        <input type="number" id="cuotasGlobal" name="cantidadcuotas" class="form-control form-control-sm me-2" min="2" max="5" style="width: 200px;" placeholder="NRO. CUOTAS" required>
                                        <button type="submit" class="btn btn-sm btn-outline-success">ASIGNAR CUOTAS</button>
                                    </div>
                                    <small class="text-muted" style="font-style: italic; margin-top: -10px;">AL ASIGNAR CUOTAS TAMBIÉN SE GENERARÁ UNA CUENTA POR COBRAR POR LETRA DE CAMBIO</small>
                                </div>
                            </form>
                        </div>

                        {{-- ATENCION COMPLETA E INFORMES PENDIENTES --}}
                        <div class="tab-pane fade" id="tab-content-pendientes-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-pendientes-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est. / Esp.</th>
                                            <th>Proveedor</th>
                                            <th>Atención</th>
                                            <th>Precio</th>
                                            <th>Prog.</th>
                                            <th>Informe</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                        @php
                                            $hoy = \Carbon\Carbon::now();
                                            $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                            $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                            $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                            $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                        @endphp
                                        
                                        @if ($accion['accion'] !== 'INFORME FINAL')
                                            @if (!is_null($accion['fechaprogramacion']) && is_null($accion['informedocumentacion']))
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    <td>
                                                        @if ($accion['fechaprogramacion'])
                                                            {{ $accion['fechaprogramacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['informedocumentacion'])
                                                            {{ $accion['informedocumentacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinforme'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinforme'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif

                                        @if ($accion['accion'] === 'INFORME FINAL')
                                            
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ATENCION E INFORMES COMPLETOS --}}
                        <div class="tab-pane fade" id="tab-content-completosprocesados-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completosprocesados-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est. / Esp.</th>
                                            <th>Proveedor</th>
                                            <th>Atención</th>
                                            <th>Precio</th>
                                            <th>Prog.</th>
                                            <th>Informe</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                        @php
                                            $hoy = \Carbon\Carbon::now();
                                            $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                            $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                            $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                            $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                        @endphp
                                        @if ($accion['accion'] !== 'INFORME FINAL')
                                            @if (!is_null($accion['fechaprogramacion']) && !is_null($accion['informedocumentacion'])/*  && !is_null($accion['pagoservicioinforme']) */)
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    <td>
                                                        @if ($accion['fechaprogramacion'])
                                                            {{ $accion['fechaprogramacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['informedocumentacion'])
                                                            {{ $accion['informedocumentacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinforme'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinforme'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif

                                        @if ($accion['accion'] === 'INFORME FINAL')
                                            @if (!is_null($accion['informedocumentacionfinal']) && !is_null($accion['pagoservicioinformefinal']))
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    <td>-------------</td>
                                                    <td colspan="2" class="text-center">
                                                        <span class="badge badge-warning">
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinformefinal'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinformefinal'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
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
                <div class="modal-footer">
                    <a type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</a>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach ($result2 as $item)
    <div class="modal fade" id="modalauditoria{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true"> 
        <div class="modal-dialog modal-xxl" role="document">
            <div class="modal-content">
                @php 
                    $sumaPrecios = collect($item['acciones'])->sum(function ($accion) use ($item) {
                        return (is_null($accion['pagoservicioinforme']) && is_null($accion['pagoservicioinformefinal']))
                            && is_numeric($accion['precio']) 
                            && (isset($item['clienteauditorianombre']) && $accion['clienteauditorianombre'] == $item['clienteauditorianombre']) 
                            && (isset($item['fechabateria']) && $accion['fechabateria'] == $item['fechabateria']) 
                            ? $accion['precio'] : 0;
                    });
                @endphp
                <div class="modal-header d-block text-center py-4" style="background: #efefef"> 
                    <div class="mb-3">
                        <h4 class="modal-title font-weight-bold" id="modalLabel{{ $loop->index }}">
                            <strong>{{ $item['clienteauditorianombre'] }}</strong>
                        </h4>
                        <h5 class="modal-title text-muted" id="modalLabel{{ $loop->index }}" style="margin-bottom: -15px;">
                            Fecha de Batería: {{ \Carbon\Carbon::parse($item['fechabateria'])->format('Y-m-d') }}
                        </h5>
                        <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="tabs-{{ $loop->index }}">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-completosauditoria-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completosauditoria-{{ $loop->index }}" role="tab" aria-controls="tab-content-completosauditoria-{{ $loop->index }}" aria-selected="true">
                                ATENCION PENDIENTE
                            </a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link" id="tab-pendientesauditoria-{{ $loop->index }}" data-toggle="tab" href="#tab-content-pendientesauditoria-{{ $loop->index }}" role="tab" aria-controls="tab-content-pendientesauditoria-{{ $loop->index }}" aria-selected="false">
                                ATENCION COMPLETA E INFORMES PENDIENTES
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-completosprocesadosauditoria-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completosprocesadosauditoria-{{ $loop->index }}" role="tab" aria-controls="tab-content-completosprocesadosauditoria-{{ $loop->index }}" aria-selected="false">
                                ATENCION E INFORMES COMPLETOS
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="tabsContent-{{ $loop->index }}">
                        {{-- ATENCION PENDIENTE --}}
                        <div class="tab-pane fade show active" id="tab-content-completosauditoria-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completosauditoria-{{ $loop->index }}">
                            <form method="POST" action="{{ route('actualizar.cantidadcuotas') }}">
                                @csrf
                                <div class="table-responsive" style="max-height: 65vh;">
                                    <table class="table table-striped">
                                        <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                            <tr>
                                                <th>Cuot.</th>
                                                <th>ID</th>
                                                <th>Est. / Esp.</th>
                                                <th>Proveedor</th>
                                                <th>Atención</th>
                                                <th>Precio</th>
                                                <th>Prog.</th>
                                                <th>Informe</th>
                                                <th>Fecha_Pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item['acciones'] as $accion) 
                                                @php
                                                    $hoy = \Carbon\Carbon::now();
                                                    $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                    $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                    $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                    $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                                @endphp

                                                @if ($accion['accion'] !== 'INFORME FINAL')
                                                    @if (is_null($accion['fechaprogramacion']))
                                                        <tr>
                                                            <td>
                                                                @if(is_null($accion['cantidadcuotas']) || $accion['cantidadcuotas'] == '')
                                                                    <input type="checkbox" class="seleccionar-checkbox" name="seleccionados[]" value="{{ $accion['id'] }}">
                                                                @else
                                                                    <span class="badge badge-secondary fs-5 p-2">{{-- {{ $accion['cantidadcuotas'] }} --}}CON CRÉDITO</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $accion['id'] }}</td>
                                                            <td>{{ $accion['accion'] }}</td>
                                                            <td>{{ $accion['proveedorasignado'] }}</td>
                                                            <td>{{ $accion['servicio'] }}</td>
                                                            <td>{{ $accion['precio'] }}</td>
                                                            <td>
                                                                @if ($accion['fechaprogramacion'])
                                                                    {{ $accion['fechaprogramacion'] }}
                                                                @else
                                                                    <div class="badge 
                                                                        {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($accion['informedocumentacion'])
                                                                    {{ $accion['informedocumentacion'] }}
                                                                @else
                                                                    <div class="badge 
                                                                        {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($accion['pagoservicioinforme'])
                                                                <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                    {{ $accion['pagoservicioinforme'] }}
                                                                </div>
                                                                @else
                                                                    <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif

                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    @if (is_null($accion['informedocumentacionfinal']))
                                                        <tr>
                                                            <td>
                                                                @if(is_null($accion['cantidadcuotas']) || $accion['cantidadcuotas'] == '')
                                                                    <input type="checkbox" class="seleccionar-checkbox" name="seleccionados[]" value="{{ $accion['id'] }}">
                                                                @else
                                                                    <span class="badge badge-secondary fs-5 p-2">{{-- {{ $accion['cantidadcuotas'] }} --}}CON CRÉDITO</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $accion['id'] }}</td>
                                                            <td>{{ $accion['accion'] }}</td>
                                                            <td>{{ $accion['proveedorasignado'] }}</td>
                                                            <td>{{ $accion['servicio'] }}</td>
                                                            <td>{{ $accion['precio'] }}</td>
                                                            <td>-------------</td>
                                                            <td>
                                                                <span class="badge badge-warning">
                                                                    {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($accion['pagoservicioinformefinal'])
                                                                <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                    {{ $accion['pagoservicioinformefinal'] }}
                                                                </div>
                                                                @else
                                                                    <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                        PENDIENTE
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="mb-3 d-flex align-items-center">
                                        <input type="number" id="cuotasGlobal" name="cantidadcuotas" class="form-control form-control-sm me-2" min="2" max="5" style="width: 200px;" placeholder="NRO. CUOTAS" required>
                                        <button type="submit" class="btn btn-sm btn-outline-success">ASIGNAR CUOTAS</button>
                                    </div>
                                    <small class="text-muted" style="font-style: italic; margin-top: -10px;">AL ASIGNAR CUOTAS TAMBIÉN SE GENERARÁ UNA CUENTA POR COBRAR POR LETRA DE CAMBIO</small>
                                </div>
                            </form>
                        </div>

                        {{-- ATENCION COMPLETA E INFORMES PENDIENTES --}}
                        <div class="tab-pane fade" id="tab-content-pendientesauditoria-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-pendientesauditoria-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est. / Esp.</th>
                                            <th>Proveedor</th>
                                            <th>Atención</th>
                                            <th>Precio</th>
                                            <th>Prog.</th>
                                            <th>Informe</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                        @php
                                            $hoy = \Carbon\Carbon::now();
                                            $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                            $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                            $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                            $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                        @endphp
                                        @if ($accion['accion'] !== 'INFORME FINAL')
                                            @if (!is_null($accion['fechaprogramacion']) && (is_null($accion['informedocumentacion'])))
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    <td>
                                                        @if ($accion['fechaprogramacion'])
                                                            {{ $accion['fechaprogramacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['informedocumentacion'])
                                                            {{ $accion['informedocumentacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinforme'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinforme'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif

                                        @if ($accion['accion'] === 'INFORME FINAL')
                                            @if (!is_null($accion['fechaprogramacion']) && is_null($accion['informedocumentacionfinal']))
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    <td>-------------</td>
                                                    <td colspan="2" class="text-center">
                                                        <span class="badge badge-warning">
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinformefinal'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinformefinal'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
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

                        {{-- ATENCION E INFORMES COMPLETOS --}}
                        <div class="tab-pane fade" id="tab-content-completosprocesadosauditoria-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completosprocesadosauditoria-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est. / Esp.</th>
                                            <th>Proveedor</th>
                                            <th>Atención</th>
                                            <th>Precio</th>
                                            <th>Prog.</th>
                                            <th>Informe</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                        @php
                                            $hoy = \Carbon\Carbon::now();
                                            $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                            $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                            $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                            $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                        @endphp

                                        @if ($accion['accion'] !== 'INFORME FINAL')
                                            @if (!is_null($accion['fechaprogramacion']) && !is_null($accion['informedocumentacion'])/*  && !is_null($accion['pagoservicioinforme']) */)
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    <td>
                                                        @if ($accion['fechaprogramacion'])
                                                            {{ $accion['fechaprogramacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['informedocumentacion'])
                                                            {{ $accion['informedocumentacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinforme'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinforme'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif

                                        @if ($accion['accion'] === 'INFORME FINAL')
                                            @if (!is_null($accion['informedocumentacionfinal']) && !is_null($accion['pagoservicioinformefinal']))
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    <td>-------------</td>
                                                    <td colspan="2" class="text-center">
                                                        <span class="badge badge-warning">
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinformefinal'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinformefinal'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
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
                <div class="modal-footer">
                    <a type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</a>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach ($result3 as $item)
    <div class="modal fade" id="modalcomun{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true"> 
        <div class="modal-dialog modal-xxl" role="document">
            <div class="modal-content">
                @php 
                    $sumaPrecios = collect($item['acciones'])->sum(function ($accion) use ($item) {
                        return (is_null($accion['pagoservicioinforme']) && is_null($accion['pagoservicioinformefinal']))
                            && is_numeric($accion['precio']) 
                            && (isset($item['clientecomunnombre']) && $accion['clientecomunnombre'] == $item['clientecomunnombre']) 
                            && (isset($item['fechabateria']) && $accion['fechabateria'] == $item['fechabateria']) 
                            ? $accion['precio'] : 0;
                    });
                @endphp
                <div class="modal-header d-block text-center py-4" style="background: #efefef"> 
                    <div class="mb-3">
                        <h4 class="modal-title font-weight-bold" id="modalLabel{{ $loop->index }}">
                            <strong>{{ $item['clientecomunnombre'] }}</strong>
                        </h4>
                        <h5 class="modal-title text-muted" id="modalLabel{{ $loop->index }}" style="margin-bottom: -15px;">
                            Fecha de Batería: {{ \Carbon\Carbon::parse($item['fechabateria'])->format('Y-m-d') }}
                        </h5>
                        <button type="button" class="close position-absolute" style="top: 10px; right: 10px;" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="tabs-{{ $loop->index }}">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-completoscomun-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completoscomun-{{ $loop->index }}" role="tab" aria-controls="tab-content-completoscomun-{{ $loop->index }}" aria-selected="true">
                                ATENCION PENDIENTE
                            </a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link" id="tab-pendientescomun-{{ $loop->index }}" data-toggle="tab" href="#tab-content-pendientescomun-{{ $loop->index }}" role="tab" aria-controls="tab-content-pendientescomun-{{ $loop->index }}" aria-selected="false">
                                ATENCION COMPLETA Y COBROS PENDIENTES
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-completosprocesadoscomun-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completosprocesadoscomun-{{ $loop->index }}" role="tab" aria-controls="tab-content-completosprocesadoscomun-{{ $loop->index }}" aria-selected="false">
                                ATENCION Y COBROS COMPLETOS
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="tabsContent-{{ $loop->index }}">
                        {{-- ATENCION PENDIENTE --}}
                        <div class="tab-pane fade show active" id="tab-content-completoscomun-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completoscomun-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est. / Esp.</th>
                                            <th>Proveedor</th>
                                            <th>Atención</th>
                                            <th>Precio</th>
                                            <th>Prog.</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                            @php
                                                $hoy = \Carbon\Carbon::now();
                                                $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                            @endphp

                                            @if ($accion['accion'] !== 'INFORME FINAL')
                                                @if (is_null($accion['fechaprogramacion']))
                                                    <tr>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td>{{ $accion['accion'] }}</td>
                                                        <td>{{ $accion['proveedorasignado'] }}</td>
                                                        <td>{{ $accion['servicio'] }}</td>
                                                        <td>{{ $accion['precio'] }}</td>
                                                        <td>
                                                            @if ($accion['fechaprogramacion'])
                                                                {{ $accion['fechaprogramacion'] }}
                                                            @else
                                                                <div class="badge 
                                                                    {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($accion['pagoservicioinforme'])
                                                            <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                {{ $accion['pagoservicioinforme'] }}
                                                            </div>
                                                            @else
                                                                <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif

                                            @if ($accion['accion'] === 'INFORME FINAL')
                                                @if (is_null($accion['informedocumentacionfinal']))
                                                    <tr>
                                                        <td>{{ $accion['id'] }}</td>
                                                        <td>{{ $accion['accion'] }}</td>
                                                        <td>{{ $accion['proveedorasignado'] }}</td>
                                                        <td>{{ $accion['servicio'] }}</td>
                                                        <td>{{ $accion['precio'] }}</td>
                                                        <td>-------------</td>
                                                        <td>
                                                            @if ($accion['pagoservicioinformefinal'])
                                                            <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                {{ $accion['pagoservicioinformefinal'] }}
                                                            </div>
                                                            @else
                                                                <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                    PENDIENTE
                                                                </div>
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

                        {{-- ATENCION COMPLETA E INFORMES PENDIENTES --}}
                        <div class="tab-pane fade" id="tab-content-pendientescomun-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-pendientescomun-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est. / Esp.</th>
                                            <th>Proveedor</th>
                                            <th>Atención</th>
                                            <th>Precio</th>
                                            <th>Prog.</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                        @php
                                            $hoy = \Carbon\Carbon::now();
                                            $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                            $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                            $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                            $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                        @endphp
                                        @if ($accion['accion'] !== 'INFORME FINAL')
                                            @if (!is_null($accion['fechaprogramacion']) && is_null($accion['informedocumentacion']) && is_null($accion['pagoservicioinforme']))
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    <td>
                                                        @if ($accion['fechaprogramacion'])
                                                            {{ $accion['fechaprogramacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinforme'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinforme'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif

                                        @if ($accion['accion'] === 'INFORME FINAL')
                                            @if (!is_null($accion['fechaprogramacion']) && is_null($accion['informedocumentacionfinal']))
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    <td>-------------</td>
                                                    <td>
                                                        @if ($accion['pagoservicioinformefinal'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinformefinal'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
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

                        {{-- ATENCION E INFORMES COMPLETOS --}}
                        <div class="tab-pane fade" id="tab-content-completosprocesadoscomun-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completosprocesadoscomun-{{ $loop->index }}">
                            <div class="table-responsive" style="max-height: 65vh;">
                                <table class="table table-striped">
                                    <thead style="position: sticky; top: 0; z-index: 1010; background-color: #f8f9fa;">
                                        <tr>
                                            <th>ID</th>
                                            <th>Est. / Esp.</th>
                                            <th>Proveedor</th>
                                            <th>Atención</th>
                                            <th>Precio</th>
                                            <th>Prog.</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                            @php
                                                $hoy = \Carbon\Carbon::now();
                                                $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                            @endphp
                                            @if (/* !is_null($accion['fechaprogramacion']) && !is_null($accion['informedocumentacion']) &&  */!is_null($accion['pagoservicioinforme']))
                                                <tr>
                                                    <td>{{ $accion['id'] }}</td>
                                                    <td>{{ $accion['accion'] }}</td>
                                                    <td>{{ $accion['proveedorasignado'] }}</td>
                                                    <td>{{ $accion['servicio'] }}</td>
                                                    <td>{{ $accion['precio'] }}</td>
                                                    @if ($accion['accion'] === 'INFORME FINAL')
                                                        <td>-------------</td>
                                                        <td>
                                                            @if ($accion['pagoservicioinformefinal'])
                                                            <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                {{ $accion['pagoservicioinformefinal'] }}
                                                            </div>
                                                            @else
                                                                <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td>
                                                            @if ($accion['fechaprogramacion'])
                                                                {{ $accion['fechaprogramacion'] }}
                                                            @else
                                                                <div class="badge 
                                                                    {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($accion['pagoservicioinforme'])
                                                            <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                {{ $accion['pagoservicioinforme'] }}
                                                            </div>
                                                            @else
                                                                <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                                @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</a>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach ($result4 as $item)
    <div class="modal fade" id="modalproveedores{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true"> 
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
                            <a class="nav-link active" id="tab-completosproveedor-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completosproveedor-{{ $loop->index }}" role="tab" aria-controls="tab-content-completosproveedor-{{ $loop->index }}" aria-selected="true">
                                INFORMES COMPLETOS Y PAGOS PENDIENTES
                            </a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link" id="tab-pendientesproveedor-{{ $loop->index }}" data-toggle="tab" href="#tab-content-pendientesproveedor-{{ $loop->index }}" role="tab" aria-controls="tab-content-pendientesproveedor-{{ $loop->index }}" aria-selected="false">
                                INFORMES PENDIENTES
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-completosprocesadosproveedor-{{ $loop->index }}" data-toggle="tab" href="#tab-content-completosprocesadosproveedor-{{ $loop->index }}" role="tab" aria-controls="tab-content-completosprocesadosproveedor-{{ $loop->index }}" aria-selected="false">
                                INFORMES COMPLETOS Y PAGOS PROCESADOS
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="tabsContent-{{ $loop->index }}">
                        {{-- INFORMES COMPLETOS Y PAGOS PENDIENTES --}}
                        <div class="tab-pane fade show active" id="tab-content-completosproveedor-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completosproveedor-{{ $loop->index }}">
                            <form action="{{ route('actualizarFactura') }}" method="POST">
                                @csrf
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
                                                <th>Pago</th>
                                                <th>Prog.</th>
                                                <th>Informe</th>
                                                <th>Fecha_Pago</th>
                                                <th hidden>ID Prog</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item['acciones'] as $accion) 
                                                @php
                                                    $hoy = \Carbon\Carbon::now();
                                                    $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                    $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                    $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                    $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                                @endphp
                                                
                                                @if ($accion['accion'] !== 'INFORME FINAL')
                                                    @if (!is_null($accion['informedocumentacion']) && is_null($accion['pagoservicioinforme']))
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
                                                        <td>{{ number_format($accion['precio'] - $accion['preciocompra'], 2) }}</td>
                                                        <td>
                                                            @if ($accion['fechaprogramacion'])
                                                                {{ $accion['fechaprogramacion'] }}
                                                            @else
                                                                <div class="badge 
                                                                    {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($accion['informedocumentacion'])
                                                                {{ $accion['informedocumentacion'] }}
                                                            @else
                                                                <div class="badge 
                                                                    {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($accion['pagoservicioinforme'])
                                                            <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                {{ $accion['pagoservicioinforme'] }}
                                                            </div>
                                                            @else
                                                                <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td hidden>{{ $accion['idprogramacion'] }}</td>
                                                    </tr>
                                                    @endif
                                                @endif

                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    @if (!is_null($accion['informedocumentacionfinal']) && is_null($accion['pagoservicioinformefinal']))
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
                                                        <td>{{ number_format($accion['precio'] - $accion['preciocompra'], 2) }}</td>
                                                        <td>
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td>
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </td>
                                                        <td>
                                                            @if ($accion['pagoservicioinformefinal'])
                                                            <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                                {{ $accion['pagoservicioinformefinal'] }}
                                                            </div>
                                                            @else
                                                                <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                    PENDIENTE
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td hidden>{{ $accion['provinfofinalid'] }}</td>
                                                    </tr>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>

                        {{-- INFORMES PENDIENTES --}}
                        <div class="tab-pane fade" id="tab-content-pendientesproveedor-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-pendientesproveedor-{{ $loop->index }}">
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
                                            <th>Pago</th>
                                            <th>Prog.</th>
                                            <th>Informe</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                            @php
                                                $hoy = \Carbon\Carbon::now();
                                                $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                            @endphp
                                            @if (is_null($accion['informedocumentacion']) && is_null($accion['informedocumentacionfinal'])) 
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
                                                <td>{{ number_format((float) ($accion['precio'] ?? 0) - (float) ($accion['preciocompra'] ?? 0), 2) }}</td>
                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    <td>
                                                        {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-warning">
                                                            {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinformefinal'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinformefinal'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                @else
                                                    <td>
                                                        @if ($accion['fechaprogramacion'])
                                                            {{ $accion['fechaprogramacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['informedocumentacion'])
                                                            {{ $accion['informedocumentacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinforme'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinforme'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- INFORMES COMPLETOS Y PAGOS PROCESADOS --}}
                        <div class="tab-pane fade" id="tab-content-completosprocesadosproveedor-{{ $loop->index }}" role="tabpanel" aria-labelledby="tab-completosprocesadosproveedor-{{ $loop->index }}">
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
                                            <th>Pago</th>
                                            <th>Prog.</th>
                                            <th>Informe</th>
                                            <th>Fecha_Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item['acciones'] as $accion) 
                                            @php
                                                $hoy = \Carbon\Carbon::now();
                                                $fechabateria = \Carbon\Carbon::parse($item['fechabateria']);
                                                $diasDesdeBateria = $fechabateria->diffInDays($hoy);
                                                $fechaprogramacion = $accion['fechaprogramacion'] ? \Carbon\Carbon::parse($accion['fechaprogramacion']) : null;
                                                $fechaatencionprogramacion = $accion['fechaatencionprogramacion'] ? \Carbon\Carbon::parse($accion['fechaatencionprogramacion']) : null;
                                            @endphp
                                            @if ((!is_null($accion['informedocumentacion']) && !is_null($accion['pagoservicioinforme'])) || (!is_null($accion['informedocumentacionfinal']) && !is_null($accion['pagoservicioinformefinal'])))
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
                                                <td>{{ number_format($accion['precio'] - $accion['preciocompra'], 2) }}</td>
                                                
                                                @if ($accion['accion'] === 'INFORME FINAL')
                                                    <td>
                                                        {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                    </td>
                                                    <td>
                                                        {{ $accion['informedocumentacionfinal'] ?? 'PENDIENTE' }}
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinformefinal'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinformefinal'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                @else
                                                    <td>
                                                        @if ($accion['fechaprogramacion'])
                                                            {{ $accion['fechaprogramacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $diasDesdeBateria >= 14 ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['informedocumentacion'])
                                                            {{ $accion['informedocumentacion'] }}
                                                        @else
                                                            <div class="badge 
                                                                {{ $fechaatencionprogramacion ? 'badge-danger' : 'badge-warning' }}">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($accion['pagoservicioinforme'])
                                                        <div class="badge badge-success" style="font-size: 0.8rem; padding: 6px;">
                                                            {{ $accion['pagoservicioinforme'] }}
                                                        </div>
                                                        @else
                                                            <div class="badge badge-danger" style="font-size: 0.8rem; padding: 6px;">
                                                                PENDIENTE
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
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
        function validarCantidad(input) {
            if (parseInt(input.value) < 2) {
                input.value = 2;
            }
            if (parseInt(input.value) > 5) {
                input.value = 5;
            }
        }
    </script>
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

