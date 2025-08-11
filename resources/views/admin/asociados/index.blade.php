@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
@can('admin.asociados.crearasociados')
<a class="btn btn-sm float-right btn-crearasociado" href="{{route('admin.asociados.create')}}">CREAR ASOCIADO</a>
@endcan
<h1>ASOCIADOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .btn-crearasociado {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-crearasociado:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-listaclientes {
        background-color:  #ffffff;
        color: #2821f3d1;
        border-color: #2821f3d1;
        border-radius: 5px;
        font-weight: bold;
        padding: 5px 8px;
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
    .btn-crearcartapolizas {
        background-color:  #ffffff;
        color: #b91bdc;
        border-color: #b91bdc;
        border-radius: 5px;
        font-weight: bold;
        padding: 5px 10px;
    }
    .btn-crearcartapolizas:hover {
        background-color: #b91bdc;
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
        @can('admin.asociados.crearasociados')
            <nav class="navbar float-right">
                <form class="form-inline">
                    <input name="buscarpor" class="form-control mr-sm-2" type="search" placeholder="ASOCIADO" aria-label="Search">
                    <button class="btn btn-buscar my-2 my-sm-0" type="submit">BUSCAR</button>
                </form>
            </nav>
        @endcan
        <div class="table-responsive">
            <table class="table table-striped table-buttons">
                @if($rolusuario !== 'ASOCIADO')
                <thead>
                    <tr>
                        <th>CLIENTES GOOD LIFE</th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupoclientes as $grupocliente)
                        <tr>
                            <td style="padding: 10 13px;">{{$grupocliente->asociado}}</td>
                            <td width="10px">
                                <abbr title="CREAR CARTA DE SOLICITUD DE POLIZAS">
                                    @if($grupocliente->asociado === 'AUDITORIA MEDICA')
                                        @can('admin.cartaspolizas.index')
                                            <a class="btn btn-sm btn-crearcartapolizas" href="{{ route('admin.cartaspolizas.listacartaspolizas') }}">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @endcan
                                    @endif
                                </abbr>
                            </td>
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
                                        <a class="btn btn-sm btn-documentacionmultiple" href="{{ route('admin.asociados.documentacionmultipleclienteauditoria', $grupocliente) }}">
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
                            <td width="10px">
                                <abbr title="Ver programaciones pendientes">
                                    @if ($grupocliente->asociado === 'AUDITORIA MEDICA')
                                        @can('admin.asociados.verprogramacionpendienteauditoria')
                                            <a class="btn btn-sm btn-verprogramacion"
                                                href="{{ route('admin.asociados.verprogramacionauditoria', $grupocliente) }}">
                                                <i class="fas fa-calendar-alt"></i>
                                            </a>
                                        @endcan
                                    @elseif($grupocliente->asociado === 'CLIENTES COMUNES')
                                        @can('admin.asociados.verprogramacionpendientecomun')
                                            <a class="btn btn-sm btn-verprogramacion"
                                                href="{{ route('admin.asociados.verprogramacioncomun', $grupocliente) }}">
                                                <i class="fas fa-calendar-alt"></i>
                                            </a>
                                        @endcan
                                    @elseif($grupocliente->asociado === 'CLIENTES ITA')
                                        @can('admin.asociados.verprogramacionpendienteita')
                                            <a class="btn btn-sm btn-verprogramacion"
                                                href="{{ route('admin.asociados.verprogramacionita', $grupocliente) }}">
                                                <i class="fas fa-calendar-alt"></i>
                                            </a>
                                        @endcan
                                    @endif
                                </abbr>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                @endif

                <thead>
                    <tr>
                        <th>ASOCIADOS</th>
                        <th colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asociados as $asociado)
                        @if($rolusuario === 'ASOCIADO')
                            @if($asociado->asociado === $empresaUsuario)
                                <tr>
                                    <td style="padding: 10 13px;">{{ $asociado->asociado }}</td>
                                    <td width="10px">

                                    </td>
                                    @can('admin.asociados.crearclientebanco')
                                    <td width="10px">
                                        <abbr title="Ver Batería">
                                            <a class="btn btn-sm btn-crearbateria" href="{{ route('admin.asociados.verbateriasbanco', $asociado) }}">
                                                <i class="fas fa-database"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                    @endcan
                                    @can('admin.asociados.listadoclientebanco')
                                    <td width="10px">
                                        <abbr title="Lista de clientes">
                                            <a class="btn btn-sm btn-listaclientes" href="{{ route('admin.asociados.listadoclientebanco', $asociado) }}">
                                                <i class="fas fa-users"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                    @endcan
                                    @can('admin.asociados.documentacionmultipleclientebanco')
                                    <td width="10px">
                                        <abbr title="Lista de clientes">
                                            <a class="btn btn-sm btn-documentacionmultiple" href="{{ route('admin.asociados.documentacionmultipleclientebanco', $asociado) }}">
                                                <i class="fas fa-folder"></i>
                                            </a>
                                        </abbr>
                                    </td>
                                    @endcan
                                </tr>
                            @endif
                        @else
                            <tr>
                                <td style="padding: 10 10px;">{{ $asociado->asociado }}</td>
                                @can('admin.asociados.crearclientebanco')
                                <td width="10px">

                                </td>
                                <td width="10px">
                                    <abbr title="Ver Batería">
                                        <a class="btn btn-sm btn-crearbateria" href="{{ route('admin.asociados.verbateriasbanco', $asociado) }}">
                                            <i class="fas fa-database"></i>
                                        </a>
                                    </abbr>
                                </td>
                                @endcan
                                @can('admin.asociados.listadoclientebanco')
                                <td width="10px">
                                    <abbr title="Lista de clientes">
                                        <a class="btn btn-sm btn-listaclientes" href="{{ route('admin.asociados.listadoclientebanco', $asociado) }}">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    </abbr>
                                </td>
                                @endcan
                                @can('admin.asociados.documentacionmultipleclientebanco')
                                <td width="10px">
                                    <abbr title="Documentación múltiple">
                                        <a class="btn btn-sm btn-documentacionmultiple" href="{{ route('admin.asociados.documentacionmultipleclientebanco', $asociado) }}">
                                            <i class="fas fa-folder"></i>
                                        </a>
                                    </abbr>
                                </td>
                                @endcan
                                
                                @can('admin.asociados.verprogramacionpendienteauditoria')
                                <td width="10px">
                                    <abbr title="Ver programaciones pendientes">
                                        <a class="btn btn-sm btn-verprogramacion" href="{{ route('admin.asociados.verprogramacionauditoria', $grupocliente) }}" style="pointer-events: none; opacity: 0.5;">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                    </abbr>
                                </td>
                                @endcan
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <style>
                    .table-buttons td {
                        padding: 10 4px;
                    }
                </style>
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