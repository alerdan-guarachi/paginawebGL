@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <div class="text-center mb-0">
        <h1 class="font-weight-bold"
            style="font-size: 1.8rem; color: #000000; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
            CIERRE DE INGRESOS
        </h1>
    </div>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: bold;
            color: #6c757d;
        }

        .btn-primary {
            background-color: #6c757d;
            border-color: #6c757d;

        }

        .btn-primary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }

        .actions-container {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .listar-datos {
            float: right;
            width: auto;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <!-- Filtros -->
        <div class="col-md-12 mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="usuario">Usuario</label>
                        <select id="usuario" class="form-control">
                            <option>TODOS LOS USUARIOS</option>
                            <option>ELIAS EFRAIN DURAN SANDOVAL</option>
                            <option>Usuario 1</option>
                            <option>Usuario 2</option>
                        </select>
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="todos_usuarios" checked>
                            <label class="form-check-label" for="todos_usuarios">Todos los Usuarios</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="forma_pago">Forma de Pago</label>
                        <select id="forma_pago" class="form-control">
                            <option>TODOS LOS PAGOS</option>
                            <option>Efectivo</option>
                            <option>Cuentas por Pagar</option>
                            <option>Cuentas por Cobrar</option>
                            <option>Transferencia Bancaria</option>
                            <option>Deposito Bancario</option>
                            <option>Cheque</option>
                            <option>ATC</option>
                        </select>
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="todos_pagos" checked>
                            <label class="form-check-label" for="todos_pagos">Todos los Pagos</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" class="form-control">
                            <option>DOCUMENTACION PENDIENTE</option>
                            <option>CANCELADO</option>
                            <option>PENDIENTE</option>
                            <option>ANULADO</option>
                            <option>FINALIZADO</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fecha_cierre">Fecha de Cierre</label>
                        <input type="date" id="fecha_cierre" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-12 d-flex justify-content-end">
                    <button class="btn btn-primary listar-datos">Listar Datos</button>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Forma de Pago</th>
                            <th>Estado</th>
                            <th>Fecha de Cierre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ELIAS EFRAIN DURAN SANDOVAL</td>
                            <td>Efectivo</td>
                            <td>PENDIENTE</td>
                            <td>2024-12-13</td>
                            <td>
                                <button class="btn btn-success btn-sm cambiar-estado">Cambiar Estado</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Usuario 1</td>
                            <td>Transferencia Bancaria</td>
                            <td>FINALIZADO</td>
                            <td>2024-12-12</td>
                            <td>
                                <button class="btn btn-success btn-sm cambiar-estado">Cambiar Estado</button>
                                <button class="btn btn-danger btn-sm">Eliminar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botones de Acciones -->
        <div class="col-md-12 actions-container">
            <button class="btn btn-success">Apertura Caja</button>
            <button class="btn btn-success">Aprobar Cierre</button>
        </div>

        <!-- Observaciones -->
        <div class="col-md-12 mt-4">
            <div class="form-group">
                <label for="observaciones">Observaciones</label>
                <textarea id="observaciones" class="form-control" rows="4"></textarea>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;
            if (!body.classList.contains('sidebar-collapse')) {
                body.classList.add('sidebar-collapse');
            }

            const todosUsuariosCheckbox = document.getElementById('todos_usuarios');
            const usuarioSelect = document.getElementById('usuario');

            todosUsuariosCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    usuarioSelect.value = 'TODOS LOS USUARIOS';
                }
            });

            usuarioSelect.addEventListener('change', function () {
                const options = Array.from(usuarioSelect.options);
                const allSelected = options.every(option => option.selected);
                todosUsuariosCheckbox.checked = allSelected;
            });

            const todosPagosCheckbox = document.getElementById('todos_pagos');
            const formaPagoSelect = document.getElementById('forma_pago');

            todosPagosCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    formaPagoSelect.value = 'TODOS LOS PAGOS';
                }
            });

            formaPagoSelect.addEventListener('change', function () {
                const options = Array.from(formaPagoSelect.options);
                const allSelected = options.every(option => option.selected);
                todosPagosCheckbox.checked = allSelected;
            });

            document.querySelectorAll('.cambiar-estado').forEach(button => {
                button.addEventListener('click', function () {
                    const row = this.closest('tr');
                    const estadoCell = row.querySelector('td:nth-child(3)');

                    const estadoSelect = document.createElement('select');
                    estadoSelect.classList.add('form-control');

                    const estados = ['DOCUMENTACION PENDIENTE', 'CANCELADO', 'PENDIENTE', 'ANULADO', 'FINALIZADO'];
                    estados.forEach(estado => {
                        const option = document.createElement('option');
                        option.value = estado;
                        option.textContent = estado;
                        if (estado === estadoCell.textContent) {
                            option.selected = true;
                        }
                        estadoSelect.appendChild(option);
                    });

                    estadoCell.innerHTML = '';
                    estadoCell.appendChild(estadoSelect);

                    estadoSelect.addEventListener('change', function () {
                        estadoCell.textContent = this.value;
                    });
                });
            });
        });
    </script>
@stop