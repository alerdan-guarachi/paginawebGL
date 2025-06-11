@extends('adminlte::page')

@section('content_header')
<h1>INFORMES FINALES ITA</h1>
@stop
 
@section('css')
<link rel="stylesheet" href="{{ asset('css/informesfinalesgeneral.css') }}">
@stop

@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
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
        $(document).ready(function() {
            $('input[name="buscarporfecha"], input[name="buscarporarea"]').on('keyup change', function() {
                var fechaSeleccionada = $('input[name="buscarporfecha"]').val();
                var areaSeleccionada = $('input[name="buscarporarea"]').val();
                var botonBuscar = $('#btn-buscar');
                
                if (fechaSeleccionada.trim() === '' && areaSeleccionada.trim() === '') {
                    botonBuscar.prop('disabled', true);
                } else {
                    botonBuscar.prop('disabled', false);
                }
            });
        });
    </script>
    <script>
        function cargarVistaPrevia() {
          var document = document.getElementById('document').files[0];
          if (document) {
            var reader = new FileReader();
            reader.onload = function(e) {
              var previewIframe = document.getElementById('document-preview');
              previewIframe.src = e.target.result;
            };
            reader.readAsDataURL(document);
          }
        }
      
        document.getElementById('document').addEventListener('change', function() {
          cargarVistaPrevia();
        });
      </script>
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

        document.getElementById('document').addEventListener('change', function(event) {
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

    </script>
    <script>

        $('.formulario-eliminar').submit(function(e){
            e.preventDefault();

            Swal.fire({
            title: '¿Estás seguro?',
            text: "El rol se eliminará definitivamente",
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
    </script>
@endsection

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
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid justify-content-end">
            <div class="d-flex flex-wrap align-items-center">
                <form id="search-form" action="{{ route('buscarprogramacionesclienteita') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="NOMBRE DEL CLIENTE">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar" type="submit">BUSCAR</button>
                    <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" name="buscartodo" type="submit" value="1">MOSTRAR TODO</button>
                </form>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarprogramacionesclienteita') }}";
            });
        });
    </script>
    
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">

            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $usuarioAutenticado === 'VANESSA MAMANI HUANACO' || $usuarioAutenticado === 'MARLENE ANDREA MONTELLANO ORTIZ')
                @can('admin.informesfinales.asignarproveedorinformesfinales')
                <li class="nav-item">
                    <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                        ASIG. PROVEEDOR
                        <?php if ($asignarCount > 0): ?>
                            <span class="circle"><?= $asignarCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>     
                @endcan
            @endif

            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $usuarioAutenticado === 'VANESSA MAMANI HUANACO' || $usuarioAutenticado === 'MARLENE ANDREA MONTELLANO ORTIZ' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE')
                @can('admin.informesfinales.aprobarbateriainformesfinales') 
                <li class="nav-item">
                    <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                        PAGO PENDIENTE INFO. FINAL
                        <?php if ($aprobarbateriaCount > 0): ?>
                            <span class="circle"><?= $aprobarbateriaCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                @endcan  
            @endif

            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $usuarioAutenticado === 'VANESSA MAMANI HUANACO' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'MARLENE ANDREA MONTELLANO ORTIZ')
                @can('admin.informesfinales.subirinformesfinales') 
                    @can('admin.informesfinales.verinformestodosproveedores') 
                    <li class="nav-item">
                        <a class="nav-link" id="tab-6" data-toggle="tab" href="#tab-content-6" role="tab" aria-controls="tab-content-6" aria-selected="true">
                            SUBIR INFORME
                            <?php if ($subirinformeCount > 0): ?>
                                <span class="circle"><?= $subirinformeCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    @endcan
                    @can('admin.informesfinales.soloverinformessegunproveedor')
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-6" data-toggle="tab" href="#tab-content-6" role="tab" aria-controls="tab-content-6" aria-selected="true">
                            SUBIR INFORME
                            <?php if ($subirinformeCount2 > 0): ?>
                                <span class="circle"><?= $subirinformeCount2 ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    @endcan
                @endcan 
            @endif
           
            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $usuarioAutenticado === 'VANESSA MAMANI HUANACO' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'MARLENE ANDREA MONTELLANO ORTIZ' || $usuarioAutenticado === 'EUDAL AGUIRRE RODRIGUEZ' || $usuarioAutenticado === 'DANNY MARVIN VIAÑA IBAÑEZ' || $usuarioAutenticado === 'JUAN DANIEL ALVARADO SOTO')
                @can('admin.informesfinales.verinformesfinales')
                    @can('admin.informesfinales.verinformestodosproveedores')  
                    <li class="nav-item">
                        <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                            INFORMES SUBIDOS
                            <?php if ($aprobadosCount > 0): ?>
                                <span class="circle"><?= $aprobadosCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    @endcan
                    @can('admin.informesfinales.soloverinformessegunproveedor')  
                    <li class="nav-item">
                        <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                            INFORMES SUBIDOS
                            <?php if ($aprobadosCount2 > 0): ?>
                                <span class="circle"><?= $aprobadosCount2 ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    @endcan
                @endcan 
            @endif
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">

            {{-- ASIGNAR PROVEEDOR --}}
            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $usuarioAutenticado === 'VANESSA MAMANI HUANACO' || $usuarioAutenticado === 'MARLENE ANDREA MONTELLANO ORTIZ')
            @can('admin.informesfinales.asignarproveedorinformesfinales')
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Cli.</th>
                                <th>Cliente</th>
                                <th>Fecha Batería</th>
                                <th>Servicio</th>
                                <th>Result. Médicos</th>
                                <th>Doc.</th>
                                <th>Prov.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if (!$item['proveedornombre'] && $item['estado'] === 'COMPLETO')
                                <tr>
                                    <td>{{ $item['clienteitaid'] }}</td>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>
                                    <td>{{ $item['fechabateria'] }}</td>
                                    <td>{{ $item['tramite'] }}</td>
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>
                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                            <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'.  $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>
                                    <td>
                                        <abbr title="ASIGNAR PROVEEDOR">
                                            <a class="btn btn-sm btn-bateria" href="{{ route('admin.asociados.verclienteita', $item['clienteitaid']) }}">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>
            @endcan
            @endcan

            {{-- PAGO PENDIENTE INFO FINAL --}}
            @can('admin.informesfinales.aprobarbateriainformesfinales')
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Cli.</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Fecha Bateria</th>
                                <th>Servicio</th>
                                <th>Result. Médicos</th>
                                <th>Doc.</th>
                                <th>Aprobar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['proveedornombre'] && $item['estadoaprob'] !== 'APROBADO' && $item['estado'] === 'COMPLETO')
                                @if ($item['estadoinforme'] !== 'PAGO PROCESADO' && $item['proveedornombre'] && $item['estado'] === 'COMPLETO')    
                                <tr>
                                        <td>{{ $item['clienteitaid'] }}</td>
                                        <td>{{ $item['clienteitanombre'] }}</td>
                                        <td>{{ $item['proveedornombre'] }}</td>
                                        <td hidden>
                                            @if ($item['proveedornombre'])
                                                {{ $item['celularproveedor'] }}
                                            @endif
                                        </td>
                                        <td>{{ $item['fechabateria'] }}</td>
                                        <td>{{ $item['tramite'] }}</td>
                                        <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                            {{ $item['estado'] }}
                                            <abbr title="VER DOCUMENTACIÓN">
                                                <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                            </abbr>
                                        </td>
                                        <td>
                                            <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                                <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>
                                            @if ($item['historiamedica'])
                                            <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                    <i class="fas fa-book-medical"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $aprobado = $aprobaciones->where('clienteitaid', $item['clienteitaid'])
                                                        ->where('clienteitanombre', $item['clienteitanombre'])
                                                        ->where('fechabateria', $item['fechabateria'])
                                                        ->first();
                                            @endphp
                                            @if ($item['proveedornombre'])
                                                @if ($aprobado)
                                                    <div class="text-completo">APROBADO</div>
                                                @else
                                                @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                                    <abbr title="APROBAR BATERÍA">
                                                        <a class="btn btn-aprobar" data-toggle="modal" data-target="#modalAprobar{{ $loop->index }}"
                                                            data-cliente="{{ $item['clienteitanombre'] }}" 
                                                            data-fecha="{{ $item['fechabateria'] }}">
                                                            <i class="fas fa-calendar-check" style="align-items: center"></i>
                                                        </a>
                                                    </abbr>
                                                    @else
                                                    <div class="text-incompleto">NO APROBADO</div>
                                                @endif
                                                @endif
                                            @else
                                                <button class="btn btn-disabled btn-disabled" disabled>
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endif
                            @endforeach


                            @foreach ($result2 as $item)
                            @if ($item['proveedornombre'] && $item['estadoaprob'] !== 'APROBADO' && $item['sinProgramacion'])
                                @if ($item['estadoinforme'] !== 'PAGO PROCESADO' && $item['proveedornombre'] && $item['sinProgramacion'])    
                                <tr>
                                        <td>{{ $item['clienteitaid'] }}</td>
                                        <td>{{ $item['clienteitanombre'] }}</td>
                                        <td>{{ $item['proveedornombre'] }}</td>
                                        <td hidden>
                                            @if ($item['proveedornombre'])
                                                {{ $item['celularproveedor'] }}
                                            @endif
                                        </td>
                                        <td>{{ $item['fechabateria'] }}</td>
                                        <td>{{ $item['tramite'] }}</td>
                                        <td>VACIO</td>
                                        <td>
                                            @if ($item['cicliente'])
                                            <abbr title="VER CARNET" style="display: inline-block;">
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>
                                            @endif

                                            @if ($item['cicliente2'])
                                            <abbr title="VER CARNET" style="display: inline-block;">
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente2']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                            @if ($item['historiamedica'])
                                            <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                    <i class="fas fa-book-medical"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $aprobado = $aprobaciones->where('clienteitaid', $item['clienteitaid'])
                                                        ->where('clienteitanombre', $item['clienteitanombre'])
                                                        ->where('fechabateria', $item['fechabateria'])
                                                        ->first();
                                            @endphp
                                            @if ($item['proveedornombre'])
                                                @if ($aprobado)
                                                    <div class="text-completo">APROBADO</div>
                                                @else
                                                @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR')
                                                    <abbr title="APROBAR BATERÍA">
                                                        <a class="btn btn-aprobar" data-toggle="modal" data-target="#modalAprobar{{ $loop->index }}"
                                                            data-cliente="{{ $item['clienteitanombre'] }}" 
                                                            data-fecha="{{ $item['fechabateria'] }}">
                                                            <i class="fas fa-calendar-check" style="align-items: center"></i>
                                                        </a>
                                                    </abbr>
                                                    @else
                                                    <div class="text-incompleto">NO APROBADO</div>
                                                @endif
                                                @endif
                                            @else
                                                <button class="btn btn-disabled btn-disabled" disabled>
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endcan

            {{-- SUBIR INFORME --}}
            @can('admin.informesfinales.subirinformesfinales')
            <div class="tab-pane fade" id="tab-content-6" role="tabpanel" aria-labelledby="tab-6">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Cliente</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Fecha Bateria</th>
                                <th>Servicio</th>
                                <th>Result. Médicos</th>
                                <th>Doc.</th>
                                <th>Subir</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @can('admin.informesfinales.soloverinformessegunproveedor')
                                @foreach ($result as $item)
                                @if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO' && $item['proveedornombre'] === $usuarioAutenticado || $item['estadoinforme'] === 'PAGO PROCESADO' && $item['proveedornombre'] && !$item['estado_informefinal']  && $item['estado'] === 'COMPLETO' && $item['proveedornombre'] === $usuarioAutenticado )

                                    <tr>
                                        <td>{{ $item['clienteitaid'] }}</td>
                                        <td>{{ $item['clienteitanombre'] }}</td>
                                        <td>{{ $item['proveedornombre'] }}</td>
                                        <td hidden>
                                            @if ($item['proveedornombre'])
                                                {{ $item['celularproveedor'] }}
                                            @endif
                                        </td>
                                        <td>{{ $item['fechabateria'] }}</td>
                                        <td>{{ $item['tramite'] }}</td>
                                        <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                            {{ $item['estado'] }}
                                            <abbr title="VER DOCUMENTACIÓN">
                                                <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                            </abbr>
                                        </td>
                                        <td>
                                            <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                                <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>

                                            @if ($item['historiamedica'])
                                            <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                    <i class="fas fa-book-medical"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-subirinformeinicio btn-upload text-black" 
                                                data-toggle="modal" 
                                                data-target="#modalUpload{{ $loop->index }}"
                                                title="SUBIR INFORME FINAL">
                                                <i class="fas fa-upload"></i>
                                            </a>                                      
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach

                                @foreach ($result2 as $item)
                                    @if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['proveedornombre'] === $usuarioAutenticado && $item['sinProgramacion'] || $item['estadoinforme'] === 'PAGO PROCESADO' && $item['proveedornombre'] && !$item['estado_informefinal'] && $item['proveedornombre'] === $usuarioAutenticado && $item['sinProgramacion'])
                                        <tr>
                                            <td>{{ $item['clienteitaid'] }}</td>
                                            <td>{{ $item['clienteitanombre'] }}</td>
                                            <td>{{ $item['proveedornombre'] }}</td>
                                            <td hidden>
                                                @if ($item['proveedornombre'])
                                                    {{ $item['celularproveedor'] }}
                                                @endif
                                            </td>
                                            <td>{{ $item['fechabateria'] }}</td>
                                            <td>{{ $item['tramite'] }}</td>
                                            <td>VACIO</td>
                                                <td>
                                                    @if ($item['cicliente'])
                                                    <abbr title="VER CARNET" style="display: inline-block;">
                                                        <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                            <i class="fas fa-address-book"></i>
                                                        </a>
                                                    </abbr>
                                                    @endif

                                                    @if ($item['cicliente2'])
                                                    <abbr title="VER CARNET" style="display: inline-block;">
                                                        <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente2']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                            <i class="fas fa-address-book"></i>
                                                        </a>
                                                    </abbr>
                                                    @endif
                                                    @if ($item['historiamedica'])
                                                    <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                        <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                            <i class="fas fa-book-medical"></i>
                                                        </a>
                                                    </abbr>
                                                    @endif
                                                </td>
                                            <td>
                                                <a class="btn btn-subirinformeinicio btn-upload2 text-black" 
                                                    data-toggle="modal" 
                                                    data-target="#modalUpload2{{ $loop->index }}"
                                                    title="SUBIR INFORME FINAL">
                                                    <i class="fas fa-upload"></i>
                                                </a>                                      
                                            </td>
                                        </tr>
                                    @endif
                                    <div class="modal fade" id="modalUpload2{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelUpload2{{ $loop->index }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body text-center">
                                                    <h4 class="mb-4">SUBIR INFORME FINAL</h4>
                                                    {!! Form::model($item, ['route' => ['admin.informesfinales.guardarinformefinal', $item['clienteitaid']], 'method' => 'POST', 'id' => 'formAprobar{{ $loop->index }}', 'enctype' => 'multipart/form-data']) !!}
                                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                    
                                                        <input type="hidden" name="clienteitaid" value="{{ $item['clienteitaid'] }}">
                                                        <div class="form-group">
                                                            <input type="hidden" name="cliente" value="{{ $item['clienteitanombre'] }}">
                                                            <input type="hidden" name="fechabateria" value="{{ $item['fechabateria'] }}">
                                                            <input type="hidden" name="tramite" value="{{ $item['tramite'] }}">
                                                            <input type="hidden" name="estado" value="EN REVISIÓN">
                                                            {!! Form::label('file', 'INFORME PDF:') !!}
                                                            <input type="file" name="document" id="document" accept=".pdf"/>
                                                            @error('document')
                                                            <small class="text-danger fas fa-exclamation-circle">
                                                                {{$message}}
                                                            </small>
                                                            @enderror
                                                            <br>
                                                            {!! Form::label('file', 'INFORME WORD:') !!}
                                                            <input type="file" name="documentword" id="documentword" accept=".docx"/>
                                                            @error('documentword')
                                                            <small class="text-danger fas fa-exclamation-circle">
                                                                {{$message}}
                                                            </small>
                                                            @enderror
                                                        </div>
                                                        <div class="modal-footer justify-content-center">
                                                            <button type="button" class="btn btn-no" data-dismiss="modal">CERRAR</button>
                                                            <button type="submit" class="btn btn-si">SUBIR</button>
                                                        </div>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endcan

                            @can('admin.informesfinales.verinformestodosproveedores')
                                @foreach ($result as $item)
                                @if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO' || $item['estadoinforme'] === 'PAGO PROCESADO' && $item['proveedornombre'] && !$item['estado_informefinal'] && $item['estado'] === 'COMPLETO')
                                    <tr>
                                        <td>{{ $item['clienteitaid'] }}</td>
                                        <td>{{ $item['clienteitanombre'] }}</td>
                                        <td>{{ $item['proveedornombre'] }}</td>
                                        <td hidden>
                                            @if ($item['proveedornombre'])
                                                {{ $item['celularproveedor'] }}
                                            @endif
                                        </td>
                                        <td>{{ $item['fechabateria'] }}</td>
                                        <td>{{ $item['tramite'] }}</td>
                                        <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                            @if (isset($item['observacion']) && !empty($item['observacion']) && $item['estado'] === 'COMPLETO')
                                            {{ $item['estado'] }}
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                                </abbr>
                                            @else
                                                {{ $item['estado'] }}
                                            
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                                </abbr>
                                            @endif
                                        </td>
                                        <td>
                                            <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                                <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>

                                            @if ($item['historiamedica'])
                                            <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                    <i class="fas fa-book-medical"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-subirinformeinicio btn-upload text-black" 
                                                data-toggle="modal" 
                                                data-target="#modalUpload{{ $loop->index }}"
                                                title="SUBIR INFORME FINAL">
                                                <i class="fas fa-upload"></i>
                                            </a>                                      
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach

                                @foreach ($result2 as $item)
                                @if ($item['estadoaprob'] === 'APROBADO' && !$item['estado_informefinal'] && $item['sinProgramacion'] || $item['estadoinforme'] === 'PAGO PROCESADO' && $item['proveedornombre'] && !$item['estado_informefinal'] && $item['sinProgramacion'])
                                <tr>
                                    <td>{{ $item['clienteitaid'] }}</td>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td>{{ $item['proveedornombre'] }}</td>
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>
                                    <td>{{ $item['fechabateria'] }}</td>
                                    <td>{{ $item['tramite'] }}</td>
                                    <td>VACIO</td>
                                            <td>
                                                @if ($item['cicliente'])
                                                <abbr title="VER CARNET" style="display: inline-block;">
                                                    <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                                @endif

                                                @if ($item['cicliente2'])
                                                <abbr title="VER CARNET" style="display: inline-block;">
                                                    <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente2']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                                @endif
                                                @if ($item['historiamedica'])
                                                <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                    <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                        <i class="fas fa-book-medical"></i>
                                                    </a>
                                                </abbr>
                                                @endif
                                            </td>
                                        <td>
                                            <a class="btn btn-subirinformeinicio btn-upload2 text-black" 
                                                data-toggle="modal" 
                                                data-target="#modalUpload2{{ $loop->index }}"
                                                title="SUBIR INFORME FINAL">
                                                <i class="fas fa-upload"></i>
                                            </a>                                      
                                        </td>
                                    </tr>
                                    @endif
                                    <div class="modal fade" id="modalUpload2{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelUpload2{{ $loop->index }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body text-center">
                                                    <h4 class="mb-4">SUBIR INFORME FINAL</h4>
                                                    {!! Form::model($item, ['route' => ['admin.informesfinales.guardarinformefinal', $item['clienteitaid']], 'method' => 'POST', 'id' => 'formAprobar{{ $loop->index }}', 'enctype' => 'multipart/form-data']) !!}
                                                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                    
                                                        <input type="hidden" name="clienteitaid" value="{{ $item['clienteitaid'] }}">
                                                        <div class="form-group">
                                                            <input type="hidden" name="cliente" value="{{ $item['clienteitanombre'] }}">
                                                            <input type="hidden" name="fechabateria" value="{{ $item['fechabateria'] }}">
                                                            <input type="hidden" name="tramite" value="{{ $item['tramite'] }}">
                                                            <input type="hidden" name="estado" value="EN REVISIÓN">
                                                            {!! Form::label('file', 'INFORME PDF:') !!}
                                                            <input type="file" name="document" id="document" accept=".pdf"/>
                                                            @error('document')
                                                            <small class="text-danger fas fa-exclamation-circle">
                                                                {{$message}}
                                                            </small>
                                                            @enderror
                                                            <br>
                                                            {!! Form::label('file', 'INFORME WORD:') !!}
                                                            <input type="file" name="documentword" id="documentword" accept=".docx"/>
                                                            @error('documentword')
                                                            <small class="text-danger fas fa-exclamation-circle">
                                                                {{$message}}
                                                            </small>
                                                            @enderror
                                                        </div>
                                                        <div class="modal-footer justify-content-center">
                                                            <button type="button" class="btn btn-no" data-dismiss="modal">CERRAR</button>
                                                            <button type="submit" class="btn btn-si">SUBIR</button>
                                                        </div>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endcan

                        </tbody>
                    </table>
                </div>
            </div>
            @endcan

            {{-- APROBADOS --}}
            @can('admin.informesfinales.verinformesfinales')
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID_Cli.</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Fecha_Bateria</th>
                                <th>Servicio</th>
                                <th>Result._Médicos</th>
                                <th>Doc.</th>
                                <th>Informe_Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @can('admin.informesfinales.soloverinformessegunproveedor')
                                @foreach ($result as $item)
                                    @if ($item['estado_informefinal'] === 'APROBADO' && $item['proveedornombre'] === $usuarioAutenticado)
                                    <tr>
                                        <td>{{ $item['clienteitaid'] }}</td>
                                        <td>{{ $item['clienteitanombre'] }}</td>
                                        <td>{{ $item['proveedornombre'] }}</td>
                                        <td hidden>
                                            @if ($item['proveedornombre'])
                                                {{ $item['celularproveedor'] }}
                                            @endif
                                        </td>
                                        <td>{{ $item['fechabateria'] }}</td>
                                        <td>{{ $item['tramite'] }}</td>
                                        <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                            {{ $item['estado'] }}
                                            <abbr title="VER DOCUMENTACIÓN">
                                                <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                            </abbr>
                                        </td>
                                        <td>
                                            <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                                <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>
                                            @if ($item['historiamedica'])
                                            <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                    <i class="fas fa-book-medical"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($item['documentos'] as $documento)
                                                @if ($documento['document'])
                                                    <a href="{{ asset('/informesfinalesclientesita/'. $item['clienteitaid'] . '/' . $documento['document']) }}" class="btn btn-sm btn-veracciones" target="_blank" title="VER INFORME FINAL">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if ($documento['documentfirmado'])
                                                    <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentfirmado']) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @endif
                                                @if ($documento['documentword'])
                                                    <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentword']) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME FINAL WORD">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @endif
                                            @endforeach

                                            @php
                                                $tramites = is_array($item['tramite']) ? $item['tramite'] : explode(',', $item['tramite']);
                                                $documentCount = 0;
                                                foreach ($item['documentos'] as $documento) {
                                                    if ($documento['document']) $documentCount++;
                                                    if ($documento['documentfirmado']) $documentCount++;
                                                    if ($documento['documentword']) $documentCount++;
                                                }
                                            @endphp

                                            @if (count($tramites) > 1 && $documentCount <= 3)
                                                <a class="btn btn-subirinformeinicio btn-upload text-black" 
                                                    data-toggle="modal" 
                                                    data-target="#modalUpload{{ $loop->index }}"
                                                    title="SUBIR INFORME FINAL">
                                                    <i class="fas fa-upload"></i>
                                                </a>
                                            @endif

                                        </td>
                                    </tr>
                                    @endif
                                @endforeach

                                @foreach ($result2 as $item)
                                    @if ($item['estado_informefinal'] === 'APROBADO' && $item['sinProgramacion'] && $item['proveedornombre'] === $usuarioAutenticado)   
                                    <tr>
                                        <td>{{ $item['clienteitaid'] }}</td>
                                        <td>{{ $item['clienteitanombre'] }}</td>
                                        <td>{{ $item['proveedornombre'] }}</td>
                                        <td hidden>
                                            @if ($item['proveedornombre'])
                                                {{ $item['celularproveedor'] }}
                                            @endif
                                        </td>
                                        <td>{{ $item['fechabateria'] }}</td>
                                        <td>{{ $item['tramite'] }}</td>
                                        <td>{{ $item['estado'] }}</td>
                                        <td>
                                            @if ($item['cicliente'])
                                            <abbr title="VER CARNET" style="display: inline-block;">
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>
                                            @endif

                                            @if ($item['cicliente2'])
                                            <abbr title="VER CARNET" style="display: inline-block;">
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente2']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                            @if ($item['historiamedica'])
                                            <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                    <i class="fas fa-book-medical"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($item['documentos'] as $documento)
                                                @if ($documento['document'])
                                                    <a href="{{ asset('/informesfinalesclientesita/'. $item['clienteitaid'] . '/' . $documento['document']) }}" class="btn btn-sm btn-veracciones" target="_blank" title="VER INFORME FINAL">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if ($documento['documentfirmado'])
                                                    <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentfirmado']) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @endif
                                                @if ($documento['documentword'])
                                                    <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentword']) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME FINAL WORD">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @endif
                                            @endforeach

                                            @php
                                                $tramites = is_array($item['tramite']) ? $item['tramite'] : explode(',', $item['tramite']);
                                                $documentCount = 0;
                                                foreach ($item['documentos'] as $documento) {
                                                    if ($documento['document']) $documentCount++;
                                                    if ($documento['documentfirmado']) $documentCount++;
                                                    if ($documento['documentword']) $documentCount++;
                                                }
                                            @endphp

                                            @if (count($tramites) > 1 && $documentCount <= 3)
                                                <a class="btn btn-subirinformeinicio btn-upload text-black" 
                                                    data-toggle="modal" 
                                                    data-target="#modalUpload{{ $loop->index }}"
                                                    title="SUBIR INFORME FINAL">
                                                    <i class="fas fa-upload"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endcan

                            @can('admin.informesfinales.verinformestodosproveedores')
                                @foreach ($result as $item)
                                    @if ($item['estado_informefinal'] === 'APROBADO')
                                        <tr>
                                            <td>{{ $item['clienteitaid'] }}</td>
                                            <td>{{ $item['clienteitanombre'] }}</td>
                                            <td>{{ $item['proveedornombre'] }}</td>
                                            <td hidden>
                                                @if ($item['proveedornombre'])
                                                    {{ $item['celularproveedor'] }}
                                                @endif
                                            </td>
                                            <td>{{ $item['fechabateria'] }}</td>
                                            <td>{{ $item['tramite'] }}</td>
                                            <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                                {{ $item['estado'] }}
                                                <abbr title="VER DOCUMENTACIÓN">
                                                    <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                                </abbr>
                                            </td>
                                            <td>
                                                <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                                    <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                        <i class="fas fa-address-book"></i>
                                                    </a>
                                                </abbr>
                                                @if ($item['historiamedica'])
                                                <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                    <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                        <i class="fas fa-book-medical"></i>
                                                    </a>
                                                </abbr>
                                                @endif
                                            </td>
                                            <td>
                                                {{-- @foreach ($item['documentos'] as $documento)
                                                    @if ($documento['document'])
                                                        <a href="{{ asset('/informesfinalesclientesita/'. $item['clienteitaid'] . '/' . $documento['document']) }}" class="btn btn-sm btn-veracciones" target="_blank" title="VER INFORME FINAL">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if ($documento['documentfirmado'])
                                                        <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentfirmado']) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                            <i class="fas fa-file"></i>
                                                        </a>
                                                    @endif
                                                    @if ($documento['documentword'])
                                                        <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentword']) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME FINAL WORD">
                                                            <i class="fas fa-file"></i>
                                                        </a>
                                                    @endif
                                                @endforeach --}}
                                                @php
                                                    $tramites = is_array($item['tramite']) ? $item['tramite'] : explode(',', $item['tramite']);
                                                    $tramites = array_map('trim', $tramites);

                                                    // Convertimos la colección a array
                                                    $documentosArray = $item['documentos']->all();

                                                    // Definimos cuántos documentos corresponden a cada trámite (ajusta si son más de 1 por trámite)
                                                    $documentGroups = array_chunk($documentosArray, 1);
                                                @endphp

                                                @foreach ($tramites as $index => $tramiteTitulo)
                                                    <div class="mb-2">
                                                        {{-- Mostrar el título solo si hay más de un trámite --}}
                                                        @if (count($tramites) > 1)
                                                            <strong>{{ strtoupper($tramiteTitulo) }}: </strong>{{-- <br> --}}
                                                        @endif

                                                        @isset($documentGroups[$index])
                                                            @foreach ($documentGroups[$index] as $documento)
                                                                @if ($documento['document'])
                                                                    <a href="{{ asset('/informesfinalesclientesita/'. $item['clienteitaid'] . '/' . $documento['document']) }}" class="btn btn-sm btn-veracciones" target="_blank" title="VER INFORME FINAL">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endif
                                                                @if ($documento['documentfirmado'])
                                                                    <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentfirmado']) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                                        <i class="fas fa-file"></i>
                                                                    </a>
                                                                @endif
                                                                @if ($documento['documentword'])
                                                                    <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentword']) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME FINAL WORD">
                                                                        <i class="fas fa-file"></i>
                                                                    </a>
                                                                @endif
                                                            @endforeach
                                                        @endisset
                                                    </div>
                                                @endforeach

                                                @php
                                                    $tramites = is_array($item['tramite']) ? $item['tramite'] : explode(',', $item['tramite']);
                                                    $documentCount = 0;
                                                    foreach ($item['documentos'] as $documento) {
                                                        if ($documento['document']) $documentCount++;
                                                        if ($documento['documentfirmado']) $documentCount++;
                                                        if ($documento['documentword']) $documentCount++;
                                                    }
                                                @endphp

                                                @if (count($tramites) > 1 && $documentCount <= 3)
                                                    <a class="btn btn-subirinformeinicio btn-upload text-black" 
                                                        data-toggle="modal" 
                                                        data-target="#modalUpload{{ $loop->index }}"
                                                        title="SUBIR INFORME FINAL">
                                                        <i class="fas fa-upload"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                @foreach ($result2 as $item)
                                    @if ($item['estado_informefinal'] === 'APROBADO' && $item['sinProgramacion'])   
                                    <tr>
                                        <td>{{ $item['clienteitaid'] }}</td>
                                        <td>{{ $item['clienteitanombre'] }}</td>
                                        <td>{{ $item['proveedornombre'] }}</td>
                                        <td hidden>
                                            @if ($item['proveedornombre'])
                                                {{ $item['celularproveedor'] }}
                                            @endif
                                        </td>
                                        <td>{{ $item['fechabateria'] }}</td>
                                        <td>{{ $item['tramite'] }}</td>
                                        <td>{{ $item['estado'] }}</td>
                                        <td>
                                            @if ($item['cicliente'])
                                            <abbr title="VER CARNET" style="display: inline-block;">
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>
                                            @endif

                                            @if ($item['cicliente2'])
                                            <abbr title="VER CARNET" style="display: inline-block;">
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid']. '/' . $item['cicliente2']) }}" class="btn btn-requisitosdocumentos" target="_blank">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                            @if ($item['historiamedica'])
                                            <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                <a href="{{ asset('/historiamedica/' . $item['clienteitaid'] .'/extracted/'. $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                    <i class="fas fa-book-medical"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($item['documentos'] as $documento)
                                                @if ($documento['document'])
                                                    <a href="{{ asset('/informesfinalesclientesita/'. $item['clienteitaid'] . '/' . $documento['document']) }}" class="btn btn-sm btn-veracciones" target="_blank" title="VER INFORME FINAL">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if ($documento['documentfirmado'])
                                                    <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentfirmado']) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @endif
                                                @if ($documento['documentword'])
                                                    <a href="{{ asset('/informesfinalesclientesita/' . $item['clienteitaid'] . '/' . $documento['documentword']) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME FINAL WORD">
                                                        <i class="fas fa-file"></i>
                                                    </a>
                                                @endif
                                            @endforeach

                                            @php
                                                $tramites = is_array($item['tramite']) ? $item['tramite'] : explode(',', $item['tramite']);
                                                $documentCount = 0;
                                                foreach ($item['documentos'] as $documento) {
                                                    if ($documento['document']) $documentCount++;
                                                    if ($documento['documentfirmado']) $documentCount++;
                                                    if ($documento['documentword']) $documentCount++;
                                                }
                                            @endphp

                                            @if (count($tramites) > 1 && $documentCount <= 3)
                                                <a class="btn btn-subirinformeinicio btn-upload text-black" 
                                                    data-toggle="modal" 
                                                    data-target="#modalUpload{{ $loop->index }}"
                                                    title="SUBIR INFORME FINAL">
                                                    <i class="fas fa-upload"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endcan

                        </tbody>
                    </table>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.btn-aprobar').on('click', function() {
            var cliente = $(this).data('cliente');
            var fecha = $(this).data('fecha');
            var modal2 = $(this).data('target');
            $(modal2).find('#cliente').val(cliente);
            $(modal2).find('#fechabateria').val(fecha);
        });
    });
</script>

@foreach ($result as $item)
    {{-- VER RESULTADOS MEDICOS --}}
    <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}"><strong>{{ $item['clienteitanombre'] }}</strong> - Fecha Bateria: {{ $item['fechabateria'] }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr> 
                                <th>Bateria</th>
                                <th>Estudio/Especialidad</th>
                                <th>Proveedor</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item['acciones'] as $accion)
                                <tr>
                                    <td>{{ $accion['created_at'] }}</td>
                                    <td>{{ $accion['accion'] }}</td>
                                    <td>{{ $accion['proveedornombre'] }}</td>
                                    <td>
                                        @if (isset($accion['observacion']) && !empty($accion['observacion']))
                                            <div class="observacion">
                                                <button class="btn btn-solicitocorreccion btn-sm" data-toggle="modal" data-target="#modalObservacion{{ $loop->parent->index }}-{{ $loop->index }}">
                                                    ACTUALIZAR
                                                </button>
                                                <div class="modal fade" id="modalObservacion{{ $loop->parent->index }}-{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalObservacionLabel{{ $loop->parent->index }}-{{ $loop->index }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="modalObservacionLabel{{ $loop->parent->index }}-{{ $loop->index }}">SOLICITÓ CORRECCIÓN</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <strong>Observación:</strong>
                                                                <p>{{ $accion['observacion'] }}</p>
                                                                
                                                                <strong>Nuevo documento:</strong>
                                                                <form action="{{ route('updateDocument', ['id' => $accion['document']->id]) }}" method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <input type="file" name="archivo" class="form-control-file dropify" required>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-guardarobservacion">Actualizar Documento</button>
                                                                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($accion['estado'] === 'COMPLETO' && isset($accion['document']))
                                            <abbr title="VER RESULTADO MÉDICO" style="display: inline-block;">
                                                <a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['document']->document) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                            </abbr>
                                            {{-- <abbr title="SOLICITAR CORRECCIÓN" style="display: inline-block;">
                                                <button class="btn btn-verdetalles" data-toggle="modal" data-target="#modalDetalles{{ $loop->parent->index }}-{{ $loop->index }}"><i class="fas fa-exclamation-triangle"></i></button>
                                            </abbr> --}}
                                        @else
                                            <div class="pendiente">PENDIENTE</div>
                                        @endif
                                    </td>
                                </tr>

                                <!-- OBSERVACION -->
                                <div class="modal fade" id="modalDetalles{{ $loop->parent->index }}-{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel{{ $loop->parent->index }}-{{ $loop->index }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="modalDetallesLabel{{ $loop->parent->index }}-{{ $loop->index }}">
                                                    SOLICITAR CORRECCIÓN
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Estudio / Especialidad:</strong> {{ $accion['accion'] }}</p>
                                                <p><strong>Proveedor programado:</strong> {{ $accion['proveedornombre'] }}</p>
                                                <form action="{{ route('updateObservacion') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="fechabateria" value="{{ $item['fechabateria'] }}">
                                                    <input type="hidden" name="accion" value="{{ $accion['accion'] }}">
                                                    <div class="form-group">
                                                        <label for="observacion">Observación:</label>
                                                        <textarea class="form-control" id="observacion" name="observacion" rows="5" placeholder="Escribe tu observación aquí...">{{ $accion['observacion'] ?? '' }}</textarea>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-guardarobservacion">Guardar</button>
                                                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- DOCUMENTACIÓN -->
    <div class="modal fade" id="modalDocumentacion{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelDocumentacion{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabelDocumentacion{{ $loop->index }}">
                        <strong>DOCUMENTACIÓN</strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Servicio</th>
                                    <th>Ver Doc.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $mostrarpoder = !empty($accion['poder']);
                                    $mostrarpoderpendiente = $accion['poder'] === 'PENDIENTE';
                                    $mostraravcci = !empty($accion['avcci']);
                                    $mostraravccipendiente = $accion['avcci'] === 'PENDIENTE';
                                    $mostrarcnacasegurado = !empty($accion['cnacasegurado']);
                                    $mostrarcnacaseguradopendiente = $accion['cnacasegurado'] === 'PENDIENTE';
                                    $mostrarciasegurado = !empty($accion['ciasegurado']);
                                    $mostrarciaseguradopendiente = $accion['ciasegurado'] === 'PENDIENTE';
                                    $mostrarcimatrimonio = !empty($accion['cmatrimonio']);
                                    $mostrarcimatrimoniopendiente = $accion['cmatrimonio'] === 'PENDIENTE';
                                    $mostrarcnacconyuge = !empty($accion['cnacconyuge']);
                                    $mostrarcnacconyugependiente = $accion['cnacconyuge'] === 'PENDIENTE';
                                    $mostrarciconyuge = !empty($accion['ciconyuge']);
                                    $mostrarciconyugependiente = $accion['ciconyuge'] === 'PENDIENTE';
                                    $mostrarcnacjihos = !empty($accion['cnacjihos']);
                                    $mostrarcnacjihospendiente = $accion['cnacjihos'] === 'PENDIENTE';
                                    $mostrarcihijos = !empty($accion['cihijos']);
                                    $mostrarcihijospendiente = $accion['cihijos'] === 'PENDIENTE';
                                    $mostrardenfaccidente = !empty($accion['denfaccidente']);
                                    $mostrardenfaccidentependiente = $accion['denfaccidente'] === 'PENDIENTE';
                                    $mostrarcrodomicilio = !empty($accion['crodomicilio']);
                                    $mostrarcrodomiciliopendiente = $accion['crodomicilio'] === 'PENDIENTE';
                                    $mostrarcontrato = !empty($accion['contrato']);
                                    $mostrarcontratopendiente = $accion['contrato'] === 'PENDIENTE';

                                    $mostraregestora = !empty($accion['egestora']);
                                    $mostraregestorapendiente = $accion['egestora'] === 'PENDIENTE';
                                    $mostrardictamencalentenc = !empty($accion['dictamencalentenc']);
                                    $mostrardictamencalentencpendiente = $accion['dictamencalentenc'] === 'PENDIENTE';
                                    $mostrarinfomedicasalud = !empty($accion['infomedicasalud']);
                                    $mostrarinfomedicasaludpendiente = $accion['infomedicasalud'] === 'PENDIENTE';
                                    $mostrarctrabajo = !empty($accion['ctrabajo']);
                                    $mostrarctrabajopendiente = $accion['ctrabajo'] === 'PENDIENTE';
                                    $mostrarboletapago = !empty($accion['boletapago']);
                                    $mostrarboletapagopendiente = $accion['boletapago'] === 'PENDIENTE';
                                    $mostraractdatos = !empty($accion['actdatos']);
                                    $mostraractdatospendiente = $accion['actdatos'] === 'PENDIENTE';
                                    $mostrarresolinvhijos = !empty($accion['resolinvhijos']);
                                    $mostrarresolinvhijospendiente = $accion['resolinvhijos'] === 'PENDIENTE';
                                    $mostrarcunionlibre = !empty($accion['cunionlibre']);
                                    $mostrarcunionlibrependiente = $accion['cunionlibre'] === 'PENDIENTE';
                                    $mostrarcnacimientounionlibre = !empty($accion['cnacimientounionlibre']);
                                    $mostrarcnacimientounionlibrependiente = $accion['cnacimientounionlibre'] === 'PENDIENTE';
                                    $mostrarciunionlibre = !empty($accion['ciunionlibre']);
                                    $mostrarciunionlibrependiente = $accion['ciunionlibre'] === 'PENDIENTE';
                                    $mostrarcdivorcio = !empty($accion['cdivorcio']);
                                    $mostrarcdivorciopendiente = $accion['cdivorcio'] === 'PENDIENTE';
                                    $mostrarcdefuncion = !empty($accion['cdefuncion']);
                                    $mostrarcdefuncionpendiente = $accion['cdefuncion'] === 'PENDIENTE';
                                    $mostrarpolizasgen = !empty($accion['polizasgen']);
                                    $mostrarpolizasgenpendiente = $accion['polizasgen'] === 'PENDIENTE';
                                    $mostrardeclasalud = !empty($accion['declasalud']);
                                    $mostrardeclasaludpendiente = $accion['declasalud'] === 'PENDIENTE';
                                    $mostrarpolizaseguro = !empty($accion['polizaseguro']);
                                    $mostrarpolizaseguropendiente = $accion['polizaseguro'] === 'PENDIENTE';

                                    $mostraranteriordictamen = !empty($accion['anteriordictamen']);
                                    $mostraranteriordictamenpendiente = $accion['anteriordictamen'] === 'PENDIENTE';
                                    $mostrarpoderciapoderado = !empty($accion['poderciapoderado']);
                                    $mostrarpoderciapoderadopendiente = $accion['poderciapoderado'] === 'PENDIENTE';
                                @endphp


                                @if ($mostrarpoder || $mostrarpoderpendiente)
                                    <tr>
                                        <td>PODER: {{ $accion['numeropoder'] }}</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarpoderpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarpoder)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['poder']) }}" class="btn btn-verdocumentacion" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostraravcci || $mostraravccipendiente)
                                    <tr>
                                        <td>AVC / CARNET DE ASEGURADO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostraravccipendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostraravcci)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['avcci']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcnacasegurado || $mostrarcnacaseguradopendiente)
                                    <tr>
                                        <td>CERTIFICADO NACIMIENTO DE ASEGURADO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcnacaseguradopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcnacasegurado)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacasegurado']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarciasegurado || $mostrarciaseguradopendiente)
                                    <tr>
                                        <td>CARNET IDENTIDAD DE ASEGURADO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarciaseguradopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarciasegurado)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciasegurado']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcimatrimonio || $mostrarcimatrimoniopendiente)
                                    <tr>
                                        <td>CERTIFICADO DE MATRIMONIO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcimatrimoniopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcimatrimonio)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cmatrimonio']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>   
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcnacconyuge || $mostrarcnacconyugependiente)
                                    <tr>
                                        <td>CERTIFICADO NACIMIENTO DE CONYUGE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcnacconyugependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcnacconyuge)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacconyuge']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarciconyuge || $mostrarciconyugependiente)
                                    <tr>
                                        <td>CARNET IDENTIDAD DE CONYUGE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarciconyugependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarciconyuge)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciconyuge']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcnacjihos || $mostrarcnacjihospendiente)
                                    <tr>
                                        <td>CERTIFICADO NACIMIENTO DE HIJOS < 25</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcnacjihospendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcnacjihos)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacjihos']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcihijos || $mostrarcihijospendiente)
                                    <tr>
                                        <td>CARNET IDENTIDAD DE HIJOS < 25</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcihijospendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcihijos)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cihijos']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrardenfaccidente || $mostrardenfaccidentependiente)
                                    <tr>
                                        <td>DENUNCIA ENFERMEDAD ACCIDENTE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrardenfaccidentependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrardenfaccidente)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['denfaccidente']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a> 
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcrodomicilio || $mostrarcrodomiciliopendiente)
                                    <tr>
                                        <td>CROQUIS DE DOMICILIO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcrodomiciliopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcrodomicilio)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['crodomicilio']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcontrato || $mostrarcontratopendiente)
                                    <tr>
                                        <td>CONTRATO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcontratopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcontrato)
                                                @if ($userRole === 'MAESTRO' || $userRole === 'ADMINISTRADOR')
                                                    <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['contrato']) }}" class="btn btn-verdocumentacion" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a> 
                                                @else
                                                    <a class="btn btn-verdocumentacion" disabled>
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostraregestora || $mostraregestorapendiente)
                                    <tr>
                                        <td>EXTRACTO DE GESTORA</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostraregestorapendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostraregestora)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['egestora']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrardictamencalentenc || $mostrardictamencalentencpendiente)
                                    <tr>
                                        <td>DICTAMEN CALIFICACION ENTIDAD ENCARGADA</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrardictamencalentencpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrardictamencalentenc)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['dictamencalentenc']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarinfomedicasalud || $mostrarinfomedicasaludpendiente)
                                    <tr>
                                        <td>INFORMACION MEDICA</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarinfomedicasaludpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarinfomedicasalud)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['infomedicasalud']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarctrabajo || $mostrarctrabajopendiente)
                                    <tr>
                                        <td>CERTIFICADO DE TRABAJO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarctrabajopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarctrabajo)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ctrabajo']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarboletapago || $mostrarboletapagopendiente)
                                    <tr>
                                        <td>BOLETA DE PAGO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarboletapagopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarboletapago)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['boletapago']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostraractdatos || $mostraractdatospendiente)
                                    <tr>
                                        <td>ACTUALIZACION DE DATOS</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostraractdatospendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostraractdatos)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['actdatos']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarresolinvhijos || $mostrarresolinvhijospendiente)
                                    <tr>
                                        <td>RESOLUCION INVALIDEZ DE HIJOS < 25</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarresolinvhijospendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarresolinvhijos)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['resolinvhijos']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcunionlibre || $mostrarcunionlibrependiente)
                                    <tr>
                                        <td>CERTIFICADO DE UNION LIBRE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcunionlibrependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcunionlibre)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cunionlibre']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcnacimientounionlibre || $mostrarcnacimientounionlibrependiente)
                                    <tr>
                                        <td>CERTIFICADO NACIMIENTO DE UNION LIBRE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcnacimientounionlibrependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcnacimientounionlibre)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cnacimientounionlibre']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarciunionlibre || $mostrarciunionlibrependiente)
                                    <tr>
                                        <td>CARNET IDENTIDAD DE UNION LIBRE</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarciunionlibrependiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarciunionlibre)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['ciunionlibre']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcdivorcio || $mostrarcdivorciopendiente)
                                    <tr>
                                        <td>CERTIFICADO DE DIVORCIO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcdivorciopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcdivorcio)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cdivorcio']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarcdefuncion || $mostrarcdefuncionpendiente)
                                    <tr>
                                        <td>CERTIFICADO DE DIFUNCION</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarcdefuncionpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarcdefuncion)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['cdefuncion']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarpolizasgen || $mostrarpolizasgenpendiente)
                                    <tr>
                                        <td>POLIZAS GENERALES</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarpolizasgenpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarpolizasgen)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['polizasgen']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrardeclasalud || $mostrardeclasaludpendiente)
                                    <tr>
                                        <td>DECLARACION SALUD</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrardeclasaludpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrardeclasalud)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['declasalud']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarpolizaseguro || $mostrarpolizaseguropendiente)
                                    <tr>
                                        <td>POLIZA SEGURO DESGRAVAMEN</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarpolizaseguropendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarpolizaseguro)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['polizaseguro']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostraranteriordictamen || $mostraranteriordictamenpendiente)
                                    <tr>
                                        <td>ANTERIOR DICTAMEN O RESOLUCION</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostraranteriordictamenpendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostraranteriordictamen)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['anteriordictamen']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if ($mostrarpoderciapoderado || $mostrarpoderciapoderadopendiente)
                                    <tr>
                                        <td>PODER Y CARNET IDENTIDAD APODERADO</td>
                                        <td>{{ $accion['servicio'] }}</td>
                                        <td>
                                            @if ($mostrarpoderciapoderadopendiente)
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($mostrarpoderciapoderado)
                                                <a href="{{ asset('/requisitosclientesita/' . $item['clienteitaid'] . '/' . $accion['poderciapoderado']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>  
                                            @endif
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- APROBAR BATERIA --}}
    <div class="modal fade" id="modalAprobar{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelAprobar{{ $loop->index }}" aria-hidden="true" style="margin-top: -80px;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center"><br>
                    <h4 class="mb-4">¿ESTÁ SEGURO DE APROBAR LA BATERÍA?</h4>
                    {!! Form::model($item, ['route' => ['admin.informesfinales.guardaraprobacioninformefinal', $item['clienteitaid']], 'method' => 'POST', 'id' => 'formAprobar{{ $loop->index }}']) !!}
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                        <input type="hidden" name="cliente" value="{{ $item['clienteitanombre'] }}">
                        <input type="hidden" name="fechabateria" value="{{ $item['fechabateria'] }}">
                        <input type="hidden" name="proveedornombre" value="{{ $item['proveedornombre'] }}">
                        <input type="hidden" name="estado" value="APROBADO">
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-no" data-dismiss="modal">NO</button>
                            <button type="submit" class="btn btn-si">SI</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    {{-- SUBIR INFORME FINAL --}}
    <div class="modal fade" id="modalUpload{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelUpload{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h4 class="mb-4">SUBIR INFORME FINAL</h4>
                    {!! Form::model($item, ['route' => ['admin.informesfinales.guardarinformefinal', $item['clienteitaid']], 'method' => 'POST', 'id' => 'formAprobar{{ $loop->index }}', 'enctype' => 'multipart/form-data']) !!}
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}

                        <input type="hidden" name="clienteitaid" value="{{ $item['clienteitaid'] }}">
                        <div class="form-group">
                            <input type="hidden" name="cliente" value="{{ $item['clienteitanombre'] }}">
                            <input type="hidden" name="fechabateria" value="{{ $item['fechabateria'] }}">
                            @php
                                $tramiteOriginal = $item['tramite'];
                                $tramites = array_map('trim', explode(',', $tramiteOriginal));
                                $ultimoTramite = end($tramites);
                            @endphp

                            <input type="hidden" name="tramite" value="{{ $ultimoTramite }}">

                            <input type="hidden" name="estado" value="EN REVISIÓN">
                            {!! Form::label('file', 'INFORME PDF (OBLIGATORIO):') !!}
                            <input type="file" name="document" id="document" class="dropify" accept=".pdf"/>
                            @error('document')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                            @enderror
                            <br>
                            {!! Form::label('file', 'INFORME WORD:') !!}
                            <input type="file" name="documentword" id="documentword" class="dropify" accept=".docx"/>
                            @error('documentword')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{$message}}
                            </small>
                            @enderror
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-no" data-dismiss="modal">CERRAR</button>
                            <button type="submit" class="btn btn-si">SUBIR</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endforeach

@stop
