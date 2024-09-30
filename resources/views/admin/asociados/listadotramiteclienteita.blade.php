@extends('adminlte::page')

@section('content_header')
    <a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>

    <a class="btn btn-sm float-right btn-asignartramite" data-toggle="modal" data-target="#modalasignartramite">ASIGNAR
        SERVICIO</a>

    <h5>SERVICIOS DE:</h5>
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
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            {{-- <th>Fecha de Bateria</th> --}}
                            {{-- <th>Apoderado</th> --}}
                            <th>Ciudad</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th>Fecha de Batería:</th>
                            <th class="text-center">Asignar Fecha de Bateria</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tramitesubclientes as $tramitesubcliente)
                            <tr>
                                <td>
                                    {{ $tramitesubcliente->tramite }} <br>
                                </td>

                                <td>{{ $tramitesubcliente->ciudad }}</td>
                                <td>{{ $tramitesubcliente->observaciones }}</td>
                                <td>{{ $tramitesubcliente->estado }}</td>

                                <td class="text-center">
                                    @if ($tramitesubcliente->fechabateria)
                                        {{ $tramitesubcliente->fechabateria }}
                                    @else
                                        No Tiene Fecha Asignada
                                    @endif
                                </td>

                                <!-- Mostrar el botón "ASIGNAR FECHA" solo si no hay fecha asignada -->
                                <td class="text-center">
                                    @if (!$tramitesubcliente->fechabateria)
                                        <a class="btn btn-sm btn-asignartramite" data-toggle="modal"
                                            data-target="#modalasignarfecha-{{ $tramitesubcliente->id }}">
                                            ASIGNAR FECHA
                                        </a>

                                        <!-- Modal de asignar fecha para cada trámite sin fecha asignada -->
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
                                                                        <option value="" disabled selected></option>
                                                                        @foreach ($accionesPorFecha as $fecha => $acciones)
                                                                            <option value="{{ $fecha }}"
                                                                                {{ old('fechabateria') == $fecha ? 'selected' : '' }}>
                                                                                {{ $fecha }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('fechabateria')
                                                                        <small class="text-danger">{{ $message }}</small>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-info d-block mx-auto mb-3"
                                                                style="width: fit-content;">ASIGNAR FECHA</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

                    <form action="{{ route('admin.asociados.guardartramiteclienteita', $cliente) }}" method="POST"
                        enctype="multipart/form-data">
                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                        {!! Form::hidden('clienteitaid', $cliente->id) !!}
                        {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
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

                        {{-- <div class="col-lg-12">
                            <div class="form-group">
                                {!! Form::label('fechabateria', 'Fecha de bateria') !!}
                                <select id="select-fechas" name="fechabateria" class="form-control">
                                    <option value="" disabled selected></option>
                                    @foreach ($accionesPorFecha as $fecha => $acciones)
                                        <option value="{{ $fecha }}">{{ $fecha }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}


                        {{-- <div class="col-lg-12"> 
                        <div class="form-group">
                            {!! Form::label('usuarioasignado', 'Apoderado') !!}
                            {!! Form::select('usuarioasignado', $apoderados, $apoderadoSiguiente, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90']) !!}
                            @error('usuarioasignado')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div> --}}

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

                        <button type="submit" class="btn btn-info d-block mx-auto mb-3" target="_blank"
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
            padding: 10px 10px;
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
            padding: 10px 20px;
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

        .btn-editar {
            background-color: #ffffff;
            color: #0400ff;
            border-color: #0400ff;
            border-radius: 5px;
        }

        .btn-editar:hover {
            background-color: #0400ff;
            color: #ffffff;
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

        .btn-crear {
            background-color: #ffffff;
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
            background-color: #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
        }

        .btn-buscar:hover {
            background-color: #faa625;
            color: #ffffff;
        }

        .btn-info {
            background-color: #ffffff;
            /* Fondo blanco */
            color: #17a2b8;
            /* Texto azul */
            border-color: #17a2b8;
            /* Borde azul */
            border-radius: 5px;
            /* Redondeo de bordes */
            padding: 10px 20px;
            /* Espaciado interno */
            transition: background-color 0.3s ease, color 0.3s ease;
            /* Transición suave */
        }

        .btn-info:hover {
            background-color: #17a2b8;
            /* Fondo azul cuando el mouse está encima */
            color: #ffffff;
            /* Texto blanco */
            border-color: #17a2b8;
            /* Borde azul */
        }

        /* Agrega este estilo adicional si deseas ajustar el formato de la fecha */
        .text-muted {
            font-size: 12px;
            color: #6c757d;
            /* Color gris tenue */
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
                        event.preventDefault(); // Evita que el formulario se envíe
                        alert("Por favor, seleccione una fecha antes de continuar.");
                    }
                });
            });
        });
    </script>
@endsection
