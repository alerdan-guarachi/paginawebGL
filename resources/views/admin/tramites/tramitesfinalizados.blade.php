@extends('adminlte::page')

@section('content_header')
@can('admin.tramites.interrumpirtramite')
<a class="btn btn-sm float-right btn-tramitesfinalizados" href="{{ route('admin.tramites.index') }}">
    REGRESAR
</a>
@endcan
<div class="modal fade" id="modalCambioApoderado" tabindex="-1" aria-labelledby="modalCambioApoderadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCambioApoderadoLabel">FINALIZAR O INTERRUMPIR TRÁMITE</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label>Buscar</label>
                <input type="text" id="buscarTramiteId" class="form-control mb-2 form-control-sm" placeholder="INGRESE EL ID DEL TRÁMITE...">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped" id="resultadoTramite">
                        <thead class="table-secondaty">
                            <tr>
                                <th>ID</th>
                                <th>Trámite</th>
                                <th>Cliente_ID</th>
                                <th>Cliente_Nombre</th>
                                <th>Apoderado_Asignado</th>
                                <th>Fecha_Asignación</th>
                                <th>Ciudad</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <form id="formCambioApoderado" method="POST" action="{{ route('tramites.interrumpirtramite') }}">
                    @csrf
                    <input type="hidden" name="tramiteid" id="tramiteid">
                    <input type="hidden" name="clienteid" id="clienteid">
                    <input type="hidden" name="clientenombre" id="clientenombre">
                    <input type="hidden" name="apoderadoanterior" id="apoderadoanterior">
                    <input type="hidden" name="fechaasignacionanterior" id="fechaasignacionanterior">
                    <div class="mb-2">
                        <label for="estadointerrupcion">Estado</label>
                        <select class="form-control" name="estadointerrupcion">
                            <option value="">Selecciona una opción...</option>
                            <option value="FINALIZADO">FINALIZAR</option>
                            <option value="INTERRUMPIDO">INTERRUMPIR</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="motivocambio">Motivo de la Finalización/Interrupción</label>
                        <textarea name="motivocambio" id="motivocambio" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-subirarchivos">GUARDAR</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buscarInput = document.getElementById('buscarTramiteId');
        const tabla = document.getElementById('resultadoTramite').querySelector('tbody');
        buscarInput.addEventListener('keyup', function(e) {
        if(e.key !== 'Enter') return;
            const id = this.value.trim();
            if(!id) return tabla.innerHTML = '';
            fetch("{{ url('tramites/buscarpendiente') }}/" + id)
            .then(res => res.json())
            .then(data => {
                tabla.innerHTML = '';
                if(!data) return;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${data.id}</td>
                    <td>${data.tramite}</td>
                    <td>${data.clienteitaid}</td>
                    <td>${data.clienteitanombre}</td>
                    <td>${data.apoderadoasignado}</td>
                    <td>${data.fechaasignacion}</td>
                    <td>${data.ciudad}</td>
                `;
                tabla.appendChild(row);
                document.getElementById('tramiteid').value = data.id;
                document.getElementById('clienteid').value = data.clienteitaid;
                document.getElementById('clientenombre').value = data.clienteitanombre;
                document.getElementById('apoderadoanterior').value = data.apoderadoasignado;
                document.getElementById('fechaasignacionanterior').value = data.fechaasignacion;
            });
        });
    });
</script>
<h1>TRÁMITES FINALIZADOS E INTERRUMPIDOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
<style>
    #tablaTramites2 thead th {
        position: sticky;
        top: 0;
        background-color: #f8fdf2;
        z-index: 10;
    }
    #tablaTramites thead th {
        position: sticky;
        top: 0;
        background-color: #f8fdf2;
        z-index: 10;
    }
    #tablaProgramaciones thead th {
        position: sticky;
        top: 0;
        background-color: #f8fdf2;
        z-index: 10;
    }
    #tablaTramites3 thead th {
        position: sticky;
        top: 0;
        background-color: #f8fdf2;
        z-index: 10;
    }
    #tablaTramites4 thead th {
        position: sticky;
        top: 0;
        background-color: #f8fdf2;
        z-index: 10;
    }
    .table-responsive {
        max-height: 65vh;
        overflow-y: auto;
    }
    .btn-vertramite {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-vertramite:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verhistoria {
        background-color: #ffffff;
        color: #226acf;
        border-color: #226acf;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-verhistoria:hover {
        background-color: #226acf;
        color: #ffffff;
    }
    .btn-verrequisito {
        background-color: #ffffff;
        color: #8c28f0;
        border-color: #8c28f0;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-verrequisito:hover {
        background-color: #8c28f0;
        color: #ffffff;
    }
    .btn-procedimientos {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-procedimientos:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-tramitesfinalizados {
        background-color: #ffffff;
        color: #1532c2;
        border-color: #1532c2;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-tramitesfinalizados:hover {
        background-color: #1532c2;
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
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="false">
                        FINALIZADOS
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-8" data-toggle="tab" href="#tab-content-8" role="tab" aria-controls="tab-content-8" aria-selected="false">
                        INTERRUMPIDOS
                    </a>
                </li>
            </ul>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll('a[data-toggle="tab"]').forEach(tab => {
                    tab.addEventListener('click', function () {
                        localStorage.setItem('pestana_activa', this.getAttribute('href'));
                    });
                });
                let pestana = localStorage.getItem('pestana_activa');
                if (pestana) {
                    const tabElement = document.querySelector(`a[href="${pestana}"]`);
                    if (tabElement) {
                        new bootstrap.Tab(tabElement).show();
                    }
                }
            });
        </script>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                {{-- FINALIZADOS --}}
                <div class="tab-pane fade show active" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                    <div class="d-flex align-items-center mb-3">
                        <input 
                            type="text" 
                            id="buscadorTramites3" 
                            class="form-control form-control-sm w-25 shadow-sm" 
                            placeholder="BUSCAR POR ID O NOMBRE DEL CLIENTE..."
                        >
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm" id="tablaTramites3">
                            <thead style="background-color: #f8fdf2">
                                <tr>
                                    <th class="text-center">Ver</th>
                                    <th>ID_Trámite</th>
                                    <th>Trámite</th>
                                    <th>Cliente_ID</th>
                                    <th>Cliente_Nombre</th>
                                    <th>Fecha_Bateria</th>
                                    <th>Apoderado_Asignado</th>
                                    <th>Fecha_Hora_Asig.</th>
                                    <th>Inicio_Trámite</th>
                                    <th>Finalización_Trámite</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($todostramitesfinalizados as $tramite)
                                    <tr>
                                        @php
                                            $rutasTramites = [
                                                'INVALIDEZ' => 'admin.tramites.procinvalidez',
                                                'APELACIÓN' => 'admin.tramites.procapelacion',
                                                'SEGUNDA SOLICITUD' => 'admin.tramites.procsegundasolicitud',
                                                'APELACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelsegsolicitud',
                                                'TERCERA SOLICITUD' => 'admin.tramites.proctercerasolicitud',
                                                'APELACIÓN TERCERA SOLICITUD' => 'admin.tramites.procapeltercersolicitud',
                                                'RECALIFICACIÓN' => 'admin.tramites.procrecalificacion',
                                                'APELACIÓN DE RECALIFICACIÓN' => 'admin.tramites.procapelrecalificacion',
                                                'RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procrecalsegsolicitud',
                                                'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelrecalsegsolicitud',
                                                'JUBILACIÓN' => 'admin.tramites.procjubilacion',
                                                'PENSIÓN POR MUERTE' => 'admin.tramites.procpensionmuerte',
                                                'RETIRO DE APORTES TOTAL' => 'admin.tramites.procretiroaportestotal',
                                                'RETIRO DE APORTES PARCIAL' => 'admin.tramites.procretiroaportesparcial',
                                                'MASA HEREDITARIA' => 'admin.tramites.procmasahereditaria',
                                                'COMPENSACIÓN DE COTIZACIONES (SENASIR)' => 'admin.tramites.proccompensacionsenasir',
                                                'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES' => 'admin.tramites.procpensionderivretiro',
                                            ];
                                        @endphp
                                        <td class="text-center">
                                            @if ($tramite->archivofinalizado == null)
                                                @if(isset($rutasTramites[$tramite->tramite]))
                                                    <a class="btn btn-sm fas fa-file-archive btn-editar" title="VER TRÁMITE"
                                                    href="{{ route($rutasTramites[$tramite->tramite], ['cliente' => $tramite->clienteitaid]) }}">
                                                    </a>
                                                @endif
                                            @else
                                                @if ($tramite->archivofinalizado)
                                                        <a href="{{ $tramite->archivofinalizado }}" 
                                                        target="_blank" 
                                                        class="btn btn-vertramite btn-sm"
                                                        title="VER TRÁMITE">
                                                        <i class="fas fa-file-alt"></i>
                                                        </a>
                                                    @if ($tramite->historiafinalizado)
                                                        <a href="{{ $tramite->historiafinalizado }}" 
                                                        target="_blank" 
                                                        class="btn btn-verhistoria btn-sm"
                                                        title="VER HISTORIA">
                                                        <i class="fas fa-notes-medical"></i>
                                                        </a>
                                                    @endif
                                                    @if ($tramite->requisitofinalizado)
                                                        <a href="{{ $tramite->requisitofinalizado }}" 
                                                        target="_blank" 
                                                        class="btn btn-verrequisito btn-sm"
                                                        title="VER REQUISITOS">
                                                        <i class="fas fa-user"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $tramite->id }}</td>
                                        <td>{{ $tramite->tramite }}</td>
                                        <td>{{ $tramite->clienteitaid }}</td>
                                        <td>{{ $tramite->clienteitanombre }}</td>
                                        <td>
                                            @if(is_null($tramite->fechabateria))
                                                @if(in_array($tramite->tramite, [
                                                    'INVALIDEZ',
                                                    'APELACIÓN',
                                                    'SEGUNDA SOLICITUD',
                                                    'APELACIÓN SEGUNDA SOLICITUD',
                                                    'TERCERA SOLICITUD',
                                                    'APELACIÓN TERCERA SOLICITUD',
                                                    'RECALIFICACIÓN',
                                                    'APELACIÓN DE RECALIFICACIÓN',
                                                    'RECALIFICACIÓN SEGUNDA SOLICITUD',
                                                    'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD']))
                                                    PENDIENTE
                                                @else
                                                    NO REQUIERE
                                                @endif
                                            @else
                                                {{ \Carbon\Carbon::parse($tramite->fechabateria)->format('d-m-Y') }}
                                            @endif
                                        </td>
                                        <td>{{ $tramite->apoderadoasignado }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tramite->fechaasignacion)->format('d-m-Y / H:i') }}</td>
                                        <td>
                                            @if($tramite->fechainicio)
                                                {{ \Carbon\Carbon::parse($tramite->fechainicio)->format('d-m-Y') }}
                                            @else
                                                {{ optional($tramite->procedimientos->first())->created_at?->format('d-m-Y') ?? '---' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($tramite->fechafinalizacion)
                                                {{ \Carbon\Carbon::parse($tramite->fechafinalizacion)->format('d-m-Y') }}
                                            @else
                                                {{ optional($tramite->procedimientos->last())->created_at?->format('d-m-Y') ?? '---' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const buscador = document.getElementById('buscadorTramites3');
                            const tabla = document.getElementById('tablaTramites3');
                            const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                            buscador.addEventListener('keyup', function() {
                                const filtro = this.value.toLowerCase();
                                Array.from(filas).forEach(function(fila) {
                                    const clienteId = fila.cells[3].textContent.toLowerCase();
                                    const clienteNombre = fila.cells[4].textContent.toLowerCase();
                                    if (clienteId.includes(filtro) || clienteNombre.includes(filtro)) {
                                        fila.style.display = '';
                                    } else {
                                        fila.style.display = 'none';
                                    }
                                });
                            });
                        });
                    </script>
                </div>

                {{-- INTERRUMPIDOS --}}
                <div class="tab-pane fade" id="tab-content-8" role="tabpanel" aria-labelledby="tab-8">
                    <div class="d-flex align-items-center mb-3">
                        <input 
                            type="text" 
                            id="buscadorTramites4" 
                            class="form-control form-control-sm w-25 shadow-sm" 
                            placeholder="BUSCAR POR ID O NOMBRE DEL CLIENTE..."
                        >
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm" id="tablaTramites4">
                            <thead style="background-color: #f8fdf2">
                                <tr>
                                    <th class="text-center">Ver</th>
                                    <th>ID_Trámite</th>
                                    <th>Trámite</th>
                                    <th>Cliente_ID</th>
                                    <th>Cliente_Nombre</th>
                                    <th>Fecha_Bateria</th>
                                    <th>Apoderado_Asignado</th>
                                    {{-- <th>Fecha_Hora_Asig.</th>
                                    <th>Inicio_Trámite</th>
                                    <th>Interrup_Trámite</th> --}}
                                    <th>Interrup_Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($todostramitesinterrumpidos as $tramite)
                                    <tr>
                                        @php
                                            $rutasTramites = [
                                                'INVALIDEZ' => 'admin.tramites.procinvalidez',
                                                'APELACIÓN' => 'admin.tramites.procapelacion',
                                                'SEGUNDA SOLICITUD' => 'admin.tramites.procsegundasolicitud',
                                                'APELACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelsegsolicitud',
                                                'TERCERA SOLICITUD' => 'admin.tramites.proctercerasolicitud',
                                                'APELACIÓN TERCERA SOLICITUD' => 'admin.tramites.procapeltercersolicitud',
                                                'RECALIFICACIÓN' => 'admin.tramites.procrecalificacion',
                                                'APELACIÓN DE RECALIFICACIÓN' => 'admin.tramites.procapelrecalificacion',
                                                'RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procrecalsegsolicitud',
                                                'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD' => 'admin.tramites.procapelrecalsegsolicitud',
                                                'JUBILACIÓN' => 'admin.tramites.procjubilacion',
                                                'PENSIÓN POR MUERTE' => 'admin.tramites.procpensionmuerte',
                                                'RETIRO DE APORTES TOTAL' => 'admin.tramites.procretiroaportestotal',
                                                'RETIRO DE APORTES PARCIAL' => 'admin.tramites.procretiroaportesparcial',
                                                'MASA HEREDITARIA' => 'admin.tramites.procmasahereditaria',
                                                'COMPENSACIÓN DE COTIZACIONES (SENASIR)' => 'admin.tramites.proccompensacionsenasir',
                                                'PENSIÓN POR MUERTE CON DERIVACIÓN A RETIRO DE APORTES' => 'admin.tramites.procpensionderivretiro',
                                            ];
                                        @endphp
                                        <td class="text-center">
                                            @php
                                                $fechaInicio = optional($tramite->procedimientos->first())->created_at;
                                            @endphp

                                            @if ($fechaInicio)
                                                @if (isset($rutasTramites[$tramite->tramite]))
                                                    <a class="btn btn-sm fas fa-file-archive btn-editar"
                                                    title="VER TRÁMITE"
                                                    href="{{ route($rutasTramites[$tramite->tramite], ['cliente' => $tramite->clienteitaid]) }}">
                                                    </a>
                                                @endif
                                            @else
                                                <span class="badge badge-warning">NO INICIADO</span>
                                            @endif
                                        </td>
                                        <td>{{ $tramite->id }}</td>
                                        <td>{{ $tramite->tramite }}</td>
                                        <td>{{ $tramite->clienteitaid }}</td>
                                        <td>{{ $tramite->clienteitanombre }}</td>
                                        <td>
                                            @if(is_null($tramite->fechabateria))
                                                @if(in_array($tramite->tramite, [
                                                    'INVALIDEZ',
                                                    'APELACIÓN',
                                                    'SEGUNDA SOLICITUD',
                                                    'APELACIÓN SEGUNDA SOLICITUD',
                                                    'TERCERA SOLICITUD',
                                                    'APELACIÓN TERCERA SOLICITUD',
                                                    'RECALIFICACIÓN',
                                                    'APELACIÓN DE RECALIFICACIÓN',
                                                    'RECALIFICACIÓN SEGUNDA SOLICITUD',
                                                    'APELACIÓN DE RECALIFICACIÓN SEGUNDA SOLICITUD']))
                                                    PENDIENTE
                                                @else
                                                    NO REQUIERE
                                                @endif
                                            @else
                                                {{ \Carbon\Carbon::parse($tramite->fechabateria)->format('d-m-Y') }}
                                            @endif
                                        </td>
                                        <td>{{ $tramite->apoderadoasignado }}</td>
                                        {{-- <td>{{ \Carbon\Carbon::parse($tramite->fechaasignacion)->format('d-m-Y / H:i') }}</td>
                                        <td>
                                            {{ optional($tramite->procedimientos->first())->created_at?->format('d-m-Y') ?? '---' }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($tramite->fechafinalizacion)->format('d-m-Y') }}</td> --}}
                                        <td>{{ $tramite->motivointerrupcion }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const buscador = document.getElementById('buscadorTramites4');
                            const tabla = document.getElementById('tablaTramites4');
                            const filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                            buscador.addEventListener('keyup', function() {
                                const filtro = this.value.toLowerCase();
                                Array.from(filas).forEach(function(fila) {
                                    const clienteId = fila.cells[3].textContent.toLowerCase();
                                    const clienteNombre = fila.cells[4].textContent.toLowerCase();
                                    if (clienteId.includes(filtro) || clienteNombre.includes(filtro)) {
                                        fila.style.display = '';
                                    } else {
                                        fila.style.display = 'none';
                                    }
                                });
                            });
                        });
                    </script>
                </div>
            </div>
        </div> 
    </div>
@stop

@section('js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
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
    $(document).ready(function() {
        $('input[name="buscarporfecha"], input[name="buscarporarea"]').on('keyup change', function() {
            var fechaSeleccionada = $('input[name="buscarporfecha"]').val();
            var areaSeleccionada = $('input[name="buscarporarea"]').val();
            var botonBuscar = $('#btn-buscar');
            
            if (fechaSeleccionada.trim() === '' && areaSeleccionada.trim() === '') {
                botonBuscar.prop('disabled', true);
            } else {
                botonBuscar.prop('disabled', false);
            }
        });
    });
</script>

<script>
    function cargarVistaPrevia() {
        var document = document.getElementById('document').files[0];
        if (document) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var previewIframe = document.getElementById('document-preview');
            previewIframe.src = e.target.result;
        };
        reader.readAsDataURL(document);
        }
    }
    document.getElementById('document').addEventListener('change', function() {
        cargarVistaPrevia();
    });
</script>

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

    document.getElementById('document').addEventListener('change', function(event) {
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
</script>

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El rol se eliminará definitivamente",
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
</script>
@endsection
