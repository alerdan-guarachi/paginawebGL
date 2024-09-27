@extends('adminlte::page')
    
@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.crearprogramacionclientebanco', $clientebanco) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-reprogramacion" data-toggle="modal" data-target="#ventanaModal">REPROGRAMACIONES</a>
<h5>REPROGRAMAR ACCIONES DE:</h5>
<h3>{{$clientebanco->nombrecompleto}}</h3>
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
        <nav class="navbar navbar-expand-lg float-right">
            <div class="container-fluid">
                <div class="d-flex flex-wrap align-items-center">
                    <form action="{{ route('buscarprogramacionclientebanco', $clientebanco) }}" method="get" class="form-inline">
                        <div class="flex-grow-1">
                            <select name="buscarpor" class="form-control mr-sm-2">
                                <option value="" disabled selected>Fecha de Bateria</option>
                                @foreach($fechas as $fecha)
                                    <option value="{{ $fecha }}">{{ $fecha }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="total" id="total" value="{{ $total }}">
                        <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
        </nav>

        <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">REPROGRAMACIONES:</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Responsable</th>
                                        <th>Proveedor</th>
                                        <th>Acción</th>
                                        <th>Motivo</th>
                                        <th>Fecha de Batería</th>
                                        <th>Fecha y hora de reprog.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reprogramaciones as $reprogramacion)
                                        <tr>
                                            <td>{{ $reprogramacion->usuarioactualizacion }}</td>
                                            <td>{{ $reprogramacion->proveedornombre }}</td>
                                            <td>{{ $reprogramacion->accionnombre }}</td>
                                            <td>{{ $reprogramacion->motivoreprogramacion }}</td>
                                            <td>{{ $reprogramacion->fechabateria }}</td>
                                            <td>{{ $reprogramacion->deleted_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Accion</th>
                        <th>Proveedor</th>
                        <th>Fecha programada</th>
                        <th>Hora programada</th>
                        <th colspan="3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($programacionsubclientes as $programacionsubcliente)
                    <tr>
                        <td>{{$programacionsubcliente->accionnombre}}</td>
                        <td>{{$programacionsubcliente->proveedornombre}}</td>
                        <td>{{$programacionsubcliente->fechaasignada}}</td>
                        <td>{{$programacionsubcliente->horadesde}} - {{$programacionsubcliente->horahasta}}</td>
                        <td width="10px">
                            <abbr title="Reprogramar">
                                <button type="button" class="btn btn-sm fas fa-list-alt btn-eliminar" data-id="{{ $programacionsubcliente->id }}" data-toggle="modal" data-target="#deleteModal"></button>
                            </abbr>
                        </td>
                    </tr>
                    @endforeach

                    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">REPROGRAMAR</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="deleteForm" action="" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-body">
                                        {!! Form::hidden('usuarioactualizacion', auth()->user()->name) !!}
                                        <div class="form-group">
                                            <label for="motivoreprogramacion">Motivo de Reprogramación:</label>
                                            <input type="text" name="motivoreprogramacion" id="motivoreprogramacion" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-cancelar" data-dismiss="modal">CANCELAR</button>
                                        <button type="submit" class="btn btn-reprogramar">REPROGRAMAR</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-cerrar {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cerrar:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-reprogramacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
        margin-right: 10px;
        }
    .btn-reprogramacion:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-reprogramar {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-reprogramar:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-cancelar {
        background-color: #ffffff;
        color: #ff0000;
        border-color: #ff0000;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cancelar:hover {
        background-color: #ff0000;
        color: #ffffff;
    }
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
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
    .btn-mostrartodo { 
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-mostrartodo:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-bateria {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
    }
    .btn-bateria:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-programar {
        background-color:  #ffffff;
        color: #2136bd;
        border-color: #2136bd;
        border-radius: 5px;
    }
    .btn-programar:hover {
        background-color: #2136bd;
        color: #ffffff;
    }
    .btn-estadoprogramacion {
        background-color:  #ffffff;
        color: #58a6f4;
        border-color: #58a6f4;
        border-radius: 5px;
    }
    .btn-estadoprogramacion:hover {
        background-color: #58a6f4;
        color: #ffffff;
    }
    .btn-subirdocumentacion {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
    }
    .btn-subirdocumentacion:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verdocumentacion {
        background-color:  #ffffff;
        color: #8721f3;
        border-color: #8721f3;
        border-radius: 5px;
    }
    .btn-verdocumentacion:hover {
        background-color: #8721f3;
        color: #ffffff;
    }
    .btn-eliminar {
                background-color:  #ffffff;
                color: #faa625;
                border-color: #faa625;
                border-radius: 5px;
            }
        .btn-eliminar:hover {
                background-color: #faa625;
                color: #ffffff;
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
        '¡Proceso exitoso!',
        'Ya puede reprogramar al cliente',
        'success'
    )
</script>
@endif

<script>
$(document).ready(function(){
    $('.btn-eliminar').on('click', function(){
        var id = $(this).data('id');
        var url = "{{ route('admin.asociados.guardarreprogramacionclientebanco', ':id') }}";
        url = url.replace(':id', id);
        $('#deleteForm').attr('action', url);
        $('#deleteModal').modal('show');
    });

    $('#deleteForm').submit(function(e){
        e.preventDefault();
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminara la programacion y podras reprogramarlo",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '¡Si, reprogramar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
{{-- <script>
    function showMotivoField(event) {
        event.preventDefault();
        Swal.fire({
            title: '¿Estás seguro?',
            html: 'Este perfil se eliminará definitivamente <br> Motivo: <input type="text" id="motivoreprogramacion" class="swal2-input">',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '¡Si, eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var motivo = document.getElementById('motivoreprogramacion').value;
                $('.formulario-eliminar').find('#motivoreprogramacion').val(motivo);
                $('.formulario-eliminar')[0].submit();
            }
        });
    }
</script> --}}
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