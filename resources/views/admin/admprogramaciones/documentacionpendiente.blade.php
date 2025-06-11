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
    <nav class="navbar navbar-expand-lg float-right">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center ml-auto">
                <form action="{{ route('admin.admprogramaciones.documentacionpendiente', $asociado) }}" method="get"
                    class="form-inline">
                    <div class="flex-grow-1">
                        <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="NOMBRE DEL PROVEEDOR"
                            aria-label="Search">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </nav>
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
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID_Prog.</th>
                                <th>Tipo_Cli.</th>
                                <th>ID_Cli.</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Estudio/Especialidad</th>
                                <th>Fecha_Atención</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programaciones as $programacion)
                                <tr>
                                    <td>{{ $programacion->id }}</td>
                                    <td>ITA</td>
                                    <td>{{ $programacion->clienteitaid }}</td>
                                    <td>{{ $programacion->clienteitanombre }}</td>
                                    <td>{{ $programacion->proveedornombre }}</td>
                                    <td>{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                </tr>
                            @endforeach
                            @foreach ($informesfinalessin as $programacion)
                                <tr>
                                    <td>{{ $programacion->id }}</td>
                                    <td>ITA</td>
                                    <td>{{ $programacion->clienteitaid }}</td>
                                    <td>{{ $programacion->clienteitanombre }}</td>
                                    <td>{{ $programacion->proveedorasignado }}</td>
                                    <td>{{ $programacion->accionnombre }}</td>
                                    <td>--------------</td>
                                </tr>
                            @endforeach
                            @foreach ($programacionesauditoria as $programacion)
                                <tr>
                                    <td>{{ $programacion->id }}</td>
                                    <td>AUDITORIA</td>
                                    <td>{{ $programacion->clienteauditoriaid }}</td>
                                    <td>{{ $programacion->clienteauditorianombre }}</td>
                                    <td>{{ $programacion->proveedornombre }}</td>
                                    <td>{{ $programacion->accionnombre }}</td>
                                    <td>{{ $programacion->fechaasignada }}</td>
                                </tr>
                            @endforeach
                            @foreach ($informesfinalesauditoriasin as $programacion)
                                <tr>
                                    <td>{{ $programacion->id }}</td>
                                    <td>AUDITORIA</td>
                                    <td>{{ $programacion->clienteauditoriaid }}</td>
                                    <td>{{ $programacion->clienteauditorianombre }}</td>
                                    <td>{{ $programacion->proveedorasignado }}</td>
                                    <td>{{ $programacion->accionnombre }}</td>
                                    <td>--------------</td>
                                </tr>
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
                                <th>ID_Doc.</th>
                                <th>Tipo_Cli.</th>
                                <th>ID_Cli.</th>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <th>Estudio/Especialidad</th>
                                <th>Fecha_Atención</th>
                                <th>Carga_Informe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documentaciones as $documentacion)
                                <tr>
                                    <td>{{ $documentacion->docid }}</td>
                                    <td>ITA</td>
                                    <td>{{ $documentacion->clienteitaid }}</td>
                                    <td>{{ $documentacion->clienteitanombre }}</td>
                                    <td>{{ $documentacion->proveedornombre }}</td>
                                    <td>{{ $documentacion->accionnombre }}</td>
                                    <td>{{ $documentacion->fechaasignada }}</td>
                                    <td>{{ \Carbon\Carbon::parse($documentacion->document_created_at)->format('d-m-Y') }}</td>
                                </tr>
                            @endforeach
                            @foreach ($informesfinalescon as $documentacion)
                                <tr>
                                    <td>{{ $documentacion->docid }}</td>
                                    <td>ITA</td>
                                    <td>{{ $documentacion->clienteitaid }}</td>
                                    <td>{{ $documentacion->clienteitanombre }}</td>
                                    <td>{{ $documentacion->proveedorasignado }}</td>
                                    <td>{{ $documentacion->accionnombre }}</td>
                                    <td>--------------</td>
                                    <td>{{ \Carbon\Carbon::parse($documentacion->document_created_at)->format('d-m-Y') }}</td>
                                </tr>
                            @endforeach
                            @foreach ($documentacionesauditoria as $documentacion)
                                <tr>
                                    <td>{{ $documentacion->docid }}</td>
                                    <td>ITA</td>
                                    <td>{{ $documentacion->clienteauditoriaid }}</td>
                                    <td>{{ $documentacion->clienteauditorianombre }}</td>
                                    <td>{{ $documentacion->proveedornombre }}</td>
                                    <td>{{ $documentacion->accionnombre }}</td>
                                    <td>{{ $documentacion->fechaasignada }}</td>
                                    <td>{{ \Carbon\Carbon::parse($documentacion->document_created_at)->format('d-m-Y') }}</td>
                                </tr>
                            @endforeach
                            @foreach ($informesfinalesauditoriacon as $documentacion)
                                <tr>
                                    <td>{{ $documentacion->docid }}</td>
                                    <td>ITA</td>
                                    <td>{{ $documentacion->clienteauditoriaid }}</td>
                                    <td>{{ $documentacion->clienteauditorianombre }}</td>
                                    <td>{{ $documentacion->proveedorasignado }}</td>
                                    <td>{{ $documentacion->accionnombre }}</td>
                                    <td>--------------</td>
                                    <td>{{ \Carbon\Carbon::parse($documentacion->document_created_at)->format('d-m-Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>  
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
        .table td {
            padding: 5px 10px;;
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
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
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

{{-- 
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
                             --}}