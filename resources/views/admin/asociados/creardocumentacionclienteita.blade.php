@extends('adminlte::page')

@section('content_header')
@if ($rolusuario != 'PROVEEDOR')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
@endif
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">INFORMES DEL CLIENTE</a>
{{-- <a class="btn btn-sm float-right btn-listainformes" href="{{ route('admin.asociados.listadodocumentacionclienteita', $cliente) }}">LISTA DE INFORMES</a> --}}
{{-- <a class="btn btn-sm float-right btn-multiple" href="{{ route('admin.asociados.documentacionmultipleclienteita', 6) }}">DOC. MÚLTIPLE</a> --}}

<h5>SUBIR INFORMES DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
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
                {!! Form::model($cliente, ['route' => ['admin.asociados.guardardocumentacionclienteita', $cliente], 'method' => 'POST', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteitaid', $cliente->id) !!}
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

                        <div class="form-group" id="acciones_select" style="display: none;">
                            {!! Form::label('', 'Acciones disponibles:') !!}
                            <div id="acciones_disponibles">
                                <label>
                                    <input type="checkbox" id="select_all"> Seleccionar todo
                                </label>
                                <br>
                            </div>
                            @error('accion')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                        <style>
                            #acciones_disponibles label {
                                font-weight: normal;
                            }
                        </style>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group">
                            {!! Form::label('file', 'Informe:') !!}
                            <input type="file" name="archivo" id="archivo" class="dropify" accept=".pdf"/>
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
                            {{-- <div class="modal-body">
                            <iframe id="document-preview" style="width: 100%; height: 500px; border: none;"></iframe>
                            </div> --}}
                            <div class="modal-body">
                                <object id="document-preview" type="application/pdf" style="width: 100%; height: 500px;">
                                    <p><a id="pdf-download" href="#" target="_blank">Descargar</a></p>
                                </object>
                            </div>
                            <script>
                                document.getElementById("archivo").addEventListener("change", function(event) {
                                let file = event.target.files[0];

                                if (file) {
                                    let objectURL = URL.createObjectURL(file);
                                    let preview = document.getElementById("document-preview");
                                    let downloadLink = document.getElementById("pdf-download");

                                    preview.data = objectURL;
                                    downloadLink.href = objectURL;
                                }
                            });
                            </script>                            
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
                                                                    <a href="{{ asset('/documentacionclientesita/' . $cliente->id . '/' . $accion['document']) }}" class="btn btn-verinforme btn-sm" target="_blank" title="VER INFORME">
                                                                        <i class="fas fa-folder-open"></i>
                                                                    </a>
                                                                @else
                                                                    <span style="color: red;">No disponible</span>
                                                                @endif
                    
                                                                @if($accion['image'])
                                                                    <a href="{{ asset('/documentacionclientesita/' . $cliente->id . '/' . $accion['image']) }}" class="btn btn-verimagen btn-sm" target="_blank" title="VER IMAGEN 1">
                                                                        <i class="fas fa-image"></i>
                                                                    </a>
                                                                @endif
                    
                                                                @if($accion['image2'])
                                                                    <a href="{{ asset('/documentacionclientesita/' . $cliente->id . '/' . $accion['image2']) }}" class="btn btn-verimagen btn-sm" target="_blank" title="VER IMAGEN 2">
                                                                        <i class="fas fa-images"></i>
                                                                    </a>
                                                                @endif

                                                                @if($accion['docfirmado'])
                                                                    <a href="{{ asset('/documentacionclientesita/' . $cliente->id . '/' . $accion['docfirmado']) }}" class="btn btn-docfirmado btn-sm" target="_blank" title="VER INFORME FIRMADO">
                                                                        <i class="fas fa-paste"></i>
                                                                    </a>
                                                                @endif

                                                                @if($accion['docword'])
                                                                    <a href="{{ asset('/documentacionclientesita/' . $cliente->id . '/' . $accion['docword']) }}" class="btn btn-docword btn-sm" target="_blank" title="VER INFORME WORD">
                                                                        <i class="fas fa-paste"></i>
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
                        table tbody tr {
                            line-height: 0.8;
                        }
                        table tbody tr td {
                            padding: 4px 5px;
                            vertical-align: middle;
                        }
                        table tbody tr:nth-child(odd) {
                            background-color: #f1f1f1;
                        }
                        table tbody tr:nth-child(even) {
                            background-color: #ffffff;
                        }
                        table tbody tr td[style*="color: red;"] {
                            font-weight: normal;
                        }
                        .btn-verinforme,
                        .btn-verimagen {
                            background-color: #ffffff;
                            border-radius: 5px;
                            padding: 2px 4px;
                            font-size: 12px;
                            margin: 0;
                            border: 1px solid transparent;
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

                        .btn-docfirmado {
                            background-color: #ffffff;
                            border-radius: 5px;
                            padding: 2px 4px;
                            font-size: 12px;
                            margin: 0;
                            border: 1px solid transparent;
                            margin-bottom: -8px;
                            margin-top: -8px;
                            color: #be26dc;
                            border-color: #be26dc;
                        }

                        .btn-docfirmado:hover {
                            background-color: #be26dc;
                            color: #ffffff;
                        }
                        .btn-docword {
                            background-color: #ffffff;
                            border-radius: 5px;
                            padding: 2px 4px;
                            font-size: 12px;
                            margin: 0;
                            border: 1px solid transparent;
                            margin-bottom: -8px;
                            margin-top: -8px;
                            color: #262cdc;
                            border-color: #262cdc;
                        }

                        .btn-docword:hover {
                            background-color: #262cdc;
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

<script>
    $(document).ready(function(){
        $('#fecha_bateria').on('change', function(){
            $('#accionselec').val('');
        });

        document.getElementById('fecha_bateria').addEventListener('change', function() {
            var fechaSeleccionada = this.value;
            var accionesContainer = document.getElementById('acciones_disponibles');
            accionesContainer.innerHTML = '';
            
            var selectAllCheckbox = document.createElement('input');
            selectAllCheckbox.type = 'checkbox';
            selectAllCheckbox.id = 'select_all';
            var selectAllLabel = document.createElement('label');
            selectAllLabel.htmlFor = 'select_all';
            selectAllLabel.textContent = 'Seleccionar todo';
            accionesContainer.appendChild(selectAllCheckbox);
            accionesContainer.appendChild(selectAllLabel);
            accionesContainer.appendChild(document.createElement('br'));

            var accionesNoRegistradasPorFecha = @json($accionesNoRegistradasPorFecha);

            if (accionesNoRegistradasPorFecha[fechaSeleccionada]) {
                accionesNoRegistradasPorFecha[fechaSeleccionada].forEach(function(accion) {
                    var checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.name = 'acciones[]';
                    checkbox.value = accion.accionnombre;
                    checkbox.classList.add('accion_checkbox');
                    checkbox.id = 'accion_' + accion.accionnombre;

                    var label = document.createElement('label');
                    label.htmlFor = 'accion_' + accion.accionnombre;
                    label.textContent = accion.accionnombre;

                    accionesContainer.appendChild(checkbox);
                    accionesContainer.appendChild(label);
                    accionesContainer.appendChild(document.createElement('br'));
                });
            }

            document.getElementById('acciones_select').style.display = 'block';

            document.getElementById('select_all').addEventListener('change', function() {
                var checkboxes = document.querySelectorAll('.accion_checkbox');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = document.getElementById('select_all').checked;
                });
            });
        });
    });

    document.getElementById('fecha_bateria').addEventListener('change', function() {
        var selectedDate = this.value;
        document.getElementById('fechabateria').value = selectedDate;
    });
</script>

<script>
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