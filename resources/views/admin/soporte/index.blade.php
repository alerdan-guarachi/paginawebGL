@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <a class="btn btn-sm float-right btn-regresar" href="#">REGRESAR</a>
    <a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaHistorial">HISTORIAL DE
        SOLICITUDES</a>
    <h1>Asignación de Solicitudes</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
    <style>
        .custom2-button {
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
            padding: 10px 20px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .custom2-button:hover {
            background-color: #faa625;
            color: #ffffff;
        }

        .img-preview {
            width: 100%;
            /* Ocupar el ancho máximo permitido */
            height: auto;
            /* Mantener la proporción original */
            margin-top: 10px;
            display: none;
            /* Oculto por defecto */
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        #motivoSolicitud {
            height: 220px;
            /* Ajuste para igualar la altura de los ejemplos */
        }

        .priority-examples {
            margin-top: 10px;
            font-size: 14px;
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 5px solid #faa625;
            border-radius: 5px;
        }

        .fas.fa-file-image {
            font-size: 1.5rem;
            color: #007bff;
            transition: color 0.3s ease;
            margin-right: 5px;
            cursor: pointer;
        }

        .fas.fa-file-image:hover {
            color: #0056b3;
        }

        .badge-warning {
            color: #ffffff !important;
            /* Forzar texto blanco */
        }

        #ventanaHistorial .table-striped th,
        #ventanaHistorial .table-striped td {
            vertical-align: middle;
            /* Quitar white-space: nowrap; para permitir quiebres de línea */
            text-align: left;
        }
    </style>
@stop

@section('content')

    @if (session('success'))
        <div id="alert-success" class="alert alert-success">
            <strong>{{ session('success') }}</strong>
        </div>
        <script>
            setTimeout(function() {
                $('#alert-success').fadeOut('fast');
            }, 5000); // Ocultar después de 5 segundos
        </script>
    @endif

    @if ($errors->any())
        <div id="alert-error" class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <script>
            setTimeout(function() {
                $('#alert-error').fadeOut('fast');
            }, 5000); // Ocultar después de 5 segundos
        </script>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.soporte.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="motivoSolicitud">Motivo de Solicitud:</label>
                            <textarea id="motivoSolicitud" name="motivosolicitud" class="form-control"
                                placeholder="Ingrese el motivo de la solicitud" required></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="nivelPrioridad">Nivel de Prioridad:</label>
                            <select id="nivelPrioridad" name="nivelprioridad" class="form-control" required>
                                <option value="" disabled selected>Seleccione el nivel de prioridad</option>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                            </select>
                            <div class="priority-examples">
                                <strong>Ejemplo:</strong>
                                <p><b>Baja:</b> Cambios estéticos o mejoras opcionales, como actualizar colores o íconos en
                                    la interfaz.</p>
                                <p><b>Media:</b> Funcionalidades operativas con fallos menores, como botones que no
                                    funcionan en casos específicos o menús desplegables que no responden correctamente en
                                    determinadas situaciones.</p>
                                <p><b>Alta:</b> Interrupciones críticas, como errores que impiden el inicio de sesión, la
                                    pérdida de datos o el bloqueo completo del sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="imagen1">Imagen 1:</label>
                            <input type="file" id="imagen1" name="motivoimagen1" class="form-control" accept="image/*"
                                required onchange="previewImage(this, 'preview1')">
                            <img id="preview1" class="img-preview" alt="Previsualización Imagen 1">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="imagen2">Imagen 2 (Opcional):</label>
                            <input type="file" id="imagen2" name="motivoimagen2" class="form-control" accept="image/*"
                                onchange="previewImage(this, 'preview2')">
                            <img id="preview2" class="img-preview" alt="Previsualización Imagen 2">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-crear">Registrar Solicitud</button>
            </form>
        </div>
    </div>

    <!-- Modal Historial -->
    <div class="modal fade" id="ventanaHistorial" tabindex="-1" role="dialog" aria-labelledby="historialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">HISTORIAL DE SOLICITUDES:</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Pestañas -->
                    <ul class="nav nav-tabs" id="tabHistorial" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="atendidos-tab" data-toggle="tab" href="#atendidos" role="tab"
                                aria-controls="atendidos" aria-selected="true">Atendidos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pendientes-tab" data-toggle="tab" href="#pendientes" role="tab"
                                aria-controls="pendientes" aria-selected="false">Pendientes</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="tabHistorialContent">
                        <!-- Atendidos -->
                        <div class="tab-pane fade show active" id="atendidos" role="tabpanel"
                            aria-labelledby="atendidos-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Motivo</th>
                                            <th>Nivel de Prioridad</th>
                                            <th>Fecha y Hora</th>
                                            <th>Atención</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($solicitudesAtendidos as $solicitud)
                                            <tr>
                                                <td>{{ $solicitud->id }}</td>
                                                <td>{{ $solicitud->motivosolicitud }}</td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                @if ($solicitud->nivelprioridad == 'alta') badge-danger 
                                                @elseif($solicitud->nivelprioridad == 'media') badge-warning 
                                                @else badge-success @endif">
                                                        {{ ucfirst($solicitud->nivelprioridad) }}
                                                    </span>
                                                </td>
                                                <td>{{ $solicitud->updated_at->format('Y-m-d H:i:s') }}</td>
                                                <td>
                                                    <a class="btn btn-link ver-atencion"
                                                        data-usuariosoporte="{{ $solicitud->usuariosoporte }}"
                                                        data-descripcionatendida="{{ $solicitud->descripcionatendida }}"
                                                        data-imagen1="{{ $solicitud->soporteimagen1 ? asset($solicitud->soporteimagen1) : '' }}"
                                                        data-imagen2="{{ $solicitud->soporteimagen2 ? asset($solicitud->soporteimagen2) : '' }}"
                                                        style="font-size: 1.2rem;">
                                                        <i class="fas fa-eye" style="color: #007bff; cursor:pointer;"></i>
                                                </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pendientes -->
                        <div class="tab-pane fade" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Motivo</th>
                                            <th>Nivel de Prioridad</th>
                                            <th>Imagen 1</th>
                                            <th>Imagen 2</th>
                                            <th>Fecha y Hora</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($solicitudesPendientes as $solicitud)
                                            <tr>
                                                <td>{{ $solicitud->id }}</td>
                                                <td>{{ $solicitud->motivosolicitud }}</td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                @if ($solicitud->nivelprioridad == 'alta') badge-danger 
                                                @elseif($solicitud->nivelprioridad == 'media') badge-warning 
                                                @else badge-success @endif">
                                                        {{ ucfirst($solicitud->nivelprioridad) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($solicitud->motivoimagen1)
                                                        <a href="#" class="mostrar-imagen"
                                                            data-imagen="{{ asset($solicitud->motivoimagen1) }}"
                                                            title="Ver Imagen 1">
                                                            <i class="fas fa-file-image"
                                                                style="font-size: 1.5rem; color: #007bff; cursor:pointer;"></i>
                                                        </a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($solicitud->motivoimagen2)
                                                        <a href="#" class="mostrar-imagen"
                                                            data-imagen="{{ asset($solicitud->motivoimagen2) }}"
                                                            title="Ver Imagen 2">
                                                            <i class="fas fa-file-image"
                                                                style="font-size: 1.5rem; color: #007bff; cursor:pointer;"></i>
                                                        </a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $solicitud->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay para atención -->
    <div id="overlay-atencion"
        style="
display:none;
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
background-color: rgba(0,0,0,0.7);
z-index: 9999; 
justify-content: center;
align-items: center;">
        <div id="overlay-atencion-content"
            style="
    position: relative; 
    max-width: 90vw; 
    max-height: 90vh; 
    background: #fff;
    border-radius: 5px;
    padding: 20px;
    overflow:auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    ">
            <button id="overlay-atencion-close"
                style="
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ccc;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 1.2rem;
        cursor: pointer;
    ">&times;</button>

            <h5><strong>Atendido por:</strong> <span id="atencion-usuariosoporte"></span></h5>
            <p style="text-align: center;"><strong>Descripción de la Atención:</strong><br>
                <span id="atencion-descripcion"></span>
            </p>

            <div id="atencion-imagenes"
                style="
        display: flex; 
        align-items: center; 
        justify-content: center; 
        flex-wrap: wrap; 
        gap: 10px; 
        margin-top: 20px;
        width: 100%;
        height: 100%;
        overflow: hidden;">
                <img id="atencion-imagen1" src="" alt="Soporte Imagen 1"
                    style="
            max-width: 90vw; 
            max-height: 90vh; 
            object-fit: contain; 
            border: 2px solid #ddd; 
            border-radius: 4px; 
            display: none;">
                <img id="atencion-imagen2" src="" alt="Soporte Imagen 2"
                    style="
            max-width: 45%; 
            max-height: 60vh; 
            object-fit: contain; 
            border: 2px solid #ddd; 
            border-radius: 4px; 
            display: none;">
                <span id="atencion-no-imagen"
                    style="display:none; font-size:1.2rem; color:#333; text-align:center;">N/A</span>
            </div>
        </div>
    </div>

    <div id="overlay-imagen"
        style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
    z-index: 9999; 
    justify-content: center;
    align-items: center;">
        <div id="overlay-content" style="position: relative; max-width: 90%; max-height: 90%;">
            <img id="overlay-image" src="" alt="Imagen"
                style="
         max-width: 90vw;
         max-height: 90vh;
         object-fit: contain;
         border: 2px solid #fff; 
         border-radius: 4px;
     ">
            <button id="overlay-close"
                style="
            position: absolute;
            top: 10px;
            right: 10px;
            background: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 1.2rem;
            cursor: pointer;
        ">&times;</button>
        </div>
    </div>

@stop

@section('js')
    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    preview.src = e.target.result;
                    preview.style.display = "block"; /* Mostrar la imagen */
                };
                reader.readAsDataURL(file);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Ajustar altura del textarea
            const examples = document.querySelector('.priority-examples');
            const motivoTextarea = document.getElementById('motivoSolicitud');
            if (examples && motivoTextarea) {
                motivoTextarea.style.height = `${examples.offsetHeight}px`;
            }

            // Overlay para imágenes (Pendientes)
            const enlacesImagen = document.querySelectorAll('.mostrar-imagen');
            const overlay = document.getElementById('overlay-imagen');
            const overlayImage = document.getElementById('overlay-image');
            const overlayClose = document.getElementById('overlay-close');

            enlacesImagen.forEach(enlace => {
                enlace.addEventListener('click', function(e) {
                    e.preventDefault();
                    const urlImagen = this.getAttribute('data-imagen');
                    if (urlImagen) {
                        overlayImage.src = urlImagen;
                        overlay.style.display = 'flex'; // Mostrar overlay
                    }
                });
            });

            overlayClose.addEventListener('click', function() {
                overlay.style.display = 'none';
                overlayImage.src = '';
            });

            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.style.display = 'none';
                    overlayImage.src = '';
                }
            });

            // Overlay de atención (Atendidos)
            const verAtencionButtons = document.querySelectorAll('.ver-atencion');
            const overlayAtencion = document.getElementById('overlay-atencion');
            const overlayAtencionClose = document.getElementById('overlay-atencion-close');

            const atencionUsuarioSoporte = document.getElementById('atencion-usuariosoporte');
            const atencionDescripcion = document.getElementById('atencion-descripcion');
            const atencionImagen1 = document.getElementById('atencion-imagen1');
            const atencionImagen2 = document.getElementById('atencion-imagen2');
            const atencionNoImagen = document.getElementById('atencion-no-imagen');
            const atencionImagenesContainer = document.getElementById('atencion-imagenes');

            verAtencionButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const soporte = this.getAttribute('data-usuariosoporte') || 'N/A';
                    const descripcion = this.getAttribute('data-descripcionatendida') || 'N/A';
                    const img1 = this.getAttribute('data-imagen1');
                    const img2 = this.getAttribute('data-imagen2');

                    // Reset de imágenes
                    atencionImagen1.style.display = 'none';
                    atencionImagen1.src = '';
                    atencionImagen1.style.maxWidth = '85vw';
                    atencionImagen1.style.maxHeight = '85vh';
                    atencionImagen2.style.display = 'none';
                    atencionImagen2.src = '';
                    atencionNoImagen.style.display = 'none';

                    // Configurar textos
                    atencionUsuarioSoporte.textContent = soporte;
                    atencionDescripcion.textContent = descripcion;

                    // Mostrar imágenes según corresponda
                    if (!img1 && !img2) {
                        atencionNoImagen.style.display = 'block';
                    } else if (img1 && !img2) {
                        atencionImagen1.src = img1;
                        atencionImagen1.style.display = 'block';
                        atencionImagen1.style.maxWidth =
                            '90vw'; // Maximiza la imagen a toda la pantalla
                        atencionImagen1.style.maxHeight = '60vh';
                        atencionImagenesContainer.style.justifyContent = 'center';
                    } else if (!img1 && img2) {
                        atencionImagen2.src = img2;
                        atencionImagen2.style.display = 'block';
                        atencionImagen2.style.maxWidth =
                            '90vw'; // Maximiza la imagen a toda la pantalla
                        atencionImagen2.style.maxHeight = '60vh';
                        atencionImagenesContainer.style.justifyContent = 'center';
                    } else {
                        atencionImagen1.src = img1;
                        atencionImagen1.style.display = 'block';
                        atencionImagen2.src = img2;
                        atencionImagen2.style.display = 'block';
                        atencionImagen1.style.maxWidth =
                            '45%'; // Reduce para mostrar las dos imágenes
                        atencionImagen1.style.maxHeight = '60vh';
                        atencionImagen2.style.maxWidth = '45%';
                        atencionImagen2.style.maxHeight = '60vh';
                        atencionImagenesContainer.style.justifyContent = 'space-between';
                    }

                    overlayAtencion.style.display = 'flex';
                });
            });

            overlayAtencionClose.addEventListener('click', function() {
                overlayAtencion.style.display = 'none';
            });

            overlayAtencion.addEventListener('click', function(e) {
                if (e.target === overlayAtencion) {
                    overlayAtencion.style.display = 'none';
                }
            });
        });
    </script>
@stop
