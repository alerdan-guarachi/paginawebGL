@extends('adminlte::page')

@section('content_header')
    <a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteauditoria', $clienteauditoria) }}">REGRESAR</a>

    <a class="btn btn-sm float-right btn-asignartramite" data-toggle="modal" data-target="#modalasignartramite">ASIGNAR
        SERVICIO</a>

    <h5>SERVICIOS DE:</h5>
    <h3>{{ $clienteauditoria->nombrecompleto }}</h3>
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
                            <th>ID Reg.</th>
                            <th>Servicio</th>
                            <th>Ciudad</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th>Usuario Reg.</th>
                            <th class="text-center">Fecha de Batería:</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tramitesubclientes as $tramitesubcliente)
                            <tr>
                                <td>{{ $tramitesubcliente->id }}</td>
                                <td>{{ $tramitesubcliente->tramite }}</td>
                                <td>{{ $tramitesubcliente->ciudad }}</td>
                                <td>{{ $tramitesubcliente->observaciones }}</td>
                                <td>{{ $tramitesubcliente->estado }}</td>
                                <td>{{ $tramitesubcliente->usuarioregistro }}</td>
                                <td class="text-center">
                                    @if ($tramitesubcliente->fechabateria)
                                        {{ $tramitesubcliente->fechabateria }}
                                    @else
                                        @if (!$tramitesubcliente->fechabateria)
                                            <a class="btn btn-sm btn-asignartramite" data-toggle="modal"
                                                data-target="#modalasignarfecha-{{ $tramitesubcliente->id }}">
                                                ASIGNAR FECHA
                                            </a>

                                            <div class="modal fade" id="modalasignarfecha-{{ $tramitesubcliente->id }}"
                                                tabindex="-1" role="dialog"
                                                aria-labelledby="modalasignarfechaLabel-{{ $tramitesubcliente->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title titulomodal"
                                                                id="modalasignarfechaLabel-{{ $tramitesubcliente->id }}">
                                                                ASIGNAR FECHA</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form
                                                                action="{{ route('admin.asociados.asignarFecha_ITA', $tramitesubcliente->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="col-lg-12">
                                                                    <div class="form-group text-left">
                                                                        {!! Form::label('fechabateria', 'Fecha de batería') !!}
                                                                        <select id="select-fechas" name="fechabateria"
                                                                            class="form-control @error('fechabateria') is-invalid @enderror">
                                                                            <option value="" disabled selected>
                                                                            </option>
                                                                            @foreach ($accionesPorFecha as $fecha => $acciones)
                                                                                <option value="{{ $fecha }}"
                                                                                    {{ old('fechabateria') == $fecha ? 'selected' : '' }}>
                                                                                    {{ $fecha }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('fechabateria')
                                                                            <small
                                                                                class="text-danger">{{ $message }}</small>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <button type="submit"
                                                                    class="btn btn-asignartramite d-block mx-auto mb-3"
                                                                    style="width: fit-content;">ASIGNAR FECHA</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- ASGINAR TRAMITE --}}
    <div class="modal fade" id="modalasignartramite" tabindex="-1" role="dialog"
        aria-labelledby="modalasignartramiteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title titulomodal" id="modalasignartramiteLabel">ASIGNAR SERVICIO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="form-asignar-tramite"
                        action="{{ route('admin.asociados.guardartramiteclienteauditoria', $clienteauditoria) }}" method="POST"
                        enctype="multipart/form-data">
                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                        {!! Form::hidden('clienteauditoriaid', $clienteauditoria->id) !!}
                        {!! Form::hidden('clienteauditorianombre', $clienteauditoria->nombrecompleto) !!}
                        @csrf
                        <div class="col-lg-12">
                            <div class="form-group">
                                {!! Form::label('tramite', 'Servicio') !!}
                                {!! Form::select('tramite', $tramites, null, [
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'maxlength' => '90',
                                ]) !!}
                                @error('tramite')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                {!! Form::label('ciudad', 'Ciudad') !!}
                                {!! Form::select('ciudad', $ciudades, null, [
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'maxlength' => '90',
                                ]) !!}
                                @error('ciudad')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                {!! Form::label('observaciones', 'Observaciones') !!}
                                {!! Form::text('observaciones', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                                @error('observaciones')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <input type="text" class="form-control" id="estado" name="estado" value="PENDIENTE" hidden>

                        <button type="submit" class="btn btn-asignartramite d-block mx-auto mb-3" target="_blank"
                            style="width: fit-content;">ASIGNAR SERVICIO</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <link rel="styleheet" href="/css/admin_custom.css">
    <style>
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

        .btn-asignartramite {
            background-color: #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
            padding: 5px 10px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .btn-asignartramite:hover {
            background-color: #94c93b;
            color: #ffffff;
        }

        h1,
        th {
            color: #94c93b;
            font-family: "Segoe UI";
            font-weight: 900;
        }

        .btn-eliminar {
            background-color: #ffffff;
            color: #ff0000;
            border-color: #ff0000;
            border-radius: 5px;
        }

        .btn-eliminar:hover {
            background-color: #ff0000;
            color: #ffffff;
        }
    </style>
@stop

@section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar') == 'ok')
        <script>
            Swal.fire(
                '¡Eliminado!',
                'El rol se eliminó con éxito',
                'success')
        </script>
    @endif

    <script>
        $('.formulario-eliminar').submit(function(e) {
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

        document.addEventListener("DOMContentLoaded", function() {
            const forms = document.querySelectorAll("form");

            forms.forEach(function(form) {
                form.addEventListener("submit", function(event) {
                    const select = form.querySelector("select[name='fechabateria']");
                    if (select.value === "") {
                        event.preventDefault();
                        alert("Por favor, seleccione una fecha antes de continuar.");
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#form-asignar-tramite').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.pdf_url && response.redirect_url) {
                            $('#modalasignartramite').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Servicio guardado y Etiqueta descargada',
                                text: 'El servicio se ha guardado correctamente.',
                                timer: 3000,
                                showConfirmButton: false
                            });

                            var link = document.createElement('a');
                            link.href = response.pdf_url;
                            link.download =
                            '';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            setTimeout(function() {
                                window.location.href = response.redirect_url;
                            }, 1);
                        } else {
                            alert(
                            'Error al generar el PDF.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert(
                            'Ocurrió un error al procesar el trámite. Por favor, intenta de nuevo.');
                    }
                });
            });
        });
    </script>
@endsection