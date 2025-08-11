@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-aprobarcotizacion btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">ESTADO DE APROB.</a>
<a class="btn btn-nrofactura btn-sm float-right" data-toggle="modal" data-target="#facturaModal">NRO. FACTURA</a>
{!! Form::open(['route' => 'generar.pdf.consentimientoinformado', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
    <a class="btn btn-consentimientoinformado btn-sm float-right" href="#" onclick="event.preventDefault(); this.closest('form').submit();">CONS. INFORMADO</a>
    {!! Form::hidden('clienteitaid', $cliente->id, ['class' => 'form-control']) !!}
    {!! Form::hidden('nombres', $cliente->nombres, ['class' => 'form-control']) !!}
    {!! Form::hidden('apepaterno', $cliente->apepaterno, ['class' => 'form-control']) !!}
    {!! Form::hidden('apematerno', $cliente->apematerno, ['class' => 'form-control']) !!}
    {!! Form::hidden('ci', $cliente->ci, ['class' => 'form-control']) !!}
{!! Form::close() !!}

<h5>APROBAR COTIZACIÓN DE PROGRAMACIÓN DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/cotizacionmedicaclientes.css') }}">
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
        <div class="row ">
            <div class="col-lg-12">
                {!! Form::model($cliente, ['route' => ['admin.asociados.guardaraprobacioncotizacionclienteita', $cliente], 'method' => 'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteitaid', $id) !!}
                {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('fechabateria', 'Fecha de bateria:') !!}
                            {!! Form::select('fechabateria', $fechas->toArray(), null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'fechabateria']) !!}
                            {!! Form::hidden('fechabateria', null, ['class' => 'form-control', 'readonly', 'id' => 'fechaSeleccionada']) !!}
                            @error('fechabateria')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            $(document).ready(function() {
                                $('#fechabateria').change(function() {
                                    var selectedOption = $(this).children("option:selected").text();
                                    $('#fechaSeleccionada').val(selectedOption);
                                });
                            });
                        </script>                    
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('file', 'Cotización aprobada:') !!}
                            <input type="file" name="archivo" id="archivo" class="dropify"/>
                            @error('archivo')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="" id="preview-card" style="display: none;">
                                <div class="">
                                    <iframe id="document-preview" style="width: 100%; height: 300px;" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            {!! Form::label('file', 'Cons. informado para realización de evaluaciones:') !!}
                            <input type="file" name="archivo2" id="archivo2" class="dropify"/>
                            @error('archivo2')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="" id="preview-card2" style="display: none;">
                                <div class="">
                                    <iframe id="document-preview2" style="width: 100%; height: 300px;" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::submit('APROBAR BATERIA', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">  
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="font-weight:750;">ESTADO DE APROBACIONES</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped">
                        <thead class="text-center table-secondary">
                            <tr>
                                <th style="color: black; font-weight:700;">Fecha de Batería</th>
                                <th style="color: black; font-weight:700;">Documentos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fechasDisponibles as $fecha)
                                <tr style="color:{{ $fechasRegistradas->contains($fecha) ? '#94c93b' : 'red' }}">
                                    <td style="vertical-align: middle;">
                                        <span style="display: inline-block; width: 8px; height: 8px; background-color: black; border-radius: 50%; margin-right: 8px;"></span>
                                        <strong>{{ $fecha }}</strong>
                                    </td>
                                    <td class="text-left" style="vertical-align: middle;">
                                        @if($fechasRegistradas->contains($fecha))
                                            @php
                                                $documentosMostrados = [];
                                                $documentoDisponible = false;
                                            @endphp
                
                                            @foreach ($cotizaciones as $cotizacion)
                                                @php
                                                    $document = $documentosPorFecha->get($fecha)->first()->document ?? null;
                                                    $documentconsinfo = $documentosPorFecha->get($fecha)->first()->documentconsinfo ?? null;
                                                @endphp
                                                
                                                @if($document && !in_array($document, $documentosMostrados))
                                                    <a href="{{ asset('/cotizacionesaprobadasita/'.$cliente->id.'/'.$document) }}" target="_blank" class="btn btn-vercotizacion btn-sm" title="VER COTIZACIÓN APROBADA">
                                                        COTIZACIÓN
                                                    </a>
                                                    @php
                                                        $documentosMostrados[] = $document;
                                                        $documentoDisponible = true;
                                                    @endphp
                                                @endif
                
                                                @if($documentconsinfo && !in_array($documentconsinfo, $documentosMostrados))
                                                    <a href="{{ asset('/cotizacionesaprobadasita/'.$cliente->id.'/'.$documentconsinfo) }}" target="_blank" class="btn btn-verconsentimiento btn-sm" title="VER CONSENTIMIENTO INFORMADO">
                                                        CONSENTIMIENTO
                                                    </a>
                                                    @php
                                                        $documentosMostrados[] = $documentconsinfo;
                                                        $documentoDisponible = true;
                                                    @endphp
                                                @endif
                                            @endforeach
                
                                            @if($documentoDisponible && $loop->last)
                                                <a type="button" class="btn btn-editar btn-sm edit-btn" data-fecha="{{ $fecha }}" data-toggle="modal" data-target="#editPdfModal" title="MODIFICAR DOCUMENTOS">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        @else
                                            <span class="badge badge-danger">NO APROBADO</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editPdfModal" tabindex="-1" role="dialog" aria-labelledby="editPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPdfModalLabel" style="font-weight:750;">MODIFICAR COTIZACIÓN</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPdfForm" action="{{ route('admin.actualizarPdf') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="clienteitaid" id="clienteitaid" value="{{ $cliente->id }}">
                    
                    <label for="">Fecha de Batería:</label>
                    <input type="text" name="fechabateria" id="fechabateria" value="{{ $fecha }}">
                
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="archivo">Nueva cotización (PDF):</label>
                            <input type="file" class="form-control-file dropify" name="archivo" id="archivo" accept="application/pdf">
                        </div>
                    
                        <div class="form-group col-lg-6">
                            <label for="archivo2">Nuevo consentimiento informado (PDF):</label>
                            <input type="file" class="form-control-file dropify" name="archivo2" id="archivo2" accept="application/pdf">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-crear">Actualizar Doc.</button>
                        <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>
                
            </div>
            
        </div>
    </div>
</div>
<div class="modal fade" id="facturaModal" tabindex="-1" role="dialog" aria-labelledby="facturaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">NRO. DE FACTURA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('guardarFacturacotclienteita') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        {!! Form::hidden('clienteitaid', $cliente->id, null, ['class' => 'form-control', 'readonly',]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('fechabateriaseleccionada', 'Fecha de batería:') !!}
                        {!! Form::select('fechabateriaseleccionada', $fechasregis->toArray(), null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'fechabateriaseleccionada']) !!}
                        {!! Form::hidden('fechabateriaseleccionada', null, ['class' => 'form-control', 'readonly', 'id' => 'fechaSeleccionada2']) !!}
                        @error('fechabateriaseleccionada')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('nrofactura', 'Nro. factura:') !!}
                        {!! Form::text('nrofactura', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        @error('nrofactura')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                        @enderror
                    </div>
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('#fechabateriaseleccionada').change(function() {
                                var selectedOption = $(this).children("option:selected").text();
                                $('#fechaSeleccionada2').val(selectedOption);
                            });
                        });
                    </script> 
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-crear2">Guardar</button>
                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
            </div>
        </form>
            
        </div>
    </div>
</div>
@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>

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

    document.getElementById('archivo2').addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var fileURL = URL.createObjectURL(file);
            var previewCard = document.getElementById('preview-card2');
            var documentPreview = document.getElementById('document-preview2');
    
            previewCard.style.display = 'block';
            documentPreview.src = fileURL;
        } else {
            var previewCard = document.getElementById('preview-card2');
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
</script>
@endsection