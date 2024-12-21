@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <div class="text-center mb-0"> <!-- Cambiado de mb-1 a mb-0 -->
        <h1 class="font-weight-bold"
            style="font-size: 1.8rem; color: #343a40; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
            EGRESOS DE CAJA
        </h1>
    </div>
@stop

@section('css')
    <style>
        .card-body {
            padding: 0.75rem;
            /* Padding reducido */
        }

        /* Colores y estilos básicos */
        body {
            background-color: #f8f9fa;
        }

        .panel {

            margin-top: 0;
            /* Margen superior reducido */
        }

        .panel,
        .card {
            background-color: #ffffff;
            border: 1px solid #d3d3d3;
            border-radius: 5px;
            padding: 10px;
        }

        h5 {
            font-weight: bold;
            font-size: 1.2rem;
            /* color: #ffffff; */
            /* Eliminado el color blanco global */
        }

        /* Botones uniformes */
        .table-a {
            border-radius: 5px;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            /* Ancho fijo */
            height: 35px;
            /* Alto fijo */
            padding: 0;
            font-size: 1rem;
        }

        .btn-success.table-a {
            background-color: #28a745;
            color: white;
        }

        .btn-success.table-a:hover {
            background-color: #218838;
        }

        .btn-danger.table-a {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger.table-a:hover {
            background-color: #c82333;
        }

        .btn-secondary.table-a {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary.table-a:hover {
            background-color: #5a6268;
        }

        /* Iconos sin texto */
        .table-a i {
            margin: 0;
        }

        /* Tabla Responsiva y Centrando */
        .table-responsive {
            width: 100%;
            overflow: auto;
            max-height: 500px;
            /* Ajusta según tus necesidades */
            margin: 0 auto;
            /* Centra el contenedor */
        }

        /* Asegura que la tabla ocupe todo el ancho disponible */
        table {
            background-color: white;
            border-collapse: collapse;
            width: 100%;
        }

        /* Encabezados y celdas */
        table th,
        table td {
            text-align: center;
            padding: 8px;
            white-space: nowrap;
            /* Evita que el contenido se divida en múltiples líneas */
        }

        /* Reducción del ancho de las columnas de botones */
        table th:last-child,
        table th:nth-last-child(2),
        table td:last-child,
        table td:nth-last-child(2) {
            width: 50px;
            /* Ajusta según tus necesidades */
        }

        /* Estilo para el modo maximizado */
        .fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            background-color: white;
            overflow: auto;
        }

        /* Transición suave al maximizar */
        .fullscreen-transition {
            transition: all 0.3s ease-in-out;
        }

        /* Reducir el padding del encabezado de la tarjeta */
        .card-header {
            background-color: #343a40;
            /* bg-dark */
            color: #ffffff;
            text-align: center;
            padding: 0.30rem;
            /* Padding aún más reducido */
        }

        /* Eliminar margen inferior del título */
        .card-header h5 {
            margin-bottom: 0;
            /* Margen inferior eliminado */
            color: #ffffff;
            /* Color blanco para el título de la tarjeta */
        }

        /* Reducir el padding del cuerpo de la tarjeta */
        .card-body {
            padding: 0.75rem;
            /* Padding reducido */
        }

        /* Aplicar color negro a las etiquetas <h5> dentro de los paneles */
        .panel h5 {
            color: #343a40;
            /* Color gris oscuro */
        }

        /* Asegura que todos los campos mantengan un tamaño uniforme */
        .form-group {
            flex: 1;
            /* Los campos ocupan el mismo espacio disponible */
            min-width: 0;
            /* Evita que se desborden */
            max-width: 100%;
            /* Limita el tamaño máximo */
        }

        .form-group label {
            display: inline-block;
            /* Asegura un alineado consistente */
            text-align: left;
            /* Mantiene el texto alineado a la izquierda */
        }

        .form-group input,
        .form-group select {
            width: 100%;
            /* Asegura que los elementos de entrada ocupen todo el ancho */
        }

        /* Ajustes adicionales para pantallas pequeñas */
        @media (max-width: 768px) {
            .form-group {
                flex: 1 1 100%;
                /* Ocupan el ancho completo en pantallas pequeñas */
            }
        }

        .panel .form-group {
            margin-bottom: 0.2rem;
            /* Espacio inferior reducido */
        }

        /* Ajuste para el grupo de botones */
        .btn-group {
            display: flex;
            width: 100%;
            /* Ocupa todo el ancho disponible */
        }

        .btn-group .btn {
            flex: 1;
            /* Cada botón ocupa la misma proporción del espacio */
            text-align: center;
            /* Centra el texto dentro del botón */
            padding: 0.3rem;
            /* Ajusta el relleno para evitar desbordes */
            font-size: 0.9rem;
            /* Reduce ligeramente el tamaño del texto */
        }

        /* Corrección para evitar desbordes en pantallas pequeñas */
        @media (max-width: 768px) {
            .btn-group {
                flex-wrap: wrap;
                /* Permite que los botones pasen a otra fila si es necesario */
            }

            .btn-group .btn {
                flex: 1 1 auto;
                /* Ajusta dinámicamente el ancho según el contenido */
            }
        }

        /* Padding de los Botones Dentro de Grupos de Botones */
        .panel .btn-group .btn {
            padding: 0.15rem 0.3rem;
            /* Padding reducido */
            /* Mantener el tamaño de fuente sin cambios */
        }

        /* Margen Inferior de los Elementos de Tipo Checkbox */
        .panel .form-check {
            margin-bottom: 0.3rem;
            /* Margen inferior reducido */
        }

        /* Espacio superior para el botón "Registrar" */
        .registrar-btn {
            margin-top: 1rem;
            /* Ajusta este valor según tus necesidades */
        }

        .row {
            display: flex;
            flex-wrap: nowrap;
            /* No permite que los paneles bajen */
            overflow-x: auto;
            /* Permite el desplazamiento horizontal si es necesario */
        }

        .col-md-2.panel {
            flex: 0 0 250px;
            /* Ancho fijo para paneles */
            max-width: 250px;
            background-color: #efefef;
        }

        .col-md-8 {
            flex: 1;
            /* Panel central ocupa el resto del espacio */
        }

        /* Asegura que el botón "Seleccionar archivo" no se deforme */
        input[type="file"] {
            width: 100%;
            /* Adapta al ancho disponible */
        }

        /* Ajustes responsivos */
        @media (max-width: 768px) {
            .row {
                flex-wrap: wrap;
                /* En pantallas pequeñas, permite filas */
            }

            .col-md-2.panel,
            .col-md-8 {
                flex: 1 1 100%;
                /* Ocupa ancho completo en pantallas pequeñas */
            }
        }

        /* Evitar que el texto del label se divida en líneas */
        label {
            white-space: nowrap;
            /* Evita que el texto pase a la siguiente línea */
        }
    </style>
    <!-- Añadir FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/k7ujTnHq6dE+TzL1jBj9zF3aSgV7KZFrdAQhPqF2OQG1B6lA1Y0Qhf8kqHzvRFeuW6O6MZqClLkRQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@stop

@section('content')
    <div class="row">
        <!-- Panel Izquierdo -->
        <div class="col-md-2 panel" style="background-color: #efefef">
            <h6 class="text" style="margin-top: 0;">Recibir Datos</h6>
            {{-- RELLENAR AUTOMATICO --}}
            <div class="form-group">
                <label>Ciudad de Operación</label>
                <select class="form-control">
                    <option disabled selected>Seleccione una opción</option>
                    <option>SANTA CRUZ</option>
                    <option>COCHABAMBA</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tipo de Transacción</label>
                <select class="form-control">
                    <option disabled selected>Seleccione una opción</option>
                    <option>ATC</option>
                    <option>CHEQUE</option>
                    <option>CUENTAS POR COBRAR</option>
                    <option>DEPÓSITO BANCARIO</option>
                    <option>EFECTIVO</option>
                    <option>TRANSFERENCIAS BANCARIAS</option>
                </select>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="doblePago">
                <label class="form-check-label" for="doblePago">Doble Tipo de Pago</label>
            </div>
            <div class="form-group">
                <label>Tipo de Usuario</label>
                <div class="btn-group btn-group-toggle">
                    <a class="btn btn-outline-secondary btn-sm">Proveedor</a>
                    <a class="btn btn-outline-secondary btn-sm">Personal</a>
                    <a class="btn btn-outline-secondary btn-sm">Cliente</a>
                    <a class="btn btn-outline-secondary btn-sm">N/A</a>
                </div>
            </div>
            <div class="form-group">
                <label>Tipo de Servicio / Área</label>
                <select class="form-control">
                    <option disabled selected>Seleccione una opción</option>
                    <option>ASESORAMIENTO LEGAL</option>
                    <option>CONTABILIDAD</option>
                    <option>ECOGRAFÍA</option>
                    <option>ESTUDIOS</option>
                    <option>LABORATORIO</option>
                    <option>MÉDICA</option>
                    <option>RAYOS X</option>
                </select>
            </div>
            <div class="form-group" hidden>
                <label>Movimiento</label>
                <select class="form-control" disabled>
                    <option>INGRESO</option>
                </select>

            </div>
            <div class="row">
                <div class="form-group col-lg-6">
                    <label>Fecha</label>
                    <input type="date" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>
                <div class="form-group col-lg-6">
                    <label>Fecha Crédito</label>
                    <input type="date" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-lg-12">
                    <label>Documento</label>
                    <input type="file" class="form-control" value="0">
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a class="btn btn-outline btn-sm">Limpiar Todo</a>
                <a class="btn btn-outline btn-sm">Actualizar</a>
            </div>
        </div>

        <!-- Panel Central -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h5 style="visibility: visible; color: #ffffff; margin-bottom: 0;">Registrar Movimiento</h5>
                    <!-- Añadido margin-bottom: 0; -->
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Clt. / Pers. / Prov.</label>
                                <input type="text" class="form-control" placeholder="Ingrese cliente">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>CI</label>
                                <input type="text" class="form-control" placeholder="Ingrese el CI">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Detalle</label>
                                <input type="text" class="form-control" placeholder="Ingrese detalle">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Descuento</label>
                                <input type="number" class="form-control" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Monto</label>
                                <input type="number" class="form-control" placeholder="0">
                            </div>
                        </div>
                    </div>

                    <!-- Tabla con Botones "Añadir" y "Maximizar" en Columnas Separadas -->
                    <div class="table-responsive">
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>
                                        <a class="btn btn-secondary table-a" title="Maximizar Tabla"
                                            id="maximizarTabla">
                                            <i class="fas fa-expand"></i>
                                        </a>
                                    </th>
                                    <th>
                                        <a class="btn btn-success table-a" title="Añadir Nueva Fila"
                                            id="addMovimiento">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </th>

                                    <th>Cliente</th>
                                    <th>Área</th>
                                    <th>Detalle</th>
                                    <th>Movimiento</th>
                                    <th>Monto Real</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td> <!-- Celda vacía para mantener la estructura -->
                                    <td>
                                        <a class="btn btn-danger table-a" title="Eliminar Movimiento"
                                            aria-label="Eliminar Movimiento"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este movimiento?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>

                                    <td>Juan Pérez</td>
                                    <td>Ventas</td>
                                    <td>Venta de producto X</td>
                                    <td>Ingreso</td>
                                    <td>200</td>

                                </tr>
                                <tr>
                                    <td></td> <!-- Celda vacía para mantener la estructura -->
                                    <td>
                                        <a class="btn btn-danger table-a" title="Eliminar Movimiento"
                                            aria-label="Eliminar Movimiento"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este movimiento?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                    <td>María López</td>
                                    <td>Marketing</td>
                                    <td>Campaña de publicidad</td>
                                    <td>Egreso</td>
                                    <td>150</td>

                                </tr>


                            </tbody>
                        </table>
                    </div>

                    <div class="card-body">
                        <h5 class="text-center" style="margin-top: 0;">Resumen</h5>
                        <!-- Añadido style="margin-top: 0;" -->
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label>Subtotal</label>
                                <input type="text" class="form-control" value="0" readonly>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Descuento</label>
                                <input type="text" class="form-control" value="0" readonly>
                            </div>
                            <div class="form-group col-lg-4">
                                <label>Total</label>
                                <input type="text" class="form-control border border-dark" value="0" readonly>
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-success btn-block registrar-btn">
                        Registrar
                    </a>
                </div>
            </div>
        </div>

        <!-- Panel Derecho -->
        <div class="col-md-2 panel" style="background-color: #efefef">
            <!-- Nuevos Campos -->
            <div class="row">
                <div class="form-group col-lg-6">
                    <label>N° Baucher</label>
                    <input type="text" class="form-control" value="0">
                </div>
                <div class="form-group col-lg-6">
                    <label>N° Recibo</label>
                    <input type="text" class="form-control" value="0">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-6">
                    <label>N° Factura</label>
                    <input type="text" class="form-control" value="0">
                </div>
                <div class="form-group col-lg-6">
                    <label>N° Crédito</label>
                    <input type="text" class="form-control" value="0">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-lg-6">
                    <label>Tipo Orden</label>
                    <select class="form-control">
                        <option>S/O</option>
                        <option>COMPRA</option>
                        <option>TRABAJO</option>
                        <option>ORX</option>
                        <option>PERSONAL</option>
                        <option>MOVIMIENTO INTERNO</option>
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label>N° Orden</label>
                    <input type="text" class="form-control" value="0">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-lg-6">
                    <label>ID Caja</label>
                    <input type="text" class="form-control" value="0">
                </div>
                <div class="form-group col-lg-6">
                    <label>Num Salida</label>
                    <input type="text" class="form-control" value="0">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-lg-6">
                    <label>N° Comprob.</label>
                    <input type="text" class="form-control" value="0">
                </div>
                <div class="form-group col-lg-6">
                    <label>Nom. Banco</label>
                    <select class="form-control">
                        <option disabled selected>Seleccione una opción</option>
                        <option>GOOD LIFE</option>
                        <option>GOOD LIFE 1</option>
                        <option>S/B</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>T. Cambio</label>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="t_cambio" id="t_cambio_bs" value="Bs"
                        checked>
                    <label class="form-check-label" for="t_cambio_bs">Bs.</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="t_cambio" id="t_cambio_usd" value="USD">
                    <label class="form-check-label" for="t_cambio_usd">USD</label>
                </div>
            </div>


        </div>
    </div>
@stop

@section('js')
    <script>

document.addEventListener('DOMContentLoaded', function () {
        // Asegúrate de que el menú esté colapsado
        const body = document.body;
        if (!body.classList.contains('sidebar-collapse')) {
            body.classList.add('sidebar-collapse'); // Colapsa el menú lateral
        }
    });
    
        document.addEventListener('DOMContentLoaded', function() {
            const addButton = document.getElementById('addMovimiento');
            const maximizarButton = document.getElementById('maximizarTabla');
            const tableContainer = document.querySelector('.table-responsive');

            addButton.addEventListener('click', function() {
                // Lógica para agregar un movimiento
                const tableBody = document.querySelector('table tbody');
                const newRow = document.createElement('tr');

                newRow.innerHTML = `
            <td><input type="text" class="form-control" placeholder="Cliente"></td>
            <td>
                <select class="form-control">
                    <option>Área 1</option>
                    <option>Área 2</option>
                    <option>Área 3</option>
                </select>
            </td>
            <td><input type="text" class="form-control" placeholder="Detalle"></td>
            <td>
                <select class="form-control">
                    <option>Ingreso</option>
                    <option>Egreso</option>
                </select>
            </td>
            <td><input type="number" class="form-control" placeholder="Monto Real"></td>
            <td>
                <a class="btn btn-danger table-a" title="Eliminar Movimiento" aria-label="Eliminar Movimiento" onclick="return confirm('¿Estás seguro de que deseas eliminar este movimiento?');">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
            <td></td> <!-- Celda vacía para mantener la estructura -->
        `;

                tableBody.appendChild(newRow);
            });

            // Delegación de eventos para botones de eliminar nuevos
            document.querySelector('table tbody').addEventListener('click', function(e) {
                if (e.target && e.target.closest('.btn-danger')) {
                    if (confirm('¿Estás seguro de que deseas eliminar este movimiento?')) {
                        const row = e.target.closest('tr');
                        row.remove();
                    }
                }
            });

            // Funcionalidad para maximizar la tabla
            maximizarButton.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    // Entrar en modo maximizado
                    tableContainer.classList.add('fullscreen-transition');
                    tableContainer.classList.add('fullscreen');
                    tableContainer.requestFullscreen().catch(err => {
                        alert(`Error al intentar maximizar: ${err.message} (${err.name})`);
                    });
                    maximizarButton.innerHTML =
                        '<i class="fas fa-compress"></i>'; // Cambiar icono a comprimir
                    maximizarButton.title = 'Restaurar Tabla';
                } else {
                    // Salir del modo maximizado
                    document.exitFullscreen();
                    tableContainer.classList.remove('fullscreen');
                    tableContainer.classList.remove('fullscreen-transition');
                    maximizarButton.innerHTML = '<i class="fas fa-expand"></i>'; // Cambiar icono a expandir
                    maximizarButton.title = 'Maximizar Tabla';
                }
            });

            // Evento para restaurar el botón cuando se sale del modo fullscreen
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement) {
                    tableContainer.classList.remove('fullscreen');
                    tableContainer.classList.remove('fullscreen-transition');
                    maximizarButton.innerHTML = '<i class="fas fa-expand"></i>';
                    maximizarButton.title = 'Maximizar Tabla';
                }
            });
        });
    </script>
@stop