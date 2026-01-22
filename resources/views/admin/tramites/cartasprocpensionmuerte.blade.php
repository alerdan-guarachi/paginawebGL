@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.tramites.procpensionmuerte', $cliente->id) }}">REGRESAR</a>
<h5>MISIVAS DE PENSIÓN POR MUERTE DE:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}">
{{-- <link rel="stylesheet" href="{{ asset('css/tramitesgestora.css') }}?v={{ filemtime(public_path('css/tramitesgestora.css')) }}"> --}}
<style>
    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        text-align: left;
    }
    .table th, .table td {
        padding: 5px;
    }
    .page {
        width: 812px;
        height: 5992px;
        border: 1px solid #ddd;
        margin: auto;
        padding: 20px;
        background: #fff;
        page-break-before: always;
    }
    label {
        margin-bottom: -10px !important;
    }
    .form-group {
        margin-bottom: 5px !important;
    }
    .form-control {
        height: 35px;
        padding: 2px 8px;
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

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTabs">
            <li class="nav-item">
                <a class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1" role="tab" aria-controls="tab-content-1" aria-selected="true">SOLICITUDES</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-2" data-toggle="tab" href="#tab-content-2" role="tab" aria-controls="tab-content-2" aria-selected="false">ADJUNTOS Y RESPUESTAS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-3" data-toggle="tab" href="#tab-content-3" role="tab" aria-controls="tab-content-3" aria-selected="false">CARTAS / RECLAMOS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-4" data-toggle="tab" href="#tab-content-4" role="tab" aria-controls="tab-content-4" aria-selected="false">MISIVAS LIBRES</a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-content-1" role="tabpanel" aria-labelledby="tab-1">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card">
                                <div class="card-body">
                                    <h5 style="margin-bottom: 15px; font-weight: 700;">DATOS DE LA SOLICITUD</h5>
                                    <form action="{{ route('admin.tramites.generarsolicitud', $cliente) }}" method="GET" enctype="multipart/form-data" id="formSolicitud">
                                        {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                                        {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                                        {!! Form::hidden('clienteid', $cliente->id) !!}
                                        {!! Form::hidden('clientenombre', $cliente->nombrecompleto) !!}
                                        {!! Form::hidden('apoderado', $apoderadoAsignado) !!}
                                        {!! Form::hidden('idtramite', $idTramite) !!}
                                        <input type="text" class="form-control" id="tramite" name="tramite" value="PENSIÓN POR MUERTE" hidden>
                                        <input type="date" class="form-control" id="fechasubida" name="fechasubida" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="form-group col-lg-2">
                                                        {!! Form::label('tipocarta', 'Tipo Solic.:') !!}
                                                        {!! Form::select('tipocarta', [
                                                            'EN CURSO' => 'EN CURSO',
                                                            'ERRÓNEA' => 'ERRÓNEA',
                                                        ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-3">
                                                        {!! Form::label('nivelprocedimiento', 'Nivel Procedimiento:') !!}
                                                        {!! Form::select('nivelprocedimiento', [
                                                            'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO' => 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO',
                                                            'COMPRA DE SERVICIOS' => 'COMPRA DE SERVICIOS',
                                                            'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA' => 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA',
                                                            'COMPLEMENTACIÓN DEL TRÁMITE' => 'COMPLEMENTACIÓN DEL TRÁMITE',
                                                        ], null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-3">
                                                        {!! Form::label('tipo_pdf', 'Tipo de Solicitud:') !!}
                                                        {!! Form::select('tipo_pdf', [
                                                            'ABONO EN CUENTA' => 'ABONO EN CUENTA',
                                                            'ACTUALIZACIÓN DE DATOS' => 'ACTUALIZACIÓN DE DATOS',
                                                            'CAMBIO DE C.E. A PASAPORTE' => 'CAMBIO DE C.E. A PASAPORTE',
                                                            'COMPRA DE SERVICIOS' => 'COMPRA DE SERVICIOS',
                                                            'COPIA LEGALIZADA DE CONTRATO' => 'COPIA LEGALIZADA DE CONTRATO',
                                                            'COPIA LEGALIZADA DE DICTAMEN' => 'COPIA LEGALIZADA DE DICTAMEN',
                                                            'EVALUACIÓN POR MEDICINA DEL TRABAJO' => 'EVALUACIÓN POR MEDICINA DEL TRABAJO',
                                                            'HISTORIA CLÍNICA LEGALIZADA' => 'HISTORIA CLÍNICA LEGALIZADA',
                                                            'INCLUSIÓN DE INFORMES MÉDICOS' => 'INCLUSIÓN DE INFORMES MÉDICOS',
                                                            'INFORME DEL EMPLEADOR' => 'INFORME DEL EMPLEADOR',
                                                            'INFORME MÉDICO' => 'INFORME MÉDICO',
                                                            'MODIFICACIÓN DE CITE' => 'MODIFICACIÓN DE CITE',
                                                            'NO DESCUENTO 3%' => 'NO DESCUENTO 3%',
                                                            'RECALIFICACIÓN DE DICTAMEN' => 'RECALIFICACIÓN DE DICTAMEN',
                                                            'REACTIVACIÓN DE TRÁMITE' => 'REACTIVACIÓN DE TRÁMITE',
                                                            'UNIFICACIÓN DE CUA' => 'UNIFICACIÓN DE CUA',
                                                        ], null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'tipoPdfSelect2', 'required' => 'required']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        {!! Form::label('fechaemitido', 'Fecha Carta:') !!}
                                                        <input type="date" class="form-control" id="fechaactual" name="fechaactual" value="{{ \Carbon\Carbon::now()->toDateString() }}" min="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        {!! Form::label('emisor', 'Emisor:') !!}
                                                        {!! Form::select('emisor', [
                                                            'CLIENTE' => 'CLIENTE',
                                                            'APODERADO' => 'APODERADO',
                                                        ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                        <input type="hidden" name="emisor" value="">
                                                    </div>
                                                    <div class="form-group col-lg-4">
                                                        {!! Form::label('apoderado', 'Emisor Apoderado:') !!}
                                                        {!! Form::select('apoderado', 
                                                            array_combine($apoderados, $apoderados),
                                                            $apoderadoAsignado,
                                                            ['class' => 'form-control']
                                                        ) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4">
                                                        {!! Form::label('aseguradora', 'Aseguradora:') !!}
                                                        {!! Form::text('aseguradora', $aseguradora, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="afpgestoraContainer" style="display: none;">
                                                        {!! Form::label('afpgestora', 'Gestora/Afp:') !!}
                                                        {!! Form::text('afpgestora', $afpgestora, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                                                    </div> 
                                                    <div class="form-group col-lg-4" id="matriculaContainer" style="display: none;">
                                                        {!! Form::label('matricula', 'Matricula:') !!}
                                                        {!! Form::text('matricula', $matriculacliente, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nombremedicoContainer" style="display: none;">
                                                        {!! Form::label('nombremedico', 'Destinatario:') !!}
                                                        {!! Form::text('nombremedico', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="cargomedicoContainer" style="display: none;">
                                                        {!! Form::label('cargomedico', 'Cargo Destinatario:') !!}
                                                        {!! Form::text('cargomedico', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="notatecnicomedicoContainer" style="display: none;">
                                                        {!! Form::label('notatecnicomedico', 'Nota:') !!}
                                                        {!! Form::text('notatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="fechanotatecnicomedicoContainer" style="display: none;">
                                                        {!! Form::label('fechanotatecnicomedico', 'Fecha de Nota:') !!}
                                                        {!! Form::date('fechanotatecnicomedico', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                                    </div> 
                                                    <div class="form-group col-lg-4" id="solicitudmodificarContainer" style="display: none;">
                                                        {!! Form::label('solicitudmodificar', 'Solic. Modif.:') !!}
                                                        {!! Form::select('solicitudmodificar', [
                                                            'TÉCNICO MÉDICO' => 'TÉCNICO MÉDICO',
                                                            'COMPLEMENTARIO' => 'COMPLEMENTARIO',
                                                            'ACTA DEL TMC' => 'ACTA DEL TMC',
                                                            'DEL EMPLEADOR' => 'DEL EMPLEADOR',
                                                        ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="campodirigidoaContainer" style="display: none;">
                                                        {!! Form::label('campodirigidoa', 'Dirigido a:') !!}
                                                        {!! Form::text('campodirigidoa', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="especialidadinformeContainer" style="display: none;">
                                                        {!! Form::label('especialidadinforme', 'Especialidad:') !!}
                                                        {!! Form::text('especialidadinforme', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="medicotratanteContainer" style="display: none;">
                                                        {!! Form::label('medicotratante', 'Médico Tratante:') !!}
                                                        {!! Form::text('medicotratante', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="campoestadolabContainer" style="display: none;">
                                                        {!! Form::label('campoestadolab', 'Estado Lab.:') !!}
                                                        {!! Form::text('campoestadolab', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="campoafiliadoaContainer" style="display: none;">
                                                        {!! Form::label('campoafiliadoa', 'Afiliado a:') !!}
                                                        {!! Form::text('campoafiliadoa', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nrocua1Container" style="display: none;">
                                                        {!! Form::label('nrocua1', 'Nro. CUA 1:') !!}
                                                        {!! Form::text('nrocua1', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nombreafp1Container" style="display: none;">
                                                        {!! Form::label('nombreafp1', 'AFP 1:') !!}
                                                        {!! Form::text('nombreafp1', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nroci1Container" style="display: none;">
                                                        {!! Form::label('nroci1', 'C.I. 1:') !!}
                                                        {!! Form::text('nroci1', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nrocua2Container" style="display: none;">
                                                        {!! Form::label('nrocua2', 'Nro. CUA 2:') !!}
                                                        {!! Form::text('nrocua2', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nombreafp2Container" style="display: none;">
                                                        {!! Form::label('nombreafp2', 'AFP 2:') !!}
                                                        {!! Form::text('nombreafp2', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nroci2Container" style="display: none;">
                                                        {!! Form::label('nroci2', 'C.I. 2:') !!}
                                                        {!! Form::text('nroci2', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="texto1Container" style="display: none;">
                                                        {!! Form::label('texto1', 'Texto Complementario:') !!}
                                                        {!! Form::text('texto1', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => 150]) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="fechainformeestudioContainer" style="display: none;">
                                                        {!! Form::label('fechainformeestudio', 'Fecha de Informes y Estudios:') !!}
                                                        {!! Form::date('fechainformeestudio', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="cambioactualizacionContainer" style="display: none;">
                                                        {!! Form::label('cambioactualizacion', 'Modificación:') !!}
                                                        {!! Form::text('cambioactualizacion', null, ['class' => 'form-control', 'placeholder' => 'Ej: EL ESTADO CIVIL DE SOLTERO A CASADO']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="entidadcalificanteContainer" style="display: none;">
                                                        {!! Form::label('entidadcalificante', 'Entidad Calificante:') !!}
                                                        {!! Form::text('entidadcalificante', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nrodictamenContainer" style="display: none;">
                                                        {!! Form::label('nrodictamen', 'Nro. Dictamen:') !!}
                                                        {!! Form::text('nrodictamen', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="fechacontratoContainer" style="display: none;">
                                                        {!! Form::label('fechacontrato', 'Fecha Documento:') !!}
                                                        {!! Form::date('fechacontrato', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="firmadoenContainer" style="display: none;">
                                                        {!! Form::label('firmadoen', 'Entregado en:') !!}
                                                        {!! Form::text('firmadoen', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="fechatramiteContainer" style="display: none;">
                                                        {!! Form::label('fechatramite', 'Fecha Trámite:') !!}
                                                        {!! Form::date('fechatramite', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="porcentajedictamenContainer" style="display: none;">
                                                        {!! Form::label('porcentajedictamen', 'Porcentaje Dictamen:') !!}
                                                        {!! Form::text('porcentajedictamen', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="origendictamenContainer" style="display: none;">
                                                        {!! Form::label('origendictamen', 'Origen Dictamen:') !!}
                                                        {!! Form::text('origendictamen', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="tipoorigenContainer" style="display: none;">
                                                        {!! Form::label('tipoorigen', 'Tipo Origen:') !!}
                                                        {!! Form::text('tipoorigen', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="notificadoenContainer" style="display: none;">
                                                        {!! Form::label('notificadoen', 'Notificado en:') !!}
                                                        {!! Form::text('notificadoen', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="fechanotificacionContainer" style="display: none;">
                                                        {!! Form::label('fechanotificacion', 'Fecha Notificación:') !!}
                                                        {!! Form::date('fechanotificacion', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nropasaporteContainer" style="display: none;">
                                                        {!! Form::label('nropasaporte', 'Nro. Pasaporte:') !!}
                                                        {!! Form::text('nropasaporte', null, ['class' => 'form-control', 'placeholder' => '', 'id' => 'nropasaporte']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nrocuaunificadoContainer" style="display: none;">
                                                        {!! Form::label('nrocuaunificado', 'Nro. CUA Unificado:') !!}
                                                        {!! Form::text('nrocuaunificado', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nrociunificadoContainer" style="display: none;">
                                                        {!! Form::label('nrociunificado', 'C.I. Unificado:') !!}
                                                        {!! Form::text('nrociunificado', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {{-- <div class="col-lg-12" id="tablaEspecialidades" style="display: none;">
                                                <div class="table-responsive">
                                                    <table class="table" id="especialidadesTable">
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-5">ESPECIALIDAD</th>
                                                                <th class="col-lg-5">DETALLE</th>
                                                                <th class="col-lg-2">CANTIDAD</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="col-lg-5"><input type="text" name="especialista1" class="form-control" /></td>
                                                                <td class="col-lg-5"><input type="text" name="detalle1" class="form-control" /></td>
                                                                <td class="col-lg-2"><input type="text" name="cantidad1" class="form-control" /></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <button type="button" class="btn btn-sm btn-verdocumento" id="agregarFila">AGREGAR MÁS</button>
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    let filaCount = 1;
                                                    $('#agregarFila').on('click', function() {
                                                        filaCount++;
                                                        if(filaCount > 5) return;

                                                        const nuevaFila = `
                                                            <tr>
                                                                <td class="col-lg-5"><input type="text" name="especialista${filaCount}" class="form-control" /></td>
                                                                <td class="col-lg-5"><input type="text" name="detalle${filaCount}" class="form-control" /></td>
                                                                <td class="col-lg-2"><input type="text" name="cantidad${filaCount}" class="form-control" /></td>
                                                            </tr>
                                                        `;
                                                        $('#especialidadesTable tbody').append(nuevaFila);
                                                    });
                                                });
                                            </script> --}}

                                            <div class="row" id="tablaEspecialidades" style="display: none;">
                                                <div class="col-lg-5">
                                                    <div id="contenedor_areas" class="mt-3">
                                                        @foreach ($programaciones as $fecha => $grupos)
                                                            <div class="card shadow-sm border mb-2">
                                                                
                                                                <div class="card-header py-2 px-3 bg-secondary text-white">
                                                                    <button class="btn btn-link text-white text-left w-100 p-0" type="button"
                                                                        data-toggle="collapse" data-target="#fecha_{{ \Str::slug($fecha) }}">
                                                                        <strong>FECHA BATERIA:</strong> {{ $fecha }}
                                                                    </button>
                                                                </div>

                                                                <div id="fecha_{{ \Str::slug($fecha) }}" class="collapse">
                                                                    <div class="card-body py-2 px-3">
                                                                        @foreach ($grupos->groupBy('areanombre') as $area => $acciones)
                                                                            <div class="card border mb-2">
                                                                                <div class="card-header py-2 px-3 bg-light">
                                                                                    <button class="btn btn-sm btn-outline-secondary w-100 text-left p-1"
                                                                                        type="button"
                                                                                        data-toggle="collapse"
                                                                                        data-target="#area_{{ \Str::slug($fecha . '_' . $area) }}">
                                                                                        <strong>ÁREA:</strong> {{ $area }}
                                                                                    </button>
                                                                                </div>

                                                                                <div id="area_{{ \Str::slug($fecha . '_' . $area) }}" class="collapse" style="margin-top: -30px;">
                                                                                    <div class="card-body pt-2 pb-1 px-3">
                                                                                        <div class="table-responsive">
                                                                                            <table class="table table-sm table-bordered mb-3" style="white-space: nowrap;">
                                                                                                <thead class="thead-light text-center">
                                                                                                    <tr>
                                                                                                        <th class="text-center"><input type="checkbox" class="seleccionar-todo-area" data-area="{{ \Str::slug($fecha . '_' . $area) }}" /></th>
                                                                                                        <th>Ver</th>
                                                                                                        <th>ID</th>
                                                                                                        <th>Estudio/Espec.</th>
                                                                                                        <th>Hojas</th>
                                                                                                    </tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    @foreach ($acciones as $doc)
                                                                                                        <tr>
                                                                                                            <td class="text-center align-middle">
                                                                                                                <input type="checkbox" class="documento-checkbox"
                                                                                                                    data-area="{{ $doc->areanombre }}"
                                                                                                                    data-accion="{{ $doc->accionnombre }}"
                                                                                                                    data-hojas="{{ $doc->nro_hojas ?? 0 }}"
                                                                                                                    data-tipoarea="{{ $doc->tipoarea }}">
                                                                                                            </td>
                                                                                                            <td class="text-center align-middle">
                                                                                                                <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->document}") }}"
                                                                                                                    target="_blank" class="btn btn-sm btn-verdoc" title="Ver documento">
                                                                                                                    <i class="fas fa-eye"></i>
                                                                                                                </a>
                                                                                                                @if(!empty($doc->image))
                                                                                                                <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->image}") }}"
                                                                                                                target="_blank" class="btn btn-sm btn-verdoc" title="Ver Imagen 1">
                                                                                                                    <i class="fas fa-image"></i>
                                                                                                                </a>
                                                                                                                @endif
                                                                                                                @if(!empty($doc->image2))
                                                                                                                <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->image2}") }}"
                                                                                                                target="_blank" class="btn btn-sm btn-verdoc" title="Ver Imagen 2">
                                                                                                                    <i class="fas fa-image"></i>
                                                                                                                </a>
                                                                                                                @endif
                                                                                                            </td>
                                                                                                            <td class="align-middle">{{ $doc->doc_id }}</td>
                                                                                                            <td class="align-middle">{{ $doc->accionnombre }}</td>
                                                                                                            <td class="text-center align-middle">
                                                                                                                <span class="badge" style="background-color: #faa625; color: #ffffff; font-size: 0.8rem; padding: 0.2em 0.3em; line-height: 1;">
                                                                                                                    {{ $doc->nro_hojas ?? '?' }}
                                                                                                                </span>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                        <div class="text-right mt-3">
                                                                            <div class="form-group row justify-content-end align-items-center">
                                                                                <div class="col-sm-8">
                                                                                    <select id="tipoDocumento" name="tipoDocumento" class="form-control form-control-sm">
                                                                                        <option value="">Seleccione una opción...</option>
                                                                                        <option value="INFORME MÉDICO">INFORME MÉDICO</option>
                                                                                        <option value="CERTIFICADO MÉDICO">CERTIFICADO MÉDICO</option>
                                                                                        <option value="ESTUDIO MÉDICO">ESTUDIO MÉDICO</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-auto">
                                                                                    <button id="btnAgregarSeleccionados" type="button" class="btn btn-sm btn-adjuntosrespuestas">
                                                                                        <i class="fas fa-plus"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-lg-7">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead class="table-secondary">
                                                                <tr style="text-align: center">
                                                                    <th class="col-lg-5">ESPECIALIDAD/ESTUDIO</th>
                                                                    <th class="col-lg-5">DETALLE DE ESPECIALIDAD/ESTUDIO</th>
                                                                    <th class="col-lg-2">CANTIDAD</th>
                                                                    <th class="col-lg-2">QUITAR</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tabla-especialistas">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const tabla = document.getElementById('tabla-especialistas');
                                                        
                                                        function llenarTablaConSeleccionados() {
                                                            const seleccionados = document.querySelectorAll('.documento-checkbox:checked');
                                                            const tipoDocumento = document.getElementById('tipoDocumento').value.trim();
                                                            console.log('Checkbox seleccionados:', seleccionados.length);
                                                            const agrupados = {};
                                                            seleccionados.forEach(input => {
                                                                const accion = input.dataset.accion;
                                                                const hojas = parseInt(input.dataset.hojas || 0);
                                                                const tipoarea = input.dataset.tipoarea;
                                                                const area = input.dataset.area;
                                                                const clave = (tipoarea === 'ESTUDIO') ? area : accion;
                                                                if (!agrupados[clave]) {
                                                                    agrupados[clave] = {
                                                                        totalHojas: 0,
                                                                        tipoarea: tipoarea
                                                                    };
                                                                    if (tipoarea === 'ESTUDIO') {
                                                                        agrupados[clave].totalHojas = hojas;
                                                                    } else {
                                                                        agrupados[clave].totalHojas += hojas;
                                                                    }
                                                                } else {
                                                                    if (tipoarea !== 'ESTUDIO') {
                                                                        agrupados[clave].totalHojas += hojas;
                                                                    }
                                                                }
                                                            });
                                                            let filasActuales = tabla.querySelectorAll('tr').length;
                                                            let i = filasActuales + 1;
                                                            for (const clave in agrupados) {
                                                                if (i > 10) break;
                                                                const especialidad = clave;
                                                                const detalle = tipoDocumento;
                                                                const hojas = agrupados[clave].totalHojas;
                                                                const fila = `<tr>
                                                                    <td><input type="text" name="especialista${i}" class="form-control" value="${especialidad}" /></td>
                                                                    <td><input type="text" name="detalle${i}" class="form-control" value="${detalle}" /></td>
                                                                    <td><input type="text" name="cantidad${i}" class="form-control" value="${hojas}" /></td>
                                                                    <td><button type="button" class="btn btn-outline-danger btn-sm quitar-fila"><i class="fas fa-trash"></i></button></td>
                                                                </tr>`;
                                                                tabla.insertAdjacentHTML('beforeend', fila);
                                                                i++;
                                                                const ultimaFila = tabla.querySelector('tr:last-child');
                                                                const btnQuitar = ultimaFila.querySelector('.quitar-fila');
                                                                btnQuitar.addEventListener('click', function () {
                                                                    ultimaFila.remove();
                                                                });
                                                            }
                                                        }
                                                        document.getElementById('btnAgregarSeleccionados').addEventListener('click', function() {
                                                            llenarTablaConSeleccionados();
                                                            const seleccionados = document.querySelectorAll('.documento-checkbox:checked');
                                                            seleccionados.forEach(checkbox => checkbox.checked = false);
                                                            const checkboxesSeleccionarTodo = document.querySelectorAll('.seleccionar-todo-area');
                                                            checkboxesSeleccionarTodo.forEach(chk => chk.checked = false);
                                                        });
                                                        document.querySelectorAll('.seleccionar-area').forEach(btn => {
                                                            btn.addEventListener('click', function () {
                                                                const area = btn.dataset.area;
                                                                const checkboxes = document.querySelectorAll(`#area_${area} .documento-checkbox`);
                                                                checkboxes.forEach(c => c.checked = true);
                                                            });
                                                        });
                                                        document.querySelectorAll('.seleccionar-todo-area').forEach(checkbox => {
                                                            checkbox.addEventListener('change', function () {
                                                                const area = this.dataset.area;
                                                                const checkboxes = document.querySelectorAll(`#area_${area} .documento-checkbox`);
                                                                checkboxes.forEach(c => c.checked = this.checked);
                                                            });
                                                        });
                                                    });
                                                </script>
                                                <script>
                                                    document.getElementById('fechabateria_select').addEventListener('change', function () {
                                                        let todas = document.querySelectorAll('.grupo_fecha');
                                                        todas.forEach(div => div.style.display = 'none');
                                                        let seleccionada = this.value;
                                                        if (seleccionada) {
                                                            let slug = seleccionada.replaceAll(/[^a-zA-Z0-9]/g, '-').toLowerCase();
                                                            let grupo = document.querySelector('.grupo_fecha_' + slug);
                                                            if (grupo) grupo.style.display = 'block';
                                                        }
                                                    });
                                                </script>
                                            </div>


                                            <div class="col-lg-12" id="tablaadjuntosContainer222" style="display: none;">
                                                <table class="table" style="margin-top: -5px;" >
                                                    <thead>
                                                        <tr>
                                                            <th class="col-lg-5" style="text-align: center">Información</th>
                                                            <th class="col-lg-5" style="text-align: center">Tipo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <tr>
                                                                <td class="col-lg-5"><input type="text" name="requerimiento2{{ $i }}" class="form-control" /></td>
                                                                <td class="col-lg-5"><input type="text" name="tipo2{{ $i }}" class="form-control" /></td>
                                                            </tr>
                                                        @endfor
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-lg-12" id="tablainformacionContainer" style="display: none;">
                                                <div class="table-responsive">
                                                    <table class="table" id="informacionTable">
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-12">INFORMACIÓN</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="col-lg-12"><input type="text" name="informacion1" class="form-control" /></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <button type="button" class="btn btn-sm btn-verdocumento" id="agregarFilaInformacion">AGREGAR MÁS</button>
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    let filaCount3 = 1;
                                                    $('#agregarFilaInformacion').on('click', function() {
                                                        filaCount3++;
                                                        if(filaCount3 > 5) return;

                                                        const nuevaFila = `
                                                            <tr>
                                                                <td class="col-lg-12"><input type="text" name="informacion${filaCount3}" class="form-control" /></td>
                                                            </tr>
                                                        `;
                                                        $('#informacionTable tbody').append(nuevaFila);
                                                    });
                                                });
                                            </script>
                                            <div class="col-lg-12" id="tablaabonoContainer" style="display: none;">
                                                <div class="table-responsive">
                                                    <table class="table" id="abonoTable">
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-4">ENTIDAD BANCARIA</th>
                                                                <th class="col-lg-4">TIPO DE CUENTA</th>
                                                                <th class="col-lg-4">N° DE CUENTA</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="col-lg-4"><input type="text" name="entidadbancaria1" class="form-control" /></td>
                                                                <td class="col-lg-4"><input type="text" name="tipocuenta1" class="form-control" /></td>
                                                                <td class="col-lg-4"><input type="text" name="nrocuenta1" class="form-control" /></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    {{-- <button type="button" class="btn btn-sm btn-verdocumento" id="agregarFilaabono">AGREGAR MÁS</button> --}}
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    let filaCount4 = 1;
                                                    $('#agregarFilaabono').on('click', function() {
                                                        filaCount4++;
                                                        if(filaCount4 > 5) return;

                                                        const nuevaFila = `
                                                            <tr>
                                                                <td class="col-lg-4"><input type="text" name="entidadbancaria${filaCount4}" class="form-control" /></td>
                                                                <td class="col-lg-4"><input type="text" name="tipocuenta${filaCount4}" class="form-control" /></td>
                                                                <td class="col-lg-4"><input type="text" name="nrocuenta${filaCount4}" class="form-control" /></td>
                                                            </tr>
                                                        `;
                                                        $('#abonoTable tbody').append(nuevaFila);
                                                    });
                                                });
                                            </script>

                                            {{-- Solicitud de Cambio de Numero de Carnet de Extranjero con datos: --}}
                                            <div class="col-lg-12" id="tablaceapasaporteContainer" style="display: none;">
                                                <label for="" style="margin-top: 10px;">Solicitud de Cambio de Numero de Carnet de Extranjero con datos:</label>
                                                <div class="table-responsive">
                                                    <table class="table" id="ceapasaporteTable">
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-3">AP. PATERNO</th>
                                                                <th class="col-lg-3">AP. MATERNO</th>
                                                                <th class="col-lg-3">PRIMER NOMBRE</th>
                                                                <th class="col-lg-3">SEGUNDO NOMBRE</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $nombres = explode(' ', trim($cliente->nombres));
                                                                $primerNombre = $nombres[0] ?? '';
                                                                $segundoNombre = count($nombres) > 1 ? implode(' ', array_slice($nombres, 1)) : '';
                                                            @endphp
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="appaterno1" class="form-control" value="{{$cliente->apepaterno}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="apmaterno1" class="form-control" value="{{$cliente->apematerno}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="primernombre1" class="form-control" value="{{ $primerNombre }}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="segundonombre1" class="form-control" value="{{ $segundoNombre }}"/></td>
                                                            </tr>
                                                        </tbody>
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-3">CUA N°</th>
                                                                <th class="col-lg-3">C.E.</th>
                                                                <th class="col-lg-3">PASAPORTE N°</th>
                                                                <th class="col-lg-3"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="cua1" class="form-control" value="{{$cliente->nuacua}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="ce1" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="pasaporte1" class="form-control" disabled/></td>
                                                                <td class="col-lg-3"><input type="text" name="" class="form-control" disabled /></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    {{-- <button type="button" class="btn btn-sm btn-verdocumento" id="agregarFilacepasaporte">AGREGAR MÁS</button> --}}
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    let filaCount5 = 1;
                                                    $('#agregarFilacepasaporte').on('click', function() {
                                                        filaCount5++;
                                                        if(filaCount5 > 5) return;

                                                        const nuevaFila = `
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="appaterno${filaCount5}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="apmaterno${filaCount5}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="primernombre${filaCount5}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="segundonombre${filaCount5}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="cua${filaCount5}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="ce${filaCount5}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="pasaporte${filaCount5}" class="form-control" /></td>
                                                            </tr>
                                                        `;
                                                        $('#ceapasaporteTable tbody').append(nuevaFila);
                                                    });
                                                });
                                            </script>

                                            {{-- A Numero de Pasaporte con datos: --}}
                                            <div class="col-lg-12" id="tablaceapasaporteContainer2" style="display: none;">
                                                <label for="" style="margin-top: 10px;">A Numero de Pasaporte con datos:</label>
                                                <div class="table-responsive">
                                                    <table class="table" id="ceapasaporteTable2">
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-3">AP. PATERNO</th>
                                                                <th class="col-lg-3">AP. MATERNO</th>
                                                                <th class="col-lg-3">PRIMER NOMBRE</th>
                                                                <th class="col-lg-3">SEGUNDO NOMBRE</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $nombres = explode(' ', trim($cliente->nombres));
                                                                $primerNombre2 = $nombres[0] ?? '';
                                                                $segundoNombre2 = count($nombres) > 1 ? implode(' ', array_slice($nombres, 1)) : '';
                                                            @endphp
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="appaterno2" class="form-control" value="{{$cliente->apepaterno}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="apmaterno2" class="form-control" value="{{$cliente->apematerno}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="primernombre2" class="form-control" value="{{ $primerNombre2 }}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="segundonombre2" class="form-control" value="{{ $segundoNombre2 }}"/></td>
                                                            </tr>
                                                        </tbody>
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-3">CUA N°</th>
                                                                <th class="col-lg-3">C.E.</th>
                                                                <th class="col-lg-3">PASAPORTE N°</th>
                                                                <th class="col-lg-3"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="cua2" class="form-control" value="{{$cliente->nuacua}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="ce2" class="form-control" disabled/></td>
                                                                <td class="col-lg-3"><input type="text" name="pasaporte2" class="form-control" id="pasaporte2"/></td>
                                                                <td class="col-lg-3"><input type="text" name="" class="form-control" disabled /></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <script>
                                                        document.addEventListener("DOMContentLoaded", function () {
                                                            const nropasaporte = document.getElementById("nropasaporte");
                                                            const pasaporte2 = document.getElementById("pasaporte2");

                                                            nropasaporte.addEventListener("input", function () {
                                                                pasaporte2.value = nropasaporte.value;
                                                            });
                                                        });
                                                    </script>
                                                    {{-- <button type="button" class="btn btn-sm btn-verdocumento" id="agregarFilacepasaporte2">AGREGAR MÁS</button> --}}
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    let filaCount6 = 1;
                                                    $('#agregarFilacepasaporte2').on('click', function() {
                                                        filaCount6++;
                                                        if(filaCount6 > 5) return;

                                                        const nuevaFila = `
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="appaterno${filaCount6}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="apmaterno${filaCount6}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="primernombre${filaCount6}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="segundonombre${filaCount6}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="cua${filaCount6}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="ce${filaCount6}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="pasaporte${filaCount6}" class="form-control" /></td>
                                                            </tr>
                                                        `;
                                                        $('#ceapasaporteTable2 tbody').append(nuevaFila);
                                                    });
                                                });
                                            </script>

                                            {{-- Unificacion de CUA Datos actuales: --}}
                                            <div class="col-lg-12" id="tablaunificacioncuaContainer" style="display: none;">
                                                <label for="" style="margin-top: 10px;">Datos Actuales:</label>
                                                <div class="table-responsive">
                                                    <table class="table" id="unificacioncuaTable">
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-3">AP. PATERNO</th>
                                                                <th class="col-lg-3">AP. MATERNO</th>
                                                                <th class="col-lg-3">PRIMER NOMBRE</th>
                                                                <th class="col-lg-3">SEGUNDO NOMBRE</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $nombres = explode(' ', trim($cliente->nombres));
                                                                $primerNombre3 = $nombres[0] ?? '';
                                                                $segundoNombre3 = count($nombres) > 1 ? implode(' ', array_slice($nombres, 1)) : '';
                                                            @endphp
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="appaterno3" class="form-control" value="{{$cliente->apepaterno}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="apmaterno3" class="form-control" value="{{$cliente->apematerno}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="primernombre3" class="form-control" value="{{ $primerNombre3 }}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="segundonombre3" class="form-control" value="{{ $segundoNombre3 }}"/></td>
                                                            </tr>
                                                        </tbody>
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-3">FECHA NAC.</th>
                                                                <th class="col-lg-3">C.I.</th>
                                                                <th class="col-lg-3">CUA 1</th>
                                                                <th class="col-lg-3">CUA 2</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="fechanacimiento3" class="form-control" value="{{$cliente->fechanacimiento}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="ci3" class="form-control" value="{{$cliente->ci}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="cua3" class="form-control"{{--  value="{{$cliente->nuacua}}" --}}/></td>
                                                                <td class="col-lg-3"><input type="text" name="cuaotro3" class="form-control" /></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    {{-- <button type="button" class="btn btn-sm btn-verdocumento" id="agregarFilaunificacioncua">AGREGAR MÁS</button> --}}
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    let filaCount7 = 1;
                                                    $('#agregarFilaunificacioncua').on('click', function() {
                                                        filaCount7++;
                                                        if(filaCount7 > 5) return;

                                                        const nuevaFila = `
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="appaterno${filaCount7}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="apmaterno${filaCount7}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="primernombre${filaCount7}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="segundonombre${filaCount7}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="fechanacimiento${filaCount7}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="ci${filaCount7}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="cua${filaCount7}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="cuaotro${filaCount7}" class="form-control" /></td>
                                                            </tr>
                                                        `;
                                                        $('#unificacioncuaTable tbody').append(nuevaFila);
                                                    });
                                                });
                                            </script>

                                            {{-- Unificacion de CUA Datos a cambiar: --}}
                                            <div class="col-lg-12" id="tablacambiounificacioncuaContainer" style="display: none;">
                                                <label for="" style="margin-top: 10px;">CUA Unificado:</label>
                                                <div class="table-responsive">
                                                    <table class="table" id="unificacioncuaTable2">
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-3">AP. PATERNO</th>
                                                                <th class="col-lg-3">AP. MATERNO</th>
                                                                <th class="col-lg-3">PRIMER NOMBRE</th>
                                                                <th class="col-lg-3">SEGUNDO NOMBRE</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $nombres = explode(' ', trim($cliente->nombres));
                                                                $primerNombre4 = $nombres[0] ?? '';
                                                                $segundoNombre4 = count($nombres) > 1 ? implode(' ', array_slice($nombres, 1)) : '';
                                                            @endphp
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="appaterno4" class="form-control" value="{{$cliente->apepaterno}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="apmaterno4" class="form-control" value="{{$cliente->apematerno}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="primernombre4" class="form-control" value="{{ $primerNombre4 }}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="segundonombre4" class="form-control" value="{{ $segundoNombre4 }}"/></td>
                                                            </tr>
                                                        </tbody>
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-3">FECHA NAC.</th>
                                                                <th class="col-lg-3">C.I.</th>
                                                                <th class="col-lg-3">CUA</th>
                                                                <th class="col-lg-3"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="fechanacimiento4" class="form-control" value="{{$cliente->fechanacimiento}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="ci4" class="form-control" value="{{$cliente->ci}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="cua4" class="form-control" value="{{$cliente->nuacua}}"/></td>
                                                                <td class="col-lg-3"><input type="text" name="" class="form-control" disabled/></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    {{-- <button type="button" class="btn btn-sm btn-verdocumento" id="agregarFilacambiounificacioncua">AGREGAR MÁS</button> --}}
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    let filaCount8 = 1;
                                                    $('#agregarFilacambiounificacioncua').on('click', function() {
                                                        filaCount8++;
                                                        if(filaCount8 > 5) return;

                                                        const nuevaFila = `
                                                            <tr>
                                                                <td class="col-lg-3"><input type="text" name="appaterno${filaCount8}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="apmaterno${filaCount8}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="primernombre${filaCount8}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="segundonombre${filaCount8}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="fechanacimiento${filaCount8}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="ci${filaCount8}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="cua${filaCount8}" class="form-control" /></td>
                                                                <td class="col-lg-3"><input type="text" name="cuaotro${filaCount8}" class="form-control" /></td>
                                                            </tr>
                                                        `;
                                                        $('#unificacioncuaTable2 tbody').append(nuevaFila);
                                                    });
                                                });
                                            </script>

                                            <div class="col-lg-12" id="tablaadjuntosContainer" style="display: none;">
                                                <div class="table-responsive">
                                                    <table class="table" id="adjuntosTable">
                                                        <thead class="table-secondary">
                                                            <tr style="text-align: center">
                                                                <th class="col-lg-5">ADJUNTO</th>
                                                                <th class="col-lg-5">CANTIDAD</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="col-lg-8"><input type="text" name="requerimiento1" class="form-control" /></td>
                                                                <td class="col-lg-4"><input type="text" name="tipo1" class="form-control" /></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <button type="button" class="btn btn-sm btn-verdocumento" id="agregarFilaadjunto">AGREGAR MÁS</button>
                                                </div>
                                                {{-- <input type="text" class="form-control" id="tipocartareclamo" name="tipocartareclamo" value="PENSIÓN DE PENSIÓN POR MUERTE" hidden> --}}
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    let filaCount2 = 1;
                                                    $('#agregarFilaadjunto').on('click', function() {
                                                        filaCount2++;
                                                        if(filaCount2 > 5) return;

                                                        const nuevaFila = `
                                                            <tr>
                                                                <td class="col-lg-8"><input type="text" name="requerimiento${filaCount2}" class="form-control" /></td>
                                                                <td class="col-lg-4"><input type="text" name="tipo${filaCount2}" class="form-control" /></td>
                                                            </tr>
                                                        `;
                                                        $('#adjuntosTable tbody').append(nuevaFila);
                                                    });
                                                });
                                            </script>
                                        </div>

                                        <script>
                                            $(document).ready(function() {
                                                const clienteOpciones = [
                                                    'EVALUACIÓN POR MEDICINA DEL TRABAJO',
                                                    'INCLUSIÓN DE INFORMES MÉDICOS',
                                                    'HISTORIA CLÍNICA LEGALIZADA',
                                                    'INFORME DEL EMPLEADOR',
                                                    'INFORME MÉDICO',
                                                    'UNIFICACIÓN DE CUA',
                                                    'NO DESCUENTO 3%'
                                                ];
                                                const apoderadoOpciones = [
                                                    'COMPRA DE SERVICIOS',
                                                    'MODIFICACIÓN DE CITE',
                                                    'RECALIFICACIÓN DE DICTAMEN'
                                                ];
                                                const $tipoPdf = $('#tipoPdfSelect2');
                                                const $emisor = $('[name="emisor"]').first();
                                                const $emisorHidden = $('input[name="emisor"]').first();

                                                function actualizarEmisor() {
                                                    const selectedTipo = $tipoPdf.val();
                                                    let valor = '';

                                                    if (clienteOpciones.includes(selectedTipo)) {
                                                        valor = 'CLIENTE';
                                                        $emisor.val(valor).prop('disabled', true);
                                                    } else if (apoderadoOpciones.includes(selectedTipo)) {
                                                        valor = 'APODERADO';
                                                        $emisor.val(valor).prop('disabled', true);
                                                    } else {
                                                        $emisor.prop('disabled', false);
                                                        valor = $emisor.val();
                                                    }
                                                    $emisorHidden.val(valor);
                                                }
                                                actualizarEmisor();
                                                $tipoPdf.on('change', actualizarEmisor);
                                                $emisor.on('change', actualizarEmisor);
                                            });
                                        </script>
                                        <script>
                                            $(document).ready(function() {
                                                const input = $('#cambioactualizacionInput');
                                                const placeholderText = input.attr('placeholder');
                                                input.css('width', `${placeholderText.length * 0.95}ch`);
                                                input.on('input', function() {
                                                    const inputLength = $(this).val().length;
                                                    const newWidth = Math.max(inputLength * 0.95, placeholderText.length * 0.95);
                                                    $(this).css('width', `${newWidth}ch`);
                                                });
                                            });
                                        </script>
                                        <script>
                                            $(document).ready(function() {
                                                $('#tipoPdfSelect2').on('change', function() {
                                                    $('#cambioactualizacionContainer').hide();
                                                    $('#apoderadoContainer').hide();
                                                    $('#notatecnicomedicoContainer').hide();
                                                    $('#fechanotatecnicomedicoContainer').hide();
                                                    $('#tablaadjuntosContainer').hide();
                                                    $('#tablaEspecialidades').hide();
                                                    $('#tablaadjuntosContainer222').hide();
                                                    $('#matriculaContainer').hide();
                                                    $('#fechainformeestudioContainer').hide();
                                                    $('#afpgestoraContainer').hide();
                                                    $('#nombremedicoContainer').hide();
                                                    $('#cargomedicoContainer').hide();
                                                    $('#tablainformacionContainer').hide();
                                                    $('#especialidadinformeContainer').hide();
                                                    $('#medicotratanteContainer').hide();
                                                    $('#tablaabonoContainer').hide();
                                                    $('#fechacontratoContainer').hide();
                                                    $('#firmadoenContainer').hide();
                                                    $('#nrodictamenContainer').hide();
                                                    $('#fechatramiteContainer').hide();
                                                    $('#entidadcalificanteContainer').hide();
                                                    $('#origendictamenContainer').hide();
                                                    $('#tipoorigenContainer').hide();
                                                    $('#notificadoenContainer').hide();
                                                    $('#fechanotificacionContainer').hide();
                                                    $('#porcentajedictamenContainer').hide();
                                                    $('#nropasaporteContainer').hide();
                                                    $('#tablaceapasaporteContainer').hide();
                                                    $('#tablaceapasaporteContainer2').hide();
                                                    $('#nrocua1Container').hide();
                                                    $('#nombreafp1Container').hide();
                                                    $('#nroci1Container').hide();
                                                    $('#nrocua2Container').hide();
                                                    $('#nombreafp2Container').hide();
                                                    $('#nroci2Container').hide();
                                                    $('#nrocuaunificadoContainer').hide();
                                                    $('#nrociunificadoContainer').hide();
                                                    $('#texto1Container').hide();
                                                    $('#tablaunificacioncuaContainer').hide();
                                                    $('#tablacambiounificacioncuaContainer').hide();
                                                    $('#solicitudmodificarContainer').hide();
                                                    $('#campodirigidoaContainer').hide();
                                                    $('#campoestadolabContainer').hide();
                                                    $('#campoafiliadoaContainer').hide();

                                                    var selectedValue = $(this).val();
                                        
                                                    if (selectedValue === 'ACTUALIZACIÓN DE DATOS') {
                                                        $('#cambioactualizacionContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                    } else if (selectedValue === 'COMPRA DE SERVICIOS') {
                                                        $('#notatecnicomedicoContainer').show();
                                                        $('#fechanotatecnicomedicoContainer').show();
                                                        $('#tablaadjuntosContainer').show();
                                                        $('#texto1Container').show();
                                                        $('#nombremedicoContainer').show();
                                                    } else if (selectedValue === 'HISTORIA CLÍNICA LEGALIZADA') {            
                                                        $('#matriculaContainer').show();
                                                        $('#afpgestoraContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                        $('#cargomedicoContainer').show();
                                                    } else if (selectedValue === 'INCLUSIÓN DE INFORMES MÉDICOS') {            
                                                        $('#tablaEspecialidades').show();
                                                        $('#matriculaContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                        $('#cargomedicoContainer').show();
                                                    } else if (selectedValue === 'INFORME DEL EMPLEADOR') {            
                                                        $('#tablainformacionContainer').show();
                                                        $('#tablaadjuntosContainer').show();
                                                        $('#notatecnicomedicoContainer').show();
                                                        $('#fechanotatecnicomedicoContainer').show();
                                                    } else if (selectedValue === 'EVALUACIÓN POR MEDICINA DEL TRABAJO') {            
                                                        $('#matriculaContainer').show();
                                                        $('#afpgestoraContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                        $('#cargomedicoContainer').show();
                                                    } else if (selectedValue === 'MODIFICACIÓN DE CITE') {   
                                                        $('#tablaadjuntosContainer').show();         
                                                        $('#texto1Container').show();
                                                        $('#campodirigidoaContainer').show();
                                                        $('#campoestadolabContainer').show();
                                                        $('#campoafiliadoaContainer').show();
                                                        $('#solicitudmodificarContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                    } else if (selectedValue === 'INFORME MÉDICO') {   
                                                        $('#nombremedicoContainer').show();
                                                        $('#cargomedicoContainer').show();
                                                        $('#especialidadinformeContainer').show();
                                                        $('#medicotratanteContainer').show();
                                                        $('#tablaadjuntosContainer').show();
                                                    } else if (selectedValue === 'ABONO EN CUENTA') {   
                                                        $('#tablaabonoContainer').show();
                                                        $('#tablaadjuntosContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                    } else if (selectedValue === 'COPIA LEGALIZADA DE CONTRATO') {   
                                                        $('#fechacontratoContainer').show();
                                                        $('#firmadoenContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                    } else if (selectedValue === 'NO DESCUENTO 3%') {   
                                                        $('#nombremedicoContainer').show();
                                                        $('#cargomedicoContainer').show();
                                                        $('#tablaadjuntosContainer').show();
                                                    } else if (selectedValue === 'COPIA LEGALIZADA DE DICTAMEN') {   
                                                        $('#nrodictamenContainer').show();
                                                        $('#fechacontratoContainer').show();
                                                        $('#firmadoenContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                    } else if (selectedValue === 'REACTIVACIÓN DE TRÁMITE') {   
                                                        $('#fechatramiteContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                    } else if (selectedValue === 'RECALIFICACIÓN DE DICTAMEN') { 
                                                        $('#fechatramiteContainer').show();  
                                                        $('#entidadcalificanteContainer').show();
                                                        $('#origendictamenContainer').show();
                                                        $('#tipoorigenContainer').show();
                                                        $('#notificadoenContainer').show();
                                                        $('#fechanotificacionContainer').show();
                                                        $('#nrodictamenContainer').show();
                                                        $('#porcentajedictamenContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                    } else if (selectedValue === 'CAMBIO DE C.E. A PASAPORTE') { 
                                                        $('#nropasaporteContainer').show();  
                                                        $('#tablaadjuntosContainer').show();
                                                        $('#tablaceapasaporteContainer').show();
                                                        $('#tablaceapasaporteContainer2').show();
                                                        $('#nombremedicoContainer').show();
                                                    } else if (selectedValue === 'UNIFICACIÓN DE CUA') { 
                                                        /* $('#nrocua1Container').show(); */
                                                        $('#nombreafp1Container').show();
                                                        /* $('#nroci1Container').show(); */
                                                        $('#nrocua2Container').show();
                                                        $('#nombreafp2Container').show();
                                                        $('#nroci2Container').show();
                                                        /* $('#nrocuaunificadoContainer').show(); */
                                                        /* $('#nrociunificadoContainer').show(); */
                                                        $('#texto1Container').show();
                                                        $('#tablaunificacioncuaContainer').show();
                                                        $('#tablacambiounificacioncuaContainer').show();
                                                        $('#nombremedicoContainer').show();
                                                    }
                                                });
                                            });
                                        </script>

                                        {{-- <button type="submit" style="margin-top: 20px;" class="btn btn-sm btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content;">GENERAR SOLICITUD</button> --}}
                                        <button type="submit" id="btnGenerarSolicitud" class="btn btn-sm btn-subirarchivos d-block mx-auto mb-3" style="width: fit-content; margin-top: 20px;">GENERAR SOLICITUD</button>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const btn = document.getElementById('btnGenerarSolicitud');
                                                const form = document.getElementById('formSolicitud');
                                                btn.addEventListener('click', function(e) {
                                                    e.preventDefault();
                                                    const confirmar = confirm("¿Está seguro de que desea generar la solicitud?");
                                                    if (confirmar) {
                                                        form.submit();
                                                    }
                                                });
                                            });
                                        </script>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0" style="font-weight: 700;">VISTA PREVIA DE SOLICITUD</h5>
                                        <a id="btnActualizarVista" class="btn btn-sm btn-verdocumento">
                                            <i class="fas fa-sync-alt"></i> ACTUALIZAR
                                        </a>
                                    </div>
                                    <iframe id="pdfPreview" style="width:100%; height:500px; border:1px solid #ddd;"></iframe>
                                </div>
                            </div>
                        </div>

                        {{-- <script>
                            $(document).ready(function() {
                                let debounceTimer;

                                function updatePDFPreview() {
                                    let formData = $('#formSolicitud').serialize();
                                    let clienteId = "{{ $cliente->id }}";

                                    // Validar campos obligatorios antes de generar PDF
                                    let nivelProcedimiento = $('[name="nivelprocedimiento"]').val();
                                    let tipoPdf = $('[name="tipo_pdf"]').val();
                                    if (!nivelProcedimiento || !tipoPdf) {
                                        $('#pdfPreview').attr('src', '');
                                        return;
                                    }

                                    $('#pdfPreview').attr('src', '{{ url("/preview-pdf") }}/' + clienteId + '?' + formData);
                                }

                                // Debounce: espera 500ms después de escribir
                                $('#formSolicitud input, #formSolicitud textarea').on('input', function() {
                                    clearTimeout(debounceTimer);
                                    debounceTimer = setTimeout(updatePDFPreview, 500);
                                });

                                // Para selects, actualiza inmediato
                                $('#formSolicitud select').on('change', function() {
                                    updatePDFPreview();
                                });
                            });
                        </script> --}}
                        <script>
                            $(document).ready(function() {
                                function updatePDFPreview() {
                                    let formData = $('#formSolicitud').serialize();
                                    let clienteId = "{{ $cliente->id }}";
                                    let nivelProcedimiento = $('[name="nivelprocedimiento"]').val();
                                    let tipoPdf = $('[name="tipo_pdf"]').val();
                                    if (!nivelProcedimiento || !tipoPdf) {
                                        $('#pdfPreview').attr('src', '');
                                        return;
                                    }
                                    $('#pdfPreview').attr('src', '{{ url("/preview-pdf") }}/' + clienteId + '?' + formData);
                                }
                                $('[name="nivelprocedimiento"], [name="tipo_pdf"]').on('change', function() {
                                    updatePDFPreview();
                                });
                                $('#btnActualizarVista').on('click', function(e) {
                                    e.preventDefault();
                                    updatePDFPreview();
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-content-2" role="tabpanel" aria-labelledby="tab-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card">
                                <div class="card-body">
                                    <h5 style="margin-bottom: 15px; font-weight: 700;">DATOS DEL ADJUNTO/RESPUESTA</h5>
                                    <form action="{{ route('admin.tramites.generaradjuntoyrespuesta', $cliente) }}" method="GET" enctype="multipart/form-data" id="formAdjunto">
                                        {!! Form::hidden('usuarioid2', auth()->user()->id) !!}
                                        {!! Form::hidden('usuarioregistro2', auth()->user()->name) !!}
                                        {!! Form::hidden('clienteid2', $cliente->id) !!}
                                        {!! Form::hidden('clientenombre2', $cliente->nombrecompleto) !!}
                                        {!! Form::hidden('apoderado2', $apoderadoAsignado) !!}
                                        {!! Form::hidden('idtramite2', $idTramite) !!}
                                        <input type="text" class="form-control" id="tramite2" name="tramite2" value="PENSIÓN POR MUERTE" hidden>
                                        <input type="date" class="form-control" id="fechasubida2" name="fechasubida2" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="form-group col-lg-5">
                                                        {!! Form::label('nivelprocedimiento2', 'Nivel Procedimiento:') !!}
                                                        {!! Form::select('nivelprocedimiento2', [
                                                            'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO' => 'SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO',
                                                            'COMPRA DE SERVICIOS' => 'COMPRA DE SERVICIOS',
                                                            'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA' => 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA',
                                                            'COMPLEMENTACIÓN DEL TRÁMITE' => 'COMPLEMENTACIÓN DEL TRÁMITE',
                                                        ], null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-5">
                                                        {!! Form::label('tipo_pdf2', 'Tipo de Adjunto y Respuesta:') !!}
                                                        {!! Form::select('tipo_pdf2', [
                                                            'ADJUNTO DE DOCUMENTOS' => 'ADJUNTO DE DOCUMENTOS',
                                                            'ADJUNTO DE DOCUMENTACIÓN MÉDICA' => 'ADJUNTO DE DOCUMENTACIÓN MÉDICA',
                                                            'ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR' => 'ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR',
                                                            'ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC' => 'ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC',
                                                            'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO' => 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO',
                                                            'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO' => 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO',
                                                            'CARTA ACLARATIVA' => 'CARTA ACLARATIVA'                                
                                                        ], null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required', 'id' => 'tipoPdfSelect22']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        {!! Form::label('fechaemitido2', 'Fecha Carta:') !!}
                                                        <input type="date" class="form-control" id="fechaactual2" name="fechaactual2" value="{{ \Carbon\Carbon::now()->toDateString() }}" min="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    </div>
                                                    {{-- NUEVO 111125 --}}
                                                    <div class="form-group col-lg-4">
                                                        {!! Form::label('apoderado2', 'Emisor Apoderado:') !!}
                                                        {!! Form::select('apoderado2', 
                                                            array_combine($apoderados, $apoderados),
                                                            $apoderadoAsignado,
                                                            ['class' => 'form-control']
                                                        ) !!}
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label for="fontsize2">Tamaño_Fuente:</label>
                                                        <select id="fontsize2" class="form-control">
                                                            <option value="12px">9</option>
                                                            <option value="13px">10</option>
                                                            <option value="14px">11</option>
                                                            <option value="15px" selected>12</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label for="marginSize2">Márgenes:</label>
                                                        <select id="marginSize2" class="form-control">
                                                            <option value="1cm 1.5cm 1cm 1.5cm">BAJO</option>
                                                            <option value="1.5cm 3cm 1.5cm 3cm" selected>MEDIO</option>
                                                            <option value="2cm 3.5cm 2cm 3.5cm">ALTO</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nombremedico2Container" style="display: none;">
                                                        {!! Form::label('nombremedico2', 'Destinatario:') !!}
                                                        {!! Form::text('nombremedico2', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="cargomedico2Container" style="display: none;">
                                                        {!! Form::label('cargomedico2', 'Cargo Destinatario:') !!}
                                                        {!! Form::text('cargomedico2', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="notatecnicomedico2Container" style="display: none;">
                                                        {!! Form::label('notatecnicomedico2', 'Nota Cite:') !!}
                                                        {!! Form::text('notatecnicomedico2', null, ['class' => 'form-control']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="fechanotatecnicomedico2Container" style="display: none;">
                                                        {!! Form::label('fechanotatecnicomedico2', 'Fecha Nota Cite:') !!}
                                                        {!! Form::date('fechanotatecnicomedico2', null, ['class' => 'form-control', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="documentoadjunto2Container" style="display: none;">
                                                        {!! Form::label('documentoadjunto2', 'Documento Adjunto:') !!}
                                                        {!! Form::select('documentoadjunto2', [
                                                            '' => '',
                                                            'CARNET DE IDENTIDAD' => 'CARNET DE IDENTIDAD',
                                                            'CERTIFICADO DE MATRIMONIO' => 'CERTIFICADO DE MATRIMONIO',
                                                            'CERTIFICADO DE NACIMIENTO' => 'CERTIFICADO DE NACIMIENTO',
                                                            'CERTIFICADO DE NACIMIENTO (EXTRANJERO)' => 'CERTIFICADO DE NACIMIENTO (EXTRANJERO)',
                                                            'CROQUIS DE DOMICILIO' => 'CROQUIS DE DOMICILIO',
                                                        ], null, ['class' => 'form-control']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="textocomplementario2Container" style="display: none;">
                                                        {!! Form::label('textocomplementario2', 'Texto Complementario:') !!}
                                                        {!! Form::text('textocomplementario2', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => 150]) !!}
                                                    </div>
                                                </div>

                                                <div class="row" id="tablaEspecialistas" style="display: none;">
                                                    <div class="col-lg-5">
                                                        <div id="contenedor_areas" class="mt-3">
                                                            @foreach ($programaciones as $fecha => $grupos)
                                                                <div class="card shadow-sm border mb-2">
                                                                    
                                                                    <div class="card-header py-2 px-3 bg-secondary text-white">
                                                                        <button class="btn btn-link text-white text-left w-100 p-0" type="button"
                                                                            data-toggle="collapse" data-target="#fecha_{{ \Str::slug($fecha) }}">
                                                                            <strong>FECHA BATERIA:</strong> {{ $fecha }}
                                                                        </button>
                                                                    </div>

                                                                    <div id="fecha_{{ \Str::slug($fecha) }}" class="collapse">
                                                                        <div class="card-body py-2 px-3">
                                                                            @foreach ($grupos->groupBy('areanombre') as $area => $acciones)
                                                                                <div class="card border mb-2">
                                                                                    <div class="card-header py-2 px-3 bg-light">
                                                                                        <button class="btn btn-sm btn-outline-secondary w-100 text-left p-1"
                                                                                            type="button"
                                                                                            data-toggle="collapse"
                                                                                            data-target="#area_{{ \Str::slug($fecha . '_' . $area) }}">
                                                                                            <strong>ÁREA:</strong> {{ $area }}
                                                                                        </button>
                                                                                    </div>

                                                                                    <div id="area_{{ \Str::slug($fecha . '_' . $area) }}" class="collapse" style="margin-top: -30px;">
                                                                                        <div class="card-body pt-2 pb-1 px-3">
                                                                                            <div class="table-responsive">
                                                                                                <table class="table table-sm table-bordered mb-3" style="white-space: nowrap;">
                                                                                                    <thead class="thead-light text-center">
                                                                                                        <tr>
                                                                                                            <th class="text-center"><input type="checkbox" class="seleccionar-todo-area2" data-area="{{ \Str::slug($fecha . '_' . $area) }}" /></th>
                                                                                                            <th>Ver</th>
                                                                                                            <th>ID</th>
                                                                                                            <th>Estudio/Espec.</th>
                                                                                                            <th>Proveedor</th>
                                                                                                            <th>Hojas</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        @foreach ($acciones as $doc)
                                                                                                            <tr>
                                                                                                                <td class="text-center align-middle">
                                                                                                                    <input type="checkbox" class="documento-checkbox2"
                                                                                                                        data-proveedor="{{ $doc->proveedor_real }}"
                                                                                                                        data-area="{{ $doc->areanombre }}"
                                                                                                                        data-accion="{{ $doc->accionnombre }}"
                                                                                                                        data-hojas="{{ $doc->nro_hojas ?? 0 }}">
                                                                                                                </td>
                                                                                                                <td class="text-center align-middle">
                                                                                                                    <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->document}") }}"
                                                                                                                        target="_blank" class="btn btn-sm btn-verdoc" title="Ver documento">
                                                                                                                        <i class="fas fa-eye"></i>
                                                                                                                    </a>
                                                                                                                    @if(!empty($doc->image))
                                                                                                                    <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->image}") }}"
                                                                                                                    target="_blank" class="btn btn-sm btn-verdoc" title="Ver Imagen 1">
                                                                                                                        <i class="fas fa-image"></i>
                                                                                                                    </a>
                                                                                                                    @endif
                                                                                                                    @if(!empty($doc->image2))
                                                                                                                    <a href="{{ url("documentacionclientesita/{$cliente->id}/{$doc->image2}") }}"
                                                                                                                    target="_blank" class="btn btn-sm btn-verdoc" title="Ver Imagen 2">
                                                                                                                        <i class="fas fa-image"></i>
                                                                                                                    </a>
                                                                                                                    @endif
                                                                                                                </td>
                                                                                                                <td class="align-middle">{{ $doc->doc_id }}</td>
                                                                                                                <td class="align-middle">{{ $doc->accionnombre }}</td>
                                                                                                                <td class="align-middle">{{ $doc->proveedor_real ?? 'Sin proveedor' }}</td>
                                                                                                                <td class="text-center align-middle">
                                                                                                                    <span class="badge" style="background-color: #faa625; color: #ffffff; font-size: 0.8rem; padding: 0.2em 0.3em; line-height: 1;">
                                                                                                                        {{ $doc->nro_hojas ?? '?' }}
                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        @endforeach
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                            <div class="text-right mt-3">
                                                                                <div class="form-group row justify-content-end align-items-center">
                                                                                    <div class="col-sm-8">
                                                                                        <select id="tipoDocumento2" name="tipoDocumento2" class="form-control form-control-sm">
                                                                                            <option value="">Seleccione una opción...</option>
                                                                                            <option value="INFORME MÉDICO DE">INFORME MÉDICO DE</option>
                                                                                            <option value="CERTIFICADO MÉDICO DE">CERTIFICADO MÉDICO DE</option>
                                                                                            <option value="ESTUDIO DE">ESTUDIO DE</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-auto">
                                                                                        <button id="btnAgregarSeleccionados2" type="button" class="btn btn-sm btn-adjuntosrespuestas">
                                                                                            <i class="fas fa-plus"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-7">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead class="table-secondary">
                                                                    <tr style="text-align: center">
                                                                        <th class="col-lg-5">ESPECIALISTA</th>
                                                                        <th class="col-lg-5">DETALLE DE ESTUDIO/ESPECIALIDAD</th>
                                                                        <th class="col-lg-2">CANTIDAD</th>
                                                                        <th class="col-lg-2">QUITAR</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="tabla-especialistas2">

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <script>
                                                $(document).ready(function() {
                                                    $('#tipoPdfSelect22').on('change', function() {
                                                        $('#notatecnicomedico2Container').hide();
                                                        $('#fechanotatecnicomedico2Container').hide();
                                                        $('#documentoadjunto2Container').hide();
                                                        $('#textocomplementario2Container').hide();
                                                        $('#tablaEspecialistas').hide();
                                                        $('#nombremedico2Container').hide();
                                                        $('#cargomedico2Container').hide();

                                                        var selectedValue = $(this).val();
                                            
                                                        if (selectedValue === 'ADJUNTO DE DOCUMENTOS') {
                                                            $('#notatecnicomedico2Container').show();
                                                            $('#fechanotatecnicomedico2Container').show();
                                                            $('#documentoadjunto2Container').show();
                                                            $('#textocomplementario2Container').show();
                                                            $('#nombremedico2Container').show();
                                                        } else if (selectedValue === 'ADJUNTO DE DOCUMENTACIÓN MÉDICA') {
                                                            $('#notatecnicomedico2Container').show();
                                                            $('#fechanotatecnicomedico2Container').show();
                                                            $('#tablaEspecialistas').show();
                                                            $('#nombremedico2Container').show();
                                                        } else if (selectedValue === 'ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR') {
                                                            $('#notatecnicomedico2Container').show();
                                                            $('#fechanotatecnicomedico2Container').show();
                                                            $('#tablaEspecialistas').show();
                                                            $('#nombremedico2Container').show();
                                                        } else if (selectedValue === 'ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC') {
                                                            $('#notatecnicomedico2Container').show();
                                                            $('#fechanotatecnicomedico2Container').show();
                                                            $('#tablaEspecialistas').show();
                                                            $('#nombremedico2Container').show();
                                                        } else if (selectedValue === 'ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO') {
                                                            $('#notatecnicomedico2Container').show();
                                                            $('#fechanotatecnicomedico2Container').show();
                                                            $('#tablaEspecialistas').show();
                                                            $('#nombremedico2Container').show();
                                                        } else if (selectedValue === 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO') {
                                                            $('#notatecnicomedico2Container').show();
                                                            $('#fechanotatecnicomedico2Container').show();
                                                            $('#tablaEspecialistas').show();
                                                            $('#nombremedico2Container').show();
                                                        }
                                                    });
                                                });
                                            </script>
                                        </div>
                                        <button type="submit" id="btnGenerarAdjunto" class="btn btn-sm btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content; margin-top: 20px;">GENERAR ADJUNTO Y RESPUESTA</button>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const btn = document.getElementById('btnGenerarAdjunto');
                                                const form = document.getElementById('formAdjunto');
                                                btn.addEventListener('click', function(e) {
                                                    e.preventDefault();
                                                    const confirmar = confirm("¿Está seguro de que desea generar el adjunto/respuesta?");
                                                    if (confirmar) {
                                                        form.submit();
                                                    }
                                                });
                                            });
                                        </script>
                                    </form>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const tabla = document.getElementById('tabla-especialistas2');
                                            
                                            function llenarTablaConSeleccionados2() {
                                                const seleccionados = document.querySelectorAll('.documento-checkbox2:checked');
                                                const tipoDocumento2 = document.getElementById('tipoDocumento2').value.trim();
                                                console.log('Checkbox seleccionados:', seleccionados.length);

                                                const agrupados = {};

                                                seleccionados.forEach(input => {
                                                    const proveedor = input.dataset.proveedor;
                                                    const area = input.dataset.area;
                                                    const hojas = parseInt(input.dataset.hojas || 0);

                                                    if (!agrupados[proveedor]) {
                                                        agrupados[proveedor] = {
                                                            area: area,
                                                            totalHojas: 0
                                                        };
                                                    }

                                                    agrupados[proveedor].totalHojas += hojas;
                                                });

                                                let filasActuales = tabla.querySelectorAll('tr').length;
                                                let i = filasActuales + 1;

                                                for (const proveedor in agrupados) {
                                                    if (i > 10) break;

                                                    const area = agrupados[proveedor].area || '';
                                                    const detalle = tipoDocumento2 && area ? `${tipoDocumento2} ${area}` : area || tipoDocumento2 || '';

                                                    const hojas = agrupados[proveedor].totalHojas;

                                                    const fila = `<tr>
                                                        <td><input type="text" name="especialista2${i}" class="form-control" value="${proveedor}" /></td>
                                                        <td><input type="text" name="detalle2${i}" class="form-control" value="${detalle}" /></td>
                                                        <td><input type="text" name="cantidad2${i}" class="form-control" value="${hojas}" /></td>
                                                        <td><button type="button" class="btn btn-outline-danger btn-sm quitar-fila"><i class="fas fa-trash"></i></button></td>
                                                    </tr>`;

                                                    tabla.insertAdjacentHTML('beforeend', fila);
                                                    i++;
                                                    // Agrega el evento al nuevo botón
                                                    const ultimaFila = tabla.querySelector('tr:last-child');
                                                    const btnQuitar = ultimaFila.querySelector('.quitar-fila');

                                                    btnQuitar.addEventListener('click', function () {
                                                        ultimaFila.remove();
                                                    });
                                                }
                                            }

                                            document.getElementById('btnAgregarSeleccionados2').addEventListener('click', function() {
                                                llenarTablaConSeleccionados2();

                                                const seleccionados = document.querySelectorAll('.documento-checkbox2:checked');
                                                seleccionados.forEach(checkbox => checkbox.checked = false);

                                                const checkboxesSeleccionarTodo = document.querySelectorAll('.seleccionar-todo-area2');
                                                checkboxesSeleccionarTodo.forEach(chk => chk.checked = false);
                                            });

                                            document.querySelectorAll('.seleccionar-area2').forEach(btn => {
                                                btn.addEventListener('click', function () {
                                                    const area = btn.dataset.area;
                                                    const checkboxes = document.querySelectorAll(`#area_${area} .documento-checkbox2`);
                                                    checkboxes.forEach(c => c.checked = true);
                                                });
                                            });

                                            document.querySelectorAll('.seleccionar-todo-area2').forEach(checkbox => {
                                                checkbox.addEventListener('change', function () {
                                                    const area = this.dataset.area;
                                                    const checkboxes = document.querySelectorAll(`#area_${area} .documento-checkbox2`);
                                                    checkboxes.forEach(c => c.checked = this.checked);
                                                });
                                            });
                                        });
                                    </script>
                                    <script>
                                        document.getElementById('fechabateria_select2').addEventListener('change', function () {
                                            let todas = document.querySelectorAll('.grupo_fecha');
                                            todas.forEach(div => div.style.display = 'none');
                                            let seleccionada = this.value;
                                            if (seleccionada) {
                                                let slug = seleccionada.replaceAll(/[^a-zA-Z0-9]/g, '-').toLowerCase();
                                                let grupo = document.querySelector('.grupo_fecha_' + slug);
                                                if (grupo) grupo.style.display = 'block';
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0" style="font-weight: 700;">VISTA PREVIA DEL ADJUNTO/RESPUESTA</h5>
                                        <a id="btnActualizarVistaAdjunto" class="btn btn-sm btn-verdocumento">
                                            <i class="fas fa-sync-alt"></i> ACTUALIZAR
                                        </a>
                                    </div>
                                    <iframe id="pdfPreview2" style="width:100%; height:500px; border:1px solid #ddd;"></iframe>
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).ready(function() {
                                function updatePDFPreview2() {
                                    let formData2 = $('#formAdjunto').serialize();
                                    let fontSize2 = $('#fontsize2').val() || '15px';
                                    formData2 += '&fontsize2=' + encodeURIComponent(fontSize2);
                                    let marginSize2 = $('#marginSize2').val() || '1.5cm 3cm 1.5cm 3cm';
                                    formData2 += '&marginsize2=' + encodeURIComponent(marginSize2);
                                    let clienteId2 = "{{ $cliente->id }}";
                                    let nivelProcedimiento2 = $('[name="nivelprocedimiento2"]').val();
                                    let tipoPdf2 = $('[name="tipo_pdf2"]').val();
                                    if (!nivelProcedimiento2 || !tipoPdf2) {
                                        $('#pdfPreview2').attr('src', '');
                                        return;
                                    }
                                    $('#pdfPreview2').attr('src', '{{ url("/preview-adjunto") }}/' + clienteId2 + '?' + formData2);
                                }
                                $('[name="nivelprocedimiento2"], [name="tipo_pdf2"], #fontsize2, #marginSize2').on('change', function() {
                                    updatePDFPreview2();
                                });
                                $('#btnActualizarVistaAdjunto').on('click', function(e) {
                                    e.preventDefault();
                                    updatePDFPreview2();
                                });
                                updatePDFPreview2();
                            });
                        </script>
                    </div>
                </div>
            </div> 
        
            <div class="tab-pane fade" id="tab-content-3" role="tabpanel" aria-labelledby="tab-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card">
                                <div class="card-body">
                                    <h5 style="margin-bottom: 15px; font-weight: 700;">DATOS DE LA CARTA/RECLAMO</h5>
                                    <form action="{{ route('admin.tramites.generarcartareclamo', $cliente) }}" method="GET" enctype="multipart/form-data" id="formCarta">
                                        {!! Form::hidden('usuarioid3', auth()->user()->id) !!}
                                        {!! Form::hidden('usuarioregistro3', auth()->user()->name) !!}
                                        {!! Form::hidden('clienteid3', $cliente->id) !!}
                                        {!! Form::hidden('clientenombre3', $cliente->nombrecompleto) !!}
                                        {!! Form::hidden('apoderado3', $apoderadoAsignado) !!}
                                        {!! Form::hidden('idtramite3', $idTramite) !!}
                                        <input type="text" class="form-control" id="tramite" name="tramite3" value="PENSIÓN POR MUERTE" hidden>
                                        <input type="date" class="form-control" id="fechasubida3" name="fechasubida3" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                                        @csrf
                                        <div class="row">
                                            @php
                                                $documento1 = $cliente->tramites()->where('tramite', 'PENSIÓN POR MUERTE')->where('nivelprocedimiento', 'INGRESO DE TRÁMITE')->where('subprocedimiento', 'RECEPCIÓN DE TRÁMITE')->first();
                                                $documento5 = $cliente->tramites()->where('tramite', 'PENSIÓN POR MUERTE')->where('nivelprocedimiento', 'FIRMA EAP')->where('subprocedimiento', 'ESTADO DE AHORRO PREVISIONAL')->first();
                                                $fechaingresoyeap = $documento1 && $documento5;

                                                $primeracartasit = $cliente->tramites()->where('tramite', 'PENSIÓN POR MUERTE')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'PRIMERA CARTA SIT')->first();
                                                $segundacartasit = $cliente->tramites()->where('tramite', 'PENSIÓN POR MUERTE')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'SEGUNDA CARTA SIT')->first();
                                                $terceracartasit = $cliente->tramites()->where('tramite', 'PENSIÓN POR MUERTE')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'TERCERA CARTA SIT')->first();
                                                $primeracartareclamo = $cliente->tramites()->where('tramite', 'PENSIÓN POR MUERTE')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'PRIMERA CARTA DE RECLAMO')->first();
                                                $segundacartareclamo = $cliente->tramites()->where('tramite', 'PENSIÓN POR MUERTE')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'SEGUNDA CARTA DE RECLAMO')->first();
                                                $terceracartareclamo = $cliente->tramites()->where('tramite', 'PENSIÓN POR MUERTE')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'TERCERA CARTA DE RECLAMO')->first();
                                                $cartareclamoaps = $cliente->tramites()->where('tramite', 'PENSIÓN POR MUERTE')->where('nivelprocedimiento', 'CARTAS / RECLAMOS')->where('subprocedimiento', 'CARTA DE RECLAMO APS')->first();
                                            @endphp

                                            <div class="form-group col-lg-8">
                                                {!! Form::label('nivelprocedimiento3', 'Nivel Procedimiento:') !!}
                                                {!! Form::select('nivelprocedimiento3', [
                                                    'INGRESO DE TRÁMITE' => 'INGRESO DE TRÁMITE',
                                                    'VALIDACIÓN DE TRÁMITE' => 'VALIDACIÓN DE TRÁMITE',
                                                    'FIRMA EAP' => 'FIRMA EAP',
                                                    'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - ÓBITO' => 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - ÓBITO',
                                                    'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - FORENSE' => 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - FORENSE',
                                                    'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - EMPLEADOR' => 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - EMPLEADOR',
                                                    'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - CERTIFICADO DE ESTUDIOS' => 'SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - CERTIFICADO DE ESTUDIOS',
                                                    'COMPLEMENTACIÓN DEL TRÁMITE' => 'COMPLEMENTACIÓN DEL TRÁMITE',
                                                    'RESPUESTAS ERRÓNEAS' => 'RESPUESTAS ERRÓNEAS',
                                                ], null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required', 'id' => 'nivelprocedimiento3']) !!}
                                            </div>
                                            <div class="form-group col-lg-4">
                                                {!! Form::label('subnivelprocedimiento3', 'Sub Nivel Procedimiento:') !!}
                                                {!! Form::select('subnivelprocedimiento3', [
                                                    'INGRESO DE TRÁMITE' => 'INGRESO DE TRÁMITE',
                                                    'VALIDACIÓN DE TRÁMITE' => 'VALIDACIÓN DE TRÁMITE',
                                                    'FIRMA EAP' => 'FIRMA EAP',
                                                    'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO' => 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO',
                                                    'ADJUNTO DE DOCUMENTOS' => 'ADJUNTO DE DOCUMENTOS',
                                                    'ADJUNTO DE DOCUMENTACIÓN MÉDICA' => 'ADJUNTO DE DOCUMENTACIÓN MÉDICA',
                                                    'SOLICITUD DE COMPRA DE SERVICIOS' => 'SOLICITUD DE COMPRA DE SERVICIOS',
                                                    'SOLICITUD DE MODIFICACIÓN DE CITE' => 'SOLICITUD DE MODIFICACIÓN DE CITE',
                                                ], null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required', 'id' => 'subnivelprocedimiento3']) !!}
                                            </div>
                                            <script>
                                                document.addEventListener("DOMContentLoaded", function () {
                                                    const nivel = document.getElementById("nivelprocedimiento3");
                                                    const subnivel = document.getElementById("subnivelprocedimiento3");
                                                    const opciones = {
                                                        "INGRESO DE TRÁMITE": ["INGRESO DE TRÁMITE"],
                                                        "VALIDACIÓN DE TRÁMITE": ["VALIDACIÓN DE TRÁMITE"],
                                                        "FIRMA EAP": ["FIRMA EAP"],
                                                        "SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - ÓBITO": ["ADJUNTO Y RESPUESTA AL COMPLEMENTARIO"],
                                                        "SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - FORENSE": ["ADJUNTO Y RESPUESTA AL COMPLEMENTARIO"],
                                                        "SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - EMPLEADOR": ["ADJUNTO Y RESPUESTA AL COMPLEMENTARIO"],
                                                        "SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - CERTIFICADO DE ESTUDIOS": ["ADJUNTO Y RESPUESTA AL COMPLEMENTARIO"],
                                                        "COMPLEMENTACIÓN DEL TRÁMITE": ["ADJUNTO DE DOCUMENTOS", "ADJUNTO DE DOCUMENTACIÓN MÉDICA"],
                                                        "RESPUESTAS ERRÓNEAS": ["SOLICITUD DE COMPRA DE SERVICIOS", "SOLICITUD DE MODIFICACIÓN DE CITE"],
                                                    };
                                                    function actualizarSubniveles() {
                                                        const seleccion = nivel.value;
                                                        subnivel.innerHTML = '<option value="">Seleccione...</option>';
                                                        if (opciones[seleccion]) {
                                                            opciones[seleccion].forEach(val => {
                                                                const opt = document.createElement("option");
                                                                opt.value = val;
                                                                opt.textContent = val;
                                                                subnivel.appendChild(opt);
                                                            });
                                                        }
                                                    }
                                                    nivel.addEventListener("change", actualizarSubniveles);
                                                });
                                            </script>
                                            <div class="form-group col-lg-2">
                                                {!! Form::label('tipo_pdf3', 'Tipo Carta:') !!}
                                                {!! Form::select('tipo_pdf3', [
                                                    'PRIMERA CARTA SIT' => 'PRIMERA CARTA SIT',
                                                    'SEGUNDA CARTA SIT' => 'SEGUNDA CARTA SIT',
                                                    'TERCERA CARTA SIT' => 'TERCERA CARTA SIT',
                                                    'PRIMERA CARTA DE RECLAMO GP' => 'PRIMERA CARTA DE RECLAMO GP',
                                                    'PRIMERA CARTA DE RECLAMO APS' => 'PRIMERA CARTA DE RECLAMO APS',
                                                    'SEGUNDA CARTA DE RECLAMO GP' => 'SEGUNDA CARTA DE RECLAMO GP',
                                                    'SEGUNDA CARTA DE RECLAMO APS' => 'SEGUNDA CARTA DE RECLAMO APS',
                                                    'TERCERA CARTA DE RECLAMO GP' => 'TERCERA CARTA DE RECLAMO GP',
                                                    'TERCERA CARTA DE RECLAMO APS' => 'TERCERA CARTA DE RECLAMO APS',
                                                    'REITERACIÓN A CARTAS DE RECLAMO GP' => 'REITERACIÓN A CARTAS DE RECLAMO GP',
                                                    'REITERACIÓN A CARTAS DE RECLAMO APS' => 'REITERACIÓN A CARTAS DE RECLAMO APS',
                                                ], null, ['class' => 'form-control', 'placeholder' => '', 'required' => 'required', 'id' => 'tipo_pdf3']) !!}
                                            </div>
                                            <div class="form-group col-lg-2">
                                                {!! Form::label('fechaemitido3', 'Fecha Carta:') !!}
                                                <input type="date" class="form-control" id="fechaactual3" name="fechaactual3" value="{{ \Carbon\Carbon::now()->toDateString() }}" min="{{ \Carbon\Carbon::now()->toDateString() }}">
                                            </div>
                                            <div class="form-group col-lg-2">
                                                {!! Form::label('fecharetorno3', 'Fecha Retorno:') !!}
                                                <input type="date" class="form-control" id="fecharetorno3" name="fecharetorno3" value="{{ \Carbon\Carbon::now()->toDateString() }}" readonly>
                                            </div>
                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const fechaActual = document.getElementById('fechaactual3');
                                                    const fechaSubida = document.getElementById('fechasubida3');
                                                    const fechaRetorno = document.getElementById('fecharetorno3');
                                                    const tipoPdf = document.getElementById('tipo_pdf3');
                                                    function sumarDias(fecha, dias) {
                                                        const f = new Date(fecha);
                                                        f.setDate(f.getDate() + dias);
                                                        return f.toISOString().split('T')[0];
                                                    }
                                                    fechaActual.addEventListener('change', function() {
                                                        fechaSubida.value = this.value;
                                                        const tipo = tipoPdf.value;
                                                        let diasSumar = (tipo === 'TERCERA CARTA SIT') ? 15 : 20;
                                                        fechaRetorno.value = sumarDias(this.value, diasSumar);
                                                    });
                                                    tipoPdf.addEventListener('change', function() {
                                                        const tipo = this.value;
                                                        let diasSumar = (tipo === 'TERCERA CARTA SIT') ? 15 : 20;
                                                        fechaRetorno.value = sumarDias(fechaActual.value, diasSumar);
                                                    });
                                                });
                                            </script>
                                            {{-- NUEVO 111125 --}}
                                            <div class="form-group col-lg-4">
                                                {!! Form::label('apoderado3', 'Emisor Apoderado:') !!}
                                                {!! Form::select('apoderado3', 
                                                    array_combine($apoderados, $apoderados),
                                                    $apoderadoAsignado,
                                                    ['class' => 'form-control']
                                                ) !!}
                                            </div>
                                            <div class="form-group col-lg-2">
                                                <label for="fontsize">Tamaño_Fuente:</label>
                                                <select id="fontsize" class="form-control">
                                                    <option value="12px">9</option>
                                                    <option value="13px">10</option>
                                                    <option value="14px">11</option>
                                                    <option value="15px" selected>12</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-2">
                                                <label for="marginSize">Márgenes:</label>
                                                <select id="marginSize" class="form-control">
                                                    <option value="1cm 1.5cm 1cm 1.5cm">BAJO</option>
                                                    <option value="1.5cm 3cm 1.5cm 3cm" selected>MEDIO</option>
                                                    <option value="2cm 3.5cm 2cm 3.5cm">ALTO</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-lg-2">
                                                {!! Form::label('fechaadjmedica', 'Fecha Adj. Doc.:') !!}
                                                {!! Form::date('fechaadjmedica', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            {{-- PRIMERA CARTA SIT ADJUNTO DE DOCUMENTOS Y DOCUMENTACIÓN MÉDICA --}}
                                            <div class="form-group col-lg-4" id="fechaadjuntoContainer" style="display: none;">
                                                {!! Form::label('fechaadjunto3', 'Fecha Adjunto:') !!}
                                                {!! Form::date('fechaadjunto3', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-4" id="tipoadjuntoContainer" style="display: none;">
                                                {!! Form::label('tipoadjunto3', 'Tipo Adjunto:') !!}
                                                {!! Form::select('tipoadjunto3', [
                                                    'INFORMES Y ESTUDIOS MEDICOS' => 'INFORMES Y ESTUDIOS MEDICOS',
                                                    'CERTIFICADO DE EXTRANJERO' => 'CERTIFICADO DE EXTRANJERO',
                                                    'BOLETAS DE PAGO' => 'BOLETAS DE PAGO',
                                                    'CERTIFICADO DE TRABAJO' => 'CERTIFICADO DE TRABAJO',
                                                    'DENUNCIA DE ACCIDENTE' => 'DENUNCIA DE ACCIDENTE',
                                                ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-6" id="nombremedico3Container" style="display: none;">
                                                {!! Form::label('nombremedico3', 'Destinatario:') !!}
                                                {!! Form::text('nombremedico3', null, ['class' => 'form-control']) !!}
                                            </div>
                                            <div class="form-group col-lg-6" id="cargomedico3Container" style="display: none;">
                                                {!! Form::label('cargomedico3', 'Cargo Destinatario:') !!}
                                                {!! Form::text('cargomedico3', null, ['class' => 'form-control']) !!}
                                            </div>
                                            <div class="form-group col-lg-4" id="fechaconclusionprog3Container" style="display: none;">
                                                {!! Form::label('fechaconclusionprog3', 'Fecha Conclusión Prog.:') !!}
                                                {!! Form::date('fechaconclusionprog3', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div> 
                                            <div class="form-group col-lg-4" id="solmodificar3Container" style="display: none;">
                                                {!! Form::label('solmodificar3', 'Solic. Modif.:') !!}
                                                {!! Form::select('solmodificar3', [
                                                    'TÉCNICO MÉDICO' => 'TÉCNICO MÉDICO',
                                                    'COMPLEMENTARIO' => 'COMPLEMENTARIO',
                                                    'ACTA DEL TMC' => 'ACTA DEL TMC',
                                                    'DEL EMPLEADOR' => 'DEL EMPLEADOR',
                                                ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-4" id="nronota3Container" style="display: none;">
                                                {!! Form::label('nronota3', 'Nota:') !!}
                                                {!! Form::text('nronota3', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-4" id="fechanota3Container" style="display: none;">
                                                {!! Form::label('fechanota3', 'Fecha de Nota:') !!}
                                                {!! Form::date('fechanota3', null, ['class' => 'form-control', 'placeholder' => '', 'value' => '{{ \Carbon\Carbon::now()->toDateString() }}']) !!}
                                            </div> 
                                            <div class="form-group col-lg-4" id="dirigidoa3Container" style="display: none;">
                                                {!! Form::label('dirigidoa3', 'Dirigido a:') !!}
                                                {!! Form::text('dirigidoa3', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-4" id="estadolab3Container" style="display: none;">
                                                {!! Form::label('estadolab3', 'Estado Lab.:') !!}
                                                {!! Form::text('estadolab3', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-4" id="afiliadoa3Container" style="display: none;">
                                                {!! Form::label('afiliadoa3', 'Afiliado a:') !!}
                                                {!! Form::text('afiliadoa3', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                            </div>
                                            <div class="form-group col-lg-4" id="textocomplementario3Container" style="display: none;">
                                                {!! Form::label('textocomplementario3', 'Texto Complementario:') !!}
                                                {!! Form::text('textocomplementario3', null, ['class' => 'form-control', 'placeholder' => '', 'maxlength' => 150]) !!}
                                            </div>
                                        </div>

                                        {{-- PRIMERA CARTA SIT --}}
                                        <div class="row">
                                            <div class="col-lg-12" id="1sitContainer" style="display: none;">
                                                <div class="card shadow-sm">
                                                    <div class="card-header p-1 text-center" style="background-color: #e9ecef; line-height: 1.1;">
                                                        <h6 class="mb-0 fw-bold" style="font-weight: 800;">1ER. CARTA SIT</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecha1sit', 'Fecha Carta:') !!}
                                                                {!! Form::date('fecha1sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('cite1sit', 'Nota Cite:') !!}
                                                                {!! Form::text('cite1sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fechacite1sit', 'Fecha N. Cite:') !!}
                                                                {!! Form::date('fechacite1sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecharesp1sit', 'Fecha Resp.:') !!}
                                                                {!! Form::date('fecharesp1sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                {!! Form::label('texto1sit', 'Texto Comp.:') !!}
                                                                {!! Form::text('texto1sit', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SEGUNDA CARTA SIT --}}
                                        <div class="row">
                                            <div class="col-lg-12" id="2sitContainer" style="display: none;">
                                                <div class="card shadow-sm">
                                                    <div class="card-header p-1 text-center" style="background-color: #e9ecef; line-height: 1.1;">
                                                        <h6 class="mb-0 fw-bold" style="font-weight: 800;">2DA. CARTA SIT</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecha2sit', 'Fecha Carta:') !!}
                                                                {!! Form::date('fecha2sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('cite2sit', 'Nota Cite:') !!}
                                                                {!! Form::text('cite2sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fechacite2sit', 'Fecha N. Cite:') !!}
                                                                {!! Form::date('fechacite2sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecharesp2sit', 'Fecha Resp.:') !!}
                                                                {!! Form::date('fecharesp2sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                {!! Form::label('texto2sit', 'Texto Comp.:') !!}
                                                                {!! Form::text('texto2sit', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- TERCERA CARTA SIT --}}
                                        <div class="row">
                                            <div class="col-lg-12" id="3sitContainer" style="display: none;">
                                                <div class="card shadow-sm">
                                                    <div class="card-header p-1 text-center" style="background-color: #e9ecef; line-height: 1.1;">
                                                        <h6 class="mb-0 fw-bold" style="font-weight: 800;">3RA. CARTA SIT</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecha3sit', 'Fecha Carta:') !!}
                                                                {!! Form::date('fecha3sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('cite3sit', 'Nota Cite:') !!}
                                                                {!! Form::text('cite3sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fechacite3sit', 'Fecha N. Cite:') !!}
                                                                {!! Form::date('fechacite3sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecharesp3sit', 'Fecha Resp.:') !!}
                                                                {!! Form::date('fecharesp3sit', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                {!! Form::label('texto3sit', 'Texto Comp.:') !!}
                                                                {!! Form::text('texto3sit', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PRIMERA CARTA DE RECLAMO GP --}}
                                        <div class="row">
                                            <div class="col-lg-12" id="1reclamogpContainer" style="display: none;">
                                                <div class="card shadow-sm">
                                                    <div class="card-header p-1 text-center" style="background-color: #e9ecef; line-height: 1.1;">
                                                        <h6 class="mb-0 fw-bold" style="font-weight: 800;">1RA. CARTA DE RECLAMO GP</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecha1reclamogp', 'Fecha Carta:') !!}
                                                                {!! Form::date('fecha1reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('cite1reclamogp', 'Nota Cite:') !!}
                                                                {!! Form::text('cite1reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fechacite1reclamogp', 'Fecha N. Cite:') !!}
                                                                {!! Form::date('fechacite1reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecharesp1reclamogp', 'Fecha Resp.:') !!}
                                                                {!! Form::date('fecharesp1reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                {!! Form::label('texto1reclamogp', 'Texto Comp.:') !!}
                                                                {!! Form::text('texto1reclamogp', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PRIMERA CARTA DE RECLAMO APS --}}
                                        <div class="row">
                                            <div class="col-lg-12" id="1reclamoapsContainer" style="display: none;">
                                                <div class="card shadow-sm">
                                                    <div class="card-header p-1 text-center" style="background-color: #e9ecef; line-height: 1.1;">
                                                        <h6 class="mb-0 fw-bold" style="font-weight: 800;">1RA. CARTA DE RECLAMO APS</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecha1reclamoaps', 'Fecha Carta:') !!}
                                                                {!! Form::date('fecha1reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('cite1reclamoaps', 'Nota Cite:') !!}
                                                                {!! Form::text('cite1reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fechacite1reclamoaps', 'Fecha N. Cite:') !!}
                                                                {!! Form::date('fechacite1reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecharesp1reclamoaps', 'Fecha Resp.:') !!}
                                                                {!! Form::date('fecharesp1reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                {!! Form::label('texto1reclamoaps', 'Texto Comp.:') !!}
                                                                {!! Form::text('texto1reclamoaps', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SEGUNDA CARTA DE RECLAMO GP --}}
                                        <div class="row">
                                            <div class="col-lg-12" id="2reclamogpContainer" style="display: none;">
                                                <div class="card shadow-sm">
                                                    <div class="card-header p-1 text-center" style="background-color: #e9ecef; line-height: 1.1;">
                                                        <h6 class="mb-0 fw-bold" style="font-weight: 800;">2DA. CARTA DE RECLAMO GP</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecha2reclamogp', 'Fecha Carta:') !!}
                                                                {!! Form::date('fecha2reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('cite2reclamogp', 'Nota Cite:') !!}
                                                                {!! Form::text('cite2reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fechacite2reclamogp', 'Fecha N. Cite:') !!}
                                                                {!! Form::date('fechacite2reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecharesp2reclamogp', 'Fecha Resp.:') !!}
                                                                {!! Form::date('fecharesp2reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                {!! Form::label('texto2reclamogp', 'Texto Comp.:') !!}
                                                                {!! Form::text('texto2reclamogp', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SEGUNDA CARTA DE RECLAMO APS --}}
                                        <div class="row">
                                            <div class="col-lg-12" id="2reclamoapsContainer" style="display: none;">
                                                <div class="card shadow-sm">
                                                    <div class="card-header p-1 text-center" style="background-color: #e9ecef; line-height: 1.1;">
                                                        <h6 class="mb-0 fw-bold" style="font-weight: 800;">2DA. CARTA DE RECLAMO APS</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecha2reclamoaps', 'Fecha Carta:') !!}
                                                                {!! Form::date('fecha2reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('cite2reclamoaps', 'Nota Cite:') !!}
                                                                {!! Form::text('cite2reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fechacite2reclamoaps', 'Fecha N. Cite:') !!}
                                                                {!! Form::date('fechacite2reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecharesp2reclamoaps', 'Fecha Resp.:') !!}
                                                                {!! Form::date('fecharesp2reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                {!! Form::label('texto2reclamoaps', 'Texto Comp.:') !!}
                                                                {!! Form::text('texto2reclamoaps', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- TERCERA CARTA DE RECLAMO GP --}}
                                        <div class="row">
                                            <div class="col-lg-12" id="3reclamogpContainer" style="display: none;">
                                                <div class="card shadow-sm">
                                                    <div class="card-header p-1 text-center" style="background-color: #e9ecef; line-height: 1.1;">
                                                        <h6 class="mb-0 fw-bold" style="font-weight: 800;">3RA. CARTA DE RECLAMO GP</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecha3reclamogp', 'Fecha Carta:') !!}
                                                                {!! Form::date('fecha3reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('cite3reclamogp', 'Nota Cite:') !!}
                                                                {!! Form::text('cite3reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fechacite3reclamogp', 'Fecha N. Cite:') !!}
                                                                {!! Form::date('fechacite3reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecharesp3reclamogp', 'Fecha Resp.:') !!}
                                                                {!! Form::date('fecharesp3reclamogp', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                {!! Form::label('texto3reclamogp', 'Texto Comp.:') !!}
                                                                {!! Form::text('texto3reclamogp', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- TERCERA CARTA DE RECLAMO APS --}}
                                        <div class="row">
                                            <div class="col-lg-12" id="3reclamoapsContainer" style="display: none;">
                                                <div class="card shadow-sm">
                                                    <div class="card-header p-1 text-center" style="background-color: #e9ecef; line-height: 1.1;">
                                                        <h6 class="mb-0 fw-bold" style="font-weight: 800;">3RA. CARTA DE RECLAMO APS</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        <div class="row">
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecha3reclamoaps', 'Fecha Carta:') !!}
                                                                {!! Form::date('fecha3reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('cite3reclamoaps', 'Nota Cite:') !!}
                                                                {!! Form::text('cite3reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fechacite3reclamoaps', 'Fecha N. Cite:') !!}
                                                                {!! Form::date('fechacite3reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-2">
                                                                {!! Form::label('fecharesp3reclamoaps', 'Fecha Resp.:') !!}
                                                                {!! Form::date('fecharesp3reclamoaps', null, ['class' => 'form-control']) !!}
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                {!! Form::label('texto3reclamoaps', 'Texto Comp.:') !!}
                                                                {!! Form::text('texto3reclamoaps', null, ['class' => 'form-control', 'maxlength' => 150]) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                            $(document).ready(function(){

                                                function buscarProcedimiento() {
                                                    let nivel = $('#nivelprocedimiento3').val();
                                                    let subnivel = $('#subnivelprocedimiento3').val();
                                                    let tipo = $('#tipo_pdf3').val();

                                                    if (nivel && subnivel && tipo) {
                                                        $.ajax({
                                                            url: "{{ route('procedimiento.buscar') }}",
                                                            type: "GET",
                                                            data: {
                                                                nivelprocedimiento: nivel,
                                                                subnivelprocedimiento: subnivel,
                                                                tipo_pdf: tipo,
                                                                tramite3: $('#tramite').val(),
                                                                clienteid3: {{ $cliente->id }}
                                                            },
                                                            success: function(response) {
                                                                $('#fecha1sit, #cite1sit, #fechacite1sit, #fecharesp1sit' +
                                                                '#fecha2sit, #cite2sit, #fechacite2sit, #fecharesp2sit' +
                                                                '#fecha3sit, #cite3sit, #fechacite3sit, #fecharesp3sit' + 
                                                                '#fecha1reclamogp, #cite1reclamogp, #fechacite1reclamogp, #fecharesp1reclamogp' + 
                                                                '#fecha2reclamogp, #cite2reclamogp, #fechacite2reclamogp, #fecharesp2reclamogp' + 
                                                                '#fecha3reclamogp, #cite3reclamogp, #fechacite3reclamogp, #fecharesp3reclamogp' + 
                                                                '#fecha1reclamoaps, #cite1reclamoaps, #fechacite1reclamoaps, #fecharesp1reclamoaps' + 
                                                                '#fecha2reclamoaps, #cite2reclamoaps, #fechacite2reclamoaps, #fecharesp2reclamoaps' + 
                                                                '#fecha3reclamoaps, #cite3reclamoaps, #fechacite3reclamoaps, #fecharesp3reclamoaps')
                                                                .val('');

                                                                if(response.success) {
                                                                    if(tipo === 'SEGUNDA CARTA SIT'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                    }
                                                                    if(tipo === 'TERCERA CARTA SIT'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                        /* $('#fecha2sitContainer, #cite2sitContainer, #fecharesp2sitContainer').show(); */
                                                                        $('#fecha2sit').val(response.fechasubida_segunda);
                                                                        $('#cite2sit').val(response.citenota_segunda);
                                                                        $('#fechacite2sit').val(response.fechacitenota_segunda);
                                                                        $('#fecharesp2sit').val(response.fecharespuesta_segunda);
                                                                    }
                                                                    if(tipo === 'PRIMERA CARTA DE RECLAMO GP'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                        $('#fecha2sit').val(response.fechasubida_segunda);
                                                                        $('#cite2sit').val(response.citenota_segunda);
                                                                        $('#fechacite2sit').val(response.fechacitenota_segunda);
                                                                        $('#fecharesp2sit').val(response.fecharespuesta_segunda);

                                                                        $('#fecha3sit').val(response.fechasubida_tercera);
                                                                        $('#cite3sit').val(response.citenota_tercera);
                                                                        $('#fechacite3sit').val(response.fechacitenota_tercera);
                                                                        $('#fecharesp3sit').val(response.fecharespuesta_tercera);
                                                                    }
                                                                    if(tipo === 'PRIMERA CARTA DE RECLAMO APS'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                        $('#fecha2sit').val(response.fechasubida_segunda);
                                                                        $('#cite2sit').val(response.citenota_segunda);
                                                                        $('#fechacite2sit').val(response.fechacitenota_segunda);
                                                                        $('#fecharesp2sit').val(response.fecharespuesta_segunda);

                                                                        $('#fecha3sit').val(response.fechasubida_tercera);
                                                                        $('#cite3sit').val(response.citenota_tercera);
                                                                        $('#fechacite3sit').val(response.fechacitenota_tercera);
                                                                        $('#fecharesp3sit').val(response.fecharespuesta_tercera);
                                                                    }
                                                                    if(tipo === 'SEGUNDA CARTA DE RECLAMO GP'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                        $('#fecha2sit').val(response.fechasubida_segunda);
                                                                        $('#cite2sit').val(response.citenota_segunda);
                                                                        $('#fechacite2sit').val(response.fechacitenota_segunda);
                                                                        $('#fecharesp2sit').val(response.fecharespuesta_segunda);

                                                                        $('#fecha3sit').val(response.fechasubida_tercera);
                                                                        $('#cite3sit').val(response.citenota_tercera);
                                                                        $('#fechacite3sit').val(response.fechacitenota_tercera);
                                                                        $('#fecharesp3sit').val(response.fecharespuesta_tercera);

                                                                        $('#fecha1reclamogp').val(response.fechasubida_primerareclamogp);
                                                                        $('#cite1reclamogp').val(response.citenota_primerareclamogp);
                                                                        $('#fechacite1reclamogp').val(response.fechacitenota_primerareclamogp);
                                                                        $('#fecharesp1reclamogp').val(response.fecharespuesta_primerareclamogp);
                                                                    }
                                                                    if(tipo === 'SEGUNDA CARTA DE RECLAMO APS'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                        $('#fecha2sit').val(response.fechasubida_segunda);
                                                                        $('#cite2sit').val(response.citenota_segunda);
                                                                        $('#fechacite2sit').val(response.fechacitenota_segunda);
                                                                        $('#fecharesp2sit').val(response.fecharespuesta_segunda);

                                                                        $('#fecha3sit').val(response.fechasubida_tercera);
                                                                        $('#cite3sit').val(response.citenota_tercera);
                                                                        $('#fechacite3sit').val(response.fechacitenota_tercera);
                                                                        $('#fecharesp3sit').val(response.fecharespuesta_tercera);

                                                                        $('#fecha1reclamogp').val(response.fechasubida_primerareclamogp);
                                                                        $('#cite1reclamogp').val(response.citenota_primerareclamogp);
                                                                        $('#fechacite1reclamogp').val(response.fechacitenota_primerareclamogp);
                                                                        $('#fecharesp1reclamogp').val(response.fecharespuesta_primerareclamogp);

                                                                        $('#fecha1reclamoaps').val(response.fechasubida_primerareclamoaps);
                                                                        $('#cite1reclamoaps').val(response.citenota_primerareclamoaps);
                                                                        $('#fechacite1reclamoaps').val(response.fechacitenota_primerareclamoaps);
                                                                        $('#fecharesp1reclamoaps').val(response.fecharespuesta_primerareclamoaps);
                                                                    }
                                                                    if(tipo === 'TERCERA CARTA DE RECLAMO GP'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                        $('#fecha2sit').val(response.fechasubida_segunda);
                                                                        $('#cite2sit').val(response.citenota_segunda);
                                                                        $('#fechacite2sit').val(response.fechacitenota_segunda);
                                                                        $('#fecharesp2sit').val(response.fecharespuesta_segunda);

                                                                        $('#fecha3sit').val(response.fechasubida_tercera);
                                                                        $('#cite3sit').val(response.citenota_tercera);
                                                                        $('#fechacite3sit').val(response.fechacitenota_tercera);
                                                                        $('#fecharesp3sit').val(response.fecharespuesta_tercera);

                                                                        $('#fecha1reclamogp').val(response.fechasubida_primerareclamogp);
                                                                        $('#cite1reclamogp').val(response.citenota_primerareclamogp);
                                                                        $('#fechacite1reclamogp').val(response.fechacitenota_primerareclamogp);
                                                                        $('#fecharesp1reclamogp').val(response.fecharespuesta_primerareclamogp);

                                                                        $('#fecha2reclamogp').val(response.fechasubida_segundareclamogp);
                                                                        $('#cite2reclamogp').val(response.citenota_segundareclamogp);
                                                                        $('#fechacite2reclamogp').val(response.fechacitenota_segundareclamogp);
                                                                        $('#fecharesp2reclamogp').val(response.fecharespuesta_segundareclamogp);
                                                                    }
                                                                    if(tipo === 'TERCERA CARTA DE RECLAMO APS'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                        $('#fecha2sit').val(response.fechasubida_segunda);
                                                                        $('#cite2sit').val(response.citenota_segunda);
                                                                        $('#fechacite2sit').val(response.fechacitenota_segunda);
                                                                        $('#fecharesp2sit').val(response.fecharespuesta_segunda);

                                                                        $('#fecha3sit').val(response.fechasubida_tercera);
                                                                        $('#cite3sit').val(response.citenota_tercera);
                                                                        $('#fechacite3sit').val(response.fechacitenota_tercera);
                                                                        $('#fecharesp3sit').val(response.fecharespuesta_tercera);

                                                                        $('#fecha1reclamogp').val(response.fechasubida_primerareclamogp);
                                                                        $('#cite1reclamogp').val(response.citenota_primerareclamogp);
                                                                        $('#fechacite1reclamogp').val(response.fechacitenota_primerareclamogp);
                                                                        $('#fecharesp1reclamogp').val(response.fecharespuesta_primerareclamogp);

                                                                        $('#fecha1reclamoaps').val(response.fechasubida_primerareclamoaps);
                                                                        $('#cite1reclamoaps').val(response.citenota_primerareclamoaps);
                                                                        $('#fechacite1reclamoaps').val(response.fechacitenota_primerareclamoaps);
                                                                        $('#fecharesp1reclamoaps').val(response.fecharespuesta_primerareclamoaps);

                                                                        $('#fecha2reclamogp').val(response.fechasubida_segundareclamogp);
                                                                        $('#cite2reclamogp').val(response.citenota_segundareclamogp);
                                                                        $('#fechacite2reclamogp').val(response.fechacitenota_segundareclamogp);
                                                                        $('#fecharesp2reclamogp').val(response.fecharespuesta_segundareclamogp);

                                                                        $('#fecha2reclamoaps').val(response.fechasubida_segundareclamoaps);
                                                                        $('#cite2reclamoaps').val(response.citenota_segundareclamoaps);
                                                                        $('#fechacite2reclamoaps').val(response.fechacitenota_segundareclamoaps);
                                                                        $('#fecharesp2reclamoaps').val(response.fecharespuesta_segundareclamoaps);
                                                                    }
                                                                    if(tipo === 'REITERACIÓN A CARTAS DE RECLAMO GP'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                        $('#fecha2sit').val(response.fechasubida_segunda);
                                                                        $('#cite2sit').val(response.citenota_segunda);
                                                                        $('#fechacite2sit').val(response.fechacitenota_segunda);
                                                                        $('#fecharesp2sit').val(response.fecharespuesta_segunda);

                                                                        $('#fecha3sit').val(response.fechasubida_tercera);
                                                                        $('#cite3sit').val(response.citenota_tercera);
                                                                        $('#fechacite3sit').val(response.fechacitenota_tercera);
                                                                        $('#fecharesp3sit').val(response.fecharespuesta_tercera);

                                                                        $('#fecha1reclamogp').val(response.fechasubida_primerareclamogp);
                                                                        $('#cite1reclamogp').val(response.citenota_primerareclamogp);
                                                                        $('#fechacite1reclamogp').val(response.fechacitenota_primerareclamogp);
                                                                        $('#fecharesp1reclamogp').val(response.fecharespuesta_primerareclamogp);

                                                                        $('#fecha2reclamogp').val(response.fechasubida_segundareclamogp);
                                                                        $('#cite2reclamogp').val(response.citenota_segundareclamogp);
                                                                        $('#fechacite2reclamogp').val(response.fechacitenota_segundareclamogp);
                                                                        $('#fecharesp2reclamogp').val(response.fecharespuesta_segundareclamogp);

                                                                        $('#fecha3reclamogp').val(response.fechasubida_tercerareclamogp);
                                                                        $('#cite3reclamogp').val(response.citenota_tercerareclamogp);
                                                                        $('#fechacite3reclamogp').val(response.fechacitenota_tercerareclamogp);
                                                                        $('#fecharesp3reclamogp').val(response.fecharespuesta_tercerareclamogp);
                                                                    }
                                                                    if(tipo === 'REITERACIÓN A CARTAS DE RECLAMO APS'){
                                                                        $('#fecha1sit').val(response.fechasubida_primera);
                                                                        $('#cite1sit').val(response.citenota_primera);
                                                                        $('#fechacite1sit').val(response.fechacitenota_primera);
                                                                        $('#fecharesp1sit').val(response.fecharespuesta_primera);

                                                                        $('#fecha2sit').val(response.fechasubida_segunda);
                                                                        $('#cite2sit').val(response.citenota_segunda);
                                                                        $('#fechacite2sit').val(response.fechacitenota_segunda);
                                                                        $('#fecharesp2sit').val(response.fecharespuesta_segunda);

                                                                        $('#fecha3sit').val(response.fechasubida_tercera);
                                                                        $('#cite3sit').val(response.citenota_tercera);
                                                                        $('#fechacite3sit').val(response.fechacitenota_tercera);
                                                                        $('#fecharesp3sit').val(response.fecharespuesta_tercera);

                                                                        $('#fecha1reclamogp').val(response.fechasubida_primerareclamogp);
                                                                        $('#cite1reclamogp').val(response.citenota_primerareclamogp);
                                                                        $('#fechacite1reclamogp').val(response.fechacitenota_primerareclamogp);
                                                                        $('#fecharesp1reclamogp').val(response.fecharespuesta_primerareclamogp);

                                                                        $('#fecha1reclamoaps').val(response.fechasubida_primerareclamoaps);
                                                                        $('#cite1reclamoaps').val(response.citenota_primerareclamoaps);
                                                                        $('#fechacite1reclamoaps').val(response.fechacitenota_primerareclamoaps);
                                                                        $('#fecharesp1reclamoaps').val(response.fecharespuesta_primerareclamoaps);

                                                                        $('#fecha2reclamogp').val(response.fechasubida_segundareclamogp);
                                                                        $('#cite2reclamogp').val(response.citenota_segundareclamogp);
                                                                        $('#fechacite2reclamogp').val(response.fechacitenota_segundareclamogp);
                                                                        $('#fecharesp2reclamogp').val(response.fecharespuesta_segundareclamogp);

                                                                        $('#fecha2reclamoaps').val(response.fechasubida_segundareclamoaps);
                                                                        $('#cite2reclamoaps').val(response.citenota_segundareclamoaps);
                                                                        $('#fechacite2reclamoaps').val(response.fechacitenota_segundareclamoaps);
                                                                        $('#fecharesp2reclamoaps').val(response.fecharespuesta_segundareclamoaps);

                                                                        $('#fecha3reclamoaps').val(response.fechasubida_tercerareclamoaps);
                                                                        $('#cite3reclamoaps').val(response.citenota_tercerareclamoaps);
                                                                        $('#fechacite3reclamoaps').val(response.fechacitenota_tercerareclamoaps);
                                                                        $('#fecharesp3reclamoaps').val(response.fecharespuesta_tercerareclamoaps);

                                                                        $('#fecha3reclamogp').val(response.fechasubida_tercerareclamogp);
                                                                        $('#cite3reclamogp').val(response.citenota_tercerareclamogp);
                                                                        $('#fechacite3reclamogp').val(response.fechacitenota_tercerareclamogp);
                                                                        $('#fecharesp3reclamogp').val(response.fecharespuesta_tercerareclamogp);
                                                                    }
                                                                }
                                                            }
                                                        });
                                                    }
                                                }

                                                $('#nivelprocedimiento3, #subnivelprocedimiento3, #tipo_pdf3').on('change', buscarProcedimiento);

                                                function toggleAdjuntoFields() {
                                                    $('#tipoadjuntoContainer').hide();
                                                    $('#fechaadjuntoContainer').hide();
                                                    $('#solmodificar3Container').hide();
                                                    $('#nronota3Container').hide();
                                                    $('#fechanota3Container').hide();
                                                    $('#dirigidoa3Container').hide();
                                                    $('#estadolab3Container').hide();
                                                    $('#afiliadoa3Container').hide();
                                                    $('#textocomplementario3Container').hide();
                                                    $('#fechaconclusionprog3Container').hide();
                                                    $('#1sitContainer').hide();
                                                    $('#2sitContainer').hide();
                                                    $('#3sitContainer').hide();
                                                    $('#1reclamogpContainer').hide();
                                                    $('#1reclamoapsContainer').hide();
                                                    $('#2reclamogpContainer').hide();
                                                    $('#2reclamoapsContainer').hide();
                                                    $('#3reclamogpContainer').hide();
                                                    $('#3reclamoapsContainer').hide();
                                                    $('#nombremedico3Container').hide();
                                                    $('#cargomedico3Container').hide();

                                                    var subnivel = $('#subnivelprocedimiento3').val();
                                                    var tipoPdf = $('#tipo_pdf3').val();

                                                    if (
                                                        tipoPdf === 'PRIMERA CARTA SIT' &&
                                                        (subnivel === 'ADJUNTO DE DOCUMENTOS' || subnivel === 'ADJUNTO DE DOCUMENTACIÓN MÉDICA')
                                                    ) {
                                                        $('#tipoadjuntoContainer').show();
                                                        $('#fechaadjuntoContainer').show();
                                                    } else if (
                                                        tipoPdf === 'PRIMERA CARTA SIT' &&
                                                        subnivel === 'SOLICITUD DE MODIFICACIÓN DE CITE'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#dirigidoa3Container').show();
                                                        $('#estadolab3Container').show();
                                                        $('#afiliadoa3Container').show();
                                                        $('#textocomplementario3Container').show();
                                                    } else if (
                                                        tipoPdf === 'PRIMERA CARTA SIT' &&
                                                        subnivel === 'ADJUNTO Y RESPUESTA AL COMPLEMENTARIO'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                    } else if (
                                                        tipoPdf === 'PRIMERA CARTA SIT' &&
                                                        subnivel === 'SOLICITUD DE COMPRA DE SERVICIOS'
                                                    ) {
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#textocomplementario3Container').show();
                                                    } else if (
                                                        tipoPdf === 'SEGUNDA CARTA SIT'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                    } else if (
                                                        tipoPdf === 'TERCERA CARTA SIT'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                        $('#2sitContainer').show();
                                                    } else if (
                                                        tipoPdf === 'PRIMERA CARTA DE RECLAMO GP'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                        $('#2sitContainer').show();
                                                        $('#3sitContainer').show();
                                                    } else if (
                                                        tipoPdf === 'PRIMERA CARTA DE RECLAMO APS'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                        $('#2sitContainer').show();
                                                        $('#3sitContainer').show();
                                                        $('#nombremedico3Container').show();
                                                        $('#cargomedico3Container').show();
                                                    } else if (
                                                        tipoPdf === 'SEGUNDA CARTA DE RECLAMO GP'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                        $('#2sitContainer').show();
                                                        $('#3sitContainer').show();
                                                        $('#1reclamogpContainer').show();
                                                    } else if (
                                                        tipoPdf === 'SEGUNDA CARTA DE RECLAMO APS'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                        $('#2sitContainer').show();
                                                        $('#3sitContainer').show();
                                                        $('#1reclamogpContainer').show();
                                                        $('#1reclamoapsContainer').show();
                                                        $('#nombremedico3Container').show();
                                                        $('#cargomedico3Container').show();
                                                    } else if (
                                                        tipoPdf === 'TERCERA CARTA DE RECLAMO GP'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                        $('#2sitContainer').show();
                                                        $('#3sitContainer').show();
                                                        $('#1reclamogpContainer').show();
                                                        $('#2reclamogpContainer').show();
                                                    } else if (
                                                        tipoPdf === 'TERCERA CARTA DE RECLAMO APS'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                        $('#2sitContainer').show();
                                                        $('#3sitContainer').show();
                                                        $('#1reclamogpContainer').show();
                                                        $('#1reclamoapsContainer').show();
                                                        $('#2reclamogpContainer').show();
                                                        $('#2reclamoapsContainer').show();
                                                        $('#nombremedico3Container').show();
                                                        $('#cargomedico3Container').show();
                                                    } else if (
                                                        tipoPdf === 'REITERACIÓN A CARTAS DE RECLAMO GP'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                        $('#2sitContainer').show();
                                                        $('#3sitContainer').show();
                                                        $('#1reclamogpContainer').show();
                                                        $('#2reclamogpContainer').show();
                                                        $('#3reclamogpContainer').show();
                                                    } else if (
                                                        tipoPdf === 'REITERACIÓN A CARTAS DE RECLAMO APS'
                                                    ) {
                                                        $('#solmodificar3Container').show();
                                                        $('#nronota3Container').show();
                                                        $('#fechanota3Container').show();
                                                        $('#fechaconclusionprog3Container').show();
                                                        $('#1sitContainer').show();
                                                        $('#2sitContainer').show();
                                                        $('#3sitContainer').show();
                                                        $('#1reclamogpContainer').show();
                                                        $('#1reclamoapsContainer').show();
                                                        $('#2reclamogpContainer').show();
                                                        $('#2reclamoapsContainer').show();
                                                        $('#3reclamogpContainer').show();
                                                        $('#3reclamoapsContainer').show();
                                                        $('#nombremedico3Container').show();
                                                        $('#cargomedico3Container').show();
                                                    }
                                                }

                                                $('#subnivelprocedimiento3, #tipo_pdf3').on('change', function() {
                                                    toggleAdjuntoFields();
                                                });
                                                toggleAdjuntoFields();

                                            });
                                        </script>

                                        <button type="submit" id="btnGenerarCarta" class="btn btn-sm btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content; margin-top: 20px;">GENERAR CARTA / RECLAMO</button>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const btn = document.getElementById('btnGenerarCarta');
                                                const form = document.getElementById('formCarta');
                                                btn.addEventListener('click', function(e) {
                                                    e.preventDefault();
                                                    const confirmar = confirm("¿Está seguro de que desea generar la carta?");
                                                    if (confirmar) {
                                                        form.submit();
                                                    }
                                                });
                                            });
                                        </script>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0" style="font-weight: 700;">VISTA PREVIA DE CARTA/RECLAMO</h5>
                                        <a id="btnActualizarVistaCarta" class="btn btn-sm btn-verdocumento">
                                            <i class="fas fa-sync-alt"></i> ACTUALIZAR
                                        </a>
                                    </div>
                                    <iframe id="pdfPreview3" style="width:100%; height:500px; border:1px solid #ddd;"></iframe>
                                </div>
                            </div>
                        </div>

                        <script>
                            /* $(document).ready(function() {
                                function updatePDFPreview3() {
                                    let formData3 = $('#formCarta').serialize();
                                    let clienteId3 = "{{ $cliente->id }}";
                                    let nivelProcedimiento3 = $('[name="nivelprocedimiento3"]').val();
                                    let subnivelProcedimiento3 = $('[name="subnivelprocedimiento3"]').val();
                                    let tipoPdf3 = $('[name="tipo_pdf3"]').val();
                                    if (!nivelProcedimiento3 || !subnivelProcedimiento3 || !tipoPdf3) {
                                        $('#pdfPreview3').attr('src', '');
                                        return;
                                    }
                                    $('#pdfPreview3').attr('src', '{{ url("/preview-carta") }}/' + clienteId3 + '?' + formData3);
                                }
                                $('[name="nivelprocedimiento3"], [name="subnivelprocedimiento3"], [name="tipo_pdf3"]').on('change', function() {
                                    updatePDFPreview3();
                                });
                                $('#btnActualizarVistaCarta').on('click', function(e) {
                                    e.preventDefault();
                                    updatePDFPreview3();
                                });
                            }); */
                            $(document).ready(function() {
                                function updatePDFPreview3() {
                                    let formData3 = $('#formCarta').serialize();
                                    let fontSize = $('#fontsize').val() || '15px';
                                    formData3 += '&fontsize3=' + encodeURIComponent(fontSize);
                                    let marginSize = $('#marginSize').val() || '1.5cm 3cm 1.5cm 3cm';
                                    formData3 += '&marginsize3=' + encodeURIComponent(marginSize);
                                    let clienteId3 = "{{ $cliente->id }}";
                                    let nivelProcedimiento3 = $('[name="nivelprocedimiento3"]').val();
                                    let subnivelProcedimiento3 = $('[name="subnivelprocedimiento3"]').val();
                                    let tipoPdf3 = $('[name="tipo_pdf3"]').val();
                                    if (!nivelProcedimiento3 || !subnivelProcedimiento3 || !tipoPdf3) {
                                        $('#pdfPreview3').attr('src', '');
                                        return;
                                    }
                                    $('#pdfPreview3').attr('src', '{{ url("/preview-carta") }}/' + clienteId3 + '?' + formData3);
                                }
                                $('[name="nivelprocedimiento3"], [name="subnivelprocedimiento3"], [name="tipo_pdf3"], #fontsize, #marginSize').on('change', function() {
                                    updatePDFPreview3();
                                });
                                $('#btnActualizarVistaCarta').on('click', function(e) {
                                    e.preventDefault();
                                    updatePDFPreview3();
                                });
                                updatePDFPreview3();
                            });
                        </script>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-content-4" role="tabpanel" aria-labelledby="tab-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card">
                                <div class="card-body">
                                    <h5 style="margin-bottom: 15px; font-weight: 700;">DATOS DE LA MISIVA LIBRE</h5>
                                    <form action="{{ route('admin.tramites.generarlibre', $cliente) }}" method="GET" enctype="multipart/form-data" id="formLibre">
                                        {!! Form::hidden('usuarioid4', auth()->user()->id) !!}
                                        {!! Form::hidden('usuarioregistro4', auth()->user()->name) !!}
                                        {!! Form::hidden('clienteid4', $cliente->id) !!}
                                        {!! Form::hidden('clientenombre4', $cliente->nombrecompleto) !!}
                                        {!! Form::hidden('apoderado4', $apoderadoAsignado) !!}
                                        {!! Form::hidden('idtramite4', $idTramite) !!}
                                        <input type="text" class="form-control" id="tramite4" name="tramite4" value="PENSIÓN POR MUERTE" hidden>
                                        <input type="date" class="form-control" id="fechasubida4" name="fechasubida4" value="{{ \Carbon\Carbon::now()->toDateString() }}" hidden>
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="form-group col-lg-4">
                                                        {!! Form::label('nivelprocedimiento4', 'Nivel Procedimiento:') !!}
                                                        <input list="niveles" name="nivelprocedimiento4" class="form-control" required placeholder="Selecciona o escribe">
                                                        <datalist id="niveles">
                                                            <option value="INGRESO DE TRÁMITE">
                                                            <option value="VALIDACIÓN DE TRÁMITE">
                                                            <option value="FIRMA EAP">
                                                            <option value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO - ENTE GESTOR DE SALUD">
                                                            <option value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO - NOTIFICACIÓN TMC">
                                                            <option value="SOLICITUD DE INFORMACIÓN TÉCNICO MÉDICO - EMPLEADOR">
                                                            <option value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - ENTE GESTOR DE SALUD">
                                                            <option value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - NOTIFICACIÓN TMC">
                                                            <option value="SOLICITUD DE INFORMACIÓN COMPLEMENTARIA - EMPLEADOR">
                                                            <option value="COMPRA DE SERVICIOS">
                                                            <option value="COMPLEMENTACIÓN DEL TRÁMITE">
                                                            <option value="RESPUESTAS ERRÓNEAS">
                                                        </datalist>
                                                    </div>

                                                    <div class="form-group col-lg-4">
                                                        {!! Form::label('subnivelprocedimiento4', 'Sub Nivel Procedimiento:') !!}
                                                        <input list="subniveles" name="subnivelprocedimiento4" class="form-control" required placeholder="Selecciona o escribe">
                                                        <datalist id="subniveles">
                                                            <option value="INGRESO DE TRÁMITE">
                                                            <option value="VALIDACIÓN DE TRÁMITE">
                                                            <option value="FIRMA EAP">
                                                            <option value="ADJUNTO Y RESPUESTA AL TÉCNICO MÉDICO">
                                                            <option value="ADJUNTO Y RESPUESTA A NOTIFICACIÓN TMC">
                                                            <option value="ADJUNTO Y RESPUESTA DE INFORME DEL EMPLEADOR">
                                                            <option value="ADJUNTO Y RESPUESTA AL COMPLEMENTARIO">
                                                            <option value="COMPRA DE SERVICIOS">
                                                            <option value="ADJUNTO DE DOCUMENTOS">
                                                            <option value="ADJUNTO DE DOCUMENTACIÓN MÉDICA">
                                                            <option value="SOLICITUD DE COMPRA DE SERVICIOS">
                                                            <option value="SOLICITUD DE MODIFICACIÓN DE CITE">
                                                            <option value="CANCELACIÓN DE TRÁMITE">
                                                            <option value="RESPUESTA A CANCELACIÓN DE TRÁMITE">
                                                            <option value="REIMPRESIÓN DE NOTIFICACIÓN CITE">
                                                        </datalist>
                                                    </div>
                                                    <div class="form-group col-lg-4">
                                                        {!! Form::label('tipo_pdf4', 'Tipo Misiva:') !!}
                                                        <input type="text" class="form-control" id="tipo_pdf4" name="tipo_pdf4">
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        {!! Form::label('emisor4', 'Emisor:') !!}
                                                        {!! Form::select('emisor4', [
                                                            'CLIENTE' => 'CLIENTE',
                                                            'APODERADO' => 'APODERADO',
                                                        ], null, ['class' => 'form-control', 'placeholder' => '']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4">
                                                        {!! Form::label('apoderado4', 'Emisor Apoderado:') !!}
                                                        {!! Form::select('apoderado4', 
                                                            array_combine($apoderados, $apoderados),
                                                            $apoderadoAsignado,
                                                            ['class' => 'form-control']
                                                        ) !!}
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        {!! Form::label('fechaemitido4', 'Fecha Carta:') !!}
                                                        <input type="date" class="form-control" id="fechaactual4" name="fechaactual4" value="{{ \Carbon\Carbon::now()->toDateString() }}" min="{{ \Carbon\Carbon::now()->toDateString() }}">
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label for="fontsize4">Tamaño Fuente:</label>
                                                        <select id="fontsize4" class="form-control">
                                                            <option value="12px">9</option>
                                                            <option value="13px">10</option>
                                                            <option value="14px">11</option>
                                                            <option value="15px" selected>12</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <label for="marginSize4">Márgenes:</label>
                                                        <select id="marginSize4" class="form-control">
                                                            <option value="1cm 1.5cm 1cm 1.5cm">BAJO</option>
                                                            <option value="1.5cm 3cm 1.5cm 3cm" selected>MEDIO</option>
                                                            <option value="2cm 3.5cm 2cm 3.5cm">ALTO</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-lg-4" id="nombremedico4Container">
                                                        {!! Form::label('nombremedico4', 'Destinatario:') !!}
                                                        {!! Form::text('nombremedico4', null, ['class' => 'form-control']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4" id="cargomedico4Container">
                                                        {!! Form::label('cargomedico4', 'Cargo Destinatario:') !!}
                                                        {!! Form::text('cargomedico4', null, ['class' => 'form-control']) !!}
                                                    </div>
                                                    <div class="form-group col-lg-4">
                                                        <label>¿Ver Encabezado?</label>
                                                        <select id="mostrarencabezado4" name="mostrarencabezado4" class="form-control">
                                                            <option value="SI">SI</option>
                                                            <option value="NO">NO</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-12">
                                                        {!! Form::label('contenidoLibre', 'Contenido libre:') !!}
                                                        <textarea name="contenidoLibre" id="contenidoLibre" class="form-control" rows="12">
                                                        
                                                        </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" id="btnGenerarLibre44" class="btn btn-sm btn-subirarchivos d-block mx-auto mb-3" target="_blank" style="width: fit-content; margin-top: 20px;">GENERAR MISIVA</button>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const btn = document.getElementById('btnGenerarLibre44');
                                                const form = document.getElementById('formLibre');
                                                btn.addEventListener('click', function(e) {
                                                    e.preventDefault();
                                                    const confirmar = confirm("¿Está seguro de que desea generar la misiva?");
                                                    if (confirmar) {
                                                        $('#contenidoLibre').val(editorContenidoLibre.getData());
                                                        form.submit();
                                                    }
                                                });
                                            });
                                        </script>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0" style="font-weight: 700;">VISTA PREVIA DE LA MISIVA LIBRE</h5>
                                        <a id="btnActualizarVistaLibre" class="btn btn-sm btn-verdocumento">
                                            <i class="fas fa-sync-alt"></i> ACTUALIZAR
                                        </a>
                                    </div>
                                    <iframe id="pdfPreview4" style="width:100%; height:500px; border:1px solid #ddd;"></iframe>
                                </div>
                            </div>
                        </div>
                        <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
                        <script>
                            let editorContenidoLibre;

                            ClassicEditor
                                .create(document.querySelector('#contenidoLibre'), {
                                    toolbar: [
                                        'heading', '|', 'bold','italic','|',
                                        'bulletedList','numberedList','|',
                                        'insertTable','|',
                                        'undo','redo'
                                    ],
                                    table: {
                                        contentToolbar: ['tableColumn','tableRow','mergeTableCells']
                                    }
                                })
                            .then(editor => {
                                editorContenidoLibre = editor;
                            })
                            .catch(error => console.error(error));

                        </script>
                        <script>
                            $(document).ready(function() {
                                function updatePDFPreview4() {
                                    let contenidoLibre = encodeURIComponent(editorContenidoLibre.getData());
                                    let formData4 = $('#formLibre').serialize();
                                    formData4 += '&contenidoLibre=' + contenidoLibre;
                                    let fontSize4 = $('#fontsize4').val() || '15px';
                                    formData4 += '&fontsize4=' + encodeURIComponent(fontSize4);
                                    let marginSize4 = $('#marginSize4').val() || '1.5cm 3cm 1.5cm 3cm';
                                    formData4 += '&marginsize4=' + encodeURIComponent(marginSize4);
                                    let clienteId4 = "{{ $cliente->id }}";
                                    let nivelProcedimiento4 = $('[name="nivelprocedimiento4"]').val();
                                    let tipoPdf4 = $('[name="tipo_pdf4"]').val();
                                    if (!nivelProcedimiento4 || !tipoPdf4) {
                                        $('#pdfPreview4').attr('src', '');
                                        return;
                                    }
                                    $('#pdfPreview4').attr('src', '{{ url("/preview-libre") }}/' + clienteId4 + '?' + formData4);
                                }
                                $('[name="nivelprocedimiento4"], [name="tipo_pdf4"], #fontsize4, #marginSize4').on('change', function() {
                                    updatePDFPreview4();
                                });
                                $('#btnActualizarVistaLibre').on('click', function(e) {
                                    e.preventDefault();
                                    updatePDFPreview4();
                                });
                                updatePDFPreview4();
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('.file-input');
        const fileLabels = document.querySelectorAll('.file-label');
    
        fileInputs.forEach((input, index) => {
            input.addEventListener('change', function() {
                const fileName = input.files[0] ? input.files[0].name : 'Subir Documento';
                fileLabels[index].innerHTML = fileName;
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.dropify').dropify({
            messages: {
                'default': '',
                'replace': '',
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

    $(document).ready(function() {
        $('#area').change(function() {
            var areaId = $(this).val();
            $('.acciones').hide();
            $('#acciones_' + areaId).show();
        });
    });

    document.getElementById('archivo').addEventListener('change', function(event) {
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
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>
@endsection

