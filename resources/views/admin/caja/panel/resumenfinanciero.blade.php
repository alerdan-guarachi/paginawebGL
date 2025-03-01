@extends('adminlte::page')
    
@section('content_header')
<a type="button" class="btn btn-outline-secondary float-right" data-toggle="modal" data-target="#calendarModal">MOVIMIENTOS APROXIMADOS</a>

<div class="container">
    <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content shadow">
                <div class="modal-header">
                    <h4 class="modal-title" id="calendarModalLabel">
                        Total de pagos aproximados de {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
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
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarContainer = document.getElementById('customCalendar');
            let year = {{ $year }};
            let month = {{ $month }};
            const modalTitle = document.getElementById('calendarModalLabel');

            function fetchRecords(year, month) {
                return fetch(`{{ route('admin.caja.panel.resumenfinanciero') }}?year=${year}&month=${month}`, {
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
            modalTitle.innerHTML = `Total de pagos aproximados de ${new Date(year, month - 1).toLocaleString('default', { month: 'long' })} ${year}`;

            // Obtener datos del servidor
            fetchRecords(year, month).then(records => {
            const recordsByDate = {};
            records.forEach(record => {
            recordsByDate[record.fechaasignada] = {
                total_ingresos: record.total_ingresos,
                total_egresos: record.total_egresos
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

            let startDay = firstDay === 0 ? 6 : firstDay - 1;

            let totalMes = 0; // Variable para acumular los totales del mes
            let totalMesegreso = 0; // Variable para acumular los totales del mes

            for (let i = 0; i < startDay; i++) {
                const cell = document.createElement('td');
                row.appendChild(cell);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const cell = document.createElement('td');
                const date = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                if (recordsByDate[date]) {
                const { total_ingresos, total_egresos } = recordsByDate[date];
                totalMes += total_ingresos;  // Acumulando solo los ingresos para el total del mes
                totalMesegreso += total_egresos;  // Acumulando solo los ingresos para el total del mes

                let content = `<strong>${day}</strong>`;
                content += `<div><span class="badge badge-success">Ingresos: ${total_ingresos.toFixed(2)}</span></div>`;
                content += `<div><span class="badge badge-danger">Egresos: ${total_egresos.toFixed(2)}</span></div>`; // Egresos en rojo

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

            // Mostrar el total del mes debajo del calendario
            const totalsContainer = document.createElement('div');
            totalsContainer.classList.add('mt-3', 'text-center');
            totalsContainer.innerHTML = `
                <h5 class="text-success">Total Ingresos: ${totalMes.toFixed(2)}</h5>
                <h5 class="text-danger">Total Egresos: ${totalMesegreso.toFixed(2)}</h5>
            `;
            calendarContainer.appendChild(totalsContainer);
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

<h1>PANEL FINANCIERO</h1>
@stop 

@section('content')
<div class="card">
    <h5>MOVIMIENTOS DE INGRESOS DE CAJA</h5>
    <div class="card-body">
        <canvas id="financialChart"></canvas>
    </div>

    <h5>MOVIMIENTOS DE EGRESOS DE CAJA</h5>
    <div class="card-body">
        <canvas id="financialChartegreso"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- INGRESOS --}}
<script> 
    document.addEventListener('DOMContentLoaded', function () {
    const graphData = @json($graphData);

    // Agrupar datos por fecha
    const groupedData = {};
    graphData.forEach(data => {
        if (!groupedData[data.fecha]) {
            groupedData[data.fecha] = [];
        }
        groupedData[data.fecha].push({
            usuario: data.usuarioregistronombre,
            total: data.total
        });
    });

    // Extraer etiquetas (fechas) y preparar datasets por usuario
    const labels = Object.keys(groupedData);
    const users = [...new Set(graphData.map(data => data.usuarioregistronombre))];

    // Definir una lista de tonos grises más diferenciados
    const grayColors = [
        'rgba(25, 25, 112, 0.6)',
        'rgba(46, 139, 87, 0.6)',
        'rgba(148, 0, 211, 0.6)',
        'rgba(255, 69, 0, 0.6)',
        'rgba(140, 140, 140, 0.6)',
        'rgba(100, 149, 237, 0.6)',
        'rgba(255, 214, 0, 0.6)',
        'rgba(34, 139, 34, 0.6)',
        'rgba(255, 182, 193, 0.6)'
    ];

    // Asignar un color fijo y sobrio a cada usuario
    const datasets = users.map((user, index) => {
        return {
            label: user,
            data: labels.map(date => {
                const record = groupedData[date].find(d => d.usuario === user);
                return record ? record.total : 0;
            }),
            backgroundColor: grayColors[index % grayColors.length],  // Cicla entre los colores
            borderColor: 'rgba(100, 100, 100, 0.8)', // Borde gris
            borderWidth: 1
        };
    });

    const ctx = document.getElementById('financialChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,  // Esto hace el gráfico responsivo
            maintainAspectRatio: false,  // Esto asegura que el gráfico no mantenga su proporción original
            plugins: {
                legend: {
                    position: 'top',  // Colocar la leyenda en la parte superior
                    labels: {
                        boxWidth: 20,
                        padding: 15
                    }
                }
            },
            scales: {
                x: {
                    stacked: false,
                    ticks: {
                        font: {
                            size: 12,
                            family: 'Arial',
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 12,
                            family: 'Arial',
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
    });
</script>

{{-- EGRESOS --}}
<script> 
    document.addEventListener('DOMContentLoaded', function () {
    const graphDataegresos = @json($graphDataegresos);

    // Agrupar datos por fecha
    const groupedData = {};
    graphDataegresos.forEach(data => {
        if (!groupedData[data.fecha]) {
            groupedData[data.fecha] = [];
        }
        groupedData[data.fecha].push({
            usuario: data.usuarioregistronombre,
            total: data.total
        });
    });

    // Extraer etiquetas (fechas) y preparar datasets por usuario
    const labels = Object.keys(groupedData);
    const users = [...new Set(graphDataegresos.map(data => data.usuarioregistronombre))];

    // Definir una lista de tonos grises más diferenciados
    const grayColors = [
        'rgba(25, 25, 112, 0.6)',
        'rgba(46, 139, 87, 0.6)',
        'rgba(148, 0, 211, 0.6)',
        'rgba(255, 69, 0, 0.6)',
        'rgba(140, 140, 140, 0.6)',
        'rgba(100, 149, 237, 0.6)',
        'rgba(255, 214, 0, 0.6)',
        'rgba(34, 139, 34, 0.6)',
        'rgba(255, 182, 193, 0.6)'
    ];

    // Asignar un color fijo y sobrio a cada usuario
    const datasets = users.map((user, index) => {
        return {
            label: user,
            data: labels.map(date => {
                const record = groupedData[date].find(d => d.usuario === user);
                return record ? record.total : 0;
            }),
            backgroundColor: grayColors[index % grayColors.length],  // Cicla entre los colores
            borderColor: 'rgba(100, 100, 100, 0.8)', // Borde gris
            borderWidth: 1
        };
    });

    const ctx = document.getElementById('financialChartegreso').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,  // Esto hace el gráfico responsivo
            maintainAspectRatio: false,  // Esto asegura que el gráfico no mantenga su proporción original
            plugins: {
                legend: {
                    position: 'top',  // Colocar la leyenda en la parte superior
                    labels: {
                        boxWidth: 20,
                        padding: 15
                    }
                }
            },
            scales: {
                x: {
                    stacked: false,
                    ticks: {
                        font: {
                            size: 12,
                            family: 'Arial',
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: {
                            size: 12,
                            family: 'Arial',
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
    });
</script>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>

    #financialChart {
        width: 100% !important;  /* Ancho 100% del contenedor */
        height: 100% !important; /* Altura 100% del contenedor */
    }
    #financialChartegreso {
        width: 100% !important;  /* Ancho 100% del contenedor */
        height: 100% !important; /* Altura 100% del contenedor */
    }

    h1, th {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    h5 {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 700;
        text-align: center;
        margin-top: 20px;
        margin-bottom: -20px;
    }
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
        background-color: #f8f8f8;
        color: #6c757d;
    }
    #customCalendar td.sin-programar strong {
        color: #495057;
    }
    #calendarModalLabel {
        text-transform: uppercase;
        font-weight: bold;
    }
    .current-day {
        background-color: #fff8e0;
        font-weight: bold;
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