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
            }, 3000);
        </script>
    @endif
    <div class="card">
        <div class="card-body">
            <nav class="navbar navbar-expand-lg float-right">
                <div class="container-fluid">
                    <div class="d-flex flex-wrap align-items-center">
                        <form action="{{ route('buscarprogramacionclienteita', $cliente) }}" method="get" class="form-inline">
                            <div class="flex-grow-1">
                                <select name="buscarpor" class="form-control mr-sm-2">
                                    <option value="" disabled {{ request('buscarpor') ? '' : 'selected' }}>
                                        Fecha de Bateria
                                    </option>

                                    @foreach ($fechas as $fecha)
                                        <option value="{{ $fecha }}"
                                            {{ request('buscarpor') == $fecha ? 'selected' : '' }}>
                                            {{ $fecha }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="total" id="total" value="{{ $total }}">
                            <button id="btn-buscar" class="btn btn-buscar my-2 my-sm-0" type="submit">BUSCAR</button>
                        </form>
                    </div>
                </div>
            </nav>

            <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                            <th>Estudio/Especialidad</th>
                                            <th>Motivo</th>
                                            <th>Fecha_Batería</th>
                                            <th>Fecha_Hora_Reprog.</th>
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
                            <button type="button" class="btn btn-cerrar" data-dismiss="modal">CERRAR</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sel.</th>
                            <th>Estudio/Especialidad</th>
                            <th>Proveedor</th>
                            <th>Fecha programada</th>
                            <th>Hora programada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($programacionsubclientes as $item)
                            <tr>
                                <td>
                                    <input type="checkbox"
                                        class="check-reprogramar"
                                        value="{{ $item->id }}"
                                        data-proveedor="{{ $item->proveedornombre }}">
                                </td>
                                <td>{{ $item->accionnombre }}</td>
                                <td>{{ $item->proveedornombre }}</td>
                                <td>{{ $item->fechaasignada }}</td>
                                <td>{{ $item->horadesde }} - {{ $item->horahasta }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="button"
                        class="btn btn-sm btn-reprogramacion mt-3"
                        id="btnReprogramarSeleccionados"
                        disabled
                        data-toggle="modal"
                        data-target="#reprogramarModal">
                    REPROGRAMAR SELECCIONADOS
                </button>
                <div class="modal fade" id="reprogramarModal">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form action="{{ route('admin.reprogramar.multiple') }}" method="POST">
                                @csrf
                                <input type="hidden" name="ids" id="idsSeleccionados">
                                <input type="hidden" name="proveedornombre" id="proveedorSeleccionado">
                                {!! Form::hidden('usuarioactualizacion', auth()->user()->name) !!}

                                <div class="modal-header">
                                    <h5 class="modal-title">REPROGRAMACIÓN</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Proveedor</label>
                                        <input type="text" class="form-control" id="proveedorTexto" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Motivo</label>
                                        <input type="text" name="motivoreprogramacion" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Fecha</label>
                                        <input type="date" name="fechaasignada" class="form-control" required>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <label>Hora Desde</label>
                                            <input type="time" name="horadesde" class="form-control" required>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label>Hora Hasta</label>
                                            <input type="time" name="horahasta" class="form-control" required>
                                        </div>
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
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const checks = document.querySelectorAll('.check-reprogramar');
                        const btn = document.getElementById('btnReprogramarSeleccionados');

                        function validarSeleccion() {
                            const seleccionados = [...checks].filter(c => c.checked);

                            if (seleccionados.length === 0) {
                                btn.disabled = true;
                                return;
                            }

                            const proveedor = seleccionados[0].dataset.proveedor;
                            const mismoProveedor = seleccionados.every(c => c.dataset.proveedor === proveedor);

                            if (!mismoProveedor) {
                                alert('SOLO PUEDES SELECCIONAR PROGRAMACIONES DEL MISMO MÉDICO');
                                checks.forEach(c => c.checked = false);
                                btn.disabled = true;
                                return;
                            }

                            btn.disabled = false;

                            document.getElementById('proveedorTexto').value = proveedor;
                            document.getElementById('proveedorSeleccionado').value = proveedor;
                            document.getElementById('idsSeleccionados').value =
                                seleccionados.map(c => c.value).join(',');
                        }

                        checks.forEach(c => c.addEventListener('change', validarSeleccion));
                    });
                </script>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="styleheet" href="/css/admin_custom.css">
    <style>
        .table td {
            padding: 3px 10px;
        }
        .btn-cerrar {
            background-color: #ffffff;
            color: #d12a2a;
            border-color: #d12a2a;
            border-radius: 5px;
            padding: 5px 10px;
        }
        .btn-cerrar:hover {
            background-color: #d12a2a;
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
            font-weight: 800;
            font-size: 23px;
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
