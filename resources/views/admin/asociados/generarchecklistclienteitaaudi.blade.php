@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-subirrequisitos" href="{{ route('admin.asociados.subirdocrequisitosaudi', $cliente) }}">SUBIR REQUISITOS</a>
<h5>DERIVACION Y REQUISITOS DE AUDITORIA MEDICA:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('content') 
@if (!$tieneRequisitos)
<form id="pdfForm" action="{{ route('admin.asociados.descargarchecklistclienteitaaudi', $cliente) }}" method="POST">
    @csrf
    <input type="hidden" name="documentosSeleccionados" id="documentosSeleccionadosInput">
    <input type="hidden" name="documentosSeleccionados2" id="documentosSeleccionados2Input">

    <div class="card col-lg-12"> 
        <div class="card-body">
            <div class="row">
                @if ($tieneauditoriamedica)
                <div class="col-md-12">
                    <h4 style="font-weight: 600; color: #94c93b; margin-bottom: 20px;">DOCUMENTACIÓN A PRESENTAR</h4>
                    <div class="form-group">
                        <input type="checkbox" name="ciasegurado" value="ciasegurado" id="ciasegurado" checked>
                        <label for="ciasegurado">CARNET IDENTIDAD</label>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="cnacasegurado" value="cnacasegurado" id="cnacasegurado" checked>
                        <label for="cnacasegurado" style="min-height: 20px;">CERTIFICADO NACIMIENTO ASEGURADO</label>
                    </div>
                </div>
                <div class="col-lg-12">
                    <h4 style="font-weight: 600; color: #94c93b; margin-right: 20px;">PÓLIZAS</h4>
                    <div style="display: flex; align-items: center; margin-bottom: 20px;">
                        <label for="numPolizas" style="margin-right: 10px;">NÚMERO DE PÓLIZAS:</label>
                        <select id="numPolizas" name="numPolizas" onchange="generarFormulario()">
                            <option value=""></option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                    <div id="formContainer"></div>
                </div>
                @endif
            </div>
            
            <button type="button" onclick="generatePDF()" class="btn-crear">GENERAR CHECK LIST</button>
            
        </div>
    </div>
</form>
@endif
<script>
    function generarFormulario() {
        var cantidad = document.getElementById('numPolizas').value;
        var contenedor = document.getElementById('formContainer');
        contenedor.innerHTML = ''; // Limpiar contenedor

        for (var i = 1; i <= cantidad; i++) {
            var group = document.createElement('div');
            group.className = 'poliza-group';

            var bancoItem = document.createElement('div');
            bancoItem.className = 'form-item';
            var bancoLabel = document.createElement('label');
            bancoLabel.innerHTML = 'BANCO ' + i;
            var bancoSelect = document.createElement('select');
            bancoSelect.name = 'banco' + i;
            bancoSelect.innerHTML = `<option value=""></option>
                                     @foreach($bancos as $id => $nombrebanco)
                                         <option value="{{ $id }}">{{ $nombrebanco }}</option>
                                     @endforeach`;
            bancoItem.appendChild(bancoLabel);
            bancoItem.appendChild(bancoSelect);

            var polizaNumItem = document.createElement('div');
            polizaNumItem.className = 'form-item';
            var polizaNumLabel = document.createElement('label');
            polizaNumLabel.innerHTML = 'Nro. PÓLIZA GENERAL ' + i;
            var polizaNumInput = document.createElement('input');
            polizaNumInput.type = 'text';
            polizaNumInput.name = 'nropolizageneral' + i;
            polizaNumItem.appendChild(polizaNumLabel);
            polizaNumItem.appendChild(polizaNumInput);

            var polizaGenItem = document.createElement('div');
            polizaGenItem.className = 'form-item';
            var polizaGenLabel = document.createElement('label');
            var polizaGenInput = document.createElement('input');
            polizaGenInput.type = 'checkbox';
            polizaGenInput.name = 'polizageneral' + i;
            polizaGenLabel.innerHTML = 'PÓLIZA GENERAL';
            polizaGenInput.checked = true;
            polizaGenItem.appendChild(polizaGenLabel);
            polizaGenItem.appendChild(polizaGenInput);

            var saludItem = document.createElement('div');
            saludItem.className = 'form-item';
            var saludLabel = document.createElement('label');
            var saludInput = document.createElement('input');
            saludInput.type = 'checkbox';
            saludInput.name = 'declasalud' + i;
            saludLabel.innerHTML = 'DECLARACIÓN DE SALUD';
            saludInput.checked = true;
            saludItem.appendChild(saludLabel);
            saludItem.appendChild(saludInput);

            var polizaDesgraItem = document.createElement('div');
            polizaDesgraItem.className = 'form-item';
            var polizaDesgraLabel = document.createElement('label');
            polizaDesgraLabel.innerHTML = 'Nro. PÓLIZA DESGRAVAMEN ' + i;
            var polizaDesgraInput = document.createElement('input');
            polizaDesgraInput.type = 'text';
            polizaDesgraInput.name = 'nropolizadesgravamen' + i;
            polizaDesgraItem.appendChild(polizaDesgraLabel);
            polizaDesgraItem.appendChild(polizaDesgraInput);

            var polizaSegDesgraItem = document.createElement('div');
            polizaSegDesgraItem.className = 'form-item';
            var polizaSegDesgraLabel = document.createElement('label');
            var polizaSegDesgraInput = document.createElement('input');
            polizaSegDesgraInput.type = 'checkbox';
            polizaSegDesgraInput.name = 'polizasegurodesgravamen' + i;
            polizaSegDesgraLabel.innerHTML = 'PÓLIZA SEGURO DESGRAVAMEN';
            polizaSegDesgraInput.checked = true;
            polizaSegDesgraItem.appendChild(polizaSegDesgraLabel);
            polizaSegDesgraItem.appendChild(polizaSegDesgraInput);
            
            group.appendChild(bancoItem);
            group.appendChild(polizaNumItem);
            group.appendChild(polizaGenItem);
            group.appendChild(saludItem);
            group.appendChild(polizaDesgraItem);
            group.appendChild(polizaSegDesgraItem);
            contenedor.appendChild(group);
        }
    }

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
</script>


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
                                    {!! Form::hidden('tramite', 'AUDITORIA MEDICA') !!}
                                    
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
                                    {!! Form::hidden('tramite', 'AUDITORIA MEDICA') !!}
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
                                        {!! Form::hidden('tramite', 'AUDITORIA MEDICA') !!}
                                        
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
                            {!! Form::hidden('tramite', 'AUDITORIA MEDICA') !!}
        
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
    /* Estilos para alinear horizontalmente los elementos */
    .poliza-group {
        display: flex;
        justify-content: center;
        gap: 20px; /* Separación entre grupos de elementos */
        margin-bottom: 20px;
        width: 100%;
    }

    /* Para centrar el título sobre cada input o checkbox */
    .form-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .form-item label {
        margin-bottom: 5px;
        font-weight: bold;
        text-align: center;
    }

    .form-item select,
    .form-item input[type="text"],
    .form-item input[type="checkbox"] {
        width: 150px;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    button {
        margin-top: 20px;
    }
</style>
<style>
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
        background-color: green;
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