@extends('adminlte::page')
    
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.proveedores.index') }}">REGRESAR</a>
<h5>CREAR BATERIA DE:</h5>
<h3>{{$proveedor->proveedor}}</h3>
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
        <div class="row">
            <div class="col-lg-6">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ESTUDIOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accionesProveedorEstudios as $accion)
                            <tr>
                                <td>{{$accion}}</td>
                                {{-- <td width="10px">
                                    <abbr title="Eliminar acción">
                                        <form action="{{route('admin.proveedores.eliminaraccionproveedor', $accion)}}" class="d-inline formulario-eliminar" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm fas fa-trash-alt btn-eliminar"></button>
                                        </form>
                                    </abbr>
                                </td> --}}
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ESPECIALIDADES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accionesProveedorEspecialidad as $accion)
                            <tr>
                                <td>{{$accion}}</td>
                                {{-- <td width="10px">
                                    <abbr title="Eliminar acción">
                                        <form action="{{route('admin.proveedores.eliminaraccionproveedor', $accion)}}" class="d-inline formulario-eliminar" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm fas fa-trash-alt btn-eliminar"></button>
                                        </form>
                                    </abbr>
                                </td> --}}
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ESPECIALIDADES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bateriaproveedores as $bateriaproveedor)
                        <tr>
                            <td>{{$bateriaproveedor->accion}}</td>
                            <td width="10px">
                                <abbr title="Eliminar acción">
                                    <form action="{{route('admin.proveedores.eliminaraccionproveedor', $bateriaproveedor)}}" class="d-inline formulario-eliminar" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm fas fa-trash-alt btn-eliminar"></button>
                                    </form>
                                </abbr>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> --}}
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-eliminar {
        background-color: #ffffff;
        color: #ff0000;
        border-color: #ff0000;
        border-radius: 5px;
        padding: 5px 8px;
    }
    .btn-eliminar:hover {
        background-color: #ff0000;
        color: #ffffff;
    }
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
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
      'La acción se eliminó con éxito',
      'success')
    </script>
    @endif

<script>
    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();
        Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción se eliminará definitivamente",
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