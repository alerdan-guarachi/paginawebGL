@extends('adminlte::page')
    
@section('content_header')
<h1>GESTIÓN DE INFORMES</h1>
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
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('admin.admprogramaciones.documentacionpendiente', $asociado) }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Proveedor" aria-label="Search">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>Buscar</button>
                    </form>
                </div>
            </div>
        </nav> --}}
        <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center ml-auto">
                    <form action="{{ route('admin.admprogramaciones.documentacionpendiente', $asociado) }}" method="get"
                        class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Proveedor / Cliente"
                                aria-label="Search">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>Buscar</button>
                    </form>
                </div>
            </div>
        </nav>
        {{-- <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Proveedor</th>
                    <th>Acción</th>
                    <th>Fecha de atención</th>
                    <th>Fecha Batería</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clientes as $cliente)
                    <tr>
                        <td>{{$cliente->clienteitaid}}</td>
                        <td>{{$cliente->clienteitanombre}}</td>
                        <td>{{$cliente->proveedornombre}}</td>
                        <td>{{$cliente->accionnombre}}</td>
                        <td>{{$cliente->fechaasignada}}</td>
                        <td>{{$cliente->fechabateria}}</td>
                    </tr>
                @endforeach
                @foreach ($clientes2 as $cliente)
                    <tr>
                        <td>{{$cliente->clientecomunid}}</td>
                        <td>{{$cliente->clientecomunnombre}}</td>
                        <td>{{$cliente->proveedornombre}}</td>
                        <td>{{$cliente->accionnombre}}</td>
                        <td>{{$cliente->fechaasignada}}</td>
                        <td>{{$cliente->fechabateria}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table> --}}
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab"
                        aria-controls="tab-content-1" aria-selected="true">
                        INFORMES PENDIENTES
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab"
                        aria-controls="tab-content-2" aria-selected="false">
                        INFORMES COMPLETOS
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">

                {{-- INFORMES PENDIENTES --}}
                <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo Cliente</th>
                                <th>ID Cliente</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Acción</th>
                                <th>Fecha de atención</th>
                                <th>Fecha Batería</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td>CLIENTE ITA</td>
                                    <td>{{ $cliente->clienteitaid }}</td>
                                    <td>{{ $cliente->clienteitanombre }}</td>
                                    <td>{{ $cliente->proveedornombre }}</td>
                                    <td>{{ $cliente->accionnombre }}</td>
                                    <td>{{ $cliente->fechaasignada }}</td>
                                    <td>{{ $cliente->fechabateria }}</td>
                                </tr>
                            @endforeach
                            @foreach ($clientes2 as $cliente)
                                <tr>
                                    <td>CLIENTE COMUN</td>
                                    <td>{{ $cliente->clientecomunid }}</td>
                                    <td>{{ $cliente->clientecomunnombre }}</td>
                                    <td>{{ $cliente->proveedornombre }}</td>
                                    <td>{{ $cliente->accionnombre }}</td>
                                    <td>{{ $cliente->fechaasignada }}</td>
                                    <td>{{ $cliente->fechabateria }}</td>
                                </tr>
                            @endforeach
                            @foreach ($clientes3 as $cliente)
                                <tr>
                                    <td>CLIENTE AUDITORIA</td>
                                    <td>{{ $cliente->clienteauditoriaid }}</td>
                                    <td>{{ $cliente->clienteauditorianombre }}</td>
                                    <td>{{ $cliente->proveedornombre }}</td>
                                    <td>{{ $cliente->accionnombre }}</td>
                                    <td>{{ $cliente->fechaasignada }}</td>
                                    <td>{{ $cliente->fechabateria }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- {{ $clientes->appends(['buscarpor' => request('buscarpor')])->links() }} --}}
                </div>

                {{-- INFORMES COMPLETOS --}}
                <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo Cliente</th>
                                <th>ID Cliente</th>
                                <th>Cliente</th>
                                <th>Fecha de Batería</th>
                                <th>Acción</th>
                                <th>Fecha de atención</th>
                                <th>Fecha y hora de subida</th>
                                <th>Documento</th>
                                <th>Imagen 1</th>
                                <th>Imagen 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documentacion as $doc)
                                <tr>
                                    <td>CLIENTE ITA</td>
                                    <td>{{ $doc->clienteitaid }}</td>
                                    <td>{{ $doc->clienteitanombre }}</td>
                                    <td>{{ $doc->fechabateria }}</td>
                                    <td>{{ $doc->accion }}</td>
                                    <td>{{ $doc->estadoprogramacionsubcliente->fechaatencionprogramacion ?? '-' }}</td>
                                    <td>{{ date('Y-m-d', strtotime($doc->created_at)) }} -
                                        {{ date('H:i:s', strtotime($doc->created_at)) }}</td>
                                    <td>
                                        @if ($doc->document)
                                            <a href="{{ asset('/documentacionclientesita/' . $doc->clienteitaid . '/' . $doc->document) }}"
                                                class="btn btn-verdocumentacion" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ asset('/documentacionclientesita/' . $doc->clienteitaid . '/' . $doc->document) }}"
                                                class="btn btn-descargardocumentacion" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($doc->image)
                                            <a href="{{ asset('/documentacionclientesita/' . $doc->clienteitaid . '/' . $doc->image) }}"
                                                class="btn btn-verimagen" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ asset('/documentacionclientesita/' . $doc->clienteitaid . '/' . $doc->image) }}"
                                                class="btn btn-descargarimagen" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($doc->image2)
                                            <a href="{{ asset('/documentacionclientesita/' . $doc->clienteitaid . '/' . $doc->image2) }}"
                                                class="btn btn-verimagen" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ asset('/documentacionclientesita/' . $doc->clienteitaid . '/' . $doc->image2) }}"
                                                class="btn btn-descargarimagen" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @foreach ($documentacionauditoria as $docaudi)
                                <tr>
                                    <td>CLIENTE AUDITORIA</td>
                                    <td>{{ $docaudi->clienteauditoriaid }}</td>
                                    <td>{{ $docaudi->clienteauditorianombre }}</td>
                                    <td>{{ $docaudi->fechabateria }}</td>
                                    <td>{{ $docaudi->accion }}</td>
                                    <td>{{ $docaudi->estadoprogramacionsubcliente->fechaatencionprogramacion ?? '-' }}</td>
                                    <td>{{ date('Y-m-d', strtotime($docaudi->created_at)) }} -
                                        {{ date('H:i:s', strtotime($docaudi->created_at)) }}</td>
                                    <td>
                                        @if ($docaudi->document)
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $docaudi->clienteauditoriaid . '/' . $docaudi->document) }}"
                                                class="btn btn-verdocumentacion" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $docaudi->clienteauditoriaid . '/' . $docaudi->document) }}"
                                                class="btn btn-descargardocumentacion" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($docaudi->image)
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $docaudi->clienteauditoriaid . '/' . $docaudi->image) }}"
                                                class="btn btn-verimagen" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $docaudi->clienteauditoriaid . '/' . $docaudi->image) }}"
                                                class="btn btn-descargarimagen" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($docaudi->image2)
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $docaudi->clienteauditoriaid . '/' . $docaudi->image2) }}"
                                                class="btn btn-verimagen" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ asset('/documentacionclientesauditoria/' . $docaudi->clienteauditoriaid . '/' . $docaudi->image2) }}"
                                                class="btn btn-descargarimagen" download>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $documentacion->appends(['buscarpor' => request('buscarpor')])->links() }}
                </div>
            </div>
        </div>
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

        .btn-verdocumentacion {
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
        }

        .btn-verdocumentacion:hover {
            background-color: #faa625;
            color: #ffffff;
        }

        .btn-verimagen {
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
        }

        .btn-verimagen:hover {
            background-color: #faa625;
            color: #ffffff;
        }

        .btn-descargarimagen {
            background-color: #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
        }

        .btn-descargarimagen:hover {
            background-color: #94c93b;
            color: #ffffff;
        }

        .btn-descargardocumentacion {
            background-color: #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
        }

        .btn-descargardocumentacion:hover {
            background-color: #94c93b;
            color: #ffffff;
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
</script>
@endsection