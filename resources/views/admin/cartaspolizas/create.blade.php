@extends('adminlte::page')

@section('content_header')

<a class="btn btn-regresar btn-sm float-right" href="{{route('admin.cartaspolizas.listacartaspolizas')}}">REGRESAR</a>

<a id="back-button" class="btn btn-otraopcion btn-sm float-right" onclick="goBack()" style="display: none; margin-right:10px;">ELEGIR OTRO TIPO DE CARTA</a>

<h1>CREAR CARTA DE SOLICITUD DE POLIZAS</h1>
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
            <!-- Botón Solicitud de Pólizas -->
            <div class="col-12 col-md-4 mb-3 d-flex justify-content-center">
                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="showForm('solicitud-polizas')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-file-powerpoint fa-5x mb-2"></i>
                        <span class="h6 mb-0">SOLICITUD DE POLIZAS</span>
                    </div>
                </button>
            </div>
            <!-- Botón Reclamo de Solicitud de Pólizas -->
            <div class="col-12 col-md-4 mb-3 d-flex justify-content-center">
                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="showForm('reclamo-solicitud-polizas')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-file-export fa-5x mb-2"></i>
                        <span class="h6 mb-0">RECLAMO DE SOLICITUD DE POLIZAS</span>
                    </div>
                </button>
            </div>
            <!-- Botón Solicitud de Pólizas Generales -->
            <div class="col-12 col-md-4 mb-3 d-flex justify-content-center">
                <button type="button" class="btn btn-custom" style="width: 80%;" onclick="showForm('solicitud-polizas-generales')">
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <i class="fas fa-file-archive fa-5x mb-2"></i>
                        <span class="h6 mb-0">SOLICITUD DE POLIZAS GENERALES</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Formularios (Inicialmente ocultos) -->
    <div id="solicitud-polizas" class="form-container" style="display: none;">
        <div class="row">
            <div class="col-md-8"> 
                <div id="pages-container" class="pages-container">
                    <div class="page" id="page-1">
                        <div id="word-preview" style="font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.2; margin-left:40px; margin-right:40px;  margin-top:40px;">
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
                                <p style="text-align: center; margin-bottom: 40px;"><strong><u>REF: SOLICITUD DE POLIZAS</u></strong></p>
                                <p>Mediante la presente carta reciba usted mis más cordiales saludos, deseándole éxitos en sus labores.</p>
                                <p>Por la presente realizo la Solicitud de:</p>
                                <p style="margin-left: 40px; margin-bottom:1px;">1.	Pólizas Generales</p>
                                <p style="margin-left: 40px; margin-top:1px; margin-bottom:1px;">2.	Declaración de Salud </p>
                                <p style="margin-left: 40px; margin-top:1px;">3.	Póliza de Seguro de Desgravamen</p>
                                <p>Aclaro que el tomador del Seguro es el Banco, por lo mismo solicito las gestiones ante la Compañía Aseguradora.</p>
                                <p>Agradeciendo su atención.</p>

                                <p style="text-align: center; margin-top: 90px;"><span id="preview-clienteuno">[Sin Cliente 1]</span></p>
                                <p style="text-align: center; margin-top: -10px;">CI: <span id="preview-clienteunoci">[Sin CI 1]</span></p>

                                <div id="vista-previa-cliente2" style="display: none; text-align: center; margin-top: 90px;">
                                    <p>
                                        <span id="preview-clientedos">[Sin Cliente 2]</span>
                                    </p>
                                    <p id="linea-ci-clientedosci" style="margin-top: -10px;">
                                        CI: <span id="preview-clientedosci">[Sin CI 2]</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                {{-- <button type="button" id="btn-agregar-cliente2" class="btn btn-sm btn-outline-primary" onclick="mostrarCliente2()"><i class="fa fa-plus"></i> Otro Cliente</button> --}}
                {{--  style="display: none;" --}}
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.cartaspolizas.descargarsolicitudpolizas') }}">
                            @csrf
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
                            {{-- <div class="form-group">
                                <label for="clienteid">ID Cliente Auditoría:</label>
                                <input type="text" id="clienteid" name="clienteid" class="form-control" placeholder="Escriba ID del cliente y presione Enter">
                            </div>
                            <div class="form-group">
                                <label for="opcion">Seleccionar Banco o Seguro:</label>
                                <select id="opcion" name="opcion" class="form-control" required onchange="updatePreview()">
                                    <option value="" disabled selected>Seleccione una opción</option>
                                    @foreach($opciones as $opcion)
                                        <option value="{{ $opcion->nombreBanco ?? $opcion->nombreSeguro }}">
                                            {{ $opcion->nombreBanco ?? $opcion->nombreSeguro }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="clienteuno">Nombre Cliente:</label>
                                <input type="text" id="clienteuno" name="clienteuno" class="form-control" placeholder="Nombre del cliente uno" required oninput="updateClienteunoPreview()">
                            </div>
                            <div class="form-group">
                                <label for="clienteunoci">CI Cliente:</label>
                                <input type="text" id="clienteunoci" name="clienteunoci" class="form-control" placeholder="CI del cliente uno" required oninput="updateCiClienteunoPreview()">
                            </div>
                            <div id="cliente2-seccion">
                                <div class="form-group">
                                    <label for="clientedos">Nombre Esp/Cony.:</label>
                                    <input type="text" id="clientedos" name="clientedos" class="form-control" placeholder="Nombre del cliente dos" oninput="updateClientedosPreview()">
                                </div>
                                <div class="form-group">
                                    <label for="clientedosci">CI Esp/Cony.:</label>
                                    <input type="text" id="clientedosci" name="clientedosci" class="form-control" placeholder="CI del cliente dos" oninput="updateCiClientedosPreview()">
                                </div>
                            </div> --}}
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label for="clienteid">ID Cliente:</label>
                                    <input type="text" id="clienteid" name="clienteid" class="form-control" placeholder="Escriba ID del cliente y presione Enter">
                                </div>
                                <div class="form-group col-lg-8"> 
                                    <label for="opcion">Seleccionar Banco o Seguro:</label>
                                    <select id="opcion" name="opcion" class="form-control" required onchange="updatePreview()">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clienteuno">Nombre Cliente:</label>
                                    <input type="text" id="clienteuno" name="clienteuno" class="form-control" placeholder="Nombre del cliente uno" required oninput="updateClienteunoPreview()">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clienteunoci">CI Cliente:</label>
                                    <input type="text" id="clienteunoci" name="clienteunoci" class="form-control" placeholder="CI del cliente uno" oninput="updateCiClienteunoPreview()">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clientedos">Nombre Esp/Cony.:</label>
                                    <input type="text" id="clientedos" name="clientedos" class="form-control" placeholder="Nombre del cliente dos" oninput="updateClientedosPreview()">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clientedosci">CI Esp/Cony.:</label>
                                    <input type="text" id="clientedosci" name="clientedosci" class="form-control" placeholder="CI del cliente dos" oninput="updateCiClientedosPreview()">
                                </div>
                            </div>
                            <script>
                                let clientesAuditoria = @json($clientesAuditoria);
                            </script>
                            <script>
                                document.getElementById('clienteid').addEventListener('keypress', function(e) {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();

                                        let clienteId = this.value.trim();
                                        if(clienteId === '') return;

                                        let cliente = clientesAuditoria.find(c => c.id == clienteId);

                                        let opcionSelect = document.getElementById('opcion');
                                        let clienteuno = document.getElementById('clienteuno');
                                        let clienteunoci = document.getElementById('clienteunoci');
                                        let clientedos = document.getElementById('clientedos');
                                        let clientedosci = document.getElementById('clientedosci');

                                        if(cliente){
                                            opcionSelect.innerHTML = '<option value="" disabled selected>Seleccione una opción</option>';
                                            if(cliente.banco1) opcionSelect.innerHTML += `<option value="${cliente.banco1}">${cliente.banco1}</option>`;
                                            if(cliente.banco2) opcionSelect.innerHTML += `<option value="${cliente.banco2}">${cliente.banco2}</option>`;
                                            if(cliente.banco3) opcionSelect.innerHTML += `<option value="${cliente.banco3}">${cliente.banco3}</option>`;

                                            clienteuno.value = cliente.nombrecompleto ?? '';
                                            clienteunoci.value = cliente.ci ?? '';
                                            clientedos.value = cliente.nombreespcon ?? '';
                                            clientedosci.value = cliente.ciespcon ?? '';

                                            updateClienteunoPreview();
                                            updateCiClienteunoPreview();
                                            updateClientedosPreview();
                                            updateCiClientedosPreview();

                                            if(cliente.nombreespcon || cliente.ciespcon){
                                                document.getElementById('vista-previa-cliente2').style.display = 'block';
                                            }
                                        } else {
                                            opcionSelect.innerHTML = '<option value="" disabled selected>Seleccione una opción</option>';
                                            clienteuno.value = '';
                                            clienteunoci.value = '';
                                            clientedos.value = '';
                                            clientedosci.value = '';

                                            updateClienteunoPreview();
                                            updateCiClienteunoPreview();
                                            updateClientedosPreview();
                                            updateCiClientedosPreview();

                                            document.getElementById('vista-previa-cliente2').style.display = 'none';
                                            alert("⚠️ Cliente no encontrado");
                                        }
                                    }
                                });
                            </script>
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
                                <p style="text-align: center; margin-bottom: 40px;"><strong><u>REF: RECLAMO DE SOLICITUD DE POLIZAS</u></strong></p>
                                <p>Mediante la presente carta reciba usted mis más cordiales saludos, deseándole éxitos en sus labores.</p>
                                <p>Por la presente realizo el Reclamo de Solicitud de Pólizas de Desgravamen, Declaración de Salud y Pólizas Generales donde indique el porcentaje de Invalidez para el Seguro de Desgravamen.</p>
                                <p>Aclaro que el tomador del Seguro es el Banco, por lo mismo solicito las gestiones ante la Compañía Aseguradora.</p>
                                <p>Agradeciendo su atención.</p>

                                <p style="text-align: center; margin-top: 90px;"><span id="preview-clienteuno2">[Sin Cliente 1]</span></p>
                                <p style="text-align: center; margin-top: -10px;">CI: <span id="preview-clienteunoci2">[Sin CI 1]</span></p>

                                <div id="vista-previa-cliente22" style="display: none; text-align: center; margin-top: 90px;">
                                    <p>
                                        <span id="preview-clientedos2">[Sin Cliente 2]</span>
                                    </p>
                                    <p id="linea-ci-clientedosci2" style="margin-top: -10px;">
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
                        <form method="POST" action="{{ route('admin.cartaspolizas.descargarreclamosolicitudpolizas') }}">
                            @csrf
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
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label for="clienteid">ID Cliente:</label>
                                    <input type="text" id="clienteid2" name="clienteid" class="form-control" placeholder="Escriba ID del cliente y presione Enter">
                                </div>
                                <div class="form-group col-lg-8">
                                    <label for="opcion">Seleccionar Banco o Seguro:</label>
                                    <select id="opcion2" name="opcion" class="form-control" required onchange="updatePreview2()">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clienteuno">Nombre Cliente:</label>
                                    <input type="text" id="clienteuno2" name="clienteuno" class="form-control" placeholder="Nombre del cliente uno" required oninput="updateClienteunoPreview2()">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clienteunoci">CI Cliente:</label>
                                    <input type="text" id="clienteunoci2" name="clienteunoci" class="form-control" placeholder="CI del cliente uno" required oninput="updateCiClienteunoPreview2()">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clientedos">Nombre Esp/Cony.:</label>
                                    <input type="text" id="clientedos2" name="clientedos" class="form-control" placeholder="Nombre del cliente dos" oninput="updateClientedosPreview2()">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clientedosci">CI Esp/Cony.:</label>
                                    <input type="text" id="clientedosci2" name="clientedosci" class="form-control" placeholder="CI del cliente dos" oninput="updateCiClientedosPreview2()">
                                </div>
                            </div>
                            <script>
                                let clientesAuditoria = @json($clientesAuditoria);
                            </script>
                            <script>
                                document.getElementById('clienteid2').addEventListener('keypress', function(e) {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();

                                        let clienteId = this.value.trim();
                                        if(clienteId === '') return;

                                        let cliente = clientesAuditoria.find(c => c.id == clienteId);

                                        let opcionSelect = document.getElementById('opcion2');
                                        let clienteuno = document.getElementById('clienteuno2');
                                        let clienteunoci = document.getElementById('clienteunoci2');
                                        let clientedos = document.getElementById('clientedos2');
                                        let clientedosci = document.getElementById('clientedosci2');

                                        if(cliente){
                                            opcionSelect.innerHTML = '<option value="" disabled selected>Seleccione una opción</option>';
                                            if(cliente.banco1) opcionSelect.innerHTML += `<option value="${cliente.banco1}">${cliente.banco1}</option>`;
                                            if(cliente.banco2) opcionSelect.innerHTML += `<option value="${cliente.banco2}">${cliente.banco2}</option>`;
                                            if(cliente.banco3) opcionSelect.innerHTML += `<option value="${cliente.banco3}">${cliente.banco3}</option>`;

                                            clienteuno.value = cliente.nombrecompleto ?? '';
                                            clienteunoci.value = cliente.ci ?? '';
                                            clientedos.value = cliente.nombreespcon ?? '';
                                            clientedosci.value = cliente.ciespcon ?? '';

                                            updateClienteunoPreview2();
                                            updateCiClienteunoPreview2();
                                            updateClientedosPreview2();
                                            updateCiClientedosPreview2();

                                            if(cliente.nombreespcon || cliente.ciespcon){
                                                document.getElementById('vista-previa-cliente22').style.display = 'block';
                                            }
                                        } else {
                                            opcionSelect.innerHTML = '<option value="" disabled selected>Seleccione una opción</option>';
                                            clienteuno.value = '';
                                            clienteunoci.value = '';
                                            clientedos.value = '';
                                            clientedosci.value = '';

                                            updateClienteunoPreview2();
                                            updateCiClienteunoPreview2();
                                            updateClientedosPreview2();
                                            updateCiClientedosPreview2();

                                            document.getElementById('vista-previa-cliente22').style.display = 'none';
                                            alert("⚠️ Cliente no encontrado");
                                        }
                                    }
                                });
                            </script>
                            <button type="submit" class="btn btn-crear btn-block" style="margin-top: 10px;">GUARDAR Y GENERAR CARTA</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="solicitud-polizas-generales" class="form-container" style="display: none;">
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
                                    <p id="linea-ci-clientedosci3" style="margin-top: -10px;">
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
                            <div class="row">
                                <div class="form-group col-lg-4">
                                    <label for="clienteid">ID Cliente:</label>
                                    <input type="text" id="clienteid3" name="clienteid" class="form-control" placeholder="Escriba ID del cliente y presione Enter">
                                </div>
                                <div class="form-group col-lg-8">
                                    <label for="opcion">Seleccionar Banco o Seguro:</label>
                                    <select id="opcion3" name="opcion" class="form-control" required onchange="updatePreview3()">
                                        <option value="" disabled selected>Seleccione una opción</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clienteuno">Nombre Cliente:</label>
                                    <input type="text" id="clienteuno3" name="clienteuno" class="form-control" placeholder="Nombre del cliente uno" required oninput="updateClienteunoPreview3()">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clienteunoci">CI Cliente:</label>
                                    <input type="text" id="clienteunoci3" name="clienteunoci" class="form-control" placeholder="CI del cliente uno" required oninput="updateCiClienteunoPreview3()">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clientedos">Nombre Esp/Cony.:</label>
                                    <input type="text" id="clientedos3" name="clientedos" class="form-control" placeholder="Nombre del cliente dos" oninput="updateClientedosPreview3()">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label for="clientedosci">CI Esp/Cony.:</label>
                                    <input type="text" id="clientedosci3" name="clientedosci" class="form-control" placeholder="CI del cliente dos" oninput="updateCiClientedosPreview3()">
                                </div>
                            </div>
                            <script>
                                let clientesAuditoria = @json($clientesAuditoria);
                            </script>
                            <script>
                                document.getElementById('clienteid3').addEventListener('keypress', function(e) {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();

                                        let clienteId = this.value.trim();
                                        if(clienteId === '') return;

                                        let cliente = clientesAuditoria.find(c => c.id == clienteId);

                                        let opcionSelect = document.getElementById('opcion3');
                                        let clienteuno = document.getElementById('clienteuno3');
                                        let clienteunoci = document.getElementById('clienteunoci3');
                                        let clientedos = document.getElementById('clientedos3');
                                        let clientedosci = document.getElementById('clientedosci3');

                                        if(cliente){
                                            opcionSelect.innerHTML = '<option value="" disabled selected>Seleccione una opción</option>';
                                            if(cliente.banco1) opcionSelect.innerHTML += `<option value="${cliente.banco1}">${cliente.banco1}</option>`;
                                            if(cliente.banco2) opcionSelect.innerHTML += `<option value="${cliente.banco2}">${cliente.banco2}</option>`;
                                            if(cliente.banco3) opcionSelect.innerHTML += `<option value="${cliente.banco3}">${cliente.banco3}</option>`;

                                            clienteuno.value = cliente.nombrecompleto ?? '';
                                            clienteunoci.value = cliente.ci ?? '';
                                            clientedos.value = cliente.nombreespcon ?? '';
                                            clientedosci.value = cliente.ciespcon ?? '';

                                            updateClienteunoPreview3();
                                            updateCiClienteunoPreview3();
                                            updateClientedosPreview3();
                                            updateCiClientedosPreview3();

                                            if(cliente.nombreespcon || cliente.ciespcon){
                                                document.getElementById('vista-previa-cliente23').style.display = 'block';
                                            }
                                        } else {
                                            opcionSelect.innerHTML = '<option value="" disabled selected>Seleccione una opción</option>';
                                            clienteuno.value = '';
                                            clienteunoci.value = '';
                                            clientedos.value = '';
                                            clientedosci.value = '';

                                            updateClienteunoPreview3();
                                            updateCiClienteunoPreview3();
                                            updateClientedosPreview3();
                                            updateCiClientedosPreview3();

                                            document.getElementById('vista-previa-cliente23').style.display = 'none';
                                            alert("⚠️ Cliente no encontrado");
                                        }
                                    }
                                });
                            </script>
                            <button type="submit" class="btn btn-crear btn-block" style="margin-top: 10px;">GUARDAR Y GENERAR CARTA</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')

{{-- SOLICITUD DE POLIZAS --}}
<script>
    function updatePreview() {
        const ciudad = document.getElementById('ciudad').value || 'Santa Cruz';
        const fecha = document.getElementById('fecha').value || '[Sin seleccionar]';
        const opcion = document.getElementById('opcion').value || '[Sin seleccionar]';

        document.getElementById('preview-ciudad').innerText = ciudad;
        document.getElementById('preview-fecha-value').innerText = formatFecha(fecha);
        document.getElementById('preview-opcion').innerHTML = `<strong>${opcion}</strong>`;
    }

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
        const preview = document.getElementById('preview-clientedosci');
        preview.innerText = clientedosci || '[Sin CI 2]';
        const lineaCI = document.getElementById('linea-ci-clientedosci');

        if(clientedosci.trim() === '') {
            lineaCI.style.display = 'none';
        } else {
            lineaCI.style.display = 'block';
        }
    }
</script>

{{-- RECLAMO DE SOLICITUD DE POLIZAS --}}
<script>
    function updatePreview2() {
        const ciudad2 = document.getElementById('ciudad2').value || 'Santa Cruz';
        const fecha2 = document.getElementById('fecha2').value || '[Sin seleccionar]';
        const opcion2 = document.getElementById('opcion2').value || '[Sin seleccionar]';

        document.getElementById('preview-ciudad2').innerText = ciudad2;
        document.getElementById('preview-fecha-value2').innerText = formatFecha(fecha2);
        document.getElementById('preview-opcion2').innerHTML = `<strong>${opcion2}</strong>`;
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
        const preview = document.getElementById('preview-clientedosci2');
        preview.innerText = clientedosci2 || '[Sin CI 2]';
        const lineaCI = document.getElementById('linea-ci-clientedosci2');

        if(clientedosci2.trim() === '') {
            lineaCI.style.display = 'none';
        } else {
            lineaCI.style.display = 'block';
        }
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
        const preview = document.getElementById('preview-clientedosci3');
        preview.innerText = clientedosci3 || '[Sin CI 2]';
        const lineaCI = document.getElementById('linea-ci-clientedosci3');

        if(clientedosci3.trim() === '') {
            lineaCI.style.display = 'none';
        } else {
            lineaCI.style.display = 'block';
        }
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
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .page {
        width: 812px;
        height: 992px;
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
        padding: 5px 10px;
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

