@extends('adminlte::page')
    
@section('content_header')

{{-- @can('admin.profiles.create') --}}
{{-- @if ($cliente) --}}
{{-- @else --}}
<a class="btn btn-crear btn-sm float-right" href="{{route('admin.clientes.create')}}">Crear cliente</a>
{{-- @endif --}}
{{-- @endcan --}}
<h1>Lista de clientes</h1>
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
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellido Materno</th>
                        <th>Apellido Paterno</th>
                        <th colspan="3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                        <tr>
                            <td>{{$cliente->nombres}}</td>
                            <td>{{$cliente->apepaterno}}</td>
                            <td>{{$cliente->apematerno}}</td>

                            <td width="10px">
                                {{-- @can('admin.clientes.index') --}}
                                <abbr title="Formulario">
                                    <a class="btn btn-sm btn-outline-success fas fa-window-restore" href="{{route('admin.clientes.formulario', $cliente)}}" ></a>
                                    </abbr>
                                {{-- @endcan --}}
                            </td>

                            <td width="10px">
                                {{-- @can('admin.clientes.index') --}}
                                <abbr title="Ver perfil">
                                    <a class="btn btn-sm btn-outline-warning fas fa-eye" href="{{route('admin.clientes.show', $cliente)}}" ></a>
                                    </abbr>
                                {{-- @endcan --}}
                            </td>

                            <td width="10px">
                                {{-- @can('admin.clientes.index') --}}
                                <abbr title="Generar Etiqueta">
                                <a class="btn btn-sm btn-outline-primary fas fa-file-alt" href="{{route('admin.clientes.print', $cliente)}}" ></a>
                                </abbr>
                                {{-- @endcan --}}
                            </td>

                            <td width="10px">
                                {{-- @can('admin.clientes.index') --}}
                                <abbr title="Generar Check List">
                                <a class="btn btn-sm btn-outline-success fas fa-folder" href="{{route('admin.clientes.show2', $cliente)}}" ></a>
                                </abbr>
                                {{-- @endcan --}}
                            </td>

                            <td width="10px">
                                {{-- @can('admin.clientes.destroy') --}}
                                <abbr title="Eliminar Perfil">
                                <form action="{{route('admin.clientes.destroy', $cliente)}}" class="d-inline formulario-eliminar" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger fas fa-trash-alt"></button>
                                </form>
                                </abbr>
                                {{-- @endcan --}}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- <div class="card-footer">
                {{$clientes->links()}}
            </div> --}}
        </div>
    </div>            
@stop

@section('css')
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
</script>
@endsection