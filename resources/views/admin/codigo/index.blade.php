@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <a class="btn btn-codigos btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">CODIGOS ASIGNADOS</a>
    <h1>ASIGNACIÓN DE CÓDIGOS</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
<style>
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
    .btn-codigos {
        background-color:  #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-codigos:hover {
        background-color: #faa625;
        color: #ffffff;
        }
    .btn-regresar {
        background-color:  #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 5px 10px;
        }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
        }
    .table td {
        padding: 5px 10px;
    }
    .truncar {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .truncar2 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100px;
    }
    .custom2-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 20px;
        margin-left: 10px;
        margin-right: 10px;
    }
    .custom2-button:hover {
        background-color: #faa625;
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
            }, 3000);
        </script>
    @endif

    @if (session('success'))
        <div id="alert-success" class="alert alert-success">
            <strong>{{ session('success') }}</strong>
        </div>
        <script>
            setTimeout(function() {
                $('#alert-success').fadeOut('fast');
            }, 3000); 
        </script>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    {!! Form::open(['route' => 'admin.codigo.store', 'method' => 'POST', 'id' => 'formCodigo']) !!}
                    <div class="card">
                        <div class="card-body">
                            <h4>ASIGNAR CÓDIGO</h4>
                            <div class="row">
                                {!! Form::hidden('solicitud_id', null, ['id' => 'solicitud_id']) !!}
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        {!! Form::label('usuarioSolicitante', 'Usuario Solicitante:') !!}
                                        {!! Form::select('usuarioSolicitante', $usuarios, null, [
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => '',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-lg-12" hidden>
                                    <div class="form-group">
                                        {!! Form::label('usuarioAutorizador', 'Usuario Autorizador:') !!}
                                        {!! Form::text('usuarioAutorizador', auth()->user()->name, [
                                            'class' => 'form-control',
                                            'maxlength' => '120',
                                            'readonly',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12" hidden>
                                    <div class="form-group">
                                        {!! Form::label('codigo', 'Código:') !!}
                                        {!! Form::text('codigo', $codigoGenerado, [
                                            'class' => 'form-control',
                                            'maxlength' => '7',
                                            'readonly',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        {!! Form::label('permisoSolicitadoDescripcion', 'Permiso Solicitado:') !!}
                                        {!! Form::select('permisoSolicitado', $permisosSolicitados, null, [
                                            'class'       => 'form-control',
                                            'required'    => true,
                                            'placeholder' => '',
                                            'id'          => 'permisoSolicitado',
                                        ]) !!}
                                        {!! Form::hidden('permisoSolicitadoNombre', '', ['id' => 'permisoSolicitadoNombre']) !!}
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        {!! Form::label('fechaSolicitada', 'Fecha Solicitada:') !!}
                                        {!! Form::date('fechaSolicitada', \Carbon\Carbon::now()->format('Y-m-d'), [
                                            'class' => 'form-control',
                                            'required',
                                            'readonly',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12" id="grupoClienteId" style="display: none;"> 
                                    <div class="form-group">
                                        {!! Form::label('clienteid', 'Item/Id:') !!}
                                        <div class="input-group">
                                            {!! Form::text('clienteid', null, [
                                                'class' => 'form-control',
                                                'id'    => 'clienteid',
                                                'required'    => true,
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12" id="grupoTiempoLimite" style="display: none;">
                                    <div class="form-group">
                                        {!! Form::label('tiempoLimiteMinutos', 'Tiempo Límite (en minutos):') !!}
                                        <div class="input-group">
                                            {!! Form::number('tiempoLimiteMinutos', null, [
                                                'class'    => 'form-control',
                                                'id'       => 'tiempoLimiteMinutos',
                                                'min'      => '0',
                                                'placeholder' => '',
                                                'required'    => true,
                                            ]) !!}
                                            <div class="input-group-append">
                                                <span class="input-group-text">min</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12"> 
                                    <div class="form-group">
                                        {!! Form::label('motivo', 'Motivo:') !!}
                                        <div class="input-group">
                                            {!! Form::text('motivo', null, [
                                                'class' => 'form-control',
                                                'id'    => 'motivo',
                                                'required'    => true,
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const selPermiso  = document.getElementById('permisoSolicitado');
                                        const inpCliente  = document.getElementById('clienteid');
                                        const inpTiempo   = document.getElementById('tiempoLimiteMinutos');
                                        const hidNombre   = document.getElementById('permisoSolicitadoNombre');
                                        const divCliente  = document.getElementById('grupoClienteId');
                                        const divTiempo   = document.getElementById('grupoTiempoLimite');
                                        const tipo1 = ['CREAR BATERIA CLIENTE ITA', 'CREAR BATERIA CLIENTE AUDITORIA'];
                                        const tipo2 = ['CONCEDER DESCUENTO INGRESO', 'CAMBIAR FECHA DE CAJA INGRESO', 'CAMBIAR FECHA DE CAJA EGRESO', 'CAMBIAR STOCK DE INVENTARIO', 'MODIFICAR FECHA DE PROCEDIMIENTO TRAMITE', 'EDITAR ARCHIVO DE PROCEDIMIENTO TRAMITE', 'DAR CONTINUIDAD DE PROCEDIMIENTO TRAMITE', 'MODIFICAR RAZON SOCIAL DE FACTURAS IMPUESTOS'];
                                        const tipo3 = ['DESBLOQUEAR CAJA', 'ADELANTO DE VACACIONES', 'DESBLOQUEAR SECCIÓN DE PRESTACIONES'];

                                        selPermiso.addEventListener('change', function(e) {
                                            const option = e.target.options[e.target.selectedIndex];
                                            if (!option || !option.text.trim()) return;
                                            const texto = option.text.trim();
                                            hidNombre.value = texto;

                                            // Oculta todo
                                            divCliente.style.display = 'none';
                                            divTiempo.style.display  = 'none';

                                            // Reset
                                            inpCliente.value = '';
                                            inpTiempo.value  = '';

                                            if (tipo1.includes(texto)) {
                                                divCliente.style.display = 'block';
                                                divTiempo.style.display  = 'block';

                                            } else if (tipo2.includes(texto)) {
                                                divCliente.style.display = 'block';

                                                if (!inpTiempo.value) {
                                                    inpTiempo.value = 1;
                                                }

                                            } else if (tipo3.includes(texto)) {
                                                inpCliente.value = 0;
                                                inpTiempo.value = 1;
                                            }
                                        });
                                    });

                                </script>
                            </div>
                        </div>
                    </div>
                    {!! Form::submit('GENERAR CÓDIGO', ['class' => 'btn btn-sm btn-crear']) !!}
                    {!! Form::close() !!}
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h4>CÓDIGOS SOLICITADOS</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sel.</th>
                                            <th>ID</th>
                                            <th>Solicitante</th>
                                            <th>Fecha_Solicitado</th>
                                            <th>Item/Id</th>
                                            <th>Permiso_Solicitado</th>
                                            <th>Limite</th>
                                            <th>Motivo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($solicitudcodigos as $solicitudcodigo)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="checkSolicitud"
                                                        data-id="{{ $solicitudcodigo->id }}"
                                                        data-usuario-id="{{ $solicitudcodigo->usuarioSolicitanteId ?? $solicitudcodigo->usuario_id }}"
                                                        data-usuario="{{ $solicitudcodigo->usuarioSolicitante }}"
                                                        data-permiso="{{ $solicitudcodigo->permisoSolicitado }}"
                                                        data-permiso-texto="{{ $descripcionesPermisos[$solicitudcodigo->permisoSolicitado] ?? '' }}"
                                                        data-cliente="{{ $solicitudcodigo->clienteid }}"
                                                        data-tiempo="{{ $solicitudcodigo->tiempoLimite }}"
                                                        data-motivo="{{ $solicitudcodigo->motivo }}"
                                                    >
                                                </td>
                                                <td>{{$solicitudcodigo->id}}</td>
                                                <td title="{{$solicitudcodigo->usuarioSolicitante}}" class="truncar">{{$solicitudcodigo->usuarioSolicitante}}</td>
                                                <td>{{ \Carbon\Carbon::parse($solicitudcodigo->fechaSolicitada)->format('Y-m-d') }}</td>
                                                <td>{{$solicitudcodigo->clienteid}}</td>
                                                <td>
                                                    {{ $descripcionesPermisos[$solicitudcodigo->permisoSolicitado] ?? 'Descripción no disponible' }}
                                                </td>
                                                <td>
                                                    {{ $solicitudcodigo->tiempoLimite }}
                                                    {{ $solicitudcodigo->tiempoLimite == 1 ? 'VEZ' : 'MIN' }}
                                                </td>
                                                <td>{{$solicitudcodigo->motivo}}</td>
                                                <td>
                                                    @if($solicitudcodigo->estado == 'solicitado')
                                                        <span class="badge badge-primary">SOLICITADO</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">
                                                    NO HAY SOLICITUDES PENDIENTES
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <script>
                                   function toggleFormulario(bloquear) {
                                        const form = document.getElementById('formCodigo');
                                        const campos = form.querySelectorAll('input:not([type="submit"]), select');

                                        campos.forEach(campo => {
                                            if (campo.name === 'solicitud_id') return;

                                            campo.readOnly = bloquear && campo.tagName === 'INPUT';
                                            campo.style.pointerEvents = bloquear ? 'none' : 'auto';
                                            campo.style.backgroundColor = bloquear ? '#e9ecef' : '';
                                        });
                                    }

                                    function limpiarFormulario() {
                                        const form = document.querySelector('form');

                                        form.reset();

                                        // limpiar campos manuales
                                        document.getElementById('clienteid').value = '';
                                        document.getElementById('tiempoLimiteMinutos').value = '';
                                        document.getElementById('motivo').value = '';
                                        document.getElementById('permisoSolicitadoNombre').value = '';
                                        document.getElementById('solicitud_id').value = '';

                                        // ocultar dinámicos
                                        document.getElementById('grupoClienteId').style.display = 'none';
                                        document.getElementById('grupoTiempoLimite').style.display = 'none';
                                    }


                                    document.addEventListener('DOMContentLoaded', function() {

                                        const checks = document.querySelectorAll('.checkSolicitud');

                                        checks.forEach(chk => {
                                            chk.addEventListener('change', function() {

                                                // Solo uno activo
                                                checks.forEach(c => { if (c !== chk) c.checked = false });

                                                // =========================
                                                // ❌ SI SE DESMARCA
                                                // =========================
                                                if (!chk.checked) {
                                                    toggleFormulario(false); // desbloquear
                                                    limpiarFormulario();
                                                    return;
                                                }

                                                // =========================
                                                // ✅ SI SE SELECCIONA
                                                // =========================
                                                const texto     = chk.dataset.permisoTexto;
                                                const cliente   = chk.dataset.cliente;
                                                const tiempo    = chk.dataset.tiempo;
                                                const motivo    = chk.dataset.motivo;
                                                const nombreUsuario = chk.dataset.usuario;

                                                // 🔹 USUARIO
                                                const selectUsuario = document.querySelector('[name="usuarioSolicitante"]');

                                                for (let i = 0; i < selectUsuario.options.length; i++) {
                                                    if (selectUsuario.options[i].text.trim() === nombreUsuario.trim()) {
                                                        selectUsuario.selectedIndex = i;
                                                        break;
                                                    }
                                                }

                                                // 🔹 PERMISO
                                                const selectPermiso = document.getElementById('permisoSolicitado');

                                                for (let i = 0; i < selectPermiso.options.length; i++) {
                                                    if (selectPermiso.options[i].text.trim() === texto.trim()) {
                                                        selectPermiso.selectedIndex = i;
                                                        break;
                                                    }
                                                }

                                                selectPermiso.dispatchEvent(new Event('change'));

                                                // 🔹 CAMPOS
                                                document.getElementById('permisoSolicitadoNombre').value = texto;
                                                document.getElementById('clienteid').value = cliente;
                                                document.getElementById('tiempoLimiteMinutos').value = tiempo;
                                                document.getElementById('motivo').value = motivo;
                                                document.getElementById('solicitud_id').value = chk.dataset.id;

                                                // 🔒 AHORA SÍ BLOQUEAR
                                                toggleFormulario(true);
                                            });
                                        });

                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">CODIGOS GENERADOS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Solicitante</th>
                                <th>Codigo</th>
                                <th>Fecha_Solicitud</th>
                                <th>Permiso_Solicitado</th>
                                <th>Motivo</th>
                                <th>Item/Id</th>
                                <th>Limite</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registroscodigos as $registroscodigo)
                                <tr>
                                    <td>{{$registroscodigo->id}}</td>
                                    <td title="{{$registroscodigo->usuarioSolicitante}}" class="truncar">{{$registroscodigo->usuarioSolicitante}}</td>
                                    <td>{{$registroscodigo->codigo}}</td>
                                    <td>{{ \Carbon\Carbon::parse($registroscodigo->fechaSolicitada)->format('Y-m-d') }}</td>
                                    <td>
                                        {{ $descripcionesPermisos[$registroscodigo->permisoSolicitado] ?? 'Descripción no disponible' }}
                                    </td>
                                    <td>{{$registroscodigo->motivo ?? 0}}</td>
                                    <td>{{$registroscodigo->clienteid}}</td>
                                    <td>
                                        {{ $registroscodigo->tiempoLimite }}
                                        {{ $registroscodigo->tiempoLimite == 1 ? 'VEZ' : 'MIN' }}
                                    </td>
                                    <td>
                                        @if($registroscodigo->estado == 'expirado')
                                        <span class="badge badge-danger">EXPIRADO</span>
                                        @elseif($registroscodigo->estado == 'pendiente')
                                        <span class="badge badge-warning">PENDIENTE</span>
                                        @elseif($registroscodigo->estado == 'activo')
                                        <span class="badge badge-danger">EXPIRADO</span>
                                        @else
                                        <span class="badge badge-secondary">{{$registroscodigo->estado}}</span>
                                        @endif
                                    </td>
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
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
