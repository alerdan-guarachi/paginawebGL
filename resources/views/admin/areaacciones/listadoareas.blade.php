@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.areaacciones.create')}}">CREAR ÁREA</a>
<h1>LISTA DE ÁREAS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
    .table td {
        padding: 5px 10px;
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
        <div class="row">
            <div class="col-lg-6">
                <strong style="font-size: 20px;">ESTUDIOS</strong>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Área</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($estudios as $estudio)
                                <tr>
                                    <td>{{ $estudio->id }}</td>
                                    <td>{{ $estudio->nombrearea }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <strong style="font-size: 20px;">ESPECIALIDADES</strong>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Área</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($especialidades as $especialidad)
                                <tr>
                                    <td>{{ $especialidad->id }}</td>
                                    <td>{{ $especialidad->nombrearea }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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