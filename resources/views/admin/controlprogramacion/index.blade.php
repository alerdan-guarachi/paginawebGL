@extends('adminlte::page')

@section('content_header')
    <h1>CONTROL DE REGISTROS</h1>
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
            <nav class="navbar float-right">
                <form action="{{ route('admin.controlprogramacion.buscarPorUsuario') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <select name="usuario" class="form-control mr-sm-2" id="usuarioSelect"
                            onchange="handleUserSelection()">
                            <option value="general" {{ !isset($usuarioId) ? 'selected' : '' }}>VER GRÁFICA GENERAL</option>
                            @foreach ($usuarios as $usuario)
                                <option value="{{ $usuario->id }}"
                                    {{ isset($usuarioId) && $usuario->id == $usuarioId ? 'selected' : '' }}>
                                    {{ $usuario->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                </form>
            </nav>

            <!-- Pestañas para gráficos -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ !isset($usuarioId) ? 'active' : '' }}" id="general-tab" data-toggle="tab"
                        href="#general" role="tab" aria-controls="general"
                        aria-selected="{{ !isset($usuarioId) ? 'true' : 'false' }}">Gráfico General</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ isset($usuarioId) ? 'active' : 'disabled' }}" id="usuarios-tab" data-toggle="tab"
                        href="#usuarios" role="tab" aria-controls="usuarios"
                        aria-selected="{{ isset($usuarioId) ? 'true' : 'false' }}">Gráficos de Usuarios</a>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="myTabContent">
                <!-- Gráfico General -->
                <div class="tab-pane fade {{ !isset($usuarioId) ? 'show active' : '' }}" id="general" role="tabpanel"
                    aria-labelledby="general-tab">
                    <div class="card-body">
                        <canvas id="registrosChart" width="400" height="150"></canvas>
                    </div>
                </div>

                <!-- Gráficos de Usuarios -->
                <div class="tab-pane fade {{ isset($usuarioId) ? 'show active' : '' }}" id="usuarios" role="tabpanel"
                    aria-labelledby="usuarios-tab">
                    @if (isset($finalDataPorTablaUsuario))
                        <div class="card-body">
                            <div class="row">
                                @foreach ($finalDataPorTablaUsuario as $tabla => $data)
                                    <div class="col-md-6 mb-4"> <!-- Ajuste para mostrar dos gráficos por fila -->
                                        <canvas id="registrosChart_{{ $tabla }}" width="400"
                                            height="200"></canvas>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        @if (isset($usuarioId) && $usuarioId != 'general')
                            <!-- Nueva condición para evitar tratar "general" como usuario -->
                            <p>No hay datos que mostrar para este usuario.</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        h1,
        th {
            color: #94c93b;
            font-family: "Segoe UI";
            font-weight: 900;
        }

        .btn-crear {
            background-color: #ffffff;
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
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
        }

        .btn-buscar:hover {
            background-color: #faa625;
            color: #ffffff;
        }

        .btn-editar {
            background-color: #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
        }

        .btn-editar:hover {
            background-color: #94c93b;
            color: #ffffff;
        }

        /* Ajuste para que los gráficos se alineen de a dos por fila */
        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .canvas-container {
            margin: 0 auto;
            text-align: center;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let previousUserSelection = document.getElementById('usuarioSelect').value; // Guardar la selección previa

        document.addEventListener("DOMContentLoaded", function() {
            // Detectar cuando se hace clic en la pestaña "Gráfico General"
            document.getElementById('general-tab').addEventListener('click', function() {
                // Simula seleccionar "VER GRÁFICA GENERAL" en el select
                document.getElementById('usuarioSelect').value = 'general';
                document.forms[0].submit(); // Envía el formulario automáticamente
            });

            // Deshabilitar la pestaña "Gráficos de Usuarios" hasta que se seleccione un usuario y haga clic en "Buscar"
            document.getElementById('usuarios-tab').classList.add('disabled');

            // Gráfico general: Si el valor es "general", cargamos el gráfico general
            @if (!isset($usuarioId))
                var ctxGeneral = document.getElementById('registrosChart').getContext('2d');
                var registrosChartGeneral = new Chart(ctxGeneral, {
                    type: 'line',
                    data: {
                        labels: @json($diasEnOrden),
                        datasets: [{
                            label: 'Registros de Usuario',
                            data: @if (is_a($finalDataGeneral, \Illuminate\Support\Collection::class))
                                @json($finalDataGeneral->values())
                            @else
                                []
                            @endif ,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Registros creados por día de la semana',
                                font: {
                                    weight: 'bold' // Título en negrita
                                }
                            },
                            datalabels: {
                                anchor: 'end', // Mueve la etiqueta fuera del punto
                                align: 'top', // Alinea la etiqueta sobre el punto
                                offset: 5, // Añadimos un desplazamiento para separar más la etiqueta del punto
                                color: '#333',
                                font: {
                                    weight: 'bold'
                                },
                                formatter: function(value, context) {
                                    return value;
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Registros'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Días de la semana'
                                }
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });
            @endif

            // Función para generar colores llamativos más variados
            const colorPalette = [
                '#FF5733', // Naranja fuerte
                '#33FF57', // Verde brillante
                '#3357FF', // Azul vivo
                '#FF33A6', // Rosa fuerte
                '#A633FF', // Morado llamativo
                '#33FFF6', // Celeste brillante
                '#FFC300', // Amarillo dorado
                '#FF5733', // Rojo anaranjado
                '#DAF7A6', // Verde lima claro
                '#C70039' // Rojo cereza fuerte
            ];

            function getRandomColor(index) {
                return colorPalette[index % colorPalette.length];
            }

            // Gráficos individuales por tabla para el usuario seleccionado
            @if (isset($finalDataPorTablaUsuario))
                @foreach ($finalDataPorTablaUsuario as $tabla => $data)
                    // Usar un color llamativo predefinido
                    var randomColor = getRandomColor({{ $loop->index }});

                    var ctx_{{ $tabla }} = document.getElementById('registrosChart_{{ $tabla }}')
                        .getContext('2d');
                    var registrosChart_{{ $tabla }} = new Chart(ctx_{{ $tabla }}, {
                        type: 'line',
                        data: {
                            labels: @json($diasEnOrden), // Días de la semana en orden
                            datasets: [{
                                label: 'Registros de {{ $tabla }}',
                                data: @json($data->values()), // Asegurarse de que está en formato de lista
                                backgroundColor: randomColor + '33', // Color con transparencia
                                borderColor: randomColor,
                                borderWidth: 2,
                                pointBackgroundColor: randomColor,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: true
                                },
                                title: {
                                    display: false // Eliminar el título duplicado en las gráficas individuales
                                },
                                datalabels: {
                                    anchor: 'end', // Mueve la etiqueta fuera del punto
                                    align: 'top', // Alinea la etiqueta sobre el punto
                                    offset: 5, // Añadimos un desplazamiento para separar más la etiqueta del punto
                                    color: '#333',
                                    font: {
                                        weight: 'bold'
                                    },
                                    formatter: function(value, context) {
                                        return value;
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Número de Registros'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Días de la semana'
                                    },
                                    ticks: {
                                        autoSkip: false // Asegurarse de que las etiquetas no se superpongan
                                    }
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                @endforeach
            @endif
        });

        // Función para manejar la selección del usuario en el select
        function handleUserSelection() {
            // Verificamos si el usuario seleccionado es "general"
            var selectedValue = document.getElementById('usuarioSelect').value;
            if (selectedValue !== previousUserSelection) { // Solo enviamos el formulario si se selecciona un nuevo usuario
                previousUserSelection = selectedValue;

                if (selectedValue === 'general') {
                    document.getElementById('usuarios-tab').classList.add('disabled'); // Bloquea la pestaña de usuarios
                } else {
                    document.getElementById('usuarios-tab').classList.remove(
                    'disabled'); // Desbloquea la pestaña de usuarios
                }

                document.forms[0].submit(); // Envía el formulario
            }
        }
    </script>
@stop
