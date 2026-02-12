@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-subirrequisitos" href="{{ route('admin.asociados.subirdocrequisitossegsolicitud', $cliente) }}">SUBIR REQUISITOS</a>
<h5>DERIVACIÓN Y REQUISITOS DE SEGUNDA SOLICITUD:</h5>
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
        <input type="hidden" name="tramitecliente" id="tramitecliente" value="SEGUNDA SOLICITUD">
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
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="avcci" value="avcci" id="avcci" checked disabled>
                    <label for="avcci">AVC/CARNET ASEGURADO</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="cnacasegurado" value="cnacasegurado" id="cnacasegurado" checked disabled>
                    <label for="cnacasegurado" style="min-height: 20px;">CERTIFICADO NACIMIENTO ASEGURADO</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="ciasegurado" value="ciasegurado" id="ciasegurado" checked disabled>
                    <label for="ciasegurado" style="min-height: 20px;">CARNET IDENTIDAD ASEGURADO</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="crodomicilio" value="crodomicilio" id="crodomicilio" checked disabled>
                    <label for="crodomicilio" style="min-height: 20px;">CROQUIS DE DOMICILIO</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="egestora" value="egestora" id="egestora">
                    <label for="egestora" style="min-height: 20px;">EXTRACTO DE GESTORA</label>
                </div>
                <div class="form-group" style="margin-top: -10px;">
                    <input type="checkbox" name="contrato" value="contrato" id="contrato" checked>
                    <label for="contrato" style="min-height: 20px;">CONTRATO</label>
                </div>
            </div>
            <div class="col-md-4" style="margin-top: 55px;">
                @if (strtolower($estadoCivil) === 'casad@')
                    <div class="form-group">
                        <input type="checkbox" name="cmatrimonio" value="cmatrimonio" id="cmatrimonio" checked>
                        <label for="cmatrimonio" style="min-height: 20px;">CERTIFICADO DE MATRIMONIO</label>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="checkbox" name="cnacconyuge" value="cnacconyuge" id="cnacconyuge" checked>
                        <label for="cnacconyuge" style="min-height: 20px;">CERTIFICADO NACIMIENTO CONYUGE</label>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="checkbox" name="ciconyuge" value="ciconyuge" id="ciconyuge" checked>
                        <label for="ciconyuge" style="min-height: 20px;">CARNET IDENTIDAD CONYUGE</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'union libre')
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="checkbox" name="cunionlibre" value="cunionlibre" id="cunionlibre" checked>
                        <label for="cunionlibre" style="min-height: 20px;">CERTIFICADO DE UNIÓN LIBRE</label>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="checkbox" name="cnacimientounionlibre" value="cnacimientounionlibre" id="cnacimientounionlibre" checked>
                        <label for="cnacimientounionlibre" style="min-height: 20px;">CERTIFICADO NACIMIENTO DE UNIÓN LIBRE</label>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="checkbox" name="ciunionlibre" value="ciunionlibre" id="ciunionlibre" checked>
                        <label for="ciunionlibre" style="min-height: 20px;">CARNET IDENTIDAD DE UNIÓN LIBRE</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'divorciad@')
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="checkbox" name="cdivorcio" value="cdivorcio" id="cdivorcio" checked>
                        <label for="cdivorcio" style="min-height: 20px;">CERTIFICADO DE DIVORCIO</label>
                    </div>
                @endif
                @if (strtolower($estadoCivil) === 'viud@')
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="checkbox" name="cdefuncion" value="cdefuncion" id="cdefuncion" checked>
                        <label for="cdefuncion" style="min-height: 20px;">CERTIFICADO DE DEFUNCIÓN</label>
                    </div>
                @endif
                @if ($numHijosMenores > 0 || $numHijosMenores !== null)
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="checkbox" name="cnacjihos" value="cnacjihos" id="cnacjihos" checked>
                        <label for="cnacjihos" style="min-height: 20px;">CERTIFICADO NACIMIENTO HIJOS < 25</label>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <input type="checkbox" name="cihijos" value="cihijos" id="cihijos" checked>
                        <label for="cihijos" style="min-height: 20px;">CARNET IDENTIDAD HIJOS < 25</label>
                    </div>
                @endif
            </div>
            <div class="col-md-4">
                <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">DOCUMENTACIÓN ADICIONAL</h4>
                <div class="form-group">
                    <input type="checkbox" name="anteriordictamen" value="anteriordictamen" id="anteriordictamen">
                    <label for="anteriordictamen" style="min-height: 20px;">ANTERIOR DICTAMEN O RESOLUCION</label>
                </div>
                <div class="form-group" style="margin-top: -10px;"> 
                    <input type="checkbox" name="denfaccidente" value="denfaccidente" id="denfaccidente">
                    <label for="denfaccidente" class="color-toggle">DENUNCIA ENFERMEDAD ACCIDENTE</label>
                </div>
                @if ($numHijosMenores > 0 || $numHijosMenores !== null)
                <div class="form-group" style="margin-top: -10px;">
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
                                    {!! Form::hidden('tramite', 'SEGUNDA SOLICITUD') !!}
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
                    @if($rolusuario === 'MAESTRO' || $rolusuario === 'ADMINISTRADOR')
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
                                    {!! Form::hidden('tramite', 'SEGUNDA SOLICITUD') !!}
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    @endif
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
                                        {!! Form::hidden('tramite', 'SEGUNDA SOLICITUD') !!}
                                        
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
                                        {!! Form::hidden('tramite', 'SEGUNDA SOLICITUD') !!}
                                        
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
                                        {!! Form::hidden('tramite', 'SEGUNDA SOLICITUD') !!}
                    
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

            <div class="col-lg-12">
                <div class="col-lg-3">
                    <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px; margin-top: 20px;">RECOMENDACIONES BATERIA</h4>
                    <div class="card shadow-sm h-100" style="background-color: #f5e8f3;">
                        <div class="card-header text-center fw-bold text-white"
                            style="background-color: #ed2eed; font-weight:900; font-size:16px;">
                            RECOMENDAR ESTUDIOS/ESPECIALIDADES
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center" style="display: flex; gap: 5px;">
                            <a class="btn btn-consen1 btn-sm float-right" data-toggle="modal" data-target="#modalRecomendar">
                                RECOMENDAR
                            </a>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalRecomendar" tabindex="-1" role="dialog" aria-labelledby="modalRecomendarLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" style="font-weight: 700; color: #94c93b;">RECOMENDAR ESTUDIOS / ESPECIALIDADES</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body"> 
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <label>CREAR NUEVAS RECOMENDACIONES</label>
                                                <div class="row mb-3">
                                                    <div class="col-md-3">
                                                        <label for="tipo_area">Tipo de Área</label>
                                                        <select id="tipo_area" class="form-control">
                                                            <option value="" disabled selected>Seleccione...</option>
                                                            <option value="ESPECIALIDAD">ESPECIALIDAD</option>
                                                            <option value="ESTUDIO">ESTUDIO</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="area">Área</label>
                                                        <select id="area" class="form-control" disabled>
                                                            <option value="" disabled selected>Seleccione un área</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="accion">Acción</label>
                                                        <select id="accion" class="form-control" disabled>
                                                            <option value="" disabled selected>Seleccione una acción</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-end">
                                                        <button type="button" id="btnAgregar" class="btn btn-outline-success btn-sm w-100"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped table-sm" id="tablaRecomendaciones">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Tipo_Área</th>
                                                                <th>Área</th>
                                                                <th>Estudio/Especialidad</th>
                                                                <th class="text-center">Quitar</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <form id="formRecomendar" method="POST" action="{{ route('admin.asociados.recomendarestudioespecialidad') }}">
                                                    @csrf
                                                    <input type="hidden" name="recomendaciones" id="inputRecomendaciones">
                                                    <input type="hidden" name="tramiterecomendacion" value="SEGUNDA SOLICITUD">
                                                    <input type="hidden" name="clienteid" value="{{$cliente->id}}">
                                                    <input type="hidden" name="clientenombre" value="{{$cliente->nombrecompleto}}">
                                                    <button type="submit" class="btn btn-crear btn-sm">GUARDAR</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <label>LISTA DE RECOMENDACIONES</label>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Reg.</th>
                                                                <th>Tipo_Área</th>
                                                                <th>Área</th>
                                                                <th>Estudio/Especialidad</th>
                                                                <th>Fecha_Registro</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $conCheck = [];
                                                                $sinCheck = [];
                                                            @endphp

                                                            @foreach($ultimosRegistros as $rec)
                                                                @php
                                                                    $existe = \DB::table('bateriasubclientes as b')
                                                                        ->join('tramitessubclientes as t', function($join) {
                                                                            $join->on('t.clienteitaid', '=', 'b.clienteitaid')
                                                                                ->on('t.fechabateria', '=', 'b.fechabateria');
                                                                        })
                                                                        ->where('b.clienteitaid', $cliente->id)
                                                                        ->where('b.accionnombre', $rec->estudioespecialidad)
                                                                        ->where('t.tramite', 'SEGUNDA SOLICITUD')
                                                                        ->exists();
                                                                @endphp

                                                                @if($existe)
                                                                    @php $conCheck[] = $rec; @endphp
                                                                @else
                                                                    @php $sinCheck[] = $rec; @endphp
                                                                @endif
                                                            @endforeach

                                                            @forelse($sinCheck as $rec)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <i class="fas fa-times-circle text-danger"></i>
                                                                    </td>
                                                                    <td>{{ $rec->tipoarea }}</td>
                                                                    <td>{{ $rec->area }}</td>
                                                                    <td>{{ $rec->estudioespecialidad }}</td>
                                                                    <td>{{ $rec->created_at->format('Y-m-d') }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="5">NO HAY RECOMENDACIONES REGISTRADAS</td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>

                                                        <tbody id="tbody-checks" style="display: none;">
                                                            @foreach($conCheck as $rec)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <i class="fas fa-check-circle text-success"></i>
                                                                    </td>
                                                                    <td>{{ $rec->tipoarea }}</td>
                                                                    <td>{{ $rec->area }}</td>
                                                                    <td>{{ $rec->estudioespecialidad }}</td>
                                                                    <td>{{ $rec->created_at->format('Y-m-d') }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="text-center mt-2">
                                                    <button type="button" id="btnVerChecks" class="btn btn-subirrequisitos btn-sm">
                                                        VER EST/ESP. REGISTRADOS
                                                    </button>
                                                </div>

                                                <script>
                                                    document.getElementById('btnVerChecks').addEventListener('click', function() {
                                                        const tbodyChecks = document.getElementById('tbody-checks');
                                                        if (tbodyChecks.style.display === 'none') {
                                                            tbodyChecks.style.display = '';
                                                            this.textContent = 'OCULTAR EST/ESP. REGISTRADOS';
                                                        } else {
                                                            tbodyChecks.style.display = 'none';
                                                            this.textContent = 'VER EST/ESP. REGISTRADOS';
                                                        }
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    let bateriaProveedores = @json($bateriaProveedores);
                    document.getElementById('tipo_area').addEventListener('change', function () {
                        let tipo = this.value;
                        let areasSelect = document.getElementById('area');
                        let accionesSelect = document.getElementById('accion');
                        areasSelect.innerHTML = '<option value="" disabled selected>Seleccione un área</option>';
                        accionesSelect.innerHTML = '<option value="" disabled selected>Seleccione una acción</option>';
                        accionesSelect.disabled = true;

                        let areas = [...new Set(
                            bateriaProveedores
                                .filter(bp => bp.tipoarea === tipo)
                                .map(bp => bp.area)
                        )].sort();
                        areas.forEach(area => {
                            let option = document.createElement('option');
                            option.value = area;
                            option.textContent = area;
                            areasSelect.appendChild(option);
                        });
                        areasSelect.disabled = false;
                    });

                    document.getElementById('area').addEventListener('change', function () {
                        let area = this.value;
                        let tipo = document.getElementById('tipo_area').value;
                        let accionesSelect = document.getElementById('accion');
                        accionesSelect.innerHTML = '<option value="" disabled selected>Seleccione una acción</option>';
                        let acciones = [...new Set(
                            bateriaProveedores
                                .filter(bp => bp.tipoarea === tipo && bp.area === area)
                                .map(bp => bp.accion)
                        )].sort();
                        acciones.forEach(accion => {
                            let option = document.createElement('option');
                            option.value = accion;
                            option.textContent = accion;
                            accionesSelect.appendChild(option);
                        });
                        accionesSelect.disabled = false;
                    });
                </script>
                <script>
                    let recomendaciones = [];
                    document.getElementById('btnAgregar').addEventListener('click', function () {
                        let tipo = document.getElementById('tipo_area').value;
                        let area = document.getElementById('area').value;
                        let accion = document.getElementById('accion').value;
                        if (!tipo || !area || !accion) {
                            alert("Debe seleccionar todos los campos.");
                            return;
                        }
                        recomendaciones.push({ tipo, area, accion });
                        renderTabla();
                    });

                    function renderTabla() {
                        let tbody = document.querySelector("#tablaRecomendaciones tbody");
                        tbody.innerHTML = "";
                        recomendaciones.forEach((rec, index) => {
                            let row = `
                                <tr>
                                    <td>${rec.tipo}</td>
                                    <td>${rec.area}</td>
                                    <td>${rec.accion}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarRecomendacion(${index})"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                        document.getElementById('inputRecomendaciones').value = JSON.stringify(recomendaciones);
                    }
                    function eliminarRecomendacion(index) {
                        recomendaciones.splice(index, 1);
                        renderTabla();
                    }
                </script>
                <style>
                    .btn-consen1 {
                        background-color: #ffffff;
                        color: #ed2eed;
                        border-color: #ed2eed;
                        border-radius: 5px;
                        padding: 10px 10px;
                    }
                    .btn-consen1:hover {
                        background-color: #ed2eed;
                        color: #ffffff;
                    }
                </style>
            </div>
        </div>
    </div>
</div>

@stop