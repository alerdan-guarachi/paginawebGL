@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <div class="text-center mb-0">
        <h1 class="font-weight-bold"
            style="font-size: 1.8rem; color: #343a40; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
            INGRESOS DE CAJA
        </h1>
    </div>
@stop

@section('css')
    <style>
        .card-body {
            padding: 0.75rem;
        }
        body {
            background-color: #f8f9fa;
        }
        .panel {
            margin-top: 0;
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
        }
        .table-button {
            border-radius: 5px;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            padding: 0;
            font-size: 1rem;
        }
        .btn-success.table-button {
            background-color: #28a745;
            color: white;
        }
        .btn-success.table-button:hover {
            background-color: #218838;
        }
        .btn-danger.table-button {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger.table-button:hover {
            background-color: #c82333;
        }
        .btn-secondary.table-button {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary.table-button:hover {
            background-color: #5a6268;
        }
        .table-button i {
            margin: 0;
        }
        .table-responsive {
            width: 100%;
            overflow: auto;
            max-height: 500px;
            margin: 0 auto;
        }
        table {
            background-color: white;
            border-collapse: collapse;
            width: 100%;
        }
        table th,
        table td {
            text-align: center;
            padding: 8px;
            white-space: nowrap;
        }
        table th:last-child,
        table th:nth-last-child(2),
        table td:last-child,
        table td:nth-last-child(2) {
            width: 50px;
        }
        .fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            z-index: 9999;
            padding: 20px;
            overflow: auto;
        }
        .fullscreen-transition {
            transition: all 0.3s ease-in-out;
        }
        .card-header {
            background-color: #343a40;
            color: #ffffff;
            text-align: center;
            padding: 0.30rem;
        }
        .card-header h5 {
            margin-bottom: 0;
            color: #ffffff;
        }
        .card-body {
            padding: 0.75rem;
        }
        .panel h5 {
            color: #343a40;
        }

        .panel .form-group {
            margin-bottom: 0.2rem;
        }
        .panel .btn-group .btn {
            padding: 0.15rem 0.3rem;
        }
        .panel .form-check {
            margin-bottom: 0.3rem;
        }
        .registrar-btn {
            margin-top: 1rem;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/k7ujTnHq6dE+TzL1jBj9zF3aSgV7KZFrdAQhPqF2OQG1B6lA1Y0Qhf8kqHzvRFeuW6O6MZqClLkRQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@stop

@section('content')
    <div class="row">
        <div class="col-md-2 panel" style="background-color: #efefef">
            <h6 class="text" style="margin-top: 0;">Recibir Datos</h6>
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
                    <button class="btn btn-outline-secondary btn-sm">Proveedor</button>
                    <button class="btn btn-outline-secondary btn-sm">Personal</button>
                    <button class="btn btn-outline-secondary btn-sm">Cliente</button>
                    <button class="btn btn-outline-secondary btn-sm">Ninguno</button>
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
                <button class="btn btn-outline btn-sm">Limpiar Todo</button>
                <button class="btn btn-outline btn-sm">Actualizar</button>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h5 style="visibility: visible; color: #ffffff; margin-bottom: 0;">Registrar Movimiento</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cliente / Personal / Proveedor</label>
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

                    <div class="table-responsive">
                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>
                                        <button class="btn btn-secondary table-button" title="Maximizar Tabla"
                                            id="maximizarTabla">
                                            <i class="fas fa-expand"></i>
                                        </button>
                                    </th>
                                    <th>
                                        <button class="btn btn-success table-button" title="Añadir Nueva Fila"
                                            id="addMovimiento">
                                            <i class="fas fa-plus"></i>
                                        </button>
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
                                    <td></td>
                                    <td>
                                        <button class="btn btn-danger table-button" title="Eliminar Movimiento"
                                            aria-label="Eliminar Movimiento"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este movimiento?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Juan Pérez</td>
                                    <td>Ventas</td>
                                    <td>Venta de producto X</td>
                                    <td>Ingreso</td>
                                    <td>200</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <button class="btn btn-danger table-button" title="Eliminar Movimiento"
                                            aria-label="Eliminar Movimiento"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este movimiento?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                    <h5 class="text-center" style="margin-top: 0;">Resumen</h5> <!-- Añadido style="margin-top: 0;" -->
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
                    <button class="btn btn-success btn-block registrar-btn">
                        Registrar
                    </button>
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
                        <button class="btn btn-danger table-button" title="Eliminar Movimiento" aria-label="Eliminar Movimiento" onclick="return confirm('¿Estás seguro de que deseas eliminar este movimiento?');">
                            <i class="fas fa-trash"></i>
                        </button>
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
                        alert(Error al intentar maximizar: $ {
                            err.message
                        }($ {
                            err.name
                        }));
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
