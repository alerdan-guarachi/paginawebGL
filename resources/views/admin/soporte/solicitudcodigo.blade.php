@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    {{-- <a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.acciones.index') }}">REGRESAR</a> --}}
    {{-- <a class="btn btn-codigos btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">CODIGOS GENERADOS</a> --}}
    <h1>SOLICITUD DE CÓDIGOS</h1>
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
        margin-right: 10px;
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
            }, 5000);
        </script>
    @endif

    @if (session('success'))
        <div id="alert-success" class="alert alert-success">
            <strong>{{ session('success') }}</strong>
        </div>
        <script>
            setTimeout(function() {
                $('#alert-success').fadeOut('fast');
            }, 5000); 
        </script>
    @endif

    <div class="card">
        <div class="card-body">
            {!! Form::open(['route' => 'admin.soporte.guardarsolicitudcodigo', 'method' => 'POST']) !!}
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4>SOLICITUD CÓDIGO</h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        {!! Form::label('permisoSolicitadoDescripcion', 'Permiso Solicitado:') !!}
                                        {!! Form::select('permisoSolicitado', $permisosSolicitados, null, [
                                        'class'       => 'form-control',
                                        'required'    => true,
                                        'placeholder' => '',
                                        'id'          => 'permisoSolicitado',
                                        'onchange'    => 'setPermisoNombre()',
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
                                <div class="col-lg-12" id="motivo"> 
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
                                            const texto = e.target.options[e.target.selectedIndex].text;
                                            const valor = e.target.value;
                                            hidNombre.value = valor;
                                            divCliente.style.display = 'none';
                                            divTiempo.style.display  = 'none';
                                            inpCliente.value = '';
                                            inpTiempo.value  = '';

                                            if ( tipo1.includes(texto) ) {
                                                divCliente.style.display = 'block';
                                                divTiempo.style.display  = 'block';

                                            } else if ( tipo2.includes(texto) ) {
                                                divCliente.style.display = 'block';
                                                inpTiempo.value = 1;

                                            } else if ( tipo3.includes(texto) ) {
                                                inpCliente.value = 0;
                                                inpTiempo.value = 1;
                                            }
                                        });
                                    });

                                    function setPermisoNombre() {
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                    {!! Form::submit('SOLICITAR CÓDIGO', ['class' => 'btn btn-sm btn-crear']) !!}
                    {!! Form::close() !!}
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h4>ULTIMOS CÓDIGOS SOLICITADOS</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Permiso_Solicitado</th>
                                            <th>Fecha_Solicitud</th>
                                            <th>Motivo</th>
                                            <th>Item/Id</th>
                                            <th>Limite</th>
                                            <th>Autorizador</th>
                                            <th>Codigo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($registroscodigos as $registroscodigo)
                                            <tr>
                                                <td>{{$registroscodigo->id}}</td>
                                                <td>
                                                    {{ $descripcionesPermisos[$registroscodigo->permisoSolicitado] ?? 'Descripción no disponible' }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($registroscodigo->fechaSolicitada)->format('Y-m-d') }}</td>
                                                <td>{{$registroscodigo->motivo ?? 0}}</td>
                                                <td>{{$registroscodigo->clienteid}}</td>
                                                <td>
                                                    {{ $registroscodigo->tiempoLimite }}
                                                    {{ $registroscodigo->tiempoLimite == 1 ? 'VEZ' : 'MIN' }}
                                                </td>
                                                <td>
                                                    @if(is_null($registroscodigo->usuarioAutorizador))
                                                        <span class="badge badge-primary">PENDIENTE</span>
                                                    @else
                                                        {{ $registroscodigo->usuarioAutorizador }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(is_null($registroscodigo->codigo))
                                                        <span class="badge badge-primary">PENDIENTE</span>
                                                    @else
                                                        {{ $registroscodigo->codigo }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($registroscodigo->estado == 'expirado')
                                                    <span class="badge badge-danger">EXPIRADO</span>
                                                    @elseif($registroscodigo->estado == 'pendiente')
                                                    <span class="badge badge-warning">PENDIENTE</span>
                                                    @elseif($registroscodigo->estado == 'activo')
                                                    <span class="badge badge-danger">EXPIRADO</span>
                                                    @elseif($registroscodigo->estado == 'solicitado')
                                                    <span class="badge badge-primary">SOLICITADO</span>
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
        </div>
    </div>
</div>

@stop
@section('js')
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function setPermisoNombre() {
            const selectPermiso = document.getElementById('permisoSolicitado');
            const nombrePermisoInput = document.getElementById('permisoSolicitadoNombre');
            nombrePermisoInput.value = selectPermiso.options[selectPermiso.selectedIndex].text;
        }
    </script>
@endsection
