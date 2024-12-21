@extends('adminlte::page')

@section('content_header')
<h1>RESERVAS MÉDICAS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/proveedoresinformes.css') }}">
<style>
.nav-link .circle {

    width: 30px; /* Tamaño del círculo */

}
.btn-crearproveedor {
background-color:  #ffffff;
color: #94c93b;
border-color: #94c93b;
border-radius: 5px;
}
.btn-crearproveedor:hover {
background-color: #94c93b;
color: #ffffff;
}
</style>
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
    {{-- <nav class="navbar navbar-expand-lg">
        <div class="container-fluid justify-content-end">
            <div class="d-flex flex-wrap align-items-center">
                <form id="search-form" action="{{ route('buscarreservamedicaclienteita') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="Nombre del Cliente">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                    <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" type="button">Mostrar Todo</button>
                </form>
            </div>
        </div>
    </nav> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            /* document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarreservamedicaclienteita') }}";
            }); */
    
            const activeTabId = localStorage.getItem('activeTab') || 'tab-1';
            const tabLink = document.querySelector(`a[href="#${activeTabId}"]`);
            if (tabLink) {
                tabLink.click();
            }
            document.querySelectorAll('#myTabs .nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    const href = this.getAttribute('href');
                    const tabId = href.substring(1);
                    localStorage.setItem('activeTab', tabId);
                });
            });
        });
    </script>
    {{-- <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarproveedor') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control buscador mr-sm-2" type="search" placeholder="ID / Proveedor / Ciudad" aria-label="Search">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>BUSCAR</button>
                    </form>
                </div>
            </div>
        </nav> --}}
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    ATENCIÓN PENDIENTE
                    <?php if ($atencionpendienteCount > 0): ?>
                        <span class="circle"><?= $atencionpendienteCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    INFORMES PENDIENTES
                    <?php if ($informependienteCount > 0): ?>
                        <span class="circle"><?= $informependienteCount ?></span>
                    <?php endif; ?>
                </a>
            </li> 
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    COMPLETOS
                    <?php if ($informecompletoCount > 0): ?>
                        <span class="circle"><?= $informecompletoCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    PAGOS MENSUALES
                </a>
            </li> --}}
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
        {{-- ATENCION PENDIENTE --}}
        <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tipo Cliente</th>
                            <th>ID Cliente</th>
                            <th>Cliente</th>
                            <th>Fecha Bateria</th>
                            @if ($rolusuario !== 'PROVEEDOR')
                            <th>Proveedor Asignado</th>
                            @endif
                            <th>Acción</th>
                            <th>Fecha asignada</th>
                            <th>Hora asignada</th>
                            <th colspan="3">Atención</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservasmedicas as $reservasmedica)
                            @if(!$reservasmedica->documentacionDisponible && !$reservasmedica->informeDisponible)
                                <tr>
                                    <td>CLIENTE ITA</td>
                                    <td>{{$reservasmedica->clienteitaid}}</td>
                                    <td>{{$reservasmedica->clienteitanombre}}</td>
                                    <td>{{$reservasmedica->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedica->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedica->accionnombre}}</td>
                                    <td>{{$reservasmedica->fechaasignada}}</td>
                                    <td>{{$reservasmedica->horadesde}} - {{$reservasmedica->horahasta}}</td>

                                    <td width="10px">
                                        @if($reservasmedica->informeDisponible)
                                            <abbr title="SUBIR INFORME">
                                                <button type="button" class="btn btn-subirinforme" 
                                                        data-toggle="modal" 
                                                        data-target="#subirinformeModal"
                                                        data-clienteitaid="{{ $reservasmedica->clienteitaid }}"
                                                        data-clienteitanombre="{{ $reservasmedica->clienteitanombre }}"
                                                        data-fechabateria="{{ $reservasmedica->fechabateria }}"
                                                        data-accion="{{ $reservasmedica->accionnombre }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </abbr>
                                        @else
                                            <p class="text-incompleto">PENDIENTE</p>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                            @if(!$reservasmedicaauditoria->documentacionDisponibleauditoria && !$reservasmedicaauditoria->informeDisponibleauditoria)
                                <tr>
                                    <td>CLIENTE AUDITORIA</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechaasignada}}</td>
                                    <td>{{$reservasmedicaauditoria->horadesde}} - {{$reservasmedicaauditoria->horahasta}}</td>

                                    <td width="10px">
                                        @if($reservasmedicaauditoria->informeDisponible)
                                            <abbr title="SUBIR INFORME">
                                                <button type="button" class="btn btn-subirinforme" 
                                                        data-toggle="modal" 
                                                        data-target="#subirinformeModal"
                                                        data-clienteauditoriaid="{{ $reservasmedicaauditoria->clienteitaid }}"
                                                        data-clienteauditorianombre="{{ $reservasmedicaauditoria->clienteitanombre }}"
                                                        data-fechabateria="{{ $reservasmedicaauditoria->fechabateria }}"
                                                        data-accion="{{ $reservasmedicaauditoria->accionnombre }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </abbr>
                                        @else
                                            <p class="text-incompleto">PENDIENTE</p>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- INFORME PENDIENTES --}}
        <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tipo Cliente</th>
                            <th>ID Cliente</th>
                            <th>Cliente</th>
                            <th>Fecha Bateria</th>
                            @if ($rolusuario !== 'PROVEEDOR')
                            <th>Proveedor Asignado</th>
                            @endif
                            <th>Acción</th>
                            <th>Fecha asignada</th>
                            <th>Hora asignada</th>
                            <th colspan="3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservasmedicas as $reservasmedica)
                            @if(!$reservasmedica->documentacionDisponible && $reservasmedica->informeDisponible)
                                <tr>
                                    <td>CLIENTE ITA</td>
                                    <td>{{$reservasmedica->clienteitaid}}</td>
                                    <td>{{$reservasmedica->clienteitanombre}}</td>
                                    <td>{{$reservasmedica->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedica->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedica->accionnombre}}</td>
                                    <td>{{$reservasmedica->fechaasignada}}</td>
                                    <td>{{$reservasmedica->horadesde}} - {{$reservasmedica->horahasta}}</td>
                                    
                                    {{-- FICHA MEDICA --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                        <td width="10px" style="padding-left: 1px; padding-right: 1px; justify-content: center;">
                                            @if($reservasmedica->fichamedicaita)
                                                <a href="{{ asset('/fichamedicaclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->fichamedicaita) }}" class="btn btn-sm btn-subirinf" target="_blank" title="VER FICHA MEDICA">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            @else
                                            <abbr title="CREAR FICHA MÉDICA">
                                                <a class="btn btn-sm btn-fichamedica" href="{{route('admin.asociados.crearformularioclienteita', $reservasmedica->clienteitaid)}}">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                    @endif

                                    {{-- DIAGNOSTICO MEDICO --}}
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $nombreusuario === 'MARICELA COLQUE SANDOVAL' || $nombreusuario === 'MONICA MACOÑO FLORES')
                                        <td width="10px">
                                            @if($reservasmedica->diagnosticomedicoita)
                                                <a href="{{ asset('/diagnosticos/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->diagnosticomedicoita) }}" class="btn btn-sm btn-subirdiagnostico" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-laptop-medical"></i>
                                                </a>
                                            @else
                                            <a href="{{ asset('/diagnosticos/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->diagnosticomedicoita) }}" 
                                                class="btn btn-sm btn-subirdiagnostico disabled" target="_blank" title="VER DIAGNÓSTICO">
                                                 <i class="fas fa-laptop-medical"></i>
                                             </a>
                                            <style>
                                                .btn.disabled {
                                                    pointer-events: none;
                                                    background-color: #d6d6d6;
                                                    color: #a5a5a5;
                                                    border-color: #d6d6d6;
                                                }
                                                .btn.disabled i {
                                                    color: #a5a5a5;
                                                }
                                            </style>                                             
                                                {{-- <abbr title="SUBIR DIAGNÓSTICO">
                                                    <button type="button" class="btn btn-sm btn-subirdiagnostico" 
                                                            data-toggle="modal" 
                                                            data-target="#subirdiagnosticoModal"
                                                            data-clienteitaid="{{ $reservasmedica->clienteitaid }}"
                                                            data-clienteitanombre="{{ $reservasmedica->clienteitanombre }}"
                                                            data-fechabateria="{{ $reservasmedica->fechabateria }}"
                                                            data-accion="{{ $reservasmedica->accionnombre }}">
                                                        <i class="fas fa-paste"></i>
                                                    </button>
                                                </abbr> --}}
                                            @endif
                                        </td>
                                        <!-- MODAL DIAGNOSTICO -->
                                        <div class="modal fade" id="subirdiagnosticoModal" tabindex="-1" role="dialog" aria-labelledby="subirdiagnosticoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="subirdiagnosticoModalLabel" style="color: #94c93b; font-weight: bold;">DIAGNÓSTICO</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    {!! Form::open(['route' => 'procesar.diagnostico', 'method' => 'POST', 'files' => true]) !!}
                                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                    {!! Form::hidden('clienteitaid', null, ['id' => 'modal-clienteitaid']) !!}
                                                    {!! Form::hidden('clienteitanombre', null, ['id' => 'modal-clienteitanombre']) !!}
                                                    {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                    {!! Form::hidden('accion', 'DIAGNÓSTICO MÉDICO') !!}
    
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-12"> 
                                                                <div class="file-upload">
                                                                    <label for="archivodiagnostico">DIAGNÓSTICO:</label>
                                                                    <input type="file" name="archivo" id="archivodiagnostico" accept=".pdf"/>
                                                                    <div class="file-preview" id="preview-archivo2"></div>
                                                                    @error('archivo')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('#subirdiagnosticoModal').on('show.bs.modal', function (event) {
                                                                var button = $(event.relatedTarget); // Botón que activó el modal
                                                                var clienteitaid = button.data('clienteitaid');
                                                                var clienteitanombre = button.data('clienteitanombre');
                                                                var fechabateria = button.data('fechabateria');
                                                                var accion = button.data('accion');
    
                                                                var modal = $(this);
                                                                modal.find('#modal-clienteitaid').val(clienteitaid);
                                                                modal.find('#modal-clienteitanombre').val(clienteitanombre);
                                                                modal.find('#modal-fechabateria').val(fechabateria);
                                                                modal.find('#modal-accion').val(accion);
                                                            });
                                                        });
                                                    </script>
                                                    <div class="modal-footer">
                                                        <div class="text-center w-100">
                                                            {!! Form::submit('SUBIR DIAGNÓSTICO', ['class' => 'btn btn-crear']) !!}
                                                        </div>
                                                    </div>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- CREAR BATERIA --}}
                                        <td width="10px" style="padding-left: 1px; padding-right: 1px; justify-content: center;">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crearproveedor" href="{{route('admin.asociados.crearbateriaclienteita', $reservasmedica->clienteitaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif
                                    <td width="10px">
                                        @if($reservasmedica->informeDisponible)
                                            <abbr title="SUBIR INFORME">
                                                <button type="button" class="btn btn-sm btn-subirinformeproveedor" 
                                                        data-toggle="modal" 
                                                        data-target="#subirinformeitaModal"
                                                        data-clienteitaid="{{ $reservasmedica->clienteitaid }}"
                                                        data-clienteitanombre="{{ $reservasmedica->clienteitanombre }}"
                                                        data-fechabateria="{{ $reservasmedica->fechabateria }}"
                                                        data-accion="{{ $reservasmedica->accionnombre }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </abbr>
                                        @else
                                            <p class="text-incompleto">FECHA DE ATENCIÓN PENDIENTE</p>
                                        @endif
                                    </td>
                                    <!-- Modal -->
                                    <div class="modal fade" id="subirinformeitaModal" tabindex="-1" role="dialog" aria-labelledby="subirinformeitaModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="subirinformeitaModalLabel" style="color: #94c93b; font-weight: bold;">SUBIR INFORME</h3>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                {!! Form::open(['route' => 'procesar.informe', 'method' => 'POST', 'files' => true]) !!}
                                                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                {!! Form::hidden('clienteitaid', null, ['id' => 'modal-clienteitaid']) !!}
                                                {!! Form::hidden('clienteitanombre', null, ['id' => 'modal-clienteitanombre']) !!}
                                                {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                {!! Form::hidden('accion', null, ['id' => 'modal-accion']) !!}

                                                <div class="modal-body">
                                                    <div class="row"> 
                                                        <!-- PDF -->
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="archivoita">INFORME:</label>
                                                                <input type="file" name="archivo" id="archivoita" class="file-input" accept=".pdf" onchange="handleFileSelectita(this, 'preview-archivoita')" />
                                                                <label for="archivoita" class="file-custom-label">Elige un PDF</label>
                                                                <div class="file-preview" id="preview-archivoita"></div>
                                                                @error('archivo')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    
                                                        <!-- Imagen 1 -->
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="picture">IMAGEN 1:</label>
                                                                <input type="file" name="picture" id="picture" accept="image/*" onchange="handleFileSelectita(this, 'preview-picture')" />
                                                                <div class="file-preview" id="preview-picture"></div>
                                                                @error('picture')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    
                                                        <!-- Imagen 2 -->
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="picture2">IMAGEN 2:</label>
                                                                <input type="file" name="picture2" id="picture2" accept="image/*" onchange="handleFileSelectita(this, 'preview-picture2')" />
                                                                <div class="file-preview" id="preview-picture2"></div>
                                                                @error('picture2')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <script>
                                                        function handleFileSelectita(input, previewId) {
                                                            const preview = document.getElementById(previewId);
                                                            preview.innerHTML = '';
                                                    
                                                            if (input.files && input.files[0]) {
                                                                const file = input.files[0];
                                                    
                                                                if (file.type === 'application/pdf') {
                                                                    // Muestra solo el nombre del archivo PDF
                                                                    const fileName = document.createElement('span');
                                                                    fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                    fileName.className = 'file-name';
                                                                    preview.appendChild(fileName);
                                                                } else if (file.type.startsWith('image/')) {
                                                                    // Muestra la vista previa de la imagen
                                                                    const fileURL = URL.createObjectURL(file);
                                                                    const img = document.createElement('img');
                                                                    img.src = fileURL;
                                                                    img.style.maxWidth = '100%';
                                                                    img.style.maxHeight = '300px';
                                                                    img.alt = 'Vista previa de la imagen';
                                                                    preview.appendChild(img);
                                                                } else {
                                                                    const fileName = document.createElement('span');
                                                                    fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                    fileName.className = 'file-name';
                                                                    preview.appendChild(fileName);
                                                                }
                                                            } else {
                                                                preview.textContent = 'No se ha seleccionado ningún archivo';
                                                            }
                                                        }
                                                    </script>
                                                    
                                                    <div class="row" hidden>
                                                        <div class="col-lg-6 text-center">
                                                            <label for="firma">Firma Digital:</label>
                                                            <div>
                                                                @if ($usuario->firmadigital)
                                                                    <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->firmadigital) }}" 
                                                                         alt="Firma Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                @else
                                                                    <p>No se ha cargado ninguna firma.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 text-center">
                                                            <label for="sello">Sello Digital:</label>
                                                            <div>
                                                                @if ($usuario->sellodigital)
                                                                    <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->sellodigital) }}" 
                                                                         alt="Sello Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                @else
                                                                    <p>No se ha cargado ningún sello.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="text-center w-100">
                                                        {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear']) !!}
                                                    </div>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>

                                    <td width="10px" style="padding-left: 1px; justify-content: center;">
                                        <abbr title="SUBIR DOCUMENTACION MULTIPLE">
                                            <a class="btn btn-sm btn-subirinf" href="{{route('admin.asociados.creardocumentacionclienteita', $reservasmedica->clienteitaid)}}">
                                                <i class="fas fa-archive"></i>
                                            </a>
                                        </abbr>
                                    </td>

                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $('#subirinformeitaModal').on('show.bs.modal', function (event) {
                                                var button = $(event.relatedTarget);
                                                var clienteitaid = button.data('clienteitaid');
                                                var clienteitanombre = button.data('clienteitanombre');
                                                var fechabateria = button.data('fechabateria');
                                                var accion = button.data('accion');

                                                var modal = $(this);
                                                modal.find('#modal-clienteitaid').val(clienteitaid);
                                                modal.find('#modal-clienteitanombre').val(clienteitanombre);
                                                modal.find('#modal-fechabateria').val(fechabateria);
                                                modal.find('#modal-accion').val(accion);
                                                var formAction = '{{ route("admin.asociados.guardardocumentacionclienteitadeproveedor", ":cliente") }}';
                                                formAction = formAction.replace(':cliente', clienteitaid);
                                                $('#subirinformeFormita').attr('action', formAction);
                                            });
                                        });
                                    </script>
                                </tr>
                            @endif
                        @endforeach
                        @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                            @if(!$reservasmedicaauditoria->documentacionDisponibleauditoria && $reservasmedicaauditoria->informeDisponibleauditoria)
                                <tr>
                                    <td>CLIENTE AUDITORIA</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechaasignada}}</td>
                                    <td>{{$reservasmedicaauditoria->horadesde}} - {{$reservasmedicaauditoria->horahasta}}</td>
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                        <td width="10px" style="padding-left: 1px; padding-right: 1px; justify-content: center;">
                                            @if($reservasmedicaauditoria->fichamedicaauditoria)
                                                <a href="{{ asset('/fichamedicaclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->fichamedicaauditoria) }}" class="btn btn-sm btn-verdocumentacion" target="_blank" title="VER FICHA MEDICA">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            @else
                                                <abbr title="CREAR FICHA MÉDICA">
                                                    <a class="btn btn-sm btn-fichamedica" href="{{ route('admin.asociados.crearformularioclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid) }}">
                                                        <i class="fas fa-file-signature"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        </td>
                                    
                                        <td width="10px">
                                            @if($reservasmedicaauditoria->diagnosticomedicoauditoria)
                                                <a href="{{ asset('/diagnosticosauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->diagnosticomedicoauditoria) }}" class="btn btn-sm btn-subirdiagnostico" target="_blank" title="VER DIAGNÓSTICO">
                                                    <i class="fas fa-laptop-medical"></i>
                                                </a>
                                            @else
                                            <a href="{{ asset('/diagnosticosauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->diagnosticomedicoauditoria) }}" 
                                                class="btn btn-sm btn-subirdiagnostico disabled" target="_blank" title="VER DIAGNÓSTICO">
                                                 <i class="fas fa-laptop-medical"></i>
                                             </a>
                                            <style>
                                            .btn.disabled {
                                                pointer-events: none;
                                                background-color: #d6d6d6;
                                                color: #a5a5a5;
                                                border-color: #d6d6d6;
                                            }
                                            .btn.disabled i {
                                                color: #a5a5a5;
                                            }
                                            </style>
                                                {{-- <abbr title="SUBIR DIAGNÓSTICO">
                                                    <button type="button" class="btn btn-sm btn-subirdiagnostico" 
                                                            data-toggle="modal" 
                                                            data-target="#subirdiagnosticoModal2"
                                                            data-clienteauditoriaid="{{ $reservasmedicaauditoria->clienteauditoriaid }}"
                                                            data-clienteauditorianombre="{{ $reservasmedicaauditoria->clienteauditorianombre }}"
                                                            data-fechabateria="{{ $reservasmedicaauditoria->fechabateria }}"
                                                            data-accion="{{ $reservasmedicaauditoria->accionnombre }}">
                                                        <i class="fas fa-paste"></i>
                                                    </button>
                                                </abbr> --}}
                                            @endif
                                        </td>
                                        <!-- DIAGNOSTICO -->
                                        <div class="modal fade" id="subirdiagnosticoModal2" tabindex="-1" role="dialog" aria-labelledby="subirdiagnosticoModal2Label" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title" id="subirdiagnosticoModal2Label" style="color: #94c93b; font-weight: bold;">DIAGNÓSTICO</h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    {!! Form::open(['route' => 'procesar.diagnosticoauditoria', 'method' => 'POST', 'files' => true]) !!}
                                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                    {!! Form::hidden('clienteauditoriaid', null, ['id' => 'modal-clienteauditoriaid']) !!}
                                                    {!! Form::hidden('clienteauditorianombre', null, ['id' => 'modal-clienteauditorianombre']) !!}
                                                    {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                    {!! Form::hidden('accion', 'DIAGNÓSTICO MÉDICO') !!}
    
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-12"> 
                                                                <div class="file-upload">
                                                                    <label for="archivo">DIAGNÓSTICO:</label>
                                                                    <input type="file" name="archivo" id="archivo" accept=".pdf"/>
                                                                    <div class="file-preview" id="preview-archivo2"></div>
                                                                    @error('archivo')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('#subirdiagnosticoModal2').on('show.bs.modal', function (event) {
                                                                var button = $(event.relatedTarget);
                                                                var clienteauditoriaid = button.data('clienteauditoriaid');
                                                                var clienteauditorianombre = button.data('clienteauditorianombre');
                                                                var fechabateria = button.data('fechabateria');
                                                                var accion = button.data('accion');
    
                                                                var modal = $(this);
                                                                modal.find('#modal-clienteauditoriaid').val(clienteauditoriaid);
                                                                modal.find('#modal-clienteauditorianombre').val(clienteauditorianombre);
                                                                modal.find('#modal-fechabateria').val(fechabateria);
                                                                modal.find('#modal-accion').val(accion);
                                                            });
                                                        });
                                                    </script>
                                                    <div class="modal-footer">
                                                        <div class="text-center w-100">
                                                            {!! Form::submit('SUBIR DIAGNÓSTICO', ['class' => 'btn btn-crear']) !!}
                                                        </div>
                                                    </div>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>

                                        <td width="10px" style="padding-left: 1px; padding-right: 1px; justify-content: center;">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crearproveedor" href="{{route('admin.asociados.crearbateriaclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif
                                    <td width="10px">
                                        @if($reservasmedicaauditoria->informeDisponibleauditoria)
                                            <abbr title="SUBIR INFORME">
                                                <button type="button" class="btn btn-sm btn-subirinformeproveedor" 
                                                        data-toggle="modal" 
                                                        data-target="#subirinformeModal5"
                                                        data-clienteauditoriaid="{{ $reservasmedicaauditoria->clienteauditoriaid }}"
                                                        data-clienteauditorianombre="{{ $reservasmedicaauditoria->clienteauditorianombre }}"
                                                        data-fechabateria="{{ $reservasmedicaauditoria->fechabateria }}"
                                                        data-accion="{{ $reservasmedicaauditoria->accionnombre }}">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </abbr>
                                        @else
                                            <p class="text-incompleto">FECHA DE ATENCIÓN PENDIENTE</p>
                                        @endif
                                    </td>
                                    <!-- Modal -->
                                    <div class="modal fade" id="subirinformeModal5" tabindex="-1" role="dialog" aria-labelledby="subirinformeModal5Label" aria-hidden="true">
                                        <div class="modal-dialog modal-xl" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="subirinformeModal5Label" style="color: #94c93b; font-weight: bold;">SUBIR INFORME</h3>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                {!! Form::open(['route' => 'procesar.informeauditoria', 'method' => 'POST', 'files' => true]) !!}
                                                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                {!! Form::hidden('clienteauditoriaid', null, ['id' => 'modal-clienteauditoriaid']) !!}
                                                {!! Form::hidden('clienteauditorianombre', null, ['id' => 'modal-clienteauditorianombre']) !!}
                                                {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                {!! Form::hidden('accion', null, ['id' => 'modal-accion']) !!}

                                                <div class="modal-body">
                                                    <div class="row"> 
                                                        <!-- PDF -->
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="archivoauditoria">INFORME:</label>
                                                                <input type="file" name="archivo" id="archivoauditoria" class="file-input" accept=".pdf" onchange="handleFileSelect(this, 'preview-archivoauditoria')" />
                                                                <label for="archivoauditoria" class="file-custom-label">Elige un PDF</label>
                                                                <div class="file-preview" id="preview-archivoauditoria"></div>
                                                                @error('archivo')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    
                                                        <!-- Imagen 1 -->
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="picture">IMAGEN 1:</label>
                                                                <input type="file" name="picture" id="picture" accept="image/*" onchange="handleFileSelect(this, 'preview-pictureauditoria1')" />
                                                                <div class="file-preview" id="preview-pictureauditoria1"></div>
                                                                @error('picture')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    
                                                        <!-- Imagen 2 -->
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="picture2">IMAGEN 2:</label>
                                                                <input type="file" name="picture2" id="picture2" accept="image/*" onchange="handleFileSelect(this, 'preview-pictureauditoria2')" />
                                                                <div class="file-preview" id="preview-pictureauditoria2"></div>
                                                                @error('picture2')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <script>
                                                        function handleFileSelect(input, previewId) {
                                                            const preview = document.getElementById(previewId);
                                                            preview.innerHTML = '';
                                                    
                                                            if (input.files && input.files[0]) {
                                                                const file = input.files[0];
                                                    
                                                                if (file.type === 'application/pdf') {
                                                                    const fileName = document.createElement('span');
                                                                    fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                    fileName.className = 'file-name';
                                                                    preview.appendChild(fileName);
                                                                } else if (file.type.startsWith('image/')) {
                                                                    const fileURL = URL.createObjectURL(file);
                                                                    const img = document.createElement('img');
                                                                    img.src = fileURL;
                                                                    img.style.maxWidth = '100%';
                                                                    img.style.maxHeight = '300px';
                                                                    img.alt = 'Vista previa de la imagen';
                                                                    preview.appendChild(img);
                                                                } else {
                                                                    const fileName = document.createElement('span');
                                                                    fileName.textContent = `Archivo seleccionado: ${file.name}`;
                                                                    fileName.className = 'file-name';
                                                                    preview.appendChild(fileName);
                                                                }
                                                            } else {
                                                                preview.textContent = 'No se ha seleccionado ningún archivo';
                                                            }
                                                        }
                                                    </script>
                                                    <div class="row" hidden>
                                                        <div class="col-lg-6 text-center">
                                                            <label for="firma">Firma Digital:</label>
                                                            <div>
                                                                @if ($usuario->firmadigital)
                                                                    <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->firmadigital) }}" 
                                                                         alt="Firma Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                @else
                                                                    <p>No se ha cargado ninguna firma.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 text-center">
                                                            <label for="sello">Sello Digital:</label>
                                                            <div>
                                                                @if ($usuario->sellodigital)
                                                                    <img src="{{ asset('/glfirmasello/' . $usuario->id . '/' . $usuario->sellodigital) }}" 
                                                                         alt="Sello Digital" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                                                @else
                                                                    <p>No se ha cargado ningún sello.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <div class="text-center w-100">
                                                        {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear']) !!}
                                                    </div>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                    <td width="10px" style="padding-left: 1px; justify-content: center;">
                                        <abbr title="SUBIR DOCUMENTACION MULTIPLE">
                                            <a class="btn btn-sm btn-subirinf" href="{{route('admin.asociados.creardocumentacionclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid)}}">
                                                <i class="fas fa-archive"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $('#subirinformeModal5').on('show.bs.modal', function (event) {
                                                var button = $(event.relatedTarget);
                                                var clienteauditoriaid = button.data('clienteauditoriaid');
                                                var clienteauditorianombre = button.data('clienteauditorianombre');
                                                var fechabateria = button.data('fechabateria');
                                                var accion = button.data('accion');

                                                var modal = $(this);
                                                modal.find('#modal-clienteauditoriaid').val(clienteauditoriaid);
                                                modal.find('#modal-clienteauditorianombre').val(clienteauditorianombre);
                                                modal.find('#modal-fechabateria').val(fechabateria);
                                                modal.find('#modal-accion').val(accion);
                                                var formAction = '{{ route("admin.asociados.guardardocumentacionclienteauditoriadeproveedor", ":clienteauditoria") }}';
                                                formAction = formAction.replace(':cliente', clienteauditoriaid);
                                                $('#subirinformeFormauditoria').attr('action', formAction);
                                            });
                                        });
                                    </script>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- INFORMES COMPLETOS --}}
        <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tipo Cliente</th>
                            <th>ID Cliente</th>
                            <th>Cliente</th>
                            <th>Fecha Bateria</th>
                            @if ($rolusuario !== 'PROVEEDOR')
                            <th>Proveedor Asignado</th>
                            @endif
                            <th>Acción</th>
                            <th>Fecha asignada</th>
                            <th>Hora asignada</th>
                            <th>Fecha registro</th>
                            <th colspan="3">Informe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservasmedicas as $reservasmedica)
                            @if($reservasmedica->documentacionDisponible)
                                <tr>
                                    <td>CLIENTE ITA</td>
                                    <td>{{$reservasmedica->clienteitaid}}</td>
                                    <td>{{$reservasmedica->clienteitanombre}}</td>
                                    <td>{{$reservasmedica->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedica->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedica->accionnombre}}</td>
                                    <td>{{$reservasmedica->fechaasignada}}</td>
                                    <td>{{$reservasmedica->horadesde}} - {{$reservasmedica->horahasta}}</td>
                                    <td>{{$reservasmedica->fechainforme}}</td>
                                    
                                    <td width="10px">
                                        
                                        <div class="dropdown-container2">
                                            <button class="btn btn-dropdown2" type="button">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                            
                                            <div class="dropdown-menu2">
                                                <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->documentacionDisponible) }}" class="btn btn-sm btn-verdocumentacion2" target="_blank" title="VER INFORME MÉDICO">
                                                    <i class="fas fa-folder-open"></i>
                                                </a>
                                                    @if($reservasmedica->imagen1Disponible)
                                                        <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->imagen1Disponible) }}" class="btn btn-verdocumentacion2" target="_blank" title="VER IMAGEN 1">
                                                            <i class="fas fa-images"></i>
                                                        </a>
                                                    @endif
                                                    @if($reservasmedica->imagen2Disponible)
                                                        <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->imagen2Disponible) }}" class="btn btn-verdocumentacion2" target="_blank" title="VER IMAGEN 2">
                                                            <i class="far fa-images"></i>
                                                        </a>
                                                    @endif
                                            </div>
                                        </div>   
                                    </td>  
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                        <td width="10px" style="padding-left: 1px; padding-right: 1px; justify-content: center;">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crear2" href="{{route('admin.asociados.crearbateriaclienteita', $reservasmedica->clienteitaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif
                                    
                                    <td width="10px">
                                        <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->documentacionfirmadaDisponible) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME MÉDICO FIRMADO">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                            @if($reservasmedicaauditoria->documentacionDisponibleauditoria)
                                <tr>
                                    <td>CLIENTE AUDITORIA</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechabateria}}</td>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                    @endif
                                    <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechaasignada}}</td>
                                    <td>{{$reservasmedicaauditoria->horadesde}} - {{$reservasmedicaauditoria->horahasta}}</td>
                                    <td>{{$reservasmedicaauditoria->fechainformeauditoria}}</td>
                                    
                                    <td width="10px">
                                        <div class="dropdown-container2">
                                            <button class="btn btn-dropdown2" type="button">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                            <div class="dropdown-menu2">
                                                <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->documentacionDisponibleauditoria) }}" class="btn btn-sm btn-verdocumentacion2" target="_blank" title="VER INFORME MÉDICO">
                                                    <i class="fas fa-folder-open"></i>
                                                </a>
                                                    @if($reservasmedicaauditoria->imagen1Disponibleauditoria)
                                                        <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->imagen1Disponibleauditoria) }}" class="btn btn-verdocumentacion2" target="_blank" title="VER IMAGEN 1">
                                                            <i class="fas fa-images"></i>
                                                        </a>
                                                    @endif
                                                    @if($reservasmedicaauditoria->imagen2Disponibleauditoria)
                                                        <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->imagen2Disponibleauditoria) }}" class="btn btn-verdocumentacion2" target="_blank" title="VER IMAGEN 2">
                                                            <i class="far fa-images"></i>
                                                        </a>
                                                    @endif
                                            </div>
                                        </div>   
                                    </td> 
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                        <td width="10px" style="padding-left: 1px; padding-right: 1px; justify-content: center;">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crear2" href="{{route('admin.asociados.crearbateriaclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif  
                                    
                                    <td width="10px">
                                        <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->documentacionfirmadaauditoriaDisponible) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME MÉDICO FIRMADO">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGOS MENSUALES --}}
        {{-- <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">

            @if ($rolusuario !== 'PROVEEDOR')
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid justify-content-end">
                    <div class="d-flex flex-wrap align-items-center">
                        <form id="search-form" action="{{ route('buscarporproveedor') }}" method="get" class="form-inline">
                            <div class="flex-grow-1">
                                <select name="proveedor" class="form-control mr-sm-2">
                                    <option value="">Seleccionar Proveedor</option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value="{{ $proveedor->proveedornombre }}" {{ request('proveedor') == $proveedor->proveedornombre ? 'selected' : '' }}>
                                            {{ $proveedor->proveedornombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                            <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" type="button">Mostrar Todo</button>
                        </form>
                    </div>
                </div>
            </nav>
            @endif
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                        window.location.href = "{{ route('buscarporproveedor') }}";
                    });
            
                    const activeTabId = localStorage.getItem('activeTab') || 'tab-1';
                    const tabLink = document.querySelector(`a[href="#${activeTabId}"]`);
                    if (tabLink) {
                        tabLink.click();
                    }
                    document.querySelectorAll('#myTabs .nav-link').forEach(function(link) {
                        link.addEventListener('click', function() {
                            const href = this.getAttribute('href');
                            const tabId = href.substring(1);
                            localStorage.setItem('activeTab', tabId);
                        });
                    });
                });
            </script>
            <script>
                $(document).ready(function() {
                    $('input[name="buscarporproveedor"]').on('keyup change', function() {
                        var proveedorSeleccionado = $('input[name="buscarporproveedor"]').val();
                        var botonBuscar = $('#btn-buscar');
                        
                        if (proveedorSeleccionado.trim() === '') {
                            botonBuscar.prop('disabled', true);
                        } else {
                            botonBuscar.prop('disabled', false);
                        }
                    });
                });
            </script>
            <!-- Pestañas de Navegación -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                @php
                    use Carbon\Carbon;
                    Carbon::setLocale('es');
                    $currentMonth = Carbon::now()->month;
                    $currentYear = Carbon::now()->year;
                    $currentMonthName = Carbon::now()->translatedFormat('F Y');
                    $currentMonthName = strtoupper($currentMonthName);

                    $previousMonth = Carbon::now()->subMonth()->month;
                    $previousYear = Carbon::now()->subMonth()->year;
                    $previousMonthName = Carbon::now()->subMonth()->translatedFormat('F Y');
                    $previousMonthName = strtoupper($previousMonthName);
                @endphp

                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="previous-month-tab" data-toggle="tab" href="#previous-month" role="tab" aria-controls="previous-month" aria-selected="true">{{ $previousMonthName }}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="current-month-tab" data-toggle="tab" href="#current-month" role="tab" aria-controls="current-month" aria-selected="true">{{ $currentMonthName }}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="pagosaprobadostab" data-toggle="tab" href="#pagosaprobados" role="tab" aria-controls="pagosaprobados" aria-selected="true">PAGOS APROBADOS</a>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="myTabContent">

                <!-- Pestaña Mes Pasado -->
                <div class="tab-pane fade show active" id="previous-month" role="tabpanel" aria-labelledby="previous-month-tab">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Cliente</th>
                                    <th>Cliente</th>
                                    <th>Fecha Bateria</th>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <th>Proveedor Asignado</th>
                                    @endif
                                    <th>Acción</th>
                                    <th>Fecha Informe</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalPreviousMonth = 0;
                                    $previousMonth = Carbon::now()->subMonth()->month;
                                    $previousYear = Carbon::now()->subMonth()->year;
                                @endphp

                                @foreach ($reservasmedicas as $reservasmedica)
                                    @if($reservasmedica->documentacionDisponible)
                                        @php
                                            $fechaInforme = Carbon::parse($reservasmedica->fechainforme);
                                        @endphp

                                        @if($fechaInforme->month == $previousMonth && $fechaInforme->year == $previousYear)
                                            @php
                                                $totalPreviousMonth += $reservasmedica->precio;
                                            @endphp
                                            <tr>
                                                <td>{{$reservasmedica->clienteitaid}}</td>
                                                <td>{{$reservasmedica->clienteitanombre}}</td>
                                                <td>{{$reservasmedica->fechabateria}}</td>
                                                @if ($rolusuario !== 'PROVEEDOR')
                                                <td>{{$reservasmedica->proveedornombre}}</td>
                                                @endif
                                                <td>{{$reservasmedica->accionnombre}}</td>
                                                <td>{{ $fechaInforme->format('Y-m-d') }}</td>
                                                <td>{{ number_format($reservasmedica->precio, 2) }}</td>                              
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                                @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                                    @if($reservasmedicaauditoria->documentacionDisponible)
                                        @php
                                            $fechaInforme = Carbon::parse($reservasmedicaauditoria->fechainforme);
                                        @endphp

                                        @if($fechaInforme->month == $previousMonth && $fechaInforme->year == $previousYear)
                                            @php
                                                $totalPreviousMonth += $reservasmedicaauditoria->precio;
                                            @endphp
                                            <tr>
                                                <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                                <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                                <td>{{$reservasmedicaauditoria->fechabateria}}</td>
                                                @if ($rolusuario !== 'PROVEEDOR')
                                                <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                                @endif
                                                <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                                <td>{{ $fechaInforme->format('Y-m-d') }}</td>
                                                <td>{{ number_format($reservasmedicaauditoria->precio, 2) }}</td>                              
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            </tbody>
                            @if ($rolusuario === 'PROVEEDOR')
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right" style="background: #fff4e4"><strong>Total:</strong></td>
                                    <td style="background: #fff4e4"><strong>{{ number_format($totalPreviousMonth, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                            @endif
                            @if ($rolusuario !== 'PROVEEDOR')
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-right" style="background: #fff4e4"><strong>Total:</strong></td>
                                    <td style="background: #fff4e4"><strong>{{ number_format($totalPreviousMonth, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Pestaña Mes Actual -->
                <div class="tab-pane fade" id="current-month" role="tabpanel" aria-labelledby="current-month-tab">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Cliente</th>
                                    <th>Cliente</th>
                                    <th>Fecha Bateria</th>
                                    @if ($rolusuario !== 'PROVEEDOR')
                                    <th>Proveedor Asignado</th>
                                    @endif
                                    <th>Acción</th>
                                    <th>Fecha Informe</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalCurrentMonth = 0;
                                    $currentMonth = Carbon::now()->month;
                                    $currentYear = Carbon::now()->year;
                                @endphp

                                @foreach ($reservasmedicas as $reservasmedica)
                                    @if($reservasmedica->documentacionDisponible)
                                        @php
                                            $fechaInforme = Carbon::parse($reservasmedica->fechainforme);
                                        @endphp

                                        @if($fechaInforme->month == $currentMonth && $fechaInforme->year == $currentYear)
                                            @php
                                                $totalCurrentMonth += $reservasmedica->precio;
                                            @endphp
                                            <tr>
                                                <td>{{$reservasmedica->clienteitaid}}</td>
                                                <td>{{$reservasmedica->clienteitanombre}}</td>
                                                <td>{{$reservasmedica->fechabateria}}</td>
                                                @if ($rolusuario !== 'PROVEEDOR')
                                                <td>{{$reservasmedica->proveedornombre}}</td>
                                                @endif
                                                <td>{{$reservasmedica->accionnombre}}</td>
                                                <td>{{ $fechaInforme->format('Y-m-d') }}</td>
                                                <td>{{ number_format($reservasmedica->precio, 2) }}</td>                              
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                                @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                                    @if($reservasmedicaauditoria->documentacionDisponible)
                                        @php
                                            $fechaInforme = Carbon::parse($reservasmedicaauditoria->fechainforme);
                                        @endphp

                                        @if($fechaInforme->month == $currentMonth && $fechaInforme->year == $currentYear)
                                            @php
                                                $totalCurrentMonth += $reservasmedicaauditoria->precio;
                                            @endphp
                                            <tr>
                                                <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                                <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                                <td>{{$reservasmedicaauditoria->fechabateria}}</td>
                                                @if ($rolusuario !== 'PROVEEDOR')
                                                <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                                @endif
                                                <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                                <td>{{ $fechaInforme->format('Y-m-d') }}</td>
                                                <td>{{ number_format($reservasmedicaauditoria->precio, 2) }}</td>                              
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            </tbody>
                            @if ($rolusuario === 'PROVEEDOR')
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right" style="background: #fff4e4"><strong>Total:</strong></td>
                                    <td style="background: #fff4e4"><strong>{{ number_format($totalCurrentMonth, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                            @endif
                            @if ($rolusuario !== 'PROVEEDOR')
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-right" style="background: #fff4e4"><strong>Total:</strong></td>
                                    <td style="background: #fff4e4"><strong>{{ number_format($totalCurrentMonth, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Pestaña Pagos aprobados -->
                <div class="tab-pane fade" id="pagosaprobados" role="tabpanel" aria-labelledby="pagosaprobadostab">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Cliente</th>
                                    <th>Cliente</th>
                                    <th>Fecha Bateria</th>
                                    <th>Proveedor Asignado</th>
                                    <th>Acción</th>
                                    <th>Fecha Informe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservasmedicas as $reservasmedica)
                                <tr>
                                    <td>{{$reservasmedica->clienteitaid}}</td>
                                    <td>{{$reservasmedica->clienteitanombre}}</td>
                                    <td>{{$reservasmedica->fechabateria}}</td>
                                    <td>{{$reservasmedica->proveedornombre}}</td>
                                    <td>{{$reservasmedica->accionnombre}}</td>
                                    <td>{{ $fechaInforme->format('Y-m-d') }}</td>                           
                                </tr>
                                @endforeach
                                @foreach ($reservasmedicasauditorias as $reservasmedicaauditoria)
                                <tr>
                                    <td>{{$reservasmedicaauditoria->clienteauditoriaid}}</td>
                                    <td>{{$reservasmedicaauditoria->clienteauditorianombre}}</td>
                                    <td>{{$reservasmedicaauditoria->fechabateria}}</td>
                                    <td>{{$reservasmedicaauditoria->proveedornombre}}</td>
                                    <td>{{$reservasmedicaauditoria->accionnombre}}</td>
                                    <td>{{ $fechaInforme->format('Y-m-d') }}</td>                           
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>
@stop


@section('js')
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

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

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
      'success')
    </script>
    @endif

    <script>
        $('.formulario-eliminar').submit(function(e){
            e.preventDefault();
    
            Swal.fire({
            title: '¿Estás seguro?',
            text: "Este perfil se eliminará definitivamente",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '¡Si, eliminar!',
            cancelButtonText: 'Cancelar'
            }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
            }) 
        });
        $(document).ready(function() {
            $('input[name="buscarpor"]').on('keyup', function() {
                var query = $(this).val();
                var botonBuscar = $('#btn-buscar');
                if (query.trim() === '') {
                    botonBuscar.prop('disabled', true);
                } else {
                    botonBuscar.prop('disabled', false);
                }
            });
        });
    </script>
    
@endsection
