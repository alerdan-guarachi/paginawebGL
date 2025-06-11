@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.acciones.index') }}">REGRESAR</a>
    {{-- <a class="btn btn-codigos btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">CODIGOS GENERADOS</a> --}}
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
            {!! Form::open(['route' => 'admin.codigo.store', 'method' => 'POST']) !!}
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4>ASIGNAR CODIGO</h4>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('usuarioSolicitante', 'Usuario Solicitante:') !!}
                                        {!! Form::select('usuarioSolicitante', $usuarios, null, [
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => '',
                                        ]) !!}
                                    </div>
                                </div>
                                <div class="col-lg-6">
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
                                <div class="col-lg-6" hidden>
                                    <div class="form-group">
                                        {!! Form::label('codigo', 'Código:') !!}
                                        {!! Form::text('codigo', $codigoGenerado, [
                                            'class' => 'form-control',
                                            'maxlength' => '7',
                                            'readonly',
                                        ]) !!}
                                    </div>
                                </div>
                                {{-- <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('clienteid', 'ID del Cliente/Proveedor:') !!}
                                        <div class="input-group">
                                            {!! Form::text('clienteid', null, [
                                                'class' => 'form-control',
                                                'id' => 'clienteid',
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('permisoSolicitadoDescripcion', 'Permiso Solicitado:') !!}
                                        {!! Form::select('permisoSolicitado', $permisosSolicitados, null, [
                                            'class' => 'form-control',
                                            'required',
                                            'placeholder' => '',
                                            'id' => 'permisoSolicitado',
                                            'onchange' => 'setPermisoNombre()',
                                        ]) !!}
                                        {!! Form::hidden('permisoSolicitadoNombre', '', ['id' => 'permisoSolicitadoNombre']) !!}
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        {!! Form::label('tiempoLimiteMinutos', 'Tiempo Límite (en minutos):') !!}
                                        <div class="input-group">
                                            {!! Form::number('tiempoLimiteMinutos', null, [
                                                'class' => 'form-control',
                                                'required',
                                                'id' => 'tiempoLimiteMinutos',
                                                'min' => '1',
                                                'placeholder' => '',
                                            ]) !!}
                                            <div class="input-group-append">
                                                <span class="input-group-text">min</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                <!-- Tus campos en Blade -->

                                <div class="col-lg-6">
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
                                <div class="col-lg-6">
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
                                <div class="col-lg-6" id="grupoClienteId" style="display: none;"> 
                                    <div class="form-group">
                                        {!! Form::label('clienteid', 'ID del Cliente/Proveedor:') !!}
                                        <div class="input-group">
                                            {!! Form::text('clienteid', null, [
                                                'class' => 'form-control',
                                                'id'    => 'clienteid',
                                                'required'    => true,
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" id="grupoTiempoLimite" style="display: none;">
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
                                <!-- Script al pie de tu Blade (o en un .js propio) -->
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const selPermiso  = document.getElementById('permisoSolicitado');
                                        const inpCliente  = document.getElementById('clienteid');
                                        const inpTiempo   = document.getElementById('tiempoLimiteMinutos');
                                        const hidNombre   = document.getElementById('permisoSolicitadoNombre');

                                        const divCliente  = document.getElementById('grupoClienteId');
                                        const divTiempo   = document.getElementById('grupoTiempoLimite');

                                        // Define los grupos
                                        const tipo1 = ['CREAR BATERIA CLIENTE ITA', 'CREAR BATERIA CLIENTE AUDITORIA'];
                                        const tipo2 = ['CONCEDER DESCUENTO INGRESO', 'CAMBIAR FECHA DE CAJA INGRESO'];
                                        const tipo3 = ['DESBLOQUEAR CAJA'];

                                        selPermiso.addEventListener('change', function(e) {
                                        const texto = e.target.options[e.target.selectedIndex].text;
                                        hidNombre.value = texto;

                                        // Oculta todo por defecto
                                        divCliente.style.display = 'none';
                                        divTiempo.style.display  = 'none';

                                        // Resetea valores
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
                                            // nada se muestra

                                        }
                                        });
                                    });

                                    function setPermisoNombre() {
                                        // ya se maneja dentro del evento change
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                    {!! Form::submit('GENERAR CÓDIGO', ['class' => 'btn btn-sm btn-crear']) !!}
            {!! Form::close() !!}
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4>ULTIMOS CODIGOS ASIGNADOS</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Solicitante</th>
                                            <th>Codigo</th>
                                            <th>Solicitado</th>
                                            <th>ID_Cli.</th>
                                            <th>Permiso_Solicitado</th>
                                            <th>Tiempo_Limite</th>
                                            <th>Fecha_Reg.</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($registroscodigos as $registroscodigo)
                                            <tr>
                                                <td title="{{$registroscodigo->usuarioSolicitante}}" class="truncar">{{$registroscodigo->usuarioSolicitante}}</td>
                                                <td>{{$registroscodigo->codigo}}</td>
                                                <td>{{ \Carbon\Carbon::parse($registroscodigo->fechaSolicitada)->format('Y-m-d') }}</td>
                                                <td>{{$registroscodigo->clienteid}}</td>
                                                <td>
                                                    {{ $descripcionesPermisos[$registroscodigo->permisoSolicitado] ?? 'Descripción no disponible' }}
                                                </td>

                                                <td>{{$registroscodigo->tiempoLimite}} min.</td>
                                                <td>{{$registroscodigo->created_at}}</td>
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Usuario Solicitante</th>
                                <th>Codigo</th>
                                <th>Solicitado</th>
                                <th>ID_Cli.</th>
                                <th>Permiso_Solicitado</th>
                                <th>Tiempo_Limite</th>
                                <th>Fecha_Reg.</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registroscodigos as $registroscodigo)
                                <tr>
                                    <td title="{{$registroscodigo->usuarioSolicitante}}" class="truncar2">{{$registroscodigo->usuarioSolicitante}}</td>
                                    <td>{{$registroscodigo->codigo}}</td>
                                    <td>{{ \Carbon\Carbon::parse($registroscodigo->fechaSolicitada)->format('Y-m-d') }}</td>
                                    <td>{{$registroscodigo->clienteid}}</td>
                                    <td>
                                        {{ \DB::table('permissions')->where('name', $registroscodigo->permisoSolicitado)->value('description') ?? 'Descripción no disponible' }}
                                    </td>
                                    <td>{{$registroscodigo->tiempoLimite}} min.</td>
                                    <td>{{$registroscodigo->created_at}}</td>
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
    <script>
        function setPermisoNombre() {
            const selectPermiso = document.getElementById('permisoSolicitado');
            const nombrePermisoInput = document.getElementById('permisoSolicitadoNombre');
            nombrePermisoInput.value = selectPermiso.options[selectPermiso.selectedIndex].text;
        }
    </script>
@endsection
