@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn btn-sm float-right btn-asignartramite" data-toggle="modal" data-target="#modalasignartramite">ASIGNAR SERVICIO</a>
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
            }, 3000);
        </script>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID_Reg.</th>
                            <th>Servicio</th>
                            <th>Ciudad</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th class="text-center">Fecha_Bat.:</th>
                            <th>Usuario_Registro</th>
                            <th class="text-center">Tramite_Finalizado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tramitesubclientes as $tramitesubcliente)
                            <tr>
                                <td>{{ $tramitesubcliente->id }}</td>
                                <td>
                                    <a href="{{ route('admin.asociados.descargarEtiqueta', [
                                            'id' => $tramitesubcliente->id,
                                            'tramite' => rawurlencode($tramitesubcliente->tramite)
                                        ]) }}" 
                                        class="btn btn-sm btn-descargar"
                                        title="DESCARGAR ETIQUETA"
                                        target="_blank">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    {{ $tramitesubcliente->tramite }}
                                </td>
                                <td>{{ $tramitesubcliente->ciudad }}</td>
                                <td>{{ $tramitesubcliente->observaciones ?? 0 }}</td>
                                <td>{{ $tramitesubcliente->estado }}</td>
                                <td class="text-center">
                                    @php
                                        $tramitesSinFecha = [
                                            'JUBILACIÓN',
                                            'PENSIÓN POR MUERTE',
                                            'RETIRO DE APORTES TOTAL',
                                            'RETIRO DE APORTES PARCIAL',
                                            'MASA HEREDITARIA',
                                            'COMPENSACIÓN DE COTIZACIONES (SENASIR)'
                                        ];
                                    @endphp

                                    @if(in_array($tramitesubcliente->tramite, $tramitesSinFecha))
                                        <span class="badge badge-success">NO REQUIERE</span>
                                    @else
                                        @if ($tramitesubcliente->fechabateria)
                                            {{ $tramitesubcliente->fechabateria }}
                                        @else
                                            @if (!$tramitesubcliente->fechabateria)
                                                <a class="btn btn-sm btn-asignartramite2" data-toggle="modal"
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
                                                                    id="modalasignarfechaLabel-{{ $tramitesubcliente->id }}" style="font-weight: 700">
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
                                                                        class="btn btn-sm btn-asignartramite2">ASIGNAR FECHA
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $tramitesubcliente->usuarioregistro }}</td>
                                <td class="text-center">
                                    {{-- SI YA EXISTE UN ARCHIVO EN DRIVE --}}
                                    @if ($tramitesubcliente->archivofinalizado)
                                            <a href="{{ $tramitesubcliente->archivofinalizado }}" 
                                            target="_blank" 
                                            class="btn btn-vertramite btn-sm"
                                            title="VER TRÁMITE">
                                            <i class="fas fa-file-alt"></i>
                                            </a>
                                        @if ($tramitesubcliente->historiafinalizado)
                                            <a href="{{ $tramitesubcliente->historiafinalizado }}" 
                                            target="_blank" 
                                            class="btn btn-verhistoria btn-sm"
                                            title="VER HISTORIA">
                                            <i class="fas fa-notes-medical"></i>
                                            </a>
                                        @endif

                                        @if ($tramitesubcliente->requisitofinalizado)
                                            <a href="{{ $tramitesubcliente->requisitofinalizado }}" 
                                            target="_blank" 
                                            class="btn btn-verrequisito btn-sm"
                                            title="VER REQUISITOS">
                                            <i class="fas fa-user"></i>
                                            </a>
                                        @endif
                                    @else
                                        {{-- BOTÓN PARA ABRIR MODAL DE SUBIR ARCHIVO --}}
                                        <a class="btn btn-subirarchivo btn-sm" data-toggle="modal"
                                            data-target="#modalSubirArchivo-{{ $tramitesubcliente->id }}">
                                            SUBIR ARCHIVOS
                                        </a>

                                        {{-- MODAL --}}
                                        <div class="modal fade" id="modalSubirArchivo-{{ $tramitesubcliente->id }}"
                                            tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">SUBIR ARCHIVOS</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form action="{{ route('admin.asociados.subirArchivoDrive', $tramitesubcliente->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf

                                                            <div class="form-group">
                                                                <label>Proceso de Trámite:</label>
                                                                <input type="file" class="form-control" name="archivo" required>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Historia Clinica:</label>
                                                                <input type="file" class="form-control" name="archivo2">
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Requisitos Personales:</label>
                                                                <input type="file" class="form-control" name="archivo3">
                                                            </div>

                                                            <button type="submit" class="btn btn-subirarchivo btn-sm">
                                                                GUARDAR
                                                            </button>
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
    {{-- ASIGNAR TRAMITE --}}
    <div class="modal fade" id="modalasignartramite" tabindex="-1" role="dialog"
        aria-labelledby="modalasignartramiteLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title titulomodal" id="modalasignartramiteLabel" style="font-weight: 700">ASIGNAR SERVICIO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-asignar-tramite"
                        action="{{ route('admin.asociados.guardartramiteclienteita', $cliente) }}" method="POST"
                        enctype="multipart/form-data">
                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                        {!! Form::hidden('clienteitaid', $cliente->id) !!}
                        {!! Form::hidden('clienteitanombre', $cliente->nombrecompleto) !!}
                        @csrf
                        <div class="col-lg-12">
                            <div class="form-group">
                                {!! Form::label('tramite', 'Servicio') !!}
                                {!! Form::select('tramite', $tramitesDisponibles, null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Seleccione un trámite',
                                    'maxlength' => '90',
                                    'id' => 'tramite'
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
                                <select id="observaciones_select" class="form-control d-none" disabled required></select>
                                <input type="text" id="observaciones_text" class="form-control" placeholder="" maxlength="90" required>
                                <input type="hidden" name="observaciones" id="observaciones_hidden">
                                @error('observaciones')
                                    <small class="text-danger fas fa-exclamation-circle">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const tramite = document.getElementById("tramite");
                                const obsText = document.getElementById("observaciones_text");
                                const obsSelect = document.getElementById("observaciones_select");
                                const obsHidden = document.getElementById("observaciones_hidden");

                                const opcionesPorTramite = {
                                    "JUBILACIÓN": [
                                        "PENSIÓN DE VEJEZ",
                                        "PENSIÓN SOLIDARIA DE VEJEZ",
                                        "PENSIÓN ANTICIPADA DE VEJEZ"
                                    ],
                                    "RETIRO DE APORTES TOTAL": [
                                        "MENOR A 120 APORTES",
                                        "MAYOR A 65 AÑOS"
                                    ],
                                    "RETIRO DE APORTES PARCIAL": [
                                        "MENOR A 120 APORTES",
                                        "MAYOR A 65 AÑOS",
                                        "RETIROS PARCIALES",
                                        "RETIROS TEMPORALES"
                                    ]
                                };

                                function syncObservaciones() {
                                    const tramiteValue = tramite.value;

                                    if (opcionesPorTramite[tramiteValue]) {
                                        obsSelect.innerHTML = '<option value="">Seleccione...</option>';
                                        opcionesPorTramite[tramiteValue].forEach(op => {
                                            const opt = document.createElement("option");
                                            opt.value = op;
                                            opt.textContent = op;
                                            obsSelect.appendChild(opt);
                                        });

                                        obsText.classList.add("d-none");
                                        obsText.disabled = true;
                                        obsText.removeAttribute("required");

                                        obsSelect.classList.remove("d-none");
                                        obsSelect.disabled = false;
                                        obsSelect.setAttribute("required", "required");

                                        obsHidden.value = obsSelect.value;
                                    } else {
                                        obsSelect.classList.add("d-none");
                                        obsSelect.disabled = true;
                                        obsSelect.removeAttribute("required");

                                        obsText.classList.remove("d-none");
                                        obsText.disabled = false;
                                        obsText.setAttribute("required", "required");

                                        obsHidden.value = obsText.value;
                                    }
                                }

                                tramite.addEventListener("change", syncObservaciones);
                                obsText.addEventListener("input", () => obsHidden.value = obsText.value);
                                obsSelect.addEventListener("change", () => obsHidden.value = obsSelect.value);

                                syncObservaciones();
                            });
                        </script>

                        <input type="text" class="form-control" id="estado" name="estado" value="PENDIENTE" hidden>

                        <button id="btnAsignar" type="submit" class="btn btn-asignartramite2 d-block mx-auto mb-3" style="width: fit-content;">ASIGNAR SERVICIO</button>
                    </form>
                    <script>
                        document.getElementById('form-asignar-tramite').addEventListener('submit', function(e) {
                            const btn = document.getElementById('btnAsignar');
                            btn.innerText = 'GUARDANDO...';
                            btn.disabled = true;
                            btn.style.pointerEvents = 'none';
                        });
                    </script>
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
    .btn-descargar {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 1px 3px;
    }
    .btn-descargar:hover {
        background-color: #faa625;
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
    .btn-verarchivo {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 5px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-verarchivo:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-vertramite {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-vertramite:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-verhistoria {
        background-color: #ffffff;
        color: #226acf;
        border-color: #226acf;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-verhistoria:hover {
        background-color: #226acf;
        color: #ffffff;
    }
    .btn-verrequisito {
        background-color: #ffffff;
        color: #8c28f0;
        border-color: #8c28f0;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-verrequisito:hover {
        background-color: #8c28f0;
        color: #ffffff;
    }
    .btn-subirarchivo {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 5px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .btn-subirarchivo:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-asignartramite2 {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 2px 5px;
    }
    .btn-asignartramite2:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    h1, th {
        color: #94c93b;
        font-family: "Segoe UI";
        font-weight: 900;
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
                            title: 'SERVICIO GUARDADO',
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
                        }, 3000);
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