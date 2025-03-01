@extends('adminlte::page')

@section('content_header')

@if($userRole === 'ASOCIADO')
<h1>RESULTADOS MÉDICOS</h1>
@endif

@if($userRole !== 'ASOCIADO')
<h1>RESULTADOS MÉDICOS CLIENTES BANCOS</h1>
@endif
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
                <form id="search-form" action="{{ route('buscarresultadosclientebanco') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input type="text" name="buscarporcliente" class="form-control mr-sm-2" placeholder="Nombre del Cliente">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                    <button id="btn-mostrar-todo" class="btn btn-mostrartodo my-2 my-sm-0 ml-2" type="button">Mostrar Todo</button>
                </form>
            </div>
        </div>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btn-mostrar-todo').addEventListener('click', function() {
                window.location.href = "{{ route('buscarresultadosclientebanco') }}";
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
    
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">

            @if($userRole === 'ASOCIADO')
            <li class="nav-item">
                <a class="nav-link active" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    RESULT. MEDICOS COMPLETOS
                </a>
            </li> 
            @endif
            @if($userRole !== 'ASOCIADO')
            <li class="nav-item">
                <a class="nav-link active" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    100% COMPLETOS
                    <?php if ($completosCount > 0): ?>
                        <span class="circle"><?= $completosCount ?></span>
                    <?php endif; ?>
                </a>
            </li> 
            
            <li class="nav-item">
                <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    INFORME MEDICO PENDIENTE
                    <?php if ($resultadosCount > 0): ?>
                        <span class="circle"><?= $resultadosCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    RESULT. MÉDICOS INCOMPLETOS
                    <?php if ($incompletosCount > 0): ?>
                        <span class="circle"><?= $incompletosCount ?></span>
                    <?php endif; ?>
                </a>
            </li>   
            @endif
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            {{-- 100% COMPLETOS --}}
            <div class="tab-pane fade show active" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 25%;">Cliente</th>
                                <th style="width: 15%;">Banco</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 10%;">Atención</th>
                                <th style="width: 15%;">Res. Médicos</th>
                                <th style="width: 15%;">Doc. Adicional</th>
                                <th style="width: 5%;">Conciliación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                                @if($userRole === 'ASOCIADO')
                                    @if($item['empresaregistro'] === $empresaUsuario)
                                        @if ($item['estado'] === 'COMPLETO' && $item['declaracionmedica'] && $item['fichamedica'] && $item['consentimiento'] && $item['informefinal'])
                                            <tr>
                                                {{-- ID DEL CLIENTE --}}
                                                <td>{{ $item['clientebancoid'] }}</td>

                                                {{-- CLIENTE --}}
                                                <td>
                                                    <a class="btn btn-sm btn-bateria" title="VER CLIENTE" href="{{ route('admin.asociados.verclientebanco', ['clientebanco' => $item['clientebancoid']]) }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    {{ $item['clientebanconombre'] }}
                                                </td>

                                                {{-- EMPRESA --}}
                                                <td>{{ $item['empresaregistro'] }}</td>

                                                {{-- USUARIO REGISTRO --}}
                                                <td>{{ $item['usuarioregistro'] }}</td>
                                                
                                                {{-- CELULAR DE PROVEEDOR --}}
                                                <td hidden>
                                                    @if ($item['proveedornombre'])
                                                        {{ $item['celularproveedor'] }}
                                                    @endif
                                                </td>

                                                {{-- FECHA DE BATERIA --}}
                                                <td>{{ $item['fechabateria'] }}</td>
                                                
                                                {{-- ESTADO DE INFORMES --}}
                                                <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                                    {{ $item['estado'] }}
                                                    <abbr title="VER RESULTADOS MÉDICOS">
                                                        <button class="btn {{ $item['estado'] === 'COMPLETO' ? 'btn-completo' : 'btn-incompleto' }} btn-sm" data-toggle="modal" data-target="#modal{{ $loop->index }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </abbr>
                                                </td>

                                                {{-- DOCUMENTOS ADICIONALES --}}
                                                <td class="{{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'text-completo' : 'text-incompleto' }}">
                                                    {{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'COMPLETO' : 'INCOMPLETO' }}
                                                
                                                    @if (isset($item['fichamedica']) || isset($item['consentimiento']) || isset($item['declaracionmedica']) || isset($item['declaracionmedicafisica']))
                                                        <abbr title="VER OTRO CONTENIDO">
                                                            <button class="btn {{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'btn-completo' : 'btn-incompleto' }} btn-sm" data-toggle="modal" data-target="#nuevoModal{{ $loop->index }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </abbr>
                                                    @endif
                                                </td>

                                                {{-- CONSILIACIÓN --}}
                                                {{-- <td>  
                                                    <a class="btn btn-sm btn-consiliacion" 
                                                    href="{{ route('admin.asociados.generarpdfcotizacionclientebanco', ['clientebanco' => $item['clientebancoid']]) }}"
                                                    onclick="confirmarGeneracionPdf(event)">
                                                    <i class="fas fa-donate"></i>
                                                    </a>
                                                </td> --}}
                                                <td>  
                                                    <a class="btn btn-sm btn-consiliacion" 
                                                    href="{{ route('admin.asociados.generarpdfcotizacionclientebanco', ['clientebanco' => $item['clientebancoid']]) }}"
                                                    target="_blank"
                                                    onclick="confirmarGeneracionPdf(event)">
                                                    <i class="fas fa-donate"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @else
                                    @if ($item['estado'] === 'COMPLETO' && $item['declaracionmedica'] && $item['fichamedica'] && $item['consentimiento'] && $item['informefinal'])
                                        <tr>
                                            {{-- ID DEL CLIENTE --}}
                                            <td>{{ $item['clientebancoid'] }}</td>

                                            {{-- CLIENTE --}}
                                            <td>
                                                <a class="btn btn-sm btn-bateria" title="VER CLIENTE" href="{{ route('admin.asociados.verclientebanco', ['clientebanco' => $item['clientebancoid']]) }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                {{ $item['clientebanconombre'] }}
                                            </td>

                                            {{-- EMPRESA --}}
                                            <td>{{ $item['empresaregistro'] }}</td>

                                            {{-- USUARIO REGISTRO --}}
                                            <td>{{ $item['usuarioregistro'] }}</td>
                                            
                                            {{-- CELULAR DE PROVEEDOR --}}
                                            <td hidden>
                                                @if ($item['proveedornombre'])
                                                    {{ $item['celularproveedor'] }}
                                                @endif
                                            </td>

                                            {{-- FECHA DE BATERIA --}}
                                            <td>{{ $item['fechabateria'] }}</td>
                                            
                                            {{-- ESTADO DE INFORMES --}}
                                            <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                                {{ $item['estado'] }}
                                                <abbr title="VER RESULTADOS MÉDICOS">
                                                    <button class="btn {{ $item['estado'] === 'COMPLETO' ? 'btn-completo' : 'btn-incompleto' }} btn-sm" data-toggle="modal" data-target="#modal{{ $loop->index }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </abbr>
                                            </td>

                                            {{-- DOCUMENTOS ADICIONALES --}}
                                            <td class="{{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'text-completo' : 'text-incompleto' }}">
                                                {{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'COMPLETO' : 'INCOMPLETO' }}
                                            
                                                @if (isset($item['fichamedica']) || isset($item['consentimiento']) || isset($item['declaracionmedica']) || isset($item['declaracionmedicafisica']))
                                                    <abbr title="VER OTRO CONTENIDO">
                                                        <button class="btn {{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'btn-completo' : 'btn-incompleto' }} btn-sm" data-toggle="modal" data-target="#nuevoModal{{ $loop->index }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </abbr>
                                                @endif
                                            </td>

                                            {{-- CONSILIACIÓN --}}
                                            {{-- <td>  
                                                <a class="btn btn-sm btn-consiliacion" 
                                                href="{{ route('admin.asociados.generarpdfcotizacionclientebanco', ['clientebanco' => $item['clientebancoid']]) }}"
                                                onclick="confirmarGeneracionPdf(event)">
                                                <i class="fas fa-donate"></i>
                                                </a>
                                            </td> --}}
                                            <td>  
                                                <a class="btn btn-sm btn-consiliacion" 
                                                href="{{ route('admin.asociados.generarpdfcotizacionclientebanco', ['clientebanco' => $item['clientebancoid']]) }}"
                                                target="_blank"
                                                onclick="confirmarGeneracionPdf(event)">
                                                <i class="fas fa-donate"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($userRole !== 'ASOCIADO')
            {{-- INFORME MEDICO PENDIENTE --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 25%;">Cliente</th>
                                <th style="width: 20%;">Banco</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 10%;">Atención</th>
                                <th style="width: 15%;">Res. Médicos</th>
                                <th style="width: 15%;">Doc. Adicional</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'COMPLETO' && $item['declaracionmedica'] && $item['fichamedica'] && $item['consentimiento'] && !$item['informefinal'] )
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clientebancoid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>
                                        <a class="btn btn-sm btn-bateria" title="VER CLIENTE" href="{{ route('admin.asociados.verclientebanco', ['clientebanco' => $item['clientebancoid']]) }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{ $item['clientebanconombre'] }}
                                    </td>

                                    {{-- EMPRESA --}}
                                    <td>{{ $item['empresaregistro'] }}</td>

                                    {{-- USUARIO REGISTRO --}}
                                    <td>{{ $item['usuarioregistro'] }}</td>
                                    
                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>
                                    
                                    {{-- ESTADO DE INFORMES --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <button class="btn {{ $item['estado'] === 'COMPLETO' ? 'btn-completo' : 'btn-incompleto' }} btn-sm" data-toggle="modal" data-target="#modal{{ $loop->index }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </abbr>
                                    </td>

                                    {{-- DOCUMENTOS ADICIONALES --}}
                                    <td class="{{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'text-completo' : 'text-incompleto' }}">
                                        {{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'COMPLETO' : 'INCOMPLETO' }}
                                    
                                        @if (isset($item['fichamedica']) || isset($item['consentimiento']) || isset($item['declaracionmedica']) || isset($item['declaracionmedicafisica']))
                                            <abbr title="VER OTRO CONTENIDO">
                                                <button class="btn {{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'btn-completo' : 'btn-incompleto' }} btn-sm" data-toggle="modal" data-target="#nuevoModal{{ $loop->index }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </abbr>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- INCOMPLETOS --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 25%;">Cliente</th>
                                <th style="width: 20%;">Banco</th>
                                <th style="width: 10%;">Sucursal</th>
                                <th style="width: 10%;">Atención</th>
                                <th style="width: 15%;">Res. Médicos</th>
                                <th style="width: 15%;">Doc. Adicional</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                            @if ($item['estado'] === 'INCOMPLETO' || !$item['declaracionmedica'] && !$item['fichamedica'] && !$item['consentimiento'])
                                <tr>
                                    {{-- ID DEL CLIENTE --}}
                                    <td>{{ $item['clientebancoid'] }}</td>

                                    {{-- CLIENTE --}}
                                    <td>
                                        <a class="btn btn-sm btn-bateria" title="VER CLIENTE" href="{{ route('admin.asociados.verclientebanco', ['clientebanco' => $item['clientebancoid']]) }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{ $item['clientebanconombre'] }}
                                    </td>

                                    {{-- EMPRESA --}}
                                    <td>{{ $item['empresaregistro'] }}</td>

                                    {{-- USUARIO REGISTRO --}}
                                    <td>{{ $item['usuarioregistro'] }}</td>
                                    
                                    {{-- CELULAR DE PROVEEDOR --}}
                                    <td hidden>
                                        @if ($item['proveedornombre'])
                                            {{ $item['celularproveedor'] }}
                                        @endif
                                    </td>

                                    {{-- FECHA DE BATERIA --}}
                                    <td>{{ $item['fechabateria'] }}</td>
                                    
                                    {{-- ESTADO DE INFORMES --}}
                                    <td class="{{ $item['estado'] === 'COMPLETO' ? 'text-completo' : 'text-incompleto' }}">
                                        {{ $item['estado'] }}
                                        <abbr title="VER RESULTADOS MÉDICOS">
                                            <button class="btn btn-veracciones2 btn-sm" data-toggle="modal" data-target="#modal{{ $loop->index }}">
                                                <i class="fas fa-eye"></i></button>
                                        </abbr>
                                    </td>

                                    {{-- DOCUMENTOS ADICIONALES --}}
                                    <td class="{{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'text-completo' : 'text-incompleto' }}">
                                        {{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'COMPLETO' : 'INCOMPLETO' }}
                                    
                                        @if (isset($item['fichamedica']) || isset($item['consentimiento']) || isset($item['declaracionmedica']) || isset($item['declaracionmedicafisica']))
                                            <abbr title="VER OTRO CONTENIDO">
                                                <button class="btn {{ isset($item['fichamedica']) && isset($item['consentimiento']) && isset($item['declaracionmedica']) && isset($item['declaracionmedicafisica']) ? 'btn-completo' : 'btn-incompleto' }} btn-sm" data-toggle="modal" data-target="#nuevoModal{{ $loop->index }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </abbr>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
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
    {{-- RESULTADOS MEDICOS --}}
    <div class="modal fade" id="modal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $loop->index }}" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}"><strong>{{ $item['clientebanconombre'] }}</strong> - Fecha Bateria: {{ \Carbon\Carbon::parse($item['fechabateria'])->format('Y-m-d') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($item['estado'] === 'COMPLETO' || $item['declaracionmedica'] && $item['fichamedica'] && $item['consentimiento'])
                        <div class="container my-4 p-2 border border-success rounded shadow-sm"  style="background-color: #fff7eb; border-width: 10px;">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center">
                                    <h6 class="text-muted m-0">Informe</h6>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h6 class="text-muted m-0">Estado</h6>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h6 class="text-muted m-0">Acciones</h6>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-md-4 text-center">
                                    <h5 class="m-0"></i> Informe Médico</h5>
                                </div>
                                <div class="col-md-4 text-center">
                                    @if ($item['informefinal'])
                                        <span class="badge badge-success px-3 py-1">Completo</span>
                                    @else
                                        <span class="badge badge-danger px-3 py-1">Pendiente</span>
                                    @endif
                                </div>
                        
                                <div class="col-md-4">
                                    @if ($item['informefinal'])
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ asset('/informesfinalesclientesbanco/' . $item['clientebancoid'] . '/' . $item['informefinal']) }}" class="btn btn-bateria btn-sm" target="_blank">
                                            <i class="fas fa-book-medical"></i> VER INFORME MEDICO
                                        </a>
                                    </div>
                                    @else
                                        <form action="{{ route('admin.informesfinales.guardarinformefinalclientebanco', $item['clientebancoid']) }}" method="POST" enctype="multipart/form-data" class="d-flex justify-content-center align-items-center">
                                            @csrf
                                            <input type="hidden" name="fechabateria" id="fechabateria" value="{{ $item['fechabateria'] }}">
                                            <div class="form-group mb-0" style="flex-grow: 1; max-width: 70%;">
                                                <input type="file" class="form-control form-control-sm" name="document" id="document" accept="application/pdf" required style="height: 35px;">
                                            </div>
                                            <button type="submit" class="btn btn-bateria btn-sm ml-2" style="height: 35px;" title="SUBIR INFORME MEDICO">
                                                <i class="fas fa-upload"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="table-responsive d-block d-md-none">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Estudio / Especialidad</th>
                                    @if($userRole !== 'ASOCIADO')
                                    <th>Proveedor</th>
                                    @endif
                                    <th>Fecha atención</th>
                                    <th>Result. Médico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item['acciones'] as $accion)
                                    <tr>
                                        <td>{{ $accion['accion'] }}</td>
                                        @if($userRole !== 'ASOCIADO')
                                        <td>{{ $accion['proveedornombre'] }}</td>
                                        @endif
                                        <td>{{ $accion['fechaasignada'] }}</td>   
                                        <td>
                                            @if ($accion['estado'] === 'COMPLETO')
                                                {{ $accion['fechadocumento']->format('Y-m-d') }}
                                                
                                                <!-- Contenedor para el botón y los documentos -->
                                                <div class="dropdown-container">
                                                    <button class="btn btn-dropdown" type="button">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @if (isset($accion['document']))
                                                            <a href="{{ asset('/documentacionclientesbanco/' . $item['clientebancoid'] . '/' . $accion['document']->document) }}" class="btn btn-verdocumentacion" target="_blank" title="VER RESULTADO MÉDICO">
                                                                <i class="fas fa-folder-open"></i>
                                                            </a>
                                                        @endif
                                        
                                                        @if (isset($accion['image']) && $accion['image']->image)
                                                            <a href="{{ asset('/documentacionclientesbanco/' . $item['clientebancoid'] . '/' . $accion['image']->image) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 1">
                                                                <i class="fas fa-images"></i>
                                                            </a>
                                                        @endif
                                        
                                                        @if (isset($accion['image2']) && $accion['image2']->image2)
                                                            <a href="{{ asset('/documentacionclientesbanco/' . $item['clientebancoid'] . '/' . $accion['image2']->image2) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 2">
                                                                <i class="far fa-images"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                            @else
                                                <div class="pendiente">PENDIENTE</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-none d-md-block">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Estudio / Especialidad</th>
                                    <th>Proveedor</th>
                                    <th>Fecha atención</th>
                                    <th>Result. Médico</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item['acciones'] as $accion)
                                    <tr>
                                        <td>{{ $accion['accion'] }}</td>
                                        <td>{{ $accion['proveedornombre'] }}</td>
                                        <td>{{ $accion['fechaasignada'] }}</td>   
                                        <td>
                                            @if ($accion['estado'] === 'COMPLETO')
                                                {{ $accion['fechadocumento']->format('Y-m-d') }}
                                                
                                                <!-- Contenedor para el botón y los documentos -->
                                                <div class="dropdown-container">
                                                    <button class="btn btn-dropdown" type="button">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @if (isset($accion['document']))
                                                            <a href="{{ asset('/documentacionclientesbanco/' . $item['clientebancoid'] . '/' . $accion['document']->document) }}" class="btn btn-verdocumentacion" target="_blank" title="VER RESULTADO MÉDICO">
                                                                <i class="fas fa-folder-open"></i>
                                                            </a>
                                                        @endif
                    
                                                        @if (isset($accion['image']) && $accion['image']->image)
                                                            <a href="{{ asset('/documentacionclientesbanco/' . $item['clientebancoid'] . '/' . $accion['image']->image) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 1">
                                                                <i class="fas fa-images"></i>
                                                            </a>
                                                        @endif
                    
                                                        @if (isset($accion['image2']) && $accion['image2']->image2)
                                                            <a href="{{ asset('/documentacionclientesbanco/' . $item['clientebancoid'] . '/' . $accion['image2']->image2) }}" class="btn btn-verdocumentacion" target="_blank" title="VER IMAGEN 2">
                                                                <i class="far fa-images"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                            @else
                                                <div class="pendiente">PENDIENTE</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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

    <!-- DOCUMENTACION ADICIONAL -->
    <div class="modal fade" id="nuevoModal{{ $loop->index }}" tabindex="-1" role="dialog" aria-labelledby="nuevoModalLabel{{ $loop->index }}" aria-hidden="true"> 
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel{{ $loop->index }}">
                        <strong>{{ $item['clientebanconombre'] }}</strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Información del cliente y fecha -->
                    
    
                    <!-- Contenido del modal con tabla -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Estado</th>
                                    <th>Ver doc.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Consentimiento informado -->
                                <tr>
                                    <td>CONSENTIMIENTO INFORMADO</td>
                                    <td>
                                        @if ($item['consentimiento'])
                                            <span class="text-completo">COMPLETO</span>
                                        @else
                                            <span class="text-incompleto">PENDIENTE</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item['consentimiento'])
                                            <abbr title="Ver Ficha Médica">
                                                <a href="{{ asset('/cotizacionesaprobadasbanco/' . $item['clientebancoid'] . '/' . $item['fichamedica']) }}" class="btn btn-completo" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </abbr>
                                        @else
                                            <span class="text-incompleto">PENDIENTE</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Declaración Médica -->
                                <tr>
                                    <td>DECLARACIÓN JURADA DIGITAL</td>
                                    <td>
                                        @if ($item['declaracionmedica'])
                                            <span class="text-completo">COMPLETO</span>
                                        @else
                                            <span class="text-incompleto">PENDIENTE</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item['declaracionmedica'])
                                            <abbr title="Ver Declaración Médica">
                                                <a href="{{ asset('/fichamedicaclientesbanco/' . $item['clientebancoid'] . '/' . $item['declaracionmedica']) }}" class="btn btn-completo" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </abbr>
                                        @else
                                            <span class="text-incompleto">PENDIENTE</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>DECLARACIÓN JURADA FISICO</td>
                                    <td>
                                        @if ($item['declaracionmedicafisica'])
                                            <span class="text-completo">COMPLETO</span>
                                        @else
                                            <span class="text-incompleto">PENDIENTE</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item['declaracionmedicafisica'])
                                            <abbr title="Ver Declaración Médica">
                                                <a href="{{ asset('/fichamedicaclientesbanco/' . $item['clientebancoid'] . '/' . $item['declaracionmedicafisica']) }}" class="btn btn-completo" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </abbr>
                                        @else
                                            <span class="text-incompleto">PENDIENTE</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Ficha Médica -->
                                <tr>
                                    <td>FICHA MÉDICA</td>
                                    <td>
                                        @if ($item['fichamedica'])
                                            <span class="text-completo">COMPLETO</span>
                                        @else
                                            <span class="text-incompleto">PENDIENTE</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item['fichamedica'])
                                            <abbr title="Ver Ficha Médica">
                                                <a href="{{ asset('/fichamedicaclientesbanco/' . $item['clientebancoid'] . '/' . $item['fichamedica']) }}" class="btn btn-completo" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </abbr>
                                        @else
                                            <span class="text-incompleto">PENDIENTE</span>
                                        @endif
                                    </td>
                                </tr>
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
@endforeach

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-bateria {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-bateria:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-consiliacion {
        background-color:  #ffffff;
        color: #993ed2;
        border-color: #993ed2;
        border-radius: 5px;
    }
    .btn-consiliacion:hover {
        background-color: #993ed2;
        color: #ffffff;
    }
    .verdoc {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .verdoc:hover {
        color:#faa625; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    /* Estilo para el contenedor del botón y menú desplegable */
    .dropdown-container {
        position: relative;
        display: inline-block;
    }

    /* Estilo para el botón que abre el menú */
    .btn-dropdown {
    margin-top: -10px;
        color: #94c93b;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 16px;
        display: flex;
    }


    /* Estilo para el menú desplegable */
    .dropdown-menu {
        display: none;
        position: absolute;
        border-radius: 5px;
        z-index: 1;
        top: 100%;
        left: 100%;
        margin-left: 5px; /* Espacio entre el botón y el menú */
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
        margin-top: -38px;
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
        margin-right: 10px; /* Espaciado entre los botones */
        margin-left: 10px;
    }


</style>
<style>
    .btn-verhistoriamedica {
        background-color:  #ffffff;
        color: #c9c971;
        border-color: #c9c971;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-verhistoriamedica:hover {
        background-color: #c9c971;
        color: #ffffff;
        }
    .btn-completo {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 8px;
        }
    .btn-completo:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-incompleto {
        background-color:  #ffffff;
        color: red;
        border-color: red;
        border-radius: 5px;
        padding: 5px 8px;
        }
    .btn-incompleto:hover {
        background-color: red;
        color: #ffffff;
        }
    .btn-noregistrado {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-noregistrado:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .text-completo {
        color: #94c93b;
        font-size: 15px;
        font-weight: 900;
        }
    .text-incompleto {
        color: red;
        font-size: 15px;
        font-weight: 900;
        }
    .text-noregistrado {
        color: red;
        font-size: 15px;
        font-weight: 900;
        }
    .text-notiene {
        color: #faa625;
        font-size: 15px;
        font-weight: 900;
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
    .btn-container {
        display: flex;
        align-items: center;
        gap: 2px;
    }
    .btn-container .btn,
    .btn-container .btn-enviarobservacion {
        justify-content: center;
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
        width: 20px;
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
    .btn-upload {
        background-color: #28a745;
        color: white;
    }
    .btn-disabled {
        background-color: #d3d3d3;
        color: #6c757d;
        cursor: not-allowed;
    }
    .btn-upload:hover {
        background-color: #218838;
    }
    .btn-disabled:hover {
        background-color: #d3d3d3;
    }
    .modal-content {
        border-radius: 10px;
        }
    .modal-header {
        border-bottom: none;
        }
    .modal-footer {
        border-top: none;
        }
    h4 {
        font-weight: bold;
        color: #94c93b;
        }
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .btn-no, .btn-si {
        background-color: #ffffff;
        border-radius: 5px;
        padding: 5px 5px;
        min-width: 50px;
        border: 1px solid;
        }
    .btn-no {
        color: #fd1d1d;
        border-color: #fd1d1d;
        width: 100px;
        }
    .btn-no:hover {
        background-color: #fd1d1d;
        color: #ffffff;
        }
    .btn-si {
        color: #8bc02f;
        border-color: #8bc02f;
        width: 100px;
        }
    .btn-si:hover {
        background-color: #8bc02f;
        color: #ffffff;
        }
    .checkverde {
        color:#94c93b; 
        }
    
    .text-aprobado {
        color: #94c93b;
        font-size: 15px;
        font-weight: 900;
        }
    .text-enrevision {
        color: #41b5eb;
        font-size: 15px;
        font-weight: 900;
        }
    .text-solicitorevision {
        color: #faa625;
        font-size: 15px;
        font-weight: 900;
        }
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-verdocumentacion:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .pendiente {color:#fb2525; 
        font-weight: 900;
        font-size: 15px;
        }
    .btn-veracciones {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-veracciones:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
    .btn-veracciones2 {
        background-color:  #ffffff;
        color: red;
        border-color: red;
        border-radius: 5px;
        
        }
    .btn-veracciones2:hover {
        background-color: red;
        color: #ffffff;
        }
    .btn-enviarobservacion {
        background-color:  #ffffff;
        color: #41b5eb;
        border-color: #41b5eb;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-enviarobservacion:hover {
        background-color: #41b5eb;
        color: #ffffff;
        }
    .btn-observaciones {
        background-color:  #ffffff;
        color: #876f4a;
        border-color: #876f4a;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-observaciones:hover {
        background-color: #876f4a;
        color: #ffffff;
        }
    .btn-solicitarrevision {
        background-color:  #ffffff;
        color: #41b5eb;
        border-color: #41b5eb;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-solicitarrevision:hover {
        background-color: #41b5eb;
        color: #ffffff;
        }
    .btn-aprobarinforme {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-aprobarinforme:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-subirinforme {
        background-color:  #ffffff;
        color: #cf44b8;
        border-color: #cf44b8;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-subirinforme:hover {
        background-color: #cf44b8;
        color: #ffffff;
        }
    .btn-subirinformeinicio {
        background-color:  #ffffff;
        color: #cf44b8;
        border-color: #cf44b8;
        border-radius: 5px;
        padding: 2px 40px;
        }
    .btn-subirinformeinicio:hover {
        background-color: #cf44b8;
        color: #ffffff;
        }
    .btn-asignarproveedor {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 40px;
        }
    .btn-asignarproveedor:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-aprobar {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 40px;
        }
    .btn-aprobar:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-disabled {
        background-color:  #ffffff;
        color: #737373;
        border-color: #737373;
        border-radius: 5px;
        padding: 2px 10px;
        }
    .btn-disabled:hover {
        background-color: #737373;
        color: #ffffff;
        }
    .btn-cerrar {
        background-color: #ffffff;
        color: #fb2525;
        border-color: #fb2525;
        border-radius: 5px;
        padding: 2px 5px;
        }
    .btn-cerrar:hover {
        background-color: #fb2525;
        color: #ffffff;
        }
</style>
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
