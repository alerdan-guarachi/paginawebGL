@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<h2 style="font-weight: 900">DOCUMENTACIÓN DE EGRESOS</h2>
@stop

@section('css')
<style>
    .table td {
        padding: 7px 10px;
    }
    .btn-verregistros {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 5px;
        }
    .btn-verregistros:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
</style>
@stop

@section('content')
@if (session('info'))
    <div id="alert-info" class="alert alert-success">
        <strong>{{ session('info') }}</strong>
    </div>
    <script>
        setTimeout(function() {
            $('#alert-info').fadeOut('fast');
        }, 3000);
    </script>
@endif

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <form method="GET" action="{{ route('admin.caja.egreso.documentacionegreso') }}">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="fecha">Fecha de Registro:</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ $fecha }}"
                                @if (!in_array($rolUsuario, ['ADMINISTRADOR', 'MAESTRO', 'ASISTENTE ADMINISTRATIVO'])) 
                                    min="{{ \Carbon\Carbon::now()->subDays(2)->toDateString() }}" 
                                @endif >
                        </div>
                        @if ($rolUsuario !== 'CONTABLE')
                            <div class="col-md-3">
                                <label for="usuario">Usuario Registro:</label>
                                <select name="usuario" id="usuario" class="form-control">
                                    <option value=""></option>
                                    @foreach ($usuarios as $usuario)
                                        <option value="{{ $usuario->usuarioconsolidadoID }}" 
                                            {{ $usuario->usuarioconsolidadoID == $usuarioSeleccionado ? 'selected' : '' }}>
                                            {{ $usuario->usuarioconsolidadoNombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-secondary">Buscar</button>
                        </div>
                    </div>
                </form>
                <form id="documentacionForm" method="POST" enctype="multipart/form-data" action="{{ route('guardar.respaldo.egreso') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-secondary">
                                <tr>
                                    <th>ID</th>
                                    <th>Proveedor</th>
                                    <th>Área</th>
                                    <th>Subtotal</th>
                                    <th>Desc.</th>
                                    <th>Monto Total</th>
                                    <th>Saldo</th>
                                    <th>Estado Pago</th>
                                    <th>Recibo</th>
                                    <th>Factura</th>
                                    <th>Comp.</th>
                                    <th>Sel.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registros as $registro)
                                    <tr>
                                        <td>{{ $registro->id }}</td>
                                        <td>{{ $registro->proveedornombre }}</td>
                                        <td>{{ $registro->area }}</td>
                                        <td>{{ number_format($registro->subtotal, 2) }}</td>
                                        <td>{{ number_format($registro->descuento, 2) }}</td>
                                        <td>{{ number_format($registro->montototal, 2) }}</td>
                                        <td>{{ number_format($registro->saldo, 2) }}</td>
                                        <td>{{ $registro->estado }}</td>
                                        <td>{{ $registro->nrorecibo }}
                                            @if ($registro->docrespaldoegreso)
                                                <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docrespaldoegreso) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER RECIBO"><i class="fas fa-eye"></i></a>
                                            @else
                                                <span class="badge badge-danger">VACIO</span>
                                            @endif
                                        </td>

                                        @php
                                            $ruta1 = public_path('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura);
                                            $ruta2 = public_path('comprobantescuentaspagar/' . $registro->docfactura);

                                            $ruta3 = public_path('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura2);
                                            $ruta4 = public_path('comprobantescuentaspagar/' . $registro->docfactura2);

                                            $ruta5 = public_path('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura3);
                                            $ruta6 = public_path('comprobantescuentaspagar/' . $registro->docfactura3);
                                        @endphp

                                        <td>
                                            @if (!empty($registro->docfactura) && file_exists($ruta1))
                                                <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @elseif (!empty($registro->docfactura) && file_exists($ruta2))
                                                <a href="{{ asset('comprobantescuentaspagar/' . $registro->docfactura) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <span class="badge badge-danger">VACÍO</span>
                                            @endif

                                            @if (!empty($registro->docfactura2) && file_exists($ruta4))
                                                <a href="{{ asset('comprobantescuentaspagar/' . $registro->docfactura2) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA 2">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @elseif (!empty($registro->docfactura2) && file_exists($ruta3))
                                                <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura2) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA 2">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif

                                            @if (!empty($registro->docfactura3) && file_exists($ruta6))
                                                <a href="{{ asset('comprobantescuentaspagar/' . $registro->docfactura3) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA 3">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @elseif (!empty($registro->docfactura3) && file_exists($ruta5))
                                                <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->docfactura3) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER FACTURA 3">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </td>

                                        @php
                                            $ruta1 = public_path('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->doccomprobante);
                                            $ruta2 = public_path('comprobantescuentaspagar/' . $registro->doccomprobante);
                                        @endphp

                                        <td>
                                            @if (!empty($registro->doccomprobante) && file_exists($ruta1))
                                                <a href="{{ asset('documentacioncaja/egresos/' . $registro->usuarioregistroid . '/' . $registro->doccomprobante) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER COMPROBANTE">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @elseif (!empty($registro->doccomprobante) && file_exists($ruta2))
                                                <a href="{{ asset('comprobantescuentaspagar/' . $registro->doccomprobante) }}" class="btn btn-sm btn-verregistros" target="_blank" title="VER COMPROBANTE">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <span class="badge badge-danger">VACÍO</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($rolUsuario === 'ADMINISTRADOR' || $rolUsuario === 'CONTABLE' || $rolUsuario === 'OPERATIVO' || $rolUsuario === 'ASISTENTE ADMINISTRATIVO')
                                                @if (!$registro->docfactura || !$registro->doccomprobante)
                                                    <input type="checkbox" name="registro_ids[]" value="{{ $registro->id }}" class="registro-checkbox">
                                                @endif
                                            @endif
                                            @can('admin.caja.egresos.editarrespaldosegresos')
                                                <input type="checkbox" name="registro_ids[]" value="{{ $registro->id }}" class="registro-checkbox">
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                
                        {{-- <div class="card">
                            <div class="card-body" style="background-color: #f7f7f7">
                                <div class="row">
                                    <!-- Factura -->
                                    <div class="col-md-6 mb-4">
                                        <label for="archivo2" class="form-label fs-5 fw-semibold">Factura:</label>
                                        <div class="d-flex align-items-center">
                                            <input type="file" name="archivo2" id="archivo2" class="form-control me-3" accept=".pdf,.jpg,.png" onchange="validateInputs()">
                                            <button type="button" class="btn btn-outline-secondary me-3" id="verVistaPrevia" onclick="previewFile2()"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                        
                                    <!-- Comprobante -->
                                    <div class="col-md-6 mb-4">
                                        <label for="archivo3" class="form-label fs-5 fw-semibold">Comprobante:</label>
                                        <div class="d-flex align-items-center">
                                            <input type="file" name="archivo3" id="archivo3" class="form-control me-3" accept=".pdf,.jpg,.png" onchange="validateInputs()">
                                            <button type="button" class="btn btn-outline-secondary me-3" id="verVistaPrevia" onclick="previewFile3()"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    @can('admin.caja.ingresos.editarrespaldosingresos')
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" id="actualizarEstadoButton" style="margin-right: 10px;" formaction="{{ route('actualizar.estado') }}" formmethod="POST" disabled>ACTUALIZAR ESTADO</button>
                                    @endcan
                                        <button type="submit" class="btn btn-outline-secondary btn-sm me-3" style="margin-right:5px;">GUARDAR RESPALDO</button>
                                </div>
                            </div>
                        </div> --}}
                        <div class="card"> 
                            <div class="card-body" style="background-color: #f7f7f7">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">
                                    
                                    <!-- IZQUIERDA: Factura y Comprobante -->
                                    <div class="d-flex align-items-center flex-wrap gap-4">
                                        <!-- Factura -->
                                        <div class="d-flex align-items-center">
                                            <label for="archivo2" class="form-label fs-6 fw-semibold me-2 mb-0">Factura:</label>
                                            <input type="file" name="archivo2" id="archivo2" class="form-control me-2" accept=".pdf,.jpg,.png" onchange="validateInputs()" style="width: 350px;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="previewFile2()">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>

                                        <!-- Comprobante -->
                                        <div class="d-flex align-items-center" style="margin-left: 20px;">
                                            <label for="archivo3" class="form-label fs-6 fw-semibold me-2 mb-0">Comprobante:</label>
                                            <input type="file" name="archivo3" id="archivo3" class="form-control me-2" accept=".pdf,.jpg,.png" onchange="validateInputs()" style="width: 350px;">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="previewFile3()">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- DERECHA: Botones -->
                                    <div class="d-flex align-items-center gap-2">
                                        {{-- @can('admin.caja.ingresos.editarrespaldosingresos')
                                            <button type="submit" class="btn btn-secondary btn-sm" id="actualizarEstadoButton"
                                                formaction="{{ route('actualizar.estado') }}" formmethod="POST" disabled>
                                                ACTUALIZAR ESTADO
                                            </button>
                                        @endcan --}}
                                        <button type="submit" class="btn btn-secondary btn-sm" style="margin-left: 10px;">
                                            GUARDAR RESPALDO
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="modalVistaPrevia" tabindex="-1" aria-labelledby="modalVistaPreviaLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalVistaPreviaLabel" style="font-weight: 700">VISTA PREVIA DEL ARCHIVO</h5>
                                    </div>
                                    <div class="modal-body">
                                        <img id="previewImage" src="#" alt="Vista previa de imagen" style="display:none; max-width: 100%;" />
                                        <iframe id="previewPdf" src="#" style="display:none; width: 100%; height: 600px;" frameborder="0"></iframe>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>   
                    </div>            
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    let selectedFile;

    function enablePreviewButton() {
        const fileInput = document.getElementById('archivo');
        const previewButton = document.getElementById('verVistaPrevia');
        const previewButton2 = document.getElementById('verVistaPrevia2');
        const previewButton3 = document.getElementById('verVistaPrevia3');
        
        // Habilitar el botón si hay un archivo seleccionado
        previewButton.disabled = !fileInput.files.length;
        previewButton2.disabled = !fileInput.files.length;
        previewButton3.disabled = !fileInput.files.length;
    }

    function previewFile() {
        const file = document.getElementById('archivo').files[0];
        const previewImage = document.getElementById('previewImage');
        const previewPdf = document.getElementById('previewPdf');

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                const fileType = file.type;

                if (fileType.startsWith('image/')) {
                    // Si es una imagen
                    previewImage.src = e.target.result; // Establece la fuente de la imagen
                    previewImage.style.display = 'block'; // Muestra la imagen
                    previewPdf.style.display = 'none'; // Oculta el PDF
                } else if (fileType === 'application/pdf') {
                    // Si es un PDF
                    previewPdf.src = e.target.result; // Establece la fuente del PDF
                    previewPdf.style.display = 'block'; // Muestra el PDF
                    previewImage.style.display = 'none'; // Oculta la imagen
                } else {
                    alert('Tipo de archivo no soportado. Solo se permiten imágenes y PDFs.');
                    return;
                }

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('modalVistaPrevia'));
                modal.show();
            };

            reader.readAsDataURL(file); // Lee el archivo como URL de datos
        }
    }

    function previewFile2() {
        const file = document.getElementById('archivo2').files[0];
        const previewImage = document.getElementById('previewImage');
        const previewPdf = document.getElementById('previewPdf');

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                const fileType = file.type;

                if (fileType.startsWith('image/')) {
                    // Si es una imagen
                    previewImage.src = e.target.result; // Establece la fuente de la imagen
                    previewImage.style.display = 'block'; // Muestra la imagen
                    previewPdf.style.display = 'none'; // Oculta el PDF
                } else if (fileType === 'application/pdf') {
                    // Si es un PDF
                    previewPdf.src = e.target.result; // Establece la fuente del PDF
                    previewPdf.style.display = 'block'; // Muestra el PDF
                    previewImage.style.display = 'none'; // Oculta la imagen
                } else {
                    alert('Tipo de archivo no soportado. Solo se permiten imágenes y PDFs.');
                    return;
                }

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('modalVistaPrevia'));
                modal.show();
            };

            reader.readAsDataURL(file); // Lee el archivo como URL de datos
        }
    }

    function previewFile3() {
        const file = document.getElementById('archivo3').files[0];
        const previewImage = document.getElementById('previewImage');
        const previewPdf = document.getElementById('previewPdf');

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                const fileType = file.type;

                if (fileType.startsWith('image/')) {
                    // Si es una imagen
                    previewImage.src = e.target.result; // Establece la fuente de la imagen
                    previewImage.style.display = 'block'; // Muestra la imagen
                    previewPdf.style.display = 'none'; // Oculta el PDF
                } else if (fileType === 'application/pdf') {
                    // Si es un PDF
                    previewPdf.src = e.target.result; // Establece la fuente del PDF
                    previewPdf.style.display = 'block'; // Muestra el PDF
                    previewImage.style.display = 'none'; // Oculta la imagen
                } else {
                    alert('Tipo de archivo no soportado. Solo se permiten imágenes y PDFs.');
                    return;
                }

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('modalVistaPrevia'));
                modal.show();
            };

            reader.readAsDataURL(file); // Lee el archivo como URL de datos
        }
    }
</script> 
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.registro-checkbox');
        const submitButton = document.getElementById('guardarRespaldo');
        const actualizarButton = document.getElementById('actualizarEstadoButton');
        const fileInput = document.getElementById('archivo2');
    
        // Función para validar los inputs
        function validateInputs() {
            const isChecked = Array.from(checkboxes).some(cb => cb.checked);
            const isFileSelected = fileInput.files.length > 0;
    
            submitButton.disabled = !(isChecked && isFileSelected); // Habilitar/Deshabilitar botón Guardar Respaldo
            actualizarButton.disabled = !isChecked; // Habilitar/Deshabilitar botón Actualizar Estado
        }
    
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', validateInputs); // Llama a validateInputs al cambiar el checkbox
        });
    
        fileInput.addEventListener('change', validateInputs); // Llama a validateInputs al seleccionar un archivo
    });
    </script>

<script>
    // Función para actualizar el estado en la tabla cajacentral
    function actualizarEstado() {
        const selectedIds = Array.from(document.querySelectorAll('.registro-checkbox:checked')).map(checkbox => checkbox.value);
    
        if (selectedIds.length === 0) {
            alert('Por favor, selecciona al menos un registro.');
            return;
        }
    
        // Realiza una solicitud AJAX para actualizar el estado
        fetch('{{ route("actualizar.estado") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Estado actualizado correctamente.');
                location.reload(); // Recargar la página para ver los cambios
            } else {
                alert('Error al actualizar el estado.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error en la solicitud.');
        });
    }
    </script>
@stop