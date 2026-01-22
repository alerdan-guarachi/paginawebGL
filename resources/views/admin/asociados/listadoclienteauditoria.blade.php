@extends('adminlte::page')
    
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{route('admin.asociados.index')}}">REGRESAR</a>
@can('admin.asociados.crearclienteauditoria')
<a class="btn btn-sm float-right btn-crearcliente" href="{{ route('admin.asociados.crearclienteauditoria', 2) }}">CREAR CLIENTE FIJO</a>
<a class="btn btn-sm float-right btn-crearcliente" href="{{ route('admin.asociados.crearclienteauditoriamomentaneo', 3) }}">CREAR CLIENTE MOMENTÁNEO</a>
@endcan
<h1>CLIENTES AUDITORIA</h1>
@stop 

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
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
    .btn-crearcliente {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        margin-right: 10px;
    }
    .btn-crearcliente:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .table td {
        padding: 5px 10px;;
    }
    .btn-vercliente {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 6px;
        }
    .btn-vercliente:hover {
        background-color: #94c93b;
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
        font-size: 14px;
        color: #faa625;
        background-color: #fef4e7;
    }
    .nav-tabs .nav-link.active {
        font-weight: bold;
        font-size: 14px;
        color: #94c93b;
        background-color: #ffffff;
    }
    .nav-link.active .circle {
        background-color: #94c93b;
        color: #fff;
    }
    .nav-link .circle {
        background-color: #faa625;
        color: #fff;
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
    <div class="card-body">
        <div class="table-responsive">
            <input id="buscador" class="form-control" style="width: 400px;" type="search" placeholder="BUSCAR POR ID, CI O NOMBRE DEL CLIENTE..." aria-label="Search">
        </div>
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                        CLIENTES FIJOS
                    </a>
                </li>     
                <li class="nav-item">
                    <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                        CLIENTES MOMENTÁNEOS
                    </a>
                </li>        
            </ul>
        </div>
        <br>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombres y Apellidos</th>
                                <th>CI</th>
                                <th>Edad</th>
                                <th>Celular</th>
                                <th>Sucursal</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                @if ($cliente->tipocliente === 'FIJO')
                                    <tr>
                                        <td class="align-middle">{{$cliente->id}}</td>
                                        <td class="align-middle">{{$cliente->nombrecompleto}}</td>
                                        <td class="align-middle">{{$cliente->ci}}</td>
                                        <td class="align-middle">{{$cliente->edad}}</td>
                                        <td class="align-middle">{{$cliente->celular}}</td>
                                        <td class="align-middle">{{$cliente->sucursal}}</td>
                                        @can('admin.asociados.verclienteauditoria')
                                        <td>
                                            <abbr title="VER CLIENTE">
                                                <a class="btn btn-sm btn-vercliente" href="{{ route('admin.asociados.verclienteauditoria', $cliente) }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                        @endcan
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombres y Apellidos</th>
                                <th>CI</th>
                                <th>Celular</th>
                                <th>Sucursal</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                @if ($cliente->tipocliente === 'MOMENTÁNEO')
                                    <tr>
                                        <td class="align-middle">{{$cliente->id}}</td>
                                        <td class="align-middle">{{$cliente->nombrecompleto}}</td>
                                        <td class="align-middle">{{$cliente->ci}}</td>
                                        <td class="align-middle">{{$cliente->celular}}</td>
                                        <td class="align-middle">{{$cliente->sucursal}}</td>
                                        @can('admin.asociados.verclienteauditoria')
                                        <td>
                                            <abbr title="VER CLIENTE">
                                                <a class="btn btn-sm btn-vercliente" href="{{ route('admin.asociados.verclienteauditoriamomentaneo', $cliente) }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </abbr>
                                        </td>
                                        @endcan
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const input = document.getElementById("buscador");
                    const rows = document.querySelectorAll("table tbody tr");
                    input.addEventListener("keyup", function () {
                        const value = this.value.toLowerCase();
                        rows.forEach(function (row) {
                            const id = row.cells[0].textContent.toLowerCase();
                            const nombre = row.cells[1].textContent.toLowerCase();
                            const ci = row.cells[2].textContent.toLowerCase();
                            if (id.includes(value) || nombre.includes(value) || ci.includes(value)) {
                                row.style.display = "";
                            } else {
                                row.style.display = "none";
                            }
                        });
                    });
                });
            </script>
        </div>
    </div>
</div>
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
</script>
@endsection