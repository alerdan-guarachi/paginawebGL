@extends('adminlte::page')

@section('content_header')

<h1>RESERVAS MÉDICAS</h1>
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

        {{-- INFORME PENDIENTE --}}
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
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                        <td width="10px">
                                            @if($reservasmedica->fichamedicaita)
                                                <a href="{{ asset('/fichamedicaclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->fichamedicaita) }}" class="btn btn-verdocumentacion" target="_blank" title="VER FICHA MEDICA">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                            <abbr title="CREAR FICHA MÉDICA">
                                                <a class="btn btn-sm btn-fichamedica" href="{{route('admin.asociados.crearformularioclienteita', $reservasmedica->clienteitaid)}}">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                        <td width="10px">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crear" href="{{route('admin.asociados.crearbateriaclienteita', $reservasmedica->clienteitaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif
                                    <td width="10px">
                                        @if($reservasmedica->informeDisponible)
                                            <abbr title="SUBIR INFORME">
                                                <button type="button" class="btn btn-sm btn-subirinforme" 
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
                                            <p class="text-incompleto">FECHA DE ATENCIÓN PENDIENTE</p>
                                        @endif
                                    </td>
                                    <!-- Modal -->
                                    <div class="modal fade" id="subirinformeModal" tabindex="-1" role="dialog" aria-labelledby="subirinformeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="subirinformeModalLabel" style="color: #94c93b; font-weight: bold;">SUBIR INFORME</h3>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                {!! Form::open(['id' => 'subirinformeForm', 'method' => 'POST', 'files' => true]) !!}
                                                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                {!! Form::hidden('clienteitaid', null, ['id' => 'modal-clienteitaid']) !!}
                                                {!! Form::hidden('clienteitanombre', null, ['id' => 'modal-clienteitanombre']) !!}
                                                {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                {!! Form::hidden('accion', null, ['id' => 'modal-accion']) !!}

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="archivo">INFORME:</label>
                                                                <input type="file" name="archivo" id="archivo" class="file-input" accept=".pdf,.doc,.docx" />
                                                                <label for="archivo" class="file-custom-label">Elige un PDF</label>
                                                                <div class="file-preview" id="preview-archivo"></div>
                                                                @error('archivo')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="picture">IMAGEN 1:</label>
                                                                <input type="file" name="picture" id="picture" class="file-input" accept="image/*" />
                                                                <label for="picture" class="file-custom-label">Elige una imagen</label>
                                                                <div class="file-preview" id="preview-picture"></div>
                                                                @error('picture')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="picture2">IMAGEN 2:</label>
                                                                <input type="file" name="picture2" id="picture2" class="file-input" accept="image/*" />
                                                                <label for="picture2" class="file-custom-label">Elige una imagen</label>
                                                                <div class="file-preview" id="preview-picture2"></div>
                                                                @error('picture2')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
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
                                                
                                                <style>
                                                    .file-upload {
                                                        position: relative;
                                                        display: flex;
                                                        flex-direction: column;
                                                        align-items: center;
                                                        border: 2px solid #faa625; /* Border color */
                                                        border-radius: 8px; /* Rounded corners */
                                                        padding: 50px;
                                                        background: rgb(249, 255, 244); /* Background color */
                                                        transition: background 0.3s ease, border-color 0.3s ease;
                                                    }
                                                
                                                    .file-upload:hover {
                                                        background: #fff7eb; /* Light background on hover */
                                                        border-color: #faa625; /* Darker border on hover */
                                                    }
                                                
                                                    .file-upload label {
                                                        font-weight: bold;
                                                        margin-bottom: 8px;
                                                    }
                                                
                                                    .file-input {
                                                        display: none; /* Hide the default file input */
                                                    }
                                                
                                                    .file-custom-label {
                                                        display: inline-block;
                                                        padding: 10px;
                                                        border: 1px solid #faa625; /* Border for the custom button */
                                                        border-radius: 4px; /* Rounded corners for button */
                                                        background: #fff; /* Background color of button */
                                                        color: #faa625; /* Text color */
                                                        cursor: pointer;
                                                        transition: background 0.3s ease;
                                                    }
                                                
                                                    .file-custom-label:hover {
                                                        background: #faa625; /* Darker background on hover */
                                                        color: #fff; /* Text color */
                                                    }
                                                
                                                    .file-preview {
                                                        margin-top: 10px;
                                                        width: 100%;
                                                        height: auto;
                                                        display: flex;
                                                        justify-content: center;
                                                        align-items: center;
                                                        text-align: center;
                                                    }
                                                
                                                    .file-preview img {
                                                        max-width: 100%;
                                                        max-height: 200px;
                                                        border-radius: 8px;
                                                        border: 2px solid #faa625; /* Border for preview image */
                                                    }
                                                
                                                    .file-preview .file-name {
                                                        font-size: 16px;
                                                        color: #007bff;
                                                    }
                                                
                                                </style>      
                                                <script>
                                                    function handleFileSelect(event) {
                                                        const input = event.target;
                                                        const preview = document.getElementById(`preview-${input.id}`);
                                                
                                                        // Clear any existing previews
                                                        preview.innerHTML = '';
                                                
                                                        if (input.files && input.files[0]) {
                                                            const file = input.files[0];
                                                            const fileURL = URL.createObjectURL(file);
                                                
                                                            if (file.type.startsWith('image/')) {
                                                                // If the file is an image
                                                                const img = document.createElement('img');
                                                                img.src = fileURL;
                                                                preview.appendChild(img);
                                                            } else {
                                                                // For PDF and other file types
                                                                const fileName = document.createElement('span');
                                                                fileName.textContent = file.name;
                                                                fileName.className = 'file-name';
                                                                preview.appendChild(fileName);
                                                            }
                                                        }
                                                    }
                                                
                                                    document.querySelectorAll('.file-input').forEach(input => {
                                                        input.addEventListener('change', handleFileSelect);
                                                    });



                                                    function agregarFirmaYSelloPDF($rutaCarpeta, $archivoPDF, $request) {
    $pdfPath = $rutaCarpeta . '/' . $archivoPDF;
    $outputPath = $rutaCarpeta . '/firmado_' . $archivoPDF;

    $fpdi = new Fpdi();

    // Carga las imágenes de la firma y el sello
    $firmaPath = public_path('/glfirmasello/' . auth()->user()->id . '/' . auth()->user()->firmadigital);
    $selloPath = public_path('/glfirmasello/' . auth()->user()->id . '/' . auth()->user()->sellodigital);

    if (!file_exists($firmaPath) || !file_exists($selloPath)) {
        throw new \Exception('Firma o sello no encontrados.');
    }

    // Cargar el archivo PDF original
    $pageCount = $fpdi->setSourceFile($pdfPath);

    // Agregar firma y sello en cada página
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $templateId = $fpdi->importPage($pageNo);
        $size = $fpdi->getTemplateSize($templateId);

        $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $fpdi->useTemplate($templateId);

        // Coordenadas para colocar la firma y el sello
        $firmaX = $size['width'] - 50; // Margen derecho
        $firmaY = $size['height'] - 30; // Margen inferior

        $selloX = $size['width'] - 100; // A la izquierda de la firma
        $selloY = $firmaY;

        // Agregar firma
        $fpdi->Image($firmaPath, $firmaX, $firmaY, 40); // 40 = Ancho de la imagen

        // Agregar sello
        $fpdi->Image($selloPath, $selloX, $selloY, 40);
    }

    // Guardar el nuevo archivo PDF
    $fpdi->Output('F', $outputPath);

    return $outputPath;
}
                                                </script>
    
                                                <div class="modal-footer">
                                                    <div class="text-center w-100">
                                                        {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear']) !!}
                                                    </div>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                    <td width="10px">
                                        <abbr title="SUBIR DOCUMENTACION MULTIPLE">
                                            <a class="btn btn-sm btn-subirinf" href="{{route('admin.asociados.creardocumentacionclienteita', $reservasmedica->clienteitaid)}}">
                                                <i class="fas fa-archive"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $('#subirinformeModal').on('show.bs.modal', function (event) {
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
                                                // Actualiza la ruta del formulario para incluir el clienteitaid
                                                var formAction = '{{ route("admin.asociados.guardardocumentacionclienteitadeproveedor", ":cliente") }}';
                                                formAction = formAction.replace(':cliente', clienteitaid);
                                                $('#subirinformeForm').attr('action', formAction);
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
                                        <td width="10px">
                                            @if($reservasmedicaauditoria->$tienefichamedicaauditoria)
                                                <a href="{{ asset('/fichamedicaclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->fichamedicaauditoria) }}" class="btn btn-verdocumentacion" target="_blank" title="VER FICHA MEDICA">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                            <abbr title="CREAR FICHA MÉDICA">
                                                <a class="btn btn-sm btn-fichamedica" href="{{route('admin.asociados.crearformularioclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid)}}">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                        <td width="10px">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crear" href="{{route('admin.asociados.crearbateriaclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif
                                    <td width="10px">
                                        @if($reservasmedicaauditoria->informeDisponibleauditoria)
                                            <abbr title="SUBIR INFORME">
                                                <button type="button" class="btn btn-sm btn-subirinforme" 
                                                        data-toggle="modal" 
                                                        data-target="#subirinformeModal"
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
                                    <div class="modal fade" id="subirinformeModal" tabindex="-1" role="dialog" aria-labelledby="subirinformeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h3 class="modal-title" id="subirinformeModalLabel" style="color: #94c93b; font-weight: bold;">SUBIR INFORME</h3>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                {!! Form::open(['id' => 'subirinformeFormauditoria', 'method' => 'POST', 'files' => true]) !!}
                                                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                                {!! Form::hidden('clienteauditoriaid', null, ['id' => 'modal-clienteauditoriaid']) !!}
                                                {!! Form::hidden('clienteauditorianombre', null, ['id' => 'modal-clienteauditorianombre']) !!}
                                                {!! Form::hidden('fechabateria', null, ['id' => 'modal-fechabateria']) !!}
                                                {!! Form::hidden('accion', null, ['id' => 'modal-accion']) !!}

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="archivo">INFORME:</label>
                                                                <input type="file" name="archivo" id="archivo" class="file-input" accept=".pdf,.doc,.docx" />
                                                                <label for="archivo" class="file-custom-label">Elige un PDF</label>
                                                                <div class="file-preview" id="preview-archivo"></div>
                                                                @error('archivo')
                                                                <small class="text-danger fas fa-exclamation-circle">
                                                                    {{$message}}
                                                                </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="picture">IMAGEN 1:</label>
                                                                <input type="file" name="picture" id="picture" class="file-input" accept="image/*" />
                                                                <label for="picture" class="file-custom-label">Elige una imagen</label>
                                                                <div class="file-preview" id="preview-picture"></div>
                                                                @error('picture')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="file-upload">
                                                                <label for="picture2">IMAGEN 2:</label>
                                                                <input type="file" name="picture2" id="picture2" class="file-input" accept="image/*" />
                                                                <label for="picture2" class="file-custom-label">Elige una imagen</label>
                                                                <div class="file-preview" id="preview-picture2"></div>
                                                                @error('picture2')
                                                                    <small class="text-danger fas fa-exclamation-circle">
                                                                        {{$message}}
                                                                    </small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <style>
                                                    .file-upload {
                                                        position: relative;
                                                        display: flex;
                                                        flex-direction: column;
                                                        align-items: center;
                                                        border: 2px solid #faa625; /* Border color */
                                                        border-radius: 8px; /* Rounded corners */
                                                        padding: 50px;
                                                        background: rgb(249, 255, 244); /* Background color */
                                                        transition: background 0.3s ease, border-color 0.3s ease;
                                                    }
                                                
                                                    .file-upload:hover {
                                                        background: #fff7eb; /* Light background on hover */
                                                        border-color: #faa625; /* Darker border on hover */
                                                    }
                                                
                                                    .file-upload label {
                                                        font-weight: bold;
                                                        margin-bottom: 8px;
                                                    }
                                                
                                                    .file-input {
                                                        display: none; /* Hide the default file input */
                                                    }
                                                
                                                    .file-custom-label {
                                                        display: inline-block;
                                                        padding: 10px;
                                                        border: 1px solid #faa625; /* Border for the custom button */
                                                        border-radius: 4px; /* Rounded corners for button */
                                                        background: #fff; /* Background color of button */
                                                        color: #faa625; /* Text color */
                                                        cursor: pointer;
                                                        transition: background 0.3s ease;
                                                    }
                                                
                                                    .file-custom-label:hover {
                                                        background: #faa625; /* Darker background on hover */
                                                        color: #fff; /* Text color */
                                                    }
                                                
                                                    .file-preview {
                                                        margin-top: 10px;
                                                        width: 100%;
                                                        height: auto;
                                                        display: flex;
                                                        justify-content: center;
                                                        align-items: center;
                                                        text-align: center;
                                                    }
                                                
                                                    .file-preview img {
                                                        max-width: 100%;
                                                        max-height: 200px;
                                                        border-radius: 8px;
                                                        border: 2px solid #faa625; /* Border for preview image */
                                                    }
                                                
                                                    .file-preview .file-name {
                                                        font-size: 16px;
                                                        color: #007bff;
                                                    }
                                                
                                                </style>      
                                                <script>
                                                    function handleFileSelect(event) {
                                                        const input = event.target;
                                                        const preview = document.getElementById(`preview-${input.id}`);
                                                
                                                        // Clear any existing previews
                                                        preview.innerHTML = '';
                                                
                                                        if (input.files && input.files[0]) {
                                                            const file = input.files[0];
                                                            const fileURL = URL.createObjectURL(file);
                                                
                                                            if (file.type.startsWith('image/')) {
                                                                // If the file is an image
                                                                const img = document.createElement('img');
                                                                img.src = fileURL;
                                                                preview.appendChild(img);
                                                            } else {
                                                                // For PDF and other file types
                                                                const fileName = document.createElement('span');
                                                                fileName.textContent = file.name;
                                                                fileName.className = 'file-name';
                                                                preview.appendChild(fileName);
                                                            }
                                                        }
                                                    }
                                                
                                                    document.querySelectorAll('.file-input').forEach(input => {
                                                        input.addEventListener('change', handleFileSelect);
                                                    });
                                                </script>
    
                                                <div class="modal-footer">
                                                    <div class="text-center w-100">
                                                        {!! Form::submit('SUBIR INFORME', ['class' => 'btn btn-crear']) !!}
                                                    </div>
                                                </div>
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    </div>
                                    <td width="10px">
                                        <abbr title="SUBIR DOCUMENTACION MULTIPLE">
                                            <a class="btn btn-sm btn-subirinf" href="{{route('admin.asociados.creardocumentacionclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid)}}">
                                                <i class="fas fa-archive"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $('#subirinformeModal').on('show.bs.modal', function (event) {
                                                var button = $(event.relatedTarget); // Botón que activó el modal
                                                var clienteitaid = button.data('clienteauditoriaid');
                                                var clienteitanombre = button.data('clienteauditorianombre');
                                                var fechabateria = button.data('fechabateria');
                                                var accion = button.data('accion');

                                                var modal = $(this);
                                                modal.find('#modal-clienteauditoriaid').val(clienteitaid);
                                                modal.find('#modal-clienteauditorianombre').val(clienteitanombre);
                                                modal.find('#modal-fechabateria').val(fechabateria);
                                                modal.find('#modal-accion').val(accion);
                                                // Actualiza la ruta del formulario para incluir el clienteitaid
                                                var formAction = '{{ route("admin.asociados.guardardocumentacionclienteauditoriadeproveedor", ":clienteauditoria") }}';
                                                formAction = formAction.replace(':cliente', clienteitaid);
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
                                        <div class="dropdown-container">
                                            <button class="btn btn-dropdown" type="button">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->documentacionDisponible) }}" class="btn btn-verdocumentacion" target="_blank" title="VER INFORME MÉDICO">
                                                    <i class="fas fa-folder-open"></i>
                                                </a>
                                                    @if($reservasmedica->imagen1Disponible)
                                                        <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->imagen1Disponible) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 1">
                                                            <i class="fas fa-images"></i>
                                                        </a>
                                                    @endif
                                                    @if($reservasmedica->imagen2Disponible)
                                                        <a href="{{ asset('/documentacionclientesita/' . $reservasmedica->clienteitaid . '/' . $reservasmedica->imagen2Disponible) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 2">
                                                            <i class="far fa-images"></i>
                                                        </a>
                                                    @endif
                                            </div>
                                        </div>   
                                    </td>  
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                        <td width="10px">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crear" href="{{route('admin.asociados.crearbateriaclienteita', $reservasmedica->clienteitaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif                               
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
                                        <div class="dropdown-container">
                                            <button class="btn btn-dropdown" type="button">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->documentacionDisponibleauditoria) }}" class="btn btn-verdocumentacion" target="_blank" title="VER INFORME MÉDICO">
                                                    <i class="fas fa-folder-open"></i>
                                                </a>
                                                    @if($reservasmedicaauditoria->imagen1Disponibleauditoria)
                                                        <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->imagen1Disponibleauditoria) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 1">
                                                            <i class="fas fa-images"></i>
                                                        </a>
                                                    @endif
                                                    @if($reservasmedicaauditoria->imagen2Disponibleauditoria)
                                                        <a href="{{ asset('/documentacionclientesauditoria/' . $reservasmedicaauditoria->clienteauditoriaid . '/' . $reservasmedicaauditoria->imagen2Disponibleauditoria) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 2">
                                                            <i class="far fa-images"></i>
                                                        </a>
                                                    @endif
                                            </div>
                                        </div>   
                                    </td> 
                                    @if ($nombreusuario === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $nombreusuario === 'DENISSE MAUREN LOPEZ FLORES' || $nombreusuario === 'AGUIRRE VASQUEZ MARIA RENEE' || $nombreusuario === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                        <td width="10px">
                                            <abbr title="CREAR BATERIA">
                                                <a class="btn btn-sm btn-crear" href="{{route('admin.asociados.crearbateriaclienteauditoria', $reservasmedicaauditoria->clienteauditoriaid)}}">
                                                    <i class="fas fa-charging-station"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                    @endif                                
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
@endsection
@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Bundle with Popper -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<style>
    /* Estilo para el contenedor del botón y menú desplegable */
.dropdown-container {
    position: relative;
    display: inline-block;
}

/* Estilo para el botón que abre el menú */
.btn-dropdown {
    margin-top: 0px;
    color: #faa625;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 25px;
    display: flex;
}

/* Estilo para el menú desplegable */
.dropdown-menu {
    display: none;
    position: absolute;
    border-radius: 5px;
    z-index: 1;
    top: 100%;
    left: auto;
    right: 100%; /* Ajusta para que el menú se posicione a la izquierda del botón */
    margin-right: 5px; /* Espacio entre el botón y el menú */
    padding: 5px;
    opacity: 0;
    background: #f7ffea;
    display: flex;
    flex-direction: row; /* Alineación horizontal de los botones */
    white-space: nowrap; /* Evita que los botones se envuelvan */
    min-width: 50px; /* Ancho mínimo del menú */
    width: auto; /* Ancho automático según el contenido */
    max-width: 300px; /* Opcional: Ajusta el máximo ancho según sea necesario */
    transition: opacity 0.3s ease; /* Transición suave */
    margin-top: -40px;
}

/* Mostrar el menú desplegable al pasar el cursor sobre el contenedor */
.dropdown-container:hover .dropdown-menu {
    display: flex;
    opacity: 1;
    visibility: visible;
}

/* Estilo para los enlaces dentro del menú desplegable */
.dropdown-menu a {
    padding: 10px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 5px; /* Espaciado entre los botones */
    margin-left: 10px;
}

</style>
<style>
    .btn-mostrartodo { 
        background-color:  #ffffff;
        color: #2f97e7;
        border-color: #2f97e7;
        border-radius: 5px;
        }
    .btn-mostrartodo:hover {
        background-color: #2f97e7;
        color: #ffffff;
        }
    .dropify-wrapper {
        height: 100px !important;
    }
    .dropify-message p {
        font-size: 14px;
    }
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-verdocumentacion:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .nav-tabs {
        display: flex;
        justify-content: space-between;
    }
    .nav-tabs .nav-item {
        flex: 1;
    }
    .nav-tabs .nav-link {
        display: block;
        text-align: center;
        width: 100%;
        font-weight: bold;
        font-size: 17px;
        color: #faa625;
        background-color: #fef4e7;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
        font-size: 17px;
        color: #94c93b;
        background-color: #ffffff;
    }
    .circle {
        display: inline-block;
        width: 50px;
        height: 20px;
        line-height: 20px;
        border-radius: 50%;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-left: 8px;
    }
    .nav-link.active .circle {
        background-color: #94c93b;
        color: #fff;
    }
    .nav-link .circle {
        background-color: #faa625;
        color: #fff;
    }
    .text-incompleto {
        color: red;
        font-size: 16px;
        font-weight: 900;
        }

</style>
<style>
h1, th {
    color:#94c93b; 
    font-family: "Segoe UI";
    font-weight: 900;
}
.btn-fichamedica {
    background-color:  #ffffff;
    color: #0400ff;
    border-color: #0400ff;
    border-radius: 5px;
}
.btn-fichamedica:hover {
    background-color: #0400ff;
    color: #ffffff;
}
.btn-eliminar {
    background-color:  #ffffff;
    color: #ff0000;
    border-color: #ff0000;
    border-radius: 5px;
}
.btn-eliminar:hover {
    background-color: #ff0000;
    color: #ffffff;
}
.btn-crear {
    background-color:  #ffffff;
    color: #94c93b;
    border-color: #94c93b;
    border-radius: 5px;
}
.btn-crear:hover {
    background-color: #94c93b;
    color: #ffffff;
}
.btn-subirinf {
    background-color:  #ffffff;
    color: #faa625;
    border-color: #faa625;
    border-radius: 5px;
}
.btn-subirinf:hover {
    background-color: #faa625;
    color: #ffffff;
}
.btn-subirinforme {
    background-color:  #ffffff;
    color: #2eade8;
    border-color: #2eade8;
    border-radius: 5px;
}
.btn-subirinforme:hover {
    background-color: #2eade8;
    color: #ffffff;
}
.btn-buscar { 
    background-color:  #ffffff;
    color: #faa625;
    border-color: #faa625;
    border-radius: 5px;
}
.btn-buscar:hover {
    background-color: #faa625;
    color: #ffffff;
}

</style>
@stop

@section('js')
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