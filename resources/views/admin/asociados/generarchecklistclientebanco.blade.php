@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclientebanco', $clientebanco) }}">REGRESAR</a>
{{-- <a class="btn btn-sm float-right btn-subirrequisitos" href="{{ route('admin.asociados.subirdocrequisitos', $clientebanco) }}">SUBIR REQUISITOS</a> --}}
    

<h5>CONSENTIMIENTO DE:</h5>
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
            <div class="col-lg-12">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px; margin-top: 20px;">CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS</h4>
                @if (!$registroExistente && !$registroaprobadoExistente)
                    <div class="form-group" style="display: flex; gap: 5px;">
                        {!! Form::open(['route' => 'generar.pdf.consentimientobanco', 'method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'consentimientoForm']) !!}
                            {!! Form::hidden('clientebancoid', $clientebanco->id, ['class' => 'form-control']) !!}
                            {!! Form::hidden('nombres', $clientebanco->nombrecompleto, ['class' => 'form-control']) !!}
                            {!! Form::hidden('ci', $clientebanco->ci, ['class' => 'form-control']) !!}
                            {!! Form::hidden('sucursal', $clientebanco->sucursal, ['class' => 'form-control']) !!}
                            {!! Form::submit('GENERAR CONSENTIMIENTO', ['class' => 'btn btn-derivar', 'id' => 'submitBtn']) !!}
                        {!! Form::close() !!}
                    </div>
                @elseif ($registroExistente)
                    <p></p>
                @elseif ($registroaprobadoExistente)
                    <p>APROBADO PARA CREAR BATERÍA</p>
                @endif
            </div>
        </div>
        
        <script>
            document.getElementById('consentimientoForm').addEventListener('submit', function() {
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            });
        </script>
        

        @if ($registroExistente && is_object($registroExistente))
        <div class="row justify-content-center my-5"> 
            <div class="col-lg-8 col-md-10">
                <div class="form-group bg-light p-5 rounded-lg border text-center">
                    <label class="d-block font-weight-bold mb-4 h5" style="font-weight: 600; color: #94c93b;">
                        Consentimiento informado
                    </label>
                    
                    @if ($registroExistente->document)
                        <div class="text-center">
                            <a href="{{ asset('cotizacionesaprobadasbanco/' . $clientebanco->id . '/' . $registroExistente->document) }}" 
                               class="btn btn-crear btn-lg px-5 py-2 font-weight-bold" target="_blank">
                                Ver
                            </a>
                        </div>
                    @else
                        {!! Form::open(['route' => 'guardar.pdf.consentimientobanco', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                            {!! Form::hidden('clientebancoid', $clientebanco->id) !!}
                            {!! Form::hidden('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS') !!}
        
                            <!-- Custom file input group centrado -->
                            <div class="mt-4">
                                <!-- Campo de archivo oculto -->
                                {!! Form::file('pdf_file', ['id' => 'real-file', 'style' => 'display:none;']) !!}
                                <!-- Botón visible personalizado centrado -->
                                <button type="button" class="btn btn-outline-primary" id="custom-button">Buscar archivo</button>
                            </div>
        
                            <!-- Texto del archivo seleccionado centrado debajo del botón -->
                            <div class="mt-3">
                                <span id="custom-text">No se ha seleccionado ningún archivo</span>
                            </div>
        
                            <div class="text-center mt-5">
                                {!! Form::submit('Guardar consentimiento', ['class' => 'btn btn-crear btn-lg px-5 py-2 font-weight-bold']) !!}
                            </div>
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
        
        <!-- JavaScript para gestionar la selección del archivo -->
        <script>
            const realFileBtn = document.getElementById('real-file');
            const customBtn = document.getElementById('custom-button');
            const customTxt = document.getElementById('custom-text');
        
            customBtn.addEventListener('click', function() {
                realFileBtn.click(); // Simula el clic en el input de archivo oculto
            });
        
            realFileBtn.addEventListener('change', function() {
                if (realFileBtn.value) {
                    customTxt.innerHTML = realFileBtn.files[0].name; // Muestra el nombre del archivo seleccionado
                } else {
                    customTxt.innerHTML = "No se ha seleccionado ningún archivo"; // Texto por defecto
                }
            });
        </script>
        
        
        
        @endif
    </div>
</div>


@stop
@section('css')
<style>
    .btn-generarpdf {
            background-color: #ffffff;
            /* Fondo blanco */
            color: #94c93b;
            /* Texto y borde verde */
            border-color: #94c93b;
            border-radius: 5px;
            padding: 10px 15px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-generarpdf:hover {
            background-color: #94c93b;
            /* Fondo verde al pasar el mouse */
            color: #ffffff;
            /* Texto blanco al pasar el mouse */
        }

    .color-toggle {
        min-height: 20px;
        color: red; /* Color inicial del texto */
        cursor: pointer; /* Cambia el cursor al pasar por encima para indicar que es clickeable */
    }
    .color-toggle.black {
        color: black; /* Color del texto al hacer doble clic */
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
    input[type="checkbox"] {
        transform: scale(1.5);
        margin-right: 5px;
        }
    input[type="checkbox"]:checked {
        background-color: green; /* Cambia el color de fondo a verde cuando el checkbox está marcado */
    }
    h1{
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
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
        .btn-derivar {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 15px;
        }
    
    .btn-derivar:hover {
        background-color: #faa625;
        color: #ffffff;
        }
        .btn-aprobarbateria {
        background-color:  #ffffff;
        color: #25b6fa;
        border-color: #25b6fa;
        border-radius: 5px;
        padding: 10px 15px;
        }
    
    .btn-aprobarbateria:hover {
        background-color: #25b6fa;
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
    .btn-subirrequisitos {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 10px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-subirrequisitos:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-consen {
        background-color: #ffffff;
        color: #af25fa;
        border-color: #af25fa;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-consen:hover {
        background-color: #af25fa;
        color: #ffffff;
    }
</style>
@stop