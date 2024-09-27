@extends('adminlte::page')

@section('content_header')
    <h1>Agregar acciones a áreas</h1>
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
    <div class="container col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        @if($accionesProveedor)
                        {!! Form::model($proveedor, ['route' => ['admin.proveedores.edit2', $proveedor], 'method' => 'POST']) !!}
                    
                            {!! Form::hidden('usuarioid', auth()->user()->id) !!}
                            {!! Form::hidden('usuarioregistro', auth()->user()->name) !!}
    
                            <div class="row">
                                {{-- <div class="col-lg-6">
                                    <div class="form-group">
                                        {!! Form::label('proveedornombre', 'NOMBRE DEL PROVEEDOR:') !!}
                                        {!! Form::text('proveedornombre', null, ['class' => 'form-control', 'placeholder' => '', 'readonly' => true]) !!}
                                        @error('proveedornombre')
                                            <small class="text-danger fas fa-exclamation-circle">
                                                {{$message}}
                                            </small>
                                        @enderror
                                    </div>
                                </div> --}}
                                <div class="col-lg-6">
                                <button type="button" class="btn custom2-button" data-toggle="modal" data-target="#ventanaModal">
                                    Acciones requeridas
                                </button>
                                </div>
                            </div>
                                <div class="modal fade" id="ventanaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Acciones requeridas:</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                </div>
                                                <div class="modal-body">
                                                <ul>
                                                    @foreach($accionesProveedor as $accion)
                                                    <li>{{ $accion }}</li>
                                                    @endforeach
                                                </ul>
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            
                        </div>
                        {{-- {!! Form::submit('Crear programacion', ['class' => 'btn btn-crear']) !!} --}}
                        {!! Form::close() !!}
                        @else
                            <div class="alert " role="alert">
                                ESTE PROVEEDOR NO TIENE BATERIA
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- @foreach ($acciones as $accion)
                            <div class="col-sm-4">
                                <div class="form-check">
                                    <label>
                                        {!! Form::checkbox('acciones[]', $accion->id, $proveedor->acciones ? $proveedor->acciones->contains($accion->id) : false, ['class' => 'mr-1']) !!}
                                        {{$accion->accion}}
                                    </label>
                                </div>
                            </div> 
                        @endforeach --}}
