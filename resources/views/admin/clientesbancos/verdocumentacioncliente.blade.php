@extends('adminlte::page')
    
@section('content_header')

{{-- @can('admin.profiles.create') --}}
{{-- <a class="btn btn-crear btn-sm float-right" href="{{route('admin.clientesbancos.create')}}">Crear cliente</a> --}}
{{-- @endcan --}}
<h1>DOCUMENTACION DE "{{ $clientebanco }}"</h1>
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
        {{-- <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('ruta.para.mostrar.todos') }}" method="get" class="mr-2 mb-2 mb-lg-0" style="max-width: calc(100% - 250px);"> <!-- ajusta el valor máximo según el ancho de tu card -->
                        <input type="hidden" name="mostrar_todos" value="1">
                        <button type="submit" class="btn btn-mostrartodo">Mostrar Todo</button>
                    </form>
            
                    <form action="{{ route('buscar.clientes') }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Cliente  /  CI  /  Ciudad" aria-label="Search">
                        </div>
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit" disabled>Buscar</button>
                    </form>
                </div>
            </div>
        </nav> --}}

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        {{-- <th>Nombre completo</th> --}}
                        <th>Accion</th>
                        <th>Documento</th>
                        <th colspan="3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documentacionclientes as $documentacioncliente)
                    <tr>
                        {{-- <td>{{$documentacioncliente->nombrecompleto}}</td> --}}
                        <td>{{$documentacioncliente->accion}}</td>
                        <td>{{$documentacioncliente->document}}</td>
                        <td width="10px">
                            <abbr title="Ver documento">
                                <a href="{{ asset('file/' . $documentacioncliente->document) }}" alt="document" class="mb-1 btn btn-sm btn-verdocumentacion">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </abbr>
                        </td>
                        <td width="10px">
                            <abbr title="Descargar documento">
                                <a class="btn btn-sm btn-descargardocumentacion" href="{{ route('admin.clientesbancos.downloadPDF', $documentacioncliente->id) }}">
                                    <i class="fas fa-download"></i>
                                </a>
                            </abbr>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="styleheet" href="/css/admin_custom.css">
<style>
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
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
<script>
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