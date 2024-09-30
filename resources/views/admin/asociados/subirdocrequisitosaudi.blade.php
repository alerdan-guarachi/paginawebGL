@extends('adminlte::page')

@section('content_header')
<a class="btn btn-sm float-right btn-regresar" href="{{ route('admin.asociados.verclienteita', $cliente) }}">REGRESAR</a>
<a class="btn custom2-button btn-sm float-right" data-toggle="modal" data-target="#requisitosModal">VER REQUISITOS</a>
<h5>REQUISITOS DE AUDITORIA MÉDICA:</h5>
<h3>{{$cliente->nombrecompleto}}</h3>
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
        <div class="row">
            <div class="col-lg-12">
                {!! Form::model($cliente, ['route' => ['admin.asociados.guardardocrequisitosaudi', $cliente], 'method' => 'PUT', 'files' => true]) !!}
                {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                {!! Form::hidden('clienteitaid', $cliente->id) !!}
                <div class="row">
                    <div class="col-lg-6" hidden>
                        <div class="form-group">
                            {!! Form::label('nombrecompleto', 'Nombre completo:') !!}
                            {!! Form::text('nombrecompleto', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => 'readonly']) !!}
                            @error('nombrecompleto')
                                <small class="text-danger fas fa-exclamation-circle">
                                    {{$message}}
                                </small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if($cnacaseguradoPendiente)
                        <div class="form-group col-lg-6">
                            {!! Form::label('cnacasegurado', 'CERTIFICADO NACIMIENTO ASEGURADO:') !!}
                            {!! Form::file('cnacasegurado', ['class' => 'form-control-file dropify']) !!}
                        </div>
                    @endif
                    @if($ciaseguradoPendiente)
                        <div class="form-group col-lg-6">
                            {!! Form::label('ciasegurado', 'CARNET IDENTIDAD ASEGURADO:') !!}
                            {!! Form::file('ciasegurado', ['class' => 'form-control-file dropify']) !!}
                        </div>
                    @endif
                </div>

                @foreach($requisitosClientepolizas as $requisito)
                    @if($requisito->polizageneral === 'PENDIENTE' || $requisito->declasalud === 'PENDIENTE' || $requisito->polizasegurodesgravamen === 'PENDIENTE')
                        <h5>{{ $requisito->banco }}</h5>
                        <div class="row">
                            {!! Form::hidden('id', $requisito->id) !!}
                
                            @if($requisito->polizageneral === 'PENDIENTE')
                                <div class="form-group col-lg-4">
                                    {!! Form::label('polizageneral', 'POLIZAS GENERALES:') !!}
                                    @if($requisito->nropolizageneral)
                                        <p>(Nro: {{ $requisito->nropolizageneral }})</p>
                                    @else
                                        {!! Form::text('nropolizageneral['.$requisito->id.']', null, ['class' => 'form-control', 'placeholder' => 'Ingrese Nro Póliza General']) !!}
                                    @endif
                                    {!! Form::file('polizageneral['.$requisito->id.']', ['class' => 'form-control-file dropify']) !!}
                                </div>
                            @endif
                
                            @if($requisito->declasalud === 'PENDIENTE')
                                <div class="form-group col-lg-4">
                                    {!! Form::label('declasalud', 'DECLARACION DE SALUD:') !!}
                                    <p>Requerido:</p>
                                    {!! Form::file('declasalud['.$requisito->id.']', ['class' => 'form-control-file dropify']) !!}
                                </div>
                            @endif
                
                            @if($requisito->polizasegurodesgravamen === 'PENDIENTE')
                                <div class="form-group col-lg-4">
                                    {!! Form::label('polizasegurodesgravamen', 'POLIZA DE SEGURO DE DESGRAVAMEN:') !!}
                                    @if($requisito->nropolizadesgravamen)
                                        <p>(Nro: {{ $requisito->nropolizadesgravamen }})</p>
                                    @else
                                        {!! Form::text('nropolizadesgravamen['.$requisito->id.']', null, ['class' => 'form-control', 'placeholder' => 'Ingrese Nro Póliza Desgravamen']) !!}
                                    @endif
                                    {!! Form::file('polizasegurodesgravamen['.$requisito->id.']', ['class' => 'form-control-file dropify']) !!}
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach

                <div class="modal fade" id="requisitosModal" tabindex="-1" aria-labelledby="requisitosModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title" id="requisitosModalLabel">REQUISITOS DE AUDITORÍA MÉDICA</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body"> 
                                <table class="table">
                                    <thead>
                                        <tr> 
                                            <th>Requisito</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!is_null($requisitosubido->cnacasegurado))
                                        <tr>
                                            <td>CERTIFICADO NACIMIENTO ASEGURADO</td>
                                            <td>
                                                @if ($cnacaseguradoSubido)
                                                    <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisitosubido->cnacasegurado}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @elseif($requisitosubido->cnacasegurado === 'PENDIENTE')
                                                    <div class="pendiente">PENDIENTE</div>
                                                @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @if (!is_null($requisitosubido->ciasegurado))
                                        <tr>
                                            <td>CARNET IDENTIDAD ASEGURADO</td>
                                            <td>
                                                @if ($ciaseguradoSubido)
                                                    <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisitosubido->ciasegurado}") }}" target="_blank" class="verdoc">VER DOCUMENTO</a>
                                                @elseif($requisitosubido->ciasegurado === 'PENDIENTE')
                                                    <div class="pendiente">PENDIENTE</div>
                                                @else
                                                    <div class="pendiente">PENDIENTE</div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                                
                                <table class="table">
                                    <thead>
                                        <tr> 
                                            <th>Banco</th>
                                            <th>Nro. Póliza General</th>
                                            <th>Póliza General</th>
                                            <th>Declaración Salud</th>
                                            <th>Nro. Póliza Desgravamen</th>
                                            <th>Poliza seguro desgravamen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requisitosClientepolizas as $requisito)
                                        <tr>
                                            <td>{{ $requisito->banco }}</td>
                                            <td>{{ $requisito->nropolizageneral ?? '' }}</td>
                                            <td>
                                                @if ($requisito->polizageneral === 'PENDIENTE')
                                                    <div class="pendiente">PENDIENTE</div>
                                                @else
                                                    <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->polizageneral}") }}" target="_blank" class="verdoc">VER DOC.</a>
                                                @endif
                                            </td>
                                            <td> 
                                                @if ($requisito->declasalud === 'PENDIENTE')
                                                    <div class="pendiente">PENDIENTE</div>
                                                @elseif ($requisito->declasalud === 'NO APLICA')
                                                    <div class="noaplica">NO APLICA</div>
                                                @else
                                                    <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->declasalud}") }}" target="_blank" class="verdoc">VER DOC.</a>
                                                @endif
                                            </td>
                                            
                                            <td>{{ $requisito->nropolizadesgravamen ?? '' }}</td>
                                            <td>
                                                @if ($requisito->polizasegurodesgravamen === 'PENDIENTE')
                                                    <div class="pendiente">PENDIENTE</div>
                                                @else
                                                    <a href="{{ asset("/requisitosclientesita/{$cliente->id}/{$requisito->polizasegurodesgravamen}") }}" target="_blank" class="verdoc">VER DOC.</a> 
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::submit('SUBIR DOCUMENTACION', ['class' => 'btn btn-crear']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@stop
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/css/dropify.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropify/0.2.2/js/dropify.min.js"></script>
<script>
    function cargarVistaPrevia() {
      var poder = document.getElementById('poder').files[0];
      if (poder) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var previewIframe = document.getElementById('document-preview');
          previewIframe.src = e.target.result;};
        reader.readAsDataURL(poder);}}
    document.getElementById('poder').addEventListener('change', function() {
      cargarVistaPrevia();});
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

//CANCELAR FUNCION DE LA TECLA ENTER
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
</script>

@endsection
@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    th {
        color:#000000; 
        font-family: "Segoe UI";
        font-weight: 900;
    }
    .pendiente {
        color:#ff0000; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .noaplica {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .verdoc {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .verdoc:hover {
        color:#faa625; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .verdoc2 {
        color:#b5b5b5; 
        font-family: "Segoe UI";
        font-weight: 700;
    }
    .dropify-wrapper {
        height: 200px !important;
    }
    .dropify-message p {
        font-size: 14px;
    }
    h1 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
    h5 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 500;
        margin-bottom: 0%;
        }
    h3 {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 1000;
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
    .mensaje-error {
        color: #e1172b;
        font-family: "Times New Roman";
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        font-size: 12.5px;
        font-weight: bold;
        display: inline-block;
        margin-left: -10px;
    }
    .custom-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 5px 40px;
    }
    .custom-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .custom2-button {
        background-color: #ffffff;
        color: #faa625;
        border-color: #faa625;
        border-radius: 5px;
        padding: 10px 20px;
    }
    .custom2-button:hover {
        background-color: #faa625;
        color: #ffffff;
    }
    .btn-cerrar {
        background-color: #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        padding: 5px 10px;
    }
    .btn-cerrar:hover {
        background-color: #94c93b;
        color: #ffffff;
    }
    .btn-regresar {
        background-color: #ffffff;
        color: #2926e2;
        border-color: #2926e2;
        border-radius: 5px;
        padding: 10px 10px;
        margin-left: 10px;
    }
    .btn-regresar:hover {
        background-color: #2926e2;
        color: #ffffff;
    }
    .btn-multiple {
        background-color: #ffffff;
        color: #26b0e2;
        border-color: #26b0e2;
        border-radius: 5px;
        padding: 10px 10px;
    }
    .btn-multiple:hover {
        background-color: #26b0e2;
        color: #ffffff;
    }
</style>
@stop