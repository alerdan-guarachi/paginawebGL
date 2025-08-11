@extends('adminlte::page')

@section('content_header')
<h1>ANUNCIOS</h1>
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
                <div class="col-lg-6">
                    <strong>NUEVO ANUNCIO</strong>
                    {!! Form::model($mensaje, ['route' => ['admin.mensajes.store', $mensaje], 'method' => 'POST']) !!}
                    {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                    {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
                    <div class="form-group">
                        {!! Form::label('titulo', 'Título del Anuncio:') !!}
                        {!! Form::text('titulo', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        @error('titulo')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('mensaje', 'Detalle del Anuncio:') !!}
                        {!! Form::text('mensaje', null, ['class' => 'form-control', 'placeholder' => '']) !!}
                        @error('mensaje')
                        <small class="text-danger fas fa-exclamation-circle">
                            {{$message}}
                        </small>  
                        @enderror
                    </div>
                    <div class="form-group">
                        {!! Form::label('opcion', 'Destino:') !!}
                        {!! Form::select('opcion', ['TODOS' => 'TODOS '/* , 'elegir_usuarios' => 'SELECCIONAR USUARIOS' */], null, ['class' => 'form-control', 'id' => 'opcionSelect']) !!}
                    </div>
                    <div class="form-group hidden" id="usuarioSelectContainer">
                        {!! Form::label('usuariodestino', 'Seleccionar usuarios:') !!}
                        <div id="usuarioCheckboxes">
                          @foreach($personal as $id => $name)
                            <div class="form-check">
                              {!! Form::checkbox('usuariodestino[]', $id, false, ['class' => 'form-check-input', 'id' => 'user-' . $id]) !!}
                              {!! Form::label('user-' . $id, $name, ['class' => 'form-check-label']) !!}
                            </div>
                          @endforeach
                        </div>
                        @error('usuariodestino')
                        <small class="text-danger fas fa-exclamation-circle">
                          {{$message}}
                        </small>
                        @enderror
                    </div>
                        {!! Form::submit('Enviar Anuncio', ['class' => 'btn btn-crear']) !!}
                    {!! Form::close() !!}
                </div>
                <div class="col-lg-6">
                    <strong>ANUNCIOS ENVIADOS HOY</strong>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Usuario Destino</th>
                                    <th>Fecha y Hora</th>
                                    <th>Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mensajesenviados as $mensajesenviado)
                                    <tr>
                                        <td>{{ $mensajesenviado->usuariodestino }}</td>
                                        <td>{{ date('Y-m-d H:i:s', strtotime($mensajesenviado->created_at)) }}</td>
                                        <td>
                                            <abbr title="Ver Mensaje">
                                                <button type="button" class="btn btn-ver btn-sm" data-toggle="modal" data-target="#modalMensaje{{ $mensajesenviado->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </abbr>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="modalMensaje{{ $mensajesenviado->id }}" tabindex="-1" role="dialog" aria-labelledby="modalMensajeLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <strong class="modal-title" id="modalMensajeLabel">{{ $mensajesenviado->titulo }}</strong>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>{{ $mensajesenviado->mensaje }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var opcionSelect = document.getElementById('opcionSelect');
            var usuarioSelectContainer = document.getElementById('usuarioSelectContainer');
            opcionSelect.addEventListener('change', function () {
            if (opcionSelect.value === 'elegir_usuarios') {
                usuarioSelectContainer.classList.remove('hidden');
            } else {
                usuarioSelectContainer.classList.add('hidden');
            }
            });
        });
    </script>
@endsection

@section('css')
<link rel="styleheet" href="/css/admin_custom.css">
<style>
    .table td {
        padding: 5px 10px;
        }
    .hidden {
      display: none;
    }
    .btn-cerrar {
        background-color:  #ffffff;
        color: #ff0000;
        border-color: #ff0000;
        border-radius: 5px;
        padding: 4px 5px;
        }
    .btn-cerrar:hover {
        background-color: #ff0000;
        color: #ffffff;
        }
    h1, th {
        color:#94c93b; 
        font-family: "Segoe UI";
        font-weight: 900;
        }
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
    .btn-ver {
        background-color:  #ffffff;
        color: #94c93b;
        border-color: #94c93b;
        border-radius: 5px;
        }
    .btn-ver:hover {
        background-color: #94c93b;
        color: #ffffff;
        }
</style>
@stop
