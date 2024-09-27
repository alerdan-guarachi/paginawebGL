@extends('adminlte::page')
    
@section('content_header')
@if ($personal)
@else
<a class="btn btn-crear btn-sm float-right" href="{{route('admin.personal.create')}}">CREAR PERFIL</a>
@endif
<h1>PERSONAL DE GOOD LIFE</h1>
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
            <p style="margin-bottom: -7px; margin-left: 10px; font-weight: bold;">TU PERFIL</p>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 30%;">Nombre Completo</th>
                        <th style="width: 30%;">Cargo</th>
                        <th style="width: 20%;">Sucursal</th>
                        <th style="width: 10%;">Estado</th>
                        <th style="width: 10%;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($personales as $personal)
                        <tr>
                            <td>{{$personal->nombrecompleto}}</td>
                            <td>{{$personal->cargo}}</td>
                            <td>{{$personal->sucursal}}</td>
                            <td>
                                @if ($personal->estado == 'ACTIVO')
                                    <span class="badge badge-success">{{ $personal->estado }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $personal->estado }}</span>
                                @endif
                            </td>
                            <td width="10px">
                                @can('admin.personal.index')
                                <abbr title="Ver perfil">
                                <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.personal.show', $personal)}}" ></a>
                                </abbr>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="margin-bottom: -7px; margin-left: 10px; margin-top: 30px; font-weight: bold;">OTROS PERFILES</p>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 30%;">Nombre Completo</th>
                        <th style="width: 30%;">Cargo</th>
                        <th style="width: 20%;">Sucursal</th>
                        <th style="width: 10%;">Estado</th>
                        <th style="width: 10%;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($todospersonales as $todopersonal)
                        <tr>
                            <td>{{$todopersonal->nombrecompleto}}</td>
                            <td>{{$todopersonal->cargo}}</td>
                            <td>{{$personal->sucursal}}</td>
                            <td>
                                @if ($todopersonal->estado == 'ACTIVO')
                                    <span class="badge badge-success">{{ $todopersonal->estado }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $todopersonal->estado }}</span>
                                @endif
                            </td>
                            <td width="10px">
                                @can('admin.personal.show')
                                <abbr title="Ver perfil">
                                <a class="btn btn-sm btn-verperfil fas fa-eye" href="{{route('admin.personal.show', $personal)}}" ></a>
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
h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
.btn-verperfil {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
        }
.btn-verperfil:hover {
        background-color: #94c93b;
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