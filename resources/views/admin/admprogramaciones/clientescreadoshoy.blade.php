@extends('adminlte::page')
    
@section('content_header')
<h1>GESTIÓN DE REGISTROS {{ $fechaActual }}</h1>
@stop 

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .btn-verinforme {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-verinforme:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verinformefirmado{
        background-color:  #ffffff;
        color: #ac26be;
        border-color: #ac26be;
        border-radius: 5px;
    }
    .btn-verinformefirmado:hover {
        background-color: #ac26be;
        color: #ffffff;
    }
    .btn-verinformeword {
        background-color:  #ffffff;
        color: #3b2fe5;
        border-color: #3b2fe5;
        border-radius: 5px;
    }
    .btn-verinformeword:hover {
        background-color: #3b2fe5;
        color: #ffffff;
    }
    .btn-verimagen {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-verimagen:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-verimagen2 {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-verimagen2:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .truncar2 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100px;
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
        {{-- <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center ml-auto">
                    <form action="{{ route('buscarclientesporfecha') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control buscador mr-sm-2" type="date"
                                max="{{ now()->toDateString() }}" value="{{ old('buscarpor') ?? $fechaActual }}"
                                aria-label="Fecha">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">BUSCAR</button>
                    </form>
                </div>
            </div>
        </nav> --}}
        <nav class="navbar navbar-expand-lg float-right"> 
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center ml-auto">
                    <form action="{{ route('buscarclientesporfecha') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control buscador mr-sm-2" type="date"
                                max="{{ now()->toDateString() }}" value="{{ old('buscarpor') ?? $fechaActual }}"
                                aria-label="Fecha">
                        </div>
                        <div class="flex-grow-1">
                            <input name="query" class="form-control buscador mr-sm-2" type="text"
                                placeholder="ID / Nombre Cliente" value="{{ request('query') }}">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">BUSCAR</button>
                    </form>
                </div>
            </div>
        </nav>


        
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab"
                        aria-controls="tab-content-1" aria-selected="true">
                        CLIENTES
                        <?php if ($contadorclientes > 0): ?>
                            <span class="circle"><?= $contadorclientes ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab"
                        aria-controls="tab-content-3" aria-selected="false">
                        BATERIAS
                        <?php if ($contadorbaterias > 0): ?>
                            <span class="circle"><?= $contadorbaterias ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab"
                        aria-controls="tab-content-2" aria-selected="false">
                        PROGRAMACIONES
                        <?php if ($contadorprogramaciones > 0): ?>
                            <span class="circle"><?= $contadorprogramaciones ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab"
                        aria-controls="tab-content-4" aria-selected="false">
                        INFORMES MÉDICOS
                        <?php if ($contadorinformes > 0): ?>
                            <span class="circle"><?= $contadorinformes ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab"
                        aria-controls="tab-content-5" aria-selected="false">
                        INFORMES FINALES
                        <?php if ($contadorinformesfinales > 0): ?>
                            <span class="circle"><?= $contadorinformesfinales ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" id="tab-6" data-toggle="tab" href="#tab-content-6" role="tab"
                        aria-controls="tab-content-6" aria-selected="false">
                        REQUISITOS
                    </a>
                </li> --}}
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="myTabContent">

                {{-- CLIENTES CREADOS --}}
                <div class="tab-pane fade show active table-responsive" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Tipo_Cli.</th>
                                <th>ID_Cli.</th>
                                <th>Nombre_Cliente</th>
                                <th>CI</th>
                                <th>Fecha_nac.</th>
                                <th>Sucursal</th>
                                <th>Usuario_Registro</th>
                                <th>Fecha_Reg.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td>ITA</td>
                                    <td>{{ $cliente->id }}</td>
                                    <td>{{ $cliente->nombrecompleto }}</td>
                                    <td>{{ $cliente->ci }}</td>
                                    <td>{{ $cliente->fechanacimiento }}</td>
                                    <td>{{ $cliente->sucursal }}</td>
                                    <td>{{ $cliente->usuarioregistro }}</td>
                                    <td>{{ $cliente->created_at }}</td>
                                    <td>
                                        <abbr title="VER CLIENTE">
                                            <a class="btn btn-sm btn-verimagen"
                                                href="{{ route('admin.asociados.verclienteita', $cliente) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($clientes2 as $cliente)
                                <tr>
                                    <td>AUDITORIA</td>
                                    <td>{{ $cliente->id }}</td>
                                    <td>{{ $cliente->nombrecompleto }}</td>
                                    <td>{{ $cliente->ci }}</td>
                                    <td>{{ $cliente->fechanacimiento }}</td>
                                    <td>{{ $cliente->sucursal }}</td>
                                    <td>{{ $cliente->usuarioregistro }}</td>
                                    <td>{{ $cliente->created_at }}</td>
                                    <td>
                                        <abbr title="VER CLIENTE">
                                            <a class="btn btn-sm btn-verimagen"
                                                href="{{ route('admin.asociados.verclienteauditoria', $cliente) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($clientes3 as $cliente)
                                <tr>
                                    <td>COMÚN</td>
                                    <td>{{ $cliente->id }}</td>
                                    <td>{{ $cliente->nombrecompleto }}</td>
                                    <td>{{ $cliente->ci }}</td>
                                    <td>{{ $cliente->fechanacimiento }}</td>
                                    <td>{{ $cliente->sucursal }}</td>
                                    <td>{{ $cliente->usuarioregistro }}</td>
                                    <td>{{ $cliente->created_at }}</td>
                                    <td>
                                        <abbr title="VER CLIENTE">
                                            <a class="btn btn-sm btn-verimagen"
                                                href="{{ route('admin.asociados.verclientecomun', $cliente) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $clientes->appends(['buscarpor' => request('buscarpor')])->links() }}
                </div>

                {{-- BATERIAS CREADAS --}}
                <div class="tab-pane fade table-responsive" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                    <form action="{{ route('eliminar-bateria') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="motivoeliminacion">Motivo de Anulación</label>
                            <input type="text" id="motivoeliminacion" name="motivoanulacion" class="form-control" placeholder="Escribe el motivo..." required>
                        </div>
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID_Reg.</th>
                                    <th>Tipo_Cli.</th>
                                    <th>ID_Cli.</th>
                                    <th>Cliente</th>
                                    <th>Fecha_Bat.</th>
                                    <th>Estudio/Especialidad</th>
                                    <th>Proveedor_Asignado</th>
                                    <th>Usuario_Registro</th>
                                    <th>Selec.<input type="checkbox" id="select-all3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bateriashoyita as $bateria)
                                    <tr>
                                        <td>{{ $bateria->id }}</td>
                                        <td>ITA</td>
                                        <td>{{ $bateria->clienteitaid }}</td>
                                        <td title="{{ $bateria->clienteitanombre }}" class="truncar">{{ $bateria->clienteitanombre }}</td>
                                        <td>{{ $bateria->fechabateria }}</td>
                                        <td title="{{ $bateria->accionnombre }}" class="truncar">{{ $bateria->accionnombre }}</td>
                                        <td title="{{ $bateria->proveedorasignado }}" class="truncar">{{ $bateria->proveedorasignado }}</td>
                                        <td title="{{ $bateria->usuarioregistro }}" class="truncar">{{ $bateria->usuarioregistro }}</td>
                                        <td><input type="checkbox" name="seleccionados3[]" value="{{ $bateria->id }}"></td>
                                    </tr>
                                @endforeach
                                @foreach ($bateriashoycomun as $bateria)
                                    <tr>
                                        <td>{{ $bateria->id }}</td>
                                        <td>COMÚN</td>
                                        <td>{{ $bateria->clientecomunid }}</td>
                                        <td title="{{ $bateria->clientecomunnombre }}" class="truncar">{{ $bateria->clientecomunnombre }}</td>
                                        <td>{{ $bateria->fechabateria }}</td>
                                        <td title="{{ $bateria->accionnombre }}" class="truncar">{{ $bateria->accionnombre }}</td>
                                        <td title="{{ $bateria->proveedorasignado }}" class="truncar">{{ $bateria->proveedorasignado }}</td>
                                        <td title="{{ $bateria->usuarioregistro }}" class="truncar">{{ $bateria->usuarioregistro }}</td>
                                        <td><input type="checkbox" name="seleccionados3[]" value="{{ $bateria->id }}"></td>
                                    </tr>
                                @endforeach
                                @foreach ($bateriashoyauditoria as $bateria)
                                    <tr>
                                        <td>{{ $bateria->id }}</td>
                                        <td>AUDITORÍA</td>
                                        <td>{{ $bateria->clienteauditoriaid }}</td>
                                        <td title="{{ $bateria->clienteauditorianombre }}" class="truncar">{{ $bateria->clienteauditorianombre }}</td>
                                        <td>{{ $bateria->fechabateria }}</td>
                                        <td title="{{ $bateria->accionnombre }}" class="truncar">{{ $bateria->accionnombre }}</td>
                                        <td title="{{ $bateria->proveedorasignado }}" class="truncar">{{ $bateria->proveedorasignado }}</td>
                                        <td title="{{ $bateria->usuarioregistro }}" class="truncar">{{ $bateria->usuarioregistro }}</td>
                                        <td><input type="checkbox" name="seleccionados3[]" value="{{ $bateria->id }}"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="text-right">
                            <button type="submit" class="btn btn-danger">ANULAR</button>
                        </div>
                        
                    </form>
                    
                    <script>
                        document.getElementById('select-all3').addEventListener('click', function() {
                            let checkboxes = document.querySelectorAll('input[name="seleccionados3[]"]');
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                        });
                    </script>

                    {{ $bateriashoyita->appends(['buscarpor' => request('buscarpor')])->links() }}
                </div>

                {{-- PROGRAMACIONES CREADAS --}}
                <div class="tab-pane fade table-responsive" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>ID_Reg.</th>
                                <th>Tipo_Cli.</th>
                                <th>ID_Cli.</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Estudio/Especialidad</th>
                                <th>Fecha_Bat.</th>
                                <th>Fecha_Prog.</th>
                                <th>Usuario_Registro</th>
                                <th>Fecha_Reg.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programacioneshoyita as $programacion)
                                <tr>
                                    <td>{{ $programacion->id }}</td>
                                    <td>ITA</td>
                                    <td>{{ $programacion->clienteitaid }}</td>
                                    <td title="{{ $programacion->clienteitanombre }}" class="truncar">{{ $programacion->clienteitanombre }}</td>
                                    <td title="{{ $programacion->proveedornombre }}" class="truncar">{{ $programacion->proveedornombre }}</td>
                                    <td title="{{ $programacion->accionnombre }}" class="truncar">{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechabateria }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                    <td title="{{ $programacion->usuarioregistro }}" class="truncar">{{ $programacion->usuarioregistro }}</td>
                                    <td>{{ $programacion->created_at }}</td>
                                </tr>
                            @endforeach
                            @foreach ($programacioneshoycomun as $programacion)
                                <tr>
                                    <td>{{ $programacion->id }}</td>
                                    <td>COMÚN</td>
                                    <td>{{ $programacion->clientecomunid }}</td>
                                    <td title="{{ $programacion->clientecomunnombre }}" class="truncar">{{ $programacion->clientecomunnombre }}</td>
                                    <td title="{{ $programacion->proveedornombre }}" class="truncar">{{ $programacion->proveedornombre }}</td>
                                    <td title="{{ $programacion->accionnombre }}" class="truncar">{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechabateria }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                    <td title="{{ $programacion->usuarioregistro }}" class="truncar">{{ $programacion->usuarioregistro }}</td>
                                    <td>{{ $programacion->created_at }}</td>
                                </tr>
                            @endforeach
                            @foreach ($programacioneshoyauditoria as $programacion)
                                <tr>
                                    <td>{{ $programacion->id }}</td>
                                    <td>AUDITORÍA</td>
                                    <td>{{ $programacion->clienteauditoriaid }}</td>
                                    <td title="{{ $programacion->clienteauditorianombre }}" class="truncar">{{ $programacion->clienteauditorianombre }}</td>
                                    <td title="{{ $programacion->proveedornombre }}" class="truncar">{{ $programacion->proveedornombre }}</td>
                                    <td title="{{ $programacion->accionnombre }}" class="truncar">{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechabateria }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                    <td title="{{ $programacion->usuarioregistro }}" class="truncar">{{ $programacion->usuarioregistro }}</td>
                                    <td>{{ $programacion->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $programacioneshoyita->appends(['buscarpor' => request('buscarpor')])->links() }}
                </div>

                {{-- INFORMES REGISTRADOS --}}
                <div class="tab-pane fade table-responsive" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                    <form action="{{ route('eliminar-documentos') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="motivoeliminacion">Motivo de Anulación</label>
                            <input type="text" id="motivoeliminacion" name="motivoanulacion" class="form-control" placeholder="Escribe el motivo..." required>
                        </div>
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID_Reg.</th>
                                    <th>Tipo_Cli.</th>
                                    <th>ID_Cli.</th>
                                    <th>Cliente</th>
                                    <th>Estudio/Especialidad</th>
                                    <th>Fecha_Bat.</th>
                                    <th>Usuario_Registro</th>
                                    <th>Fecha_Reg.</th>
                                    <th>Informes</th>
                                    <th>Selec.<input type="checkbox" id="select-all"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($informeshoyita as $informes)
                                    <tr>
                                        <td>{{ $informes->id }}</td>
                                        <td>ITA</td>
                                        <td>{{ $informes->clienteitaid }}</td>
                                        <td title="{{ $informes->clienteitanombre }}" class="truncar">{{ $informes->clienteitanombre }}</td>
                                        <td title="{{ $informes->accion }}" class="truncar">{{ $informes->accion }}</td>
                                        <td>{{ $informes->fechabateria }}</td>
                                        <td title="{{ $informes->usuarioregistro }}" class="truncar">{{ $informes->usuarioregistro }}</td>
                                        <td>{{ $informes->created_at }}</td>
                                        <td>
                                            @if ($informes->accion === 'HISTORIA MÉDICA')
                                                @if ($informes->document)
                                                <a href="{{ asset('/historiamedica/' . $informes->clienteitaid . '/extracted/' . $informes->document) }}" class="btn btn-verinforme btn-sm" target="_blank" title="VER HISTORIA MEDICA">
                                                    <i class="fas fa-file"></i>
                                                </a>
                                                @endif
                                            @else
                                                @if ($informes->document)
                                                <a href="{{ asset('/documentacionclientesita/' . $informes->clienteitaid . '/' . $informes->document) }}" class="btn btn-verinforme btn-sm" target="_blank" title="VER INFORME">
                                                    <i class="fas fa-file"></i>
                                                </a>
                                                @endif
                                                @if ($informes->documentfirmado)
                                                <a href="{{ asset('/documentacionclientesita/' . $informes->clienteitaid . '/' . $informes->documentfirmado) }}" class="btn btn-verinformefirmado btn-sm" target="_blank" title="VER INFORME FIRMADO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                                @endif
                                                @if ($informes->documentword)
                                                <a href="{{ asset('/documentacionclientesita/' . $informes->clienteitaid . '/' . $informes->documentword) }}" class="btn btn-verinformeword btn-sm" target="_blank" title="VER INFORME WORD">
                                                    <i class="fas fa-file"></i>
                                                </a>
                                                @endif
                                                @if ($informes->image)
                                                <a href="{{ asset('/documentacionclientesita/' . $informes->clienteitaid . '/' . $informes->image) }}" class="btn btn-verimagen btn-sm" target="_blank" title="VER IMAGEN 1">
                                                    <i class="fas fa-image"></i>
                                                </a>
                                                @endif
                                                @if ($informes->image2)
                                                <a href="{{ asset('/documentacionclientesita/' . $informes->clienteitaid . '/' . $informes->image2) }}" class="btn btn-verimagen2 btn-sm" target="_blank" title="VER IMAGEN 2">
                                                    <i class="fas fa-images"></i>
                                                </a>
                                                @endif
                                            @endif
                                        </td>
                                        <td><input type="checkbox" name="seleccionados[]" value="{{ $informes->id }}"></td> 
                                    </tr>
                                @endforeach
                                @foreach ($informeshoyauditoria as $informes)
                                    <tr>
                                        <td>{{ $informes->id }}</td>
                                        <td>AUDITORÍA</td>
                                        <td>{{ $informes->clienteauditoriaid }}</td>
                                        <td title="{{ $informes->clienteauditorianombre }}" class="truncar">{{ $informes->clienteauditorianombre }}</td>
                                        <td title="{{ $informes->accion }}" class="truncar">{{ $informes->accion }}</td>
                                        <td>{{ $informes->fechabateria }}</td>
                                        <td title="{{ $informes->usuarioregistro }}" class="truncar">{{ $informes->usuarioregistro }}</td>
                                        <td>{{ $informes->created_at }}</td>
                                        <td>
                                            @if ($informes->accion === 'HISTORIA MÉDICA')
                                                @if ($informes->document)
                                                <a href="{{ asset('/historiamedicaauditoria/' . $informes->clienteauditoriaid . '/extracted/' . $informes->document) }}" class="btn btn-verinforme btn-sm" target="_blank" title="VER HISTORIA MEDICA">
                                                    <i class="fas fa-file"></i>
                                                </a>
                                                @endif
                                            @else
                                                @if ($informes->document)
                                                <a href="{{ asset('/documentacionclientesauditoria/' . $informes->clienteauditoriaid . '/' . $informes->document) }}" class="btn btn-verinforme btn-sm" target="_blank" title="VER INFORME">
                                                    <i class="fas fa-file"></i>
                                                </a>
                                                @endif
                                                @if ($informes->documentfirmado)
                                                <a href="{{ asset('/documentacionclientesauditoria/' . $informes->clienteauditoriaid . '/' . $informes->documentfirmado) }}" class="btn btn-verinformefirmado btn-sm" target="_blank" title="VER INFORME FIRMADO">
                                                    <i class="fas fa-paste"></i>
                                                </a>
                                                @endif
                                                @if ($informes->documentword)
                                                <a href="{{ asset('/documentacionclientesauditoria/' . $informes->clienteauditoriaid . '/' . $informes->documentword) }}" class="btn btn-verinformeword btn-sm" target="_blank" title="VER INFORME WORD">
                                                    <i class="fas fa-file"></i>
                                                </a>
                                                @endif
                                                @if ($informes->image)
                                                <a href="{{ asset('/documentacionclientesauditoria/' . $informes->clienteauditoriaid . '/' . $informes->image) }}" class="btn btn-verimagen btn-sm" target="_blank" title="VER IMAGEN 1">
                                                    <i class="fas fa-image"></i>
                                                </a>
                                                @endif
                                                @if ($informes->image2)
                                                <a href="{{ asset('/documentacionclientesauditoria/' . $informes->clienteauditoriaid . '/' . $informes->image2) }}" class="btn btn-verimagen2 btn-sm" target="_blank" title="VER IMAGEN 2">
                                                    <i class="fas fa-images"></i>
                                                </a>
                                                @endif
                                            @endif
                                        </td>
                                        <td><input type="checkbox" name="seleccionados[]" value="{{ $informes->id }}"></td> <!-- Checkbox para cada registro -->
                                    </tr>
                                @endforeach
                                @foreach ($informeshoycomun as $informes)
                                    <tr>
                                        <td>{{ $informes->id }}</td>
                                        <td>COMÚN</td>
                                        <td>{{ $informes->clientecomunid }}</td>
                                        <td title="{{ $informes->clientecomunnombre }}" class="truncar">{{ $informes->clientecomunnombre }}</td>
                                        <td title="{{ $informes->accion }}" class="truncar">{{ $informes->accion }}</td>
                                        <td>{{ $informes->fechabateria }}</td>
                                        <td title="{{ $informes->usuarioregistro }}" class="truncar">{{ $informes->usuarioregistro }}</td>
                                        <td>{{ $informes->created_at }}</td>
                                        <td>
                                            @if ($informes->document)
                                            <a href="{{ asset('/documentacionclientescomun/' . $informes->clientecomunid . '/' . $informes->document) }}" class="btn btn-verinforme btn-sm" target="_blank" title="VER INFORME">
                                                <i class="fas fa-file"></i>
                                            </a>
                                            @endif
                                            @if ($informes->documentfirmado)
                                            <a href="{{ asset('/documentacionclientescomun/' . $informes->clientecomunid . '/' . $informes->documentfirmado) }}" class="btn btn-verinformefirmado btn-sm" target="_blank" title="VER INFORME FIRMADO">
                                                <i class="fas fa-paste"></i>
                                            </a>
                                            @endif
                                            @if ($informes->documentword)
                                            <a href="{{ asset('/documentacionclientescomun/' . $informes->clientecomunid . '/' . $informes->documentword) }}" class="btn btn-verinformeword btn-sm" target="_blank" title="VER INFORME WORD">
                                                <i class="fas fa-file"></i>
                                            </a>
                                            @endif
                                            @if ($informes->image)
                                            <a href="{{ asset('/documentacionclientescomun/' . $informes->clientecomunid . '/' . $informes->image) }}" class="btn btn-verimagen btn-sm" target="_blank" title="VER IMAGEN 1">
                                                <i class="fas fa-image"></i>
                                            </a>
                                            @endif
                                            @if ($informes->image2)
                                            <a href="{{ asset('/documentacionclientescomun/' . $informes->clientecomunid . '/' . $informes->image2) }}" class="btn btn-verimagen2 btn-sm" target="_blank" title="VER IMAGEN 2">
                                                <i class="fas fa-images"></i>
                                            </a>
                                            @endif
                                        </td>
                                        <td><input type="checkbox" name="seleccionados[]" value="{{ $informes->id }}"></td> <!-- Checkbox para cada registro -->
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-right">
                            <button type="submit" class="btn btn-danger">ANULAR</button>
                        </div>
                        
                    </form>
                    
                    <script>
                        document.getElementById('select-all').addEventListener('click', function() {
                            let checkboxes = document.querySelectorAll('input[name="seleccionados[]"]');
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                        });
                    </script>

                    {{ $programacioneshoyita->appends(['buscarpor' => request('buscarpor')])->links() }}
                </div>

                {{-- INFORMES FINALES REGISTRADOS --}}
                <div class="tab-pane fade table-responsive" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                    <form action="{{ route('eliminar-informesfinal') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="motivoeliminacion">Motivo de Anulación</label>
                            <input type="text" id="motivoeliminacion" name="motivoanulacion" class="form-control" placeholder="Escribe el motivo..." required>
                        </div>
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID_Reg.</th>
                                    <th>Tipo_Cli.</th>
                                    <th>ID_Cli.</th>
                                    <th>Cliente</th>
                                    <th>Acción</th>
                                    <th>Fecha_Bat.</th>
                                    <th>Usuario_Registro</th>
                                    <th>Fecha_Reg.</th>
                                    <th>Informes</th>
                                    <th>Selec.<input type="checkbox" id="select-all2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($informesfinaleshoyita as $informesfinal)
                                    <tr>
                                        <td>{{ $informesfinal->id }}</td>
                                        <td>ITA</td>
                                        <td>{{ $informesfinal->clienteitaid }}</td>
                                        <td title="{{ $informesfinal->clienteitanombre }}" class="truncar">{{ $informesfinal->clienteitanombre }}</td>
                                        <td>INFORME FINAL</td>
                                        <td>{{ $informesfinal->fechabateria }}</td>
                                        <td title="{{ $informesfinal->usuarioregistro }}" class="truncar">{{ $informesfinal->usuarioregistro }}</td>
                                        <td>{{ $informesfinal->created_at }}</td>
                                        <td>
                                            @if ($informesfinal->document)
                                            <a href="{{ asset('/informesfinalesclientesita/' . $informesfinal->clienteitaid . '/' . $informesfinal->document) }}" class="btn btn-verinforme btn-sm" target="_blank" title="VER INFORME FINAL">
                                                <i class="fas fa-file"></i>
                                            </a>
                                            @endif
                                            @if ($informesfinal->documentfirmado)
                                            <a href="{{ asset('/informesfinalesclientesita/' . $informesfinal->clienteitaid . '/' . $informesfinal->documentfirmado) }}" class="btn btn-verinformefirmado btn-sm" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                <i class="fas fa-paste"></i>
                                            </a>
                                            @endif
                                            @if ($informesfinal->documentword)
                                            <a href="{{ asset('/informesfinalesclientesita/' . $informesfinal->clienteitaid . '/' . $informesfinal->documentword) }}" class="btn btn-verinformeword btn-sm" target="_blank" title="VER INFORME FINAL WORD">
                                                <i class="fas fa-paste"></i>
                                            </a>
                                            @endif
                                        </td>
                                        <td><input type="checkbox" name="seleccionados2[]" value="{{ $informesfinal->id }}"></td> <!-- Checkbox para cada registro -->
                                    </tr>
                                @endforeach
                                @foreach ($informesfinaleshoyauditoria as $informesfinal)
                                    <tr>
                                        <td>{{ $informesfinal->id }}</td>
                                        <td>AUDITORÍA</td>
                                        <td>{{ $informesfinal->clienteauditoriaid }}</td>
                                        <td title="{{ $informesfinal->clienteauditorianombre }}" class="truncar">{{ $informesfinal->clienteauditorianombre }}</td>
                                        <td>INFORME FINAL</td>
                                        <td>{{ $informesfinal->fechabateria }}</td>
                                        <td title="{{ $informesfinal->usuarioregistro }}" class="truncar">{{ $informesfinal->usuarioregistro }}</td>
                                        <td>{{ $informesfinal->created_at }}</td>
                                        <td>
                                            @if ($informesfinal->document)
                                            <a href="{{ asset('/informesfinalesclientesauditoria/' . $informesfinal->clienteauditoriaid . '/' . $informesfinal->document) }}" class="btn btn-verinforme btn-sm" target="_blank" title="VER INFORME FINAL">
                                                <i class="fas fa-file"></i>
                                            </a>
                                            @endif
                                            @if ($informesfinal->documentfirmado)
                                            <a href="{{ asset('/informesfinalesclientesauditoria/' . $informesfinal->clienteauditoriaid . '/' . $informesfinal->documentfirmado) }}" class="btn btn-verinformefirmado btn-sm" target="_blank" title="VER INFORME FINAL FIRMADO">
                                                <i class="fas fa-paste"></i>
                                            </a>
                                            @endif
                                            @if ($informesfinal->documentword)
                                            <a href="{{ asset('/informesfinalesclientesauditoria/' . $informesfinal->clienteauditoriaid . '/' . $informesfinal->documentword) }}" class="btn btn-verinformeword btn-sm" target="_blank" title="VER INFORME FINAL WORD">
                                                <i class="fas fa-paste"></i>
                                            </a>
                                            @endif
                                        </td>
                                        <td><input type="checkbox" name="seleccionados2[]" value="{{ $informesfinal->id }}"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-right">
                            <button type="submit" class="btn btn-danger">ANULAR</button>
                        </div>
                        
                    </form>
                    
                    <script>
                        document.getElementById('select-all2').addEventListener('click', function() {
                            let checkboxes = document.querySelectorAll('input[name="seleccionados2[]"]');
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                        });
                    </script>
                    {{ $programacioneshoyita->appends(['buscarpor' => request('buscarpor')])->links() }}
                </div>
                
                {{-- REQUISITOS REGISTRADOS --}}
                {{-- <div class="tab-pane fade table-responsive" id="tab-content-6" role="tabpanel" aria-labelledby="tab-6">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID_Reg.</th>
                                    <th>Tipo_Cli.</th>
                                    <th>ID_Cli.</th>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Requisitos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requisitoshoyita as $requisitos)
                                    <tr>
                                        <td>{{ $requisitos->id }}</td>
                                        <td>ITA</td>
                                        <td>{{ $requisitos->clienteitaid }}</td>
                                        <td title="{{ $requisitos->clienteitanombre }}" class="truncar">{{ $requisitos->clienteitanombre }}</td>
                                        <td>{{ $requisitos->servicio }}</td>
                                        <td>
                                            <a class="btn btn-verimagen btn-sm" data-toggle="modal" data-target="#requisitosModal{{ $requisitos->id }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                @foreach ($requisitoshoyauditoria as $requisitos)
                                    <tr>
                                        <td>{{ $requisitos->id }}</td>
                                        <td>AUDITORÍA</td>
                                        <td>{{ $requisitos->clienteauditoriaid }}</td>
                                        <td title="{{ $requisitos->clienteauditorianombre }}" class="truncar">{{ $requisitos->clienteauditorianombre }}</td>
                                        <td>{{ $requisitos->servicio }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-right">
                            <button type="submit" class="btn btn-danger">ANULAR</button>
                        </div>

                    <script>
                        document.getElementById('select-all5').addEventListener('click', function() {
                            let checkboxes = document.querySelectorAll('input[name="seleccionados5[]"]');
                            checkboxes.forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                        });
                    </script>
                </div> --}}
            </div>
        </div>
    </div>
    {{-- @foreach ($requisitoshoyita as $requisitos)
            <div class="modal fade" id="requisitosModal{{ $requisitos->id }}" tabindex="-1" aria-labelledby="requisitosModalLabel{{ $requisitos->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="requisitosModalLabel{{ $requisitos->id }}">REQUISITOS DE {{ $requisitos->clienteitanombre }}</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('anular-pendiente-requisitos') }}" method="POST">
                            @csrf
                            <input type="hidden" name="idrequisito" value="{{ $requisitos->id }}">
                            <div class="modal-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Requisito</th>
                                            <th>Estado</th>
                                            <th>Selec.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!is_null($requisitos->poder))
                                            <tr>
                                                @if (is_null($requisitos->numeropoder))
                                                    <td>PODER:</td>
                                                @endif
                                                @if (!is_null($requisitos->numeropoder))
                                                    <td>PODER: {{ $requisitos->numeropoder }}</td>
                                                @endif
                                                <td>
                                                    @if ($requisitos->poder)
                                                        <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->poder}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                        <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td><input type="checkbox" name="seleccionados5[]" value="poder"></td>
                                            </tr>
                                        @endif
        
                                        @if (!is_null($requisitos->avcci))
                                            <tr>
                                                <td>AVC/CARNET ASEGURADO</td>
                                                <td>
                                                    @if ($requisitos->avcci)
                                                        <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->avcci}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                        <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td><input type="checkbox" name="seleccionados5[]" value="avcci"></td>
                                            </tr>
                                        @endif
        
                                        @if (!is_null($requisitos->cnacasegurado))
                                            <tr>
                                                <td>CERTIFICADO NACIMIENTO ASEGURADO</td>
                                                <td>
                                                    @if ($requisitos->cnacasegurado)
                                                        <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->cnacasegurado}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                        <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td><input type="checkbox" name="seleccionados5[]" value="cnacasegurado"></td>
                                            </tr>
                                        @endif
                                        
                                        @if (!is_null($requisitos->ciasegurado))
                                            <tr>
                                                <td>CARNET IDENTIDAD ASEGURADO</td>
                                                <td>
                                                    @if ($requisitos->ciasegurado)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->ciasegurado}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td><input type="checkbox" name="seleccionados5[]" value="ciasegurado"></td>
                                            </tr>
                                        @endif
                                
                                        @if (!is_null($requisitos->cmatrimonio))
                                            <tr>
                                                <td>CERTIFICADO DE MATRIMONIO</td>
                                                <td>
                                                    @if ($requisitos->cmatrimonio)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->cmatrimonio}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif
                                
                                        @if (!is_null($requisitos->cnacconyuge))
                                            <tr>
                                                <td>CERTIFICADO NACIMIENTO CONYUGE</td>
                                                <td>
                                                    @if ($requisitos->cnacconyuge)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->cnacconyuge}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif
                                
                                        @if (!is_null($requisitos->ciconyuge))
                                            <tr>
                                                <td>CARNET IDENTIDAD CONYUGE</td>
                                                <td>
                                                    @if ($requisitos->ciconyuge)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->ciconyuge}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif
                                
                                        @if (!is_null($requisitos->cunionlibre))
                                            <tr>
                                                <td>CERTIFICADO DE UNIÓN LIBRE</td>
                                                <td>
                                                    @if ($requisitos->cunionlibre)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->cunionlibre}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->cnacimientounionlibre))
                                            <tr>
                                                <td>CERTIFICADO DE NACIMIENTO DE UNIÓN LIBRE</td>
                                                <td>
                                                    @if ($requisitos->cnacimientounionlibre)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->cnacimientounionlibre}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->ciunionlibre))
                                            <tr>
                                                <td>CARNET IDENTIDAD DE UNIÓN LIBRE</td>
                                                <td>
                                                    @if ($requisitos->ciunionlibre)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->ciunionlibre}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->cdivorcio))
                                            <tr>
                                                <td>CERTIFICADO DE DIVORCIO</td>
                                                <td>
                                                    @if ($requisitos->cdivorcio)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->cdivorcio}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif
                                        
                                        @if (!is_null($requisitos->cdefuncion))
                                            <tr>
                                                <td>CERTIFICADO DE DEFUNCIÓN</td>
                                                <td>
                                                    @if ($requisitos->cdefuncion)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->cdefuncion}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->cnacjihos))
                                            <tr>
                                                <td>CERTIFICADO NACIMIENTO HIJOS &lt; 25</td>
                                                <td>
                                                    @if ($requisitos->cnacjihos)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->cnacjihos}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->cihijos))
                                            <tr>
                                                <td>CARNET IDENTIDAD HIJOS &lt; 25</td>
                                                <td>
                                                    @if ($requisitos->cihijos)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->cihijos}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->denfaccidente))
                                            <tr>
                                                <td>DENUNCIA ENFERMEDAD ACCIDENTE</td>
                                                <td>
                                                    @if ($requisitos->denfaccidente)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->denfaccidente}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->crodomicilio))
                                            <tr>
                                                <td>CROQUIS DE DOMICILIO</td>
                                                <td>
                                                    @if ($requisitos->crodomicilio)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->crodomicilio}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->contrato))
                                            <tr>
                                                <td>CONTRATO</td>
                                                <td>
                                                    @if ($requisitos->contrato)
                                                        <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->contrato}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->recordservicios))
                                            <tr>
                                                <td>RECORD SERVICIOS</td>
                                                <td>
                                                    @if ($requisitos->recordservicios)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->recordservicios}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->ctrabajo))
                                            <tr>
                                                <td>CERTIFICADO DE TRABAJO</td>
                                                <td>
                                                    @if ($requisitos->ctrabajo)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->ctrabajo}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->boletapago))
                                            <tr>
                                                <td>BOLETA DE PAGO</td>
                                                <td>
                                                    @if ($requisitos->boletapago)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->boletapago}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->egestora))
                                            <tr>
                                                <td>EXTRACTO DE GESTORA</td>
                                                <td>
                                                    @if ($requisitos->egestora)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->egestora}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->actdatos))
                                            <tr>
                                                <td>ACTUALIZACIÓN DE DATOS</td>
                                                <td>
                                                    @if ($requisitos->actdatos)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->actdatos}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif

                                        @if (!is_null($requisitos->resolinvhijos))
                                            <tr>
                                                <td>RESOL. INVAL. HIJOS < 25</td>
                                                <td>
                                                    @if ($requisitos->resolinvhijos)
                                                    <a href="{{ asset("/requisitosclientesita/{$requisitos->clienteitaid}/{$requisitos->resolinvhijos}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                    @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="seleccionados5[]" value="{{ $requisitos->id }}">
                                                </td>
                                            </tr>
                                        @endif
                                        
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                <button type="submit" name="accion" value="anular" class="btn btn-danger">ANULAR</button>
                                <button type="submit" name="accion" value="rechazar" class="btn btn-success">RECHAZAR</button>
                            </div>                            
                        </form>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
    @endforeach --}}
@stop

@section('js')
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script>
$('.dropify').dropify();
</script>
    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El perfil se eliminó con éxito',
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