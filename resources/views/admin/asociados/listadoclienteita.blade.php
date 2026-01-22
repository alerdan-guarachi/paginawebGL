@extends('adminlte::page')
    
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{route('admin.asociados.index')}}">REGRESAR</a>
@can('admin.asociados.crearclienteita')
<a class="btn btn-sm float-right btn-crearcliente" href="{{ route('admin.asociados.crearclienteita', 6) }}">CREAR CLIENTE</a>
@endcan
<h1>CLIENTES ITA</h1>
@stop 

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
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
        margin-left: 10px;
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
        }, 3000);
    </script>
@endif

<div class="card">
    <div class="card-body">
        {{-- <nav class="navbar navbar-expand-lg float-right"> --}}
            {{-- <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarclientesita') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Nombre  /  CI  / ID" aria-label="Search">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div> --}}
            
        {{-- </nav> --}}
        <div class="table-responsive">
            <input id="buscador" class="form-control" style="width: 400px;" type="search" placeholder="BUSCAR POR ID, CI O NOMBRE DEL CLIENTE..." aria-label="Search"><br>
            <table class="table table-striped table-sm table-bordered">
                <thead>
                    <tr> 
                        <th style="width: 20px;">ID</th>
                        <th style="width: 200px;">Nombres y Apellidos</th>
                        <th style="width: 150px;">Derivación</th>
                        <th style="width: 150px;">Servicio</th>
                        <th style="width: 100px;">CI</th>
                        <th style="width: 80px;">Edad</th>
                        <th style="width: 80px;">Celular</th>
                        <th style="width: 120px;">Sucursal</th>
                        <th style="width: 50px;">Ver</th>
                    </tr>
                    
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                        <tr>
                            <td class="align-middle">{{$cliente->id}}</td>
                            <td class="align-middle">{{$cliente->nombrecompleto}}</td>
                            <td class="align-middle">{{$cliente->derivacion ?? 0}}</td>
                            <td class="align-middle"> 
                                @if ($cliente->servicios->isNotEmpty())
                                    {{ implode(', ', $cliente->servicios->pluck('tramite')->unique()->toArray()) }}
                                @else
                                    NINGUNO
                                @endif
                            </td>                      
                            <td class="align-middle">{{$cliente->ci}}</td>
                            <td class="align-middle">{{$cliente->edad}}</td>
                            <td class="align-middle">{{$cliente->celular ?? 0 }}</td>
                            <td class="align-middle">{{$cliente->sucursal}}</td>
                            {{-- @can('admin.asociados.verclienteita')
                            <td width="10px">
                                <abbr title="VER CLIENTE">
                                    <a class="btn btn-sm btn-vercliente" href="{{ route('admin.asociados.verclienteita', $cliente) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </abbr>
                            </td>
                            @endcan --}}
                            @can('admin.asociados.verclienteita')
                                <td>
                                    <abbr title="VER CLIENTE">
                                        @php
                                            $esOperativo = auth()->user()->roles->contains('name', 'OPERATIVO');
                                            $bloquear = $esOperativo && (is_null($cliente->derivacion) || $cliente->derivacion === 'CARTERA DE CLIENTES');
                                        @endphp

                                        <a class="btn btn-sm btn-vercliente {{ $bloquear ? 'disabled' : '' }}" 
                                        href="{{ $bloquear ? '#' : route('admin.asociados.verclienteita', $cliente) }}" 
                                        @if($bloquear) aria-disabled="true" @endif>
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </abbr>
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const input = document.getElementById("buscador");
                            const rows = document.querySelectorAll("table tbody tr");

                            input.addEventListener("keyup", function () {
                                const value = this.value.toLowerCase();

                                rows.forEach(function (row) {
                                    // buscamos en ID, nombrecompleto y CI
                                    const id = row.cells[0].textContent.toLowerCase();
                                    const nombre = row.cells[1].textContent.toLowerCase();
                                    const ci = row.cells[4].textContent.toLowerCase();

                                    if (id.includes(value) || nombre.includes(value) || ci.includes(value)) {
                                        row.style.display = "";
                                    } else {
                                        row.style.display = "none";
                                    }
                                });
                            });
                        });
                    </script>
                </tbody>
            </table>
        </div>
        {{-- {{ $clientes->links() }} --}}
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