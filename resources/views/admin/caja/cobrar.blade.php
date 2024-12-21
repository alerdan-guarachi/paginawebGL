@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <div class="text-center mb-0">
        <h1 class="font-weight-bold"
            style="font-size: 1.8rem; color: #000000; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
            CUENTAS POR COBRAR - PENDIENTES
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
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-primary:hover {
            background-color: #228f3b;
            border-color: #228f3b;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }

        .totales {
            font-weight: bold;
            color: #6c757d;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
        }

        .btn-group button {
            margin: 5px;
        }

        .add-icon {
            font-size: 2.5rem;
            color: #28a745;
            cursor: pointer;
            line-height: 1;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            text-align: center;
            padding: 0.3rem;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            border: 2px solid #28a745;
            height: 50px;
            width: 50px;
        }

        .add-icon:hover {
            background-color: #28a745;
            color: #fff;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3);
        }

        .search-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        textarea {
            resize: none;
        }

        .input-group-custom input, select {
            margin-right: 5px;
        }

        .input-group-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .flex-equitable {
            flex: 1;
            margin-right: 10px;
        }

        .align-items-center-right {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .totales-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            margin-top: 1rem;
        }

        .search-options-container {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .dynamic-field {
            display: none;
            margin-top: 10px;
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Grupo de Pestañas -->
        <div class="btn-group d-flex justify-content-between mb-3">
            <button class="btn btn-secondary">Cuentas Pendientes</button>
            {{-- <button class="btn btn-secondary">Enviar Cuentas</button> --}}
            <button class="btn btn-secondary">Historial de Cuentas</button>
        </div>

        <!-- Filtros -->
        <div class="search-options-container">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="search-options">
                        <div class="d-flex align-items-center">
                            <div class="form-group mb-0 flex-equitable">
                                <label for="fecha_inicial">Fecha Inicial</label>
                                <input type="date" id="fecha_inicial" class="form-control">
                            </div>
                            <div class="form-group mb-0 flex-equitable">
                                <label for="fecha_final">Fecha Final</label>
                                <input type="date" id="fecha_final" class="form-control">
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="form-check mb-0 mr-3 flex-equitable">
                                <input type="checkbox" class="form-check-input" id="buscar_pago">
                                <label class="form-check-label" for="buscar_pago">Buscar por Fecha de Pago</label>
                                <div class="dynamic-field" id="buscar_pago-field">
                                    <input type="date" class="form-control">
                                </div>
                            </div>
                            <div class="form-check mb-0 mr-3 flex-equitable">
                                <input type="checkbox" class="form-check-input" id="buscar_f_retrasadas">
                                <label class="form-check-label" for="buscar_f_retrasadas">Buscar por Fechas Retrasadas</label>
                                <div class="dynamic-field" id="buscar_f_retrasadas-field">
                                    <input type="date" class="form-control">
                                </div>
                            </div>
                            <div class="form-check mb-0 mr-3 flex-equitable">
                                <input type="checkbox" class="form-check-input" id="todas_sucursales">
                                <label class="form-check-label" for="todas_sucursales">Todas las Sucursales</label>
                                <div class="dynamic-field" id="todas_sucursales-field">
                                    <select class="form-control">
                                        <option value="">Seleccione...</option>
                                        <option>Sucursal 1</option>
                                        <option>Sucursal 2</option>
                                        <option>Sucursal 3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-check mb-0 flex-equitable">
                                <input type="checkbox" class="form-check-input" id="buscar_estado">
                                <label class="form-check-label" for="buscar_estado">Buscar por Estado</label>
                                <div class="dynamic-field" id="buscar_estado-field">
                                    <select class="form-control">
                                        <option value="">Seleccione...</option>
                                        <option>Activo</option>
                                        <option>Inactivo</option>
                                        <option>Pendiente</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary ml-3">Listar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campo de Búsqueda -->
        <div class="col-md-12 mb-3">
            <label for="buscar_nombre" class="font-weight-bold text-success">BUSCAR</label>
            <input type="text" id="buscar_nombre" class="form-control" placeholder="Ingrese nombre o detalle">
        </div>

        <!-- Tabla -->
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID CAJA</th>
                            <th>CÓDIGO</th>
                            <th>CLIENTE / RAZÓN SOCIAL</th>
                            <th>DETALLE</th>
                            <th>AREA</th>
                            <th>FECHA OPERACIÓN</th>
                            <th>FECHA CRÉDITO</th>
                            <th>MONTO REAL</th>
                            <th>DESCUENTO</th>
                            <th>MONTO TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>53122</td>
                            <td>0</td>
                            <td>OSCAR ROLANDO</td>
                            <td>INFORME FINAL</td>
                            <td>ASESORAMIENTO</td>
                            <td>7/27/2021</td>
                            <td>10/8/2021</td>
                            <td>2100.00</td>
                            <td>0.00</td>
                            <td>2100.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Campos de Acción Inferiores -->
        <div class="col-md-12 mt-4">
            <div class="row input-group-custom">
                <div class="form-group flex-equitable">
                    <label>Cliente</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group flex-equitable">
                    <label>Detalle</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group flex-equitable">
                    <label>Tipo de Transacción</label>
                    <select class="form-control">
                        <option>Seleccione...</option>
                        <option>Efectivo</option>
                        <option>Transferencias Bancarias</option>
                        <option>Depositos Bancarios</option>
                        <option>Cheque</option>
                        <option>ATC</option>
                    </select>
                </div>
                <div class="form-group flex-equitable">
                    <label>Saldo</label>
                    <input type="text" class="form-control" value="0">
                </div>
                <div class="form-group flex-equitable">
                    <label>Cancelar</label>
                    <input type="text" class="form-control" value="0">
                </div>
                <div class="form-group flex-equitable">
                    <label>Nuevo Saldo</label>
                    <input type="text" class="form-control" value="0">
                </div>
                <div class="align-items-center-right">
                    <span class="add-icon">+</span>
                </div>
            </div>
        </div>

        <!-- Totales -->
        <div class="totales-container">
            <div>Total Ingresos: <span class="text-success font-weight-bold">14027.80</span></div>
            <div>Nuevo Saldo: <span class="text-success font-weight-bold">0</span></div>
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
        });

        document.addEventListener('DOMContentLoaded', function () {
            const fields = [
                { checkbox: 'buscar_pago', field: 'buscar_pago-field' },
                { checkbox: 'buscar_f_retrasadas', field: 'buscar_f_retrasadas-field' },
                { checkbox: 'todas_sucursales', field: 'todas_sucursales-field' },
                { checkbox: 'buscar_estado', field: 'buscar_estado-field' },
            ];

            fields.forEach(({ checkbox, field }) => {
                const checkboxElement = document.getElementById(checkbox);
                const fieldElement = document.getElementById(field);

                checkboxElement.addEventListener('change', function () {
                    fieldElement.style.display = this.checked ? 'block' : 'none';
                });
            });
        });
    </script>
@stop