@extends('adminlte::page')

@section('content_header')

<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>

<a id="back-button" class="btn btn-otraopcion btn-sm float-right" onclick="goBack()" style="display: none; margin-right:10px;">ELEGIR OTRO TIPO DE CARTA</a>

<h1>CREAR CARTA DE ACTIVACIÓN DE SEGURO DE DESGRAVAMEN</h1>
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
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="">
    <!-- Botones -->
    <div id="buttons-container" class="text-center card-body card">
        <h2 style="margin-top: 30px; margin-bottom: 30px; font-weight: 900; font-size:25px;">ELIGE UN TIPO DE CARTA</h2>
        <div class="row">
            <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="showForm('solicitud-polizas')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-bell fa-5x mb-2"></i>
                        <span class="h6 mb-0">ACTIVACIÓN DE SEGURO DE DESGRAVAMEN</span>
                    </div>
                </button>
            </div>
            <div class="col-12 col-md-6 mb-3 d-flex justify-content-center">
                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="showForm('reclamo-solicitud-polizas')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-paste fa-5x mb-2"></i>
                        <span class="h6 mb-0">ACTIVACIÓN COBERTURA DE SEGURO DE DESGRAVAMEN</span>
                    </div>
                </button>
            </div>
            {{-- <div class="col-12 col-md-4 mb-3 d-flex justify-content-center">
                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="showForm('reclamo-solicitud-polizas')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-boxes fa-5x mb-2"></i>
                        <span class="h6 mb-0">TERCERA CARTA ACTIVACIÓN DE SEGURO DE DESGRAVAMEN</span>
                    </div>
                </button>
            </div> --}}
        </div>
    </div>
    
    <!-- Formularios (Inicialmente ocultos) -->
    <div id="solicitud-polizas" class="form-container" style="display: none;">
        <div class="row">
            <div class="col-md-8"> 
                <div id="pages-container" class="pages-container">
                    <div class="page" id="page-1">
                        <div id="word-preview" style="font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.5; margin-left:60px; margin-right:60px;  margin-top:70px;">
                            <div style="text-align: right; margin-top: 20px;">
                                <p id="preview-ciudad-fecha">
                                    <span id="preview-ciudad">Santa Cruz</span>, 
                                    <span id="preview-fecha-value">[Sin seleccionar]</span>
                                </p>
                            </div>
                            <div style="text-align: left; margin-top: 50px;">
                                <p>Señores:</p>
                                <p id="preview-opcion" style="margin-top: 10px;"><strong>[Sin seleccionar]</strong></p>
                                <p style="margin-bottom: 40px;">Presente.-</p>
                                <p style="text-align: center; margin-bottom: 40px;"><strong><u>REF: ACTIVACION DE SEGURO DESGRAVAMEN</u></strong></p>
                                <p style="text-align: justify;">
                                    Por la presente me dirijo a Uds. Para saludarle primeramente y después realizar la entrega de la 
                                    <strong>
                                        Determinación según Dictamen Nro. {{ $nrodictamen ?? 'Vacío' }} 
                                        emitido por El Medico Calificador Dra. María Angela Lozano Flores con Matricula Profesional L-655 
                                        en fecha {{ $fechadictamen != 'Vacío' ? \Carbon\Carbon::parse($fechadictamen)->format('d/m/Y') : 'Vacio' }} 
                                        el que dictamina una Enfermedad del {{ $porcentajeinvalidez ?? 'Vacío' }}.
                                    </strong>
                                </p>
                                

                                <p><strong>EL CUAL SOLICITA LA HABILITACION DEL SEGURO DESGRAVAMEN</strong></p>
                                <p id="creditos-texto"  style="margin-top: -15px; text-align: justify;">Créditos con Nro. otorgado a mi persona, para fines consiguientes se adjunta fotocopia de carnet de identidad, original certificado de nacimiento.</p>

                                <div id="vista-previa-acciones" style="margin-top: 30px;">
                                    <p>Adjuntando la siguiente documentación:</p>
                                    <div id="documentos-seleccionados" style="margin-top: 10px; margin-bottom: -15px;">[Sin documentos seleccionados]</div>
                                    <div id="acciones-seleccionadas" style="margin-top: 10px;">[Sin acciones seleccionadas]</div>
                                </div>
                                
                                <p style="text-align: center; margin-top: 90px;"><span id="preview-clienteuno">{{ $clienteauditoria->nombrecompleto }}</span></p>
                                <p style="text-align: center; margin-top: -10px;">CI: <span id="preview-clienteunoci">{{ $clienteauditoria->ci }}</span></p>

                                <div id="vista-previa-cliente2" style="display: none; text-align: center; margin-top: 90px;">
                                    <p>
                                        <span id="preview-clientedos">[Sin Cliente 2]</span>
                                    </p>
                                    <p style="margin-top: -10px;">
                                        CI: <span id="preview-clientedosci">[Sin CI 2]</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.asociados.cartasdesgravamen.descargarcartaactdesgravamen') }}">
                            @csrf
                            {!! Form::hidden('clienteauditoriaid', $clienteauditoria->id) !!}
                            {!! Form::hidden('clienteauditorianombre', $clienteauditoria->nombrecompleto) !!}
                            {!! Form::hidden('ci', $clienteauditoria->ci) !!}
                            <input type="hidden" id="creditos" name="creditos">
                            <div class="row">
                                <div class="form-group col-lg-7">
                                    <label for="ciudad">Ciudad:</label>
                                    <select id="ciudad" name="ciudad" class="form-control" required onchange="updatePreview()">
                                        <option value="Santa Cruz" selected>Santa Cruz</option>
                                        <option value="Cochabamba">Cochabamba</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-5">
                                    <label for="fecha">Fecha:</label>
                                    <input type="date" id="fecha" name="fecha" class="form-control" required onchange="updatePreview()">
                                </div>
                            </div>
                            <div class="form-group">  
                                <label for="opcion">Seleccionar Banco:</label>
                                <select id="opcion" name="opcion" class="form-control" required onchange="updatePreview()">
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    @if ($opciones->banco1)
                                        <option value="banco1">{{ $opciones->banco1 }}</option>
                                    @endif
                                    @if ($opciones->banco2)
                                        <option value="banco2">{{ $opciones->banco2 }}</option>
                                    @endif
                                    @if ($opciones->banco3)
                                        <option value="banco3">{{ $opciones->banco3 }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Documentos:</label>
                                <div id="documentos-container">
                                    <div class="checkbox-container">
                                        <input type="checkbox" class="documento-checkbox" id="doc-nacimiento" data-documento="CERTIFICADO DE NACIMIENTO ORIGINAL" onchange="updateDocumentoPreview()">
                                        <label for="doc-nacimiento">CERTIFICADO DE NACIMIENTO ORIGINAL</label>
                                        <input type="text" class="form-control hoja-count" placeholder="Número de hojas" id="hojas-doc-nacimiento" oninput="updateDocumentoPreview()">
                                    </div>
                                    <div class="checkbox-container">
                                        <input type="checkbox" class="documento-checkbox" id="doc-ci" data-documento="CARNET DE IDENTIDAD COPIA" onchange="updateDocumentoPreview()">
                                        <label for="doc-ci">CARNET DE IDENTIDAD COPIA</label>
                                        <input type="text" class="form-control hoja-count" placeholder="Número de hojas" id="hojas-doc-ci" oninput="updateDocumentoPreview()">
                                    </div>
                                    <div class="checkbox-container">
                                        <input type="checkbox" class="documento-checkbox" id="doc-dictamen" data-documento="DICTAMEN N° {{ $dictamen->nrodictamen }} ORIGINAL" onchange="updateDocumentoPreview()">
                                        <label for="doc-dictamen">DICTAMEN N° {{ $dictamen->nrodictamen }} ORIGINAL</label>
                                        <input type="text" class="form-control hoja-count" placeholder="Número de hojas" id="hojas-doc-dictamen" oninput="updateDocumentoPreview()">
                                    </div>
                                    <div class="checkbox-container">
                                        <input type="checkbox" class="documento-checkbox" id="doc-resumen" data-documento="RESUMEN CLÍNICO OCUPACIONAL ORIGINAL" onchange="updateDocumentoPreview()">
                                        <label for="doc-resumen">RESUMEN CLÍNICO OCUPACIONAL ORIGINAL</label>
                                        <input type="text" class="form-control hoja-count" placeholder="Número de hojas" id="hojas-doc-resumen" oninput="updateDocumentoPreview()">
                                    </div>
                                </div>
                                
                                <label>Acciones:</label>
                                <div id="acciones-container">
                                    @foreach($acciones as $accion)
                                        <div class="checkbox-container">
                                            <input type="checkbox" class="accion-checkbox" id="accion-{{ $loop->index }}" data-accion="{{ $accion->accionnombre }}" data-tipoarea="{{ $accion->tipoarea }}" onchange="updateAccionPreview()">
                                            <label for="accion-{{ $loop->index }}">{{ $accion->accionnombre }}</label>
                                            <input type="text" class="form-control hoja-count" placeholder="Número de hojas" id="hojas-accion-{{ $loop->index }}" oninput="updateAccionPreview()">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <input type="hidden" name="documentos_seleccionados" id="documentos_seleccionados">
                            <input type="hidden" name="acciones_seleccionadas" id="acciones_seleccionadas">
                            <button type="button" id="btn-agregar-cliente2" class="btn btn-outline-primary" onclick="mostrarCliente2()"><i class="fa fa-plus"></i> Otro Cliente</button>

                            <div id="cliente2-seccion" style="display: none;">
                                <div class="form-group">
                                    <label for="clientedos">Nombre Cliente 2:</label>
                                    <input type="text" id="clientedos" name="clientedos" class="form-control" placeholder="Nombre del cliente dos" oninput="updateClientedosPreview()">
                                </div>
                                <div class="form-group">
                                    <label for="clientedosci">CI Cliente 2:</label>
                                    <input type="text" id="clientedosci" name="clientedosci" class="form-control" placeholder="CI del cliente dos" oninput="updateCiClientedosPreview()">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-crear btn-block" style="margin-top: 10px;">GUARDAR Y GENERAR CARTA</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="reclamo-solicitud-polizas" class="form-container" style="display: none;">
        <div class="row">
            <div class="col-md-8"> 
                <div id="pages-container" class="pages-container">
                    <div class="page" id="page-1">
                        <div id="word-preview" style="font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.2; margin-left:40px; margin-right:40px;  margin-top:40px;">
                            <div style="text-align: right; margin-top: 20px;">
                                <p id="preview-ciudad-fecha">
                                    <span id="preview-ciudad2">Santa Cruz</span>, 
                                    <span id="preview-fecha-value2">[Sin seleccionar]</span>
                                </p>
                            </div>
                            <div style="text-align: left; margin-top: 50px;">
                                <p>Señores:</p>
                                <p id="preview-opcion2" style="margin-top: 10px;"><strong>[Sin seleccionar]</strong></p>
                                <p style="margin-bottom: 40px;">Presente.-</p>
                                <p style="margin-bottom: 40px;" contenteditable="true" id="editable_ref">
                                    <strong><u>REF: ACTIVACIÓN COBERTURA SEGURO DE DESGRAVAMEN</u></strong>
                                </p>
                                                         
                                <p>De mi consideración:</p>

                                <div id="preview-articulos" style="text-align: justify;">

                                </div>
                                <p>Agradeciendo su atención.</p>

                                <p style="text-align: center; margin-top: 90px;"><span id="preview-clienteuno">{{ $clienteauditoria->nombrecompleto }}</span></p>
                                <p style="text-align: center; margin-top: -10px;">CI: <span id="preview-clienteunoci">{{ $clienteauditoria->ci }}</span></p>

                                <div id="vista-previa-cliente22" style="display: none; text-align: center; margin-top: 90px;">
                                    <p>
                                        <span id="preview-clientedos2">[Sin Cliente 2]</span>
                                    </p>
                                    <p style="margin-top: -10px;">
                                        CI: <span id="preview-clientedosci2">[Sin CI 2]</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.asociados.cartasdesgravamen.descargarcartaactcoberturadesgravamen') }}">
                            @csrf
                            {!! Form::hidden('clienteauditoriaid', $clienteauditoria->id) !!}
                            {!! Form::hidden('clienteauditorianombre', $clienteauditoria->nombrecompleto) !!}
                            {!! Form::hidden('ci', $clienteauditoria->ci) !!}
                            <input type="hidden" id="articulosSeleccionados" name="articulosSeleccionados" value="">
                            <input type="hidden" id="ref_carta" name="ref_carta" value="REF: ACTIVACIÓN COBERTURA SEGURO DE DESGRAVAMEN">

                            <script>
                                // Asegurarse de que el campo oculto se actualice cuando el contenido editable cambie
                                window.addEventListener('load', function() {
                                    // Inicializar el campo oculto con el valor del contenteditable
                                    var contenidoEditable = document.getElementById('editable_ref').innerHTML;
                                    document.getElementById('ref_carta').value = contenidoEditable;
                                });
                            
                                // Detectar cualquier cambio en el contenido editable y actualizar el campo oculto
                                document.getElementById('editable_ref').addEventListener('input', function() {
                                    // Actualizar el campo oculto con el contenido actual del contenteditable
                                    var contenidoEditable = document.getElementById('editable_ref').innerHTML;
                                    document.getElementById('ref_carta').value = contenidoEditable;
                                });
                            </script>
    
                            <div class="row">
                                <div class="form-group col-lg-7">
                                    <label for="ciudad">Ciudad:</label>
                                    <select id="ciudad2" name="ciudad" class="form-control" required onchange="updatePreview2()">
                                        <option value="Santa Cruz" selected>Santa Cruz</option>
                                        <option value="Cochabamba">Cochabamba</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-5">
                                    <label for="fecha">Fecha:</label>
                                    <input type="date" id="fecha2" name="fecha" class="form-control" required onchange="updatePreview2()">
                                </div>
                            </div>
                            <div class="form-group">  
                                <label for="opcion">Seleccionar Banco:</label>
                                <select id="opcion2" name="opcion" class="form-control" required onchange="updatePreview2()">
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    @if ($opciones->banco1)
                                        <option value="banco1">{{ $opciones->banco1 }}</option>
                                    @endif
                                    @if ($opciones->banco2)
                                        <option value="banco2">{{ $opciones->banco2 }}</option>
                                    @endif
                                    @if ($opciones->banco3)
                                        <option value="banco3">{{ $opciones->banco3 }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="articulos">Seleccionar artículos:</label><br>
                                
                                <!-- Checkbox para Art. 1031 -->
                                <input type="checkbox" id="articulo1031" name="articulo1031" value="Art. 1031.- (INFORMES Y EVIDENCIAS)" onchange="updateArticulosPreview()">
                                <label for="articulo1031">Art. 1031.- (INFORMES Y EVIDENCIAS)</label><br>
                            
                                <!-- Checkbox para Art. 1033 -->
                                <input type="checkbox" id="articulo1033" name="articulo1033" value="Art. 1033.- (PLAZO PARA PRONUNCIARSE)" onchange="updateArticulosPreview()">
                                <label for="articulo1033">Art. 1033.- (PLAZO PARA PRONUNCIARSE)</label><br>

                                <input type="checkbox" id="clausuladefiniciones" name="clausuladefiniciones" value="CLAUSULA 1 DEFINICIONES" onchange="updateArticulosPreview()">
                                <label for="clausuladefiniciones">CLAUSULA 1 DEFINICIONES</label><br>

                                <input type="checkbox" id="leyseguro1883" name="leyseguro1883" value="LEY DE SEGUROS 1883" onchange="updateArticulosPreview()">
                                <label for="leyseguro1883">LEY DE SEGUROS 1883</label><br>
                            </div>
                            
                            <!-- Campo de texto para artículos adicionales -->
                            <div class="form-group">
                                <label for="articulo-texto">Escribir artículo adicional:</label>
                                <input type="text" id="articulo-texto" name="articulo-texto" class="form-control" placeholder="Escribe un artículo adicional">
                            </div>

                            <button type="button" class="btn btn-outline-primary" onclick="agregarArticuloTexto()">
                                <i class="fa fa-plus"></i> Agregar Artículo
                            </button>

                            <button type="button" id="btn-agregar-cliente22" class="btn btn-outline-primary" onclick="mostrarCliente22()"><i class="fa fa-plus"></i> Otro Cliente</button>

                            <div id="cliente2-seccion2" style="display: none;">
                                <div class="form-group">
                                    <label for="clientedos">Nombre Cliente 2:</label>
                                    <input type="text" id="clientedos2" name="clientedos" class="form-control" placeholder="Nombre del cliente dos" oninput="updateClientedosPreview2()">
                                </div>
                                <div class="form-group">
                                    <label for="clientedosci">CI Cliente 2:</label>
                                    <input type="text" id="clientedosci2" name="clientedosci" class="form-control" placeholder="CI del cliente dos" oninput="updateCiClientedosPreview2()">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-crear btn-block" style="margin-top: 10px;">GUARDAR Y GENERAR CARTA</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
   {{--  <div id="solicitud-polizas-generales" class="form-container" style="display: none;">
        <div class="row">
            <div class="col-md-8"> 
                <div id="pages-container" class="pages-container">
                    <div class="page" id="page-1">
                        <div id="word-preview" style="font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.2; margin-left:40px; margin-right:40px;  margin-top:40px;">
                            <div style="text-align: right; margin-top: 20px;">
                                <p id="preview-ciudad-fecha">
                                    <span id="preview-ciudad3">Santa Cruz</span>, 
                                    <span id="preview-fecha-value3">[Sin seleccionar]</span>
                                </p>
                            </div>
                            <div style="text-align: left; margin-top: 50px;">
                                <p>Señores:</p>
                                <p id="preview-opcion3" style="margin-top: 10px;"><strong>[Sin seleccionar]</strong></p>
                                <p style="margin-bottom: 40px;">Presente.-</p>
                                <p style="text-align: center; margin-bottom: 40px;"><strong><u>REF: RECLAMO DE SOLICITUD DE POLIZAS GENERALES MAS PORCENTAJE DE INVALIDEZ</u></strong></p>
                                <p>Mediante la presente carta reciba usted mis más cordiales saludos, deseándole éxitos en sus labores.</p>
                                <p>Por la presente realizo el Reclamo de Solicitud de Pólizas Generales donde indique el porcentaje de Invalidez para el Seguro de Desgravamen.</p>
                                <p>Aclaro que el tomador del Seguro es el Banco, por lo mismo solicito las gestiones ante la Compañía Aseguradora.</p>
                                <p>Agradeciendo su atención.</p>

                                <p style="text-align: center; margin-top: 90px;"><span id="preview-clienteuno3">[Sin Cliente 1]</span></p>
                                <p style="text-align: center; margin-top: -10px;">CI: <span id="preview-clienteunoci3">[Sin CI 1]</span></p>

                                <div id="vista-previa-cliente23" style="display: none; text-align: center; margin-top: 90px;">
                                    <p>
                                        <span id="preview-clientedos3">[Sin Cliente 2]</span>
                                    </p>
                                    <p style="margin-top: -10px;">
                                        CI: <span id="preview-clientedosci3">[Sin CI 2]</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.cartaspolizas.descargarreclamosolicitudpolizasgenerales') }}">
                            @csrf
                            <div class="row">
                                <div class="form-group col-lg-7">
                                    <label for="ciudad">Ciudad:</label>
                                    <select id="ciudad3" name="ciudad" class="form-control" required onchange="updatePreview3()">
                                        <option value="Santa Cruz" selected>Santa Cruz</option>
                                        <option value="Cochabamba">Cochabamba</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-5">
                                    <label for="fecha">Fecha:</label>
                                    <input type="date" id="fecha3" name="fecha" class="form-control" required onchange="updatePreview3()">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="opcion">Seleccionar Banco o Seguro:</label>
                                <select id="opcion3" name="opcion" class="form-control" required onchange="updatePreview3()">
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    @foreach($opciones as $opcion)
                                        <option value="{{ $opcion->nombreBanco ?? $opcion->nombreSeguro }}">
                                            {{ $opcion->nombreBanco ?? $opcion->nombreSeguro }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="clienteuno">Nombre Cliente 1:</label>
                                <input type="text" id="clienteuno3" name="clienteuno" class="form-control" placeholder="Nombre del cliente uno" required oninput="updateClienteunoPreview3()">
                            </div>
                            <div class="form-group">
                                <label for="clienteunoci">CI Cliente 1:</label>
                                <input type="text" id="clienteunoci3" name="clienteunoci" class="form-control" placeholder="CI del cliente uno" required oninput="updateCiClienteunoPreview3()">
                            </div>

                            <button type="button" id="btn-agregar-cliente23" class="btn btn-outline-primary" onclick="mostrarCliente23()"><i class="fa fa-plus"></i> Otro Cliente</button>

                            <div id="cliente2-seccion3" style="display: none;">
                                <div class="form-group">
                                    <label for="clientedos">Nombre Cliente 2:</label>
                                    <input type="text" id="clientedos3" name="clientedos" class="form-control" placeholder="Nombre del cliente dos" oninput="updateClientedosPreview3()">
                                </div>
                                <div class="form-group">
                                    <label for="clientedosci">CI Cliente 2:</label>
                                    <input type="text" id="clientedosci3" name="clientedosci" class="form-control" placeholder="CI del cliente dos" oninput="updateCiClientedosPreview3()">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-crear btn-block" style="margin-top: 10px;">GUARDAR Y GENERAR CARTA</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@stop

@section('js')
<script>
    function updateAccionPreview() {
    let hojasTextos = [];
    const palabrasClavePlacas = ['PLACA', 'TOMOGRAFIA', 'RESONANCIA'];

    let accionesSeleccionadas = [];

    document.querySelectorAll('.accion-checkbox').forEach((checkbox) => {
        if (checkbox.checked) {
            const accionNombre = checkbox.getAttribute('data-accion');
            const tipoArea = checkbox.getAttribute('data-tipoarea');
            const inputHojas = document.getElementById('hojas-' + checkbox.id);
            const hojas = inputHojas ? (inputHojas.value || "1") : "1";

            let tipoHoja = hojas > 1 ? "hojas" : "hoja";
            if (palabrasClavePlacas.some(palabra => accionNombre.toUpperCase().includes(palabra))) {
                tipoHoja = hojas > 1 ? "placas" : "placa";
            }

            let nombreAccionFinal = tipoArea === "ESPECIALIDAD" ? `INFORME DE ${accionNombre}` : accionNombre;
            hojasTextos.push(`<li>${nombreAccionFinal} ${hojas} ${tipoHoja}</li>`);

            // Guardar en el array de acciones seleccionadas
            accionesSeleccionadas.push({
                accion: accionNombre,
                tipoArea: tipoArea,
                hojas: hojas,
                tipoHoja: tipoHoja
            });
        }
    });

    // Actualizar la vista previa
    document.getElementById('acciones-seleccionadas').innerHTML = hojasTextos.length ? `<ul>${hojasTextos.join('')}</ul>` : '[Sin acciones seleccionadas]';

    // Enviar los datos al formulario
    document.getElementById('acciones_seleccionadas').value = JSON.stringify(accionesSeleccionadas);
}

function updateDocumentoPreview() {
    let hojasTextos = [];
    
    let documentosSeleccionados = [];

    document.querySelectorAll('.documento-checkbox').forEach((checkbox) => {
        if (checkbox.checked) {
            const documentoNombre = checkbox.getAttribute('data-documento');
            const inputHojas = document.getElementById('hojas-' + checkbox.id);
            const hojas = inputHojas ? (inputHojas.value || "1") : "1";

            let tipoHoja = hojas > 1 ? "hojas" : "hoja";
            hojasTextos.push(`<li>${documentoNombre} ${hojas} ${tipoHoja}</li>`);

            // Guardar en el array de documentos seleccionados
            documentosSeleccionados.push({
                documento: documentoNombre,
                hojas: hojas
            });
        }
    });

    // Actualizar la vista previa
    document.getElementById('documentos-seleccionados').innerHTML = hojasTextos.length ? `<ul>${hojasTextos.join('')}</ul>` : '[Sin documentos seleccionados]';

    // Enviar los datos al formulario
    document.getElementById('documentos_seleccionados').value = JSON.stringify(documentosSeleccionados);
}

</script>
    
{{-- ACTIVACION DE SEGURO DE DESGRAVAMEN --}}
<script>
    function updatePreview() { 
        const ciudad = document.getElementById('ciudad').value || 'Santa Cruz';
        const fecha = document.getElementById('fecha').value || '[Sin seleccionar]';
        const opcion = document.getElementById('opcion').value || '[Sin seleccionar]';

        const creditos = {
            banco1: [
                @if ($opciones->nrocredito1) "{{ $opciones->nrocredito1 }}", @endif
                @if ($opciones->nrocredito2) "{{ $opciones->nrocredito2 }}", @endif
                @if ($opciones->nrocredito3) "{{ $opciones->nrocredito3 }}", @endif
                @if ($opciones->nrocredito4) "{{ $opciones->nrocredito4 }}", @endif
                @if ($opciones->nrocredito5) "{{ $opciones->nrocredito5 }}", @endif
                @if ($opciones->nrocredito6) "{{ $opciones->nrocredito6 }}", @endif
            ],
            banco2: [
                @if ($opciones->nrocredito7) "{{ $opciones->nrocredito7 }}", @endif
                @if ($opciones->nrocredito8) "{{ $opciones->nrocredito8 }}", @endif
                @if ($opciones->nrocredito9) "{{ $opciones->nrocredito9 }}", @endif
                @if ($opciones->nrocredito10) "{{ $opciones->nrocredito10 }}", @endif
                @if ($opciones->nrocredito11) "{{ $opciones->nrocredito11 }}", @endif
                @if ($opciones->nrocredito12) "{{ $opciones->nrocredito12 }}", @endif
            ],
            banco3: [
                @if ($opciones->nrocredito13) "{{ $opciones->nrocredito13 }}", @endif
                @if ($opciones->nrocredito14) "{{ $opciones->nrocredito14 }}", @endif
                @if ($opciones->nrocredito15) "{{ $opciones->nrocredito15 }}", @endif
                @if ($opciones->nrocredito16) "{{ $opciones->nrocredito16 }}", @endif
                @if ($opciones->nrocredito17) "{{ $opciones->nrocredito17 }}", @endif
                @if ($opciones->nrocredito18) "{{ $opciones->nrocredito18 }}", @endif
            ]
        };

        document.getElementById('preview-ciudad').innerText = ciudad;
    document.getElementById('preview-fecha-value').innerText = formatFecha(fecha);

    const bancoSeleccionado = document.getElementById('opcion').options[document.getElementById('opcion').selectedIndex].text;
    document.getElementById('preview-opcion').innerHTML = `<strong>${bancoSeleccionado}</strong>`;

    let textoCreditos = "Créditos con Nro. ";

    let creditosSeleccionados = [];

    if (opcion && creditos[opcion]) {
        creditosSeleccionados = creditos[opcion];
        textoCreditos += creditosSeleccionados.join(", ") + " otorgado a mi persona, para fines consiguientes se adjunta fotocopia de carnet de identidad, original certificado de nacimiento.";
    } else {
        textoCreditos += "otorgado a mi persona, para fines consiguientes se adjunta fotocopia de carnet de identidad, original certificado de nacimiento.";
    }
    
    document.getElementById('creditos-texto').innerText = textoCreditos;

    // Actualizar el campo oculto con los créditos seleccionados
    document.getElementById('creditos').value = creditosSeleccionados.join(", ");
    }
</script>

<script>
    function formatFecha(fecha) {
        if (!fecha) return '[Sin seleccionar]';

        const date = new Date(fecha + 'T00:00:00');
        date.setMinutes(date.getMinutes() + date.getTimezoneOffset());

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('es-BO', options);
    }

    function updateClienteunoPreview() {
        const clienteuno = document.getElementById('clienteuno').value || '[Sin Cliente 1]';
        document.getElementById('preview-clienteuno').innerText = clienteuno;

    }
    function updateCiClienteunoPreview() {
        const clienteunoci = document.getElementById('clienteunoci').value || '[Sin CI 1]';
        document.getElementById('preview-clienteunoci').innerText = clienteunoci;

    }

    function mostrarCliente2() {
        const seccionCliente2 = document.getElementById('cliente2-seccion');
        seccionCliente2.style.display = 'block';

        const vistaPreviaCliente2 = document.getElementById('vista-previa-cliente2');
        vistaPreviaCliente2.style.display = 'block';
    }

    function updateClientedosPreview() {
        const clientedos = document.getElementById('clientedos').value || '';
        document.getElementById('preview-clientedos').innerText = clientedos;

    }
    function updateCiClientedosPreview() {
        const clientedosci = document.getElementById('clientedosci').value || '';
        document.getElementById('preview-clientedosci').innerText = clientedosci;

    }
</script>

{{-- ACTIVACIÓN COBERTURA SEGURO DE DESGRAVAM --}}
<script>
    function updatePreview2() {
        const ciudad2 = document.getElementById('ciudad2').value || 'Santa Cruz';
        const fecha2 = document.getElementById('fecha2').value || '[Sin seleccionar]';
        const opcion2 = document.getElementById('opcion2').value || '[Sin seleccionar]';

        // Definir los números de crédito para cada banco
        const creditos = {
            banco1: [
                @if ($opciones->nrocredito1) "{{ $opciones->nrocredito1 }}", @endif
                @if ($opciones->nrocredito2) "{{ $opciones->nrocredito2 }}", @endif
                @if ($opciones->nrocredito3) "{{ $opciones->nrocredito3 }}", @endif
                @if ($opciones->nrocredito4) "{{ $opciones->nrocredito4 }}", @endif
                @if ($opciones->nrocredito5) "{{ $opciones->nrocredito5 }}", @endif
                @if ($opciones->nrocredito6) "{{ $opciones->nrocredito6 }}", @endif
            ],
            banco2: [
                @if ($opciones->nrocredito7) "{{ $opciones->nrocredito7 }}", @endif
                @if ($opciones->nrocredito8) "{{ $opciones->nrocredito8 }}", @endif
                @if ($opciones->nrocredito9) "{{ $opciones->nrocredito9 }}", @endif
                @if ($opciones->nrocredito10) "{{ $opciones->nrocredito10 }}", @endif
                @if ($opciones->nrocredito11) "{{ $opciones->nrocredito11 }}", @endif
                @if ($opciones->nrocredito12) "{{ $opciones->nrocredito12 }}", @endif
            ],
            banco3: [
                @if ($opciones->nrocredito13) "{{ $opciones->nrocredito13 }}", @endif
                @if ($opciones->nrocredito14) "{{ $opciones->nrocredito14 }}", @endif
                @if ($opciones->nrocredito15) "{{ $opciones->nrocredito15 }}", @endif
                @if ($opciones->nrocredito16) "{{ $opciones->nrocredito16 }}", @endif
                @if ($opciones->nrocredito17) "{{ $opciones->nrocredito17 }}", @endif
                @if ($opciones->nrocredito18) "{{ $opciones->nrocredito18 }}", @endif
            ]
        };

        // Actualizar los valores de la ciudad, fecha y opción
        document.getElementById('preview-ciudad2').innerText = ciudad2;
        document.getElementById('preview-fecha-value2').innerText = formatFecha(fecha2);

        // Actualizar el nombre del banco seleccionado
        const bancoSeleccionado2 = document.getElementById('opcion2').options[document.getElementById('opcion2').selectedIndex].text;
        document.getElementById('preview-opcion2').innerHTML = `<strong>${bancoSeleccionado2}</strong>`;

        // Mostrar los números de crédito si se seleccionó un banco
        let textoCreditos = "Créditos con Nro. ";

        if (opcion && creditos[opcion]) {
            // Aquí se añaden los números de crédito correspondientes al banco seleccionado
            textoCreditos += creditos[opcion].join(", ") + " otorgado a mi persona, para fines consiguientes se adjunta fotocopia de carnet de identidad, original certificado de nacimiento.";
        } else {
            textoCreditos += "otorgado a mi persona, para fines consiguientes se adjunta fotocopia de carnet de identidad, original certificado de nacimiento.";
        }

        // Actualizar el texto de los créditos en el párrafo
        document.getElementById('creditos-texto').innerText = textoCreditos;
    }

    function formatFecha2(fecha) {
        if (!fecha) return '[Sin seleccionar]';

        const date = new Date(fecha + 'T00:00:00');
        date.setMinutes(date.getMinutes() + date.getTimezoneOffset());

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('es-BO', options);
    }

    function updateClienteunoPreview2() {
        const clienteuno2 = document.getElementById('clienteuno2').value || '[Sin Cliente 1]';
        document.getElementById('preview-clienteuno2').innerText = clienteuno2;

    }
    function updateCiClienteunoPreview2() {
        const clienteunoci2 = document.getElementById('clienteunoci2').value || '[Sin CI 1]';
        document.getElementById('preview-clienteunoci2').innerText = clienteunoci2;

    }

    function mostrarCliente22() {
        const seccionCliente2 = document.getElementById('cliente2-seccion2');
        seccionCliente2.style.display = 'block';

        const vistaPreviaCliente2 = document.getElementById('vista-previa-cliente22');
        vistaPreviaCliente2.style.display = 'block';
    }

    function updateClientedosPreview2() {
        const clientedos2 = document.getElementById('clientedos2').value || '';
        document.getElementById('preview-clientedos2').innerText = clientedos2;

    }
    function updateCiClientedosPreview2() {
        const clientedosci2 = document.getElementById('clientedosci2').value || '';
        document.getElementById('preview-clientedosci2').innerText = clientedosci2;

    }

</script>

{{-- RECLAMO DE SOLICITUD DE POLIZAS GENERALES --}}
<script>
    function updatePreview3() {
        const ciudad3 = document.getElementById('ciudad3').value || 'Santa Cruz';
        const fecha3 = document.getElementById('fecha3').value || '[Sin seleccionar]';
        const opcion3 = document.getElementById('opcion3').value || '[Sin seleccionar]';

        document.getElementById('preview-ciudad3').innerText = ciudad3;
        document.getElementById('preview-fecha-value3').innerText = formatFecha(fecha3);
        document.getElementById('preview-opcion3').innerHTML = `<strong>${opcion3}</strong>`;
    }

    function formatFecha3(fecha) {
        if (!fecha) return '[Sin seleccionar]';

        const date = new Date(fecha + 'T00:00:00');
        date.setMinutes(date.getMinutes() + date.getTimezoneOffset());

        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return date.toLocaleDateString('es-BO', options);
    }

    function updateClienteunoPreview3() {
        const clienteuno3 = document.getElementById('clienteuno3').value || '[Sin Cliente 1]';
        document.getElementById('preview-clienteuno3').innerText = clienteuno3;

    }
    function updateCiClienteunoPreview3() {
        const clienteunoci3 = document.getElementById('clienteunoci3').value || '[Sin CI 1]';
        document.getElementById('preview-clienteunoci3').innerText = clienteunoci3;

    }

    function mostrarCliente23() {
        const seccionCliente2 = document.getElementById('cliente2-seccion3');
        seccionCliente2.style.display = 'block';

        const vistaPreviaCliente2 = document.getElementById('vista-previa-cliente23');
        vistaPreviaCliente2.style.display = 'block';
    }

    function updateClientedosPreview3() {
        const clientedos3 = document.getElementById('clientedos3').value || '';
        document.getElementById('preview-clientedos3').innerText = clientedos3;

    }
    function updateCiClientedosPreview3() {
        const clientedosci3 = document.getElementById('clientedosci3').value || '';
        document.getElementById('preview-clientedosci3').innerText = clientedosci3;

    }
</script>

<script>
    function showForm(formId) {
        document.getElementById('buttons-container').style.display = 'none';
        document.getElementById(formId).style.display = 'block';
        document.getElementById('back-button').style.display = 'block';
    }

    function goBack() {
        document.getElementById('buttons-container').style.display = 'block';
        document.getElementById('solicitud-polizas').style.display = 'none';
        document.getElementById('reclamo-solicitud-polizas').style.display = 'none';
        document.getElementById('solicitud-polizas-generales').style.display = 'none';
        document.getElementById('back-button').style.display = 'none';
    }

</script>

<script>
    let articulosAcumulados = [];

    function updateArticulosPreview() {
        const previewArticulos = document.getElementById('preview-articulos');
        previewArticulos.innerHTML = ''; 

        // Verificar los checkboxes seleccionados y actualizar la lista global
        const articulo1031 = document.getElementById('articulo1031').checked;
        const articulo1033 = document.getElementById('articulo1033').checked;
        const clausuladefiniciones = document.getElementById('clausuladefiniciones').checked;
        const leyseguro1883 = document.getElementById('leyseguro1883').checked;

        if (articulo1031 && !articulosAcumulados.includes("<i>Art. 1031.- (INFORMES Y EVIDENCIAS). El asegurado o beneficiario, según el caso, tienen la obligación de facilitar, a requerimiento del asegurador, todas las informaciones que tengan sobre los hechos y circunstancias del siniestro, a suministrar las evidencias conducentes a la determinación de la causa, identidad de las personas o intereses asegurados y cuantía de los daños, así como permitir las indagaciones pertinentes necesarias a tal objeto.</i>")) {
            articulosAcumulados.push("<i>Art. 1031.- (INFORMES Y EVIDENCIAS). El asegurado o beneficiario, según el caso, tienen la obligación de facilitar, a requerimiento del asegurador, todas las informaciones que tengan sobre los hechos y circunstancias del siniestro, a suministrar las evidencias conducentes a la determinación de la causa, identidad de las personas o intereses asegurados y cuantía de los daños, así como permitir las indagaciones pertinentes necesarias a tal objeto.</i>");
        }

        if (articulo1033 && !articulosAcumulados.includes("<i>Art. 1033.- (PLAZO PARA PRONUNCIARSE). El asegurador debe pronunciarse sobre el derecho del asegurado o beneficiario dentro de los treinta días de recibidas la información y evidencia citadas en el artículo 1031. Se dejará constancia escrita de la fecha de recepción de la información y evidencias a efecto del cómputo del plazo.<br>En caso de demora u omisión del asegurado o beneficiario en proporcionar la información y evidencias sobre el siniestro, el término señalado no corre hasta el cumplimiento de estas obligaciones.<br>El silencio del asegurador, vencido el término para pronunciarse, importa la aceptación del reclamo.</i>")) {
            articulosAcumulados.push("<i>Art. 1033.- (PLAZO PARA PRONUNCIARSE). El asegurador debe pronunciarse sobre el derecho del asegurado o beneficiario dentro de los treinta días de recibidas la información y evidencia citadas en el artículo 1031. Se dejará constancia escrita de la fecha de recepción de la información y evidencias a efecto del cómputo del plazo.<br>En caso de demora u omisión del asegurado o beneficiario en proporcionar la información y evidencias sobre el siniestro, el término señalado no corre hasta el cumplimiento de estas obligaciones.<br>El silencio del asegurador, vencido el término para pronunciarse, importa la aceptación del reclamo.</i>");
        }

        if (clausuladefiniciones && !articulosAcumulados.includes("<b>CLAUSULA 1 DEFINICIONES.-</b><br>“Invalidez Total y Permanente: Se considera invalidez Total y Permanente el estado de situación física que como consecuencia de una enfermedad o accidente presenta una pérdida o disminución de su capacidad física y/o intelectual , <b>igual o superior a 60% de su capacidad de trabajo,</b> siempre que el grado de tal incapacidad sea reconocido y formalizado por el Instituto de Salud Ocupacional (INSO) o la Entidad Encargada de Calificar (EEC) o por un médico calificador debidamente registrado en la APS”")) {
            articulosAcumulados.push("<b>CLAUSULA 1 DEFINICIONES.-</b><br>“Invalidez Total y Permanente: Se considera invalidez Total y Permanente el estado de situación física que como consecuencia de una enfermedad o accidente presenta una pérdida o disminución de su capacidad física y/o intelectual , <b>igual o superior a 60% de su capacidad de trabajo,</b> siempre que el grado de tal incapacidad sea reconocido y formalizado por el Instituto de Salud Ocupacional (INSO) o la Entidad Encargada de Calificar (EEC) o por un médico calificador debidamente registrado en la APS”");
        }

        if (leyseguro1883 && !articulosAcumulados.includes("LEY DE SEGUROS 1883 - TÍTULO V - DE LA PROTECCIÓN A LOS ASEGURADOS, TOMADORES Y BENEFICIARIOS DEL SEGURO - CAPÍTULO ÚNICO ART. 38 - DISPOSICIONES GENERALES, que indica:<br><i>“La protección jurídica a los asegurados, tomadores y beneficiarios de los seguros, se concretará en los siguientes aspectos, <b>b) El alcance del contrato de seguros, en caso de discrepancia, ambigüedad o duda será interpretado siempre del modo más favorable para el asegurado, tomador o beneficiario…”</b></i>")) {
            articulosAcumulados.push("LEY DE SEGUROS 1883 - TÍTULO V - DE LA PROTECCIÓN A LOS ASEGURADOS, TOMADORES Y BENEFICIARIOS DEL SEGURO - CAPÍTULO ÚNICO ART. 38 - DISPOSICIONES GENERALES, que indica:<br><i>“La protección jurídica a los asegurados, tomadores y beneficiarios de los seguros, se concretará en los siguientes aspectos, <b>b) El alcance del contrato de seguros, en caso de discrepancia, ambigüedad o duda será interpretado siempre del modo más favorable para el asegurado, tomador o beneficiario…”</b></i>");
        }

        // Mostrar los artículos en la vista previa
        articulosAcumulados.forEach(function(articulo) {
            previewArticulos.innerHTML += `<p>${articulo}</p>`;
        });

        // Actualizar el valor del campo oculto
        document.getElementById('articulosSeleccionados').value = articulosAcumulados.join('|');
    }

    function agregarArticuloTexto() {
        const articuloTexto = document.getElementById('articulo-texto').value.trim();
        
        if (articuloTexto && !articulosAcumulados.includes(articuloTexto)) {
            articulosAcumulados.push(articuloTexto);
            updateArticulosPreview();
            document.getElementById('articulo-texto').value = '';
        } else {
            alert("Por favor, escribe un artículo antes de agregarlo o evita duplicados.");
        }
    }
</script>


@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .page {
        width: 812px;
        height: 5992px;
        border: 1px solid #ddd;
        margin: auto;
        padding: 20px;
        background: #fff;
        page-break-before: always;
    }
    .pages-container {
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        max-height: none;
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
        }
    .btn-otraopcion {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-otraopcion:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-crear {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 10px;
        }
    .btn-crear:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .btn-custom {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .btn-custom:hover {
        background-color: #94c93b;
        color: #ffffff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: scale(1.05);
    }
    .btn-custom:disabled {
        background-color: #d6d6d6;
        color: #a0a0a0;
        cursor: not-allowed;
    }
</style>
@stop

