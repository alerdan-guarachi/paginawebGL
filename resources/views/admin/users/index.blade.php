@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<h1>LISTA DE USUARIOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
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
        <nav class="navbar float-right">
            <form class="form-inline">
                <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Nombre del usuario" aria-label="Search">
                <button class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </nav>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombres y Apellidos</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                {{ $user->rolesFormatted }}
                            </td>
                            <td>
                                @if ($user->estado == 'ACTIVO')
                                    <span class="badge badge-success">{{ $user->estado }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $user->estado }}</span>
                                @endif
                            </td>
                            <td width="10px">
                                @can('admin.users.edit')
                                <abbr title="ASIGNAR ROL">
                                    <a class="btn btn-sm btn-editar" href="{{route('admin.users.edit', $user)}}">
                                        <i class="fas fa-id-card-alt"></i>
                                    </a>
                                </abbr>
                                @endcan
                            </td>

                            <td width="10px">
                                {{-- @can('admin.users.destroy')
                                <abbr title="Eliminar Usuario">
                                    <form action="{{route('admin.users.destroy', $user)}}" class="d-inline formulario-eliminar" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger fas fa-trash-alt"></button>
                                    </form>
                                </abbr>
                                @endcan --}}
                            </td>  
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El usuario se eliminó con éxito',
      'success')
    </script>
    @endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El usuario se eliminará definitivamente",
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