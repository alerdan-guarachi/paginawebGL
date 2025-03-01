@extends('adminlte::page')

@section('content_header')
<h1>INFORMES FINALES AUDITORIA</h1>
@stop
 
@section('css')
<link rel="stylesheet" href="{{ asset('css/informesfinales.css') }}">
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
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid justify-content-end">
            <div class="d-flex flex-wrap align-items-center">
                {{-- {{ $programacionclientes->links() }} --}}
                <form id="search-form" action="{{ route('buscarprogramacionesclienteauditoria') }}" method="get" class="form-inline">
                    <div class="d-flex">
                        <input type="text" name="buscarporcliente" class="form-control" placeholder="NOMBRE DE CLIENTE">
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0 ml-2" type="submit">BUSCAR</button>
                        {{-- <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" type="button">Mostrar Todo</button> --}}
                        <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" name="buscartodo" type="submit" value="1">MOSTRAR TODO</button>
                    </div>
                </form>
            </div>
        </div>
    </nav>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarprogramacionesclienteauditoria') }}";
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
            {{-- @can('admin.informesfinales.asignarproveedorinformesfinales') --}}
            <li class="nav-item">
                <a class="nav-link" id="tab-7" data-toggle="tab" href="#tab-content-7" role="tab" aria-controls="tab-content-7" aria-selected="true">
                    INFO. OBSERVADOS
                    <?php if ($docobservadosCount > 0): ?>
                        <span class="circle"><?= $docobservadosCount ?></span>
                    <?php endif; ?>
                </a>
            </li>     
            {{-- @endcan --}}

            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $usuarioAutenticado === 'VANESSA MAMANI HUANACO' || $usuarioAutenticado === 'MARLENE ANDREA MONTELLANO ORTIZ' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE')
                @can('admin.informesfinales.aprobarbateriainformesfinales') 
                <li class="nav-item">
                    <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                        APROBAR BATERIA
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
           {{--  @can('admin.informesfinales.subirinformesfinales') 
                @can('admin.informesfinales.verinformestodosproveedores') 
                <li class="nav-item">
                    <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                        SOLIC. REVISIÓN
                        
                    </a>
                </li>
                @endcan
                @can('admin.informesfinales.soloverinformessegunproveedor') 
                <li class="nav-item">
                    <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                        SOLIC. REVISIÓN
                        
                    </a>
                </li>
                @endcan
            @endcan --}}

            {{-- @can('admin.informesfinales.subirinformesfinales') 
                @can('admin.informesfinales.verinformestodosproveedores') 
                <li class="nav-item">
                    <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                        EN REVISIÓN
                        
                    </a>
                </li>
                @endcan
                @can('admin.informesfinales.soloverinformessegunproveedor') 
                <li class="nav-item">
                    <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                        EN REVISIÓN
                        
                    </a>
                </li>
                @endcan 
            @endcan  --}}

            @if ($usuarioAutenticado === 'CARLOS ALEJANDRO GUARACHI SANDOVAL' || $usuarioAutenticado === 'DENISSE MAUREN LOPEZ FLORES' || $usuarioAutenticado === 'JHOSELINE EVA VELASQUEZ ESCOBAR' || $usuarioAutenticado === 'VANESSA MAMANI HUANACO' || $usuarioAutenticado === 'AGUIRRE VASQUEZ MARIA RENEE' || $usuarioAutenticado === 'MARLENE ANDREA MONTELLANO ORTIZ')
                @can('admin.informesfinales.verinformesfinales')
                    @can('admin.informesfinales.verinformestodosproveedores')  
                    <li class="nav-item">
                        <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                            APROBADOS
                            <?php if ($aprobadosCount > 0): ?>
                                <span class="circle"><?= $aprobadosCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    @endcan
                    @can('admin.informesfinales.soloverinformessegunproveedor')  
                    <li class="nav-item">
                        <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                            APROBADOS
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
            @can('admin.informesfinales.asignarproveedorinformesfinales')
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Cliente</th>
                                <th>Cliente</th>
                                <th>Fecha Batería</th>
                                <th>Servicio</th>
                                <th>Result. Médicos</th>
                                <th>Otros doc.</th>
                                <th>Asig. prov.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if (!$item['proveedornombre'] && $item['estado'] === 'COMPLETO')
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>

                                    {{-- SERVICIO TRAMITE --}}
                                    <td>{{ $item['tramite'] }}</td>

                                    {{-- RESULTADOS MÉDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>

                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>

                                    {{-- ASIGNAR --}}
                                    <td> {{-- PROVEEDOR PENDIENTE --}}
                                        <abbr title="ASIGNAR PROVEEDOR">
                                            <a class="btn btn-sm btn-bateria" href="{{ route('admin.asociados.verclienteauditoria', $item['clienteauditoriaid']) }}">
                                                <i class="fas fa-user"></i>
                                            </a>
                                        </abbr>
                                        {{-- @if ($item['proveedornombre'])
                                            {{ $item['proveedornombre'] }}
                                        @else
                                        <abbr title="ASIGNAR PROVEEDOR">
                                            <button class="btn btn-asignarproveedor"
                                                    data-toggle="modal"
                                                    data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                    data-clienteitanombre="{{ $item['clienteitanombre'] }}"
                                                    data-clienteitaid="{{ $item['clienteitaid'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}">
                                                    <i class="fas fa-user-tag"></i>
                                            </button>
                                        </abbr>
                                        @endif --}}
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody> 
                    </table>
                </div>
            </div>
            @endcan

            {{-- DOCUMENTOS OBSERVADOS --}}
            {{-- @can('admin.informesfinales.aprobarbateriainformesfinales') --}}
            @foreach ($result as $item)
            <div class="tab-pane fade" id="tab-content-7" role="tabpanel" aria-labelledby="tab-7">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr> 
                                <th>ID Cliente</th>
                                <th>Cliente</th>
                                <th>Bateria</th>
                                <th>Acción</th>
                                <th>Proveedor</th>
                                <th>Documento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item['acciones'] as $accion)
                                <tr>
                                    @if (isset($accion['observacion']) && !empty($accion['observacion']))
                                    <td>{{ $accion['clienteauditoriaid'] }}</td>
                                    <td>{{ $accion['clienteauditorianombre'] }}</td>
                                    <td>{{ $accion['fechabateria'] }}</td>
                                    <td>{{ $accion['accion'] }}</td>
                                    <td>{{ $accion['proveedornombre'] }}</td>
                                    <td>
                                        <div class="observacion">
                                            <button class="btn btn-solicitocorreccion btn-sm" data-toggle="modal" data-target="#modalObservacion2{{ $loop->parent->index }}-{{ $loop->index }}">
                                                ACTUALIZAR
                                            </button>
                                            <div class="modal fade" id="modalObservacion2{{ $loop->parent->index }}-{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalObservacion2Label{{ $loop->parent->index }}-{{ $loop->index }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="modalObservacion2Label{{ $loop->parent->index }}-{{ $loop->index }}">SOLICITÓ CORRECCIÓN</h4>
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
                                    </td>
                                    @elseif ($accion['estado'] === 'COMPLETO' && isset($accion['document']))
                                            
                                    @else
                                        <div class="pendiente">PENDIENTE</div>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
            {{-- @endcan --}}

            {{-- APROBAR BATERIA --}}
            @can('admin.informesfinales.aprobarbateriainformesfinales')
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
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
                                <th>Otros doc.</th>
                                <th>Aprobar Batería</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['proveedornombre'] && $item['estadoinforme'] !== 'APROBADO' && $item['estado'] === 'COMPLETO')
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- PROVEEDOR --}}
                                    <td>
                                        @if ($item['proveedornombre'])
                                            {{ $item['proveedornombre'] }}
                                        @else
                                            <button class="btn btn-asignarproveedor"
                                                    data-toggle="modal"
                                                    data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}">
                                                Asignar Proveedor
                                            </button>
                                        @endif
                                    </td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>

                                    {{-- SERVICIO TRAMITE --}}
                                    <td>{{ $item['tramite'] }}</td>

                                    {{-- ESTUDIOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>

                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>

                                    {{-- ACCIONES --}}
                                    <td>
                                        @php
                                            $aprobado = $aprobaciones->where('clienteauditoriaid', $item['clienteauditoriaid'])
                                                                    ->where('clienteauditorianombre', $item['clienteauditorianombre'])
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
                                                            data-cliente="{{ $item['clienteauditorianombre'] }}" 
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
                                <th>Otros doc.</th>
                                <th>Subir Informe</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @can('admin.informesfinales.soloverinformessegunproveedor')
                            @foreach ($result as $item)
                            @if ($item['proveedornombre'] && $item['estadoinforme'] === 'APROBADO' && !$item['estado_informefinal'] && $item['proveedornombre'] === $usuarioAutenticado)
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- PROVEEDOR --}}
                                    <td>
                                        @if ($item['proveedornombre'])
                                            {{ $item['proveedornombre'] }}
                                        @else
                                            <button class="btn btn-asignarproveedor"
                                                    data-toggle="modal"
                                                    data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}">
                                                Asignar Proveedor
                                            </button>
                                        @endif
                                    </td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>

                                    {{-- SERVICIO TRAMITE --}}
                                    <td>{{ $item['tramite'] }}</td>

                                    {{-- RESULTADOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>

                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>

                                    {{-- SUBIR INFORME FINAL --}}
                                    <td>
                                        @if ($item['document'])
                                        @else
                                            @if ($item['estado_informefinal'] != 'APROBADO')
                                                @php
                                                    $aprobado = $aprobaciones->where('clienteauditoriaid', $item['clienteauditoriaid'])
                                                                            ->where('clienteauditorianombre', $item['clienteauditorianombre'])
                                                                            ->where('fechabateria', $item['fechabateria'])
                                                                            ->first();
                                                @endphp
                                                <abbr title="SUBIR INFORME">
                                                    <a class="btn btn-subirinformeinicio {{ $aprobado ? 'btn-upload' : 'btn-disabled' }} {{ $aprobado ? 'text-black' : 'text-secondary' }}"
                                                            data-toggle="{{ $aprobado ? 'modal' : '' }}"
                                                            data-target="{{ $aprobado ? '#modalUpload' . $loop->index : '' }}"
                                                            {{ !$aprobado ? 'disabled' : '' }}>
                                                            <i class="fas fa-upload"></i>
                                                    </a>
                                                </abbr>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            @endcan

                            @can('admin.informesfinales.verinformestodosproveedores')
                            @foreach ($result as $item)
                            @if ($item['proveedornombre'] && $item['estadoinforme'] === 'APROBADO' && !$item['estado_informefinal'])
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- PROVEEDOR --}}
                                    <td>
                                        @if ($item['proveedornombre'])
                                            {{ $item['proveedornombre'] }}
                                        @else
                                            <button class="btn btn-asignarproveedor"
                                                    data-toggle="modal"
                                                    data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}">
                                                Asignar Proveedor
                                            </button>
                                        @endif
                                    </td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>

                                    {{-- SERVICIO TRAMITE --}}
                                    <td>{{ $item['tramite'] }}</td>

                                    {{-- RESULTADOS MEDICOS --}}
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
                                    

                                    {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>

                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>

                                    {{-- SUBIR INFORME FINAL --}}
                                    <td>
                                        @if ($item['document'])
                                        @else
                                            @if ($item['estado_informefinal'] != 'APROBADO')
                                                @php
                                                    $aprobado = $aprobaciones->where('clienteauditoriaid', $item['clienteauditoriaid'])
                                                                            ->where('clienteauditorianombre', $item['clienteauditorianombre'])
                                                                            ->where('fechabateria', $item['fechabateria'])
                                                                            ->first();
                                                @endphp
                                                <abbr title="SUBIR INFORME">
                                                    <a class="btn btn-subirinformeinicio {{ $aprobado ? 'btn-upload' : 'btn-disabled' }} {{ $aprobado ? 'text-black' : 'text-secondary' }}"
                                                            data-toggle="{{ $aprobado ? 'modal' : '' }}"
                                                            data-target="{{ $aprobado ? '#modalUpload' . $loop->index : '' }}"
                                                            {{ !$aprobado ? 'disabled' : '' }}>
                                                            <i class="fas fa-upload"></i>
                                                </a>
                                                </abbr>
                                            @endif
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

            {{-- EN REVISION --}}
            @can('admin.informesfinales.subirinformesfinales')
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Cliente</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Fecha Bateria</th>
                                <th>Cod. Servicio</th>
                                <th>Result. Médicos</th>
                                <th>Otros doc.</th>
                                <th>Informe Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @can('admin.informesfinales.soloverinformessegunproveedor')
                            @foreach ($result as $item)
                            @if ($item['estado_informefinal'] === 'EN REVISIÓN' && $item['proveedornombre'] === $usuarioAutenticado)
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- PROVEEDOR --}}
                                    <td>
                                        @if ($item['proveedornombre'])
                                            {{ $item['proveedornombre'] }}
                                        @else
                                            <button class="btn btn-asignarproveedor"
                                                    data-toggle="modal"
                                                    data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}">
                                                Asignar Proveedor
                                            </button>
                                        @endif
                                    </td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>

                                    {{-- SERVICIO TRAMITE --}}
                                    <td>{{ $item['tramite'] }}</td>

                                    {{-- ESTUDIOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>

                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>

                                    {{-- ESTADO INFORME FINAL --}}
                                    <td>
                                        @if ($item['estado_informefinal'])
                                            <span class="{{ 
                                                $item['estado_informefinal'] === 'APROBADO' ? 'text-aprobado' : 
                                                ($item['estado_informefinal'] === 'EN REVISIÓN' ? 'text-enrevision' : 
                                                ($item['estado_informefinal'] === 'SOLICITÓ REVISIÓN' ? 'text-solicitorevision' : ''))
                                            }}">
                                                {{ $item['estado_informefinal'] }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- ACCIONES --}}
                                    <td>
                                        <div class="btn-container">
                                            @if ($item['document'])

                                                {{-- VER INFORME FINAL --}}
                                                <abbr title="VER INFORME FINAL">
                                                    <a href="{{ asset('/informesfinalesclientesauditoria/' .$item['clienteauditoriaid'] . '/' .  $item['document']) }}" class="btn btn-veracciones" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </abbr>
                                                @if ($item['estado_informefinal'] != 'APROBADO')

                                                    {{-- SOLICITAR REVISION --}}
                                                    @can('admin.informesfinales.solicitarrevisioninformesfinales')
                                                    <abbr title="SOLICITAR REVISIÓN">
                                                        <button class="btn btn-solicitarrevision" 
                                                                data-toggle="modal" 
                                                                data-target="#modalSolicitarRevision{{ $item['idinformefinal'] }}">
                                                                <i class="fas fa-edit"></i>
                                                        </button>
                                                    </abbr>
                                                    @endcan
                                                    {{-- MODAL SOLICITAR REVISION --}}
                                                    <div class="modal fade" id="modalSolicitarRevision{{ $item['idinformefinal'] }}" tabindex="-1" role="dialog" aria-labelledby="modalSolicitarRevisionLabel{{ $item['clienteitaid'] }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" id="modalSolicitarRevisionLabel{{ $item['clienteauditoriaid'] }}">SOLICITAR REVISIÓN</h4>

                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                
                                                                <form action="{{ route('admin.informesfinales.solrevisioninformefinalauditoria', ['item' => $item['idinformefinal']]) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                        <div class="form-group">
                                                                            <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                            <label for="observaciones">Observaciones:</label>
                                                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-center">
                                                                        <button type="button" class="btn btn-no" data-dismiss="modal">Cerrar</button>
                                                                        <button type="submit" class="btn btn-si">Guardar</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- APROBAR INFORME --}}
                                                    @can('admin.informesfinales.aprobarinformesfinales')
                                                    <abbr title="APROBAR INFORME">
                                                        <button class="btn btn-aprobarinforme" 
                                                                data-toggle="modal" 
                                                                data-target="#modalAprobarInforme{{ $item['idinformefinal'] }}">
                                                                <i class="fas fa-check"></i>
                                                        </button>
                                                    </abbr>
                                                    @endcan
                                                    {{-- MODAL APROBAR INFORME --}}
                                                    <div class="modal fade" id="modalAprobarInforme{{ $item['idinformefinal'] }}" tabindex="-1" role="dialog" aria-labelledby="modalAprobarInformeLabel{{ $item['clienteauditoriaid'] }}" aria-hidden="true">
                                                        <div class="modal-dialog  modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header text-center">
                                                                    {{-- <h4 class="modal-title" id="modalAprobarInforme{{ $item['clienteitaid'] }}">Aprobar Informe</h4> --}}
                                                                    <h4 class="mb-4" id="modalAprobarInforme{{ $item['clienteauditoriaid'] }}">¿ESTÁ SEGURO DE QUE DESEA APROBAR EL INFORME FINAL?</h4>
                                                                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button> --}}
                                                                </div>
                                                                
                                                                <form action="{{ route('admin.informesfinales.aprobarinformefinalfsauditoria', ['item' => $item['idinformefinal']]) }}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="modal-body" style="margin-top: -40px" >
                                                                        <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                    </div>
                                                                    <div class="modal-footer justify-content-center">
                                                                        <button type="button" class="btn btn-no" data-dismiss="modal">NO</button>
                                                                        <button type="submit" class="btn btn-si">SI</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                            @else
                                                {{-- SUBIR INFORME FINAL --}}
                                                @if ($item['estado_informefinal'] != 'APROBADO')
                                                    @php
                                                        $aprobado = $aprobaciones->where('clienteitaid', $item['clienteitaid'])
                                                                                ->where('clienteitanombre', $item['clienteitanombre'])
                                                                                ->where('fechabateria', $item['fechabateria'])
                                                                                ->first();
                                                    @endphp
                                                    <abbr title="SUBIR INFORME">
                                                        <button class="btn btn-subirinforme {{ $aprobado ? 'btn-upload' : 'btn-disabled' }} {{ $aprobado ? 'text-black' : 'text-secondary' }}"
                                                                data-toggle="{{ $aprobado ? 'modal' : '' }}"
                                                                data-target="{{ $aprobado ? '#modalUpload' . $loop->index : '' }}"
                                                                {{ !$aprobado ? 'disabled' : '' }}>
                                                                <i class="fas fa-upload "></i>
                                                        </button>
                                                    </abbr>
                                                @endif
                                            @endif

                                            {{-- ENVIAR OBSERVACION POR WHATSAPP --}}
                                            @if ($item['proveedornombre'] && $item['celularproveedor']  && $item['clienteauditorianombre'] && $item['ultima_observacion'])
                                                @php
                                                    $mensaje = "Buenos días, informarle que su informe del cliente " . $item['clienteauditorianombre']. " tiene la siguiente observación: " . $item['ultima_observacion'];
                                                    $mensajeUrl = urlencode($mensaje);
                                                @endphp
                                                <abbr title="ENVIAR OBSERVACIÓN">
                                                <a class="btn btn-enviarobservacion" 
                                                    href="https://wa.me/{{ $item['celularproveedor'] }}?text={{ $mensajeUrl }}" 
                                                    target="_blank"
                                                    class="btn btn-whatsapp">
                                                    <i class="fas fa-comment"></i>
                                                </a>
                                            </abbr>
                                            @endif

                                            {{-- ULTIMA OBSERVACION --}}
                                            @if ($item['ultima_observacion2'])
                                            <abbr title="ÚLTIMA OBSERVACIÓN">
                                                <a class="btn btn-observaciones" data-toggle="modal" data-target="#modalobservaciones{{ $loop->index }}"><i class="fas fa-exclamation-triangle"></i></a>
                                            </abbr>
                                            @endif
                                        </div> 
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            @endcan

                            @can('admin.informesfinales.verinformestodosproveedores')
                            @foreach ($result as $item)
                            @if ($item['estado_informefinal'] === 'EN REVISIÓN')
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- PROVEEDOR --}}
                                    <td>
                                        @if ($item['proveedornombre'])
                                            {{ $item['proveedornombre'] }}
                                        @else
                                            <button class="btn btn-asignarproveedor"
                                                    data-toggle="modal"
                                                    data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}">
                                                Asignar Proveedor
                                            </button>
                                        @endif
                                    </td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>

                                    {{-- SERVICIO TRAMITE --}}
                                    <td>{{ $item['tramite'] }}</td>

                                    {{-- ESTUDIOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>

                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>

                                    {{-- ESTADO INFORME FINAL --}}
                                    {{-- <td>
                                        @if ($item['estado_informefinal'])
                                            <span class="{{ 
                                                $item['estado_informefinal'] === 'APROBADO' ? 'text-aprobado' : 
                                                ($item['estado_informefinal'] === 'EN REVISIÓN' ? 'text-enrevision' : 
                                                ($item['estado_informefinal'] === 'SOLICITÓ REVISIÓN' ? 'text-solicitorevision' : ''))
                                            }}">
                                                {{ $item['estado_informefinal'] }}
                                            </span>
                                        @endif
                                    </td> --}}

                                    {{-- ACCIONES --}}
                                    <td>
                                        <div class="btn-container">
                                            @if ($item['documentfirmado'])

                                                {{-- VER INFORME FINAL --}}
                                                <abbr title="VER INFORME FINAL">
                                                    <a href="{{ asset('/informesfinalesclientesauditoria/' .$item['clienteauditoriaid']. '/' .  $item['documentfirmado']) }}" class="btn btn-veracciones" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </abbr>
                                                @if ($item['estado_informefinal'] != 'APROBADO')

                                                    {{-- SOLICITAR REVISION --}}
                                                    @can('admin.informesfinales.solicitarrevisioninformesfinales')
                                                    <abbr title="SOLICITAR REVISIÓN">
                                                        <a class="btn btn-solicitarrevision" 
                                                                data-toggle="modal" 
                                                                data-target="#modalSolicitarRevision{{ $item['idinformefinal'] }}">
                                                                <i class="fas fa-edit"></i>
                                                    </a>
                                                    </abbr>
                                                    @endcan
                                                    {{-- MODAL SOLICITAR REVISION --}}
                                                    <div class="modal fade" id="modalSolicitarRevision{{ $item['idinformefinal'] }}" tabindex="-1" role="dialog" aria-labelledby="modalSolicitarRevisionLabel{{ $item['clienteauditoriaid'] }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" id="modalSolicitarRevisionLabel{{ $item['clienteauditoriaid'] }}">SOLICITAR REVISIÓN</h4>

                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                
                                                                <form action="{{ route('admin.informesfinales.solrevisioninformefinal', ['item' => $item['idinformefinal']]) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                        <div class="form-group">
                                                                            <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                            <label for="observaciones">Observaciones:</label>
                                                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-center">
                                                                        <button type="button" class="btn btn-no" data-dismiss="modal">Cerrar</button>
                                                                        <button type="submit" class="btn btn-si">Guardar</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- APROBAR INFORME --}}
                                                    @can('admin.informesfinales.aprobarinformesfinales')
                                                    <abbr title="APROBAR INFORME">
                                                        <a class="btn btn-aprobarinforme" 
                                                                data-toggle="modal" 
                                                                data-target="#modalAprobarInforme{{ $item['idinformefinal'] }}">
                                                                <i class="fas fa-check"></i>
                                                    </a>
                                                    </abbr>
                                                    @endcan
                                                    {{-- MODAL APROBAR INFORME --}}
                                                    <div class="modal fade" id="modalAprobarInforme{{ $item['idinformefinal'] }}" tabindex="-1" role="dialog" aria-labelledby="modalAprobarInformeLabel{{ $item['clienteauditoriaid'] }}" aria-hidden="true">
                                                        <div class="modal-dialog  modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header text-center">
                                                                    {{-- <h4 class="modal-title" id="modalAprobarInforme{{ $item['clienteitaid'] }}">Aprobar Informe</h4> --}}
                                                                    <h4 class="mb-4" id="modalAprobarInforme{{ $item['clienteauditoriaid'] }}">¿ESTÁ SEGURO DE APROBAR EL INFORME FINAL?</h4>
                                                                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button> --}}
                                                                </div>
                                                                
                                                                <form action="{{ route('admin.informesfinales.aprobarinformefinalfs', ['item' => $item['idinformefinal']]) }}" method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="modal-body" style="margin-top: -40px" >
                                                                        <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                    </div>
                                                                    <div class="modal-footer justify-content-center">
                                                                        <button type="button" class="btn btn-no" data-dismiss="modal">NO</button>
                                                                        <button type="submit" class="btn btn-si">SI</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                            @else
                                                {{-- SUBIR INFORME FINAL --}}
                                                @if ($item['estado_informefinal'] != 'APROBADO')
                                                    @php
                                                        $aprobado = $aprobaciones->where('clienteauditoriaid', $item['clienteauditoriaid'])
                                                                                ->where('clienteauditorianombre', $item['clienteauditorianombre'])
                                                                                ->where('fechabateria', $item['fechabateria'])
                                                                                ->first();
                                                    @endphp
                                                    <abbr title="SUBIR INFORME">
                                                        <button class="btn btn-subirinforme {{ $aprobado ? 'btn-upload' : 'btn-disabled' }} {{ $aprobado ? 'text-black' : 'text-secondary' }}"
                                                                data-toggle="{{ $aprobado ? 'modal' : '' }}"
                                                                data-target="{{ $aprobado ? '#modalUpload' . $loop->index : '' }}"
                                                                {{ !$aprobado ? 'disabled' : '' }}>
                                                                <i class="fas fa-upload "></i>
                                                        </button>
                                                    </abbr>
                                                @endif
                                            @endif

                                            {{-- ENVIAR OBSERVACION POR WHATSAPP --}}
                                            @if ($item['proveedornombre'] && $item['celularproveedor']  && $item['clienteauditorianombre'] && $item['ultima_observacion'])
                                                @php
                                                    $mensaje = "Hola!, informarle que el informe final del cliente " . $item['clienteauditorianombre']. " tiene la siguiente observación: " . $item['ultima_observacion'];
                                                    $mensajeUrl = urlencode($mensaje);
                                                @endphp
                                                <abbr title="ENVIAR OBSERVACIÓN">
                                                <a class="btn btn-enviarobservacion" 
                                                    href="https://wa.me/{{ $item['celularproveedor'] }}?text={{ $mensajeUrl }}" 
                                                    target="_blank"
                                                    class="btn btn-whatsapp">
                                                    <i class="fas fa-comment"></i>
                                                </a>
                                            </abbr>
                                            @endif

                                            {{-- ULTIMA OBSERVACION --}}
                                            @if ($item['ultima_observacion2'])
                                            <abbr title="ÚLTIMA OBSERVACIÓN">
                                                <a class="btn btn-observaciones" data-toggle="modal" data-target="#modalobservaciones{{ $loop->index }}"><i class="fas fa-exclamation-triangle"></i></a>
                                            </abbr>
                                            @endif
                                        </div> 
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

            @can('admin.informesfinales.subirinformesfinales')
            {{-- SOLICITO REVISION --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
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
                                <th>Otros doc.</th>
                                <th>Informe final</th>
                            </tr>
                        </thead>
                        <tbody>

                            @can('admin.informesfinales.soloverinformessegunproveedor')
                            @foreach ($result as $item)
                                    @if ($item['estado_informefinal'] === 'SOLICITÓ REVISIÓN' && $item['proveedornombre'] === $usuarioAutenticado)
                                    <tr>
                                        {{-- ID DEL CLIENTE --}}
                                        <td>{{ $item['clienteauditoriaid'] }}</td>

                                        {{-- CLIENTE --}}
                                        <td>{{ $item['clienteauditorianombre'] }}</td>

                                        {{-- PROVEEDOR --}}
                                        <td>
                                            @if ($item['proveedornombre'])
                                                {{ $item['proveedornombre'] }}
                                            @else
                                                <button class="btn btn-asignarproveedor"
                                                        data-toggle="modal"
                                                        data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                        data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                        data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                        data-fechabateria="{{ $item['fechabateria'] }}">
                                                    Asignar Proveedor
                                                </button>
                                            @endif
                                        </td>

                                        {{-- CELULAR DE PROVEEDOR --}}
                                        <td hidden>
                                            @if ($item['proveedornombre'])
                                                {{ $item['celularproveedor'] }}
                                            @endif
                                        </td>

                                        {{-- FECHA DE BATERIA --}}
                                        <td>{{ $item['fechabateria'] }}</td>

                                        {{-- SERVICIO TRAMITE --}}
                                        <td>{{ $item['tramite'] }}</td>

                                        {{-- ESTADO DE DOCUMENTACION --}}
                                        <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                            {{ $item['estado'] }}
                                            <abbr title="VER DOCUMENTACIÓN">
                                                <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                            </abbr>
                                        </td>

                                        {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                        <td>
                                            <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                                <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>

                                            @if ($item['historiamedica'])
                                            <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                    <i class="fas fa-book-medical"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="btn-container">
                                                @if ($item['document'])

                                                    {{-- VER INFORME FINAL --}}
                                                    <a href="{{ asset('/informesfinalesclientesauditoria/' . $item['document']) }}" class="btn btn-veracciones" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if ($item['estado_informefinal'] != 'APROBADO')

                                                        {{-- SOLICITAR REVISION --}}
                                                        <abbr title="SOLICITAR REVISIÓN">
                                                            <a class="btn btn-solicitarrevision" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalSolicitarRevision{{ $item['idinformefinal'] }}">
                                                                    <i class="fas fa-edit"></i>
                                                        </a>
                                                        </abbr>
                                                        <div class="modal fade" id="modalSolicitarRevision{{ $item['idinformefinal'] }}" tabindex="-1" role="dialog" aria-labelledby="modalSolicitarRevisionLabel{{ $item['clienteauditoriaid'] }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title" id="modalSolicitarRevisionLabel{{ $item['clienteauditoriaid'] }}">SOLICITAR REVISIÓN</h4>

                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    
                                                                    <form action="{{ route('admin.informesfinales.solrevisioninformefinal', ['item' => $item['idinformefinal']]) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                            <div class="form-group">
                                                                                <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                                <label for="observaciones">Observaciones:</label>
                                                                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer justify-content-center">
                                                                            <button type="button" class="btn btn-no" data-dismiss="modal">Cerrar</button>
                                                                            <button type="submit" class="btn btn-si">Enviar Solicitud</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- APROBAR INFORME --}}
                                                        <abbr title="APROBAR INFORME">
                                                            <a class="btn btn-aprobarinforme" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalAprobarInforme{{ $item['idinformefinal'] }}">
                                                                    <i class="fas fa-check"></i>
                                                        </a>
                                                        </abbr>
                                                        <div class="modal fade" id="modalAprobarInforme{{ $item['idinformefinal'] }}" tabindex="-1" role="dialog" aria-labelledby="modalAprobarInformeLabel{{ $item['clienteauditoriaid'] }}" aria-hidden="true">
                                                            <div class="modal-dialog  modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header text-center">
                                                                        {{-- <h4 class="modal-title" id="modalAprobarInforme{{ $item['clienteitaid'] }}">Aprobar Informe</h4> --}}
                                                                        <h4 class="mb-4" id="modalAprobarInforme{{ $item['clienteauditoriaid'] }}">¿ESTÁ SEGURO DE QUE DESEA APROBAR EL INFORME FINAL?</h4>
                                                                        {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button> --}}
                                                                    </div>
                                                                    
                                                                    <form action="{{ route('admin.informesfinales.aprobarinformefinalfs', ['item' => $item['idinformefinal']]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="modal-body" style="margin-top: -40px" >
                                                                            <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                        </div>
                                                                        <div class="modal-footer justify-content-center">
                                                                            <button type="button" class="btn btn-no" data-dismiss="modal">NO</button>
                                                                            <button type="submit" class="btn btn-si">SI</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                @else
                                                    {{-- SUBIR INFORME FINAL --}}
                                                    @if ($item['estado_informefinal'] != 'APROBADO')
                                                        @php
                                                            $aprobado = $aprobaciones->where('clienteauditoriaid', $item['clienteauditoriaid'])
                                                                                    ->where('clienteauditorianombre', $item['clienteauditorianombre'])
                                                                                    ->where('fechabateria', $item['fechabateria'])
                                                                                    ->first();
                                                        @endphp
                                                        <abbr title="SUBIR INFORME">
                                                            <a class="btn btn-subirinforme {{ $aprobado ? 'btn-upload' : 'btn-disabled' }} {{ $aprobado ? 'text-black' : 'text-secondary' }}"
                                                                    data-toggle="{{ $aprobado ? 'modal' : '' }}"
                                                                    data-target="{{ $aprobado ? '#modalUpload' . $loop->index : '' }}"
                                                                    {{ !$aprobado ? 'disabled' : '' }}>
                                                                    <i class="fas fa-upload "></i>
                                                        </a>
                                                        </abbr>
                                                    @endif
                                                @endif

                                                {{-- ENVIAR OBSERVACION POR WHATSAPP --}}
                                                @can('admin.informesfinales.enviarobservacioninformesfinales')
                                                @if ($item['proveedornombre'] && $item['celularproveedor']  && $item['clienteauditorianombre'] && $item['ultima_observacion'])
                                                    @php
                                                        $mensaje = "Buenos días, informarle que su informe del cliente " . $item['clienteauditorianombre']. " tiene la siguiente observación: " . $item['ultima_observacion'];
                                                        $mensajeUrl = urlencode($mensaje);
                                                    @endphp
                                                    <abbr title="ENVIAR OBSERVACIÓN">
                                                    <a class="btn btn-enviarobservacion" 
                                                        href="https://wa.me/{{ $item['celularproveedor'] }}?text={{ $mensajeUrl }}" 
                                                        target="_blank"
                                                        class="btn btn-whatsapp">
                                                        <i class="fas fa-comment"></i>
                                                    </a>
                                                </abbr>
                                                @endif
                                                @endcan

                                                {{-- ULTIMA OBSERVACION --}}
                                                @if ($item['ultima_observacion2'])
                                                <abbr title="ÚLTIMA OBSERVACIÓN">
                                                    <a class="btn btn-observaciones" data-toggle="modal" data-target="#modalobservaciones{{ $loop->index }}"><i class="fas fa-exclamation-triangle"></i></a>
                                                </abbr>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                            @endforeach
                            @endcan

                            @can('admin.informesfinales.verinformestodosproveedores')
                            @foreach ($result as $item)
                                    @if ($item['estado_informefinal'] === 'SOLICITÓ REVISIÓN')
                                    <tr>
                                        {{-- ID DEL CLIENTE --}}
                                        <td>{{ $item['clienteauditoriaid'] }}</td>

                                        {{-- CLIENTE --}}
                                        <td>{{ $item['clienteauditorianombre'] }}</td>

                                        {{-- PROVEEDOR --}}
                                        <td>
                                            @if ($item['proveedornombre'])
                                                {{ $item['proveedornombre'] }}
                                            @else
                                                <button class="btn btn-asignarproveedor"
                                                        data-toggle="modal"
                                                        data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                        data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                        data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                        data-fechabateria="{{ $item['fechabateria'] }}">
                                                    Asignar Proveedor
                                                </button>
                                            @endif
                                        </td>

                                        {{-- CELULAR DE PROVEEDOR --}}
                                        <td hidden>
                                            @if ($item['proveedornombre'])
                                                {{ $item['celularproveedor'] }}
                                            @endif
                                        </td>

                                        {{-- FECHA DE BATERIA --}}
                                        <td>{{ $item['fechabateria'] }}</td>

                                        {{-- SERVICIO TRAMITE --}}
                                        <td>{{ $item['tramite'] }}</td>

                                        {{-- RESULTADOS MEDICOS --}}
                                        <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                            {{ $item['estado'] }}
                                            <abbr title="VER DOCUMENTACIÓN">
                                                <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                            </abbr>
                                        </td>

                                        {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                        <td>
                                            <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                                <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                    <i class="fas fa-address-book"></i>
                                                </a>
                                            </abbr>

                                            @if ($item['historiamedica'])
                                            <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                                <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                    <i class="fas fa-book-medical"></i>
                                                </a>
                                            </abbr>
                                            @endif
                                        </td>

                                        {{-- ESTADO INFORME FINAL --}}
                                        {{-- <td>
                                            @if ($item['estado_informefinal'])
                                                <span class="{{ 
                                                    $item['estado_informefinal'] === 'APROBADO' ? 'text-aprobado' : 
                                                    ($item['estado_informefinal'] === 'EN REVISION' ? 'text-enrevision' : 
                                                    ($item['estado_informefinal'] === 'SOLICITÓ REVISIÓN' ? 'text-solicitorevision' : ''))
                                                }}">
                                                    {{ $item['estado_informefinal'] }}
                                                </span>
                                            @endif
                                        </td> --}}

                                        <td>
                                            <div class="btn-container">
                                                @if ($item['document'])

                                                    {{-- VER INFORME FINAL --}}
                                                    <a href="{{ asset('/informesfinalesclientesauditoria/' . $item['document']) }}" class="btn btn-veracciones" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if ($item['estado_informefinal'] != 'APROBADO')

                                                        {{-- SOLICITAR REVISION --}}
                                                        <abbr title="SOLICITAR REVISIÓN">
                                                            <a class="btn btn-solicitarrevision" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalSolicitarRevision{{ $item['idinformefinal'] }}">
                                                                    <i class="fas fa-edit"></i>
                                                        </a>
                                                        </abbr>
                                                        <div class="modal fade" id="modalSolicitarRevision{{ $item['idinformefinal'] }}" tabindex="-1" role="dialog" aria-labelledby="modalSolicitarRevisionLabel{{ $item['clienteauditoriaid'] }}" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title" id="modalSolicitarRevisionLabel{{ $item['clienteauditoriaid'] }}">SOLICITAR REVISIÓN</h4>

                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    
                                                                    <form action="{{ route('admin.informesfinales.solrevisioninformefinal', ['item' => $item['idinformefinal']]) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                            <div class="form-group">
                                                                                <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                                <label for="observaciones">Observaciones:</label>
                                                                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer justify-content-center">
                                                                            <button type="button" class="btn btn-no" data-dismiss="modal">Cerrar</button>
                                                                            <button type="submit" class="btn btn-si">Enviar Solicitud</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- APROBAR INFORME --}}
                                                        <abbr title="APROBAR INFORME">
                                                            <a class="btn btn-aprobarinforme" 
                                                                    data-toggle="modal" 
                                                                    data-target="#modalAprobarInforme{{ $item['idinformefinal'] }}">
                                                                    <i class="fas fa-check"></i>
                                                        </a>
                                                        </abbr>
                                                        <div class="modal fade" id="modalAprobarInforme{{ $item['idinformefinal'] }}" tabindex="-1" role="dialog" aria-labelledby="modalAprobarInformeLabel{{ $item['clienteauditoriaid'] }}" aria-hidden="true">
                                                            <div class="modal-dialog  modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header text-center">
                                                                        {{-- <h4 class="modal-title" id="modalAprobarInforme{{ $item['clienteitaid'] }}">Aprobar Informe</h4> --}}
                                                                        <h4 class="mb-4" id="modalAprobarInforme{{ $item['clienteauditoriaid'] }}">¿ESTÁ SEGURO DE QUE DESEA APROBAR EL INFORME FINAL?</h4>
                                                                        {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button> --}}
                                                                    </div>
                                                                    
                                                                    <form action="{{ route('admin.informesfinales.aprobarinformefinalfs', ['item' => $item['idinformefinal']]) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="modal-body" style="margin-top: -40px" >
                                                                            <input type="hidden" name="idinformefinal" value="{{ $item['idinformefinal'] }}">
                                                                        </div>
                                                                        <div class="modal-footer justify-content-center">
                                                                            <button type="button" class="btn btn-no" data-dismiss="modal">NO</button>
                                                                            <button type="submit" class="btn btn-si">SI</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                @else
                                                    {{-- SUBIR INFORME FINAL --}}
                                                    @if ($item['estado_informefinal'] != 'APROBADO')
                                                        @php
                                                            $aprobado = $aprobaciones->where('clienteauditoriaid', $item['clienteauditoriaid'])
                                                                                    ->where('clienteauditorianombre', $item['clienteauditorianombre'])
                                                                                    ->where('fechabateria', $item['fechabateria'])
                                                                                    ->first();
                                                        @endphp
                                                        <abbr title="SUBIR INFORME">
                                                            <a class="btn btn-subirinforme {{ $aprobado ? 'btn-upload' : 'btn-disabled' }} {{ $aprobado ? 'text-black' : 'text-secondary' }}"
                                                                    data-toggle="{{ $aprobado ? 'modal' : '' }}"
                                                                    data-target="{{ $aprobado ? '#modalUpload' . $loop->index : '' }}"
                                                                    {{ !$aprobado ? 'disabled' : '' }}>
                                                                    <i class="fas fa-upload "></i>
                                                        </a>
                                                        </abbr>
                                                    @endif
                                                @endif

                                                {{-- ENVIAR OBSERVACION POR WHATSAPP --}}
                                                @can('admin.informesfinales.enviarobservacioninformesfinales')
                                                @if ($item['proveedornombre'] && $item['celularproveedor']  && $item['clienteauditorianombre'] && $item['ultima_observacion'])
                                                    @php
                                                        $mensaje = "Buenos días, informarle que su informe del cliente " . $item['clienteauditorianombre']. " tiene la siguiente observación: " . $item['ultima_observacion'];
                                                        $mensajeUrl = urlencode($mensaje);
                                                    @endphp
                                                    <abbr title="ENVIAR OBSERVACIÓN">
                                                    <a class="btn btn-enviarobservacion" 
                                                        href="https://wa.me/{{ $item['celularproveedor'] }}?text={{ $mensajeUrl }}" 
                                                        target="_blank"
                                                        class="btn btn-whatsapp">
                                                        <i class="fas fa-comment"></i>
                                                    </a>
                                                </abbr>
                                                @endif
                                                @endcan

                                                {{-- ULTIMA OBSERVACION --}}
                                                @if ($item['ultima_observacion2'])
                                                <abbr title="ÚLTIMA OBSERVACIÓN">
                                                    <a class="btn btn-observaciones" data-toggle="modal" data-target="#modalobservaciones{{ $loop->index }}"><i class="fas fa-exclamation-triangle"></i></a>
                                                </abbr>
                                                @endif
                                            </div>
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

            {{-- APROBADOS --}}
            @can('admin.informesfinales.verinformesfinales')
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
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
                                <th>Otros doc.</th>
                                <th>Informe final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @can('admin.informesfinales.soloverinformessegunproveedor')
                            @foreach ($result as $item)
                            @if ($item['estado_informefinal'] === 'APROBADO' && $item['proveedornombre'] === $usuarioAutenticado)
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- PROVEEDOR --}}
                                    <td>
                                        @if ($item['proveedornombre'])
                                            {{ $item['proveedornombre'] }}
                                        @else
                                            <button class="btn btn-asignarproveedor"
                                                    data-toggle="modal"
                                                    data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}">
                                                Asignar Proveedor
                                            </button>
                                        @endif
                                    </td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>

                                    {{-- SERVICIO TRAMITE --}}
                                    <td>{{ $item['tramite'] }}</td>

                                    {{-- RESULTADOS MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>

                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>

                                    {{-- ESTADO Y VER INFORME FINAL --}}
                                    <td>
                                        @if ($item['estado_informefinal'])
                                            <span class="{{ 
                                                $item['estado_informefinal'] === 'APROBADO' ? 'text-aprobado' : 
                                                ($item['estado_informefinal'] === 'EN REVISION' ? 'text-enrevision' : 
                                                ($item['estado_informefinal'] === 'SOLICITÓ REVISIÓN' ? 'text-solicitorevision' : ''))
                                            }}">
                                                {{ $item['estado_informefinal'] }}
                                            </span>
                                        @endif

                                        @if ($item['document'])
                                            <a href="{{ asset('/informesfinalesclientesauditoria/' . $item['clienteauditoriaid'] . '/'. $item['documentfirmado']) }}" class="btn btn-sm btn-veracciones" target="_blank" title="VER INFORME FINAL">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        
                                        @if ($item['documentfirmado'])
                                            <a href="{{ asset('/informesfinalesclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $item['documentfirmado']) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @else
                                            {{-- <a href="" class="btn btn-sm btn-sinregistro disabled" target="_blank" title="VER INFORME MÉDICO FIRMADO" aria-disabled="true">
                                                <i class="fas fa-file"></i>
                                            </a> --}}
                                        @endif

                                        @if ($item['documentword'])
                                            <a href="{{ asset('/informesfinalesclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $item['documentword']) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME FINAL WORD">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @else
                                            {{-- <a href="" class="btn btn-sm btn-sinregistro disabled" target="_blank" title="VER INFORME MÉDICO FIRMADO" aria-disabled="true">
                                                <i class="fas fa-file"></i>
                                            </a> --}}
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
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clienteauditoriaid'] }}</td>
                                    
                                    {{-- CLIENTE --}}
                                    <td>{{ $item['clienteauditorianombre'] }}</td>

                                    {{-- PROVEEDOR --}}
                                    <td>
                                        @if ($item['proveedornombre'])
                                            {{ $item['proveedornombre'] }}
                                        @else
                                            <button class="btn btn-asignarproveedor"
                                                    data-toggle="modal"
                                                    data-target="#modalAsignarProveedor{{ $loop->index }}"
                                                    data-clienteauditorianombre="{{ $item['clienteauditorianombre'] }}"
                                                    data-clienteauditoriaid="{{ $item['clienteauditoriaid'] }}"
                                                    data-fechabateria="{{ $item['fechabateria'] }}">
                                                Asignar Proveedor
                                            </button>
                                        @endif
                                    </td>

                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>

                                    {{-- SERVICIO TRAMITE --}}
                                    <td>{{ $item['tramite'] }}</td>

                                    {{-- RESULTADO MEDICOS --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER DOCUMENTACIÓN">
                                            <a class="btn btn-veracciones" data-toggle="modal" data-target="#modal{{ $loop->index }}"><i class="fas fa-file-medical-alt"></i></a>
                                        </abbr>
                                    </td>

                                    {{-- HISTORIAS MEDICAS Y DOCUMENTACION --}}
                                    <td>
                                        <abbr title="VER DOCUMENTACIÓN" style="display: inline-block;">
                                            <a class="btn btn-requisitosdocumentos" data-toggle="modal" data-target="#modalDocumentacion{{ $loop->index }}">
                                                <i class="fas fa-address-book"></i>
                                            </a>
                                        </abbr>

                                        @if ($item['historiamedica'])
                                        <abbr title="VER HISTORIA MÉDICA" style="display: inline-block;">
                                            <a href="{{ asset('/historiamedicaauditoria/' . $item['clienteauditoriaid'] . '/' . $item['historiamedica']) }}" class="btn btn-verhistoriamedica" target="_blank">
                                                <i class="fas fa-book-medical"></i>
                                            </a>
                                        </abbr>
                                        @endif
                                    </td>

                                    {{-- ESTADO Y VER INFORME FINAL --}}
                                    <td>
                                        @if ($item['estado_informefinal'])
                                            <span class="{{ 
                                                $item['estado_informefinal'] === 'APROBADO' ? 'text-aprobado' : 
                                                ($item['estado_informefinal'] === 'EN REVISION' ? 'text-enrevision' : 
                                                ($item['estado_informefinal'] === 'SOLICITÓ REVISIÓN' ? 'text-solicitorevision' : ''))
                                            }}">
                                                {{ $item['estado_informefinal'] }}
                                            </span>
                                        @endif

                                        @if ($item['document'])
                                            <a href="{{ asset('/informesfinalesclientesauditoria/'. $item['clienteauditoriaid'] . '/'. $item['document']) }}" class="btn btn-sm btn-veracciones" target="_blank" title="VER INFORME FINAL">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif

                                        @if ($item['documentfirmado'])
                                            <a href="{{ asset('/informesfinalesclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $item['documentfirmado']) }}" class="btn btn-sm btn-verinformefirmado" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @else
                                            {{-- <a href="" class="btn btn-sm btn-sinregistro disabled" target="_blank" title="VER INFORME MÉDICO FIRMADO" aria-disabled="true">
                                                <i class="fas fa-file"></i>
                                            </a> --}}
                                        @endif

                                        @if ($item['documentword'])
                                            <a href="{{ asset('/informesfinalesclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $item['documentword']) }}" class="btn btn-sm btn-verinformeword" target="_blank" title="DESCARGAR INFORME FINAL WORD">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @else
                                            {{-- <a href="" class="btn btn-sm btn-sinregistro disabled" target="_blank" title="VER INFORME MÉDICO FIRMADO" aria-disabled="true">
                                                <i class="fas fa-file"></i>
                                            </a> --}}
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

            // Asignar los valores al modal
            var modal2 = $(this).data('target');
            $(modal2).find('#cliente').val(cliente);
            $(modal2).find('#fechabateria').val(fecha);
        });
    });
</script>

@foreach ($result as $item)
    {{-- <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}"><strong>{{ $item['clienteitanombre'] }}</strong> - Cod: {{ $item['fechabateria'] }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr> 
                                <th>Fecha Bateria</th>
                                <th>Acción</th>
                                <th>Proveedor</th>
                                <th>Ver Doc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item['acciones'] as $accion)
                                <tr>
                                    <td>{{ $accion['created_at']->format('Y-m-d') }}</td>
                                    <td>{{ $accion['accion'] }}</td>
                                    <td>{{ $accion['proveedornombre'] }}</td>
                                    <td>
                                        @if ($accion['estado'] === 'COMPLETO' && isset($accion['document']))
                                            <a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['document']->document) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @else
                                        <div class="pendiente">PENDIENTE</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}"><strong>{{ $item['clienteitanombre'] }}</strong> - Cod: {{ $item['fechabateria'] }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr> 
                                <th>Fecha Bateria</th>
                                <th>Acción</th>
                                <th>Proveedor</th>
                                <th>Ver Doc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item['acciones'] as $accion)
                                <tr>
                                    <td>{{ $accion['created_at']->format('Y-m-d') }}</td>
                                    <td>{{ $accion['accion'] }}</td>
                                    <td>{{ $accion['proveedornombre'] }}</td>
                                    <td>
                                        @if ($accion['estado'] === 'COMPLETO' && isset($accion['document']))   
                                            <a href="{{ asset('/documentacionclientesita/' . $item['clienteitaid'] . '/' . $accion['document']->document) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                            <button class="btn btn-verdetalles" data-toggle="modal" data-target="#modalDetalles{{ $loop->parent->index }}-{{ $loop->index }}"><i class="fas fa-info-circle"></i></button>
                                        @else
                                            <div class="pendiente">PENDIENTE</div>
                                        @endif
                                    </td>
                                </tr>
                                <!-- Modal de Detalles -->
                                <div class="modal fade" id="modalDetalles{{ $loop->parent->index }}-{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel{{ $loop->parent->index }}-{{ $loop->index }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="modalDetallesLabel{{ $loop->parent->index }}-{{ $loop->index }}">
                                                    Detalles de la Acción
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Acción:</strong> {{ $accion['accion'] }}</p>
                                                <p><strong>Proveedor:</strong> {{ $accion['proveedornombre'] }}</p>
                                                <!-- Campo para añadir una observación -->
                                                <form action="{{ route('guardarObservacion') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="accion_id" value="{{ $accion['accion'] }}"> <!-- Suponiendo que cada acción tiene un ID único -->
                                                    <div class="form-group">
                                                        <label for="observacion">Agregar Observación</label>
                                                        <textarea class="form-control" id="observacion" name="observacion" rows="3" placeholder="Escribe tu observación aquí..."></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                            </div>
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
    </div> --}}

    {{-- VER RESULTADOS MEDICOS --}}
    <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}"><strong>{{ $item['clienteauditorianombre'] }}</strong> - Cod: {{ $item['fechabateria'] }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr> 
                                <th>Bateria</th>
                                <th>Acción</th>
                                <th>Proveedor</th>
                                <th>Documento</th>
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
                                            <abbr title="VER DOCUMENTO" style="display: inline-block;">
                                                <a href="{{ asset('/documentacionclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['document']->document) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                            </abbr>
                                            <abbr title="SOLICITAR CORRECCIÓN" style="display: inline-block;">
                                                <button class="btn btn-verdetalles" data-toggle="modal" data-target="#modalDetalles{{ $loop->parent->index }}-{{ $loop->index }}"><i class="fas fa-exclamation-triangle"></i></button>
                                            </abbr>
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Ver Doc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $mostrarcnacaseguradoau = !empty($accion['cnacaseguradoau']);
                                $mostrarcnacaseguradopendienteau = $accion['cnacaseguradoau'] === 'PENDIENTE';
                                $mostrarciaseguradoau = !empty($accion['ciaseguradoau']);
                                $mostrarciaseguradopendienteau = $accion['ciaseguradoau'] === 'PENDIENTE';
                            @endphp
                            
                            @if ($mostrarcnacaseguradoau || $mostrarcnacaseguradopendienteau)
                                <tr>
                                    <td>CERTIFICADO NACIMIENTO DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarcnacaseguradopendienteau)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarcnacaseguradoau)
                                            <a href="{{ asset('/requisitosclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['cnacaseguradoau']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if ($mostrarciaseguradoau || $mostrarciaseguradopendienteau)
                                <tr>
                                    <td>CARNET IDENTIDAD DE ASEGURADO</td>
                                    <td>
                                        @if ($mostrarciaseguradopendienteau)
                                            <div class="pendiente">PENDIENTE</div>
                                        @elseif ($mostrarciaseguradoau)
                                            <a href="{{ asset('/requisitosclientesauditoria/' . $item['clienteauditoriaid'] . '/' . $accion['ciaseguradoau']) }}" class="btn btn-verdocumentacion" target="_blank"><i class="fas fa-eye"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            <table class="table">
                                <thead>
                                    <tr> 
                                        <th>Banco</th>
                                        <th>Nro. Póliza General</th>
                                        <th>Póliza General</th>
                                        <th>Declaración Salud</th>
                                        <th>Nro. Póliza Desgravamen</th>
                                        <th>Poliza seguro desgravamen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requisitosClientepolizas as $requisito)
                                    <tr>
                                        <td>{{ $requisito->banco }}</td>
                                        <td>{{ $requisito->nropolizageneral ?? '' }}</td>
                                        <td>
                                            @if ($requisito->polizageneral === 'PENDIENTE')
                                                <div class="pendiente">PENDIENTE</div>
                                            @else
                                                <a href="{{ asset("/requisitosclientesauditoria/{$clienteauditoria->id}/{$requisito->polizageneral}") }}" target="_blank" class="verdoc">VER DOC.</a>
                                            @endif
                                        </td>
                                        <td> 
                                            @if ($requisito->declasalud === 'PENDIENTE')
                                                <div class="pendiente">PENDIENTE</div>
                                            @elseif ($requisito->declasalud === 'NO APLICA')
                                                <div class="noaplica">NO APLICA</div>
                                            @else
                                                <a href="{{ asset("/requisitosclientesauditoria/{$clienteauditoria->id}/{$requisito->declasalud}") }}" target="_blank" class="verdoc">VER DOC.</a>
                                            @endif
                                        </td>
                                        
                                        <td>{{ $requisito->nropolizadesgravamen ?? '' }}</td>
                                        <td>
                                            @if ($requisito->polizasegurodesgravamen === 'PENDIENTE')
                                                <div class="pendiente">PENDIENTE</div>
                                            @else
                                                <a href="{{ asset("/requisitosclientesauditoria/{$clienteauditoria->id}/{$requisito->polizasegurodesgravamen}") }}" target="_blank" class="verdoc">VER DOC.</a> 
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </tbody>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ASIGNAR PROVEEDOR --}}
    {{-- <div class="modal fade" id="modalAsignarProveedor{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabelAsignarProveedor{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabelAsignarProveedor{{ $loop->index }}">ASIGNAR PROVEEDOR</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! Form::model($item, ['route' => ['admin.informesfinales.guardarproveedorinformefinal', $item['clienteauditoriaid']], 'method' => 'POST', 'id' => 'formAprobar{{ $loop->index }}']) !!}
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    <input type="hidden" name="cliente" value="{{ $item['clienteauditorianombre'] }}">
                    <input type="hidden" name="clienteauditoriaid" value="{{ $item['clienteauditoriaid'] }}">
                    <input type="hidden" name="fechabateria" value="{{ $item['fechabateria'] }}">
                    
                    <div class="form-group">
                        {!! Form::label('proveedorasignado_' . $loop->index, 'Proveedor:') !!}
                        {!! Form::select('proveedorasignado', $proveedores->pluck('proveedor', 'id'), null, ['class' => 'form-control proveedor-select', 'id' => 'proveedorasignado_' . $loop->index, 'data-index' => $loop->index, 'placeholder' => '']) !!}
                        @error('proveedorasignado')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        {!! Form::label('celularproveedor_' . $loop->index, 'Celular del proveedor:') !!}
                        {!! Form::text('celularproveedor', null, ['class' => 'form-control', 'id' => 'celularproveedor_' . $loop->index, 'readonly' => true]) !!}
                        @error('celularproveedor')
                            <small class="text-danger fas fa-exclamation-circle">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Solo es necesario añadir el listener para los selects en los modales
                            document.querySelectorAll('.proveedor-select').forEach(function(selectProveedor) {
                                var index = selectProveedor.dataset.index;
                                var celularProveedor = document.getElementById('celularproveedor_' + index);

                                var proveedores = {!! $proveedores->toJson() !!};

                                selectProveedor.addEventListener('change', function() {
                                    var selectedId = parseInt(this.value);
                                    
                                    // Buscar el proveedor seleccionado en el array de proveedores
                                    var proveedorSeleccionado = proveedores.find(function(proveedor) {
                                        return proveedor.id === selectedId;
                                    });
                                    
                                    if (proveedorSeleccionado) {
                                        celularProveedor.value = '591' + proveedorSeleccionado.celular;
                                    } else {
                                        celularProveedor.value = ''; // Limpiar el campo si no se encuentra el proveedor seleccionado
                                    }
                                });
                            });
                        });
                    </script>
                    
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-no" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-si">Asignar</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div> --}}

    {{-- VER OBSERVACIONES --}}
    <div class="modal fade" id="modalobservaciones{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalobservacionesLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalobservacionesLabel{{ $loop->index }}">OBSERVACIONES</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="ultima_observacion2">
                        {{ $item['ultima_observacion2'] }}
                    </span>
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
                    {!! Form::model($item, ['route' => ['admin.informesfinales.guardaraprobacioninformefinalauditoria', $item['clienteauditoriaid']], 'method' => 'POST', 'id' => 'formAprobar{{ $loop->index }}']) !!}
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                        <input type="hidden" name="clienteauditorianombre" value="{{ $item['clienteauditorianombre'] }}">
                        <input type="hidden" name="clienteauditoriaid" value="{{ $item['clienteauditoriaid'] }}">
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
                    {!! Form::model($item, ['route' => ['admin.informesfinales.guardarinformefinalauditoria', $item['clienteauditoriaid']], 'method' => 'POST', 'id' => 'formAprobar{{ $loop->index }}', 'enctype' => 'multipart/form-data']) !!}
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}

                        <input type="hidden" name="clienteauditoriaid" value="{{ $item['clienteauditoriaid'] }}">
                        <div class="form-group">
                            <input type="hidden" name="clienteauditorianombre" value="{{ $item['clienteauditorianombre'] }}">
                            <input type="hidden" name="fechabateria" value="{{ $item['fechabateria'] }}">
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
                            <button type="button" class="btn btn-no" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-si">Subir</button>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endforeach

@stop

@section('js')
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
