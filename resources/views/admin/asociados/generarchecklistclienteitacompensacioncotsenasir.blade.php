@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-subirrequisitos" href="{{ route('admin.asociados.subirdocrequisitoscompensacioncotsenasir', $cliente) }}">SUBIR REQUISITOS</a>
<h5>REQUISITOS DE COMPENSACIÓN DE COTIZACIONES (SENASIR):</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/requisitosgeneral.css') }}">
<style>
    h3 {
    font-size: 23px;
    }
</style>
@stop

@section('content')
    <form id="pdfForm" action="{{ route('admin.asociados.descargarchecklistclienteita', $cliente) }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="documentosSeleccionados" id="documentosSeleccionadosInput">
        <input type="hidden" name="documentosSeleccionados2" id="documentosSeleccionados2Input">
        <input type="hidden" name="tramitecliente" id="tramitecliente" value="COMPENSACIÓN DE COTIZACIONES (SENASIR)">
    </form>
</div>

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
@if (!$tieneRequisitos)
<div class="card"> 
    <div class="card-body">    
        <div class="row">
            <div class="col-md-4">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">DOCUMENTACIÓN A PRESENTAR</h4>
                <div class="form-group">
                    <input type="hidden" name="poder_estado" id="poder_estado">
                    <input type="checkbox" name="poder" value="poder" id="poder">
                    <label for="poder">PODER Y CARNET IDENTIDAD APODERADO</label>
                </div>
                @if (strtolower($estadoLaboral) === 'activo')
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="avcci" value="avcci" id="avcci" checked>
                    <label for="avcci">AVC/CARNET ASEGURADO</label>
                </div>
                @endif
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="cnacasegurado" value="cnacasegurado" id="cnacasegurado" checked>
                    <label for="cnacasegurado" style="min-height: 20px;">CERTIFICADO NACIMIENTO ASEGURADO</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="ciasegurado" value="ciasegurado" id="ciasegurado" checked>
                    <label for="ciasegurado" style="min-height: 20px;">CARNET IDENTIDAD ASEGURADO</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="crodomicilio" value="crodomicilio" id="crodomicilio" checked>
                    <label for="crodomicilio" style="min-height: 20px;">CROQUIS DE DOMICILIO</label>
                </div>
            </div>
            <div class="col-md-4" style="margin-top: 55px;">
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="boletapago" value="boletapago" id="boletapago" checked>
                    <label for="boletapago" style="min-height: 20px;">BOLETA DE PAGO</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="csalarioaportes" value="csalarioaportes" id="csalarioaportes" checked>
                    <label for="csalarioaportes" style="min-height: 20px;">CERTIFICADO DE SALARIOS Y APORTES</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="fotofrojoasegurado" value="fotofrojoasegurado" id="fotofrojoasegurado" checked>
                    <label for="fotofrojoasegurado" style="min-height: 20px;">ASEGURADO FOTO 4X4 FONDO ROJO</label>
                </div>
            </div>
            <div class="col-md-4">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">DOCUMENTACIÓN ADICIONAL</h4>
                <div class="form-group">
                    <input type="checkbox" name="egestora" value="egestora" id="egestora" checked>
                    <label for="egestora" style="min-height: 20px;">EXTRACTO DE GESTORA</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="fotofrojoapoderadocroquis" value="fotofrojoapoderadocroquis" id="fotofrojoapoderadocroquis" checked>
                    <label for="fotofrojoapoderadocroquis" style="min-height: 20px;">APOD. FOTO 4X4 FONDO ROJO + CROQUIS DOM.</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="csalarioaporteslegalizada" value="csalarioaporteslegalizada" id="csalarioaporteslegalizada" checked>
                    <label for="csalarioaporteslegalizada" style="min-height: 20px;">CERTIFICADO DE SALARIOS Y APORTES (LEGALIZADO)</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="finiquito" value="finiquito" id="finiquito" checked>
                    <label for="finiquito" style="min-height: 20px;">FINIQUITO</label>
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

{{-- <div class="card"> 
    <div class="card-body">
        <div class="row"> 
            <div class="col-lg-12">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px; margin-top: 20px;">ATENCIÓN MÉDICA</h4>
                @if (!$registroExistente && !$registroaprobadoExistente && !$registroaprobadoinformefinalExistente)
                
                <div class="row">
                    <div class="col-lg-3">
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
                                    {!! Form::hidden('tramite', 'COMPENSACIÓN DE COTIZACIONES (SENASIR)') !!}
                                    
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
                
                    <div class="col-lg-3">
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
                                    {!! Form::hidden('tramite', 'COMPENSACIÓN DE COTIZACIONES (SENASIR)') !!}
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>

                    @if($rolusuario === 'MAESTRO' || $rolusuario === 'ADMINISTRADOR')
                        <div class="col-lg-3">
                            <div class="card shadow-sm h-100" style="background-color: #e0f4ff;">
                                <div class="card-header text-center fw-bold text-white" style="background-color: #2ea4ed; font-weight:900; font-size:16px;">
                                    INICIAR BATERIA SIN CONSENTIMIENTO
                                </div>
                                <div class="card-body d-flex align-items-center justify-content-center" style="display: flex; gap: 5px;">
                                    {!! Form::open(['route' => 'aprobariniciarcrearbateria', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                                        {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                        {!! Form::hidden('nombres', $cliente->nombres) !!}
                                        {!! Form::hidden('apepaterno', $cliente->apepaterno) !!}
                                        {!! Form::hidden('apematerno', $cliente->apematerno) !!}
                                        {!! Form::hidden('tramite', 'COMPENSACIÓN DE COTIZACIONES (SENASIR)') !!}
                                        
                                        <div class="d-flex justify-content-center w-100">
                                            <button type="submit" class="btn btn-aprobarbateria px-4">APROBAR</button>
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card shadow-sm h-100" style="background-color: #eaffef;">
                                <div class="card-header text-center fw-bold text-white" style="background-color: #27c451; font-weight:900; font-size:16px;">
                                    INFORME FINAL DIRECTO
                                </div>
                                <div class="card-body d-flex align-items-center justify-content-center" style="display: flex; gap: 5px;">
                                    {!! Form::open(['route' => 'aprobarinformefinaldirecto', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                                        {!! Form::hidden('clienteitaid', $cliente->id) !!}
                                        {!! Form::hidden('nombres', $cliente->nombres) !!}
                                        {!! Form::hidden('apepaterno', $cliente->apepaterno) !!}
                                        {!! Form::hidden('apematerno', $cliente->apematerno) !!}
                                        {!! Form::hidden('tramite', 'COMPENSACIÓN DE COTIZACIONES (SENASIR)') !!}
                                        
                                        <div class="d-flex justify-content-center w-100">
                                            <button type="submit" class="btn btn-informefinal px-4">APROBAR</button>
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
                    <div class="p-3 border rounded shadow-sm" style="background-color: #e0f4ff; width: fit-content;">
                        <p class="m-0 fw-bold" style="font-weight: 900">APROBADO PARA CREAR BATERÍA</p>
                    </div>
                </div>
                @elseif ($registroaprobadoinformefinalExistente)
                <div class="d-flex mt-3">
                    <div class="p-3 border rounded shadow-sm" style="background-color: #edffef; width: fit-content;">
                        <p class="m-0 fw-bold" style="font-weight: 900">APROBADO PARA INFORME FINAL DIRECTO</p>
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
                            {!! Form::hidden('tramite', 'COMPENSACIÓN DE COTIZACIONES (SENASIR)') !!}
        
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
</div> --}}

@stop