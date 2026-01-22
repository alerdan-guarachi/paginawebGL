@extends('adminlte::page')

@section('content_header')
    <h1>REGISTROS DE PRESTACIONES</h1>
@stop
@section('css')
<link rel="stylesheet" href="{{ asset('css/opcionesmultiples.css') }}">
<style>
    .btn-registrar {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 20px;
        }
    .btn-registrar:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-actualizar {
        background-color:  #ffffff;
        color: #e8932b;
        border-color: #e8932b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-actualizar:hover {
        background-color: #e8932b;
        color: #ffffff;
        }
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }
    .truncar2 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .btn-verregistros {
        background-color:  #ffffff;
        color: #787878;
        border-color: #787878;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-verregistros:hover {
        background-color: #787878;
        color: #ffffff;
        }
    .btn-custom2 {
        background-color:  #ffffff;
        color: #9d9d9d;
        border-color: #9d9d9d;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-custom2:hover {
        background-color: #9d9d9d;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: scale(1.05);
    }
    .btn-custom2:disabled {
        background-color: #d6d6d6;
        color: #a0a0a0;
        cursor: not-allowed;
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
        background-color: #6e6e6e;
        color: #fff;
    }
    .nav-link .circle {
        background-color: #6e6e6e;
        color: #fff;
    }
</style>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    REGISTROS DE PROCESOS POR FUNCIONARIO
                </a>
            </li>   
            <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    REGISTROS TOTAL POR FUNCIONARIO
                </a>
            </li>
        </ul>
    </div>

     <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="card">
                    <div class="card-body">
                        {{-- FILTROS ARRIBA --}}
                        <form method="GET" action="{{ route('reportes.resumen_tramites') }}">
                            <div class="row align-items-end">
                                <!-- Funcionario -->
                                <div class="form-group col-lg-5">
                                    <label>Funcionario:</label>
                                    <select name="usuario" class="form-select form-control" onchange="this.form.submit()">
                                        <option value="">Seleccione funcionario...</option>
                                        @foreach($funcionarios as $f)
                                            <option value="{{ $f }}" {{ $usuario == $f ? 'selected' : '' }}>{{ $f }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Desde -->
                                <div class="form-group col-lg-3">
                                    <label>Desde:</label>
                                    <input type="date" name="desde" value="{{ $fechaDesde }}" class="form-control" onchange="this.form.submit()">
                                </div>

                                <!-- Hasta -->
                                <div class="form-group col-lg-3">
                                    <label>Hasta:</label>
                                    <input type="date" name="hasta" value="{{ $fechaHasta }}" class="form-control" onchange="this.form.submit()">
                                </div>

                                <!-- Botón Exportar -->
                                <div class="form-group col-lg-1">
                                    <button type="submit"
                                        formaction="{{ route('reportes.resumen_tramites.exportar') }}"
                                        class="btn btn-outline-success">
                                         EXPORTAR
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex gap-3 flex-wrap" style="margin-bottom: 20px;">
                                <!-- Último registro -->
                                <div class="d-inline-flex align-items-center border rounded-pill px-3 py-1 bg-light shadow-sm" style="white-space: nowrap; margin-right:20px;">
                                    <i class="fas fa-clock text-success me-2"></i>
                                    <span class="fw-bold text-dark me-2" style="font-weight: 900; margin-right:5px; margin-left:5px;">ÚLTIMO REGISTRO:</span>
                                    <span class="fw-bold text-success" style="font-weight: 900;">
                                        {{ $ultimaFechaFuncionario 
                                            ? \Carbon\Carbon::parse($ultimaFechaFuncionario)->format('d/m/Y - H:i') 
                                            : \Carbon\Carbon::parse($ultimaFechaTotal)->format('d/m/Y - H:i') }}
                                    </span>
                                </div>

                                <!-- Cantidad de procesos -->
                                <div class="d-inline-flex align-items-center border rounded-pill px-3 py-1 bg-light shadow-sm" style="white-space: nowrap; margin-right:20px;"">
                                    <i class="fas fa-file text-success me-2"></i>
                                    <span class="fw-bold text-dark me-2" style="font-weight: 900; margin-right:5px; margin-left:5px;">CANT. PROCESOS:</span>
                                    <span class="fw-bold text-success" style="font-weight: 900;">
                                        {{ $resumenClientes->sum('total_registros') }}
                                    </span>
                                </div>

                                <!-- Cantidad de trámites -->
                                <div class="d-inline-flex align-items-center border rounded-pill px-3 py-1 bg-light shadow-sm" style="white-space: nowrap;">
                                    <i class="fas fa-folder text-success me-2"></i>
                                    <span class="fw-bold text-dark me-2" style="font-weight: 900; margin-right:5px; margin-left:5px;">CANT. TRÁMITES:</span>
                                    <span class="fw-bold text-success" style="font-weight: 900;">
                                        {{ $resumenClientes->count() }}
                                    </span>
                                </div>
                            </div>
                        </form>

                        {{-- TABLA DE RESULTADOS --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm" id="tablaClientes">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Ver</th>
                                        <th>Nombre_Cliente</th>
                                        <th>Trámite</th>
                                        <th>Total_Reg.</th>
                                        <th hidden>Total_Reg_por_Funcionario</th>
                                        <th>Fecha_Último_Reg.</th>
                                        <th>Último_Procedimiento</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($resumenClientes as $cliente)
                                        @php
                                            $key = $cliente->clientenombre . '||' . $cliente->tramite;
                                            $ultimo = $ultimoNivelProcedimiento[$key] ?? null;
                                            $fechaUltimo = $ultimo ? \Carbon\Carbon::parse($ultimo->created_at)->format('d/m/Y - H:i') : '-';
                                            $nivel = $ultimo->subprocedimiento ?? '-';
                                            $tipo = $ultimo->tipo ?? '-';
                                            $tipocarta = $ultimo->tipocarta ?? null;
                                            $estado = $estadosTramites[$cliente->clientenombre][$cliente->tramite][0]->estado ?? 'PENDIENTE';
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-success btn-xs p-0 px-1 toggle-detalle"
                                                    data-cliente="{{ $cliente->clientenombre }}"
                                                    data-tramite="{{ $cliente->tramite }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                            <td>{{ $cliente->clientenombre }}</td>
                                            <td>{{ $cliente->tramite }}</td>
                                            <td class="text-center">{{ $cliente->total_registros }}</td>
                                            <td hidden>
                                                @if(isset($detallesUsuarios[$cliente->clientenombre][$cliente->tramite]))
                                                    <ul class="mb-0">
                                                        @foreach($detallesUsuarios[$cliente->clientenombre][$cliente->tramite] as $detalle)
                                                            <li>{{ $detalle->usuario ?? 'SIN USUARIO' }}: {{ $detalle->cantidad }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">Sin detalle</span>
                                                @endif
                                            </td>
                                            <td>{{ $fechaUltimo }}</td>
                                            <td>
                                                {{ $tipo }} - {{ $nivel }}
                                                @if(!empty($tipocarta))
                                                    - {{ $tipocarta }}
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if($estado == 'FINALIZADO')
                                                    <span class="badge bg-success">FINALIZADO</span>
                                                @elseif($estado == 'INTERRUMPIDO')
                                                    <span class="badge bg-warning text-dark">INTERRUMPIDO</span>
                                                @else
                                                    <span class="badge bg-danger">PENDIENTE</span>
                                                @endif
                                            </td>
                                        </tr>
                                        {{-- FILA EXPANDIBLE --}}
                                        <tr class="fila-detalle d-none" data-cliente="{{ $cliente->clientenombre }}" data-tramite="{{ $cliente->tramite }}">
                                            <td colspan="8">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Nivel_Procedimiento</th>
                                                                <th>Sub_Procedimiento</th>
                                                                <th>Fecha_Reg</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($detallesRegistros[$cliente->clientenombre][$cliente->tramite] ?? [] as $d)
                                                                <tr>
                                                                    <td>{{ $d->id }}</td>
                                                                    <td>{{ $d->nivelprocedimiento }}</td>
                                                                    <td>
                                                                        {{ $d->tipo }} - {{ $d->subprocedimiento }}
                                                                        @if($d->tipocarta)
                                                                            - {{ $d->tipocarta }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ \Carbon\Carbon::parse($d->created_at)->format('d/m/Y - H:i') }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="6" class="text-muted">Sin registros</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">SIN REGISTROS PARA ESTA FECHA O FUNCIONARIO</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <script>
                            document.querySelectorAll('.toggle-detalle').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    const cliente = this.dataset.cliente;
                                    const tramite = this.dataset.tramite;
                                    const filaDetalle = document.querySelector(`.fila-detalle[data-cliente="${cliente}"][data-tramite="${tramite}"]`);
                                    if (filaDetalle) filaDetalle.classList.toggle('d-none');
                                });
                            });
                        </script>

                        <script>
                            document.getElementById('buscadorClienteTramite').addEventListener('keyup', function() {
                                let filtro = this.value.toLowerCase();
                                document.querySelectorAll('#tablaClientes tbody tr').forEach(fila => {
                                    let cliente = fila.cells[0].textContent.toLowerCase();
                                    let tramite = fila.cells[1].textContent.toLowerCase();
                                    fila.style.display = (cliente.includes(filtro) || tramite.includes(filtro)) ? '' : 'none';
                                });
                            });
                        </script>

                        {{-- === GRAFICO DE ACTIVIDAD POR HORA === --}}
                        <div class="card">
                            <div class="card-body">
                                <div class="mt-4">
                                    <h6 class="fw-bold text-center mb-3 text-success">
                                        DISTRIBUCIÓN DE ACTIVIDAD POR HORAS ({{ $fechaDesde }} - {{ $fechaHasta }})
                                    </h6>
                                    <div class="w-100">
                                        <canvas id="graficoActividad" style="width: 100%; height: 400px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                            const ctx = document.getElementById('graficoActividad').getContext('2d');
                            const data = {
                                labels: [
                                    '00:00', '01:00', '02:00', '03:00', '04:00', '05:00',
                                    '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
                                    '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
                                    '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'
                                ],
                                datasets: [{
                                    label: 'Cantidad de registros',
                                    data: @json($actividadFormateada),
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.3)',
                                    borderWidth: 2,
                                    tension: 0.3,
                                    fill: true,
                                    pointRadius: 3,
                                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                                }]
                            };

                            const grafico = new Chart(ctx, {
                                type: 'line',
                                data,
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: { stepSize: 1 }
                                        },
                                        x: {
                                            ticks: {
                                                callback: function(value, index) {
                                                    // Coloreamos horas de trabajo (8–12 y 14–18)
                                                    const horasTrabajo = [8,9,10,11,14,15,16,17];
                                                    return this.getLabelForValue(index);
                                                }
                                            }
                                        }
                                    },
                                    plugins: {
                                        title: {
                                            display: true,
                                            text: 'Actividad de funcionarios por hora',
                                            font: { size: 14, weight: 'bold' }
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return `${context.formattedValue} registros`;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        </script>

                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body p-2">
                        <canvas id="resumenChart"></canvas>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-between align-items-center">
                                <h5 class="modal-title" id="detalleModalLabel" style="font-weight: 900">
                                    REGISTROS DE FECHA <span id="fechaDetalle" class="fw-bold ms-2"></span>
                                </h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Tabla principal -->
                                <div class="table-responsive mb-2">
                                    <table class="table table-bordered table-sm text-center">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Funcionario</th>
                                                <th>Cant_Reg.</th>
                                                <th>Ver</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalBody"></tbody>
                                    </table>
                                </div>

                                <!-- Contenedor detalle por tabla (oculto inicialmente) -->
                                <div id="detalleTablas" class="table-responsive" style="display:none;">
                                    <table class="table table-bordered table-sm text-center">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Tabla</th>
                                                <th>Total Registros</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalBodyTablas"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scripts -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const ctxResumen = document.getElementById('resumenChart').getContext('2d');
                    const fechasResumen = @json(array_map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m'), $fechas));
                    const datasetsOriginalResumen = @json($datasets);

                    // Dataset separado para detalle por tabla
                    const datasetsPorTabla = @json($datasetsPorTabla);

                    const datasetsSeparadosResumen = datasetsOriginalResumen.map(ds => {
                        const dataCompleta = fechasResumen.map((_, i) => ds.data[i] ?? 0);
                        return { ...ds, data: dataCompleta, borderRadius: 6 };
                    });

                    Chart.register(ChartDataLabels);

                    function ajustarAlturaCanvasResumen() {
                        const canvas = document.getElementById('resumenChart');
                        const ancho = window.innerWidth;
                        if (ancho < 576) canvas.height = 300;
                        else if (ancho < 768) canvas.height = 250;
                        else canvas.height = 120;
                    }
                    ajustarAlturaCanvasResumen();
                    window.addEventListener('resize', () => {
                        ajustarAlturaCanvasResumen();
                        if(resumenChart) resumenChart.resize();
                    });

                    const resumenChart = new Chart(ctxResumen, {
                        type: 'bar',
                        data: { labels: fechasResumen, datasets: datasetsSeparadosResumen },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'bottom' },
                                datalabels: {
                                    anchor: 'end',
                                    align: 'top',
                                    color: '#000',
                                    font: { weight: 'bold' },
                                    formatter: value => value > 0 ? value : ''
                                },
                                tooltip: { enabled: true }
                            },
                            scales: {
                                x: { stacked: false, ticks: { autoSkip: false } },
                                y: {
                                    stacked: false,
                                    beginAtZero: true,
                                    ticks: { stepSize: 1 },
                                    suggestedMax: Math.max(...datasetsOriginalResumen.flatMap(ds => ds.data)) * 1.1
                                }
                            },
                            onClick: (evt, elements) => {
                                if (!elements.length) return;
                                const idx = elements[0].index;
                                const fecha = fechasResumen[idx];
                                let html = '';

                                // Construir filas del modal
                                datasetsSeparadosResumen.forEach(ds => {
                                    const cantidad = ds.data[idx] ?? 0;

                                    html += `<tr>
                                        <td>${ds.label}</td>
                                        <td>${cantidad}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary btnDetalleTabla" 
                                                    data-usuario="${ds.label}" 
                                                    data-fecha="${fecha}">
                                                CANT. REG. POR TABLA
                                            </button>
                                            <!-- Contenedor detalle por tabla individual -->
                                            <div class="table-responsive mt-2 contenedorDetalleTabla" style="display:none;">
                                                <table class="table table-bordered table-sm text-center">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th>Tabla</th>
                                                            <th>Total Registros</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>`;
                                });

                                document.getElementById('fechaDetalle').innerText = fecha;
                                document.getElementById('modalBody').innerHTML = html;

                                const detalleModal = new bootstrap.Modal(document.getElementById('detalleModal'));
                                detalleModal.show();

                                // Evento de cerrar modal: ocultar todos los detalles
                                document.getElementById('detalleModal').addEventListener('hidden.bs.modal', () => {
                                    document.querySelectorAll('.contenedorDetalleTabla').forEach(c => c.style.display = 'none');
                                });

                                // Agregar evento a los botones por fila
                                document.querySelectorAll('.btnDetalleTabla').forEach(btn => {
                                    btn.onclick = function() {
                                        const usuario = this.getAttribute('data-usuario');
                                        const fecha = this.getAttribute('data-fecha');

                                        // Índice de la fecha
                                        const idxFecha = fechasResumen.indexOf(fecha);

                                        let detalleTablasHtml = '';
                                        const tablasAmigables = @json($tablasAmigables);

                                        datasetsPorTabla.forEach(ds => {
                                            if(ds.usuario === usuario){
                                                const total = ds.data[idxFecha] ?? 0;
                                                const nombreTabla = tablasAmigables[ds.tabla] ?? ds.tabla; // nombre amigable
                                                detalleTablasHtml += `<tr>
                                                    <td>${nombreTabla}</td>
                                                    <td>${total}</td>
                                                </tr>`;
                                            }
                                        });

                                        // Mostrar / ocultar contenedor individual
                                        const contenedor = this.parentElement.querySelector('.contenedorDetalleTabla');
                                        contenedor.querySelector('tbody').innerHTML = detalleTablasHtml;
                                        contenedor.style.display = contenedor.style.display === 'none' ? 'block' : 'none';
                                    };
                                });
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                });
                </script>
            </div>
        </div>
    </div>
</div>
@stop
