@extends('adminlte::page')
    
@section('content_header')
<h1>GESTIÓN DE REGISTROS {{ $fechaActual }}</h1>
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
    {{-- <div class="card-body"> --}}
        {{-- <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarclientesporfecha') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control buscador mr-sm-2" type="date" 
                                max="{{ now()->toDateString() }}" value="{{ old('buscarpor') ?? $fechaActual }}" aria-label="Fecha">
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
                        CLIENTES REGISTRADOS
                        <?php if ($contadorclientes > 0): ?>
                            <span class="circle"><?= $contadorclientes ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab"
                        aria-controls="tab-content-3" aria-selected="false">
                        BATERIAS REGISTRADOS
                        <?php if ($contadorbaterias > 0): ?>
                            <span class="circle"><?= $contadorbaterias ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab"
                        aria-controls="tab-content-2" aria-selected="false">
                        PROGRAMACIONES REGISTRADOS
                        <?php if ($contadorprogramaciones > 0): ?>
                            <span class="circle"><?= $contadorprogramaciones ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="myTabContent">

                {{-- CLIENTES CREADOS --}}
                <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo de Cliente</th>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>CI</th>
                                <th>Fecha de nac.</th>
                                <th>Sucursal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td>CLIENTE ITA</td>
                                    <td>{{ $cliente->id }}</td>
                                    <td>{{ $cliente->nombrecompleto }}</td>
                                    <td>{{ $cliente->ci }}</td>
                                    <td>{{ $cliente->fechanacimiento }}</td>
                                    <td>{{ $cliente->sucursal }}</td>
                                    <td>
                                        <abbr title="Ver Cliente">
                                            <a class="btn btn-sm btn-bateria"
                                                href="{{ route('admin.asociados.verclienteita', $cliente) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($clientes2 as $cliente)
                                <tr>
                                    <td>CLIENTE AUDITORIA</td>
                                    <td>{{ $cliente->id }}</td>
                                    <td>{{ $cliente->nombrecompleto }}</td>
                                    <td>{{ $cliente->ci }}</td>
                                    <td>{{ $cliente->fechanacimiento }}</td>
                                    <td>{{ $cliente->sucursal }}</td>
                                    <td>
                                        <abbr title="Ver Cliente">
                                            <a class="btn btn-sm btn-bateria"
                                                href="{{ route('admin.asociados.verclienteauditoria', $cliente) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($clientes3 as $cliente)
                                <tr>
                                    <td>CLIENTE COMÚN</td>
                                    <td>{{ $cliente->id }}</td>
                                    <td>{{ $cliente->nombrecompleto }}</td>
                                    <td>{{ $cliente->ci }}</td>
                                    <td>{{ $cliente->fechanacimiento }}</td>
                                    <td>{{ $cliente->sucursal }}</td>
                                    <td>
                                        <abbr title="Ver Cliente">
                                            <a class="btn btn-sm btn-bateria"
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
                <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo de Cliente</th>
                                <th>Cliente</th>
                                <th>Tipo de Área</th>
                                <th>Área</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bateriashoyita as $bateria)
                                <tr>
                                    <td>CLIENTE ITA</td>
                                    <td>{{ $bateria->clienteitanombre }}</td>
                                    <td>{{ $bateria->tipoarea }}</td>
                                    <td>{{ $bateria->areanombre }}</td>
                                    <td>{{ $bateria->accionnombre }}</td>
                                </tr>
                            @endforeach
                            @foreach ($bateriashoycomun as $bateria)
                                <tr>
                                    <td>CLIENTE COMÚN</td>
                                    <td>{{ $bateria->clientecomunnombre }}</td>
                                    <td>{{ $bateria->tipoarea }}</td>
                                    <td>{{ $bateria->areanombre }}</td>
                                    <td>{{ $bateria->accionnombre }}</td>
                                </tr>
                            @endforeach
                            @foreach ($bateriashoyauditoria as $bateria)
                                <tr>
                                    <td>CLIENTE AUDITORÍA</td>
                                    <td>{{ $bateria->clienteauditorianombre }}</td>
                                    <td>{{ $bateria->tipoarea }}</td>
                                    <td>{{ $bateria->areanombre }}</td>
                                    <td>{{ $bateria->accionnombre }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $bateriashoyita->appends(['buscarpor' => request('buscarpor')])->links() }}
                </div>

                {{-- PROGRAMACIONES CREADAS --}}
                <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo de Cliente</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Acción</th>
                                <th>Fecha de batería</th>
                                <th>Fecha programada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programacioneshoyita as $programacion)
                                <tr>
                                    <td>CLIENTE ITA</td>
                                    <td>{{ $programacion->clienteitanombre }}</td>
                                    <td>{{ $programacion->proveedornombre }}</td>
                                    <td>{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechabateria }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                </tr>
                            @endforeach
                            @foreach ($programacioneshoycomun as $programacion)
                                <tr>
                                    <td>CLIENTE COMÚN</td>
                                    <td>{{ $programacion->clientecomunnombre }}</td>
                                    <td>{{ $programacion->proveedornombre }}</td>
                                    <td>{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechabateria }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                </tr>
                            @endforeach
                            @foreach ($programacioneshoyauditoria as $programacion)
                                <tr>
                                    <td>CLIENTE AUDITORÍA</td>
                                    <td>{{ $programacion->clienteauditorianombre }}</td>
                                    <td>{{ $programacion->proveedornombre }}</td>
                                    <td>{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechabateria }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $programacioneshoyita->appends(['buscarpor' => request('buscarpor')])->links() }}
                </div>
            </div>
        </div>

        {{-- <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tipo de Cliente</th>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>CI</th>
                    <th>Fecha de nac.</th>
                    <th>Sucursal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    <tr>
                        <td>CLIENTE ITA</td>
                        <td>{{$cliente->id}}</td>
                        <td>{{$cliente->nombrecompleto}}</td>
                        <td>{{$cliente->ci}}</td>
                        <td>{{$cliente->fechanacimiento}}</td>
                        <td>{{$cliente->sucursal}}</td>
                        <td>
                        <abbr title="Ver Cliente">
                            <a class="btn btn-sm btn-bateria" href="{{route('admin.asociados.verclienteita', $cliente)}}">
                                <i class="fas fa-eye"></i>
                            </a>
                        </abbr>
                        </td>
                    </tr>
                @endforeach
                @foreach ($clientes2 as $cliente)
                    <tr>
                        <td>CLIENTE AUDITORIA</td>
                        <td>{{$cliente->id}}</td>
                        <td>{{$cliente->nombrecompleto}}</td>
                        <td>{{$cliente->ci}}</td>
                        <td>{{$cliente->fechanacimiento}}</td>
                        <td>{{$cliente->sucursal}}</td>
                        <td>
                        <abbr title="Ver Cliente">
                            <a class="btn btn-sm btn-bateria" href="{{route('admin.asociados.verclienteauditoria', $cliente)}}">
                                <i class="fas fa-eye"></i>
                            </a>
                        </abbr>
                        </td>
                    </tr>
                @endforeach
                @foreach ($clientes3 as $cliente)
                    <tr>
                        <td>CLIENTE COMÚN</td>
                        <td>{{$cliente->id}}</td>
                        <td>{{$cliente->nombrecompleto}}</td>
                        <td>{{$cliente->ci}}</td>
                        <td>{{$cliente->fechanacimiento}}</td>
                        <td>{{$cliente->sucursal}}</td>
                        <td>
                        <abbr title="Ver Cliente">
                            <a class="btn btn-sm btn-bateria" href="{{route('admin.asociados.verclientecomun', $cliente)}}">
                                <i class="fas fa-eye"></i>
                            </a>
                        </abbr>
                        </td>
                    </tr>
                @endforeach
                
            </tbody>
        </table> --}}
    {{-- </div> --}}
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
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
            width: 30px;
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

        .form-control.buscador {
            background-color: #ffffff;
            color: #000000;
            border: 1px solid #cccccc;
            border-radius: 5px;
            padding: 10px 20px;
        }

        .form-control.buscador:hover {
            background-color: #f0f0f0;
        }


        .btn-buscar:hover {
            background-color: #e6e6e6;
        }

        .nav-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

    .custom-select-wrapper {
        position: relative;
        display: inline-block;
        width: 150px;
    }
    .custom-select-wrapper select {
        width: 100%;
        padding: 6px 26px 6px 10px;
        font-size: 14px;
        border: none;
        border-radius: 3px;
        background-color: #f8f9fa;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        cursor: pointer;
    }
    .custom-select-wrapper select:focus {
        outline: none;
    }
    .custom-select-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        pointer-events: none;
        color: #000000;
    }
    .custom-select-wrapper select {
        background-color:  #eff9df;
        color: #000000;
        border-color: #000000;
        border-radius: 5px;
        padding: 10px 20px;
    }
    .custom-select-wrapper select:hover {
        background-color: #f4e1c6;
        color: #000000;
    }
    .custom-select-wrapper select option {
        background-color: #ffffff;
    }
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .btn-crear {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 10px 20px;
    }
    .btn-crear:hover {
        background-color: #94c93b;
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
    .btn-mostrartodo { 
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-mostrartodo:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
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
    .btn-programar {
        background-color:  #ffffff;
        color: #2136bd;
        border-color: #2136bd;
        border-radius: 5px;
    }
    .btn-programar:hover {
        background-color: #2136bd;
        color: #ffffff;
    }
    .btn-estadoprogramacion {
        background-color:  #ffffff;
        color: #58a6f4;
        border-color: #58a6f4;
        border-radius: 5px;
    }
    .btn-estadoprogramacion:hover {
        background-color: #58a6f4;
        color: #ffffff;
    }
    .btn-subirdocumentacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-subirdocumentacion:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #8721f3;
        border-color: #8721f3;
        border-radius: 5px;
    }
    .btn-verdocumentacion:hover {
        background-color: #8721f3;
        color: #ffffff;
    }
    .btn-formulario {
        background-color:  #ffffff;
        color: #ea3ab8;
        border-color: #ea3ab8;
        border-radius: 5px;
    }
    .btn-formulario:hover {
        background-color: #ea3ab8;
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
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-verdocumentacion:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verimagen {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-verimagen:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-descargarimagen {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-descargarimagen:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-descargardocumentacion {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-descargardocumentacion:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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

        document.addEventListener('DOMContentLoaded', function() {
            const activeTabId = localStorage.getItem('activeTab') || 'tab-1';
            const tabLink = document.querySelector(a#${activeTabId});
            if (tabLink) {
                tabLink.click();
            }
            document.querySelectorAll('#myTabs .nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    const tabId = this.getAttribute('id');
                    localStorage.setItem('activeTab', tabId);
                });
            });
        });

</script>
@endsection