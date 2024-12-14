@extends('adminlte::page')
    
@section('content_header')
<a type="button" class="btn btn-sm btn-crear float-right" data-toggle="modal" data-target="#calendarModal">CONTROL DE PAGOS
</a>

<div class="container">
    <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h4 class="modal-title" id="calendarModalLabel">
                        Pagos de {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
                    </h4>
                    <button type="button" class="btn-close text-white" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
<style>
    #customCalendar table {
    width: 100%;
    border-collapse: collapse;
}

#customCalendar th {
    background-color: #f8f9fa;
    padding: 10px;
    text-transform: uppercase;
    font-size: 0.85rem;
}

#customCalendar td {
    height: 80px;
    vertical-align: top;
    padding: 5px;
    border: 1px solid #dee2e6;
    position: relative;
}

#customCalendar td strong {
    font-size: 1rem;
    color: #495057;
}

#customCalendar .badge {
    font-size: 0.75rem;
    padding: 4px 6px;
    margin-top: 4px;
    display: block;
}

#customCalendar td div {
    font-size: 0.85rem;
    margin-top: 5px;
    text-align: center;
}

#customCalendar td.sin-programar {
    background-color: #f8f8f8; /* Fondo gris claro */
    color: #6c757d; /* Texto gris oscuro */
}

#customCalendar td.sin-programar strong {
    color: #495057; /* Color para el número del día */
}
#calendarModalLabel {
    text-transform: uppercase;
    font-weight: bold;
}
.current-day {
    background-color: #fff8e0; /* Fondo amarillo bajito */
    font-weight: bold;
}


</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarContainer = document.getElementById('customCalendar');
    let year = {{ $year }};
    let month = {{ $month }};
    const modalTitle = document.getElementById('calendarModalLabel');

    function fetchRecords(year, month) {
        return fetch(`{{ route('admin.admprogramaciones.pagosprogramaciones') }}?year=${year}&month=${month}`, {
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
        const daysInMonth = new Date(year, month, 0).getDate(); // Días en el mes
        const firstDay = new Date(year, month - 1, 1).getDay(); // Día de la semana en el que empieza el mes

        // Actualizar el título del modal con el mes y año actual
        modalTitle.innerHTML = `Pagos de ${new Date(year, month - 1).toLocaleString('default', { month: 'long' })} ${year}`;

        // Obtener datos del servidor
        fetchRecords(year, month).then(records => {
            const recordsByDate = {};
            records.forEach(record => {
                recordsByDate[record.fechaasignada] = {
                    procesados: record.procesados,
                    sin_pago: record.sin_pago,
                };
            });

            // Renderizar el calendario con los datos obtenidos
            renderCalendar(year, month, daysInMonth, firstDay, recordsByDate);
        });
    }

    function renderCalendar(year, month, daysInMonth, firstDay, recordsByDate) {
    const table = document.createElement('table');
    table.classList.add('table', 'table-bordered', 'text-center');

    const header = document.createElement('thead');
    header.innerHTML = `
        <tr>
            <th>Lunes</th>
            <th>Martes</th>
            <th>Miércoles</th>
            <th>Jueves</th>
            <th>Viernes</th>
            <th>Sábado</th>
            <th>Domingo</th>
        </tr>`;
    table.appendChild(header);

    const body = document.createElement('tbody');
    let row = document.createElement('tr');

    // Ajuste para que el primer día se muestre correctamente
    let startDay = firstDay === 0 ? 6 : firstDay - 1;

    // Agregar celdas vacías al inicio
    for (let i = 0; i < startDay; i++) {
        const cell = document.createElement('td');
        row.appendChild(cell);
    }

    const currentDate = new Date();
    const currentDay = currentDate.getDate();
    const currentMonth = currentDate.getMonth() + 1; // Mes actual (1 basado)

    // Agregar días del mes
    for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement('td');
        const date = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        // Verificar si el día actual es el mismo
        if (day === currentDay && month === currentMonth && year === currentDate.getFullYear()) {
            cell.classList.add('current-day');  // Añadir clase para el fondo amarillo
        }

        if (recordsByDate[date]) {
            const { procesados, sin_pago } = recordsByDate[date];

            let content = `<strong>${day}</strong>`;
            content += `<div><span class="badge badge-success">Procesados: ${procesados}</span></div>`;
            if (sin_pago > 0) {
                content += `<div><span class="badge badge-danger">Sin Procesar: ${sin_pago}</span></div>`;
            }

            cell.innerHTML = content;
        } else {
            cell.innerHTML = `<strong>${day}</strong><div>Sin prog.</div>`;
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
}


    // Inicializar el calendario con el mes actual
    loadCalendar(year, month);

    // Navegar entre los meses
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

<h1>PAGOS DE PROGRAMACIONES ({{ $fechaActual }})</h1>
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
    <nav class="navbar navbar-expand-lg float-right">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center ml-auto">
                <form action="{{ route('buscarprogramacionesporfecha') }}" method="get" class="form-inline">
                    <div class="form-group mr-2">
                        <!-- Campo para ID, Nombre o CI -->
                        <input 
                            name="criterio" 
                            class="form-control" 
                            type="text" 
                            placeholder="ID, Nombre del Cliente" 
                            value="{{ old('criterio') }}" 
                            aria-label="Criterio de búsqueda">
                    </div>
                    <div class="form-group mr-2">
                        <!-- Campo para la fecha -->
                        <input 
                            name="fecha" 
                            class="form-control" 
                            type="date" 
                            {{-- max="{{ now()->toDateString() }}" --}} 
                            value="{{ old('fecha') }}" 
                            aria-label="Fecha">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">BUSCAR</button>
                </form>
            </div>
        </div>
    </nav>
    
    {{-- PESTAÑAS SUPERIORES --}}
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    PAGOS PENDIENTES INT.
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    PAGOS PENDIENTES EXT.
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    PAGOS PROCESADOS
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-6" data-toggle="tab" href="#tab-content-6" role="tab" aria-controls="tab-content-6" aria-selected="true">
                    PAGOS PEND. INFO. FINAL
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-7" data-toggle="tab" href="#tab-content-7" role="tab" aria-controls="tab-content-7" aria-selected="true">
                    PAGOS PROC. INFO. FINAL
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">

            {{-- PAGOS PENDIENTES INTERNOS --}}
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1"> 
                <form method="POST" action="{{ route('confirmar-pagos') }}">
                    @csrf
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th hidden>Sucursal</th>
                                <th>ID Prog</th>
                                <th>Tipo Cliente</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Acción</th>
                                <th>Fecha batería</th>
                                <th>Fecha programada</th>
                                <th>Hora programada</th>
                                <th>Servicio</th>
                                <th>Precio</th>
                                <th>Selec.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nombreUsuario = auth()->user()->name;
                                $sucursalUsuario = auth()->user()->sucursal;
                            @endphp
                                @foreach ($pagosprogramacionesita as $programacion)
                                    @if (
                                        (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                        !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                    )
                                    <tr>
                                        <td hidden>{{$programacion->cliente_sucursal }}</td>
                                        <td>{{ $programacion->id }}</td>
                                        <td>CLIENTE ITA</td>
                                        <td>{{ $programacion->clienteitanombre }}</td>
                                        <td>{{ $programacion->proveedornombre }}</td>
                                        <td>{{ $programacion->accionnombre }}</td>
                                        <td>{{ $programacion->fechabateria }}</td>
                                        <td>{{ $programacion->fechaasignada }}</td>
                                        <td>{{ $programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                        <td>{{ $programacion->servicio }}</td>
                                        <td>{{ $programacion->precio }}</td>
                                        <td>
                                            <input type="checkbox" name="programaciones[]" value="{{ $programacion->id }}" class="programacion-checkbox">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach

                                @foreach ($pagosprogramacionescomun as $programacion)
                                    @if (
                                        (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                        !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                    )
                                    <tr>
                                        <td hidden>{{$programacion->cliente_sucursal }}</td>
                                        <td>{{ $programacion->id }}</td>
                                        <td>CLIENTE COMÚN</td>
                                        <td>{{ $programacion->clientecomunnombre }}</td>
                                        <td>{{ $programacion->proveedornombre }}</td>
                                        <td>{{ $programacion->accionnombre }}</td>
                                        <td>{{ $programacion->fechabateria }}</td>
                                        <td>{{ $programacion->fechaasignada }}</td>
                                        <td>{{ $programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                        <td>{{ $programacion->servicio }}</td>
                                        <td>{{ $programacion->precio }}</td>
                                        <td>
                                            <input type="checkbox" name="programaciones[]" value="{{ $programacion->id }}" class="programacion-checkbox">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                                @foreach ($pagosprogramacionesauditoria as $programacion)
                                    @if (
                                        (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                        !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                    )
                                    <tr>
                                        <td hidden>{{$programacion->cliente_sucursal }}</td>
                                        <td>{{ $programacion->id }}</td>
                                        <td>CLIENTE AUDITORÍA</td>
                                        <td>{{ $programacion->clienteauditorianombre }}</td>
                                        <td>{{ $programacion->proveedornombre }}</td>
                                        <td>{{ $programacion->accionnombre }}</td>
                                        <td>{{ $programacion->fechabateria }}</td>
                                        <td>{{ $programacion->fechaasignada }}</td>
                                        <td>{{ $programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                        <td>{{ $programacion->servicio }}</td>
                                        <td>{{ $programacion->precio }}</td>
                                        <td>
                                            <input type="checkbox" name="programaciones[]" value="{{ $programacion->id }}" class="programacion-checkbox">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary" id="confirmar-pago-btn" disabled>Confirmar Pago</button>
                    </div>
                </form>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const checkboxes = document.querySelectorAll('.programacion-checkbox');
                    const confirmButton = document.getElementById('confirmar-pago-btn');
                    function toggleButtonState() {
                        const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                        confirmButton.disabled = !anyChecked;
                    }
            
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', toggleButtonState);
                    });
                    toggleButtonState();
                });
            </script>
            
            {{-- PAGOS PENDIENTES EXTERNOS --}}
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5"> 
                <form method="POST" action="{{ route('confirmar-pagos') }}">
                    @csrf
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th hidden>Sucursal</th>
                                <th>ID Prog</th>
                                <th>Tipo Cliente</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Acción</th>
                                <th>Fecha batería</th>
                                <th>Fecha programada</th>
                                <th>Hora programada</th>
                                <th>Servicio</th>
                                <th>Precio</th>
                                <th>Selec.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nombreUsuario = auth()->user()->name;
                                $sucursalUsuario = auth()->user()->sucursal;
                            @endphp
                                @foreach ($pagosexternosprogramacionesita as $programacion)
                                    @if (
                                        (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                        !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                    )
                                    <tr>
                                        <td hidden>{{$programacion->cliente_sucursal }}</td>
                                        <td>{{ $programacion->id }}</td>
                                        <td>CLIENTE ITA</td>
                                        <td>{{ $programacion->clienteitanombre }}</td>
                                        <td>{{ $programacion->proveedornombre }}</td>
                                        <td>{{ $programacion->accionnombre }}</td>
                                        <td>{{ $programacion->fechabateria }}</td>
                                        <td>{{ $programacion->fechaasignada }}</td>
                                        <td>{{ $programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                        <td>{{ $programacion->servicio }}</td>
                                        <td>{{ $programacion->precio }}</td>
                                        <td>
                                            <input type="checkbox" name="programaciones[]" value="{{ $programacion->id }}" class="programacion-checkbox2">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                                @foreach ($pagosexternosprogramacionescomun as $programacion)
                                    @if (
                                        (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                        !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                    )
                                    <tr>
                                        <td hidden>{{$programacion->cliente_sucursal }}</td>
                                        <td>{{ $programacion->id }}</td>
                                        <td>CLIENTE COMÚN</td>
                                        <td>{{ $programacion->clientecomunnombre }}</td>
                                        <td>{{ $programacion->proveedornombre }}</td>
                                        <td>{{ $programacion->accionnombre }}</td>
                                        <td>{{ $programacion->fechabateria }}</td>
                                        <td>{{ $programacion->fechaasignada }}</td>
                                        <td>{{ $programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                        <td>{{ $programacion->servicio }}</td>
                                        <td>{{ $programacion->precio }}</td>
                                        <td>
                                            <input type="checkbox" name="programaciones[]" value="{{ $programacion->id }}" class="programacion-checkbox2">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                                @foreach ($pagosexternosprogramacionesauditoria as $programacion)
                                    @if (
                                        (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                        !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                    )
                                    <tr>
                                        <td hidden>{{$programacion->cliente_sucursal }}</td>
                                        <td>{{ $programacion->id }}</td>
                                        <td>CLIENTE AUDITORÍA</td>
                                        <td>{{ $programacion->clienteauditorianombre }}</td>
                                        <td>{{ $programacion->proveedornombre }}</td>
                                        <td>{{ $programacion->accionnombre }}</td>
                                        <td>{{ $programacion->fechabateria }}</td>
                                        <td>{{ $programacion->fechaasignada }}</td>
                                        <td>{{ $programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                        <td>{{ $programacion->servicio }}</td>
                                        <td>{{ $programacion->precio }}</td>
                                        <td>
                                            <input type="checkbox" name="programaciones[]" value="{{ $programacion->id }}" class="programacion-checkbox2">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary" id="confirmar-pago-btn2" disabled>Confirmar Pago</button>
                    </div>
                </form>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const checkboxes = document.querySelectorAll('.programacion-checkbox2');
                    const confirmButton = document.getElementById('confirmar-pago-btn2');
                    function toggleButtonState() {
                        const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                        confirmButton.disabled = !anyChecked;
                    }
            
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', toggleButtonState);
                    });
                    toggleButtonState();
                });
            </script>

            {{-- PAGOS PROCESADOS --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th hidden>Sucursal</th>
                            <th>ID Prog</th>
                            <th>Tipo Cliente</th>
                            <th>Cliente</th>
                            <th>Proveedor</th>
                            <th>Acción</th>
                            <th>Fecha bateria</th>
                            <th>Fecha programada</th>
                            <th>Hora programada</th>
                            <th>Servicio</th>
                            <th>Precio</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $nombreUsuario = auth()->user()->name;
                            $sucursalUsuario = auth()->user()->sucursal;
                        @endphp
                        
                            @foreach ($pagadosprogramacionesita as $programacion)
                                @if (
                                    (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                    !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                )
                                <tr>
                                    <td hidden>{{$programacion->cliente_sucursal }}</td>
                                    <td>{{ $programacion->id }}</td>
                                    <td>CLIENTE ITA</td>
                                    <td>{{$programacion->clienteitanombre}}</td>
                                    <td>{{$programacion->proveedornombre}}</td>
                                    <td>{{$programacion->accionnombre}}</td>
                                    <td>{{$programacion->fechabateria}}</td>
                                    <td>{{$programacion->fechaasignada}}</td>
                                    <td>{{$programacion->horadesde }} - {{ $programacion->horahasta }}</td>
                                    <td>{{$programacion->servicio}}</td>
                                    <td>{{$programacion->precio}}</td>
                                    
                                </tr>
                                @endif
                            @endforeach
                            @foreach ($pagadosprogramacionescomun as $programacion)
                                @if (
                                    (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                    !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                )
                                <tr>
                                    <td hidden>{{$programacion->cliente_sucursal }}</td>
                                    <td>{{ $programacion->id }}</td>
                                    <td>CLIENTE COMÚN</td>
                                    <td>{{$programacion->clientecomunnombre}}</td>
                                    <td>{{$programacion->proveedornombre}}</td>
                                    <td>{{$programacion->accionnombre}}</td>
                                    <td>{{$programacion->fechabateria}}</td>
                                    <td>{{$programacion->fechaasignada}}</td>
                                    <td>{{$programacion->horadesde}} - {{$programacion->horahasta}}</td>
                                    <td>{{$programacion->servicio}}</td>
                                    <td>{{$programacion->precio}}</td>
                                    
                                </tr>
                                @endif
                            @endforeach
                            @foreach ($pagadosprogramacionesauditoria as $programacion)
                                @if (
                                    (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                    !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                )
                                <tr>
                                    <td hidden>{{$programacion->cliente_sucursal }}</td>
                                    <td>{{ $programacion->id }}</td>
                                    <td>CLIENTE AUDITORÍA</td>
                                    <td>{{$programacion->clienteauditorianombre}}</td>
                                    <td>{{$programacion->proveedornombre}}</td>
                                    <td>{{$programacion->accionnombre}}</td>
                                    <td>{{$programacion->fechabateria}}</td>
                                    <td>{{$programacion->fechaasignada}}</td>
                                    <td>{{$programacion->horadesde}} - {{ $programacion->horahasta}}</td>
                                    <td>{{$programacion->servicio}}</td>
                                    <td>{{$programacion->precio}}</td>
                                </tr>
                                @endif
                            @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGOS PENDIENTES INFORMES FINALES --}}
            <div class="tab-pane fade" id="tab-content-6" role="tabpanel" aria-labelledby="tab-6"> 
                <form method="POST" action="{{ route('confirmar-pagos-informefinal') }}">
                    @csrf
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th hidden>Sucursal</th>
                                <th>ID</th>
                                <th>Tipo Cliente</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Acción</th>
                                <th>Fecha batería</th>
                                <th>Servicio</th>
                                <th>Precio</th>
                                <th>Selec.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nombreUsuario = auth()->user()->name;
                                $sucursalUsuario = auth()->user()->sucursal;
                            @endphp
                                @foreach ($pagosinformefinalita as $programacion)
                                    @if (
                                        (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                        !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                    )
                                    <tr>
                                        <td hidden>{{$programacion->cliente_sucursal }}</td>
                                        <td>{{ $programacion->id }}</td>
                                        <td>CLIENTE ITA</td>
                                        <td>{{ $programacion->clienteitanombre }}</td>
                                        <td>{{ $programacion->proveedorasignado }}</td>
                                        <td>INFORME FINAL</td>
                                        <td>{{ $programacion->fechabateria }}</td>
                                        <td>{{ $programacion->servicio }}</td>
                                        <td>{{ $programacion->precio }}</td>
                                        <td>
                                            <input type="checkbox" name="programaciones[]" value="{{ $programacion->id }}" class="programacion-checkbox3">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                    
                                @foreach ($pagosinformefinalauditoria as $programacion)
                                    @if (
                                        (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                        !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                    )
                                    <tr>
                                        <td hidden>{{$programacion->cliente_sucursal }}</td>
                                        <td>{{ $programacion->id }}</td>
                                        <td>CLIENTE ITA</td>
                                        <td>{{ $programacion->clienteauditorianombre }}</td>
                                        <td>{{ $programacion->proveedorasignado }}</td>
                                        <td>INFORME FINAL</td>
                                        <td>{{ $programacion->fechabateria }}</td>
                                        <td>{{ $programacion->servicio }}</td>
                                        <td>{{ $programacion->precio }}</td>
                                        <td>
                                            <input type="checkbox" name="programaciones[]" value="{{ $programacion->id }}" class="programacion-checkbox3">
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary" id="confirmar-pago-btn3" disabled>Confirmar Pago</button>
                    </div>
                </form>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const checkboxes = document.querySelectorAll('.programacion-checkbox3');
                    const confirmButton = document.getElementById('confirmar-pago-btn3');
                    function toggleButtonState() {
                        const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                        confirmButton.disabled = !anyChecked;
                    }
            
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', toggleButtonState);
                    });
                    toggleButtonState();
                });
            </script>

            {{-- PAGOS PROCESADOS INFORMES FINALES--}}
            <div class="tab-pane fade" id="tab-content-7" role="tabpanel" aria-labelledby="tab-7">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th hidden>Sucursal</th>
                            <th>ID Prog</th>
                            <th>Tipo Cliente</th>
                            <th>Cliente</th>
                            <th>Proveedor</th>
                            <th>Acción</th>
                            <th>Fecha bateria</th>
                            <th>Servicio</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $nombreUsuario = auth()->user()->name;
                            $sucursalUsuario = auth()->user()->sucursal;
                        @endphp
                        
                            @foreach ($pagosprocesadosinformefinalita as $programacion)
                                @if (
                                    (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                    !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                )
                                <tr>
                                    <td hidden>{{$programacion->cliente_sucursal }}</td>
                                    <td>{{ $programacion->id }}</td>
                                    <td>CLIENTE ITA</td>
                                    <td>{{$programacion->clienteitanombre}}</td>
                                    <td>{{$programacion->proveedorasignado}}</td>
                                    <td>INFORME FINAL</td>
                                    <td>{{$programacion->fechabateria}}</td>
                                    <td>{{$programacion->servicio}}</td>
                                    <td>{{$programacion->precio}}</td>
                                </tr>
                                @endif
                            @endforeach
                            @foreach ($pagosprocesadosinformefinalauditoria as $programacion)
                                @if (
                                    (in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO']) && $sucursalUsuario == $programacion->cliente_sucursal) || 
                                    !in_array($nombreUsuario, ['MARLENE ANDREA MONTELLANO ORTIZ', 'VANESSA MAMANI HUANACO'])
                                )
                                <tr>
                                    <td hidden>{{$programacion->cliente_sucursal }}</td>
                                    <td>{{ $programacion->id }}</td>
                                    <td>CLIENTE AUDITORÍA</td>
                                    <td>{{$programacion->clienteauditorianombre}}</td>
                                    <td>{{$programacion->proveedorasignado}}</td>
                                    <td>INFORME FINAL</td>
                                    <td>{{$programacion->fechabateria}}</td>
                                    <td>{{$programacion->servicio}}</td>
                                    <td>{{$programacion->precio}}</td>
                                </tr>
                                @endif
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .nav-tabs {
            display: flex;
            justify-content: space-between;
        }

        .nav-tabs .nav-item {
            flex: 1;
        }

        .nav-tabs .nav-link {
            display: block;
            text-align: center;
            width: 100%;
            font-weight: bold;
            font-size: 15px;
            color: #faa625;
            background-color: #fef4e7;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
            font-size: 15px;
            color: #94c93b;
            background-color: #ffffff;
        }

        .circle {
            display: inline-block;
            width: 30px;
            height: 20px;
            line-height: 20px;
            border-radius: 50%;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-left: 8px;
        }

        .nav-link.active .circle {
            background-color: #94c93b;
            color: #fff;
        }

        .nav-link .circle {
            background-color: #faa625;
            color: #fff;
        }
    .custom-select-wrapper {
        position: relative;
        display: inline-block;
        width: 150px;
    }
    .custom-select-wrapper select {
        width: 100%;
        padding: 6px 26px 6px 10px;
        font-size: 14px;
        border: none;
        border-radius: 3px;
        background-color: #f8f9fa;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
    }
    .custom-select-wrapper select:focus {
        outline: none;
    }
    .custom-select-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        pointer-events: none;
        color: #000000;
    }
    .custom-select-wrapper select {
        background-color:  #eff9df;
        color: #000000;
        border-color: #000000;
        border-radius: 5px;
        padding: 10px 20px;
    }
    .custom-select-wrapper select:hover {
        background-color: #f4e1c6;
        color: #000000;
    }
    .custom-select-wrapper select option {
        background-color: #ffffff;
    }
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;
    }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-buscar { 
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-buscar:hover {
        background-color: #faa625;
        color: #ffffff;
    }  
    .btn-mostrartodo { 
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-mostrartodo:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-bateria {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-bateria:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-programar {
        background-color:  #ffffff;
        color: #2136bd;
        border-color: #2136bd;
        border-radius: 5px;
    }
    .btn-programar:hover {
        background-color: #2136bd;
        color: #ffffff;
    }
    .btn-estadoprogramacion {
        background-color:  #ffffff;
        color: #58a6f4;
        border-color: #58a6f4;
        border-radius: 5px;
    }
    .btn-estadoprogramacion:hover {
        background-color: #58a6f4;
        color: #ffffff;
    }
    .btn-subirdocumentacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-subirdocumentacion:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #8721f3;
        border-color: #8721f3;
        border-radius: 5px;
    }
    .btn-verdocumentacion:hover {
        background-color: #8721f3;
        color: #ffffff;
    }
    .btn-formulario {
        background-color:  #ffffff;
        color: #ea3ab8;
        border-color: #ea3ab8;
        border-radius: 5px;
    }
    .btn-formulario:hover {
        background-color: #ea3ab8;
        color: #ffffff;
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-verdocumentacion:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verimagen {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-verimagen:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-descargarimagen {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-descargarimagen:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-descargardocumentacion {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-descargardocumentacion:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El perfil se eliminó con éxito',
      'success')
    </script>
    @endif
<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "Este perfil se eliminará definitivamente",
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
    $(document).ready(function() {
        $('input[name="buscarpor"]').on('keyup', function() {
            var query = $(this).val();
            var botonBuscar = $('#btn-buscar');
            if (query.trim() === '') {
                botonBuscar.prop('disabled', true);
            } else {
                botonBuscar.prop('disabled', false);
            }
        });
    });
</script>
@endsection