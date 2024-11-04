@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclientebanco', $clientebanco) }}">REGRESAR</a>
<h5>DECLARACIONES JURADAS DE:</h5>
<h3>{{$clientebanco->nombrecompleto}}</h3>
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
            <div class="row">
                {{-- Mostrar el documento digital si existe --}}
                @if (isset($declaracionDigital))
                    <div class="col-6">
                        <h4 class="text-center">Declaración Jurada Digital</h4>
                        <embed src="{{ asset('fichamedicaclientesbanco/' . $clientebanco->id . '/' . $declaracionDigital->document) }}" type="application/pdf" width="100%" height="400px" />
                    </div>
                @endif

                {{-- Mostrar mensaje animado si falta el documento físico --}}
                @if (!isset($declaracionFisico))
                    <div class="col-6 text-center">
                        <div class="alert alert-warning" id="alert-fisico" role="alert">
                            DECLARACION JURADA FISICA PENDIENTE
                        </div>
                        {{-- <p class="text-center">
                            Por favor, sube el documento físico a continuación.
                        </p> --}}
                        <!-- Botón para subir un PDF escaneado (físico) -->
                <div style="text-align: center; margin-bottom: 10px;">
                    {!! Form::open([
                        'route' => ['admin.asociados.formularios.guardardeclaracion', $clientebanco],
                        'method' => 'POST',
                        'files' => true,
                    ]) !!}
                    {!! Form::hidden('tipodocumento', 'FISICO') !!}

                    <!-- Etiqueta para el archivo PDF -->
                    {!! Form::label('pdf_fisico', 'Selecciono la declaracion jurada fisica:') !!}

                    <!-- Campo de archivo centrado y ocupando el 50% de la pantalla -->
                    <div style="display: flex; justify-content: center;">
                        {{-- {!! Form::file('pdf_fisico', ['class' => 'form-control', 'style' => 'width: 50%;']) !!} --}}
                        <input type="file" name="pdf_fisico" id="pdf_fisico" class="dropify" />
                    </div>

                    <!-- Separación entre el campo de archivo y el botón -->
                    <div style="margin-top: 15px;">
                        {!! Form::submit('Subir Declaracion Física', ['class' => 'btn btn-crear']) !!}
                    </div>

                    {!! Form::close() !!}
                </div>
                    </div>
                @else
                    {{-- Si existe el documento físico, mostrarlo --}}
                    <div class="col-6">
                        <h4 class="text-center">Declaración Jurada Física</h4>
                        <embed src="{{ asset('fichamedicaclientesbanco/' . $clientebanco->id . '/' . $declaracionFisico->document) }}" type="application/pdf" width="100%" height="400px" />
                    </div>
                @endif
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

//CANCELAR FUNCION DE LA TECLA ENTER
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
@stop
@section('css')
    <link rel="styleheet" href="/css/admin_custom.css">
    <style>
        .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 15px;
        }
    
    .btn-crear:hover {
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
        h1,
        th {
            color: #94c93b;
            font-family: "Segoe UI";
            font-weight: 900;
        }

        .btn-mas {
            background-color: #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
            padding: 10px 15px;
        }

        .btn-mas:hover {
            background-color: #94c93b;
            color: #ffffff;
        }

        .btn-menos {
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
            padding: 10px 15px;
        }

        .btn-menos:hover {
            background-color: #faa625;
            color: #ffffff;
        }

        .odd-row {
            background-color: #f2f3f5;
        }

        .container2 {
            width: 80%;
            margin: auto;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .form-line {
            display: flex;
            align-items: center;
        }

        .form-line label {
            margin-right: 10px;
        }

        .form-line input {
            border: none;
            border-bottom: 1px solid #000;
            outline: none;
            text-align: center;
            width: 150px;
        }

        embed {
            border: 1px solid #94c93b;
            border-radius: 5px;
        }

        .alert-warning {
            background-color: #ffeeba;
            color: #856404;
            border-color: #ffeeba;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
@stop

@section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.dropify').dropify();
        });
    </script>
@endsection
