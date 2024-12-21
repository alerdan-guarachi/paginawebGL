@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <div class="text-center mb-0">
        <h1 class="font-weight-bold"
            style="font-size: 1.8rem; color: #000000; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
            APROBACIONES DE CUENTAS - COBRAR
        </h1>
    </div>
@stop

@section('css')
    <style>
        .form-group label {
            font-weight: bold;
            color: #6c757d;
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

        .btn-group button {
            margin: 5px;
        }


        textarea {
            resize: none;
        }

        .input-group-custom input,
        select {
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

        .upload-icon {
            font-size: 3rem;
            color: #28a745;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            text-align: center;
            padding: 0.6rem;
            background-color: #fff;
            border: 2px solid #28a745;
            height: 70px;
            width: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .upload-icon:hover {
            background-color: #28a745;
            color: #fff;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3);
        }

        .file-preview {
            margin-top: 10px;
            font-size: 1rem;
            color: #6c757d;
            text-align: center;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
            gap: 2rem; /* Espacio entre elementos */
        }
    </style>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Grupo de Pestañas -->
        <div class="btn-group d-flex justify-content-between mb-3">
            <button class="btn btn-secondary" onclick="window.location='{{ route('admin.caja.cobrar') }}'">Cuentas Pendientes</button>
            {{-- <button class="btn btn-secondary">Enviar Cuentas</button> --}}
            <button class="btn btn-secondary" onclick="window.location='{{ route('admin.caja.aprobacionesCobrar') }}'">APROBACIONES DE CUENTAS</button>
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
                        @for ($i = 1; $i <= 20; $i++)
                            <tr>
                                <td>{{ 53122 + $i }}</td>
                                <td>{{ rand(0, 10) }}</td>
                                <td>Cliente {{ $i }}</td>
                                <td>Detalle {{ $i }}</td>
                                <td>Asesoramiento</td>
                                <td>{{ now()->subDays($i)->format('m/d/Y') }}</td>
                                <td>{{ now()->addDays($i)->format('m/d/Y') }}</td>
                                <td>{{ number_format(rand(1000, 3000), 2) }}</td>
                                <td>{{ number_format(0, 2) }}</td>
                                <td>{{ number_format(rand(1000, 3000), 2) }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Botones Aprobar y Subir Documento -->
            <div class="action-buttons">
                <button class="btn btn-success">Aprobar</button>

                <div>
                    <label class="upload-icon" for="file-upload">
                        <i class="fas fa-upload"></i>
                    </label>
                    <input type="file" id="file-upload" style="display: none;">
                    <div class="file-preview" id="file-preview"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            if (!body.classList.contains('sidebar-collapse')) {
                body.classList.add('sidebar-collapse');
            }

            const fileInput = document.getElementById('file-upload');
            const filePreview = document.getElementById('file-preview');

            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    filePreview.textContent = `Documento seleccionado: ${fileInput.files[0].name}`;
                } else {
                    filePreview.textContent = 'No hay documento seleccionado';
                }
            });
        });
    </script>
@stop
