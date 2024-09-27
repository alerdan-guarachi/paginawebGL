@extends('adminlte::page')

@section('content_header')

@can('admin.mensajes.create')
<a class="btn btn-sm float-right btn-crear" href="{{route('admin.mensajes.create')}}">NUEVO MENSAJE</a>
@endcan
<h1>MENSAJES</h1>
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
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Encargado</th>
                    <th>Asunto</th>
                    <th>Fecha y Hora</th>
                    <th>Ver</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mensajes as $mensaje)
                    <tr>
                        <td>{{ $mensaje->usuarioregistro }}</td>
                        <td>{{ $mensaje->titulo }}</td>
                        <td>{{ date('Y-m-d H:i:s', strtotime($mensaje->created_at)) }}</td>
                        <td>
                            <abbr title="Ver Mensaje">
                                <button type="button" class="btn btn-ver btn-sm" data-toggle="modal" data-target="#modalMensaje{{ $mensaje->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </abbr>
                        </td>
                    <div class="modal fade" id="modalMensaje{{ $mensaje->id }}" tabindex="-1" role="dialog" aria-labelledby="modalMensajeLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalMensajeLabel">{{ $mensaje->titulo }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ $mensaje->mensaje }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-cerrar {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cerrar:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    h5{
        font-size: 25px;
        font-weight: 900;
        }
    .btn-ver {
                background-color:  #ffffff;
                color: #faa625;
                border-color: #faa625;
                border-radius: 5px;
            }
        .btn-ver:hover {
                background-color: #faa625;
                color: #ffffff;
            }
    h1, th {color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
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