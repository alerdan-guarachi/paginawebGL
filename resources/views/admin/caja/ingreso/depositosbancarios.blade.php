@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <div class="text-center mb-0">
        <h1 class="font-weight-bold"
            style="font-size: 1.8rem; color: #000000; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0;">
            DEPOSITOS BANCARIOS
        </h1>
    </div>
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

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <form method="GET" action="{{ route('admin.caja.ingreso.depositosbancarios') }}">
                <div class="row mb-3">
                    {{-- <div class="col-md-2">
                        <label for="fecha">Fecha de Registro:</label>
                        <input type="date" name="fecha" id="fecha" class="form-control" value="{{ $fecha }}">
                    </div> --}}
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

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha Mov.</th>
                            <th>Usuario Registro</th>
                            <th>Total Efectivo</th>
                            <th>Respaldo</th>
                            <th>Comprob.</th>
                            <th>Bancarizacion</th>
                            <th>Banco Destino</th>
                            <th>Selec.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registros as $registro)
                            <tr>
                                <td>{{ $registro->fecha }}</td>
                                <td>{{ $registro->usuarioRegistroNombre }}</td>
                                <td>{{ number_format($registro->total, 2) }}</td>
                                <td>
                                    @if ($registro->documentorespaldo)
                                        <a href="{{ asset('documentacioncaja/depositosbancarios/' . $registro->usuarioregistroid . '/' . $registro->documentorespaldo) }}" 
                                           class="btn btn-sm btn-outline-success" target="_blank" title="VER RESPALDO">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @else
                                        <span class="badge badge-danger">VACIO</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($registro->documentofactura)
                                        <a href="{{ asset('documentacioncaja/depositosbancarios/' . $registro->usuarioregistroid . '/' . $registro->documentofactura) }}" 
                                           class="btn btn-sm btn-outline-success" target="_blank" title="VER COMPROBANTE">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @else
                                        <span class="badge badge-danger">VACIO</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $registro->bancarizacion ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $registro->bancodestino ?? 'N/A' }}
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="registro_ids[]" value="{{ $registro->id }}" class="registro-checkbox">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                
            
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <!-- Respaldo -->
                            <div class="col-md-3 mb-4">
                                <label for="archivo" class="form-label fs-5 fw-semibold">Respaldo:</label>
                                <div class="d-flex align-items-center">
                                    <input type="file" name="archivo" id="archivo" class="form-control me-3" accept=".pdf,.jpg,.png" onchange="validateInputs()">
                                    <button type="button" class="btn btn-outline-primary me-3" id="verVistaPrevia" onclick="previewFile()"><i class="fas fa-eye"></i></button>
                                    
                                </div>
                            </div>
                
                            <!-- Comprobante -->
                            <div class="col-md-3 mb-4">
                                <label for="archivo3" class="form-label fs-5 fw-semibold">Comprobante:</label>
                                <div class="d-flex align-items-center">
                                    <input type="file" name="archivo3" id="archivo3" class="form-control me-3" accept=".pdf,.jpg,.png" onchange="validateInputs()">
                                    <button type="button" class="btn btn-outline-primary me-3" id="verVistaPrevia" onclick="previewFile3()"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                
                            <!-- Bancarización -->
                            <div class="col-md-3 mb-4">
                                <label for="bancarizacion" class="form-label fs-5 fw-semibold">Bancarización</label>
                                <input type="text" id="bancarizacion" name="bancarizacion" class="form-control">
                            </div>

                            <!-- Banco Destino -->
                            <div class="col-md-3 mb-4">
                                <label for="bancodestino" class="form-label fs-5 fw-semibold">Nro. Banco Destino</label>
                                <select name="bancodestino" id="bancodestino" class="form-control">
                                    <option value=""></option>
                                    @foreach ($cuentas as $cuenta)
                                        <option value="{{ $cuenta->numerocuenta }}">{{ $cuenta->numerocuenta }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-secondary" id="guardarRespaldo" disabled>GUARDAR DEPOSITO</button>
                        </div>
                    </div>
                </div>
                
                
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
        
        // Habilitar el botón si hay un archivo seleccionado
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