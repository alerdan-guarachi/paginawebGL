@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
@php
    $rolUsuario = auth()->user()->getRoleNames()->first();
@endphp
@php
    $tieneRolContable = auth()->user()->getRoleNames()->contains('CONTABLE');
@endphp

{{-- @if (!$mostrarVista && $rolUsuario === 'CONTABLE') --}}
@if (!$mostrarVista && $tieneRolContable)
    <div class="alert alert-danger text-center py-4" style="border-radius: 10px; background-color: #f8d7da; color: #842029; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
        <h4 class="font-weight-bold mb-3" style="text-transform: uppercase; letter-spacing: 1px;">Caja Bloqueada</h4>
        <p class="mb-4" style="font-size: 1.1rem;">
            {{ $motivoBloqueo ?? 'TU CAJA ESTA BLOQUEADA, SOLICITA UN CÓDIGO DE DESBLOQUEO A ADMINISTRACIÓN.' }}
        </p>
        <form action="{{ route('verificar.codigo4') }}" method="POST" style="max-width: 500px; margin: 0 auto;">
            @csrf
            <div class="form-group mb-3">
                <label for="codigo" class="font-weight-bold" style="font-size: 1rem;">Ingresa el código para continuar:</label>
                <input type="text" id="codigo" name="codigo" class="form-control" placeholder="Código de autorización" required style="border-radius: 5px;">
            </div>
            <button type="submit" class="btn btn-sm btn-success btn-block" style="padding: 5px 10px; font-size: 1rem; border-radius: 5px;">VALIDAR CÓDIGO</button>
        </form>
    </div>
@else
<h2 style="font-weight: 900">DEPÓSITOS BANCARIOS</h2>
@endif
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

@if (!$mostrarVista && $tieneRolContable)
@else
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.caja.ingreso.depositosbancarios') }}">
                    <div class="row mb-3">
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
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-secondary">Buscar</button>
                            </div>
                        @endif
                    </div>
                </form>
                <form id="documentacionForm" method="POST" enctype="multipart/form-data" action="{{ route('guardardepositobancario') }}">
                    @csrf
                    <input type="hidden" name="fecha" id="fechaSeleccionada">
                    <input type="hidden" name="monto" id="montoSeleccionado">
                    <input type="hidden" name="usuarioregistro" id="usuarioregistro">

                    <div class="card"> 
                        <div class="card-body" style="background-color: #f7f7f7;">
                            <div class="row" style="margin-top: -10px; margin-bottom: -20px;">
                                <!-- Respaldo -->
                                <div class="col-md-3 mb-4">
                                    <label for="archivo" class="form-label fs-5 fw-semibold">Respaldo:</label>
                                    <div class="d-flex align-items-center">
                                        <input type="file" name="archivo" id="archivo" class="form-control me-3" accept=".pdf,.jpg,.png" onchange="validateInputs()">
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-3" id="verVistaPrevia" onclick="previewFile()"><i class="fas fa-eye"></i></button>
                                        
                                    </div>
                                </div>
                    
                                <!-- Comprobante -->
                                <div class="col-md-3 mb-4">
                                    <label for="archivo3" class="form-label fs-5 fw-semibold">Comprobante:</label>
                                    <div class="d-flex align-items-center">
                                        <input type="file" name="archivo3" id="archivo3" class="form-control me-3" accept=".pdf,.jpg,.png" onchange="validateInputs()">
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-3" id="verVistaPrevia" onclick="previewFile3()"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                    
                                <!-- Bancarización -->
                                <div class="col-md-2 mb-4">
                                    <label for="bancarizacion" class="form-label fs-5 fw-semibold">Bancarización</label>
                                    <input type="text" id="bancarizacion" name="bancarizacion" class="form-control">
                                </div>

                                <!-- Banco Destino -->
                                <div class="col-md-2 mb-4">
                                    <label for="bancodestino" class="form-label fs-5 fw-semibold">Nro. Banco Destino</label>
                                    <select name="bancodestino" id="bancodestino" class="form-control">
                                        <option value=""></option>
                                        @foreach ($cuentas as $cuenta)
                                            <option value="{{ $cuenta->numerocuenta }}">{{ $cuenta->numerocuenta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-4 d-flex flex-column justify-content-end">
                                    <label class="form-label fs-5 fw-semibold invisible">.</label>
                                    <button type="submit" class="btn btn-secondary" id="guardarRespaldo" disabled>GUARDAR DEPÓSITO</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped">
                        <thead class="table-secondary text-center align-middle">
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario Registro</th>
                                <th>Total Efectivo</th>
                                <th>Monto Depositado</th>
                                <th>Respaldo</th>
                                <th>Comprob.</th>
                                <th>Bancarización</th>
                                <th>Banco Destino</th>
                                <th>Selec.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registros as $registro)
                                @php
                                    $depositos = $registro->depositos;
                                    $rowCount = $depositos->count() > 0 ? $depositos->count() : 1;
                                @endphp

                                @foreach ($depositos->isEmpty() ? [null] : $depositos as $index => $deposito)
                                    <tr>
                                        @if ($index === 0)
                                            <td rowspan="{{ $rowCount }}" class="text-center align-middle bg-white">{{ $registro->fecha }}</td>
                                            <td rowspan="{{ $rowCount }}" class="text-center align-middle bg-white">{{ $registro->usuarioRegistroNombre }}</td>
                                            <td rowspan="{{ $rowCount }}" class="text-center align-middle bg-white">{{ number_format($registro->total, 2) }}</td>
                                        @endif

                                        <td class="text-center align-middle">
                                            {{ isset($deposito->monto) ? number_format($deposito->monto, 2, '.', ',') : 'N/A' }}
                                        </td>

                                        <td class="text-center align-middle">
                                            @if ($deposito && $deposito->documentorespaldo)
                                                <a class="btn btn-sm btn-verregistros" href="{{ asset('documentacioncaja/depositosbancarios/' . $deposito->usuarioregistroid . '/' . $deposito->documentorespaldo) }}" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <span class="badge badge-danger">VACÍO</span>
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">
                                            @if ($deposito && $deposito->documentofactura)
                                                <a class="btn btn-sm btn-verregistros" href="{{ asset('documentacioncaja/depositosbancarios/' . $deposito->usuarioregistroid . '/' . $deposito->documentofactura) }}" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <span class="badge badge-danger">VACÍO</span>
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">{{ $deposito->bancarizacion ?? 'N/A' }}</td>
                                        <td class="text-center align-middle">{{ $deposito->bancodestino ?? 'N/A' }}</td>

                                        <td class="text-center align-middle">
                                            @if ((!$deposito || !$deposito->documentorespaldo) && (!$deposito || !$deposito->documentofactura))
                                                <input type="checkbox" name="registro_ids[]" value="{{ $registro->id }}" class="registro-checkbox">
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Modal Vista Previa-->
                    <div class="modal fade" id="modalVistaPrevia" tabindex="-1" aria-labelledby="modalVistaPreviaLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalVistaPreviaLabel">VISTA PREVIA</h5>
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
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkboxes = document.querySelectorAll(".registro-checkbox");
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener("change", actualizarValores);
        });

        function actualizarValores() {
            let fechas = [];
            let totalMonto = 0;
            let usuarios = new Set();

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    let fila = checkbox.closest("tr");
                    let fecha = fila.cells[0].textContent.trim();
                    let usuario = fila.cells[1].textContent.trim();
                    let monto = parseFloat(fila.cells[2].textContent.replace(",", ""));

                    fechas.push(fecha);
                    usuarios.add(usuario);
                    totalMonto += isNaN(monto) ? 0 : monto;
                }
            });

            if (fechas.length > 0) {
                document.getElementById("fechaSeleccionada").value = fechas.sort()[0];
            } else {
                document.getElementById("fechaSeleccionada").value = "";
            }
            document.getElementById("montoSeleccionado").value = totalMonto.toFixed(2);

            document.getElementById("usuarioregistro").value = usuarios.size === 1 ? [...usuarios][0] : "";
        }
    });
</script>
<script>
    let selectedFile;
    function enablePreviewButton() {
        const fileInput = document.getElementById('archivo');
        const previewButton = document.getElementById('verVistaPrevia');
        previewButton.disabled = !fileInput.files.length;
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
        const fileInput = document.getElementById('archivo');
    
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