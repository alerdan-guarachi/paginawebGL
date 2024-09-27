@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>
<a class="btn btn-actualizarestado btn-sm float-right" data-toggle="modal" data-target="#accionModal">ACTUALIZAR ESTADO</a>
<a class="btn btn-sm float-right btn-generarpdf" href="{{ route('admin.asociados.generarpdfprogramacionclienteauditoria', ['clienteauditoria' => $clienteauditoria, 'buscarpor' => $fechaSeleccionada]) }}">GENERAR PDF</a>
<h5>ESTADO DE PROGRAMACIÓN DE:</h5>
<h3>{{$clienteauditoria->nombrecompleto}}</h3>
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
    <div class="card-body">
        <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarprogramacionclientesauditoria', $clienteauditoria) }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <select name="buscarpor" class="form-control mr-sm-2">
                                <option value="" disabled selected>Fecha de Bateria</option>
                                @foreach($fechas as $fecha)
                                    <option value="{{ $fecha }}">{{ $fecha }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
        </nav>     
        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
        {!! Form::hidden('clienteauditoriaid', $id) !!}
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        <th>Proveedor</th>
                        <th>Fecha bateria</th>
                        <th>Fecha asignada</th>
                        <th>Hora asignada</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accionesDisponibles as $accion)
                    <?php
                    $mensaje = "Hola, le hablo de la empresa GOOD LIFE, le recordamos que tiene una cita con: " .
                            $accion->proveedornombre . ", para realizarse: " .
                            $accion->accionnombre . ", para la fecha: " .
                            $accion->fechaasignada . ", a la hora: " . 
                            $accion->horadesde . ". Que tenga un excelente dia.";

                    $mensajeCodificado = urlencode($mensaje);
                    ?>
                    <tr>
                        <td class="align-middle">{{ $accion->accionnombre }}</td>
                        <td class="align-middle">{{ $accion->proveedornombre }}</td>
                        <td class="align-middle">{{ $accion->fechabateria }}</td>
                        <td class="align-middle">{{ $accion->fechaasignada }}</td>
                        <td class="align-middle">{{ $accion->horadesde }} - {{ $accion->horahasta }}</td>
                        <td width="10px">
                            @if(in_array($accion->accionnombre, $estadoRegistrados))
                                <i class="fas fa-check-circle fa-2x checkverde"></i>
                            @else
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            @endif
                        </td>
                        <td width="10px">
                            <abbr title="Recordar">
                                <a class="btn btn-sm btn-whatsapp @if(in_array($accion->accionnombre, $estadoRegistrados)) disabled @endif" 
                                @if(in_array($accion->accionnombre, $estadoRegistrados)) 
                                    onclick="return false;" 
                                @else 
                                    href="https://wa.me/{{ $clienteauditoria->celular }}?text={{ $mensajeCodificado }}" 
                                @endif>
                                    <i class="fas fa-sms"></i>
                                </a>
                            </abbr>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @error('accion')
                <small class="text-danger fas fa-exclamation-circle">
                    {{$message}}
                </small>
            @enderror
        </div>
    </div>
</div>

<div class="modal fade" id="accionModal" tabindex="-1" role="dialog" aria-labelledby="accionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accionModalLabel">ACTUALIZAR ESTADO</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => ['admin.asociados.guardarestadoprogramacionclienteauditoria', $clienteauditoria], 'method' => 'POST']) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteauditoriaid', $id) !!}
                {!! Form::hidden('clienteauditorianombre', $nombreclienteita) !!}
                {!! Form::hidden('accionid', '', ['id' => 'modalAccionId']) !!}

                <div class="form-group">
                    {!! Form::label('', 'Fecha de Bateria:') !!}
                    <select class="form-control" id="fecha_bateria">
                        <option value="" disabled selected></option>
                        @foreach($accionesPorFecha as $fecha => $acciones)
                            <option value="{{ $fecha }}">{{ $fecha }}</option>
                        @endforeach
                    </select>
                    @error('fechabateria')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                    @enderror
                </div>
                <input type="hidden" id="fechabateria" name="fechabateria">

                <div class="form-group" id="acciones_select">
                    {!! Form::label('', 'Acciones disponibles:') !!}
                    <select class="form-control" id="accion" name="accion">
                        <option value="" disabled selected></option>
                    </select>
                    @error('accion')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                    @enderror
                </div>
                <input type="hidden" id="accionnombre" name="accionnombre">
                <div class="form-group" hidden>
                    {!! Form::label('nombrecompleto', 'Nombre del Cliente:') !!}
                    {!! Form::text('nombrecompleto', $clienteauditoria->nombrecompleto, ['id' => 'modalNombreCompleto', 'class' => 'form-control', 'readonly']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('fechaatencionprogramacion', 'Fecha de Atención:') !!}
                    {!! Form::date('fechaatencionprogramacion', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                    @error('fechaatencionprogramacion')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                    @enderror
                </div>
                {!! Form::submit('Actualizar', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        $('#fecha_bateria').on('change', function(){
            $('#accion').val('');
            $('#accionnombre').val('');
        });

        $('#accion').on('change', function(){
            var selectedOption = $(this).val();
            $('#accionnombre').val(selectedOption);
        });
    });

document.getElementById('fecha_bateria').addEventListener('change', function() {
    var fechaSeleccionada = this.value;
    var accionesDisponibles = document.getElementById('accion');
    accionesDisponibles.innerHTML = '';
    var accionesPorFecha = @json($accionesPorFecha);
    var accionesRegistradas = @json($accionesRegistradas);

    var opcionVacia = document.createElement('option');
    opcionVacia.value = '';
    opcionVacia.text = '';
    accionesDisponibles.appendChild(opcionVacia);

    var accionesFechaSeleccionada = accionesPorFecha[fechaSeleccionada];

    var accionesDisponiblesFiltradas = accionesFechaSeleccionada.filter(function(accion) {
        return !accionesRegistradas.includes(accion);
    });

    accionesDisponiblesFiltradas.forEach(function(accion) {
        var opcion = document.createElement('option');
        opcion.value = accion;
        opcion.text = accion;
        accionesDisponibles.appendChild(opcion);
    });
    
    document.getElementById('acciones_select').style.display = 'block';
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#fechabateria').change(function() {
            var selectedOption = $(this).children("option:selected").text();
            $('#fechaSeleccionada').val(selectedOption);
        });
    });

    document.getElementById('fecha_bateria').addEventListener('change', function() {
        var selectedDate = this.value;
        document.getElementById('fechabateria').value = selectedDate;
    });

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

    document.getElementById('area_select').addEventListener('change', function() {
        var select = document.getElementById('area_select');
        var selectedOption = select.options[select.selectedIndex];
        if (selectedOption.value !== '') {
            // Oculta el campo select
            select.style.display = 'none';
            // Oculta el label
            document.getElementById('area_label').style.display = 'none';
            // Muestra el nombre del área seleccionada como título para acciones correspondientes
            var areaName = selectedOption.text;
            var accionesDiv = document.getElementById('acciones_' + selectedOption.value);
            accionesDiv.style.display = 'block'; // Muestra las acciones correspondientes al área seleccionada
            // Añade el nombre del área seleccionada como título para acciones correspondientes si no existe ya
            if (!document.getElementById('acciones_label_' + selectedOption.value)) {
                var accionesLabel = document.createElement('label');
                accionesLabel.innerHTML = 'Acciones para: ' + areaName;
                accionesLabel.id = 'acciones_label_' + selectedOption.value;
                accionesDiv.prepend(accionesLabel);
            }
            // Muestra el botón solo si no está visible
            var resetButton = document.getElementById('reset_button');
            if (!resetButton) {
                var button = document.createElement('button');
                button.type = 'button';
                button.innerHTML = 'Elegir otra area';
                button.classList.add('custom-button');

                button.id = 'reset_button';
                button.onclick = resetSelectAndCheckboxes;
                document.getElementById('reset_button_container').appendChild(button);
            }
        }
    });

    function resetSelectAndCheckboxes() {
        var select = document.getElementById('area_select');
        select.style.display = 'block'; // Mostrar el select nuevamente
        select.value = ''; // Restablecer el valor del select

        // Mostrar el label nuevamente
        document.getElementById('area_label').style.display = 'block';

        // Desmarcar todos los checkboxes de acciones
        var checkboxes = document.querySelectorAll('[id^="accionnombre_"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });

        // Ocultar todas las secciones de acciones y etiquetas de "Acciones para"
        var accionesDivs = document.querySelectorAll('[id^="acciones_"]');
        accionesDivs.forEach(function(div) {
            div.style.display = 'none';
            var label = document.getElementById('acciones_label_' + div.id.split('_')[1]);
            if (label) {
                label.remove();
            }
        });

        // Ocultar el botón de restablecimiento
        var resetButton = document.getElementById('reset_button');
        if (resetButton) {
            resetButton.remove();
        }
    }
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

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-buscar { 
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-buscar:hover {
        background-color: #faa625;
        color: #ffffff;
    } 
    .dropify-wrapper {
        height: 125px !important;
    }
    .dropify-message p {
        font-size: 14px;
    }
    .checkverde {
        color:#94c93b; 
        }
    th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
        }
    .btn-actualizarestado {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
        margin-right: 10px;
        }
    .btn-actualizarestado:hover {
        background-color: #faa625;
        color: #ffffff;
        }
        .btn-generarpdf {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;

        }
    .btn-generarpdf:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .mensaje-error {
        color: #e1172b;
        font-family: "Times New Roman";
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        font-size: 12.5px;
        font-weight: bold;
        display: inline-block;
        margin-left: -10px;
    }
    .custom-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 40px;
    }
    .custom-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .custom2-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 20px;
    }
    .custom2-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-cerrar {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cerrar:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-whatsapp {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-whatsapp:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
</style>
@stop