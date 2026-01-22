@extends('adminlte::page')
    
@section('content_header')
<h1>INSTRUCTIVAS DE PODER</h1>
@stop 

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .table td {
        padding: 2px 5px;
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
    .btn-bateria {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 1px 3px;
    }
    .btn-bateria:hover {
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
    <nav class="navbar navbar-expand-lg float-right" style="margin-top: 10px;">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center">
                {{-- <form action="{{ route('buscarclientesitainstructiva') }}" method="get" class="form-inline">
                    <div class="flex-grow-1">
                        <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="NOMBRE, ID, CI DEL CLIENTE Y TRAMITE..." aria-label="Search" style="width: 400px;">
                    </div>
                    <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>Buscar</button>
                </form> --}}
                <input type="text" id="buscador" class="form-control" placeholder="BUSCAR POR ID O NOMBRE DEL CLIENTE..." style="width: 500px;">
            </div>
        </div>
    </nav>
    

    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    NUEVA INSTRUCTIVA
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="true">
                    INSTRUCTIVAS GENERADAS
                </a>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Nombre Completo</th>
                                <th>CI</th>
                                <th>Edad</th>
                                <th>Sucursal</th>
                                <th class="text-center">Crear_Ins.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td class="align-middle">{{$cliente->id}}</td>
                                    <td class="align-middle">{{$cliente->nombrecompleto}}</td>
                                    <td class="align-middle">{{$cliente->ci}}</td>
                                    <td class="align-middle">{{$cliente->edad}}</td>
                                    <td class="align-middle">{{$cliente->sucursal}}</td>
                                    <td class="text-center">
                                        <abbr title="CREAR INSTRUCTIVA DE PODER">
                                            <a class="btn btn-sm btn-bateria" href="{{ route('admin.instructivaspoder.crearinstructivapoder', $cliente) }}">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width: 5%;">ID Reg.</th>
                                <th style="width: 5%;">Cliente ID</th>
                                <th style="width: 20%;">Cliente Nombre</th>
                                <th style="width: 15%;">Trámite</th>
                                <th class="text-center" style="width: 10%;">Apoderados</th>
                                <th class="text-center" style="width: 10%;">Ins. Generada</th>
                                <th class="text-center" style="width: 15%;">Ins. Firmada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instructivas as $instructiva)
                                <tr>
                                    <td>{{$instructiva->id}}</td>
                                    <td>{{$instructiva->clienteid}}</td>
                                    <td>{{$instructiva->clientenombre}}</td>
                                    <td>{{$instructiva->tramite}}</td>
                                    <td class="text-center">
                                        @php
                                            $apoderados = [];
                                            for ($i = 1; $i <= 10; $i++) {
                                                $campo = 'apoderado' . $i;
                                                if (!empty($instructiva->$campo)) {
                                                    $apoderados[] = $instructiva->$campo;
                                                }
                                            }
                                        @endphp

                                        @if(count($apoderados) > 0)
                                            <button type="button" class="btn btn-sm btn-bateria" data-toggle="modal" data-target="#apoderadosModal{{ $instructiva->id }}">
                                                Ver Apoderados
                                            </button>
                                            <div class="modal fade" id="apoderadosModal{{ $instructiva->id }}" tabindex="-1" aria-labelledby="apoderadosModalLabel{{ $instructiva->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="apoderadosModalLabel{{ $instructiva->id }}">APODERADOS</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body" style="text-align: left">
                                                            <p style="margin-top: -15px;"><strong>CLIENTE:</strong> {{ $instructiva->clientenombre }}</p>
                                                            <p style="margin-top: -15px;"><strong>TRÁMITE:</strong> {{ $instructiva->tramite }}</p>
                                                            <ul>
                                                                @foreach($apoderados as $apoderado)
                                                                    <li>{{ $apoderado }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ asset('instructivaspoder/' . $instructiva->clienteid . '/' . $instructiva->documento) }}" 
                                            target="_blank" 
                                            class="btn btn-sm btn-bateria">
                                                <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        @if($instructiva->documentofirmado)
                                            <a href="{{ asset('instructivaspoder/' . $instructiva->clienteid . '/' . $instructiva->documentofirmado) }}" 
                                            target="_blank" 
                                            class="btn btn-sm btn-bateria">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <form action="{{ route('subirinstructivaFirmado', $instructiva->id) }}" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap:5px;">
                                                @csrf
                                                <input type="file" name="documentofirmado" class="form-control form-control-sm" required>
                                                <button type="submit" class="btn btn-sm btn-ver">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const buscador = document.getElementById("buscador");

            buscador.addEventListener("keyup", function() {
                const filtro = this.value.toLowerCase();

                // Buscar en la primera tabla (clientes)
                document.querySelectorAll("#tab-content-1 table tbody tr").forEach(function(fila) {
                    const clienteId = fila.cells[0]?.textContent.toLowerCase() || "";
                    const clienteNombre = fila.cells[1]?.textContent.toLowerCase() || "";
                    
                    fila.style.display = (clienteId.includes(filtro) || clienteNombre.includes(filtro)) ? "" : "none";
                });

                // Buscar en la segunda tabla (instructivas)
                document.querySelectorAll("#tab-content-2 table tbody tr").forEach(function(fila) {
                    const clienteId = fila.cells[1]?.textContent.toLowerCase() || "";
                    const clienteNombre = fila.cells[2]?.textContent.toLowerCase() || "";

                    fila.style.display = (clienteId.includes(filtro) || clienteNombre.includes(filtro)) ? "" : "none";
                });
            });
        });
    </script>
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