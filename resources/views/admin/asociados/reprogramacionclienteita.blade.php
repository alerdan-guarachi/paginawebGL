@extends('adminlte::page')

@section('content_header')
    <a class="btn btn-sm float-right btn-regresar"
        href="{{ route('admin.asociados.crearprogramacionclienteita', $cliente) }}">REGRESAR</a>
    <a class="btn btn-sm float-right btn-reprogramacion" data-toggle="modal" data-target="#ventanaModal">REPROGRAMACIONES</a>
    <h5>REPROGRAMAR ACCIONES DE:</h5>
    <h3>{{ $cliente->nombrecompleto }}</h3>
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
                        <form action="{{ route('buscarprogramacionclienteita', $cliente) }}" method="get"
                            class="form-inline">
                            <div class="flex-grow-1">
                                <select name="buscarpor" class="form-control mr-sm-2">
                                    <option value="" disabled selected>Fecha de Bateria</option>
                                    @foreach ($fechas as $fecha)
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

            <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
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
                                <td>{{ $programacionsubcliente->accionnombre }}</td>
                                <td>{{ $programacionsubcliente->proveedornombre }}</td>
                                <td>{{ $programacionsubcliente->fechaasignada }}</td>
                                <td>{{ $programacionsubcliente->horadesde }} - {{ $programacionsubcliente->horahasta }}
                                </td>
                                <td width="10px">
                                    <abbr title="Reprogramar">
                                        <button type="button" class="btn btn-sm fas fa-list-alt btn-eliminar"
                                            data-id="{{ $programacionsubcliente->id }}"
                                            data-proveedor="{{ $programacionsubcliente->proveedornombre }}"
                                            data-toggle="modal" data-target="#deleteModal"></button>
                                    </abbr>
                                </td>
                            </tr>
                        @endforeach

                        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog"
                            aria-labelledby="deleteModalLabel" aria-hidden="true">
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
                                                <input type="text" name="motivoreprogramacion" id="motivoreprogramacion"
                                                    class="form-control" required>
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('proveedornombre', 'Proveedor:') !!}
                                                <input type="text" name="proveedornombre" id="proveedornombre" class="form-control" readonly>
                                            </div>


                                            <!-- Campo dinámico para ingresar texto si es "Proveedor Ajeno" -->
                                            <div class="form-group" id="proveedorajeno-container" style="display: none;">
                                                {!! Form::label('proveedorajeno', 'Ingrese el nombre del Proveedor Ajeno:') !!}
                                                {!! Form::text('proveedorajeno', null, [
                                                    'class' => 'form-control',
                                                    'id' => 'proveedorajeno',
                                                ]) !!}
                                                @error('proveedorajeno')
                                                    <small
                                                        class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <!-- Campo para la fecha de reprogramación -->
                                            <div class="form-group">
                                                {!! Form::label('fechaasignada', 'Fecha de Reprogramación:') !!}
                                                {!! Form::date('fechaasignada', null, ['class' => 'form-control', 'required' => true]) !!}
                                                @error('fechaasignada')
                                                    <small
                                                        class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <!-- Campo para la hora desde -->
                                            <div class="form-group">
                                                {!! Form::label('horadesde', 'Hora Desde:') !!}
                                                {!! Form::time('horadesde', null, ['class' => 'form-control', 'required' => true]) !!}
                                                @error('horadesde')
                                                    <small
                                                        class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <!-- Campo para la hora hasta -->
                                            <div class="form-group">
                                                {!! Form::label('horahasta', 'Hora Hasta:') !!}
                                                {!! Form::time('horahasta', null, ['class' => 'form-control', 'required' => true]) !!}
                                                @error('horahasta')
                                                    <small
                                                        class="text-danger fas fa-exclamation-circle">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-cancelar"
                                                data-dismiss="modal">CANCELAR</button>
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
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
            padding: 5px 10px;
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
        h1,
        th {
            color: #94c93b;
            font-family: "Segoe UI";
            font-weight: 900;
        }
        h5 {
            color: #94c93b;
            font-family: "Segoe UI";
            font-weight: 500;
            margin-bottom: 0%;
        }
        h3 {
            color: #94c93b;
            font-family: "Segoe UI";
            font-weight: 1000;
        }
        .btn-buscar {
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
        }
        .btn-buscar:hover {
            background-color: #faa625;
            color: #ffffff;
        }
        .btn-eliminar {
            background-color: #ffffff;
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
            padding: 5px 10px;
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

    @if (session('eliminar') == 'ok')
        <script>
            Swal.fire(
                '¡Proceso exitoso!',
                'Ya puede reprogramar al cliente',
                'success'
            )
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $('.btn-eliminar').on('click', function() {
                var id = $(this).data('id');
                var proveedor = $(this).data('proveedor');
                console.log('ID:', id);
                console.log('Proveedor:', proveedor);
                var url = "{{ route('admin.asociados.guardarreprogramacionclienteita', ':id') }}";
                url = url.replace(':id', id);
                $('#deleteForm').attr('action', url);

                if (proveedor) {
                    $('#proveedornombre').val(proveedor).change();
                } else {
                    $('#proveedornombre').val('').change();
                }

                $('#deleteModal').modal('show');
            });

            $('#deleteForm').submit(function(e) {
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

        document.addEventListener('DOMContentLoaded', function() {
            const selectProveedor = document.getElementById('proveedornombre');
            const proveedorAjenoContainer = document.getElementById('proveedorajeno-container');
            const proveedorAjenoInput = document.getElementById('proveedorajeno');

            function toggleProveedorAjeno() {
                if (selectProveedor.value === 'PROVEEDOR AJENO') {
                    proveedorAjenoContainer.style.display = 'block';
                    proveedorAjenoInput.required = true;
                } else {
                    proveedorAjenoContainer.style.display = 'none';
                    proveedorAjenoInput.required = false;
                    proveedorAjenoInput.value = '';
                }
            }

            selectProveedor.addEventListener('change', toggleProveedorAjeno);

            toggleProveedorAjeno();
        });
    </script>

@endsection
