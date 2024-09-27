@extends('adminlte::page')

@section('content_header')

{{-- @can('admin.categories.create') --}}
<a class="btn btn-outline-primary btn-sm float-right" href="{{route('admin.tipo_clientes.create')}}">Nueva categoría</a>
{{-- @endcan --}}
<h1>Tipo de Cliente</h1>
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
                        <th>Nombre</th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tipo_clientes as $tipo_cliente)
                        <tr>
                            <td>{{$tipo_cliente->nombre}}</td>
                            <td width="10px">
                                @can('admin.tipo_clientes.edit')
                                <abbr title="Editar Categoria">
                                <a class="btn btn-sm btn-outline-success fas fa-edit" href="{{route('admin.tipo_clientes.edit', $tipo_cliente)}}" ></a>
                                </abbr>
                                @endcan
                            </td>

                            <td width="10px">
                                @can('admin.tipo_clientes.destroy')
                                <abbr title="Eliminar Categoria">
                                <form action="{{route('admin.tipo_clientes.destroy', $tipo_cliente)}}" class="d-inline formulario-eliminar" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger fas fa-trash-alt"></button>
                                </form>
                                </abbr>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {color:green; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'La categoría se eliminó con éxito',
      'success')
    </script>
    @elseif (session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}'
        });
    </script>
    @endif

<script>

    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "La categoría se eliminará definitivamente",
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