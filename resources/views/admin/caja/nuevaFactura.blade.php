@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <div class="text-center mb-0">
        <h1 class="font-weight-bold"
            style="font-size: 1.8rem; color: #000000; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
            NUEVA FACTURA
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

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }

        textarea {
            resize: none;
        }

        .totales {
            font-weight: bold;
            color: #6c757d;
        }

        .btn-group button {
            margin: 5px;
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
    </style>
@stop

@section('content')

    <div class="btn-group d-flex justify-content-between">
        <button class="btn btn-secondary" onclick="window.location='{{ route('admin.caja.historialFacturas') }}'">Historial</button>
        <button class="btn btn-secondary">Facturas Registradas</button>
        <button class="btn btn-secondary" onclick="window.location='{{ route('admin.caja.nuevaFactura') }}'">Facturas
            Nuevas</button>
    </div>

    <div class="row">
        <!-- Panel Izquierdo -->
        <div class="col-md-4 panel" style="background-color: #efefef; padding: 15px; border-radius: 5px;">
            <div class="form-group">
                <label for="nro_factura">Nro Factura</label>
                <input type="text" id="nro_factura" class="form-control">
            </div>
            <div class="form-group">
                <label for="nro_nit">Nro NIT</label>
                <input type="text" id="nro_nit" class="form-control">
            </div>
            <div class="form-group">
                <label for="razon_social">Razón Social</label>
                <input type="text" id="razon_social" class="form-control">
            </div>
            <div class="form-group">
                <label for="nro_autorizacion">Nro Autorización</label>
                <input type="text" id="nro_autorizacion" class="form-control">
            </div>
        </div>

        <!-- Panel Central -->
        <div class="col-md-4 panel" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
            <div class="form-group">
                <label for="fecha_factura">Fecha Factura</label>
                <input type="date" id="fecha_factura" class="form-control"
                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>
            <div class="form-group">
                <label for="subtotal">SubTotal</label>
                <input type="text" id="subtotal" class="form-control">
            </div>
            <div class="form-group">
                <label for="total">Total</label>
                <input type="text" id="total" class="form-control">
            </div>
            <div class="form-group">
                <label for="descuento">Descuento</label>
                <input type="text" id="descuento" class="form-control">
            </div>
            <div class="d-flex justify-content-center">
                <button class="btn btn-success">Guardar</button>
            </div>
        </div>

        <!-- Panel Derecho -->
        <div class="col-md-4 panel" style="background-color: #efefef; padding: 15px; border-radius: 5px;">
            <div class="form-group">
                <label for="cred_fiscal">Cred. Fiscal</label>
                <input type="text" id="cred_fiscal" class="form-control">
            </div>
            <div class="form-group">
                <label for="cod_control">Cod. Control</label>
                <input type="text" id="cod_control" class="form-control">
            </div>
            <div class="form-group">
                <label for="busqueda_nombre">Búsqueda por Nombre</label>
                <input type="text" id="busqueda_nombre" class="form-control"
                    placeholder="Ingrese el nombre del proveedor">
            </div>
            <div class="form-group">
                <label for="importe_cred_fiscal">Importe Sujeto Cred. Fiscal</label>
                <input type="text" id="importe_cred_fiscal" class="form-control" value="0">
                <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" id="habilitar_cred_fiscal">
                    <label class="form-check-label" for="habilitar_cred_fiscal">Habilitar Importe Cred. Fiscal</label>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        {{-- <div class="col-md-12 mt-4">
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
                            <td>*</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> --}}
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            if (!body.classList.contains('sidebar-collapse')) {
                body.classList.add('sidebar-collapse');
            }

            const habilitarCredFiscalCheckbox = document.getElementById('habilitar_cred_fiscal');
            const importeCredFiscalInput = document.getElementById('importe_cred_fiscal');

            habilitarCredFiscalCheckbox.addEventListener('change', function() {
                importeCredFiscalInput.disabled = !this.checked;
                if (!this.checked) {
                    importeCredFiscalInput.value = '0';
                }
            });
        });
    </script>
@stop
