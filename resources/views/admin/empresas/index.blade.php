@extends('adminlte::page')

@section('content_header')

@can('admin.empresas.create')
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.empresas.create')}}">NUEVA EMPRESA</a>
@endcan
<h1>EMPRESAS</h1>
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
                <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="Nombre de la empresa" aria-label="Search">
                <button class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </nav>
        <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Contacto</th>
                    <th>Celular</th>
                    <th>Telefono</th>
                    <th>Direccion</th>
                    <th colspan="2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($empresas as $empresa)
                    <tr>
                        <td>{{$empresa->nombreempresa}}</td>
                        <td>{{$empresa->contacto}}</td>
                        <td>{{$empresa->celular}}</td>
                        <td>{{$empresa->telefono}}</td>
                        <td>{{$empresa->direccion}}</td>
                        <td width="10px">
                            @can('admin.empresas.edit')
                            <abbr title="Editar Empresa">
                                <a class="btn btn-sm fas fa-edit btn-editar" href="{{route('admin.empresas.edit', $empresa)}}" ></a>
                            </abbr>
                            @endcan
                        </td>

                        <td width="10px">
                            {{-- @can('admin.roles.destroy') --}}
                            {{-- <abbr title="Eliminar Empresa">
                                <form action="{{route('admin.empresas.destroy', $empresa)}}" class="d-inline formulario-eliminar" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm fas fa-trash-alt btn-eliminar"></button>
                                </form>
                            </abbr> --}}
                                {{-- @endcan --}}
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