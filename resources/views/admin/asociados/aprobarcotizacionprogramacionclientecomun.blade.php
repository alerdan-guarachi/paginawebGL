@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclientecomun', $clientecomun) }}">REGRESAR</a>
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">ESTADO DE APROB.</a>

<h5>APROBAR COTIZACIÓN DE PROGRAMACIÓN DE:</h5>
<h3>{{$clientecomun->nombrecompleto}}</h3>
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
        <div class="row ">
            <div class="col-lg-12">
                {!! Form::model($clientecomun, ['route' => ['admin.asociados.guardaraprobacioncotizacionclientecomun', $clientecomun], 'method' => 'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clientecomunid', $id) !!}
                {!! Form::hidden('clientecomunnombre', $clientecomun->nombrecompleto) !!}
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
                    <div class="col-lg-8">
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
                <h5 class="modal-title" id="exampleModalLabel">ESTADO DE APROBACIONES</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"> 
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha de Bateria</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fechasDisponibles as $fecha)
                            <tr style="color:{{ $fechasRegistradas->contains($fecha) ? 'green' : 'red' }}">
                                <td>
                                    <span style="display: inline-block; width: 5px; height: 5px; background-color: black; border-radius: 50%; margin-right: 5px;"></span>
                                    {{ $fecha }}
                                </td>
                                <td>
                                    @if($fechasRegistradas->contains($fecha))
                                        <a type="button" class="btn btn-editar btn-sm edit-btn" data-fecha="{{ $fecha }}" data-toggle="modal" data-target="#editPdfModal" title="MODIFICAR COTIZACIÓN">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @php
                                            $document = $documentosPorFecha->get($fecha)->first()->document ?? null;
                                            $whatsappNumber = $clientecomun->celular;
                                            $mensaje = urlencode("Hola, le comparto la cotización. Puede descargarla aquí:");
                                        @endphp
                                        @if($document)
                                            <a href="{{ asset('/cotizacionesaprobadascomun/'.$clientecomun->id.'/'.$document) }}" target="_blank" class="btn btn-vercotizacion btn-sm" title="VER COTIZACIÓN APROBADA">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </a>
                                        @endif
                                    @else
                                        <span style="color: red;">NO APROBADO</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
            </div>
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

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-editar {
    background-color:  #ffffff;
    color: #faa625;
    border-color: #faa625;
    border-radius: 5px;
    font-weight: bold;
    padding: 5px 10px;
}
.btn-editar:hover {
    background-color: #faa625;
    color: #ffffff;
}
.btn-whatsapp {
    background-color:  #ffffff;
    color: #94c93b;
    border-color: #94c93b;
    border-radius: 5px;
    font-weight: bold;
    padding: 5px 10px;
}
.btn-whatsapp:hover {
    background-color: #94c93b;
    color: #ffffff;
}
.btn-vercotizacion {
    background-color:  #ffffff;
    color: #1294b8d1;
    border-color: #1294b8d1;
    border-radius: 5px;
    font-weight: bold;
    padding: 5px 10px;
}
.btn-vercotizacion:hover {
    background-color: #1294b8d1;
    color: #ffffff;
}
.btn-verconsentimiento {
    background-color:  #ffffff;
    color: #1294b8d1;
    border-color: #1294b8d1;
    border-radius: 5px;
    font-weight: bold;
    padding: 5px 10px;
}
.btn-verconsentimiento:hover {
    background-color: #1294b8d1;
    color: #ffffff;
}
    .dropify-wrapper {
        height: 125px !important;
    }
    .dropify-message p {
        font-size: 14px;
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
        font-size: 23px;
        }
    h6 {
        font-weight: 900;
        }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 20px;
        }
    .btn-crear:hover {
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
        padding: 10px 20px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .custom2-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-nrofactura {
        background-color: #ffffff;
        color: #f04cc4;
        border-color: #f04cc4;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
    }
    .btn-nrofactura:hover {
        background-color: #f04cc4;
        color: #ffffff;
    }
    .btn-consentimientoinformado {
        background-color: #ffffff;
        color: #5db2cd;
        border-color: #5db2cd;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
    }
    .btn-consentimientoinformado:hover {
        background-color: #5db2cd;
        color: #ffffff;
    }
    .btn-cerrar {
        background-color: #ffffff;
        color: #e62e2e;
        border-color: #e62e2e;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cerrar:hover {
        background-color: #e62e2e;
        color: #ffffff;
    }
    .btn-guardarobservacion {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-guardarobservacion:hover {
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