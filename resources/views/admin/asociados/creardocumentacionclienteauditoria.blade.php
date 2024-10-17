@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">INFORMES DEL CLIENTE</a>
{{-- <a class="btn btn-sm float-right btn-listainformes" href="{{ route('admin.asociados.listadodocumentacionclienteita', $cliente) }}">LISTA DE INFORMES</a> --}}
{{-- <a class="btn btn-sm float-right btn-multiple" href="{{ route('admin.asociados.documentacionmultipleclienteita', 6) }}">DOC. MÚLTIPLE</a> --}}

<h5>SUBIR INFORMES DE:</h5>
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
        <div class="row ">
            <div class="col-lg-12">
                {!! Form::model($clienteauditoria, ['route' => ['admin.asociados.guardardocumentacionclienteauditoria', $clienteauditoria], 'method' => 'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteauditoriaid', $clienteauditoria->id) !!}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group" hidden>
                            {!! Form::label('nombrecompleto', 'Nombre completo:') !!}
                            {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                            @error('nombrecompleto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
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
                        
                        {{-- <div class="form-group" id="acciones_select">
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
                        <input type="hidden" id="accionselec" name="accionselec"> --}}

                        <div class="form-group" id="acciones_select" style="display: none;">
                            {!! Form::label('', 'Acciones disponibles:') !!}
                            <div id="acciones_disponibles"></div>
                            @error('accion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <style>
                            /* Estilo para que los labels de las acciones no se muestren en negritas */
                            #acciones_disponibles label {
                                font-weight: normal; /* Evita que las etiquetas sean gruesas */
                            }
                        </style>
                        <input type="hidden" id="accionselec" name="accionselec">
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('file', 'Informe:') !!}
                            <input type="file" name="archivo" id="archivo" class="dropify" />
                            @error('archivo')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                            @enderror
                        </div>
                    <button type="button" class="btn btn-regresar" data-toggle="modal" data-target="#previewModal">VISTA PREVIA</button>
                    </div>

                    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="previewModalLabel">Vista Previa del Informe</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                            <div class="modal-body">
                            <iframe id="document-preview" style="width: 100%; height: 500px; border: none;"></iframe>
                            </div>
                        </div>
                        </div>
                    </div>
  
                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('file', 'Imagen 1:') !!}
                            <input type="file" name="picture" id="picture" class="dropify"/>
                            @error('picture')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <div class="form-group">
                            {!! Form::label('file', 'Imagen 2:') !!}
                            <input type="file" name="picture2" id="picture2" class="dropify"/>
                            @error('picture2')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> 
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">INFORMES DEL CLIENTE:</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <strong>Fecha de Batería:</strong>
                                    <select id="select-fechas" class="form-control">
                                        <option value="" disabled selected></option>
                                        @foreach($accionesConEstadoPorFecha as $fecha => $acciones)
                                            <option value="{{ $fecha }}">{{ $fecha }}</option>
                                        @endforeach
                                    </select>
                        
                                    <div id="acciones-container" class="mt-3">
                                        <table class="table table-bordered" id="acciones-table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Estudio/Especialidad</th>
                                                    <th>Proveedor</th>
                                                    <th>Registro</th>
                                                    <th>Informes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($accionesConEstadoPorFecha as $fecha => $acciones)
                                                    @foreach($acciones as $accion)
                                                        <tr id="acciones-{{ $fecha }}" class="acciones" style="display: none;">
                                                            <td @if($accion['registrado']) style="color: green;" @else style="color: red;" @endif>
                                                                @if($accion['registrado']) 
                                                                    {{ $accion['id'] }} 
                                                                @else 
                                                                    0 
                                                                @endif
                                                            </td>
                                                            
                                                            <td @if($accion['registrado']) style="color: green;" @else style="color: red;" @endif>{{ $accion['accionnombre'] }}</td>
                                                            <td @if($accion['registrado']) style="color: green;" @else style="color: red;" @endif>
                                                                @if($accion['registrado'])
                                                                    {{ $accion['proveedornombre'] }}
                                                                @else
                                                                    <span style="color: red;">No registrado</span>
                                                                @endif
                                                            </td>
                                                            <td @if($accion['registrado']) style="color: green;" @else style="color: red;" @endif>
                                                                @if($accion['creacionregistro'])
                                                                    <span style="color: green;">{{ $accion['creacionregistro'] }}</span>
                                                                @else
                                                                    <span style="color: red;">No registrado</span>
                                                                @endif
                                                            </td>
                                                            
                                                            <td @if($accion['registrado']) style="color: green;" @else style="color: red;" @endif>
                                                                @if($accion['document'])
                                                                    <a href="{{ asset('/documentacionclientesauditoria/' . $clienteauditoria->id . '/' . $accion['document']) }}" class="btn btn-verinforme btn-sm" target="_blank">
                                                                        <i class="fas fa-folder-open"></i>
                                                                    </a>
                                                                @else
                                                                    <span style="color: red;">No disponible</span>
                                                                @endif
                    
                                                                @if($accion['image'])
                                                                    <a href="{{ asset('/documentacionclientesauditoria/' . $clienteauditoria->id . '/' . $accion['image']) }}" class="btn btn-verimagen btn-sm" target="_blank">
                                                                        <i class="fas fa-images"></i>
                                                                    </a>
                                                                @endif
                    
                                                                @if($accion['image2'])
                                                                    <a href="{{ asset('/documentacionclientesauditoria/' . $clienteauditoria->id . '/' . $accion['image2']) }}" class="btn btn-verimagen btn-sm" target="_blank">
                                                                        <i class="fas fa-images"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
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
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const selectFechas = document.getElementById('select-fechas');
                            const allActionRows = document.querySelectorAll('.acciones');
                    
                            selectFechas.addEventListener('change', function(event) {
                                const selectedFecha = this.value;
                                
                                allActionRows.forEach(function(actionRow) {
                                    // Muestra solo las filas de la fecha seleccionada
                                    if (actionRow.id === 'acciones-' + selectedFecha) {
                                        actionRow.style.display = "table-row";
                                    } else {
                                        actionRow.style.display = "none";
                                    }
                                });
                            });
                        });
                    </script>
                    
                    <style>
                        /* Reduce el interlineado entre filas */
                        table tbody tr {
                            line-height: 0.5; /* Menor interlineado */
                        }
                    
                        table tbody tr td {
                            padding: 4px 5px; /* Reduce el padding para disminuir el espacio vertical */
                            vertical-align: middle; /* Asegura la alineación vertical en el medio */
                        }
                    
                        /* Colorear las filas impares */
                        table tbody tr:nth-child(odd) {
                            background-color: #f1f1f1; /* Color de fondo para filas impares */
                        }
                    
                        table tbody tr:nth-child(even) {
                            background-color: #ffffff; /* Color de fondo para filas pares */
                        }
                    
                        /* Asegura que el color rojo se aplique a todos los textos cuando no está registrado */
                        table tbody tr td[style*="color: red;"] {
                            font-weight: normal; /* Resalta más el texto en rojo */
                        }
                    
                        .btn-verinforme,
                        .btn-verimagen {
                            background-color: #ffffff;
                            border-radius: 5px;
                            padding: 2px 4px; /* Reduce el padding para botones */
                            font-size: 12px; /* Tamaño de fuente más pequeño */
                            margin: 0; /* Elimina el margen para evitar espacios extra */
                            border: 1px solid transparent; /* Establecer un borde */
                            margin-bottom: -8px;
                            margin-top: -8px;
                        }
                    
                        .btn-verinforme {
                            color: #faa625;
                            border-color: #faa625;
                        }
                    
                        .btn-verinforme:hover {
                            background-color: #faa625;
                            color: #ffffff;
                        }
                    
                        .btn-verimagen {
                            color: #25b6fa;
                            border-color: #25b6fa;
                        }
                    
                        .btn-verimagen:hover {
                            background-color: #25b6fa;
                            color: #ffffff;
                        }
                    </style>
                </div>
                {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear']) !!}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
{{-- <script>
        $(document).ready(function(){
            $('#fecha_bateria').on('change', function(){
                $('#accion').val('');
                $('#accionselec').val('');
            });
            $('#accion').on('change', function(){o
                var selectedOption = $(this).val();
                $('#accionselec').val(selectedOption);
            });
        });
        
        document.getElementById('fecha_bateria').addEventListener('change', function() {
        var fechaSeleccionada = this.value;
        var accionesDisponibles = document.getElementById('accion');
        accionesDisponibles.innerHTML = '';
        var accionesNoRegistradasPorFecha = @json($accionesNoRegistradasPorFecha);

        var opcionVacia = document.createElement('option');
        opcionVacia.value = '';
        opcionVacia.text = '';
        accionesDisponibles.appendChild(opcionVacia);

        if (accionesNoRegistradasPorFecha[fechaSeleccionada]) {
            accionesNoRegistradasPorFecha[fechaSeleccionada].forEach(function(accion) {
                var opcion = document.createElement('option');
                opcion.value = accion.accionnombre;
                opcion.text = accion.accionnombre;
                accionesDisponibles.appendChild(opcion);
            });
        }

        document.getElementById('acciones_select').style.display = 'block';
    });

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
</script> --}}

<script>
    $(document).ready(function(){
        $('#fecha_bateria').on('change', function(){
            $('#accionselec').val('');  // Limpia el valor previo
        });

        document.getElementById('fecha_bateria').addEventListener('change', function() {
            var fechaSeleccionada = this.value;
            var accionesContainer = document.getElementById('acciones_disponibles');
            accionesContainer.innerHTML = '';  // Limpia los checkboxes previos
            var accionesNoRegistradasPorFecha = @json($accionesNoRegistradasPorFecha);

            if (accionesNoRegistradasPorFecha[fechaSeleccionada]) {
                accionesNoRegistradasPorFecha[fechaSeleccionada].forEach(function(accion) {
                    // Crear el checkbox
                    var checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'acciones[]';
                    checkbox.value = accion.accionnombre;
                    checkbox.id = 'accion_' + accion.accionnombre;

                    // Crear la etiqueta del checkbox
                    var label = document.createElement('label');
                    label.htmlFor = 'accion_' + accion.accionnombre;
                    label.textContent = accion.accionnombre;

                    // Añadir checkbox y etiqueta al contenedor
                    accionesContainer.appendChild(checkbox);
                    accionesContainer.appendChild(label);
                    accionesContainer.appendChild(document.createElement('br'));
                });
            }

            // Mostrar el div de acciones
            document.getElementById('acciones_select').style.display = 'block';
        });
    });

    document.getElementById('fecha_bateria').addEventListener('change', function() {
        var selectedDate = this.value;
        document.getElementById('fechabateria').value = selectedDate;
    });
</script>

<script>
    // Función para cargar la vista previa del documento seleccionado en el iframe del modal
    function cargarVistaPrevia() {
      var archivo = document.getElementById('archivo').files[0];
      if (archivo) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var previewIframe = document.getElementById('document-preview');
          previewIframe.src = e.target.result;
        };
        reader.readAsDataURL(archivo);
      }
    }
  
    // Evento cuando se selecciona un archivo
    document.getElementById('archivo').addEventListener('change', function() {
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

@endsection
@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .dropify-wrapper {
        height: 200px !important;
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
        padding: 10px 20px;
        margin-left: 10px;
        margin-right: 10px;
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
    .btn-multiple {
        background-color: #ffffff;
        color: #26b0e2;
        border-color: #26b0e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-multiple:hover {
        background-color: #26b0e2;
        color: #ffffff;
    }
    .btn-listainformes {
        background-color: #ffffff;
        color: #493535;
        border-color: #493535;
        border-radius: 5px;
        padding: 10px 10px;
        margin-left: 10px;

    }
    .btn-listainformes:hover {
        background-color: #493535;
        color: #ffffff;
    }
</style>
@stop