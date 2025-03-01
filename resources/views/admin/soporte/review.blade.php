@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    {{-- <a class="btn btn-sm float-right btn-regresar" href="#">REGRESAR</a> --}}
    <a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaSolicitudes">VER SOLICITUDES</a>
    <h1>REVISIÓN DE SOLICITUDES</h1>
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
        }

        .custom2-button:hover {
            background-color: #faa625;
            color: #ffffff;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .badge {
            font-size: 14px;
            padding: 5px 10px;
        }

        .badge-priority-baja {
            background-color: #28a745;
            color: #ffffff;
        }

        .badge-priority-media {
            background-color: #ffc107;
            color: #ffffff;
        }

        .badge-priority-alta {
            background-color: #dc3545;
            color: #ffffff;
        }

        .img-thumbnail {
            max-width: 100px;
            height: auto;
        }

        .img-preview {
            display: none;
            max-width: 100px;
            max-height: 100px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        .modal-lg {
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 15px;
        }
        .modal-xxl {
            max-width: 90%;
            width: 90%;
        }
        .img-preview {
            display: none;
            width: 50px;
            height: auto;
            margin-left: 10px;
            vertical-align: middle;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 2px;
        }

        .badge-priority-alta {
            background-color: #dc3545;
            color: #ffffff;
            animation: pulseRedShadow 2s infinite alternate;
        }

        @keyframes pulseRedShadow {
            0% {
                box-shadow: 0 0 0px #dc3545;
            }

            100% {
                box-shadow: 0 0 10px #dc3545;
            }
        }

        .fecha-nowrap {
            white-space: nowrap;
        }

        .usuario-nowrap {
            white-space: nowrap;
        }

        .prioridad-nowrap {
            white-space: nowrap;
        }

        .descripcionAtencion-nowrap {
            white-space: nowrap;
        }
    </style>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <h5><strong>Solicitudes Pendientes</strong></h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="usuario-nowrap">Solicitante</th>
                            <th>Motivo</th>
                            <th>Prioridad</th>
                            <th class="fecha-nowrap">Fecha</th>
                            <th>Acciones</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendientes as $index => $solicitud)
                            <tr class="{{ $solicitud->nivelprioridad == 'alta' ? 'priority-high' : '' }}">
                                <td>{{ $solicitud->id }}</td>
                                <td class="usuario-nowrap">{{ $solicitud->usuariosolicitante }}</td>
                                <td>{{ $solicitud->motivosolicitud }}</td>
                                <td>
                                    <span
                                        class="badge 
                                    @if ($solicitud->nivelprioridad == 'ALTA') badge-priority-alta
                                    @elseif($solicitud->nivelprioridad == 'MEDIA') badge-priority-media
                                    @else badge-priority-baja @endif">
                                        {{ ucfirst($solicitud->nivelprioridad) }}
                                    </span>
                                </td>
                                <td class="fecha-nowrap">{{ $solicitud->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a class="btn btn-outline-success btn-sm" data-toggle="modal"
                                        data-target="#modalAtender-{{ $solicitud->id }}">ATENDER</a>
                                </td>
                                <td>
                                    <button class="btn ver-detalles btn-outline-primary btn-sm" style="font-size: 1.2rem;"
                                        data-imagen1="{{ $solicitud->motivoimagen1 ? asset($solicitud->motivoimagen1) : '' }}"
                                        data-imagen2="{{ $solicitud->motivoimagen2 ? asset($solicitud->motivoimagen2) : '' }}">
                                        <i class="fas fa-eye" style="cursor:pointer;"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Atender -->
                            <div class="modal fade" id="modalAtender-{{ $solicitud->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="atenderModalLabel-{{ $solicitud->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.soporte.atender', $solicitud->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="atenderModalLabel-{{ $solicitud->id }}">Atender
                                                    Solicitud</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="form-group">
                                                    <label for="descripcionatendida-{{ $solicitud->id }}">Descripción de la
                                                        Atención:</label>
                                                    <textarea id="descripcionatendida-{{ $solicitud->id }}" name="descripcionatendida" class="form-control"
                                                        placeholder="" rows="3" required></textarea>
                                                </div>

                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label for="soporteimagen1-{{ $solicitud->id }}">Imagen
                                                                1:</label>
                                                            <input type="file" id="soporteimagen1-{{ $solicitud->id }}"
                                                                name="soporteimagen1" class="form-control" accept="image/*"
                                                                required
                                                                onchange="previewImage(this, 'previewImagen1-{{ $solicitud->id }}')">
                                                            <img id="previewImagen1-{{ $solicitud->id }}"
                                                                class="img-preview" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label for="soporteimagen2-{{ $solicitud->id }}">Imagen 2
                                                                (Opcional)
                                                                :</label>
                                                            <input type="file" id="soporteimagen2-{{ $solicitud->id }}"
                                                                name="soporteimagen2" class="form-control" accept="image/*"
                                                                onchange="previewImage(this, 'previewImagen2-{{ $solicitud->id }}')">
                                                            <img id="previewImagen2-{{ $solicitud->id }}"
                                                                class="img-preview" />
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-outline-success">Guardar Atención</button>
                                                <button type="button" class="btn btn-outline-danger"
                                                    data-dismiss="modal">Cancelar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Solicitudes (Atendidos y Pendientes) -->
    <div class="modal fade" id="ventanaSolicitudes" tabindex="-1" role="dialog" aria-labelledby="solicitudesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xxl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="solicitudesModalLabel">SOLICITUDES</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Pestañas -->
                    <ul class="nav nav-tabs" id="tabSolicitudes" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pendientes-tab" data-toggle="tab" href="#pendientes" role="tab"
                                aria-controls="pendientes" aria-selected="false">PENDIENTES</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="atendidos-tab" data-toggle="tab" href="#atendidos"
                                role="tab" aria-controls="atendidos" aria-selected="true">ATENDIDOS</a>
                        </li>
                        
                    </ul>

                    <!-- Contenido de las pestañas -->
                    <div class="tab-content" id="tabSolicitudesContent">
                        <!-- Atendidos -->
                        <div class="tab-pane fade" id="atendidos" role="tabpanel"
                            aria-labelledby="atendidos-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Solicitante</th>
                                            <th>Desc. de la Atención</th>
                                            <th>Prioridad</th>
                                            <th>Fecha Atención</th>
                                            <th>Hora Atención</th>
                                            <th>Atendió</th>
                                            <th>Detalles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($atendidos as $index => $solicitud)
                                            <tr>
                                                <td>{{ $solicitud->id }}</td>
                                                <td>{{ $solicitud->usuariosolicitante }}</td>
                                                <td>{{ $solicitud->descripcionatendida }}</td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                    @if ($solicitud->nivelprioridad == 'ALTA') badge-priority-alta
                                                    @elseif($solicitud->nivelprioridad == 'MEDIA') badge-priority-media
                                                    @else badge-priority-baja @endif">
                                                        {{ ucfirst($solicitud->nivelprioridad) }}
                                                    </span>
                                                </td>
                                                <td>{{ $solicitud->updated_at->format('Y-m-d') }}</td>
                                                <td>{{ $solicitud->updated_at->format('H:i') }}</td>
                                                <td>{{ $solicitud->usuariosoporte }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-primary ver-detalles"
                                                        data-imagen1="{{ $solicitud->soporteimagen1 ? asset($solicitud->soporteimagen1) : '' }}"
                                                        data-imagen2="{{ $solicitud->soporteimagen2 ? asset($solicitud->soporteimagen2) : '' }}">
                                                        <i class="fas fa-eye" style="cursor:pointer;"></i>
                                                </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pendientes -->
                        <div class="tab-pane fade show active" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Solicitante</th>
                                            <th>Motivo</th>
                                            <th class="prioridad-nowrap">Prioridad</th>
                                            <th>Fecha</th>
                                            <th>Detalles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendientes as $index => $solicitud)
                                            <tr>
                                                <td>{{ $solicitud->id }}</td>
                                                <td class="usuario-nowrap">{{ $solicitud->usuariosolicitante }}</td>
                                                <td>{{ $solicitud->motivosolicitud }}</td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                    @if ($solicitud->nivelprioridad == 'ALTA') badge-priority-alta
                                                    @elseif($solicitud->nivelprioridad == 'MEDIA') badge-priority-media
                                                    @else badge-priority-baja @endif">
                                                        {{ ucfirst($solicitud->nivelprioridad) }}
                                                    </span>
                                                </td>
                                                <td class="fecha-nowrap">{{ $solicitud->created_at->format('Y-m-d') }}
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-primary ver-detalles"
                                                        data-imagen1="{{ $solicitud->motivoimagen1 ? asset($solicitud->motivoimagen1) : '' }}"
                                                        data-imagen2="{{ $solicitud->motivoimagen2 ? asset($solicitud->motivoimagen2) : '' }}">
                                                        <i class="fas fa-eye" style="cursor:pointer;"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div id="overlay-imagen"
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

        <div id="overlay-content"
            style="
            position: relative; 
            max-width: 90vw; 
            max-height: 90vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        ">
            <img id="overlay-image1" src="" alt="Imagen 1"
                style="
                max-width:45vw;
                max-height:90vh;
                object-fit:contain;
                border:2px solid #fff; 
                border-radius:4px;
                margin: 0 10px;
                display:none;
            ">
            <img id="overlay-image2" src="" alt="Imagen 2"
                style="
                max-width:45vw;
                max-height:90vh;
                object-fit:contain;
                border:2px solid #fff; 
                border-radius:4px;
                margin: 0 10px;
                display:none;
            ">
            <span id="overlay-no-image"
                style="
            color:#fff; 
            font-size:1.5rem; 
            display:none;
            ">N/A</span>

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
                    preview.src = e.target.result; // Muestra la imagen cargada
                    preview.style.display = "block"; // Hace visible la miniatura
                };
                reader.readAsDataURL(file); // Lee el archivo de imagen
            } else {
                const preview = document.getElementById(previewId);
                preview.src = ""; // Limpia la imagen previa si no hay archivo
                preview.style.display = "none"; // Oculta la miniatura
            }
        }

        $(document).ready(function() {
            const verDetallesButtons = document.querySelectorAll('.ver-detalles');
            const overlay = document.getElementById('overlay-imagen');
            const overlayImage1 = document.getElementById('overlay-image1');
            const overlayImage2 = document.getElementById('overlay-image2');
            const overlayNoImage = document.getElementById('overlay-no-image');
            const overlayClose = document.getElementById('overlay-close');

            verDetallesButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const img1 = this.getAttribute('data-imagen1');
                    const img2 = this.getAttribute('data-imagen2');

                    // Reset
                    overlayImage1.style.display = 'none';
                    overlayImage1.src = '';
                    overlayImage2.style.display = 'none';
                    overlayImage2.src = '';
                    overlayNoImage.style.display = 'none';

                    if (!img1 && !img2) {
                        overlayNoImage.style.display = 'block';
                    } else if (img1 && !img2) {
                        overlayImage1.src = img1;
                        overlayImage1.style.display = 'block';
                    } else if (!img1 && img2) {
                        overlayImage2.src = img2;
                        overlayImage2.style.display = 'block';
                    } else {
                        overlayImage1.src = img1;
                        overlayImage1.style.display = 'block';
                        overlayImage2.src = img2;
                        overlayImage2.style.display = 'block';
                    }

                    overlay.style.display = 'flex';
                });
            });

            overlayClose.addEventListener('click', function() {
                overlay.style.display = 'none';
            });

            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.style.display = 'none';
                }
            });

            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);
        });
        </script>
@stop
