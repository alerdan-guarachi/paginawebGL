@extends('adminlte::page')

@section('content_header')
<h1>TRÁMITES PARA GESTORA PÚBLICA</h1>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
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
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-11" data-toggle="tab" href="#tab-content-11" role="tab" aria-controls="tab-content-11" aria-selected="true">
                    DERIVAR
                    <?php if ($derivarCount > 0): ?>
                        <span class="circle"><?= $derivarCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-5" data-toggle="tab" href="#tab-content-5" role="tab" aria-controls="tab-content-5" aria-selected="true">
                    APELACIÓN
                    <?php if ($apelacionCount > 0): ?>
                        <span class="circle"><?= $apelacionCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="true">
                    NO INICIADO
                    <?php if ($noIniciadoCount > 0): ?>
                        <span class="circle"><?= $noIniciadoCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">
                    INICIADO
                    <?php if ($pendienteCount > 0): ?>
                        <span class="circle"><?= $pendienteCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="true">
                    VENCIDOS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="false">
                    FINALIZADOS
                    <?php if ($finalizadoCount > 0): ?>
                        <span class="circle"><?= $finalizadoCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="myTabContent">

            {{-- DERIVAR A APODERADOS--}}
            <div class="tab-pane fade show active" id="tab-content-11" role="tabpanel" aria-labelledby="tab-11">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead style="background-color: #f8fdf2" class="table-sm">
                            <tr>
                                <th>ID _Cli.</th>
                                <th>Cliente</th>
                                <th>Trámite</th>
                                <th>Fecha_Bateria</th>
                                <th>Apoderado</th>
                                <th>Derivar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                                @if (!$item['apoderadoasignado'])
                                <tr>
                                    <td>{{ $item['clienteitaid'] }}</td>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td>{{ $item['tipocliente'] }}</td>
                                    <td>{{ $item['fechabateria'] }}</td>
                                    <td>
                                        @if ($item['apoderadoasignado'])
                                            {{ $item['apoderadoasignado'] }}
                                        @else
                                            {!! Form::open(['route' => ['admin.tramites.asignarapoderadotramiteclienteita', $item['clienteitaid']], 'method' => 'POST']) !!}
                                            {!! Form::hidden('clienteitaid', $item['clienteitaid']) !!}
                                            {!! Form::hidden('fechabateria', $item['fechabateria']) !!}
                                            {!! Form::hidden('tramite', $item['tipocliente']) !!}
                                            {!! Form::select('apoderadoasignado_display', $apoderados, $apoderadoSiguiente, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => '90', 'disabled' => 'disabled', 'style' => 'width: 300px;']) !!}
                                            {!! Form::hidden('apoderadoasignado', $apoderadoSiguiente) !!}
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-derivarapoderado fas fa-sign-out-alt" title="Derivar"></button>
                                        {!! Form::close() !!}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- APELACIÓN --}}
            <div class="tab-pane fade" id="tab-content-5" role="tabpanel" aria-labelledby="tab-5">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead style="background-color: #f8fdf2" class="table-sm">
                            <tr>
                                <th>Cliente</th>
                                <th>Trámite</th>
                                <th>Inicio_Trámite</th>
                                <th>Nivel_Trámite</th>
                                <th>Sub.Nivel_Trámite</th>
                                <th>Última_Carta</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                                @if ($item['tipocliente'] === 'APELACIÓN' && $item['estadotramite'] === 'PENDIENTE' && $item['apoderadoasignado'] === $usuarioAutenticado)
                                <tr>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td>{{ $item['tipocliente'] }}</td>
                                    <td style="color: {{ $item['iniciotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['iniciotramite'] }}</td>
                                    <td style="color: {{ $item['nivelprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelprocedimientotramite'] }}</td>
                                    <td style="color: {{ $item['nivelsubprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelsubprocedimientotramite'] }}</td>
                                    <td>{{ $item['ultimacartatramite'] }}</td>
                                    <td width="10px">
                                        <abbr title="Iniciar Trámite">
                                            @if ($item['tipocliente'] === 'JUBILACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procjubilacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'MASA HEREDITARIA')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procmasahereditaria', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'APELACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procapelacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'COMPENZACIÓN SENASIR')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.proccompensacionsenasir', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'INVALIDEZ')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procinvalidez', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'PENSIÓN POR MUERTE')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procpensionpormuerte', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES TOTAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportestotal', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES PARCIAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportesparcial', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'SEGUNDA SOLICITUD')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procsegundasolicitud', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @endif
                                        </abbr>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- NO INICIADO --}}
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead style="background-color: #f8fdf2" class="table-sm">
                            <tr>
                                <th>ID_Proceso</th>
                                <th>Cliente</th>
                                <th>Trámite</th>
                                <th>Inicio_Trámite</th>
                                <th>Nivel_Trámite</th>
                                <th>Sub.Nivel_Trámite</th>
                                <th>Última_Carta</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                                @if ($item['apoderadoasignado'] === $usuarioAutenticado && $item['nivelprocedimientotramite'] === 'NO INICIADO' && $item['tipocliente'] !== 'APELACIÓN')
                                <tr>
                                    <td>{{ $item['idproceso'] }}</td>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td>{{ $item['tipocliente'] }}</td>
                                    <td style="color: {{ $item['iniciotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['iniciotramite'] }}</td>
                                    <td style="color: {{ $item['nivelprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelprocedimientotramite'] }}</td>
                                    <td style="color: {{ $item['nivelsubprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelsubprocedimientotramite'] }}</td>
                                    <td>{{ $item['ultimacartatramite'] }}</td>
                                    <td width="10px">
                                        <abbr title="INICIAR TRÁMITE">
                                            @if ($item['tipocliente'] === 'JUBILACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procjubilacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'MASA HEREDITARIA')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procmasahereditaria', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'APELACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procapelacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'COMPENZACIÓN SENASIR')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.proccompensacionsenasir', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'INVALIDEZ')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procinvalidez', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'PENSIÓN POR MUERTE')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procpensionpormuerte', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES TOTAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportestotal', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES PARCIAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportesparcial', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'SEGUNDA SOLICITUD')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procsegundasolicitud', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @endif
                                        </abbr>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- INICIADO --}}
            <div class="tab-pane fade" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead style="background-color: #f8fdf2" class="table-sm">
                            <tr>
                                <th>ID_Proceso</th>
                                <th>Cliente</th>
                                <th>Trámite</th>
                                <th>Inicio_Trámite</th>
                                <th>Nivel_Trámite</th>
                                <th>Sub.Nivel_Trámite</th>
                                <th>Última_Carta</th>
                                <th>Tiempo_Próximo_Nivel</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                                @if ($item['estadotramite'] === 'PENDIENTE' && $item['apoderadoasignado'] === $usuarioAutenticado && $item['nivelprocedimientotramite'] !== 'NO INICIADO' && $item['tipocliente'] !== 'APELACIÓN')
                                <tr>
                                    <td>{{ $item['idproceso'] }}</td>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td>{{ $item['tipocliente'] }}</td>
                                    <td style="color: {{ $item['iniciotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['iniciotramite'] }}</td>
                                    <td style="color: {{ $item['nivelprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelprocedimientotramite'] }}</td>
                                    <td style="color: {{ $item['nivelsubprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelsubprocedimientotramite'] }}</td>
                                    <td style="color: {{ $item['ultimacartatramite'] === 'NINGUNA CARTA' ? 'red' : 'inherit' }}">
                                        {{ $item['ultimacartatramite'] }}</td>
                                    <td>{{ $item['tiempo_proximo'] }}</td>
                                    <td width="10px">
                                        <abbr title="CONTINUAR TRÁMITE">
                                            @if ($item['tipocliente'] === 'JUBILACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procjubilacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'MASA HEREDITARIA')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procmasahereditaria', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'APELACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procapelacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'COMPENZACIÓN SENASIR')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.proccompensacionsenasir', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'INVALIDEZ')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procinvalidez', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'PENSIÓN POR MUERTE')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procpensionpormuerte', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES TOTAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportestotal', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES PARCIAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportesparcial', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'SEGUNDA SOLICITUD')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procsegundasolicitud', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @endif
                                        </abbr>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- VENCIDOS --}}
            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead style="background-color: #f8fdf2" class="table-sm">
                            <tr>
                                <th>Cliente</th>
                                <th>Trámite</th>
                                <th>Inicio_Trámite</th>
                                <th>Nivel_Trámite</th>
                                <th>Sub.Nivel_Trámite</th>
                                <th>Última_Carta</th>
                                <th>Ver_Trámite</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                                @if ($item['estadotramite'] === 'FINALIZADO' && $item['apoderadoasignado'] === $usuarioAutenticado)
                                <tr>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td>{{ $item['tipocliente'] }}</td>
                                    <td style="color: {{ $item['iniciotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['iniciotramite'] }}</td>
                                    <td style="color: {{ $item['nivelprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelprocedimientotramite'] }}</td>
                                    <td style="color: {{ $item['nivelsubprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelsubprocedimientotramite'] }}</td>
                                    <td>{{ $item['ultimacartatramite'] }}</td>
                                    <td width="10px">
                                        <abbr title="Ver Trámite">
                                            @if ($item['tipocliente'] === 'JUBILACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procjubilacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'MASA HEREDITARIA')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procmasahereditaria', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'APELACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procapelacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'COMPENZACIÓN SENASIR')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.proccompensacionsenasir', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'INVALIDEZ')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procinvalidez', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'PENSIÓN POR MUERTE')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procpensionpormuerte', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES TOTAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportestotal', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES PARCIAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportesparcial', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'SEGUNDA SOLICITUD')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procsegundasolicitud', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @endif
                                        </abbr>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FINALIZADOS --}}
            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead style="background-color: #f8fdf2" class="table-sm">
                            <tr>
                                <th>Cliente</th>
                                <th>Trámite</th>
                                <th>Inicio_Trámite</th>
                                <th>Nive_Trámite</th>
                                <th>Sub.Nivel_Trámite</th>
                                <th>Última_Carta</th>
                                {{-- <th>Tiempo próximo nivel</th> --}}
                                {{-- <th>Estado</th> --}}
                                <th>Ver_Trámite</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $item)
                                @if ($item['estadotramite'] === 'FINALIZADO' && $item['apoderadoasignado'] === $usuarioAutenticado)
                                <tr>
                                    <td>{{ $item['clienteitanombre'] }}</td>
                                    <td>{{ $item['tipocliente'] }}</td>
                                    <td style="color: {{ $item['iniciotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['iniciotramite'] }}</td>
                                    <td style="color: {{ $item['nivelprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelprocedimientotramite'] }}</td>
                                    <td style="color: {{ $item['nivelsubprocedimientotramite'] === 'NO INICIADO' ? 'red' : 'inherit' }}">
                                        {{ $item['nivelsubprocedimientotramite'] }}</td>
                                    <td>{{ $item['ultimacartatramite'] }}</td>
                                    {{-- <td>{{ $item['tiempo_proximo'] }}</td> --}}
                                    {{-- <td>{{ $item['estadotramite'] }}</td> --}}
                                    <td width="10px">
                                        <abbr title="Ver Trámite">
                                            @if ($item['tipocliente'] === 'JUBILACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procjubilacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'MASA HEREDITARIA')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procmasahereditaria', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'APELACIÓN')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procapelacion', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'COMPENZACIÓN SENASIR')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.proccompensacionsenasir', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'INVALIDEZ')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procinvalidez', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'PENSIÓN POR MUERTE')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procpensionpormuerte', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES TOTAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportestotal', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'RETIRO DE APORTES PARCIAL')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procretiroaportesparcial', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @elseif ($item['tipocliente'] === 'SEGUNDA SOLICITUD')
                                                <a class="btn btn-sm fas fa-file-archive btn-editar" href="{{ route('admin.tramites.procsegundasolicitud', ['cliente' => $item['clienteitaid']]) }}"></a>
                                            @endif
                                        </abbr>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
</div>


@stop

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .btn-derivarapoderado {
    background-color:  #ffffff;
    color: #94c93b;
    border-color: #94c93b;
    border-radius: 5px;
    padding: 5px 10px;
}
.btn-derivarapoderado:hover {
    background-color: #94c93b;
    color: #ffffff;
}
    .nav-tabs {
        display: flex;
        justify-content: space-between;
    }
    
    .nav-tabs .nav-item {
        flex: 1;
    }
    
    .nav-tabs .nav-link {
        display: block;
        text-align: center;
        width: 100%;
        font-weight: bold;
        font-size: 20px;
        color: #faa625;
        background-color: #fef4e7;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: bold;
        font-size: 20px;
        color: #94c93b;
    }
    </style>
<style>
    .btn-upload {
    background-color: #28a745; /* Verde */
    color: white;
    }

    .btn-disabled {
        background-color: #d3d3d3; /* Gris */
        color: #6c757d; /* Color del texto gris */
        cursor: not-allowed; /* Indica que el botón no es presionable */
    }

    .btn-upload:hover {
        background-color: #218838; /* Un verde más oscuro al pasar el mouse */
    }

    .btn-disabled:hover {
        background-color: #d3d3d3; /* Sin efecto al pasar el mouse */
    }


        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            border-bottom: none;
        }
        .modal-footer {
            border-top: none;
        }
        h4 {
            font-weight: bold;
        }
        .btn-no, .btn-si {
    background-color: #ffffff;
    border-radius: 5px;
    padding: 5px 5px; /* Ajusta el padding según sea necesario */
    min-width: 50px; /* Establece un ancho mínimo para ambos botones */
    border: 1px solid; /* Agrega borde común */
    }

    .btn-no {
    color: #fd1d1d;
    border-color: #fd1d1d;
    }

    .btn-no:hover {
    background-color: #fd1d1d;
    color: #ffffff;
    }

    .btn-si {
    color: #94c93b;
    border-color: #94c93b;
    }

    .btn-si:hover {
    background-color: #94c93b;
    color: #ffffff;
    }

</style>
<style>
    .checkverde {
        color:#94c93b; 
        }
    .text-completo {
    color: #94c93b; /* Verde para COMPLETADO */
    font-size: 17px;
    font-weight: 900;
    }

    .text-incompleto {
        color: red; /* Rojo para INCOMPLETO */
        font-size: 17px;
        font-weight: 900;
    }

        h1 {color:#94c93b; 
            font-family: "Segoe UI";
            font-weight: 900;
            }
            .btn-editar {
                    background-color:  #ffffff;
                    color: #2561fa;
                    border-color: #2561fa;
                    border-radius: 5px;
                }
            .btn-editar:hover {
                    background-color: #2561fa;
                    color: #ffffff;
                }
            .btn-eliminar {
                    background-color:  #ffffff;
                    color: #ff0000;
                    border-color: #ff0000;
                    border-radius: 5px;
                }
            .btn-eliminar:hover {
                    background-color: #ff0000;
                    color: #ffffff;
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
                .btn-verdocumentacion {
            background-color:  #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
            padding: 2px 10px;
        }
        .btn-verdocumentacion:hover {
            background-color: #94c93b;
            color: #ffffff;
        }
        .pendiente {color:#fb2525; 
            font-weight: 900;
            font-size: 15px;
            }
            .btn-veracciones {
            background-color:  #ffffff;
            color: #faa625;
            border-color: #faa625;
            border-radius: 5px;
            padding: 2px 10px;
        }
        .btn-veracciones:hover {
            background-color: #faa625;
            color: #ffffff;
        }
        .btn-aprobar {
            background-color:  #ffffff;
            color: #94c93b;
            border-color: #94c93b;
            border-radius: 5px;
            padding: 2px 10px;
        }
        .btn-aprobar:hover {
            background-color: #94c93b;
            color: #ffffff;
        }
        .btn-disabled {
            background-color:  #ffffff;
            color: #737373;
            border-color: #737373;
            border-radius: 5px;
            padding: 2px 10px;
        }
        .btn-disabled:hover {
            background-color: #737373;
            color: #ffffff;
        }
        .btn-cerrar {
            background-color: #ffffff;
            color: #fb2525;
            border-color: #fb2525;
            border-radius: 5px;
            padding: 2px 5px;

        }
        .btn-cerrar:hover {
            background-color: #fb2525;
            color: #ffffff;
        }
</style>
@stop

@section('js')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('eliminar')=='ok')
    <script>
        Swal.fire(
      '¡Eliminado!',
      'El rol se eliminó con éxito',
      'success')
    </script>
    @endif
    <script>
        $(document).ready(function() {
            $('input[name="buscarporfecha"], input[name="buscarporarea"]').on('keyup change', function() {
                var fechaSeleccionada = $('input[name="buscarporfecha"]').val();
                var areaSeleccionada = $('input[name="buscarporarea"]').val();
                var botonBuscar = $('#btn-buscar');
                
                if (fechaSeleccionada.trim() === '' && areaSeleccionada.trim() === '') {
                    botonBuscar.prop('disabled', true);
                } else {
                    botonBuscar.prop('disabled', false);
                }
            });
        });
    </script>
    <script>
        // Función para cargar la vista previa del documento seleccionado en el iframe del modal
        function cargarVistaPrevia() {
          var document = document.getElementById('document').files[0];
          if (document) {
            var reader = new FileReader();
            reader.onload = function(e) {
              var previewIframe = document.getElementById('document-preview');
              previewIframe.src = e.target.result;
            };
            reader.readAsDataURL(document);
          }
        }
      
        // Evento cuando se selecciona un archivo
        document.getElementById('document').addEventListener('change', function() {
          cargarVistaPrevia();
        });
      </script>
    <script>
        $(document).ready(function() {
        $('.dropify').dropify({
            messages: {
                'default': 'Arrastre y suelte un archivo o haga clic aquí',
                'replace': 'Arrastre y suelte o haga clic para reemplazar',
                'remove': 'Eliminar',
                'error': 'Ooops, algo salió mal.'
            }
        });
    
        $('.dropify').on('dropify.error.fileSize', function(event, element) {
            var maxSize = element.input.files[0].size / (1024 * 1024);
            var errorMessage = 'El archivo es demasiado grande (' + maxSize.toFixed(2) + ' MB máx.).';
            $(element.input).siblings('.dropify-error').text(errorMessage);
        });
    });

    document.getElementById('document').addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var fileURL = URL.createObjectURL(file);
            var previewCard = document.getElementById('preview-card');
            var documentPreview = document.getElementById('document-preview');
    
            previewCard.style.display = 'block';
            documentPreview.src = fileURL;
        } else {
            var previewCard = document.getElementById('preview-card');
            previewCard.style.display = 'none';
            documentPreview.src = '';
        }
    });

    </script>
<script>

    $('.formulario-eliminar').submit(function(e){
        e.preventDefault();

        Swal.fire({
        title: '¿Estás seguro?',
        text: "El rol se eliminará definitivamente",
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
</script>
@endsection
