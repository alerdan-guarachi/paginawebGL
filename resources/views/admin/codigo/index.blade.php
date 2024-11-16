@extends('adminlte::page')

<link href="assets/img/logo.png" rel="icon">

@section('content_header')
    <a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.acciones.index') }}">REGRESAR</a>
    <a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#ventanaModal">CODIGOS GENERADOS</a>
    <h1>Asignación de Códigos</h1>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/estilogl.css') }}">
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
                    <div class="form-group">
                        {!! Form::label('usuarioSolicitante', 'Usuario Solicitante:') !!}
                        {!! Form::select('usuarioSolicitante', $usuarios, null, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => 'Seleccione un usuario',
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
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('codigo', 'Código:') !!}
                        {!! Form::text('codigo', $codigoGenerado, [
                            'class' => 'form-control',
                            'maxlength' => '15',
                            'readonly',
                        ]) !!}
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
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('tiempoLimiteMinutos', 'Tiempo Límite (en minutos):') !!}
                        <div class="input-group">
                            {!! Form::number('tiempoLimiteMinutos', null, [
                                'class' => 'form-control',
                                'required',
                                'id' => 'tiempoLimiteMinutos',
                                'min' => '1',
                                'placeholder' => 'Ingrese el tiempo en minutos',
                            ]) !!}
                            <div class="input-group-append">
                                <span class="input-group-text">min</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        {!! Form::label('permisoSolicitadoDescripcion', 'Permiso Solicitado:') !!}
                        {!! Form::select('permisoSolicitado', $permisosSolicitados, null, [
                            'class' => 'form-control',
                            'required',
                            'placeholder' => 'Seleccione un permiso',
                            'id' => 'permisoSolicitado',
                            'onchange' => 'setPermisoNombre()',
                        ]) !!}
                        {!! Form::hidden('permisoSolicitadoNombre', '', ['id' => 'permisoSolicitadoNombre']) !!}
                    </div>
                </div>
            </div>
            {!! Form::submit('ASIGNAR CÓDIGO', ['class' => 'btn btn-crear']) !!}
            {!! Form::close() !!}
        </div>
    </div>
</div>

<div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">CODIGOS REGISTRADOS:</h5>
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
                                <th>Fecha Solicitada</th>
                                <th>Permiso Solicitado</th>
                                <th>Tiempo limite</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registroscodigos as $registroscodigo)
                                <tr>
                                    <td>{{$registroscodigo->usuarioSolicitante}}</td>
                                    <td>{{$registroscodigo->codigo}}</td>
                                    <td>{{ \Carbon\Carbon::parse($registroscodigo->fechaSolicitada)->format('Y-m-d') }}</td>
                                    <td>
                                        {{ \DB::table('permissions')->where('name', $registroscodigo->permisoSolicitado)->value('description') ?? 'Descripción no disponible' }}
                                    </td>
                                    <td>{{$registroscodigo->tiempoLimite}} min.</td>
                                    <td>
                                        @if($registroscodigo->estado == 'expirado')
                                          <span class="badge badge-danger">EXPIRADO</span>
                                        @elseif($registroscodigo->estado == 'pendiente')
                                          <span class="badge badge-warning">PENDIENTE</span>
                                        @elseif($registroscodigo->estado == 'activo')
                                          <span class="badge badge-success">ACTIVO</span>
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

<style>
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
@section('js')
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Función para calcular la diferencia en minutos
        /* function calcularTiempoLimite() {
            const tiempoLimiteInput = document.getElementById('tiempoLimite').value;
            const tiempoLimiteMinutosInput = document.getElementById('tiempoLimiteMinutos');

            if (tiempoLimiteInput) {
                // Hora límite seleccionada
                const tiempoLimite = new Date();
                const [horas, minutos] = tiempoLimiteInput.split(':');
                tiempoLimite.setHours(horas, minutos, 0, 0);

                // Hora actual
                const ahora = new Date();
                
                // Calcular diferencia en minutos
                const diferenciaMinutos = Math.floor((tiempoLimite - ahora) / (1000 * 60));
                tiempoLimiteMinutosInput.value = diferenciaMinutos > 0 ? diferenciaMinutos : 0; // Evita valores negativos
            }
        } */

        // Llama a la función al cambiar el tiempo límite
        /* document.getElementById('tiempoLimite').addEventListener('change', calcularTiempoLimite); */

        function setPermisoNombre() {
            const selectPermiso = document.getElementById('permisoSolicitado');
            const nombrePermisoInput = document.getElementById('permisoSolicitadoNombre');
            nombrePermisoInput.value = selectPermiso.options[selectPermiso.selectedIndex].text;
        }
    </script>
@endsection
