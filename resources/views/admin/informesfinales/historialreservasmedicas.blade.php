@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-secondary" href="{{ route('admin.informesfinales.reservasmedicas') }}">REGRESAR</a>
<h1>HISTORIAL RESERVAS MÉDICAS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/reservasmedicas.css') }}">
<style>
    .table td {
        padding: 5px 10px;;
    }
    .btn.disabled {
        pointer-events: none;
        background-color: #d6d6d6;
        color: #a5a5a5;
        border-color: #d6d6d6;
    }
    .btn.disabled i {
        color: #a5a5a5;
    }
    .dropdown-menu {
        min-width: 220px;
    }

    .dropdown-item {
        white-space: nowrap;
    }
    #acciones-checkboxes label {
        font-weight: normal !important;
    }
    .normal-label {
        font-weight: normal !important;
    }
    #acciones-checkboxes h4 {
        font-weight: bold;
        margin-bottom: 10px;
    }
    #acciones-checkboxes3 label {
        font-weight: normal !important;
    }
    #acciones-checkboxes3 h4 {
        font-weight: bold;
        margin-bottom: 10px;
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
        }, 5000);
    </script>
@endif
<div class="card">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid justify-content-end">
            <div class="d-flex flex-wrap align-items-center">
                <form id="search-form" action="{{ route('buscarhistorialreservamedicas') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="NOMBRE DEL CLIENTE">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar" type="submit">BUSCAR</button>
                    <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" name="buscartodo" type="submit" value="1">MOSTRAR TODO</button>
                </form>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarhistorialreservamedicas') }}";
            });
        });
    </script>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th>Tipo_Cli.</th>
                        <th>ID_Cli.</th>
                        <th>Cliente</th>
                        <th hidden>Fecha_Bateria</th>
                        @if ($rolusuario !== 'PROVEEDOR')
                        <th>Proveedor_Atención</th>
                        @endif
                        <th>Estudio/Especialidad</th>
                        <th>Fecha_Atención</th>
                        <th>Hora_Atención</th>
                        <th>Fecha_Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservasmedicas as $reservasmedica)
                        @if($reservasmedica->documentacionDisponible)
                            <tr>
                                <td>ITA</td>
                                <td>{{$reservasmedica->clienteitaid}}</td>
                                <td>{{$reservasmedica->clienteitanombre}}</td>
                                <td hidden>{{$reservasmedica->fechabateria}}</td>
                                @if ($rolusuario !== 'PROVEEDOR')
                                <td>{{$reservasmedica->proveedornombre}}</td>
                                @endif
                                <td>{{$reservasmedica->accionnombre}}</td>
                                <td>{{$reservasmedica->fechaasignada}}</td>
                                <td>{{ \Carbon\Carbon::parse($reservasmedica->horadesde)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasmedica->horahasta)->format('H:i') }}</td>
                                <td>{{$reservasmedica->fechainforme}}</td>

                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                            <i class="fas fa-folder-open"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">

                                            {{-- INFORME MEDICO --}}
                                            <a class="dropdown-item" target="_blank"
                                            href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->documentacionDisponible) }}">
                                                <i class="fas fa-file-alt mr-2"></i> Ver Informe Médico
                                            </a>

                                            {{-- INFORME FIRMADO --}}
                                            @if ($reservasmedica->documentacionfirmadaDisponible)
                                                <a class="dropdown-item" target="_blank"
                                                href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->documentacionfirmadaDisponible) }}">
                                                    <i class="fas fa-file mr-2"></i> Ver Informe Firmado
                                                </a>
                                            @endif

                                            {{-- WORD --}}
                                            @if($reservasmedica->documentacionworditaDisponible)
                                                <a class="dropdown-item" target="_blank"
                                                href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->documentacionworditaDisponible) }}">
                                                    <i class="fas fa-file-word mr-2"></i> Descargar Word
                                                </a>
                                            @endif

                                            {{-- IMAGEN 1 --}}
                                            @if($reservasmedica->imagen1Disponible)
                                                <a class="dropdown-item" target="_blank"
                                                href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->imagen1Disponible) }}">
                                                    <i class="fas fa-images mr-2"></i> Ver Imagen 1
                                                </a>
                                            @endif

                                            {{-- IMAGEN 2 --}}
                                            @if($reservasmedica->imagen2Disponible)
                                                <a class="dropdown-item" target="_blank"
                                                href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->imagen2Disponible) }}">
                                                    <i class="far fa-images mr-2"></i> Ver Imagen 2
                                                </a>
                                            @endif
                                            
                                            {{-- CREAR BATERIA --}}
                                            @if (
                                                $nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' ||
                                                $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' ||
                                                $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' ||
                                                $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR' ||
                                                $nombreusuario === 'YELKA MORALES VELARDE'
                                            )
                                                <a class="dropdown-item"
                                                href="{{ route('admin.asociados.crearbateriaclienteita', $reservasmedica->clienteitaid) }}">
                                                    <i class="fas fa-charging-station mr-2"></i> Crear Batería
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                        @if($reservasmedicaauditoria->documentacionDisponibleauditoria)
                            <tr>
                                <td>AUDITORIA</td>
                                <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                <td hidden>{{$reservasmedicaauditoria->fechabateria}}</td>
                                @if ($rolusuario !== 'PROVEEDOR')
                                <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                @endif
                                <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                <td>{{$reservasmedicaauditoria->fechaasignada}}</td>
                                <td>{{ \Carbon\Carbon::parse($reservasmedicaauditoria->horadesde)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservasmedicaauditoria->horahasta)->format('H:i') }}</td>
                                <td>{{$reservasmedicaauditoria->fechainformeauditoria}}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown">
                                            <i class="fas fa-folder-open"></i>
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-right">

                                            {{-- INFORME MEDICO --}}
                                            <a class="dropdown-item" target="_blank"
                                            href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->documentacionDisponibleauditoria) }}">
                                                <i class="fas fa-file-alt mr-2"></i> Ver Informe Médico
                                            </a>

                                            {{-- INFORME FIRMADO --}}
                                            @if ($reservasmedicaauditoria->documentacionfirmadaauditoriaDisponible)
                                                <a class="dropdown-item" target="_blank"
                                                href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->documentacionfirmadaauditoriaDisponible) }}">
                                                    <i class="fas fa-file mr-2"></i> Ver Informe Firmado
                                                </a>
                                            @endif

                                            {{-- WORD --}}
                                            @if($reservasmedicaauditoria->documentacionwordauditoriaDisponible)
                                                <a class="dropdown-item" target="_blank"
                                                href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->documentacionwordauditoriaDisponible) }}">
                                                    <i class="fas fa-file-word mr-2"></i> Descargar Word
                                                </a>
                                            @endif

                                            {{-- IMAGEN 1 --}}
                                            @if($reservasmedicaauditoria->imagen1Disponibleauditoria)
                                                <a class="dropdown-item" target="_blank"
                                                href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->imagen1Disponibleauditoria) }}">
                                                    <i class="fas fa-images mr-2"></i> Ver Imagen 1
                                                </a>
                                            @endif

                                            {{-- IMAGEN 2 --}}
                                            @if($reservasmedicaauditoria->imagen2Disponibleauditoria)
                                                <a class="dropdown-item" target="_blank"
                                                href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->imagen2Disponibleauditoria) }}">
                                                    <i class="far fa-images mr-2"></i> Ver Imagen 2
                                                </a>
                                            @endif

                                            {{-- CREAR BATERIA --}}
                                            @if (
                                                $nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' ||
                                                $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' ||
                                                $nombreusuario === 'YELKA MORALES VELARDE'
                                            )
                                                <a class="dropdown-item"
                                                href="{{ route('admin.asociados.crearbateriaclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid) }}">
                                                    <i class="fas fa-charging-station mr-2"></i> Crear Batería
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

<script>
    $(document).ready(function() {
        $('.dropify').dropify({
            messages: {
                'default': 'Arrastre y suelte un archivo o haga clic aquí',
                'replace': 'Arrastre y suelte o haga clic para reemplazar',
                'remove': 'Eliminar',
                'error': 'Ooops, algo salió mal.'
            }
        });
    
        $('.dropify').on('dropify.error.fileSize', function(event, element) {
            var maxSize = element.input.files[0].size / (1024 * 1024);
            var errorMessage = 'El archivo es demasiado grande (' + maxSize.toFixed(2) + ' MB máx.).';
            $(element.input).siblings('.dropify-error').text(errorMessage);
        });
    });

    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });

    document.getElementById('archivo').addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var fileURL = URL.createObjectURL(file);
            var previewCard = document.getElementById('preview-card');
            var documentPreview = document.getElementById('document-preview');
    
            previewCard.style.display = 'block';
            documentPreview.src = fileURL;
        } else {
            var previewCard = document.getElementById('preview-card');
            previewCard.style.display = 'none';
            documentPreview.src = '';
        }
    });

    //CANCELAR FUNCION DE LA TECLA ENTER
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('eliminar')=='ok')
<script>
    Swal.fire(
    '¡Eliminado!',
    'El rol se eliminó con éxito',
    'success')
</script>
@endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "Este perfil se eliminará definitivamente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Si, eliminar!',
        cancelButtonText: 'Cancelar'
        }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
        }) 
    });
    $(document).ready(function() {
        $('input[name="buscarpor"]').on('keyup', function() {
            var query = $(this).val();
            var botonBuscar = $('#btn-buscar');
            if (query.trim() === '') {
                botonBuscar.prop('disabled', true);
            } else {
                botonBuscar.prop('disabled', false);
            }
        });
    });
</script>
    
@endsection
