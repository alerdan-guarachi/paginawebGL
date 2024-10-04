@extends('adminlte::page')
<link href="assets/img/logo.png" rel="icon">
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.roles.index') }}">REGRESAR</a>
<h1>EDITAR ROL</h1>
<style>
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
    .btn-actualizar {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 8px 16px;
        }
    
    .btn-actualizar:hover {
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
        {!! Form::model($role, ['route' => ['admin.roles.update', $role], 'method' => 'put']) !!}

        @include('admin.roles.partials.form')

            {!! Form::submit('ACTUALIZAR ROL', ['class' => 'btn btn-actualizar']) !!}
        {!! Form::close() !!}
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">

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