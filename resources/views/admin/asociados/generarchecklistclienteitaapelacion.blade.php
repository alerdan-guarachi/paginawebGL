@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-subirrequisitos" href="{{ route('admin.asociados.subirdocrequisitosapelacion', $cliente) }}">SUBIR REQUISITOS</a>
<h5>DERIVACION Y REQUISITOS DE APELACIÓN:</h5>
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
@if (!$tieneRequisitos)
<div class="card"> 
    <div class="card-body">
        
        <div class="row">
            <div class="col-md-4">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">DOCUMENTACIÓN A PRESENTAR</h4>
                <div class="form-group">
                    <input type="hidden" name="poder_estado" id="poder_estado">
                    <input type="checkbox" name="poder" value="poder" id="poder" checked disabled>
                    <label for="poder">PODER</label>
                </div>
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
                @if ($numHijosMenores > 0 || $numHijosMenores !== null)
                    <div class="form-group">
                        <input type="checkbox" name="cnacjihos" value="cnacjihos" id="cnacjihos" checked>
                        <label for="cnacjihos" style="min-height: 20px;">CERTIFICADO NACIMIENTO HIJOS < 25</label>
                    </div>
                @endif
                @if ($numHijosMenores > 0 || $numHijosMenores !== null)
                    <div class="form-group">
                        <input type="checkbox" name="cihijos" value="cihijos" id="cihijos" checked>
                        <label for="cihijos" style="min-height: 20px;">CARNET IDENTIDAD HIJOS < 25</label>
                    </div>
                @endif
            </div>
            <div class="col-md-4">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">DOCUMENTACIÓN ADICIONAL</h4>
                <div class="form-group">
                    <input type="checkbox" name="egestora" value="egestora" id="egestora" checked disabled>
                    <label for="egestora" style="min-height: 20px;">EXTRACTO DE GESTORA</label>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="infomedicasalud" value="infomedicasalud" id="infomedicasalud" checked disabled>
                    <label for="infomedicasalud" style="min-height: 20px;">INFORMACIÓN MÉDICA</label>
                </div>
                
                <div class="form-group"> 
                    <input type="checkbox" name="denfaccidente" value="denfaccidente" id="denfaccidente">
                    <label for="denfaccidente" class="color-toggle">DENUNCIA ENFERMEDAD ACCIDENTE</label>
                </div>
                @if ($numHijosMenores > 0 || $numHijosMenores !== null)
                <div class="form-group">
                    <input type="checkbox" name="resolinvhijos" value="resolinvhijos" id="resolinvhijos">
                    <label for="resolinvhijos" class="color-toggle">RESOLUCIÓN INVALIDEZ DE HIJOS < 25</label>
                </div>
                @endif
                <script>
                    document.querySelectorAll('.color-toggle').forEach(label => {
                        label.addEventListener('dblclick', () => {
                            label.classList.toggle('black');
                        });
                    });
                </script>
            </div>
        </div>
        
        <button onclick="generatePDF(), generatePDF2()" class="btn-crear">GENERAR CHECK LIST</button>
        
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
@endif

<div class="card"> 
    <div class="card-body">
        <div class="row"> 
            <div class="col-lg-12">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px; margin-top: 20px;">ATENCIÓN MÉDICA</h4>
                @if (!$registroExistente && !$registroaprobadoExistente)
                
                <div class="row">
                    <!-- Primera Card -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm h-100" style="background-color: #fdf4e3;">
                            <div class="card-header text-center fw-bold text-white" style="background-color: #edab2e; font-weight:900; font-size:16px;">
                                DERIVAR A MEDICINA LABORAL
                            </div>
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                {!! Form::open(['route' => 'generar.pdf.consentimiento', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                                    {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                    {!! Form::hidden('nombres', $cliente->nombres) !!}
                                    {!! Form::hidden('apepaterno', $cliente->apepaterno) !!}
                                    {!! Form::hidden('apematerno', $cliente->apematerno) !!}
                                    {!! Form::hidden('ci', $cliente->ci) !!}
                                    {!! Form::hidden('sucursal', $cliente->sucursal) !!}
                                    {!! Form::hidden('tramite', 'APELACION') !!}
                                    
                                    <div class="form-group mb-3">
                                        <label for="proveedor_id" class="fw-bold d-flex justify-content-center w-100">Seleccionar Proveedor:</label>
                                        <select name="proveedorasignado" id="proveedor_id" class="form-control" required>
                                            <option value="" disabled selected></option>
                                            @foreach($proveedores as $proveedor)
                                                <option value="{{ $proveedor }}">{{ $proveedor }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                
                                    <div class="d-flex justify-content-center w-100">
                                        <button type="submit" class="btn btn-derivar px-4" id="submit-btn" disabled>DERIVAR</button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                
                    <!-- Segunda Card -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm h-100" style="background-color: #f2e8f5;">
                            <div class="card-header text-center fw-bold text-white" style="background-color: #b02eed; font-weight:900; font-size:16px;">
                                GENERAR SOLO CONSENTIMIENTO
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center"  style="display: flex; gap: 5px;">
                                {!! Form::open(['route' => 'generar.pdf.soloconsentimiento', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                                    <a class="btn btn-consen btn-sm float-right" href="#" onclick="event.preventDefault(); this.closest('form').submit();">GENERAR</a>
                                    {!! Form::hidden('clienteitaid', $cliente->id, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('nombres', $cliente->nombres, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('apepaterno', $cliente->apepaterno, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('apematerno', $cliente->apematerno, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('ci', $cliente->ci, ['class' => 'form-control']) !!}
                                    {!! Form::hidden('tramite', 'APELACION') !!}
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>

                    @if($rolusuario === 'MAESTRO' || $rolusuario === 'ADMINISTRADOR')
                        <div class="col-lg-4">
                            <div class="card shadow-sm h-100" style="background-color: #e0f4ff;">
                                <div class="card-header text-center fw-bold text-white" style="background-color: #2ea4ed; font-weight:900; font-size:16px;">
                                    APROBAR INICIAR BATERIA SIN CONSENTIMIENTO
                                </div>
                                <div class="card-body d-flex align-items-center justify-content-center" style="display: flex; gap: 5px;">
                                    {!! Form::open(['route' => 'aprobariniciarcrearbateria', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                                        {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                        {!! Form::hidden('nombres', $cliente->nombres) !!}
                                        {!! Form::hidden('apepaterno', $cliente->apepaterno) !!}
                                        {!! Form::hidden('apematerno', $cliente->apematerno) !!}
                                        {!! Form::hidden('tramite', 'APELACION') !!}
                                        
                                        <div class="d-flex justify-content-center w-100">
                                            <button type="submit" class="btn btn-aprobarbateria px-4">APROBAR</button>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        let proveedorSelect = document.getElementById("proveedor_id");
                        let submitBtn = document.getElementById("submit-btn");
                
                        proveedorSelect.addEventListener("change", function() {
                            submitBtn.disabled = (this.value === "");
                        });
                    });
                </script>
                @elseif ($registroExistente)
                    <p></p>
                @elseif ($registroaprobadoExistente)
                <div class="d-flex mt-3">
                    <div class="p-3 border rounded shadow-sm" style="background-color: #edffef; width: fit-content;">
                        <p class="m-0 fw-bold" style="font-weight: 900">APROBADO PARA CREAR BATERÍA</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if ($registroExistente && is_object($registroExistente))
        <div class="row justify-content-center my-5"> 
            <div class="col-lg-8 col-md-10">
                <div class="form-group bg-light p-5 rounded-lg border text-center">
                    <label class="d-block font-weight-bold mb-4 h5" style="font-weight: 600; color: #94c93b;">
                        Consentimiento informado para evaluación inicial
                    </label>
                    
                    @if ($registroExistente->document)
                        <div class="text-center">
                            <a href="{{ asset('cotizacionesaprobadasita/' . $cliente->id . '/' . $registroExistente->document) }}" 
                               class="btn btn-crear btn-lg px-5 py-2 font-weight-bold" target="_blank">
                                Ver Consentimiento
                            </a>
                        </div>
                    @else
                        {!! Form::open(['route' => 'guardar.pdf.consentimiento', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                            {!! Form::hidden('clienteitaid', $cliente->id) !!}
                            {!! Form::hidden('detalle', 'CARTA DE CONSENTIMIENTO INFORMADO PARA EVALUACIÓN Y DERIVACIÓN A ESPECIALISTAS') !!}
                            {!! Form::hidden('tramite', 'APELACION') !!}
        
                            <div class="mt-4">
                                {!! Form::file('pdf_file', ['id' => 'real-file', 'style' => 'display:none;']) !!}
                                <button type="button" class="btn btn-outline-primary" id="custom-button">Buscar archivo</button>
                            </div>
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

        <script>
            const realFileBtn = document.getElementById('real-file');
            const customBtn = document.getElementById('custom-button');
            const customTxt = document.getElementById('custom-text');
            customBtn.addEventListener('click', function() {
                realFileBtn.click();
            });
            realFileBtn.addEventListener('change', function() {
                if (realFileBtn.value) {
                    customTxt.innerHTML = realFileBtn.files[0].name;
                } else {
                    customTxt.innerHTML = "No se ha seleccionado ningún archivo";
                }
            });
        </script>
        @endif
    </div>
</div>

@stop
@section('css')
<style>
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
    .color-toggle {
        min-height: 20px;
        color: red;
        cursor: pointer;
    }
    .color-toggle.black {
        color: black;
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