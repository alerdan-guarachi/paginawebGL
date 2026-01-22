@extends('adminlte::page')

@section('content_header')
@can('admin.tramites.cambiarapoderadotramite')
<button type="button" class="btn btn-sm float-right btn-subirarchivos" data-toggle="modal" data-target="#modalCambioApoderado">
    CAMBIAR APODERADO
</button>
@endcan
<div class="modal fade" id="modalCambioApoderado" tabindex="-1" aria-labelledby="modalCambioApoderadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCambioApoderadoLabel">CAMBIAR APODERADO</h5>
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
                <form id="formCambioApoderado" method="POST" action="{{ route('tramites.cambiarapoderado') }}">
                    @csrf
                    <input type="hidden" name="tramiteid" id="tramiteid">
                    <input type="hidden" name="clienteid" id="clienteid">
                    <input type="hidden" name="clientenombre" id="clientenombre">
                    <input type="hidden" name="apoderadoanterior" id="apoderadoanterior">
                    <input type="hidden" name="fechaasignacionanterior" id="fechaasignacionanterior">
                    <div class="mb-2">
                        <label for="apoderadoactual">Nuevo Apoderado</label>
                        <select name="apoderadoactual" id="apoderadoactual" class="form-control" required></select>
                    </div>
                    {{-- <div class="mb-2">
                        <label for="motivocambio">Motivo del Cambio</label>
                        <textarea name="motivocambio" id="motivocambio" class="form-control" required></textarea>
                    </div> --}}
                    <div class="mb-2">
                        <label for="motivocambio">Motivo del Cambio</label>
                        <select name="motivocambio" id="motivocambio" class="form-control" required>
                            <option value="">Selecciona un motivo...</option>
                            <option value="VACACIONES">VACACIONES</option>
                            <option value="BAJA MÉDICA">BAJA MÉDICA</option>
                            <option value="AUSENCIA LABORAL">AUSENCIA LABORAL</option>
                            <option value="MALA GESTIÓN">MALA GESTIÓN</option>
                            <option value="APOYO TEMPORAL">APOYO TEMPORAL</option>
                            <option value="DEVOLUCIÓN DE APOYO TEMPORAL">DEVOLUCIÓN DE APOYO TEMPORAL</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-subirarchivos">ACTUALIZAR APODERADO</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buscarInput = document.getElementById('buscarTramiteId');
        const tabla = document.getElementById('resultadoTramite').querySelector('tbody');
        const selectApoderado = document.getElementById('apoderadoactual');

        buscarInput.addEventListener('keyup', function(e) {
        if(e.key !== 'Enter') return;
            const id = this.value.trim();
            if(!id) return tabla.innerHTML = '';

            fetch("{{ url('tramites/buscarpendiente') }}/" + id)

            .then(res => res.json())
            .then(data => {
                tabla.innerHTML = '';
                selectApoderado.innerHTML = '';
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

                // llenar hidden del formulario
                document.getElementById('tramiteid').value = data.id;
                document.getElementById('clienteid').value = data.clienteitaid;
                document.getElementById('clientenombre').value = data.clienteitanombre;
                document.getElementById('apoderadoanterior').value = data.apoderadoasignado;
                document.getElementById('fechaasignacionanterior').value = data.fechaasignacion;

                // llenar select de apoderados
                data.apoderados.forEach(a => {
                    // NO agregar el apoderado que ya está asignado
                    if(a === data.apoderadoasignado) return;

                    const opt = document.createElement('option');
                    opt.value = a;
                    opt.textContent = a;
                    selectApoderado.appendChild(opt);
                });

            });
        });
    });
</script>

<h1>TRÁMITES PARA LA GESTORA PÚBLICA</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
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
        <div class="d-flex align-items-center mb-3">
            <input type="text" id="buscadorTramites" class="form-control form-control-sm w-25 shadow-sm" placeholder="BUSCAR POR ID O NOMBRE DEL CLIENTE...">
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="tablaTramites">
                <thead style="background-color: #f8fdf2" class="table-sm">
                    <tr>
                        <th>ID_Trámite</th>
                        <th>Trámite</th>
                        <th>Requisitos_Pend.</th>
                        <th>Cliente_ID</th>
                        <th>Cliente_Nombre</th>
                        <th>Ciudad</th>
                        <th>Fecha_Bateria</th>
                        <th class="text-center">Recordar</th>
                        <th>Asignar_Apoderado</th>
                        <th class="text-center">Derivar</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $idsMostrados = [];
                    @endphp
                    @foreach ($todostramites as $tramite)
                        @if (!$tramite->apoderadoasignado && !in_array($tramite->id, $idsMostrados))
                            @php
                                $idsMostrados[] = $tramite->id; // Guardamos el ID mostrado
                            @endphp
                            <tr>
                                <td>{{ $tramite->id }}</td>
                                <td>{{ $tramite->tramite }}</td>
                                @php
                                    $estadoReq = 'COMPLETO';

                                    if($tramite->contrato_req === 'PENDIENTE' && $tramite->poder_req === 'PENDIENTE') {
                                        $estadoReq = 'CONTRATO Y PODER';
                                    } elseif($tramite->contrato_req === 'PENDIENTE') {
                                        $estadoReq = 'CONTRATO';
                                    } elseif($tramite->poder_req === 'PENDIENTE') {
                                        $estadoReq = 'PODER';
                                    }
                                @endphp

                                <td>{{ $estadoReq }}</td>
                                <td>{{ $tramite->clienteitaid }}</td>
                                <td>{{ $tramite->clienteitanombre }}</td>
                                <td>{{ $tramite->ciudad }}</td>
                                <td>
                                    @if(is_null($tramite->fechabateria))
                                        @if(in_array($tramite->tramite, ['INVALIDEZ','APELACIÓN','SEGUNDA SOLICITUD','TERCERA SOLICITUD']))
                                            PENDIENTE
                                        @else
                                            NO REQUIERE
                                        @endif
                                    @else
                                        {{ $tramite->fechabateria }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($estadoReq === 'COMPLETO')
                                        <i class="fas fa-check fa-lg" style="color: #94c93b" title="REQUISITOS COMPLETOS"></i>
                                    @elseif(in_array((int)$tramite->id, $notificaciones, true))
                                        <i class="fas fa-bell fa-lg" style="color: #faa625" title="YA SE ENVIÓ EL RECORDATORIO"></i>
                                    @else
                                        {!! Form::open(['route' => ['recordarSubirRequisitos', $tramite->id], 'method' => 'PUT', 'style' => 'display:inline']) !!}
                                            {!! Form::hidden('clienteitanombre', $tramite->clienteitanombre) !!}
                                            {!! Form::hidden('estadoReq', $estadoReq) !!}
                                            {!! Form::hidden('apoderadoasignado', $apoderadosList[$tramite->id]['siguiente']) !!}
                                            <button type="submit" class="btn btn-adjuntosrespuestas btn-sm fas fa-bell" title="Recordar subir requisitos"></button>
                                        {!! Form::close() !!}
                                    @endif
                                </td>

                                <td class="text-center">
                                    {!! Form::open(['route' => ['admin.tramites.asignarapoderadotramiteclienteita', $tramite->clienteitaid], 'method' => 'POST', 'id' => 'form_asignar_'.$tramite->id]) !!}
                                    {!! Form::hidden('clienteitaid', $tramite->clienteitaid) !!}
                                    {!! Form::hidden('fechabateria', $tramite->fechabateria) !!}
                                    {!! Form::hidden('tramite', $tramite->tramite) !!}
                                    {!! Form::select(
                                        'apoderadoasignado_display',
                                        $apoderadosList[$tramite->id]['lista'],
                                        $apoderadosList[$tramite->id]['siguiente'],
                                        [
                                            'class' => 'form-control apoderado-select form-control-sm',
                                            'data-target' => 'apoderado_input_'.$tramite->id,
                                            'placeholder' => 'Seleccione apoderado',
                                            'style' => 'width: 300px;',
                                            'disabled' => 'disabled'
                                        ]
                                    ) !!}

                                    {!! Form::hidden(
                                        'apoderadoasignado',
                                        $apoderadosList[$tramite->id]['siguiente'],
                                        [
                                            'id' => 'apoderado_input_'.$tramite->id,
                                            'class' => 'form-control apoderado-input form-control-sm',
                                            'maxlength' => 90
                                        ]
                                    ) !!}
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-derivarapoderado btn-sm fas fa-sign-out-alt"
                                        {{-- @if($estadoReq !== 'COMPLETO') disabled @endif --}}
                                        @if(!in_array($estadoReq, ['COMPLETO', 'CONTRATO'])) disabled @endif
                                        title="Derivar">
                                    </button>
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            document.querySelectorAll('.apoderado-select').forEach(function(select) {
                                var targetId = select.dataset.target;
                                var input = document.getElementById(targetId);
                                if (!input) return;
                                select.addEventListener('change', function () {
                                    input.value = this.value;
                                });
                                input.addEventListener('input', function () {
                                    var val = this.value;
                                    var exists = Array.from(select.options).some(function(o){ return o.value === val; });
                                    if (!exists && val.trim() !== '') {
                                        var opt = document.createElement('option');
                                        opt.value = val;
                                        opt.text  = val;
                                        select.appendChild(opt);
                                    }
                                    select.value = val;
                                });
                            });
                        });
                    </script>
                </tbody>
            </table>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const buscador = document.getElementById('buscadorTramites');
                const tabla = document.getElementById('tablaTramites');
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
