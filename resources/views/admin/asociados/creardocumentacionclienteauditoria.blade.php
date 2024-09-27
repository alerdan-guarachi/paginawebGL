@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">DOCUMENTACION DEL CLIENTE</a>
<a class="btn btn-sm float-right btn-multiple" href="{{ route('admin.asociados.documentacionmultipleclienteauditoria', 2) }}">DOC. MÚLTIPLE</a>
<h5>SUBIR DOCUMENTACIÓN DE:</h5>
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
                {!! Form::hidden('clienteauditoriaid', $id) !!}
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
                        <input type="hidden" id="accionselec" name="accionselec">
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
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
                           // JavaScript para mostrar las acciones correspondientes cuando se selecciona una fecha de batería
                           document.getElementById('fecha_bateria').addEventListener('change', function() {
    var fechaSeleccionada = this.value;
    var accionesDisponibles = document.getElementById('accion');
    accionesDisponibles.innerHTML = '';
    var accionesPorFecha = @json($accionesPorFecha);
    var accionesRegistradas = @json($accionesRegistradas);
    var accionesEnEstado = @json($accionesEnEstado); // Aquí debes pasar las acciones de ESTADOPROGRAMACIONSUBCLIENTE

    // Mostrar la primera opción vacía
    var opcionVacia = document.createElement('option');
    opcionVacia.value = '';
    opcionVacia.text = '';
    accionesDisponibles.appendChild(opcionVacia);

    // Obtener las acciones correspondientes a la fecha seleccionada
    var accionesFechaSeleccionada = accionesPorFecha[fechaSeleccionada];

    // Filtrar las acciones que no están registradas y que están en ESTADOPROGRAMACIONSUBCLIENTE
    var accionesDisponiblesFiltradas = accionesFechaSeleccionada.filter(function(accion) {
        return !accionesRegistradas.includes(accion) && accionesEnEstado.includes(accion);
    });

    // Mostrar las acciones filtradas en el select
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
                        </script>    
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('file', 'Documento:') !!}
                            <input type="file" name="archivo" id="archivo" class="dropify" />
                            @error('archivo')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                            @enderror
                        </div>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#previewModal">VISTA PREVIA</button>
                    </div>

                    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="previewModalLabel">Vista Previa del Documento</h5>
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
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">DOCUMENTOS DEL CLIENTE:</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <strong>Fecha de Bateria:</strong>
                                    <select id="select-fechas" class="form-control">
                                        <option value="" disabled selected></option>
                                        @foreach($accionesPorFecha as $fecha => $acciones)
                                            <option value="{{ $fecha }}">{{ $fecha }}</option>
                                        @endforeach
                                    </select>
                                    <div id="acciones-container" class="mt-3">
                                        <strong>Documentos requeridos:</strong>
                                        @foreach($accionesPorFecha as $fecha => $acciones)
                                            <div id="acciones-{{ $fecha }}" class="acciones" style="display: none;">
                                                @foreach($acciones as $accion)
                                                    @if(in_array($accion, $documentosRegistrados))
                                                        <div style="color: green;">&#10003; {{ $accion }}</div>
                                                    @else
                                                        <div style="color: red;">&#10007; {{ $accion }}</div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        const selectFechas = document.getElementById('select-fechas');
                                        const accionesContainer = document.getElementById('acciones-container');
                                
                                        selectFechas.addEventListener('change', function(event) {
                                            const selectedFecha = this.value;
                                            const allActionDivs = accionesContainer.querySelectorAll('.acciones');
                                            allActionDivs.forEach(function(actionDiv) {
                                                if (actionDiv.id === 'acciones-' + selectedFecha) {
                                                    actionDiv.style.display = "block";
                                                } else {
                                                    actionDiv.style.display = "none";
                                                }
                                            });
                                        });
                                    });
                                </script>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
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
</style>
@stop