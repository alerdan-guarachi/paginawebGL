@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>
@can('admin.asociados.crearcontactoclienteauditoria')
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.asociados.crearcontactoclienteauditoria', $clienteauditoria)}}">CREAR CONTACTO</a>
@endcan
<h5>CONTACTOS DE:</h5> 
<h3>{{$clienteauditoria->nombrecompleto}}</h3>



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
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Contacto</th>
                        <th>Parentesco</th>
                        <th>Celular</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contactos as $contacto)
                        <tr>
                            <td>{{$contacto->nombrecontacto}}</td>
                            <td>{{$contacto->parentesco}}</td>
                            <td>{{$contacto->celularcontacto}}</td>
                            <td>{{$contacto->telefonocontacto}}</td>
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
h1, th {
    color:#94c93b; 
    font-family: "Segoe UI";
    font-weight: 900;
}
.btn-editar {
    background-color:  #ffffff;
    color: #0400ff;
    border-color: #0400ff;
    border-radius: 5px;
}
.btn-editar:hover {
    background-color: #0400ff;
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
    margin-left: 10px;
    margin-right: 10px;
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