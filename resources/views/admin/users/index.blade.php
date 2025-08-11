@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<h1>LISTA DE USUARIOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .table td {
        padding: 5px 10px;
        }
    h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
        .btn-eliminar2 {
        background-color:  #ffffff;
        color: #cc2d2d;
        border-color: #cc2d2d;
        border-radius: 5px;
        padding: 2px 10px;
        }
        .btn-eliminar2:hover {
        background-color: #cc2d2d;
        color: #ffffff;
        }
        .btn-editar2 {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 6px 10px;
        }
        .btn-editar2:hover {
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
        <nav class="navbar float-right">
            <form class="form-inline">
                <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="NOMBRE DEL USUARIO..." aria-label="Search">
                <button class="btn btn-buscar my-2 my-sm-0" type="submit">BUSCAR</button>
            </form>
        </nav>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombres y Apellidos</th>
                        <th>Email</th>
                        <th>Registro</th>
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
                            <td>{{$user->created_at->format('Y-m-d') }}</td>
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
                            <td>
                                @if ($user->estado === 'ACTIVO')
                                    @can('admin.users.edit')
                                        @if ($user->name === 'CARLOS ALEJANDRO GUARACHI SANDOVAL')
                                            <a class="btn btn-sm btn-secondary disabled" href="#" title="NO SE PUEDE ASIGNAR ROL A ESTE USUARIO"
                                            tabindex="-1" aria-disabled="true" style="padding: 5px 10px;">
                                                <i class="fas fa-id-card-alt"></i>
                                            </a>
                                        @else
                                            <a class="btn btn-sm btn-editar2" href="{{ route('admin.users.edit', $user) }}" title="ASIGNAR ROL">
                                                <i class="fas fa-id-card-alt"></i>
                                            </a>
                                        @endif
                                    @endcan
                                    @if (auth()->user()->name === 'CARLOS ALEJANDRO GUARACHI SANDOVAL')
                                        @if ($user->name === 'CARLOS ALEJANDRO GUARACHI SANDOVAL')
                                            <button class="btn btn-sm btn-secondary" disabled title="INACTIVAR USUARIO DESHABILITADO" style="padding: 5px 10px;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @else
                                            <form action="{{route('admin.users.destroy', $user)}}" class="d-inline formulario-eliminar" method="POST" title="INACTIVAR USUARIO">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-eliminar2 fas fa-trash-alt"></button>
                                            </form>
                                        @endif
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled title="INACTIVAR USUARIO DESHABILITADO" style="padding: 5px 10px;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif
                                @else
                                    <a class="btn btn-sm btn-secondary disabled" href="#" title="ASIGNAR ROL DESHABILITADO" tabindex="-1" aria-disabled="true" style="padding: 5px 10px;">
                                        <i class="fas fa-id-card-alt"></i>
                                    </a>
                                    <button class="btn btn-sm btn-secondary" disabled title="INACTIVAR USUARIO DESHABILITADO" style="padding: 5px 10px;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
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
      '¡Inactivado!',
      'El usuario se inactivó con éxito',
      'success')
    </script>
    @endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El usuario se inactivará",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Si, inactivar!',
        cancelButtonText: 'Cancelar'
        }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
        }) 
    });
</script>
@endsection