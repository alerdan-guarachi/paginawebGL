@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
{{-- @can('admin.roles.create') --}}
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.asociados.create')}}">CREAR ASOCIADO</a>
{{-- @endcan --}}
<h1>ASOCIADOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .btn-crearbateria {
        background-color:  #ffffff;
        color: #725050d1;
        border-color: #725050d1;
        border-radius: 5px;
        font-weight: bold;
        padding: 5px 10px;
    }
    .btn-crearbateria:hover {
        background-color: #725050d1;
        color: #ffffff;
    }
    .btn-listaclientes {
        background-color:  #ffffff;
        color: #2821f3d1;
        border-color: #2821f3d1;
        border-radius: 5px;
        font-weight: bold;
        padding: 5px 10px;
    }
    .btn-listaclientes:hover {
        background-color: #2821f3d1;
        color: #ffffff;
    }
    .btn-documentacionmultiple {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        font-weight: bold;
        padding: 5px 10px;
    }
    .btn-documentacionmultiple:hover {
        background-color: #faa625;
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
        }, 5000);
    </script>
@endif
<div class="card">
    <div class="card-body">
        <nav class="navbar float-right">
            <form class="form-inline">
                <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="ASOCIADO" aria-label="Search">
                <button class="btn btn-buscar my-2 my-sm-0" type="submit">BUSCAR</button>
            </form>
        </nav>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Clientes Good Life</th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupoclientes as $grupocliente)
                        <tr>
                            <td>{{$grupocliente->asociado}}</td>
                            <td width="10px">
                                <abbr title="Ver bateria">
                                    @if($grupocliente->asociado === 'AUDITORIA MEDICA')
                                    @can('admin.asociados.crearclienteauditoria')
                                        <a class="btn btn-sm btn-crearbateria" href="{{ route('admin.asociados.verbateriaasociado', $grupocliente) }}">
                                            <i class="fas fa-database"></i>
                                        </a>
                                        @endcan
                                    @elseif($grupocliente->asociado === 'CLIENTES COMUNES')
                                    @can('admin.asociados.crearclientecomun')
                                        <a class="btn btn-sm btn-crearbateria" href="{{ route('admin.asociados.verbateriaasociado', $grupocliente) }}">
                                            <i class="fas fa-database"></i>
                                        </a>
                                        @endcan
                                    @elseif($grupocliente->asociado === 'CLIENTES ITA')
                                    @can('admin.asociados.crearclienteita')
                                        <a class="btn btn-sm btn-crearbateria" href="{{ route('admin.asociados.verbateriaasociado', $grupocliente) }}">
                                            <i class="fas fa-database"></i>
                                        </a>
                                        @endcan
                                    @endif
                                </abbr>
                            </td>
                               
                            <td width="10px">
                                <abbr title="Lista de clientes">
                                    @if($grupocliente->asociado === 'AUDITORIA MEDICA')
                                    @can('admin.asociados.listadoclienteauditoria')
                                        <a class="btn btn-sm btn-listaclientes" href="{{ route('admin.asociados.listadoclienteauditoria', $grupocliente) }}">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        @endcan
                                    @elseif($grupocliente->asociado === 'CLIENTES COMUNES')
                                    @can('admin.asociados.listadoclientecomun')
                                        <a class="btn btn-sm btn-listaclientes" href="{{ route('admin.asociados.listadoclientecomun', $grupocliente) }}">
                                            <i class="fas fa-users"></i>
                                        </a>
                                        @endcan
                                    @elseif($grupocliente->asociado === 'CLIENTES ITA')
                                    @can('admin.asociados.listadoclienteita')
                                        <a class="btn btn-sm btn-listaclientes" href="{{ route('admin.asociados.listadoclienteita', $grupocliente) }}">
                                            <i class="fas fa-users"></i>
                                        </a>    
                                        @endcan
                                    @endif
                                </abbr>
                            </td> 
                            <td width="10px">
                                <abbr title="Documentación múltiple">
                                    @if($grupocliente->asociado === 'AUDITORIA MEDICA')
                                    @can('admin.asociados.documentacionmultipleclienteita')
                                        <a class="btn btn-sm btn-documentacionmultiple" href="{{ route('admin.asociados.documentacionmultipleclienteita', $grupocliente) }}">
                                            <i class="fas fa-folder"></i>
                                        </a>
                                        @endcan
                                    @elseif($grupocliente->asociado === 'CLIENTES COMUNES')
                                    @can('admin.asociados.documentacionmultipleclientecomun')
                                        <a class="btn btn-sm btn-documentacionmultiple" href="{{ route('admin.asociados.documentacionmultipleclientecomun', $grupocliente) }}">
                                            <i class="fas fa-folder"></i>
                                        </a>
                                        @endcan
                                    @elseif($grupocliente->asociado === 'CLIENTES ITA')
                                    @can('admin.asociados.documentacionmultipleclienteita')
                                        <a class="btn btn-sm btn-documentacionmultiple" href="{{ route('admin.asociados.documentacionmultipleclienteita', $grupocliente) }}">
                                            <i class="fas fa-folder"></i>
                                        </a>   
                                        @endcan 
                                    @endif
                                </abbr>
                            </td>   
                        </tr>
                    @endforeach
                </tbody>
                <thead>
                    <tr>
                        <th>Asociados</th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asociados as $asociado)
                        <tr>
                            <td>{{$asociado->asociado}}</td>
                            {{-- @can('admin.asociados.crearclientebanco') --}}
                            <td width="10px">
                                <abbr title="Ver Batería">
                                    <a class="btn btn-sm btn-crearbateria" href="{{ route('admin.asociados.verbateriasbanco', $asociado) }}">
                                        <i class="fas fa-database"></i>
                                    </a>
                                </abbr>
                            </td>
                            {{-- @endcan --}}
                            @can('admin.asociados.listadoclientebanco')
                            <td width="10px">
                                <abbr title="Lista de clientes">
                                    <a class="btn btn-sm btn-listaclientes" href="{{ route('admin.asociados.listadoclientebanco', $asociado) }}">
                                        <i class="fas fa-users"></i>
                                    </a> 
                                </abbr>
                            </td>    
                            @endcan
                            {{-- @can('admin.asociados.documentacionmultipleclienteita') --}}
                            <td width="10px">
                                <abbr title="Lista de clientes">
                                    <a class="btn btn-sm btn-documentacionmultiple" href="{{ route('admin.asociados.documentacionmultipleclientebanco', $asociado) }}">
                                        <i class="fas fa-folder"></i>
                                    </a>   
                                </abbr>
                            </td>    
                            {{-- @endcan --}}
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