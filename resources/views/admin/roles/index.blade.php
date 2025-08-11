@extends('adminlte::page')
<link href="assets/img/logo.png" rel="icon">
@section('content_header')
@can('admin.roles.create')
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.roles.create')}}">CREAR ROL</a>
@endcan
<h1>LISTA DE ROLES</h1>
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
                    <th>Rol</th>
                    <th colspan="2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{$role->name}}</td>
                        <td width="10px">
                            @can('admin.roles.edit')
                            <abbr title="Editar Rol">
                                <a class="btn btn-sm fas fa-edit btn-editar" href="{{route('admin.roles.edit', $role)}}" ></a>
                            </abbr>
                            @endcan
                        </td>

                        {{-- <td width="10px">
                            @can('admin.roles.destroy')
                            <abbr title="Eliminar Rol">
                                <form action="{{route('admin.roles.destroy', $role)}}" class="d-inline formulario-eliminar" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm fas fa-trash-alt btn-eliminar"></button>
                                </form>
                            </abbr>
                            @endcan
                        </td>
                    </tr> --}}
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .btn-editar {
            background-color:  #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
        }
    .btn-editar:hover {
            background-color: #94c93b;
            color: #ffffff;
        }
    .btn-eliminar {
            background-color:  #ffffff;
            color: #ff0000;
            border-color: #ff0000;
            border-radius: 5px;
        }
    .btn-eliminar:hover {
            background-color: #ff0000;
            color: #ffffff;
        }
    .btn-crear {
            background-color:  #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
            padding: 5px 10px;
        }
    .btn-crear:hover {
            background-color: #94c93b;
            color: #ffffff;
        }
    .table td {
        padding: 5px 10px;
        }
</style>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
      'success')
    </script>
    @endif

<script>

    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El rol se eliminará definitivamente",
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