@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-subirrequisitos" href="{{ route('admin.asociados.subirdocrequisitosapelacion', $cliente) }}">SUBIR REQUISITOS</a>
<h5>REQUISITOS DE APELACIÓN:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('content')
    <form id="pdfForm" action="{{ route('admin.asociados.descargarchecklistclienteitaapelacion', $cliente) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="documentosSeleccionados" id="documentosSeleccionadosInput">
        <input type="hidden" name="documentosSeleccionados2" id="documentosSeleccionados2Input">
    </form>
</div>

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

            <div class="col-md-4">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">DOCUMENTACIÓN A PRESENTAR</h4>
                {{-- <div class="form-group">
                    <input type="hidden" name="poder_estado" id="poder_estado">
                    <input type="checkbox" name="poder" value="poder" id="poder" checked disabled>
                    <label for="poder">PODER</label>
                </div> --}}
                @if (strtolower($estadoLaboral) === 'activo')
                <div class="form-group">
                    <input type="checkbox" name="avcci" value="avcci" id="avcci" checked>
                    <label for="avcci">AVC/CARNET ASEGURADO</label>
                </div>
                @endif
                <div class="form-group">
                    <input type="checkbox" name="cnacasegurado" value="cnacasegurado" id="cnacasegurado" checked disabled>
                    <label for="cnacasegurado" style="min-height: 20px;">CERTIFICADO NACIMIENTO ASEGURADO</label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="ciasegurado" value="ciasegurado" id="ciasegurado" checked disabled>
                    <label for="ciasegurado" style="min-height: 20px;">CARNET IDENTIDAD ASEGURADO</label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="crodomicilio" value="crodomicilio" id="crodomicilio" checked disabled>
                    <label for="crodomicilio" style="min-height: 20px;">CROQUIS DE DOMICILIO</label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="dictamencalentenc" value="dictamencalentenc" id="dictamencalentenc" checked disabled>
                    <label for="dictamencalentenc" style="min-height: 20px;">DICTAMEN CALIFICACION ENTIDAD ENCARGADA</label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="egestora" value="egestora" id="egestora">
                    <label for="egestora" style="min-height: 20px;">EXTRACTO DE GESTORA</label>
                </div>
            </div>
            <div class="col-md-4" style="margin-top: 47px;">
                @if (strtolower($estadoCivil) === 'casad@')
                    <div class="form-group">
                        <input type="checkbox" name="cmatrimonio" value="cmatrimonio" id="cmatrimonio" checked>
                        <label for="cmatrimonio" style="min-height: 20px;">CERTIFICADO DE MATRIMONIO</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'casad@')
                    <div class="form-group">
                        <input type="checkbox" name="cnacconyuge" value="cnacconyuge" id="cnacconyuge" checked>
                        <label for="cnacconyuge" style="min-height: 20px;">CERTIFICADO NACIMIENTO CONYUGE</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'union libre')
                    <div class="form-group">
                        <input type="checkbox" name="cunionlibre" value="cunionlibre" id="cunionlibre" checked>
                        <label for="cunionlibre" style="min-height: 20px;">CERTIFICADO DE UNIÓN LIBRE</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'union libre')
                    <div class="form-group">
                        <input type="checkbox" name="cnacimientounionlibre" value="cnacimientounionlibre" id="cnacimientounionlibre" checked>
                        <label for="cnacimientounionlibre" style="min-height: 20px;">CERTIFICADO NACIMIENTO DE UNIÓN LIBRE</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'divorciad@')
                    <div class="form-group">
                        <input type="checkbox" name="cdivorcio" value="cdivorcio" id="cdivorcio" checked>
                        <label for="cdivorcio" style="min-height: 20px;">CERTIFICADO DE DIVORCIO</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'viud@')
                    <div class="form-group">
                        <input type="checkbox" name="cdefuncion" value="cdefuncion" id="cdefuncion" checked>
                        <label for="cdefuncion" style="min-height: 20px;">CERTIFICADO DE DIFUNCIÓN</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'casad@')
                    <div class="form-group">
                        <input type="checkbox" name="ciconyuge" value="ciconyuge" id="ciconyuge" checked>
                        <label for="ciconyuge" style="min-height: 20px;">CARNET IDENTIDAD CONYUGE</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'union libre')
                    <div class="form-group">
                        <input type="checkbox" name="ciunionlibre" value="ciunionlibre" id="ciunionlibre" checked>
                        <label for="ciunionlibre" style="min-height: 20px;">CARNET IDENTIDAD DE UNIÓN LIBRE</label>
                    </div>
                @endif
                @if ($numHijosMenores > 0 || $numHijosMenores === null)
                    <div class="form-group">
                        <input type="checkbox" name="cnacjihos" value="cnacjihos" id="cnacjihos" checked>
                        <label for="cnacjihos" style="min-height: 20px;">CERTIFICADO NACIMIENTO HIJOS < 25</label>
                    </div>
                @endif
                @if ($numHijosMenores > 0 || $numHijosMenores === null)
                    <div class="form-group">
                        <input type="checkbox" name="cihijos" value="cihijos" id="cihijos" checked>
                        <label for="cihijos" style="min-height: 20px;">CARNET IDENTIDAD HIJOS < 25</label>
                    </div>
                @endif
                
            </div>
            <div class="col-md-4">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">DOCUMENTACIÓN ADICIONAL</h4>
                <div class="form-group">
                    <input type="checkbox" name="infomedicasalud" value="infomedicasalud" id="infomedicasalud" checked disabled>
                    <label for="infomedicasalud" style="min-height: 20px;">INFORMACIÓN MÉDICA</label>
                </div>
                
                <div class="form-group"> 
                    <input type="checkbox" name="denfaccidente" value="denfaccidente" id="denfaccidente">
                    <label for="denfaccidente" class="color-toggle">DENUNCIA ENFERMEDAD ACCIDENTE</label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="resolinvhijos" value="resolinvhijos" id="resolinvhijos">
                    <label for="resolinvhijos" class="color-toggle">RESOLUCIÓN INVALIDEZ DE HIJOS < 25</label>
                </div>
                
                <script>
                    document.querySelectorAll('.color-toggle').forEach(label => {
                        label.addEventListener('dblclick', () => {
                            label.classList.toggle('black');
                        });
                    });
                </script>
            </div>
        </div>
        @if (!$tieneRequisitos)
        <button onclick="generatePDF(), generatePDF2()" class="btn-crear">GENERAR CHECK LIST</button>
        @endif
        <script>
            function generatePDF() {
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                var documentosSeleccionados = [];
                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        documentosSeleccionados.push(checkbox.value);
                    }
                });
                document.getElementById('documentosSeleccionadosInput').value = JSON.stringify(documentosSeleccionados);
                document.getElementById('pdfForm').submit();
            }

            function generatePDF2() {
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                var documentosSeleccionados2 = [];
                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        documentosSeleccionados2.push(checkbox.value);
                    }
                });
                document.getElementById('documentosSeleccionados2Input').value = JSON.stringify(documentosSeleccionados2);
                document.getElementById('pdfForm').submit();
            }
        </script>
    </div>
</div>
{{-- <div class="card"> 
    <div class="card-body">
        <div class="row"> 
            <div class="col-lg-12">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">ATENCIÓN MÉDICA</h4>
                @if (!$registroExistente && !$registroaprobadoExistente)
                    <div class="form-group" style="display: flex; gap: 5px;">
                        {!! Form::open(['route' => 'generar.pdf.consentimiento', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                            {!! Form::hidden('clienteitaid', $cliente->id, ['class' => 'form-control']) !!}
                            {!! Form::hidden('nombres', $cliente->nombres, ['class' => 'form-control']) !!}
                            {!! Form::hidden('apepaterno', $cliente->apepaterno, ['class' => 'form-control']) !!}
                            {!! Form::hidden('apematerno', $cliente->apematerno, ['class' => 'form-control']) !!}
                            {!! Form::submit('DERIVAR A MEDICINA LABORAL', ['class' => 'btn btn-derivar']) !!}
                        {!! Form::close() !!}
                    </div>

                    <div class="form-group" style="display: flex; gap: 5px;">
                        {!! Form::open(['route' => 'aprobariniciarcrearbateria', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                            {!! Form::hidden('clienteitaid', $cliente->id, ['class' => 'form-control']) !!}
                            {!! Form::hidden('nombres', $cliente->nombres, ['class' => 'form-control']) !!}
                            {!! Form::hidden('apepaterno', $cliente->apepaterno, ['class' => 'form-control']) !!}
                            {!! Form::hidden('apematerno', $cliente->apematerno, ['class' => 'form-control']) !!}

                            {!! Form::submit('APROBAR INICIAR BATERIA', [
                                'class' => 'btn btn-aprobarbateria', 
                                'hidden' => !($rolusuario === 'MAESTRO' || $rolusuario === 'ADMINISTRADOR')
                            ]) !!}
                        {!! Form::close() !!}
                    </div>
                @elseif ($registroExistente)
                    <p></p>
                @elseif ($registroaprobadoExistente)
                    <p>APROBADO PARA CREAR BATERÍA</p>
                @endif

            </div>
        </div>

        @if ($registroExistente && is_object($registroExistente))
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        @if ($registroExistente->document)
                            <label>Consentimiento informado para evaluación inicial:</label>
                            <a href="{{ asset('cotizacionesaprobadasita/' . $cliente->id . '/' . $registroExistente->document) }}" class="btn btn-crear" target="_blank">Ver Consentimiento</a>
                        @else
                            <label>Consentimiento informado para evaluación inicial:</label>
                            {!! Form::open(['route' => 'guardar.pdf.consentimiento', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                {!! Form::hidden('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS') !!}
                                {!! Form::file('pdf_file', ['class' => 'dropify', 'data-height' => '150']) !!}
                                <div class="mt-3">
                                    {!! Form::submit('Guardar consentimiento', ['class' => 'btn btn-crear']) !!}
                                </div>
                            {!! Form::close() !!}
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div> --}}


@stop
@section('css')
<style>
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
</style>
@stop


{{-- @extends('adminlte::page')

@section('content_header')
<h1>Check List</h1>
@stop

@section('content')
<body>
    <table>
        <caption>DOCUMENTACIÓN A PRESENTAR</caption>
        <tr>
            <th>PODER</th>
            <th>AVC/CARNET ASEGURADO</th>
            <th>CERTIFICADO NACIMIENTO ASEGURADO</th>
            <th>CARNET IDENTIDAD ASEGURADO</th>
            <th>CERTIFICADO DE MATRIMONIO</th>
            <th>CERTIFICADO NACIMIENTO CONYUGE</th>
            <th>CARNET IDENTIDAD CONYUGE</th>
            <th>CERTIFICADO NACIMIENTO HIJOS &lt; 25</th>
            <th>CARNET IDENTIDAD HIJOS &lt; 25</th>
            <th>DENUNCIA ENFERMEDAD ACCIDENTE</th>
            <th>CROQUIS DE DOMICILIO</th>
            <th>CONTRATO</th>
        </tr>
        <tr id="documentos">
            <td><input type="checkbox" name="documentos[]" value="poder"></td>
            <td><input type="checkbox" name="documentos[]" value="avc"></td>
            <td><input type="checkbox" name="documentos[]" value="certificado_nacimiento_asegurado"></td>
            <td><input type="checkbox" name="documentos[]" value="carnet_identidad_asegurado"></td>
            <td><input type="checkbox" name="documentos[]" value="certificado_matrimonio"></td>
            <td><input type="checkbox" name="documentos[]" value="certificado_nacimiento_conyuge"></td>
            <td><input type="checkbox" name="documentos[]" value="carnet_identidad_conyuge"></td>
            <td><input type="checkbox" name="documentos[]" value="certificado_nacimiento_hijos"></td>
            <td><input type="checkbox" name="documentos[]" value="carnet_identidad_hijos"></td>
            <td><input type="checkbox" name="documentos[]" value="denuncia_enfermedad_accidente"></td>
            <td><input type="checkbox" name="documentos[]" value="croquis_domicilio"></td>
            <td><input type="checkbox" name="documentos[]" value="contrato"></td>
        </tr>
    </table>
    <form id="pdfForm" action="{{ route('admin.clientes.print2', $cliente) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="documentosSeleccionados" id="documentosSeleccionadosInput">
    </form>
    
    <button onclick="generatePDF()">Generar PDF</button>

    <script>
        function generatePDF() {
            var checkboxes = document.querySelectorAll('#documentos input[type="checkbox"]');
            var documentosSeleccionados = [];
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    documentosSeleccionados.push(checkbox.value);
                }
            });
            document.getElementById('documentosSeleccionadosInput').value = JSON.stringify(documentosSeleccionados);
            // Ahora envía el formulario
            document.getElementById('pdfForm').submit();
        }
    </script>
    
</body>
</html>
@endsection
@section('css')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }
    caption {
        caption-side: top;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 20px;
    }
</style>
@stop --}}