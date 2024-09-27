@extends('adminlte::page')

@section('content_header')
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">DOCUMENTACION DEL CLIENTE</a>
<h1>DOCUMENTACION DEL CLIENTE</h1>
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
                {!! Form::model($clientebanco, ['route' => ['admin.clientesbancos.subirdocumentacioncliente', $clientebanco], 'method' => 'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteid', $id) !!}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('nombrecompleto', 'Nombre completo:') !!}
                            {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                            @error('nombrecompleto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            {!! Form::label('accion', 'Ácciones programadas:') !!}
                            {!! Form::select('accion', $accionesDisponibles, null, ['class' => 'form-control', 'placeholder' => '']) !!}
                            @error('accion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            {!! Form::label('file', 'Documento:') !!}
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
                    <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">DOCUMENTACION DEL CLIENTE:</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    @foreach($accionesCliente as $accion)
                                        @if(in_array($accion, $documentosRegistrados))
                                            <div style="color: green;">&#10003; {{ $accion }}</div>
                                        @else
                                            <div style="color: red;">&#10007; {{ $accion }}</div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::submit('SUBIR DOCUMENTACION', ['class' => 'btn btn-crear']) !!}
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
<!-- Dropify CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">

<!-- Dropify JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializar Dropify con opciones personalizadas
        $('.dropify').dropify({
            messages: {
                'default': 'Arrastre y suelte un archivo o haga clic aquí',
                'replace': 'Arrastre y suelte o haga clic para reemplazar',
                'remove': 'Eliminar',
                'error': 'Ooops, algo salió mal.'
            }
        });
    
        // Escuchar el evento de error de Dropify
        $('.dropify').on('dropify.error.fileSize', function(event, element) {
            var maxSize = element.input.files[0].size / (1024 * 1024); // Convertir a MB
            var errorMessage = 'El archivo es demasiado grande (' + maxSize.toFixed(2) + ' MB máx.).';
            $(element.input).siblings('.dropify-error').text(errorMessage);
        });
    });
    </script>
    
<script>
    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });
</script>

<script>
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
    </script>
    
<script>
//VALIDAR QUE FECHA DE NACIMIENTO NO SEA POSTERIOR A LA FECHA ACTUAL
    var fechaNacimiento = document.getElementById('fecha_nacimiento');
    fechaNacimiento.addEventListener('change', function() {
        var selectedDate = new Date(this.value);
        var currentDate = new Date();
        if (selectedDate > currentDate) {
            this.value = '{{ \Carbon\Carbon::now()->format("Y-m-d") }}';
            if (!document.getElementById('errorMensaje')) {
                var errorMensaje = document.createElement('div');
                errorMensaje.id = 'errorMensaje';
                errorMensaje.classList.add('mensaje-error');
                var iconoError = document.createElement('i');
                iconoError.classList.add('fas', 'fa-exclamation-circle');
                errorMensaje.appendChild(iconoError);
                
                var textoError = document.createElement('span');
                textoError.textContent = ' La fecha de nacimiento no puede ser posterior a la fecha actual.';
                errorMensaje.appendChild(textoError);
                this.parentNode.appendChild(errorMensaje);
            }
        } else {
            var mensajeError = document.getElementById('errorMensaje');
            if (mensajeError) {
                mensajeError.remove();
            }
        }
    });

//CALCULAR LA EDAD
function calcularEdad(fecha_nacimiento) {
    var fecha_actual = new Date();
    var fecha_nacimiento = new Date(fecha_nacimiento);
    
    if (isNaN(fecha_nacimiento.getFullYear()) || fecha_nacimiento.getFullYear() < 1000) {
        return '';
    }
    var edad = fecha_actual.getFullYear() - fecha_nacimiento.getFullYear();
    var mes = fecha_actual.getMonth() - fecha_nacimiento.getMonth();
    if (mes < 0 || (mes === 0 && fecha_actual.getDate() < fecha_nacimiento.getDate())) {
        edad--;
    }
    return edad;
}

//VALIDAR FECHA DE NACIMIENTO
document.getElementById('fecha_nacimiento').addEventListener('change', function() {
        var fecha_nacimiento = this.value;
        var fecha_actual = new Date();
        var selectedDate = new Date(fecha_nacimiento);
        if (selectedDate <= fecha_actual) {
            var edad = calcularEdad(fecha_nacimiento);
            document.getElementById('edad').value = edad;
        } else {
            document.getElementById('edad').value = '';
        }
    });
    var fecha_nacimiento = document.getElementById('fecha_nacimiento').value;
    var fecha_actual = new Date();
    var selectedDate = new Date(fecha_nacimiento);
    if (selectedDate <= fecha_actual) {
        var edad = calcularEdad(fecha_nacimiento);
        document.getElementById('edad').value = edad;
    } else {
        document.getElementById('edad').value = '';
    }

//CANCELAR FUNCION DE LA TECLA ENTER
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
<script>
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
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    /* Personalizar la altura del recuadro de Dropify */
    .dropify-wrapper {
        height: 125px !important; /* Ajusta esta altura según tus necesidades */
    }
    /* Opcional: centrar el mensaje de Dropify */
    .dropify-message p {
        font-size: 14px; /* Ajusta el tamaño del texto si es necesario */
    }
</style>
<style>
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;
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
</style>
@stop