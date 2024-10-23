@extends('adminlte::page')

@section('content_header')
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
                        <h4 class="text-center">Declaración Médica Digital</h4>
                        <embed src="{{ asset('fichamedicaclientesbanco/' . $clientebanco->id . '/' . $declaracionDigital->document) }}" type="application/pdf" width="100%" height="400px" />
                    </div>
                @endif

                {{-- Mostrar mensaje animado si falta el documento físico --}}
                @if (!isset($declaracionFisico))
                    <div class="col-6 text-center">
                        <div class="alert alert-warning" id="alert-fisico" role="alert">
                            <strong>¡Atención!</strong> Aún no has subido la Declaración Médica Física.
                        </div>
                        <p class="text-center">
                            Por favor, sube el documento físico a continuación.
                        </p>
                        <!-- Botón para subir un PDF escaneado (físico) -->
                <div style="text-align: center; margin-bottom: 10px;">
                    {!! Form::open([
                        'route' => ['admin.asociados.formularios.guardardeclaracion', $clientebanco],
                        'method' => 'POST',
                        'files' => true,
                    ]) !!}
                    {!! Form::hidden('tipodocumento', 'FISICO') !!}

                    <!-- Etiqueta para el archivo PDF -->
                    {!! Form::label('pdf_fisico', 'Subir PDF Físico escaneado:') !!}

                    <!-- Campo de archivo centrado y ocupando el 50% de la pantalla -->
                    <div style="display: flex; justify-content: center;">
                        {!! Form::file('pdf_fisico', ['class' => 'form-control', 'style' => 'width: 50%;']) !!}
                    </div>

                    <!-- Separación entre el campo de archivo y el botón -->
                    <div style="margin-top: 15px;">
                        {!! Form::submit('Subir PDF Físico', ['class' => 'btn btn-secondary']) !!}
                    </div>

                    {!! Form::close() !!}
                </div>
                    </div>
                @else
                    {{-- Si existe el documento físico, mostrarlo --}}
                    <div class="col-6">
                        <h4 class="text-center">Declaración Médica Física</h4>
                        <embed src="{{ asset('fichamedicaclientesbanco/' . $clientebanco->id . '/' . $declaracionFisico->document) }}" type="application/pdf" width="100%" height="400px" />
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="styleheet" href="/css/admin_custom.css">
    <style>
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
