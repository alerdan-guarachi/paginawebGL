@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <div class="text-center mb-0">
        <h1 class="font-weight-bold"
            style="font-size: 1.8rem; color: #000000; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
            HISTORIAL DE FACTURAS EMITIDAS
        </h1>
    </div>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: bold;
            color: #6c757d;
        }

        .btn-group a {
            margin: 5px;
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

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }

        .totales {
            font-weight: bold;
            color: #000000;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
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

        .btn-group .btn {
            margin: 0.25rem;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #4e555b;
        }

        .table-bordered th, .table-bordered td {
            white-space: nowrap;
        }

        .background-container {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            padding: 1rem;
        }
    </style>
@stop

@section('content')
    <div class="background-container">
        <!-- Filtros y Tabs -->
        <div class="col-md-12 mb-4">
            <div class="btn-group d-flex justify-content-between">
                <a class="btn btn-secondary" onclick="window.location='{{ route('admin.caja.historialFacturas') }}'">Historial</a>
                <a class="btn btn-secondary">Facturas Registradas</a>
                <a class="btn btn-secondary" onclick="window.location='{{ route('admin.caja.nuevaFactura') }}'">Facturas Nuevas</a>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="ciudad">Ciudad</label>
                        <select id="ciudad" class="form-control">
                            <option>Todas las ciudades</option>
                            <option>Santa Cruz</option>
                            <option>Cochabamba</option>
                            <option>La Paz</option>
                        </select>
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="todas_ciudades" checked>
                            <label class="form-check-label" for="todas_ciudades">Todas las ciudades</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="fecha">Búsqueda por fechas</label>
                        <input type="date" id="fecha" class="form-control">
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a class="btn btn-primary w-100">Listar</a>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Especificación</th>
                            <th>NIT Proveedor</th>
                            <th>Razón Social Proveedor</th>
                            <th>Código Autorización</th>
                            <th>Número Factura</th>
                            <th>Número DUI/DIM</th>
                            <th>Fecha Factura DUI/DIM</th>
                            <th>Importe Total Compra</th>
                            <th>Importe ICE</th>
                            <th>Importe IEHD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>1</td>
                            <td>1020255020</td>
                            <td>TELEFONÍA CELULAR</td>
                            <td>4.16E+14</td>
                            <td>38150</td>
                            <td>0</td>
                            <td>03/01/2022</td>
                            <td>772.51</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>1</td>
                            <td>258640027</td>
                            <td>JL INDUSTRIES</td>
                            <td>1.14E+14</td>
                            <td>17128</td>
                            <td>0</td>
                            <td>06/01/2022</td>
                            <td>30.5</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totales -->
        <div class="totales-container">
            <div class="totales">Total Compras: <span class="text-success font-weight-bold">-</span></div>
            <div class="totales">Total Ventas: <span class="text-success font-weight-bold">-</span></div>
            <div class="totales">Saldo de Venta: <span class="text-success font-weight-bold">-</span></div>
        </div>

        <!-- Botón Generar -->
        <div class="col-md-12 mt-3">
            <a class="btn btn-success w-100">Generar</a>
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
            
            const todasCiudadesCheckbox = document.getElementById('todas_ciudades');
            const ciudadSelect = document.getElementById('ciudad');

            todasCiudadesCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    ciudadSelect.value = 'Todas las ciudades';
                }
            });

            ciudadSelect.addEventListener('change', function () {
                todasCiudadesCheckbox.checked = ciudadSelect.value === 'Todas las ciudades';
            });
        });
    </script>
@stop